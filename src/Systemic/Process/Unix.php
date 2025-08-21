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

    public static function getCurrentProcessId(): int
    {
        if (extension_loaded('posix')) {
            return posix_getpid();
        } else {
            if (false === ($output = getmypid())) {
                throw Exceptional::UnexpectedValue(
                    message: 'Unable to get current PID'
                );
            }

            return $output;
        }
    }


    public function getParentProcessId(): int
    {
        if ($this->parentProcessId !== null) {
            return $this->parentProcessId;
        }

        exec('ps -o ppid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (!isset($output[0])) {
            throw Exceptional::Runtime(
                message: 'Unable to extract parent process id'
            );
        }

        return $this->parentProcessId = (int)$output[0];
    }


    public function isAlive(): bool
    {
        return self::isProcessIdLive($this->processId);
    }

    public function kill(): bool
    {
        return $this->sendSignal('SIGTERM');
    }

    public function sendSignal(
        Signal|string|int $signal
    ): bool {
        $signal = Signal::create($signal);

        if (extension_loaded('posix')) {
            $output = posix_kill($this->processId, $signal->number);
        } else {
            exec('kill -' . $signal->number . ' ' . $this->processId);
            $output = true;
        }

        if (
            $output &&
            in_array($signal->name, ['SIGINT', 'SIGTERM', 'SIGQUIT']) &&
            $this->pidFile &&
            file_exists($this->pidFile)
        ) {
            unlink($this->pidFile);
        }

        return $output;
    }



    public function getOwnerId(): int
    {
        exec('ps -o euid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract process owner id'
        );
    }

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
            return explode(':', $output[0])[0];
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract process owner name'
        );
    }

    public function getGroupId(): int
    {
        exec('ps -o egid --no-heading --pid ' . escapeshellarg((string)$this->processId), $output);

        if (isset($output[0])) {
            return (int)trim($output[0]);
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract process owner id'
        );
    }

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
            return explode(':', $output[0])[0];
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract process group name'
        );
    }

    public function isPrivileged(): bool
    {
        $uid = $this->getOwnerId();
        return $uid == 0;
    }
}
