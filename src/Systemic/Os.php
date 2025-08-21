<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

interface Os
{
    public string $name { get; }
    public string $platformType { get; }
    public string $distribution { get; }
    public string $version { get; }
    public string $release { get; }
    public string $hostName { get; }

    public function isWindows(): bool;
    public function isUnix(): bool;
    public function isLinux(): bool;
    public function isMac(): bool;

    public function userIdToUserName(
        int $id
    ): string;

    public function userNameToUserId(
        string $name
    ): int;

    public function groupIdToGroupName(
        int $id
    ): string;

    public function groupNameToGroupId(
        string $name
    ): int;


    public function which(
        string $binaryName
    ): ?string;
}
