<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins;

use DateTimeZone;
use DecodeLabs\Glitch\Dumpable;
use DecodeLabs\Systemic\Context;
use DecodeLabs\Veneer\Plugin;

class Timezone implements Plugin, Dumpable
{
    use GetterSetterPluginTrait;

    protected $timezone;

    /**
     * Init with parent factory
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        date_default_timezone_set('UTC');
        $this->timezone = new DateTimeZone('UTC');
    }

    /**
     * Set current value
     */
    protected function setCurrent($value): DateTimeZone
    {
        if (!$value instanceof DateTimeZone) {
            $value = new DateTimeZone((string)$value);
        }

        $this->timezone = $value;
        return $value;
    }

    /**
     * Get current value
     */
    protected function getCurrent(): DateTimeZone
    {
        return $this->timezone;
    }
}
