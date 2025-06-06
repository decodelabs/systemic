<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Archetype;
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use DecodeLabs\Systemic\Os\Darwin;
use DecodeLabs\Systemic\Os\Linux;
use DecodeLabs\Systemic\Os\Unix;
use DecodeLabs\Systemic\Os\Windows;

abstract class OsAbstract implements
    Os,
    Dumpable
{
    protected string $name;
    protected ?string $platformType = null;
    protected string $version;
    protected string $release;
    protected string $hostName;


    /**
     * Load for current OS
     */
    public static function load(
        ?string $name = null
    ): Os {
        if ($name === null) {
            $name = php_uname('s');

            if (substr(strtolower($name), 0, 3) == 'win') {
                $name = 'Windows';
            }
        }

        $class = Archetype::resolve(
            Os::class,
            $name,
            Unix::class
        );

        return new $class($name);
    }


    /**
     * Init with OS info
     */
    public function __construct(
        string $name
    ) {
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
     * Is a windows platform?
     */
    public function isWindows(): bool
    {
        return $this instanceof Windows;
    }

    /**
     * Is this a unix platform?
     */
    public function isUnix(): bool
    {
        return $this instanceof Unix;
    }

    /**
     * Is this a Linux platform?
     */
    public function isLinux(): bool
    {
        return $this instanceof Linux;
    }

    /**
     * Is this a Mac platform?
     */
    public function isMac(): bool
    {
        return $this instanceof Darwin;
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

    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);

        $entity->setProperty('name', $this->getName(), 'protected');
        $entity->setProperty('platformType', $this->getPlatformType(), 'protected');
        $entity->setProperty('distribution', $this->getDistribution(), 'protected');
        $entity->setProperty('version', $this->getVersion(), 'protected');
        $entity->setProperty('release', $this->getRelease(), 'protected');
        $entity->setProperty('hostName', $this->getHostName(), 'protected');

        return $entity;
    }
}
