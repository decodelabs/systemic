<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

class Result
{
    protected bool $launched = true;
    protected bool $completed = false;
    protected float $startTime;
    protected ?float $endTime = null;
    protected ?string $output = null;
    protected ?string $error = null;

    /**
     * Init with start time of process
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Set that task has failed
     */
    public function registerFailure(): static
    {
        $this->endTime = microtime(true);
        $this->launched = false;

        return $this;
    }

    /**
     * Has the process launched?
     */
    public function hasLaunched(): bool
    {
        return $this->launched;
    }


    /**
     * Set that task has completed
     */
    public function registerCompletion(): static
    {
        $this->endTime = microtime(true);
        $this->completed = true;

        return $this;
    }

    /**
     * Has the process completed?
     */
    public function hasCompleted(): bool
    {
        return $this->completed;
    }


    /**
     * Set main process output
     */
    public function setOutput(?string $output): static
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Append to main process output
     */
    public function appendOutput(?string $output): static
    {
        $this->output .= $output;
        return $this;
    }

    /**
     * Has any output been set?
     */
    public function hasOutput(): bool
    {
        return isset($this->output[0]);
    }

    /**
     * Get stored output buffer
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }


    /**
     * Set error output
     */
    public function setError(?string $error): static
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Append to error output
     */
    public function appendError(?string $error): static
    {
        $this->error .= $error;
        return $this;
    }

    /**
     * Has any error output been set?
     */
    public function hasError(): bool
    {
        return isset($this->error[0]);
    }

    /**
     * Get error output buffer
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}
