<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Unix as UnixProcess;
use DecodeLabs\Systemic\Process\Result;
use DecodeLabs\Systemic\Process\Launcher;

class Unix extends Base
{
    protected $readChunkSize = 2048;

    /**
     * Launch process
     */
    public function launch(): Result
    {
        $command = $this->prepareCommand();

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];

        $workingDirectory = $this->workingDirectory !== null ?
            realpath($this->workingDirectory) : null;

        $result = new Result();
        $processHandle = proc_open($command, $descriptors, $pipes, $workingDirectory);

        if (!is_resource($processHandle)) {
            return $result->registerFailure();
        }

        $outputBuffer = $errorBuffer = $input = false;
        $outputPipe = $pipes[1];
        $errorPipe = $pipes[2];

        stream_set_blocking($outputPipe, false);
        stream_set_blocking($errorPipe, false);

        if ($this->multiplexer) {
            $this->multiplexer->setReadBlocking(false);
        }

        while (true) {
            $status = proc_get_status($processHandle);

            $outputBuffer = $this->readChunk($outputPipe, $this->readChunkSize);
            $errorBuffer = $this->readChunk($errorPipe, $this->readChunkSize);

            if ($this->multiplexer) {
                $input = $this->multiplexer->readChunk($this->readChunkSize);
            } elseif ($this->inputReader) {
                $input = ($this->inputReader)($this->readChunkSize);
            }

            if ($outputBuffer !== false) {
                $result->appendOutput($outputBuffer);

                if ($this->multiplexer) {
                    $this->multiplexer->write($outputBuffer);
                } elseif ($this->outputWriter) {
                    ($this->outputWriter)($outputBuffer);
                }
            }

            if ($errorBuffer !== false) {
                $result->appendError($errorBuffer);

                if ($this->multiplexer) {
                    $this->multiplexer->writeError($errorBuffer);
                } elseif ($this->errorWriter) {
                    ($this->errorWriter)($errorBuffer);
                }
            }

            if ($input !== false) {
                fwrite($pipes[0], $input);
                $input = false;
            }

            if (!$status['running'] && $outputBuffer === false && $errorBuffer === false && $input === false) {
                break;
            }

            usleep(500);
        }

        foreach ($pipes as $pipe) {
            if ($pipe) {
                fclose($pipe);
            }
        }

        proc_close($processHandle);
        $result->registerCompletion();

        if ($this->multiplexer) {
            $this->multiplexer->setReadBlocking(true);
        }

        return $result;
    }

    /**
     * Read a chunk from buffer
     */
    protected function readChunk($pipe, int $length)
    {
        try {
            $output = fread($pipe, $length);
        } catch (\Throwable $e) {
            return false;
        }

        if ($output === ''
        || $output === null
        || $output === false) {
            return false;
        }

        return $output;
    }


    /**
     * Launch a task in the background and return immediately
     */
    public function launchBackground(): Process
    {
        $command = $this->prepareCommand();
        $activeCommand = $command.' > /dev/null 2>&1 & echo $!';

        if ($this->workingDirectory !== null) {
            $cwd = getcwd();
            chdir(realpath($this->workingDirectory));
        }

        exec($activeCommand, $pidArr);
        $pid = $pidArr[0];

        if ($this->workingDirectory) {
            chdir($cwd);
        }

        return new UnixProcess($pid, $command);
    }

    /**
     * Prepare the command string for execution
     */
    protected function prepareCommand(): string
    {
        $command = '';

        if ($this->path) {
            $command .= rtrim($this->path, '/\\').DIRECTORY_SEPARATOR;
        }

        $command .= $this->processName;

        if (!empty($this->args)) {
            $temp = [];

            foreach ($this->args as $arg) {
                $arg = (string)$arg;

                if (!strlen($arg)) {
                    continue;
                }

                if ($arg{0} != '-') {
                    $arg = escapeshellarg($arg);
                }

                $temp[] = $arg;
            }

            $command .= ' '.implode(' ', $temp);
        }

        if ($this->user) {
            $command = 'sudo -u '.$this->user.' '.$command;
        }

        return $command;
    }
}
