<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Coercion;
use DecodeLabs\Eventful\Dispatcher\Select as SelectDispatcher;

trait ControllerTrait
{
    protected Manifold $manifold;
    protected SelectDispatcher $dispatcher;

    public function __construct(
        Manifold $manifold
    ) {
        $this->manifold = $manifold;
        $this->dispatcher = new SelectDispatcher();
    }

    public function execute(Command $command): ?Process
    {
        if (null === ($process = $this->manifold->open($command))) {
            $this->registerFailure();
            return null;
        }

        $streams = $this->manifold->getStreams();


        // Input stream
        if (
            isset($streams[0]) &&
            $input = $this->getInputStream()
        ) {
            $this->dispatcher->bindStreamRead($input, function ($input) use ($streams) {
                $streams[0]->write($input->read(2048));
            });
        }

        // Output stream
        if (isset($streams[1])) {
            $this->dispatcher->bindStreamRead($streams[1], function ($out) {
                if (null === ($data = $out->readAll())) {
                    return;
                }

                $this->consumeOutput($data);
            });
        }


        // Error
        if (isset($streams[2])) {
            $this->dispatcher->bindStreamRead($streams[2], function ($err) {
                if (null === ($data = $err->readAll())) {
                    return;
                }

                $this->consumeError($data);
            });
        }

        // Signals
        if (!empty($signals = $command->getSignals())) {
            $this->dispatcher->bindSignal('passthrough', $signals, function ($signal) use ($process) {
                $process->sendSignal($signal);
            });
        }

        // Ticks
        $this->dispatcher->setTickHandler(function () {
            $status = $this->manifold->getStatus();

            /* @phpstan-ignore-next-line */
            if (!($status['running'] ?? false)) {
                $this->registerCompletion(Coercion::toInt($status['exitcode'] ?? 0));
                return false;
            }
        });

        if (empty($streams)) {
            $this->dispatcher->bindTimer('keepAlive', 1, function () {
                // Lah di dah
            });
        }


        $this->dispatcher->listen();
        $this->manifold->close();

        return $process;
    }
}
