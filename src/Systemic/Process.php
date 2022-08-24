<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Systemic\Process\Signal;

interface Process
{
    public static function isProcessIdLive(int $pid): bool;
    public static function getCurrentProcessId(): int;

    public function getTitle(): ?string;
    public function getProcessId(): int;

    public function isAlive(): bool;
    public function kill(): bool;

    public function sendSignal(
        Signal|string|int $signal
    ): bool;

    public function isPrivileged(): bool;
}
