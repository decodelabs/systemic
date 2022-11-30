<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Deliverance\Channel\Stream;

/**
 * @property array<int, Stream> $streams
 */
interface Manifold
{
    /**
     * @return array<int, resource|array<string>>
     */
    public function getDescriptors(): array;

    public function isOpen(): bool;

    public function open(Command $command): ?Process;

    /**
     * @return array<Stream>
     */
    public function getStreams(): array;

    /**
     * @return array<string, mixed>
     */
    public function getStatus(): ?array;

    public function close(): void;
}
