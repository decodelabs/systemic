<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Coercion;
use DecodeLabs\Deliverance\Broker\ConnectorTrait as BrokerConnectorTrait;
use DecodeLabs\Eventful\Signal;
use DecodeLabs\Exceptional;
use DecodeLabs\Fluidity\ThenTrait;
use DecodeLabs\Systemic\Controller\BlindCapture as BlindCaptureController;
use DecodeLabs\Systemic\Controller\Custom as CustomController;
use DecodeLabs\Systemic\Controller\LiveCapture as LiveCaptureController;
use DecodeLabs\Systemic\Controller\ResultProvider;
use DecodeLabs\Systemic\Controller\Severed as SeveredController;
use DecodeLabs\Systemic\Controller\Terminal as TerminalController;
use DecodeLabs\Systemic\Manifold\DevNull as DevNullManifold;
use DecodeLabs\Systemic\Manifold\Pipe as PipeManifold;
use DecodeLabs\Systemic\Manifold\Pty as PtyManifold;
use DecodeLabs\Systemic\Manifold\Tty as TtyManifold;
use Stringable;

trait CommandTrait
{
    use BrokerConnectorTrait;
    use ThenTrait;

    /**
     * @var string|array<string>
     */
    protected string|array $command;

    /**
     * @var array<string, string>
     */
    protected array $variables = [];

    protected ?string $user = null;
    protected ?string $workingDirectory = null;

    /**
     * @var array<string, Signal>
     */
    protected array $signals = [];

    /**
     * Init with raw command and variables
     *
     * @param string|Stringable|array<string|Stringable> $command
     * @param array<string, string> $variables
     */
    public function __construct(
        string|Stringable|array $command,
        array $variables = []
    ) {
        if ($command instanceof Stringable) {
            $command = (string)$command;
        }

        if (is_array($command)) {
            $command = array_map('strval', $command);
        }

        $this->command = $command;
        $this->setVariables($variables);
    }

    /**
     * Get raw string or array representation
     *
     * @return string|array<string>
     */
    public function getRaw(): string|array
    {
        return $this->command;
    }

    public function getRawString(): string
    {
        if (is_array($this->command)) {
            return implode(' ', $this->command);
        }

        return $this->command;
    }

    /**
     * Prepend to command
     *
     * @param string|Stringable|array<string|Stringable> $prefix
     * @return $this
     */
    public function prepend(
        string|Stringable|array $prefix
    ): static {
        if ($prefix instanceof Stringable) {
            $prefix = (string)$prefix;
        }

        if (is_array($this->command)) {
            if (!is_array($prefix)) {
                $prefix = [(string)$prefix];
            } else {
                $prefix = array_map('strval', $prefix);
            }

            $this->command = array_merge($prefix, $this->command);
        } else {
            if (is_array($prefix)) {
                $prefix = implode(' ', $prefix);
            }

            $this->command = $prefix . ' ' . $this->command;
        }

        return $this;
    }

    /**
     * Append to command
     *
     * @param string|Stringable|array<string|Stringable> $suffix
     * @return $this
     */
    public function append(
        string|Stringable|array $suffix
    ): static {
        if ($suffix instanceof Stringable) {
            $prefix = (string)$suffix;
        }

        if (is_array($this->command)) {
            if (!is_array($suffix)) {
                $suffix = [(string)$suffix];
            } else {
                $suffix = array_map('strval', $suffix);
            }

            $this->command = array_merge($this->command, $suffix);
        } else {
            if (is_array($suffix)) {
                $suffix = implode(' ', $suffix);
            }

            $this->command .= ' ' . $suffix;
        }

        return $this;
    }


    /**
     * Set list of variables
     *
     * @param array<string, string|Stringable|int|float> $variables
     * @return $this
     */
    public function setVariables(
        array $variables
    ): static {
        foreach ($variables as $name => $value) {
            $this->setVariable($name, $value);
        }

        return $this;
    }

    /**
     * Get all variables
     *
     * @return array<string, string>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Set single variable
     *
     * @return $this
     */
    public function setVariable(
        string $name,
        string|Stringable|int|float $value
    ): static {
        $this->variables[$name] = (string)$value;
        return $this;
    }

    /**
     * Get single variable if exists
     */
    public function getVariable(
        string $name
    ): ?string {
        return $this->variables[$name] ?? null;
    }

    /**
     * Is a variable set?
     */
    public function hasVariable(
        string $name
    ): bool {
        return isset($this->variables[$name]);
    }

    /**
     * Get final value for variable
     */
    protected function resolveVariable(
        string $name
    ): string {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }

        if (isset($this->env[$name])) {
            return $this->env[$name];
        }

        throw Exceptional::InvalidArgumentException(
            'No value available for variable "' . $name . '"'
        );
    }


    /**
     * Set working directory
     */
    public function setWorkingDirectory(
        string|Stringable|null $path
    ): static {
        $this->workingDirectory = Coercion::toStringOrNull($path);
        return $this;
    }

    /**
     * Get working directory
     */
    public function getWorkingDirectory(): ?string
    {
        $workingDirectory = $this->workingDirectory !== null ?
            realpath($this->workingDirectory) : null;

        if ($workingDirectory === false) {
            $workingDirectory = $this->workingDirectory;
        }

        return $workingDirectory;
    }




    /**
     * Add signal(s) to be passed to process if caught
     */
    public function addSignal(
        Signal|string|int ...$signals
    ): static {
        foreach ($signals as $signal) {
            $signal = Signal::create($signal);
            $this->signals[$signal->getName()] = $signal;
        }

        return $this;
    }

    /**
     * Get list of registered signals
     *
     * @return array<string, Signal>
     */
    public function getSignals(): array
    {
        return $this->signals;
    }

    /**
     * Are any signals registered?
     */
    public function hasSignals(): bool
    {
        return !empty($this->signals);
    }

    /**
     * Is this particular signal registered?
     */
    public function hasSignal(
        Signal|string|int $signal
    ): bool {
        $signal = Signal::create($signal);
        return isset($this->signals[$signal->getName()]);
    }

    /**
     * Remove signal if registered
     */
    public function removeSignal(
        Signal|string|int $signal
    ): static {
        $signal = Signal::create($signal);
        unset($this->signals[$signal->getName()]);
        return $this;
    }

    /**
     * Remove all signals
     */
    public function clearSignals(): static
    {
        $this->signals = [];
        return $this;
    }


    /**
     * Set process owner
     */
    public function setUser(
        ?string $user
    ): static {
        $this->user = $user;
        return $this;
    }

    /**
     * Get process owner
     */
    public function getUser(): ?string
    {
        return $this->user;
    }



    /**
     * Convert command to string for launch
     */
    public function __toString(): string
    {
        if (is_array($this->command)) {
            return $this->prepareAsArray();
        } else {
            return $this->prepareAsString();
        }
    }

    /**
     * Prepare array representation
     */
    protected function prepareAsArray(): string
    {
        $output = [];

        /** @var array<string> $command */
        $command = $this->command;

        foreach ($command as $part) {
            $part = (string)$part;

            if (preg_match('/"\$\{:([_a-zA-Z]++[_a-zA-Z0-9]*+)\}"/', $part, $matches)) {
                $part = $this->resolveVariable($matches[1]);
            }

            $output[] = $this->escapeArgument($part);
        }

        return implode(' ', $output);
    }

    /**
     * Prepare string representation
     */
    protected function prepareAsString(): string
    {
        return Coercion::toString(
            preg_replace_callback('/"\$\{:([_a-zA-Z]++[_a-zA-Z0-9]*+)\}"/', function ($matches) {
                return $this->escapeArgument(
                    $this->resolveVariable($matches[1])
                );
            }, $this->command)
        );
    }

    abstract protected function escapeArgument(
        string $argument
    ): string;



    /**
     * @return array<string, string|int|float|null>
     */
    public function getEnv(): array
    {
        return array_merge(
            $this->getDefaultEnv(),
            $this->variables
        );
    }

    /**
     * @return array<string, string|int|float|null>
     */
    protected function getDefaultEnv(): array
    {
        static $output;

        if (!isset($output)) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $output = [];
            } else {
                $env = getenv();
                $output = array_merge($env, $_ENV);
            }
        }

        return $output;
    }


    /**
     * Has PHP been compiled with --enable-sigchild
     */
    protected function isSigChildEnabled(): bool
    {
        static $output;

        if (!isset($output)) {
            ob_start();
            phpinfo(\INFO_GENERAL);
            $output = str_contains((string)ob_get_clean(), '--enable-sigchild');
        }

        return $output;
    }





    /**
     * Run and capture result
     */
    public function capture(): Result
    {
        $controller = new BlindCaptureController(new PipeManifold());
        $controller->execute($this);
        return $controller->getResult();
    }

    /**
     * Run and capture result
     */
    public function liveCapture(): Result
    {
        $controller = new LiveCaptureController(new PipeManifold());
        $controller->execute($this);
        return $controller->getResult();
    }

    /**
     * Run as interactive terminal
     */
    public function run(): bool
    {
        if (
            defined('STDIN') &&
            defined('STDOUT') &&
            is_resource(\STDOUT)
        ) {
            $manifold = null;

            if (stream_isatty(\STDOUT)) {
                if (TtyManifold::isSupported()) {
                    $manifold = new TtyManifold();
                } elseif (PtyManifold::isSupported()) {
                    $manifold = new PtyManifold();
                }
            }

            if ($manifold === null) {
                $manifold = new PipeManifold();
            }

            $controller = new TerminalController($manifold);
        } else {
            // Can't do it if not on CLI!
            // Fallback to blind run
            $manifold = new PipeManifold();
            $controller = new SeveredController($manifold);
        }

        $controller->execute($this);
        return $controller->wasSuccessful();
    }

    /**
     * Run background
     */
    public function launch(): Process
    {
        $controller = new SeveredController(new DevNullManifold());
        $process = $controller->execute($this);

        if (!$process) {
            throw Exceptional::Runtime('Unable to launch command: ' . $this->getRawString());
        }

        return $process;
    }

    /**
     * Start custom controller
     */
    public function start(
        callable|Controller $controller
    ): Result {
        if (
            is_callable($controller) &&
            !$controller instanceof Controller
        ) {
            $controller = new CustomController($controller);
        }

        if ($controller instanceof ResultProvider) {
            $result = $controller->getResult();
        } else {
            $result = new Result();
        }

        $controller->execute($this);

        if (!$controller instanceof ResultProvider) {
            $result->registerCompletion(
                $controller->wasSuccessful() ? 0 : -1
            );
        }

        return $result;
    }
}
