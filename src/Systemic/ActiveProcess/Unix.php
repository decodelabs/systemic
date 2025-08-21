<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\ActiveProcess;

use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\ActiveProcess;
use DecodeLabs\Systemic\Process\Unix as UnixBase;
use Throwable;

class Unix extends UnixBase implements ActiveProcess
{
    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId !== null) {
            return $this->parentProcessId;
        }

        if (extension_loaded('posix')) {
            return $this->parentProcessId = posix_getppid();
        }

        return parent::getParentProcessId();
    }

    /**
     * Set process identity
     */
    public function setIdentity(
        string|int $uid,
        string|int $gid
    ): static {
        if (!is_numeric($uid)) {
            $uid = Systemic::getOs()->userNameToUserId($uid);
        }

        if (!is_numeric($gid)) {
            $gid = Systemic::getOs()->groupNameToGroupId($gid);
        }

        $uid = (int)$uid;
        $gid = (int)$gid;

        if (!extension_loaded('posix')) {
            throw Exceptional::Runtime(
                message: 'Unable to set process identity - posix not available'
            );
        }

        $doUid = $uid != $this->getOwnerId();
        $doGid = $gid != $this->getGroupId();
        $doPidFile = $this->pidFile !== null && is_file($this->pidFile);

        if (
            $doGid &&
            $doPidFile &&
            $this->pidFile !== null
        ) {
            chgrp($this->pidFile, $gid);
        }

        if (
            $doUid &&
            $doPidFile &&
            $this->pidFile !== null
        ) {
            chown($this->pidFile, $uid);
        }

        if ($doGid) {
            if (!posix_setgid($gid)) {
                throw Exceptional::Runtime(
                    message: 'Set group failed'
                );
            }
        }

        if ($doUid) {
            if (!posix_setuid($uid)) {
                throw Exceptional::Runtime(
                    message: 'Set owner failed'
                );
            }
        }

        return $this;
    }

    /**
     * Set process owner
     */
    public function setOwnerId(
        int $id
    ): static {
        if (extension_loaded('posix')) {
            if ($id != $this->getOwnerId()) {
                if ($this->pidFile && is_file($this->pidFile)) {
                    chown($this->pidFile, $id);
                }

                try {
                    posix_setuid($id);
                } catch (Throwable $e) {
                    throw Exceptional::Runtime(
                        message: 'Set owner failed',
                        previous: $e
                    );
                }
            }
        } else {
            throw Exceptional::Runtime(
                message: 'Unable to set owner id - posix not available'
            );
        }

        return $this;
    }

    /**
     * Get current process owner
     */
    public function getOwnerId(): int
    {
        if (extension_loaded('posix')) {
            return posix_geteuid();
        }

        if (false !== ($uid = getmyuid())) {
            return $uid;
        }

        return parent::getOwnerId();
    }

    /**
     * Set process owner by name
     */
    public function setOwnerName(
        string $name
    ): static {
        return $this->setOwnerId(Systemic::getOs()->userNameToUserId($name));
    }


    /**
     * Set process group
     */
    public function setGroupId(
        int $id
    ): static {
        if (extension_loaded('posix')) {
            if ($id != $this->getGroupId()) {
                if ($this->pidFile && is_file($this->pidFile)) {
                    chgrp($this->pidFile, $id);
                }

                try {
                    posix_setgid($id);
                } catch (Throwable $e) {
                    throw Exceptional::Runtime(
                        message: 'Set group failed',
                        previous: $e
                    );
                }
            }
        } else {
            throw Exceptional::Runtime(
                message: 'Unable to set group id - posix not available'
            );
        }

        return $this;
    }

    /**
     * Get process group id
     */
    public function getGroupId(): int
    {
        if (extension_loaded('posix')) {
            return posix_getegid();
        }

        return parent::getGroupId();
    }

    /**
     * Set process group by name
     */
    public function setGroupName(
        string $name
    ): static {
        return $this->setGroupId(Systemic::getOs()->groupNameToGroupId($name));
    }



    /**
     * Is system capable of forking processes?
     */
    public function canFork(): bool
    {
        return extension_loaded('pcntl');
    }

    /**
     * Fork this process
     */
    public function fork(): ?static
    {
        if (!$this->canFork()) {
            throw Exceptional::ComponentUnavailable(
                message: 'This process is not capable of forking'
            );
        }

        $pid = pcntl_fork();

        if ($pid === -1) {
            throw Exceptional::Runtime(
                message: 'The process did not fork successfully'
            );
        } elseif ($pid) {
            // Parent
            $output = clone $this;
            $output->processId = $pid;

            return $output;
        } else {
            // Child
            $this->processId = self::getCurrentProcessId();
            return null;
        }
    }
}
