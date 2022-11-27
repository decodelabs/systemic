<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);
declare(ticks=1);

namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Launcher;
use DecodeLabs\Systemic\Process\LauncherTrait;
use DecodeLabs\Systemic\Process\Result;
use DecodeLabs\Systemic\Process\Unix as UnixProcess;

use Throwable;

class Unix implements Launcher
{
    use LauncherTrait;

    /**
     * @var int<0, max>
     */
    protected int $readChunkSize = 2048;

    protected ?int $signal = null;

    /**
     * Launch process
     */
    public function launch(): Result
    {
        $command = $this->prepareCommand();
        //$type = $this->decoratable ? 'pty' : 'pipe';
        $type = 'pipe';

        $descriptors = [
            0 => [$type, 'r'],
            1 => [$type, 'w'],
            2 => [$type, 'w']
        ];

        $workingDirectory = $this->workingDirectory !== null ?
            realpath($this->workingDirectory) : null;

        if ($workingDirectory === false) {
            $workingDirectory = $this->workingDirectory;
        }


        // Stty
        $snapshot = null;

        if ($this->session) {
            $snapshot = $this->session->snapshotStty();
            $this->session->toggleInputEcho(false);
            $this->session->toggleInputBuffer(false);
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
        $generatorCalled = $brokerBlocking = false;

        stream_set_blocking($outputPipe, false);
        stream_set_blocking($errorPipe, false);

        if ($this->broker) {
            $brokerBlocking = $this->broker->isReadBlocking();
            $this->broker->setReadBlocking(false);
        }

        if (
            !empty($this->signals) &&
            extension_loaded('pcntl')
        ) {
            foreach ($this->signals as $signal) {
                pcntl_signal(
                    $signal->getNumber(),
                    function (int $number) {
                        $this->signal = $number;
                    }
                );
            }
        }

        $exit = null;
        $this->signal = null;

        while (true) {
            $status = (array)proc_get_status($processHandle);

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

            // Interrupt
            /** @phpstan-ignore-next-line */
            if ($this->signal !== null) {
                if (extension_loaded('posix')) {
                    posix_kill($status['pid'], $this->signal);
                } else {
                    $exit = -1;
                    break;
                }
            }

            if (
                /* @phpstan-ignore-next-line */
                !($status['running'] ?? false) &&
                $outputBuffer === null &&
                $errorBuffer === null &&
                $input === null
            ) {
                $exit = $status['exitcode'];
                break;
            }

            usleep($this->cycleSleep);
        }

        foreach ($pipes as $pipe) {
            if ($pipe) {
                fclose($pipe);
            }
        }

        proc_close($processHandle);
        $result->registerCompletion((int)$exit);

        if ($this->broker) {
            $this->broker->setReadBlocking($brokerBlocking);
        }

        if ($this->session) {
            $this->session->restoreStty($snapshot);
        }

        return $result;
    }

    /**
     * Read a chunk from buffer
     *
     * @param resource $pipe
     * @param int<0, max> $length
     */
    protected function readChunk(
        $pipe,
        int $length
    ): ?string {
        try {
            $output = fread($pipe, $length);
        } catch (Throwable $e) {
            return null;
        }

        if ($output === '' || $output === false) {
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
        $activeCommand = $command . ' > /dev/null 2>&1 & echo $!';
        $cwd = null;

        if ($this->workingDirectory !== null) {
            $cwd = getcwd();

            if (false !== ($dir = realpath($this->workingDirectory))) {
                chdir($dir);
            }
        }

        exec($activeCommand, $pidArr);
        $pid = $pidArr[0];

        if ($this->workingDirectory !== null) {
            chdir((string)$cwd);
        }

        return new UnixProcess((int)$pid, $command);
    }

    /**
     * Prepare the command string for execution
     */
    protected function prepareCommand(): string
    {
        $command = $this->path;

        if (!empty($this->args)) {
            $temp = [];

            foreach ($this->args as $arg) {
                $arg = (string)$arg;

                if (!strlen($arg)) {
                    continue;
                }

                if ($arg[0] != '-') {
                    $arg = escapeshellarg($arg);
                }

                $temp[] = $arg;
            }

            $command .= ' ' . implode(' ', $temp);
        }


        if ($this->decoratable) {
            if (Systemic::$os->isMac()) {
                $command = 'script -q /dev/null ' . $command;
            } else {
                $command = 'script -e -q -c "' . $command . '" /dev/null';
            }
        }

        if ($this->user) {
            if (false !== strpos($this->user, ':')) {
                $parts = explode(':', $this->user, 2);
                $user = (string)array_shift($parts);
                $pass = array_shift($parts);
            } else {
                $user = $this->user;
                $pass = null;
            }

            if ($pass !== null) {
                $command = 'echo ' . $pass . ' | sudo -k -u ' . $user . ' -p "" -S ' . $command;
            } else {
                $command = 'sudo -k -u ' . $user . ' ' . $command;
            }
        }

        return $command;
    }

    /**
     * Prepare env for proc_open
     *
     * @return array<string, mixed>|null
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
