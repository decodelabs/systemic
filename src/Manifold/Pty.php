<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Manifold;

use DecodeLabs\Coercion;
use DecodeLabs\Systemic\Command;
use DecodeLabs\Systemic\ManifoldAbstract;

class Pty extends ManifoldAbstract
{
    private static ?bool $supported = null;
    protected ?string $snapshot = null;

    public static function isSupported(): bool
    {
        return self::$supported ??= (
            \DIRECTORY_SEPARATOR !== '\\' &&
            (bool)proc_open('echo 1 >/dev/null', [['pty'], ['pty'], ['pty']], $pipes)
        );
    }

    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array
    {
        return [
            0 => ['pty'],
            1 => ['pty'],
            2 => ['pty']
        ];
    }

    protected function onOpen(
        Command $command
    ): void {
        $this->snapshot = trim(Coercion::asString(`stty -g`));
        $this->setStty('-echo');
        $this->setStty('-icanon');
    }

    protected function onClose(): void
    {
        $this->setStty((string)$this->snapshot);
    }

    /**
     * Set stty config
     */
    protected function setStty(
        string $config
    ): void {
        system('stty \'' . $config . '\'');
    }
}
