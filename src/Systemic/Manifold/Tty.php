<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Systemic\ManifoldAbstract;

class Tty extends ManifoldAbstract
{
    private static ?bool $supported = null;

    public static function isSupported(): bool
    {
        return self::$supported ??= (
            \DIRECTORY_SEPARATOR === '/' &&
            stream_isatty(\STDOUT)
        );
    }

    /**
     * @return array<int,resource|list<string>>
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
