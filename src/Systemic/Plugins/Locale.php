<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Coercion;
use DecodeLabs\Glitch\Dumpable;
use DecodeLabs\Veneer\Plugin;
use Locale as SysLocale;

class Locale implements Plugin, Dumpable
{
    /**
     * @use GetterSetterPluginTrait<string>
     */
    use GetterSetterPluginTrait;

    /**
     * Set current value
     *
     * @param string $value
     */
    protected function setCurrent($value): string
    {
        $value = SysLocale::canonicalize(Coercion::toString($value));
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
    public function getName(?string $inLocale = null): string
    {
        if ($inLocale !== null) {
            return SysLocale::getDisplayName($this->get(), $inLocale);
        } else {
            return SysLocale::getDisplayName($this->get());
        }
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
    public function getLanguageName(?string $inLocale = null): string
    {
        if ($inLocale !== null) {
            return SysLocale::getDisplayLanguage($this->get(), $inLocale);
        } else {
            return SysLocale::getDisplayLanguage($this->get());
        }
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
    public function getRegionName(?string $inLocale = null): string
    {
        if ($inLocale !== null) {
            return SysLocale::getDisplayRegion($this->get(), $inLocale);
        } else {
            return SysLocale::getDisplayRegion($this->get());
        }
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
    public function getScriptName(?string $inLocale = null): ?string
    {
        if ($inLocale !== null) {
            $output = SysLocale::getDisplayScript($this->get(), $inLocale);
        } else {
            $output = SysLocale::getDisplayScript($this->get());
        }

        if (!strlen($output)) {
            $output = null;
        }

        return $output;
    }


    /**
     * Get current variants
     *
     * @return array<string>
     */
    public function getVariants(): array
    {
        $output = SysLocale::getAllVariants($this->get());

        if (empty($output)) {
            $output = [];
        }

        return $output;
    }

    /**
     * Get current script variant name
     */
    public function getVariantName(?string $inLocale = null): ?string
    {
        if ($inLocale) {
            $output = SysLocale::getDisplayVariant($this->get(), $inLocale);
        } else {
            $output = SysLocale::getDisplayVariant($this->get());
        }

        if (!strlen($output)) {
            $output = null;
        }

        return $output;
    }


    /**
     * Get keywords
     *
     * @return array<string>
     */
    public function getKeywords(): array
    {
        if (false === ($output = SysLocale::getKeywords($this->get()))) {
            return [];
        }

        return $output;
    }
}
