<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;

interface Managed extends Process
{
    /**
     * @return $this
     */
    public function setTitle(?string $title): static;

    public function getParentProcessId(): int;

    /**
     * @return $this
     */
    public function setPriority(int $priority): static;

    public function getPriority(): int;


    /**
     * @return $this
     */
    public function setIdentity(
        string|int $uid,
        string|int $gid
    ): static;


    /**
     * @return $this
     */
    public function setOwnerId(int $id): static;

    public function getOwnerId(): int;


    /**
     * @return $this
     */
    public function setOwnerName(string $name): static;

    public function getOwnerName(): string;


    /**
     * @return $this
     */
    public function setGroupId(int $id): static;

    public function getGroupId(): int;


    /**
     * @return $this
     */
    public function setGroupName(string $name): static;

    public function getGroupName(): string;

    public function hasPidFile(): bool;

    /**
     * @return $this
     */
    public function setPidFilePath(?string $path): static;

    public function getPidFilePath(): ?string;

    public function canFork(): bool;
    public function fork(): ?Managed;
}
