<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use COM;
use DecodeLabs\Eventful\Signal;
use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\ProcessTrait;

class Windows implements Process
{
    use ProcessTrait;

    public const ExitSuccess = 0;
    public const ExitAccessDenied = 2;
    public const ExitPrivileges = 3;
    public const ExitUnknownFailure = 8;
    public const ExitPathNotFound = 9;
    public const ExitInvalidParameter = 21;

    /**
     * Check if process under PID is still running
     */
    public static function isProcessIdLive(
        int $pid
    ): bool {
        $wmi = self::getWmi();
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
            throw Exceptional::UnexpectedValue(
                message: 'Unable to get current PID'
            );
        }

        return $output;
    }


    /**
     * Get parent process id
     */
    public function getParentProcessId(): int
    {
        if ($this->parentProcessId !== null) {
            return $this->parentProcessId;
        }

        $wmi = $this->getWmi();
        $procs = $wmi->ExecQuery('SELECT * FROM Win32_Process WHERE ProcessId=\'' . $this->getProcessId() . '\'');

        foreach ($procs as $process) {
            $this->parentProcessId = (int)$process->ParentProcessId;
            break;
        }

        return $this->parentProcessId;
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
    public function sendSignal(
        Signal|string|int $signal
    ): bool {
        return false;
    }

    /**
     * Is this process running as root?
     */
    public function isPrivileged(): bool
    {
        return true;
    }

    protected static function getWmi(): COM
    {
        $os = Systemic::getOs();

        /** @phpstan-ignore-next-line */
        return $os->wmi;
    }
}
