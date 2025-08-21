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

/**
 * @phpstan-require-implements Command
 */
trait CommandTrait
{
    use BrokerConnectorTrait;
    use ThenTrait;

    /**
     * @var string|array<string>
     */
    protected string|array $command;

    /**
     * @var array<string,string>
     */
    protected array $variables = [];

    protected ?string $user = null;
    protected ?string $workingDirectory = null;

    /**
     * @var array<string, Signal>
     */
    protected array $signals = [];


    /**
     * @var array<string,string|int|float|null>
     */
    private static ?array $defaultEnv = null;
    private static ?bool $sigChildEnabled = null;

    /**
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
     * @return array<string, string>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return $this
     */
    public function setVariable(
        string $name,
        string|Stringable|int|float $value
    ): static {
        $this->variables[$name] = (string)$value;
        return $this;
    }

    public function getVariable(
        string $name
    ): ?string {
        return $this->variables[$name] ?? null;
    }

    public function hasVariable(
        string $name
    ): bool {
        return isset($this->variables[$name]);
    }

    protected function resolveVariable(
        string $name
    ): string {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }

        throw Exceptional::InvalidArgumentException(
            message: 'No value available for variable "' . $name . '"'
        );
    }


    public function setWorkingDirectory(
        string|Stringable|null $path
    ): static {
        $this->workingDirectory = Coercion::tryString($path);
        return $this;
    }

    public function getWorkingDirectory(): ?string
    {
        $workingDirectory = $this->workingDirectory !== null ?
            realpath($this->workingDirectory) : null;

        if ($workingDirectory === false) {
            $workingDirectory = $this->workingDirectory;
        }

        return $workingDirectory;
    }




    public function addSignal(
        Signal|string|int ...$signals
    ): static {
        foreach ($signals as $signal) {
            $signal = Signal::create($signal);
            $this->signals[$signal->name] = $signal;
        }

        return $this;
    }

    /**
     * @return array<string, Signal>
     */
    public function getSignals(): array
    {
        return $this->signals;
    }

    public function hasSignals(): bool
    {
        return !empty($this->signals);
    }

    public function hasSignal(
        Signal|string|int $signal
    ): bool {
        $signal = Signal::create($signal);
        return isset($this->signals[$signal->name]);
    }

    public function removeSignal(
        Signal|string|int $signal
    ): static {
        $signal = Signal::create($signal);
        unset($this->signals[$signal->name]);
        return $this;
    }

    public function clearSignals(): static
    {
        $this->signals = [];
        return $this;
    }


    public function setUser(
        ?string $user
    ): static {
        $this->user = $user;
        return $this;
    }


    public function getUser(): ?string
    {
        return $this->user;
    }




    public function __toString(): string
    {
        if (is_array($this->command)) {
            return $this->prepareAsArray();
        } else {
            return $this->prepareAsString();
        }
    }


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


    protected function prepareAsString(): string
    {
        return Coercion::asString(
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
     * @return array<string,string|int|float|null>
     */
    protected function getDefaultEnv(): array
    {
        // @phpstan-ignore-next-line
        return self::$defaultEnv ??= (
            isset($_SERVER['HTTP_HOST']) ?
                [] :
                array_merge(getenv(), $_ENV)
        );
    }



    protected function isSigChildEnabled(): bool
    {
        if (!isset(self::$sigChildEnabled)) {
            ob_start();
            phpinfo(\INFO_GENERAL);
            self::$sigChildEnabled = str_contains(
                (string)ob_get_clean(),
                '--enable-sigchild'
            );
        }

        return self::$sigChildEnabled;
    }






    public function capture(): Result
    {
        $controller = new BlindCaptureController(new PipeManifold());
        $controller->execute($this);
        return $controller->getResult();
    }


    public function liveCapture(): Result
    {
        $controller = new LiveCaptureController(new PipeManifold());
        $controller->execute($this);
        return $controller->getResult();
    }


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


    public function launch(): Process
    {
        $controller = new SeveredController(new DevNullManifold());
        $process = $controller->execute($this);

        if (!$process) {
            throw Exceptional::Runtime(
                message: 'Unable to launch command: ' . $this->getRawString()
            );
        }

        return $process;
    }


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
