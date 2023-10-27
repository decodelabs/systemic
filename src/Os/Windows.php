<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Os;

use COM;
use DecodeLabs\Glitch\Proxy as Glitch;
use DecodeLabs\Systemic\OsAbstract;

class Windows extends OsAbstract
{
    protected static COM $wmi;

    protected ?string $platformType = 'Windows';
    protected ?string $distribution = null;

    /**
     * Init with name, instantiate WMI
     */
    protected function __construct(string $name)
    {
        parent::__construct($name);

        self::$wmi = new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2");
    }

    /**
     * Get active WMI COM
     */
    public function getWmi(): COM
    {
        return self::$wmi;
    }

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
        $res = self::$wmi->ExecQuery('SELECT * FROM Win32_OperatingSystem');

        foreach ($res as $os) {
            return $os->Caption;
        }

        return 'Windows NT';
    }



    /**
     * Get system user name from id
     */
    public function userIdToUserName(int $id): string
    {
        return (string)$id;
    }

    /**
     * Get system user id from name
     */
    public function userNameToUserId(string $name): int
    {
        return (int)$name;
    }

    /**
     * Get system group name from id
     */
    public function groupIdToGroupName(int $id): string
    {
        return (string)$id;
    }

    /**
     * Get system group id from name
     */
    public function groupNameToGroupId(string $name): int
    {
        return (int)$name;
    }


    /**
     * Lookup system binary location
     */
    public function which(string $binaryName): string
    {
        Glitch::incomplete($binaryName);
    }
}
