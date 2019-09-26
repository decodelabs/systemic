<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins\Os;

class Windows extends Base
{
    protected static $wmi;

    protected $platformType = 'Windows';
    protected $distribution;

    /**
     * Init with name, instantiate WMI
     */
    protected function __construct(string $name)
    {
        parent::__construct($name);

        self::$wmi = new \COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2");
    }

    /**
     * Get active WMI COM
     */
    public function getWMI(): \COM
    {
        return self::$wmi;
    }

    /**
     * Get specific OS distribution
     */
    public function getDistribution()
    {
        if ($this->distribution === null) {
            $this->distribution = $this->lookupDistribution();
        }

        return $this->distribution;
    }

    /**
     * Lookup version of windows
     */
    private function lookupDistribution()
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
        return $id;
    }

    /**
     * Get system user id from name
     */
    public function userNameToUserId(string $name): int
    {
        return $name;
    }

    /**
     * Get system group name from id
     */
    public function groupIdToGroupName(int $id): string
    {
        return $id;
    }

    /**
     * Get system group id from name
     */
    public function groupNameToGroupId(string $name): int
    {
        return $name;
    }


    /**
     * Lookup system binary location
     */
    public function which(string $binaryName): string
    {
        Glitch::incomplete($binaryName);
    }
}
