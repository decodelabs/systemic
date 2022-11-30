<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Systemic\Manifold;
use DecodeLabs\Systemic\ManifoldTrait;

class Severed implements Manifold
{
    use ManifoldTrait;

    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array
    {
        return [
        ];
    }
}
