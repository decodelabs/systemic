<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

interface Os
{
    public function getName(): string;
    public function getPlatformType(): string;
    public function getDistribution(): string;

    public function isWindows(): bool;
    public function isUnix(): bool;
    public function isLinux(): bool;
    public function isMac(): bool;

    public function getVersion(): string;
    public function getRelease(): string;
    public function getHostName(): string;

    public function userIdToUserName(int $id): string;
    public function userNameToUserId(string $name): int;
    public function groupIdToGroupName(int $id): string;
    public function groupNameToGroupId(string $name): int;

    public function which(string $binaryName): ?string;

    public function getShellWidth(): int;
    public function getShellHeight(): int;
    public function canColorShell(): bool;
}
