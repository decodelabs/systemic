<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins;

use DecodeLabs\Systemic\Context;
use DecodeLabs\Veneer\FacadePlugin;

use DecodeLabs\Systemic\Process as ProcessInterface;
use DecodeLabs\Systemic\Process\Result;
use DecodeLabs\Systemic\Process\Signal;
use DecodeLabs\Systemic\Process\Launcher;
use DecodeLabs\Systemic\Process\Launcher\Base as LauncherBase;
use DecodeLabs\Systemic\Process\Base as BaseProcess;

use DecodeLabs\Atlas\Broker;

class Process implements FacadePlugin
{
    protected $context;
    protected $current;

    /**
     * Init with parent factory
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Get current PHP process
     */
    public function getCurrent(): ProcessInterface
    {
        if (!$this->current) {
            $class = BaseProcess::getSystemClass();
            $pid = $class::getCurrentProcessId();
            $this->current = new $class($pid, 'Current process');
        }

        return $this->current;
    }

    /**
     * Get current process owner
     */
    public function getCurrentOwner(): string
    {
        return $this->getCurrent()->getOwnerName();
    }

    /**
     * Get current process group
     */
    public function getCurrentGroup(): string
    {
        return $this->getCurrent()->getGroupName();
    }


    /**
     * Wrap process from PID
     */
    public function fromPid(int $pid): ProcessInterface
    {
        return BaseProcess::fromPid($pid);
    }


    /**
     * New signal object
     */
    public function newSignal($signal): Signal
    {
        return Signal::create($signal);
    }


    /**
     * Launch standard process
     */
    public function launch(string $process, $args=null, string $path=null, ?Broker $ioBroker=null, string $user=null): Result
    {
        return $this->newLauncher($process, $args, $path, $ioBroker, $user)->launch();
    }

    /**
     * Launch PHP script
     */
    public function launchScript(string $path, $args=null, ?Broker $ioBroker=null, string $user=null): Result
    {
        return $this->newScriptLauncher($path, $args, $ioBroker, $user)->launch();
    }

    /**
     * Launch background process
     */
    public function launchBackground(string $process, $args=null, string $path=null, ?Broker $ioBroker=null, string $user=null): Process
    {
        return $this->newLauncher($process, $args, $path, $ioBroker, $user)->launchBackground();
    }

    /**
     * Launch background PHP script
     */
    public function launchBackgroundScript(string $path, $args=null, ?Broker $ioBroker=null, string $user=null): Process
    {
        return $this->newScriptLauncher($path, $args, $ioBroker, $user)->launchBackground();
    }


    /**
     * Create a new launcher object
     */
    public function newLauncher(string $process, $args=[], string $path=null, ?Broker $ioBroker=null, string $user=null): Launcher
    {
        if ($args === null) {
            $args = [];
        } elseif (!is_array($args)) {
            $args = (array)$args;
        }

        return LauncherBase::create($process, $args, $path, $ioBroker, $user);
    }

    /**
     * Create a new launcher for php scripts
     */
    public function newScriptLauncher(string $path, $args=[], ?Broker $ioBroker=null, string $user=null): Launcher
    {
        if ($args === null) {
            $args = [];
        } elseif (!is_array($args)) {
            $args = (array)$args;
        }

        $binaryPath = Systemic::$os->which('php');

        if (!file_exists($binaryPath)) {
            $binaryPath = 'php';
        }

        $phpName = basename($binaryPath);
        $phpPath = null;

        if ($phpName != $binaryPath) {
            $phpPath = dirname($binaryPath);
        }

        array_unshift($args, trim($path));
        return LauncherBase::create($phpName, $args, $phpPath, $ioBroker, $user);
    }
}
