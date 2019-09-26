<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;

interface Managed extends Process
{
    public function setTitle(?string $title): Managed;
    public function getParentProcessId(): int;
    public function setPriority(int $priority): Managed;
    public function getPriority(): int;

    public function setIdentity(int $uid, int $gid): Managed;

    public function setOwnerId(int $id): Managed;
    public function getOwnerId(): int;

    public function setOwnerName(string $name): Managed;
    public function getOwnerName(): string;

    public function setGroupId(int $id): Managed;
    public function getGroupId(): int;

    public function setGroupName(string $name): Managed;
    public function getGroupName(): string;

    public function hasPidFile(): bool;
    public function setPidFilePath(?string $path): Managed;
    public function getPidFilePath(): ?string;

    public function canFork(): bool;
    public function fork(): Managed;
}
