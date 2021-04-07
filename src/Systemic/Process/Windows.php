<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use COM;

use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\ProcessTrait;

class Windows implements Process
{
    use ProcessTrait;

    public const EXIT_SUCCESS = 0;
    public const EXIT_ACCESS_DENIED = 2;
    public const EXIT_PRIVILEGES = 3;
    public const EXIT_UNKNOWN_FAILURE = 8;
    public const EXIT_PATH_NOT_FOUND = 9;
    public const EXIT_INVALID_PARAMETER = 21;

    /**
     * Check if process under PID is still running
     */
    public static function isProcessIdLive(int $pid): bool
    {
        $wmi = Systemic::$os->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $pid . '\'');

        foreach ($procs as $process) {
            if ($process !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get PID of current process
     */
    public static function getCurrentProcessId(): int
    {
        if (false === ($output = getmypid())) {
            throw Exceptional::UnexpectedValue('Unable to get current PID');
        }

        return $output;
    }

    /**
     * Check if process is still running
     */
    public function isAlive(): bool
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $this->getProcessId() . '\'');

        foreach ($procs as $process) {
            if ($process !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send kill signal
     */
    public function kill(): bool
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $this->getProcessId() . '\'');
        $output = 0;

        foreach ($procs as $process) {
            $output = $process->Terminate();
            break;
        }

        return $output == 0;
    }

    /**
     * Send a signal to this process
     */
    public function sendSignal($signal): bool
    {
        return false;
    }

    /**
     * Is this process running as root?
     */
    public function isPrivileged(): bool
    {
        return true;
    }

    protected function getWmi(): COM
    {
        return Systemic::$os->getWmi();
    }
}
