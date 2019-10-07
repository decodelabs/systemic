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

        if ($workingDirectory === false) {
            $workingDirectory = $this->workingDirectory;
        }

        $result = new Result();

        $env = $this->prepareEnv();
        $processHandle = proc_open($command, $descriptors, $pipes, $workingDirectory, $env);

        if (!is_resource($processHandle)) {
            return $result->registerFailure();
        }

        $outputBuffer = $errorBuffer = $input = null;
        $outputPipe = $pipes[1];
        $errorPipe = $pipes[2];
        $generatorCalled = false;

        stream_set_blocking($outputPipe, false);
        stream_set_blocking($errorPipe, false);

        if ($this->broker) {
            $brokerBlocking = $this->broker->isBlocking();
            $this->broker->setBlocking(false);
        }

        while (true) {
            $status = proc_get_status($processHandle);

            // Get output & error
            $outputBuffer = $this->readChunk($outputPipe, $this->readChunkSize);
            $errorBuffer = $this->readChunk($errorPipe, $this->readChunkSize);

            // Get input
            if ($this->inputGenerator) {
                if (!$generatorCalled) {
                    $input = ($this->inputGenerator)();
                    $generatorCalled = true;
                }
            } elseif ($this->broker) {
                $input = $this->broker->read($this->readChunkSize);
            }


            // Write output
            if ($outputBuffer !== null) {
                $result->appendOutput($outputBuffer);

                if ($this->broker) {
                    $this->broker->write($outputBuffer);
                }
            }


            // Write error
            if ($errorBuffer !== null) {
                $result->appendError($errorBuffer);

                if ($this->broker) {
                    $this->broker->writeError($errorBuffer);
                }
            }


            // Write input
            if ($input !== null) {
                fwrite($pipes[0], $input);
                $input = null;

                if ($generatorCalled) {
                    fclose($pipes[0]);
                    $pipes[0] = null;
                }
            }

            if (!$status['running'] && $outputBuffer === null && $errorBuffer === null && $input === null) {
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

        if ($this->broker) {
            $this->broker->setBlocking($brokerBlocking);
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
            return null;
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

        return new UnixProcess((int)$pid, $command);
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

        if ($this->decoratable && !$this->user && Systemic::$os->which('script')) {
            if (Systemic::$os->isMac()) {
                $command = 'script -q /dev/null '.$command;
            } else {
                $command = 'script -e -q -c "'.$command.'" /dev/null';
            }
        }

        return $command;
    }

    /**
     * Prepare env for proc_open
     */
    protected function prepareEnv(): ?array
    {
        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'])) {
            $output = $_ENV;

            if (!isset($output['PATH'])) {
                $output['PATH'] = '/usr/local/sbin:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin';
            }

            return $output;
        }

        $env = $_SERVER;
        unset($env['argv']);
        unset($env['argc']);

        if (!isset($env['COLUMNS'])) {
            $env['COLUMNS'] = Systemic::$os->getShellWidth();
        }

        if (!isset($env['ROWS'])) {
            $env['ROWS'] = Systemic::$os->getShellHeight();
        }

        return $env;
    }
}
