<?php
/**
 * This file is part of the Veneer package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);

/**
 * global helpers
 */
namespace DecodeLabs\Systemic
{
    use DecodeLabs\Systemic;
    use DecodeLabs\Systemic\Context;
    use DecodeLabs\Veneer;

    // Register the Veneer proxy
    Veneer::register(Context::class, Systemic::class);
}
