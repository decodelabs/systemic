<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

class Result
{
    protected bool $launched = true;
    protected bool $completed = false;
    protected bool $success = false;
    protected ?int $exit = null;
    protected float $startTime;
    protected ?float $endTime = null;
    protected ?string $output = null;
    protected ?string $error = null;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function registerFailure(): static
    {
        $this->endTime = microtime(true);
        $this->launched = false;

        return $this;
    }

    public function hasLaunched(): bool
    {
        return $this->launched;
    }


    public function registerCompletion(
        int $exit = 0
    ): static {
        $this->endTime = microtime(true);
        $this->completed = true;
        $this->exit = $exit;

        $this->success = $exit === 0;

        return $this;
    }

    public function hasCompleted(): bool
    {
        return $this->completed;
    }

    public function getExitCode(): ?int
    {
        return $this->exit;
    }

    public function wasSuccessful(): bool
    {
        return $this->success;
    }


    public function setOutput(
        ?string $output
    ): static {
        $this->output = $output;
        return $this;
    }

    public function appendOutput(
        ?string $output
    ): static {
        $this->output .= $output;
        return $this;
    }

    public function hasOutput(): bool
    {
        return isset($this->output[0]);
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }



    public function setError(
        ?string $error
    ): static {
        $this->error = $error;
        return $this;
    }


    public function appendError(
        ?string $error
    ): static {
        $this->error .= $error;
        return $this;
    }


    public function hasError(): bool
    {
        return isset($this->error[0]);
    }


    public function getError(): ?string
    {
        return $this->error;
    }
}
