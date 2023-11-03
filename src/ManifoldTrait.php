<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Coercion;
use DecodeLabs\Deliverance\Channel\Stream;
use DecodeLabs\Exceptional;
use DecodeLabs\Systemic;

trait ManifoldTrait
{
    /**
     * @var resource|null
     */
    protected $handle = null;

    /**
     * @var array<int, Stream>
     */
    public array $streams = [];

    public function isOpen(): bool
    {
        return $this->handle !== null;
    }

    /**
     * Open process command
     */
    public function open(
        Command $command
    ): ?Process {
        $this->onOpen($command);

        $handle = proc_open(
            (string)$command,
            $this->getDescriptors(),
            $pipes,
            $command->getWorkingDirectory(),
            $command->getEnv(),
            [
                'suppress_errors' => true,
                'bypass_shell' => true
            ]
        );

        if (!is_resource($handle)) {
            return null;
        }

        $this->handle = $handle;

        foreach ($pipes as $i => $pipe) {
            $this->streams[Coercion::toInt($i)] = (new Stream($pipe))->setReadBlocking(false);
        }

        if (!$status = $this->getStatus()) {
            $this->close();
            throw Exceptional::Runtime('Unable to get process statuc');
        }

        return Systemic::getProcess(Coercion::toInt($status['pid']));
    }

    protected function onOpen(
        Command $command
    ): void {
    }

    /**
     * @return array<Stream>
     */
    public function getStreams(): array
    {
        return $this->streams;
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatus(): ?array
    {
        if (!$this->handle) {
            return null;
        }

        return (array)proc_get_status($this->handle);
    }

    public function close(): void
    {
        if ($this->handle) {
            proc_close($this->handle);
        }

        foreach ($this->streams as $i => $stream) {
            unset($this->streams[$i]);
            $stream->close();
        }

        $this->handle = null;
    }

    protected function onClose(): void
    {
    }
}
