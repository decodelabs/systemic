<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Eventful\Signal;
use DecodeLabs\Fluidity\Then;
use Stringable;

interface Command extends Then
{
    /**
     * @param string|Stringable|array<string> $command
     * @param array<string, string|Stringable|int|float> $variables
     */
    public function __construct(
        string|Stringable|array $command,
        array $variables = []
    );

    /**
     * @return string|array<string>
     */
    public function getRaw(): string|array;
    public function getRawString(): string;

    /**
     * @param string|Stringable|array<string> $prefix
     * @return $this
     */
    public function prepend(
        string|Stringable|array $prefix
    ): static;

    /**
     * @param string|Stringable|array<string> $suffix
     * @return $this
     */
    public function append(
        string|Stringable|array $suffix
    ): static;

    /**
     * @param array<string, string|Stringable|int|float> $variables
     * @return $this
     */
    public function setVariables(array $variables): static;

    /**
     * @return array<string, string>
     */
    public function getVariables(): array;

    /**
     * @return $this
     */
    public function setVariable(
        string $name,
        string|Stringable|int|float $value
    ): static;

    public function getVariable(
        string $name
    ): string|Stringable|int|float|null;

    public function hasVariable(string $name): bool;



    /**
     * @return $this
     */
    public function setWorkingDirectory(
        string|Stringable|null $path
    ): static;

    public function getWorkingDirectory(): ?string;



    /**
     * @return $this
     */
    public function addSignal(
        Signal|string|int ...$signals
    ): static;

    /**
     * @return array<string, Signal>
     */
    public function getSignals(): array;

    public function hasSignals(): bool;
    public function hasSignal(Signal|string|int $signal): bool;

    /**
     * @return $this
     */
    public function removeSignal(Signal|string|int $signal): static;

    /**
     * @return $this
     */
    public function clearSignals(): static;


    /**
     * @return $this
     */
    public function setUser(?string $user): static;

    public function getUser(): ?string;

    public function __toString(): string;


    /**
     * @return array<string, string|int|float|null>
     */
    public function getEnv(): array;


    /**
     * Quetly call process, capture result
     */
    public function capture(): Result;

    /**
     * Launch process in the background
     */
    public function launch(): Process;

    /**
     * Run interactive process over TTY
     */
    public function run(): bool;
}
