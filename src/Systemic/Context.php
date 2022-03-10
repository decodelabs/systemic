<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Exceptional;
use DecodeLabs\Systemic\Plugins\Locale as LocalePlugin;
use DecodeLabs\Systemic\Plugins\Os;
use DecodeLabs\Systemic\Plugins\Os\Base as OsBase;
use DecodeLabs\Systemic\Plugins\Process as ProcessPlugin;
use DecodeLabs\Systemic\Plugins\Timezone as TimezonePlugin;

use DecodeLabs\Veneer\Plugin\AccessTarget as VeneerPluginAccessTarget;
use DecodeLabs\Veneer\Plugin\AccessTargetTrait as VeneerPluginAccessTargetTrait;
use DecodeLabs\Veneer\Plugin as VeneerPlugin;
use DecodeLabs\Veneer\Plugin\Provider as VeneerPluginProvider;
use DecodeLabs\Veneer\Plugin\ProviderTrait as VeneerPluginProviderTrait;

/**
 * @property LocalePlugin $locale
 * @property Os $os
 * @property ProcessPlugin $process
 * @property TimezonePlugin $timezone
 */
class Context implements VeneerPluginProvider, VeneerPluginAccessTarget
{
    use VeneerPluginProviderTrait;
    use VeneerPluginAccessTargetTrait;

    public const PLUGINS = [
        'locale',
        'os',
        'process',
        'timezone'
    ];


    /**
     * Stub to get empty plugin list to avoid broken targets
     */
    public function getVeneerPluginNames(): array
    {
        return static::PLUGINS;
    }


    /**
     * Load factory plugins
     */
    public function loadVeneerPlugin(string $name): VeneerPlugin
    {
        if (!in_array($name, self::PLUGINS)) {
            throw Exceptional::InvalidArgument(
                $name . ' is not a recognised Veneer plugin'
            );
        }

        switch ($name) {
            case 'os':
                return OsBase::load();

            default:
                /** @var class-string<VeneerPlugin> */
                $class = '\\DecodeLabs\\Systemic\\Plugins\\' . ucfirst($name);
                return new $class($this);
        }
    }
}
