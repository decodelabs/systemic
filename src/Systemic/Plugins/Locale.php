<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Systemic\Context;
use DecodeLabs\Veneer\FacadePlugin;

use DecodeLabs\Glitch\Inspectable;

use Locale as SysLocale;

class Locale implements FacadePlugin, Inspectable
{
    use GetterSetterPluginTrait;

    /**
     * Set current value
     */
    protected function setCurrent($value): string
    {
        $value = SysLocale::canonicalize($value);
        SysLocale::setDefault($value);
        return $value;
    }

    /**
     * Get current value
     */
    protected function getCurrent(): string
    {
        return SysLocale::getDefault();
    }


    /**
     * Get current display name
     */
    public function getName(?string $inLocale=null): string
    {
        return SysLocale::getDisplayName($this->get(), $inLocale);
    }


    /**
     * Get current language
     */
    public function getLanguage(): string
    {
        return SysLocale::getPrimaryLanguage($this->get());
    }

    /**
     * Get current language display name
     */
    public function getLanguageName(?string $inLocale=null): string
    {
        return SysLocale::getDisplayLanguage($this->get(), $inLocale);
    }


    /**
     * Get current region
     */
    public function getRegion(): string
    {
        return SysLocale::getRegion($this->get());
    }

    /**
     * Get current region display name
     */
    public function getRegionName(?string $inLocale=null): string
    {
        return SysLocale::getDisplayRegion($this->get(), $inLocale);
    }


    /**
     * Get current script
     */
    public function getScript(): ?string
    {
        $output = SysLocale::getScript($this->get());

        if (!strlen($output)) {
            $output = null;
        }

        return $output;
    }

    /**
     * Get current script display name
     */
    public function getScriptName(?string $inLocale=null): ?string
    {
        $output = SysLocale::getDisplayScript($this->get(), $inLocale);

        if (!strlen($output)) {
            $output = null;
        }

        return $output;
    }


    /**
     * Get current variants
     */
    public function getVariants(): array
    {
        $output = SysLocale::getAllVariants($this->get());

        if ($output === null) {
            $output = [];
        }

        return $output;
    }

    /**
     * Get current script variant name
     */
    public function getVariantName(?string $inLocale=null): ?string
    {
        $output = SysLocale::getDisplayVariant($this->get(), $inLocale);

        if (!strlen($output)) {
            $output = null;
        }

        return $output;
    }


    /**
     * Get keywords
     */
    public function getKeywords(): array
    {
        return SysLocale::getKeywords($this->get());
    }
}
