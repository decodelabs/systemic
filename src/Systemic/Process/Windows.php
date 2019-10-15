<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\ProcessTrait;

class Windows implements Process
{
    use ProcessTrait;

    const EXIT_SUCCESS = 0;
    const EXIT_ACCESS_DENIED = 2;
    const EXIT_PRIVILEGES = 3;
    const EXIT_UNKNOWN_FAILURE = 8;
    const EXIT_PATH_NOT_FOUND = 9;
    const EXIT_INVALID_PARAMETER = 21;

    /**
     * Check if process under PID is still running
     */
    public static function isProcessIdLive(int $pid): bool
    {
        $wmi = Systemic::$os->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\''.$pid.'\'');

        foreach ($procs as $process) {
            return true;
        }

        return false;
    }

    /**
     * Get PID of current process
     */
    public static function getCurrentProcessId(): int
    {
        return getmypid();
    }

    /**
     * Check if process is still running
     */
    public function isAlive(): bool
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\''.$this->getProcessId().'\'');

        foreach ($procs as $process) {
            return true;
        }

        return false;
    }

    /**
     * Send kill signal
     */
    public function kill(): bool
    {
        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\''.$this->getProcessId().'\'');
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

    protected function getWmi()
    {
        return Systemic::$os->getWmi();
    }
}
