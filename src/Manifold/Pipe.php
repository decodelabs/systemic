<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Systemic\Manifold;
use DecodeLabs\Systemic\ManifoldTrait;

class Pipe implements Manifold
{
    use ManifoldTrait;

    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array
    {
        return [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
    }
}
