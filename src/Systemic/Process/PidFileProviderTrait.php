<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;

trait PidFileProviderTrait
{
    protected $pidFile;

    public function hasPidFile(): bool
    {
        return $this->pidFile !== null;
    }

    public function setPidFilePath(?string $path): Managed
    {
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
                throw Glitch::ERuntime(
                    'PID file '.basename($path).' already exists and is live with pid of '.$oldPid
                );
            }
        }


        if ($write) {
            try {
                file_put_contents($path, $pid);
            } catch (\Throwable $e) {
                throw Glitch::ERuntime(
                    'Unable to write PID file', 0, $e
                );
            }
        }

        $this->pidFile = $path;
        return $this;
    }

    public function getPidFilePath(): ?string
    {
        return $this->pidFile;
    }
}
