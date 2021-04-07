<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Atlas\Broker;
use DecodeLabs\Fluidity\ThenTrait;
use DecodeLabs\Systemic\Process;

trait LauncherTrait
{
    use ThenTrait;

    /**
     * @var string
     */
    protected $processName;

    /**
     * @var array<string>
     */
    protected $args = [];

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var string|null
     */
    protected $user;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var int|null
     */
    protected $priority;

    /**
     * @var string|null
     */
    protected $workingDirectory;

    /**
     * @var Broker|null
     */
    protected $broker;

    /**
     * @var callable|null
     */
    protected $inputGenerator;

    /**
     * @var bool
     */
    protected $decoratable = true;

    /**
     * Init with main params
     *
     * @param array<string> $args
     */
    public function __construct(string $processName, array $args = [], string $path = null, ?Broker $broker = null, string $user = null)
    {
        $this->setProcessName($processName);
        $this->setArgs($args);
        $this->setPath($path);
        $this->setTitle($this->processName);
        $this->setBroker($broker);
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
     * Set callback to read user input
     */
    public function setBroker(?Broker $broker): Launcher
    {
        $this->broker = $broker;
        return $this;
    }

    /**
     * Get input reader
     */
    public function getBroker(): ?Broker
    {
        return $this->broker;
    }

    /**
     * Set input generator callable
     */
    public function setInputGenerator(?callable $generator): Launcher
    {
        $this->inputGenerator = $generator;
        return $this;
    }

    /**
     * Get input generator callable
     */
    public function getInputGenerator(): ?callable
    {
        return $this->inputGenerator;
    }


    /**
     * Set whether to try to make this a true interactive shell for the command
     */
    public function setDecoratable(bool $flag): Launcher
    {
        $this->decoratable = $flag;
        return $this;
    }

    /**
     * Can we try to make this a true interactive shell?
     */
    public function isDecoratable(): bool
    {
        return $this->decoratable;
    }
}
