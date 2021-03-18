<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;

use Throwable;

class UnixManaged extends Unix implements Managed
{
    use PidFileProviderTrait;

    protected $parentProcessId;

    /**
     * Ensure pid file is removed on kill
     */
    public function kill(): bool
    {
        if (($output = parent::kill()) && $this->pidFile) {
            @unlink($this->pidFile);
        }

        return $output;
    }

    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId === null) {
            if (extension_loaded('posix')) {
                $this->parentProcessId = posix_getppid();
            } else {
                exec('ps -o ppid --no-heading --pid ' . escapeshellarg($this->processId), $output);

                if (isset($output[0])) {
                    $this->parentProcessId = (int)$output[0];
                } else {
                    throw Exceptional::Runtime(
                        'Unable to extract parent process id'
                    );
                }
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

        if (
            $title &&
            extension_loaded('proctitle') &&
            function_exists('setproctitle')
        ) {
            /** @phpstan-ignore-next-line */
            \setproctitle($title);
        }

        return $this;
    }

    /**
     * Set process priority
     */
    public function setPriority(int $priority): Managed
    {
        if (extension_loaded('pcntl')) {
            @pcntl_setpriority($priority, $this->processId);
        }

        return $this;
    }

    /**
     * Get process priority
     */
    public function getPriority(): int
    {
        if (extension_loaded('pcntl')) {
            return (int)@pcntl_getpriority($this->processId);
        }

        return 0;
    }


    /**
     * Set process identity
     */
    public function setIdentity($uid, $gid): Managed
    {
        if (!is_numeric($uid)) {
            $uid = Systemic::$os->userNameToUserId($uid);
        }

        if (!is_numeric($gid)) {
            $gid = Systemic::$os->groupNameToGroupId($gid);
        }

        if (!extension_loaded('posix')) {
            throw Exceptional::Runtime(
                'Unable to set process identity - posix not available'
            );
        }

        $doUid = $uid != $this->getOwnerId();
        $doGid = $gid != $this->getGroupId();
        $doPidFile = $this->pidFile && is_file($this->pidFile);

        if ($doGid && $doPidFile) {
            chgrp($this->pidFile, $gid);
        }

        if ($doUid && $doPidFile) {
            chown($this->pidFile, $uid);
        }

        if ($doGid) {
            if (!posix_setgid($gid)) {
                throw Exceptional::Runtime(
                    'Set group failed'
                );
            }
        }

        if ($doUid) {
            if (!posix_setuid($uid)) {
                throw Exceptional::Runtime(
                    'Set owner failed'
                );
            }
        }

        return $this;
    }

    /**
     * Set process owner
     */
    public function setOwnerId(int $id): Managed
    {
        if (!is_numeric($id)) {
            return $this->setOwnerName($id);
        }

        if (extension_loaded('posix')) {
            if ($id != $this->getOwnerId()) {
                if ($this->pidFile && is_file($this->pidFile)) {
                    chown($this->pidFile, $id);
                }

                try {
                    posix_setuid($id);
                } catch (Throwable $e) {
                    throw Exceptional::Runtime(
                        'Set owner failed',
                        [
                            'previous' => $e
                        ]
                    );
                }
            }
        } else {
            throw Exceptional::Runtime(
                'Unable to set owner id - posix not available'
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

        exec('ps -o euid --no-heading --pid ' . escapeshellarg($this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            'Unable to extract process owner id'
        );
    }

    /**
     * Set process owner by name
     */
    public function setOwnerName(string $name): Managed
    {
        return $this->setOwnerId(Systemic::$os->userNameToUserId($name));
    }

    /**
     * Get current owner name
     */
    public function getOwnerName(): string
    {
        if (extension_loaded('posix')) {
            $output = posix_getpwuid($this->getOwnerId());
            return $output['name'];
        }

        exec('getent passwd ' . escapeshellarg((string)$this->getOwnerId()), $output);

        if (isset($output[0])) {
            $parts = explode(':', $output[0]);

            if (null !== ($output = array_shift($parts))) {
                return $output;
            }
        }

        throw Exceptional::Runtime(
            'Unable to extract process owner name'
        );
    }


    /**
     * Set process group
     */
    public function setGroupId(int $id): Managed
    {
        if (!is_numeric($id)) {
            return $this->setGroupName($id);
        }

        if (extension_loaded('posix')) {
            if ($id != $this->getGroupId()) {
                if ($this->pidFile && is_file($this->pidFile)) {
                    chgrp($this->pidFile, $id);
                }

                try {
                    posix_setgid($id);
                } catch (Throwable $e) {
                    throw Exceptional::Runtime(
                        'Set group failed',
                        [
                            'previous' => $e
                        ]
                    );
                }
            }
        } else {
            throw Exceptional::Runtime(
                'Unable to set group id - posix not available'
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

        exec('ps -o egid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            'Unable to extract process owner id'
        );
    }

    /**
     * Set process group by name
     */
    public function setGroupName(string $name): Managed
    {
        return $this->setGroupId(Systemic::$os->groupNameToGroupId($name));
    }

    /**
     * Get process group name
     */
    public function getGroupName(): string
    {
        if (extension_loaded('posix')) {
            $output = posix_getgrgid($this->getGroupId());
            return $output['name'];
        }

        exec('getent group ' . escapeshellarg((string)$this->getGroupId()), $output);

        if (isset($output[0])) {
            $parts = explode(':', $output[0]);

            if (null !== ($output = array_shift($parts))) {
                return $output;
            }
        }

        throw Exceptional::Runtime(
            'Unable to extract process group name'
        );
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
    public function fork(): ?Managed
    {
        if (!$this->canFork()) {
            throw Exceptional::ComponentUnavailable(
                'This process is not capable of forking'
            );
        }

        $pid = pcntl_fork();

        if ($pid === -1) {
            throw Exceptional::Runtime(
                'The process did not fork successfully'
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
