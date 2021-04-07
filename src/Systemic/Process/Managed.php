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
    public function setTitle(?string $title): Managed;

    public function getParentProcessId(): int;

    /**
     * @return $this
     */
    public function setPriority(int $priority): Managed;

    public function getPriority(): int;


    /**
     * @param string|int $uid
     * @param string|int $gid
     * @return $this
     */
    public function setIdentity($uid, $gid): Managed;


    /**
     * @return $this
     */
    public function setOwnerId(int $id): Managed;

    public function getOwnerId(): int;


    /**
     * @return $this
     */
    public function setOwnerName(string $name): Managed;

    public function getOwnerName(): string;


    /**
     * @return $this
     */
    public function setGroupId(int $id): Managed;

    public function getGroupId(): int;


    /**
     * @return $this
     */
    public function setGroupName(string $name): Managed;

    public function getGroupName(): string;

    public function hasPidFile(): bool;

    /**
     * @return $this
     */
    public function setPidFilePath(?string $path): Managed;

    public function getPidFilePath(): ?string;

    public function canFork(): bool;
    public function fork(): ?Managed;
}
