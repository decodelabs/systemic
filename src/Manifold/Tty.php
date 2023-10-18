<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Systemic\Manifold;
use DecodeLabs\Systemic\ManifoldTrait;

class Tty implements Manifold
{
    use ManifoldTrait;

    public static function isSupported(): bool
    {
        static $output;

        return $output ??= (
            \DIRECTORY_SEPARATOR === '/' &&
            stream_isatty(\STDOUT)
        );
    }

    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array
    {
        return [
            0 => ['file', '/dev/tty', 'r'],
            1 => ['file', '/dev/tty', 'w'],
            2 => ['file', '/dev/tty', 'w']
        ];
    }
}
