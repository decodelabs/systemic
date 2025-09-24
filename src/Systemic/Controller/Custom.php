<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Controller;

use ArrayIterator;
use DecodeLabs\Coercion;
use DecodeLabs\Deliverance\Channel\Stream;
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\ControllerTrait;
use DecodeLabs\Systemic\Manifold\Pipe as PipeManifold;
use DecodeLabs\Systemic\Result;
use Generator;
use Iterator;
use IteratorAggregate;

class Custom implements
    Controller,
    ResultProvider
{
    use ControllerTrait {
        __construct as traitConstruct;
    }

    /**
     * @var Iterator<string>
     */
    protected Iterator $data;
    protected bool $iterating = false;
    protected string $output = '';
    protected string $error = '';
    protected Result $result;
    protected ?bool $success = null;

    /**
     * @param callable(Custom): iterable<string> $callback
     */
    public function __construct(
        callable $callback,
    ) {
        $this->traitConstruct(new PipeManifold());
        $this->result = new Result();

        $data = $callback($this);

        if ($data instanceof IteratorAggregate) {
            $data = $data->getIterator();
        }

        if (!$data instanceof Iterator) {
            if (is_array($data)) {
                $data = new ArrayIterator($data);
            } else {
                $data = new ArrayIterator([Coercion::asString($data)]);
            }
        }

        $this->data = $data;
    }


    public function getInputStream(): ?Stream
    {
        return null;
    }

    public function provideInput(): ?string
    {
        if ($this->iterating) {
            $this->data->next();
        }

        $this->iterating = true;

        if (!$this->data->valid()) {
            return null;
        }

        $output = $this->data->current();
        return $output;
    }

    public function consumeOutput(
        string $data
    ): void {
        $this->output .= $data;
    }

    public function consumeError(
        string $data
    ): void {
        $this->error .= $data;
    }

    public function read(): ?string
    {
        if (!strlen($this->output)) {
            return null;
        }

        $output = $this->output;
        $this->output = '';
        return $output;
    }

    public function readError(): ?string
    {
        if (!strlen($this->error)) {
            return null;
        }

        $output = $this->error;
        $this->error = '';
        return $output;
    }

    public function closeInput(): void
    {
        if (isset($this->manifold->streams[0])) {
            $this->manifold->streams[0]->close();
            unset($this->manifold->streams[0]);
        }
    }

    public function capture(): Generator
    {
        $written = false;

        while (true) {
            $this->result->appendOutput($output = $this->read());
            $this->result->appendError($error = $this->readError());

            $writtenNow =
                $output !== null ||
                $error !== null;

            $written =
                $written ||
                $writtenNow;

            if (
                (
                    $written &&
                    !$writtenNow
                ) ||
                !$this->result->hasLaunched() ||
                $this->result->hasCompleted()
            ) {
                break;
            }

            yield null;
        }
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
        return $this->success ?? true;
    }
}
