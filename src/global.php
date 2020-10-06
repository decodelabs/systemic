<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

/**
 * global helpers
 */
namespace DecodeLabs\Systemic
{
    use DecodeLabs\Systemic;
    use DecodeLabs\Veneer;

    // Register the Veneer proxy
    Veneer::register(Context::class, Systemic::class);
}
