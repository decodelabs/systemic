<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

interface ActiveProcess extends Process
{
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
    public function setOwnerId(
        int $id
    ): static;

    /**
     * @return $this
     */
    public function setOwnerName(
        string $name
    ): static;

    /**
     * @return $this
     */
    public function setGroupId(
        int $id
    ): static;

    /**
     * @return $this
     */
    public function setGroupName(
        string $name
    ): static;



    public function canFork(): bool;
    public function fork(): ?static;
}
