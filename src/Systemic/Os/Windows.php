<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Os;

use COM;
use DecodeLabs\Exceptional;
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Systemic\Os;
use DecodeLabs\Systemic\OsTrait;

class Windows implements
    Os,
    Dumpable
{
    use OsTrait;

    public protected(set) COM $wmi {
        get => $this->wmi ??= new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2");
    }

    public protected(set) ?string $platformType = 'Windows';
    public protected(set) ?string $distribution = null;


    /**
     * Get specific OS distribution
     */
    public function getDistribution(): string
    {
        if ($this->distribution === null) {
            $this->distribution = $this->lookupDistribution();
        }

        return $this->distribution;
    }

    /**
     * Lookup version of windows
     */
    private function lookupDistribution(): string
    {
        $res = $this->wmi->ExecQuery('SELECT * FROM Win32_OperatingSystem');

        foreach ($res as $os) {
            return $os->Caption;
        }

        return 'Windows NT';
    }



    /**
     * Get system user name from id
     */
    public function userIdToUserName(
        int $id
    ): string {
        return (string)$id;
    }

    /**
     * Get system user id from name
     */
    public function userNameToUserId(
        string $name
    ): int {
        return (int)$name;
    }

    /**
     * Get system group name from id
     */
    public function groupIdToGroupName(
        int $id
    ): string {
        return (string)$id;
    }

    /**
     * Get system group id from name
     */
    public function groupNameToGroupId(
        string $name
    ): int {
        return (int)$name;
    }


    /**
     * Lookup system binary location
     */
    public function which(
        string $binaryName
    ): string {
        throw Exceptional::ComponentUnavailable('Which on Windows is not available yet: ' . $binaryName);
    }
}
