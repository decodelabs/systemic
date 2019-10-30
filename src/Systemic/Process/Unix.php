<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\ProcessTrait;

class Unix implements Process
{
    use ProcessTrait;

    /**
     * Check if process under PID is still running
     */
    public static function isProcessIdLive(int $pid): bool
    {
        exec('ps -o pid --no-heading --pid '.escapeshellarg((string)$pid), $output);
        return isset($output[0]);
    }

    /**
     * Get PID of current process
     */
    public static function getCurrentProcessId(): int
    {
        if (extension_loaded('posix')) {
            return posix_getpid();
        } else {
            return getmypid();
        }
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
        if (extension_loaded('posix')) {
            return posix_kill($this->processId, SIGTERM);
        } else {
            exec('kill -'.SIGTERM.' '.$this->processId);
            return true;
        }
    }

    /**
     * Send a signal to this process
     */
    public function sendSignal($signal): bool
    {
        $signal = Signal::create($signal);

        if (extension_loaded('posix')) {
            return posix_kill($this->processId, $signal->getNumber());
        } else {
            exec('kill -'.$signal->getNumber().' '.$this->processId);
            return true;
        }
    }

    /**
     * Is this process running as root?
     */
    public function isPrivileged(): bool
    {
        if ($this instanceof Managed) {
            $uid = $this->getOwnerId();
        } elseif (extension_loaded('posix')) {
            $uid = posix_geteuid();
        } else {
            $uid = getmyuid();
        }

        return $uid == 0;
    }
}
