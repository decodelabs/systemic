<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Systemic\Context;
use DecodeLabs\Veneer\FacadePlugin;

interface Os extends FacadePlugin
{
    public function getName(): string;
    public function getPlatformType(): string;
    public function getDistribution(): string;

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
