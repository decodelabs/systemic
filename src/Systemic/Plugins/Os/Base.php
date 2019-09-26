<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins\Os;

use DecodeLabs\Systemic\Context;
use DecodeLabs\Systemic\Plugins\Os;

use DecodeLabs\Glitch\Dumper\Entity;
use DecodeLabs\Glitch\Dumper\Inspector;

abstract class Base implements Os
{
    protected $name;
    protected $platformType;
    protected $version;
    protected $release;
    protected $hostName;

    /**
     * Load for current OS
     */
    public static function load(?string $name=null): Os
    {
        if ($name === null) {
            $name = php_uname('s');

            if (substr(strtolower($name), 0, 3) == 'win') {
                $name = 'Windows';
            }
        }

        $class = '\\DecodeLabs\\Systemic\\Plugins\\Os\\'.ucfirst($name);

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\Plugins\\Os\\Unix';
        }

        return new $class($name);
    }


    /**
     * Init with OS info
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->version = php_uname('v');
        $this->release = php_uname('r');
        $this->hostName = php_uname('n');
    }


    /**
     * Get OS name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get platform type
     */
    public function getPlatformType(): string
    {
        return $this->platformType ?? $this->name;
    }

    /**
     * Get OS distribution
     */
    public function getDistribution(): string
    {
        return $this->name;
    }

    /**
     * Get OS version
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get OS release
     */
    public function getRelease(): string
    {
        return $this->release;
    }

    /**
     * Get host name
     */
    public function getHostName(): string
    {
        return $this->hostName;
    }

    /**
     * Inspect for Glitch
     */
    public function glitchInspect(Entity $entity, Inspector $inspector): void
    {
        $entity
            ->setProperty('*name', $inspector($this->getName()))
            ->setProperty('*platformType', $inspector($this->getPlatformType()))
            ->setProperty('*distribution', $inspector($this->getDistribution()))
            ->setProperty('*version', $inspector($this->getVersion()))
            ->setProperty('*release', $inspector($this->getRelease()))
            ->setProperty('*hostName', $inspector($this->getHostName()));
    }
}
