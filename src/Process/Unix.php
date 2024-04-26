<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Eventful\Signal;
use DecodeLabs\Exceptional;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\ProcessTrait;

class Unix implements Process
{
    use ProcessTrait;

    /**
     * Check if process under PID is still running
     */
    public static function isProcessIdLive(
        int $pid
    ): bool {
        if (extension_loaded('posix')) {
            $output = posix_kill($pid, 0);

            if (!$output) {
                $output = posix_get_last_error() == 1;
            }

            return $output;
        } else {
            exec('ps -o pid --no-heading --pid ' . escapeshellarg((string)$pid), $output);
            return isset($output[0]);
        }
    }

    /**
     * Get PID of current process
     */
    public static function getCurrentProcessId(): int
    {
        if (extension_loaded('posix')) {
            return posix_getpid();
        } else {
            if (false === ($output = getmypid())) {
                throw Exceptional::UnexpectedValue('Unable to get current PID');
            }

            return $output;
        }
    }


    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId !== null) {
            return $this->parentProcessId;
        }

        exec('ps -o ppid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (!isset($output[0])) {
            throw Exceptional::Runtime(
                'Unable to extract parent process id'
            );
        }

        return $this->parentProcessId = (int)$output[0];
    }


    /**
     * Check if process is still running
     */
    public function isAlive(): bool
    {
        return self::isProcessIdLive($this->processId);
    }

    /**
     * Send kill signal
     */
    public function kill(): bool
    {
        return $this->sendSignal('SIGTERM');
    }

    /**
     * Send a signal to this process
     */
    public function sendSignal(
        Signal|string|int $signal
    ): bool {
        $signal = Signal::create($signal);

        if (extension_loaded('posix')) {
            $output = posix_kill($this->processId, $signal->getNumber());
        } else {
            exec('kill -' . $signal->getNumber() . ' ' . $this->processId);
            $output = true;
        }

        if (
            $output &&
            in_array($signal->getName(), ['SIGINT', 'SIGTERM', 'SIGQUIT']) &&
            $this->pidFile &&
            file_exists($this->pidFile)
        ) {
            unlink($this->pidFile);
        }

        return $output;
    }



    /**
     * Get current process owner
     */
    public function getOwnerId(): int
    {
        exec('ps -o euid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            'Unable to extract process owner id'
        );
    }

    /**
     * Get current owner name
     */
    public function getOwnerName(): string
    {
        if (extension_loaded('posix')) {
            $output = posix_getpwuid($this->getOwnerId());

            if ($output !== false) {
                return $output['name'];
            }
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
     * Get process group id
     */
    public function getGroupId(): int
    {
        exec('ps -o egid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            'Unable to extract process owner id'
        );
    }

    /**
     * Get process group name
     */
    public function getGroupName(): string
    {
        if (extension_loaded('posix')) {
            $output = posix_getgrgid($this->getGroupId());

            if ($output !== false) {
                return $output['name'];
            }
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
     * Is this process running as root?
     */
    public function isPrivileged(): bool
    {
        $uid = $this->getOwnerId();
        return $uid == 0;
    }
}
