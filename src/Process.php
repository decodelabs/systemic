<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Eventful\Signal;

interface Process
{
    public static function isProcessIdLive(
        int $pid
    ): bool;

    public static function getCurrentProcessId(): int;

    public function getProcessId(): int;
    public function getParentProcessId(): int;

    public function isAlive(): bool;
    public function kill(): bool;

    public function sendSignal(
        Signal|string|int $signal
    ): bool;


    public function hasPidFile(): bool;

    /**
     * @return $this
     */
    public function setPidFilePath(
        ?string $path
    ): static;

    public function getPidFilePath(): ?string;

    /**
     * @return $this
     */
    public function setPriority(
        int $priority
    ): static;

    public function getPriority(): int;

    public function getOwnerId(): int;
    public function getOwnerName(): string;
    public function getGroupId(): int;
    public function getGroupName(): string;
    public function isPrivileged(): bool;
}
