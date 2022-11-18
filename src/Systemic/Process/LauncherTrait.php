<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Coercion;
use DecodeLabs\Deliverance\Broker;
use DecodeLabs\Fluidity\ThenTrait;
use DecodeLabs\Terminus\Session;

use Stringable;

trait LauncherTrait
{
    use ThenTrait;

    /**
     * @var array<string>
     */
    protected array $args = [];

    protected string $path;
    protected ?string $user = null;
    protected ?string $title = null;
    protected ?int $priority = null;
    protected ?string $workingDirectory = null;
    protected ?Broker $broker = null;
    protected ?Session $session = null;
    protected bool $decoratable = true;

    /**
     * @var callable|null
     */
    protected $inputGenerator;


    /**
     * Init with main params
     *
     * @param array<string> $args
     */
    public function __construct(
        string|Stringable $path,
        array $args = [],
        string|Stringable|null $workingDirectory = null,
        Broker|Session|null $io = null,
        string $user = null
    ) {
        $this->setPath($path);
        $this->setArgs($args);
        $this->setWorkingDirectory($workingDirectory);
        $this->setTitle($this->path);
        $this->setUser($user);

        if ($io instanceof Broker) {
            $this->setBroker($io);
        } elseif ($io instanceof Session) {
            $this->setSession($io);
        }
    }


    /**
     * Get process exec path
     */
    public function setPath(
        string|Stringable $path
    ): static {
        $this->path = Coercion::toString($path);
        return $this;
    }

    /**
     * Get process exec path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set process args
     */
    public function setArgs(array $args): static
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
     * Set process owner
     */
    public function setUser(?string $user): static
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
    public function setTitle(?string $title): static
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
    public function setPriority(?int $priority): static
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
        return $this->workingDirectory;
    }



    /**
     * Set callback to read user input
     */
    public function setBroker(?Broker $broker): static
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
     * Set CLI session
     */
    public function setSession(?Session $session): static
    {
        $this->session = $session;

        if ($session) {
            $this->setBroker($session->getBroker());
        }

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }


    /**
     * Set input generator callable
     */
    public function setInputGenerator(?callable $generator): static
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
    public function setDecoratable(bool $flag): static
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
