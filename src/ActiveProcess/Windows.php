<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\ActiveProcess;

use DecodeLabs\Exceptional;
use DecodeLabs\Glitch\Proxy as Glitch;
use DecodeLabs\Systemic\ActiveProcess;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Windows as WindowsBase;

class Windows extends WindowsBase implements ActiveProcess
{
    /**
     * Set process identity
     */
    public function setIdentity(
        string|int $uid,
        string|int $gid
    ): static {
        return $this->setOwnerId($uid)->setGroupId($gid);
    }

    /**
     * Set process owner
     */
    public function setOwnerId(int $id): static
    {
        Glitch::incomplete();
        return $this;
    }

    /**
     * Get current process owner
     */
    public function getOwnerId(): int
    {
        Glitch::incomplete();
        return 0;
    }

    /**
     * Set process owner by name
     */
    public function setOwnerName(string $name): static
    {
        Glitch::incomplete();
        return $this;
    }

    /**
     * Get current owner name
     */
    public function getOwnerName(): string
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $this->getProcessId() . '\'');

        foreach ($procs as $process) {
            $owner = new Variant(null);
            $process->GetOwner($owner);
            return (string)$owner;
        }

        throw Exceptional::Runtime(
            'Owner name could not be found for process'
        );
    }

    /**
     * Set process group
     */
    public function setGroupId(int $id): static
    {
        Glitch::incomplete();
        return $this;
    }

    /**
     * Get process group id
     */
    public function getGroupId(): int
    {
        Glitch::incomplete();
        return 0;
    }

    /**
     * Set process group by name
     */
    public function setGroupName(string $name): static
    {
        Glitch::incomplete();
        return $this;
    }

    /**
     * Get process group name
     */
    public function getGroupName(): string
    {
        Glitch::incomplete();
        return 'group';
    }

    /**
     * Is system capable of forking processes?
     */
    public function canFork(): bool
    {
        return false;
    }

    /**
     * Fork this process
     */
    public function fork(): ?static
    {
        throw Exceptional::Runtime(
            'PHP on windows is currently not able to fork processes'
        );
    }
}
