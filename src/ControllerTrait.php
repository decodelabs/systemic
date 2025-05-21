<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Coercion;
use DecodeLabs\Deliverance\Channel\Stream;
use DecodeLabs\Deliverance\DataReceiver;
use DecodeLabs\Eventful\Dispatcher\Select as SelectDispatcher;
use DecodeLabs\Eventful\Signal;
use DecodeLabs\Exceptional;

/**
 * @phpstan-require-implements Controller
 */
trait ControllerTrait
{
    protected Manifold $manifold;
    protected SelectDispatcher $dispatcher;

    protected bool $stopping = false;

    public function __construct(
        Manifold $manifold
    ) {
        $this->manifold = $manifold;
        $this->dispatcher = new SelectDispatcher();
    }

    public function execute(
        Command $command
    ): ?Process {
        if (null === ($process = $this->manifold->open($command))) {
            $this->registerFailure();
            return null;
        }

        $streams = $this->manifold->getStreams();


        // Prepare broker data
        foreach ($command->inputProviders as $provider) {
            $provider->readBlocking = false;
        }


        // Input stream
        if (
            isset($this->manifold->streams[0]) &&
            $input = $this->getInputStream()
        ) {
            $input->readBlocking = false;

            $this->dispatcher->bindStreamRead($input, function (
                Stream $input
            ) {
                $this->manifold->streams[0]->write(
                    $input->read(2048)
                );
            });
        }

        // Output stream
        if (isset($streams[1])) {
            $this->dispatcher->bindStreamRead($streams[1], function (
                Stream $out
            ) use ($command) {
                if (null === ($data = $out->readAll())) {
                    return;
                }

                $this->consumeOutput($data);

                foreach ($command->outputReceivers as $receiver) {
                    if (!$receiver->isWritable()) {
                        continue;
                    }

                    $this->writeToReceiver($receiver, $data);
                }
            });
        }


        // Error
        if (isset($streams[2])) {
            $this->dispatcher->bindStreamRead($streams[2], function (
                Stream $err
            ) use ($command) {
                if (null === ($data = $err->readAll())) {
                    return;
                }

                $this->consumeError($data);

                foreach ($command->errorReceivers as $receiver) {
                    if (!$receiver->isWritable()) {
                        continue;
                    }

                    $this->writeToReceiver($receiver, $data);
                }
            });
        }

        // Signals
        if (!empty($signals = $command->getSignals())) {
            $this->dispatcher->bindSignal('passthrough', $signals, function (
                Signal|string|int $signal
            ) use ($process) {
                $process->sendSignal($signal);
            });
        }

        // Ticks
        $this->dispatcher->setTickHandler(function () use ($command) {
            $status = $this->manifold->getStatus();
            $running = ($status['running'] ?? false);
            $data = $this->provideInput();

            // Provide input
            if ($running) {
                if (isset($this->manifold->streams[0])) {
                    // Controller data iterator
                    $this->writeToReceiver(
                        $this->manifold->streams[0],
                        $data
                    );

                    // Command input providers
                    foreach ($command->inputProviders as $provider) {
                        if (!$provider->isReadable()) {
                            continue;
                        }

                        $this->writeToReceiver(
                            $this->manifold->streams[0],
                            $provider->readAll()
                        );
                    }
                }
            }

            // Complete process
            elseif (!$this->stopping) {
                $this->registerCompletion(Coercion::asInt($status['exitcode'] ?? 0));

                // Go round one more time to make sure everything is read
                $this->stopping = true;
            } else {
                return false;
            }
        });

        if (empty($streams)) {
            $this->dispatcher->bindTimer('keepAlive', 1, function () {
                // We need a timer to keep the tick handler running if there are no streams
            });
        }


        // Run dispatcher
        $this->dispatcher->listen();

        // Shutdown
        $this->dispatcher->removeAllBindings();
        $this->stopping = false;
        $this->manifold->close();

        return $process;
    }

    protected static function writeToReceiver(
        DataReceiver $receiver,
        ?string $data
    ): void {
        if ($data === null) {
            return;
        }

        $error = 0;

        while (strlen($data) > 0) {
            $written = $receiver->write($data, 2048);

            if ($written === 0) {
                if (++$error > 500) {
                    throw Exceptional::Runtime(
                        message: 'Unable to write to data receiver'
                    );
                }

                usleep(10000);
            } else {
                $error = 0;
            }

            $data = substr($data, $written);
        }
    }
}
