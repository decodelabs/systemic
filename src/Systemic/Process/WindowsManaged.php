<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Exceptional;
use DecodeLabs\Glitch\Proxy as Glitch;
use DecodeLabs\Systemic\Process;

use Variant;

class WindowsManaged extends Windows implements Managed
{
    use PidFileProviderTrait;

    protected ?int $parentProcessId = null;

    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId === null) {
            $wmi = $this->getWmi();
            $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $this->getProcessId() . '\'');

            foreach ($procs as $process) {
                $this->parentProcessId = $process->ParentProcessId;
                break;
            }
        }

        return $this->parentProcessId;
    }

    /**
     * Set process title
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set process priority
     */
    public function setPriority(int $priority): static
    {
        Glitch::incomplete();
    }

    /**
     * Get process priority
     */
    public function getPriority(): int
    {
        Glitch::incomplete();
    }


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
    }

    /**
     * Get current process owner
     */
    public function getOwnerId(): int
    {
        Glitch::incomplete();
    }

    /**
     * Set process owner by name
     */
    public function setOwnerName(string $name): static
    {
        Glitch::incomplete();
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
    }

    /**
     * Get process group id
     */
    public function getGroupId(): int
    {
        Glitch::incomplete();
    }

    /**
     * Set process group by name
     */
    public function setGroupName(string $name): static
    {
        Glitch::incomplete();
    }

    /**
     * Get process group name
     */
    public function getGroupName(): string
    {
        Glitch::incomplete();
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
    public function fork(): ?Managed
    {
        throw Exceptional::Runtime(
            'PHP on windows is currently not able to fork processes'
        );
    }
}
