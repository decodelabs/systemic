<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Eventful\Signal;
use DecodeLabs\Kingdom\PureService;
use DecodeLabs\Kingdom\PureServiceTrait;
use DecodeLabs\Systemic\ActiveProcess;
use DecodeLabs\Systemic\Command;
use DecodeLabs\Systemic\Command\Unix as UnixCommand;
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\Os;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Result;
use Stringable;

class Systemic implements PureService
{
    use PureServiceTrait;

    private static Os $osInstance;

    public Os $os {
        get => self::getOs();
    }

    public static function getOs(): Os
    {
        if (isset(self::$osInstance)) {
            return self::$osInstance;
        }

        $name = php_uname('s');

        if (substr(strtolower($name), 0, 3) == 'win') {
            $name = 'Windows';
        }

        $class = match ($name) {
            'Darwin' => Os\Darwin::class,
            'Linux' => Os\Linux::class,
            'Windows' => Os\Windows::class,
            default => Os\Unix::class,
        };

        return self::$osInstance = new $class($name);
    }

    public function __construct()
    {
    }



    public static function getProcess(
        int $pid
    ): Process {
        /** @var class-string<Process> */
        $class = self::getProcessSystemClass();
        return new $class($pid);
    }


    public static function getActiveProcess(): ActiveProcess
    {
        /** @var class-string<ActiveProcess> */
        $class = self::getProcessSystemClass(true);
        $pid = $class::getCurrentProcessId();
        return new $class($pid);
    }


    protected static function getProcessSystemClass(
        bool $active = false
    ): string {
        $os = self::getOs();
        $prefix = $active ? 'Active' : '';

        $classes = [
            '\\DecodeLabs\\Systemic\\' . $prefix . 'Process\\' . $os->name,
            '\\DecodeLabs\\Systemic\\' . $prefix . 'Process\\' . $os->platformType,
        ];

        foreach ($classes as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }

        throw Exceptional::ComponentUnavailable(
            message: 'Processes aren\'t currently supported on this platform!'
        );
    }



    public function newSignal(
        Signal|string|int $signal
    ): Signal {
        return Signal::create($signal);
    }


    public function normalizeSignal(
        Signal|string|int $signal
    ): int {
        return Signal::create($signal)->number;
    }


    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function capture(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Result {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->capture();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function captureScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Result {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->capture();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function liveCapture(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Result {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->liveCapture();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function liveCaptureScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Result {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->liveCapture();
    }


    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function launch(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Process {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->launch();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function launchScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): Process {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->launch();
    }



    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function run(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): bool {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->run();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function runScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null
    ): bool {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->run();
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function start(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory,
        callable|Controller $controller
    ): Result {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->start($controller);
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function startScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory,
        callable|Controller $controller
    ): Result {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->start($controller);
    }



    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     * @param array<string, string|Stringable|int|float> $variables
     */
    public function command(
        string|Stringable|array|Command $command,
        array $variables = []
    ): Command {
        if (!$command instanceof Command) {
            $command = $this->newCommand($command);
        }

        $command->setVariables($variables);
        return $command;
    }

    /**
     * @param string|Stringable|array<string|Stringable>|Command $command
     * @param array<string, string|Stringable|int|float> $variables
     */
    public function scriptCommand(
        string|Stringable|array|Command $command,
        array $variables = []
    ): Command {
        return $this->command($command, $variables)
            ->prepend($this->getPhpPath());
    }



    protected function getPhpPath(): string
    {
        $binaryPath = $this->os->which('php');

        if (
            empty($binaryPath) ||
            !file_exists($binaryPath)
        ) {
            $binaryPath = 'php';
        }

        return $binaryPath;
    }

    /**
     * @param string|Stringable|array<string|Stringable> $command
     * @param array<string, string|Stringable|int|float> $variables
     */
    protected function newCommand(
        string|Stringable|array $command,
        array $variables = []
    ): Command {
        static $class;

        if (!isset($class)) {
            $name = php_uname('s');

            if (substr(strtolower($name), 0, 3) == 'win') {
                //$name = 'Windows';
                throw Exceptional::ComponentUnavailable(
                    message: 'Windows is not supported yet'
                );
            }

            $class = UnixCommand::class;
        }

        /** @var class-string<Command> $class */
        return new $class($command, $variables);
    }
}
