<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Deliverance\Channel\Stream;

interface Controller
{
    public function execute(Command $command): ?Process;

    public function getInputStream(): ?Stream;
    public function provideInput(): ?string;
    public function consumeOutput(string $data): void;
    public function consumeError(string $data): void;

    public function registerFailure(): void;
    public function registerCompletion(int $exit): void;

    public function wasSuccessful(): bool;
}
