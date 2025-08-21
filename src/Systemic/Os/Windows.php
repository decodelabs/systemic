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


    public function getDistribution(): string
    {
        if ($this->distribution === null) {
            $this->distribution = $this->lookupDistribution();
        }

        return $this->distribution;
    }

    private function lookupDistribution(): string
    {
        $res = $this->wmi->ExecQuery('SELECT * FROM Win32_OperatingSystem');

        foreach ($res as $os) {
            return $os->Caption;
        }

        return 'Windows NT';
    }



    public function userIdToUserName(
        int $id
    ): string {
        return (string)$id;
    }

    public function userNameToUserId(
        string $name
    ): int {
        return (int)$name;
    }

    public function groupIdToGroupName(
        int $id
    ): string {
        return (string)$id;
    }

    public function groupNameToGroupId(
        string $name
    ): int {
        return (int)$name;
    }


    public function which(
        string $binaryName
    ): string {
        throw Exceptional::ComponentUnavailable('Which on Windows is not available yet: ' . $binaryName);
    }
}
