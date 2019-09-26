<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Launcher;

abstract class Base implements Launcher
{
    protected $processName;
    protected $args = [];
    protected $path;
    protected $user;
    protected $title;
    protected $priority;
    protected $workingDirectory;

    protected $multiplexer;
    protected $outputWriter;
    protected $errorWriter;
    protected $inputReader;

    /**
     * Create process launcher for specific OS
     */
    public static function create(string $processName, array $args=[], string $path=null, string $user=null): Launcher
    {
        $class = '\\DecodeLabs\\Systemic\\Process\\Launcher\\'.Systemic::$os->getName();

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\Process\\Launcher\\'.Systemic::$os->getPlatformType();

            if (!class_exists($class)) {
                throw Glitch::EComponentUnavailable(
                    'Sorry, I don\'t know how to launch processes on this platform!'
                );
            }
        }

        return new $class($processName, $args, $path);
    }


    /**
     * Init with main params
     */
    protected function __construct(string $processName, array $args=[], stirng $path=null, string $user=null)
    {
        $this->setProcessName($processName);
        $this->setArgs($args);
        $this->setPath($path);
        $this->setTitle($this->processName);
        $this->setUser($user);
    }


    /**
     * Set process name
     */
    public function setProcessName(string $name): Launcher
    {
        $this->processName = $name;
        return $this;
    }

    /**
     * Get process name
     */
    public function getProcessName(): string
    {
        return $this->processName;
    }

    /**
     * Set process args
     */
    public function setArgs(array $args): Launcher
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Get process args
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Get process exec path
     */
    public function setPath(?string $path): Launcher
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get process exec path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set process owner
     */
    public function setUser(?string $user): Launcher
    {
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
     * Set process title
     */
    public function setTitle(?string $title): Launcher
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get process title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set process priority
     */
    public function setPriority(?int $priority): Launcher
    {
        $this->priority = (int)$priority;
        return $this;
    }

    /**
     * Get process priority
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set working directory
     */
    public function setWorkingDirectory(?string $path): Launcher
    {
        $this->workingDirectory = $path;
        return $this;
    }

    /**
     * Get working directory
     */
    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }



    /**
     * Set callback to pass on output to another stream
     */
    public function setOutputWriter(?callable $writer): Launcher
    {
        $this->outputWriter = $writer;
        return $this;
    }

    /**
     * Get the output writer
     */
    public function getOutputWriter(): ?callable
    {
        return $this->outputWriter;
    }

    /**
     * Set callback to pass on error output to another stream
     */
    public function setErrorWriter(?callable $writer): Launcher
    {
        $this->errorWriter = $writer;
        return $this;
    }

    /**
     * Get error writer
     */
    public function getErrorWriter(): ?callable
    {
        return $this->errorWriter;
    }

    /**
     * Set callback to read user input
     */
    public function setInputReader(?callable $reader): Launcher
    {
        $this->inputReader = $reader;
        return $this;
    }

    /**
     * Get input reader
     */
    public function getInputReader(): ?callable
    {
        return $this->inputReader;
    }
}
