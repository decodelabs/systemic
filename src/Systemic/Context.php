<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic;

use DecodeLabs\Veneer\FacadeTarget;
use DecodeLabs\Veneer\FacadeTargetTrait;
use DecodeLabs\Veneer\FacadePluginAccessTarget;
use DecodeLabs\Veneer\FacadePluginAccessTargetTrait;
use DecodeLabs\Veneer\FacadePlugin;

use DecodeLabs\Systemic\Plugins\Os\Base as Os;

class Context implements FacadeTarget, FacadePluginAccessTarget
{
    use FacadeTargetTrait;
    use FacadePluginAccessTargetTrait;

    const FACADE = 'Systemic';

    const PLUGINS = [
        'locale',
        'timezone',
        'os',
        'process'
    ];


    /**
     * Stub to get empty plugin list to avoid broken targets
     */
    public function getFacadePluginNames(): array
    {
        return static::PLUGINS;
    }


    /**
     * Load factory plugins
     */
    public function loadFacadePlugin(string $name): FacadePlugin
    {
        if (!in_array($name, self::PLUGINS)) {
            throw Glitch::EInvalidArgument($name.' is not a recognised facade plugin');
        }

        switch ($name) {
            case 'os':
                return Os::load();

            default:
                $class = '\\DecodeLabs\\Systemic\\Plugins\\'.ucfirst($name);
                return new $class($this);
        }
    }
}
