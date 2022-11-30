<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Controller;

use DecodeLabs\Deliverance\Channel\Stream;
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\ControllerTrait;

class Severed implements Controller
{
    use ControllerTrait;

    public function getInputStream(): ?Stream
    {
        return null;
    }

    public function provideInput(): ?string
    {
        return null;
    }

    public function consumeOutput(string $data): void
    {
    }

    public function consumeError(string $data): void
    {
    }


    public function registerFailure(): void
    {
    }

    public function registerCompletion(int $exit): void
    {
    }

    public function wasSuccessful(): bool
    {
        return true;
    }
}
