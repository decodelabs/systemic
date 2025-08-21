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
use DecodeLabs\Systemic\Manifold;
use DecodeLabs\Systemic\Result;

class BlindCapture implements
    Controller,
    ResultProvider
{
    use ControllerTrait {
        __construct as traitConstruct;
    }

    protected Result $result;

    public function __construct(
        Manifold $manifold
    ) {
        $this->traitConstruct($manifold);
        $this->result = new Result();
    }

    public function getInputStream(): ?Stream
    {
        return null;
    }

    public function provideInput(): ?string
    {
        return null;
    }

    public function consumeOutput(
        string $data
    ): void {
        $this->result->appendOutput($data);
    }

    public function consumeError(
        string $data
    ): void {
        $this->result->appendError($data);
    }


    public function registerFailure(): void
    {
        $this->result->registerFailure();
    }

    public function registerCompletion(
        int $exit
    ): void {
        $this->result->registerCompletion($exit);
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function wasSuccessful(): bool
    {
        return $this->result->wasSuccessful();
    }
}
