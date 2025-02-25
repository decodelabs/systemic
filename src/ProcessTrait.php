<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Exceptional;
use Throwable;

/**
 * @phpstan-require-implements Process
 */
trait ProcessTrait
{
    protected int $processId;
    protected ?int $parentProcessId = null;
    protected ?string $pidFile = null;

    /**
     * Init with PID and title
     */
    public function __construct(
        int $processId
    ) {
        $this->processId = $processId;
    }

    /**
     * Get current PID
     */
    public function getProcessId(): int
    {
        return $this->processId;
    }



    /**
     * Has a PID file been defined for this process?
     */
    public function hasPidFile(): bool
    {
        return $this->pidFile !== null;
    }

    /**
     * Define a PID file for this process
     */
    public function setPidFilePath(
        ?string $path
    ): static {
        if ($path === null) {
            $this->pidFile = null;
            return $this;
        }

        $dirname = dirname($path);

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        $write = true;
        $pid = $this->getProcessId();

        if (is_file($path)) {
            $oldPid = (int)file_get_contents($path);

            if ($oldPid == $pid) {
                $write = false;
            } elseif (self::isProcessIdLive($oldPid)) {
                throw Exceptional::Runtime(
                    message: 'PID file ' . basename($path) . ' already exists and is live with pid of ' . $oldPid
                );
            }
        }


        if ($write) {
            try {
                file_put_contents($path, $pid);
            } catch (Throwable $e) {
                throw Exceptional::Runtime(
                    message: 'Unable to write PID file',
                    previous: $e
                );
            }
        }

        $this->pidFile = $path;
        return $this;
    }

    /**
     * Get PID file path
     */
    public function getPidFilePath(): ?string
    {
        return $this->pidFile;
    }


    /**
     * Set process priority
     */
    public function setPriority(
        int $priority
    ): static {
        if (extension_loaded('pcntl')) {
            pcntl_setpriority($priority, $this->processId);
        }

        return $this;
    }

    /**
     * Get process priority
     */
    public function getPriority(): int
    {
        if (extension_loaded('pcntl')) {
            return (int)pcntl_getpriority($this->processId);
        }

        return 0;
    }
}
