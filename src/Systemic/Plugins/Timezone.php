<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins;

use DateTimeZone;
use DecodeLabs\Coercion;
use DecodeLabs\Glitch\Dumpable;
use DecodeLabs\Systemic\Context;

class Timezone implements Dumpable
{
    /**
     * @use GetterSetterPluginTrait<DateTimeZone|string, DateTimeZone>
     */
    use GetterSetterPluginTrait;

    protected DateTimeZone $timezone;

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
    protected function setCurrent(mixed $value): DateTimeZone
    {
        if (!$value instanceof DateTimeZone) {
            $value = new DateTimeZone(Coercion::toString($value));
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
