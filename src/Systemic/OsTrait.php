<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Nuance\Entity\NativeObject as NuanceEntity;
use DecodeLabs\Systemic\Os\Darwin;
use DecodeLabs\Systemic\Os\Linux;
use DecodeLabs\Systemic\Os\Unix;
use DecodeLabs\Systemic\Os\Windows;

/**
 * @phpstan-require-implements Os
 */
trait OsTrait
{
    public protected(set) string $name;

    public protected(set) string $platformType {
        get => $this->platformType ?? $this->name;
    }

    public string $distribution {
        get => $this->lookupDistribution();
    }

    public protected(set) string $version;
    public protected(set) string $release;
    public protected(set) string $hostName;


    public function __construct(
        string $name
    ) {
        $this->name = $name;
        $this->version = php_uname('v');
        $this->release = php_uname('r');
        $this->hostName = php_uname('n');
    }


    protected function lookupDistribution(): string
    {
        return $this->name;
    }

    public function isWindows(): bool
    {
        return $this instanceof Windows;
    }

    public function isUnix(): bool
    {
        return $this instanceof Unix;
    }

    public function isLinux(): bool
    {
        return $this instanceof Linux;
    }

    public function isMac(): bool
    {
        return $this instanceof Darwin;
    }

    public function toNuanceEntity(): NuanceEntity
    {
        $entity = new NuanceEntity($this);

        $entity->setProperty('name', $this->name, 'protected');
        $entity->setProperty('platformType', $this->platformType, 'protected');
        $entity->setProperty('distribution', $this->distribution, 'protected');
        $entity->setProperty('version', $this->version, 'protected');
        $entity->setProperty('release', $this->release, 'protected');
        $entity->setProperty('hostName', $this->hostName, 'protected');

        return $entity;
    }
}
