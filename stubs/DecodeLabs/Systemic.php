<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Systemic\Context as Inst;
use DecodeLabs\Systemic\Plugins\Locale as LocalePlugin;
use DecodeLabs\Systemic\Plugins\Os as OsPlugin;
use DecodeLabs\Systemic\Plugins\Process as ProcessPlugin;
use DecodeLabs\Systemic\Plugins\Timezone as TimezonePlugin;
use DecodeLabs\Veneer\Plugin\Wrapper as PluginWrapper;

class Systemic implements Proxy
{
    use ProxyTrait;

    const VENEER = 'DecodeLabs\Systemic';
    const VENEER_TARGET = Inst::class;

    public static Inst $instance;
    /** @var LocalePlugin|PluginWrapper<LocalePlugin> $locale */
    public static LocalePlugin|PluginWrapper $locale;
    /** @var OsPlugin|PluginWrapper<OsPlugin> $os */
    public static OsPlugin|PluginWrapper $os;
    /** @var ProcessPlugin|PluginWrapper<ProcessPlugin> $process */
    public static ProcessPlugin|PluginWrapper $process;
    /** @var TimezonePlugin|PluginWrapper<TimezonePlugin> $timezone */
    public static TimezonePlugin|PluginWrapper $timezone;

};
