<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Exceptional;

class Signal
{
    protected static $signalMap = [
        'SIGHUP' => null,
        'SIGINT' => null,
        'SIGQUIT' => null,
        'SIGKILL' => null,
        'SIGILL' => null,
        'SIGTRAP' => null,
        'SIGABRT' => null,
        'SIGIOT' => null,
        'SIGBUS' => null,
        'SIGFPE' => null,
        'SIGUSR1' => null,
        'SIGSEGV' => null,
        'SIGUSR2' => null,
        'SIGALRM' => null,
        'SIGTERM' => null,
        'SIGSTKFLT' => null,
        'SIGCLD' => null,
        'SIGCHLD' => null,
        'SIGCONT' => null,
        'SIGTSTP' => null,
        'SIGTTIN' => null,
        'SIGTTOU' => null,
        'SIGURG' => null,
        'SIGXCPU' => null,
        'SIGXFSZ' => null,
        'SIGVTALRM' => null,
        'SIGPROF' => null,
        'SIGWINCH' => null,
        'SIGPOLL' => null,
        'SIGIO' => null,
        'SIGPWR' => null,
        'SIGSYS' => null,
        'SIGBABY' => null
    ];

    protected static $init = false;

    protected $name;
    protected $number;

    /**
     * Normalize or create a new signal instance
     */
    public static function create($signal)
    {
        if ($signal instanceof Signal) {
            return $signal;
        }

        $signal = self::normalizeSignalName((string)$signal);

        if (!$signal) {
            throw Exceptional::InvalidArgument(
                'Signal is not defined'
            );
        }

        return new self($signal);
    }

    /**
     * Normalize signal name
     */
    public static function normalizeSignalName(string $signal)
    {
        if (!self::$init) {
            self::$init = true;

            if (extension_loaded('pcntl')) {
                foreach (self::$signalMap as $signalName => $number) {
                    if (defined($signalName)) {
                        self::$signalMap[$signalName] = constant($signalName);
                    }
                }
            } else {
                $list = explode(' ', trim((string)shell_exec("kill -l")));

                foreach ($list as $i => $name) {
                    $name = 'SIG'.$name;

                    if (array_key_exists($name, self::$signalMap)) {
                        self::$signalMap[$name] = $i + 1;
                    }
                }
            }
        }

        if (is_numeric($signal)) {
            $signal = (int)$signal;

            if (false !== ($t = array_search($signal, self::$signalMap))) {
                $signal = $t;
            } else {
                throw Exceptional::InvalidArgument(
                    $signal.' is not a valid signal identifier'
                );
            }
        } else {
            $signal = strtoupper($signal);

            if (!array_key_exists($signal, self::$signalMap)) {
                throw Exceptional::InvalidArgument(
                    $signal.' is not a valid signal identifier'
                );
            }
        }

        return $signal;
    }

    /**
     * Init with signal name
     */
    protected function __construct(string $name)
    {
        $this->name = $name;
        $this->number = self::$signalMap[$name];
    }

    /**
     * Get signal name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get signal number
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}
