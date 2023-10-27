<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Controller;

use DecodeLabs\Deliverance;
use DecodeLabs\Deliverance\Channel\Stream;
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\ControllerTrait;
use DecodeLabs\Systemic\Manifold;

class Http implements Controller
{
    use ControllerTrait {
        __construct as traitConstruct;
    }

    protected Stream $output;
    protected ?bool $success = null;

    public function __construct(
        Manifold $manifold
    ) {
        $this->traitConstruct($manifold);

        $this->output = Deliverance::openHttpOutputStream();
    }

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
        $this->output->write($data);
    }

    public function consumeError(string $data): void
    {
        $this->output->write($data);
    }


    public function registerFailure(): void
    {
        $this->success = false;
    }

    public function registerCompletion(int $exit): void
    {
        $this->success = $exit === 0;
    }

    public function wasSuccessful(): bool
    {
        return (bool)$this->success;
    }
}
