<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Systemic\Manifold;
use DecodeLabs\Systemic\ManifoldTrait;

class DevNull implements Manifold
{
    use ManifoldTrait;

    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array
    {
        if (!$devNull = fopen('/dev/null', 'c')) {
            return [];
        }

        return [
            0 => $devNull,
            1 => $devNull,
            2 => $devNull
        ];
    }
}
