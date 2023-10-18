<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Archetype;
use DecodeLabs\Eventful\Signal;
use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Command\Unix as UnixCommand;
use DecodeLabs\Veneer;
use DecodeLabs\Veneer\LazyLoad;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Veneer\Plugin\Wrapper;
use Stringable;

class Context
{
    /**
     * @var Os|Wrapper<Os>
     */
    #[Plugin(OsAbstract::class)]
    #[LazyLoad]
    public Os|Wrapper $os;


    /**
     * Wrap process from PID
     */
    public function getProcess(int $pid): Process
    {
        /** @var class-string<Process> */
        $class = $this->getProcessSystemClass();
        return new $class($pid);
    }

    /**
     * Get current process
     */
    public function getCurrentProcess(): ActiveProcess
    {
        /** @var class-string<ActiveProcess> */
        $class = $this->getProcessSystemClass(true);
        $pid = $class::getCurrentProcessId();
        return new $class($pid);
    }

    /**
     * Get class for current system's managed process
     */
    protected function getProcessSystemClass(bool $active = false): string
    {
        $prefix = $active ? 'Active' : '';
        $class = '\\DecodeLabs\\Systemic\\' . $prefix . 'Process\\' . $this->os->getName();

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\' . $prefix . 'Process\\' . $this->os->getPlatformType();

            if (!class_exists($class)) {
                throw Exceptional::ComponentUnavailable(
                    'Processes aren\'t currently supported on this platform!'
                );
            }
        }

        return $class;
    }


    /**
     * New signal object
     */
    public function newSignal(
        Signal|string|int $signal
    ): Signal {
        return Signal::create($signal);
    }

    /**
     * Normalize signal id
     */
    public function normalizeSignal(
        Signal|string|int $signal
    ): int {
        return Signal::create($signal)->getNumber();
    }


    /**
     * Run process, capture output as Result
     *
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
     * Run script, capture output as Result
     *
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
     * Run process, capture output as Result and stream to terminal
     *
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
     * Run script, capture output as Result and stream to terminal
     *
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
     * Launch background process
     *
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
     * Launch background script
     *
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
     * Start TTY terminal process
     *
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
     * Start TTY terminal process
     *
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
     * Start custom controller process
     *
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function start(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null,
        callable|Controller $controller
    ): Result {
        return $this->command($command)
            ->setWorkingDirectory($workingDirectory)
            ->start($controller);
    }

    /**
     * Start custom controller script
     *
     * @param string|Stringable|array<string|Stringable>|Command $command
     */
    public function startScript(
        string|Stringable|array|Command $command,
        string|Stringable|null $workingDirectory = null,
        callable|Controller $controller
    ): Result {
        return $this->scriptCommand($command)
            ->setWorkingDirectory($workingDirectory)
            ->start($controller);
    }



    /**
     * Prepare command
     *
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
     * Prepare script command
     *
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


    /**
     * Get PHP binary path
     */
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
     * Create new command
     *
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
                $name = 'Windows';
            }

            $class = Archetype::resolve(
                Command::class,
                $name,
                UnixCommand::class
            );
        }

        /** @var class-string<Command> $class */
        return new $class($command, $variables);
    }
}


// Register the Veneer proxy
Veneer::register(Context::class, Systemic::class);
