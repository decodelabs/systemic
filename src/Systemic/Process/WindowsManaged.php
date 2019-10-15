<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Glitch;

use Variant;

class WindowsManaged extends Windows implements Managed
{
    use PidFileProviderTrait;

    protected $parentProcessId;

    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId === null) {
            $wmi = $this->getWmi();
            $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\''.$this->getProcessId().'\'');

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
    public function setTitle(?string $title): Managed
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set process priority
     */
    public function setPriority(int $priority): Managed
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
    public function setIdentity($uid, $gid): Managed
    {
        return $this->setOwnerId($uid)->setGroupId($gid);
    }

    /**
     * Set process owner
     */
    public function setOwnerId(int $id): Managed
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
    public function setOwnerName(string $name): Managed
    {
        Glitch::incomplete();
    }

    /**
     * Get current owner name
     */
    public function getOwnerName(): string
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\''.$this->getProcessId().'\'');

        foreach ($procs as $process) {
            $owner = new Variant(null);
            $process->GetOwner($owner);
            return (string)$owner;
        }

        throw Glitch::ERuntime('Owner name could not be found for process');
    }

    /**
     * Set process group
     */
    public function setGroupId(int $id): Managed
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
    public function setGroupName(string $name): Managed
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
        throw Glitch::ERuntime(
            'PHP on windows is currently not able to fork processes'
        );
    }
}
