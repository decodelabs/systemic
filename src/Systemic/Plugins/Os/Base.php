<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins\Os;

use DecodeLabs\Glitch\Dumpable;
use DecodeLabs\Systemic\Plugins\Os;

abstract class Base implements Os, Dumpable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $platformType;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $release;

    /**
     * @var string
     */
    protected $hostName;

    /**
     * Load for current OS
     */
    public static function load(?string $name = null): Os
    {
        if ($name === null) {
            $name = php_uname('s');

            if (substr(strtolower($name), 0, 3) == 'win') {
                $name = 'Windows';
            }
        }

        $class = '\\DecodeLabs\\Systemic\\Plugins\\Os\\' . ucfirst($name);

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\Plugins\\Os\\Unix';
        }

        /** @phpstan-var class-string<Os> $class */
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


    /**
     * Can color shell
     */
    public function canColorShell(): bool
    {
        return false;
    }


    /**
     * Export for dump inspection
     */
    public function glitchDump(): iterable
    {
        yield 'properties' => [
            '*name' => $this->getName(),
            '*platformType' => $this->getPlatformType(),
            '*distribution' => $this->getDistribution(),
            '*version' => $this->getVersion(),
            '*release' => $this->getRelease(),
            '*hostName' => $this->getHostName()
        ];
    }
}
