<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Atlas;
use DecodeLabs\Atlas\Broker;
use DecodeLabs\Atlas\Channel\Stream;

use df\core\io\IMultiplexer;
use df\core\io\IMultiplexReaderChannel;

abstract class Base implements Launcher
{
    protected $processName;
    protected $args = [];
    protected $path;
    protected $user;
    protected $title;
    protected $priority;
    protected $workingDirectory;
    protected $broker;

    /**
     * Create process launcher for specific OS
     */
    public static function create(string $processName, array $args=[], string $path=null, ?Broker $broker=null, string $user=null): Launcher
    {
        $class = '\\DecodeLabs\\Systemic\\Process\\Launcher\\'.Systemic::$os->getName();

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\Process\\Launcher\\'.Systemic::$os->getPlatformType();

            if (!class_exists($class)) {
                throw Glitch::EComponentUnavailable(
                    'Sorry, I don\'t know how to launch processes on this platform!'
                );
            }
        }

        return new $class($processName, $args, $path, $broker, $user);
    }


    /**
     * Init with main params
     */
    protected function __construct(string $processName, array $args=[], string $path=null, ?Broker $broker=null, string $user=null)
    {
        $this->setProcessName($processName);
        $this->setArgs($args);
        $this->setPath($path);
        $this->setTitle($this->processName);
        $this->setIoBroker($broker);
        $this->setUser($user);
    }


    /**
     * Set process name
     */
    public function setProcessName(string $name): Launcher
    {
        $this->processName = $name;
        return $this;
    }

    /**
     * Get process name
     */
    public function getProcessName(): string
    {
        return $this->processName;
    }

    /**
     * Set process args
     */
    public function setArgs(array $args): Launcher
    {
        $this->args = $args;
        return $this;
    }

    /**
     * Get process args
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Get process exec path
     */
    public function setPath(?string $path): Launcher
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get process exec path
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set process owner
     */
    public function setUser(?string $user): Launcher
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get process owner
     */
    public function getUser(): ?string
    {
        return $this->user;
    }


    /**
     * Set process title
     */
    public function setTitle(?string $title): Launcher
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get process title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set process priority
     */
    public function setPriority(?int $priority): Launcher
    {
        $this->priority = (int)$priority;
        return $this;
    }

    /**
     * Get process priority
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set working directory
     */
    public function setWorkingDirectory(?string $path): Launcher
    {
        $this->workingDirectory = $path;
        return $this;
    }

    /**
     * Get working directory
     */
    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }



    /**
     * Set callback to read user input
     */
    public function setIoBroker(?Broker $broker): Launcher
    {
        $this->broker = $broker;
        return $this;
    }

    /**
     * Get input reader
     */
    public function getIoBroker(): ?Broker
    {
        return $this->broker;
    }


    /**
     * TEMP: Wrap r7 multiplexer
     */
    public function setR7Multiplexer(IMultiplexer $multiplexer): Launcher
    {
        if (!class_exists('DecodeLabs\\Atlas')) {
            throw Glitch::EComponentUnavailable('Atlas is not available');
        }

        $broker = Atlas::newBroker();

        foreach ($multiplexer->getChannels() as $channel) {
            if ($channel instanceof IMultiplexReaderChannel) {
                $broker
                    ->addInputChannel(Atlas::openCliInputStream())
                    ->addOutputChannel(Atlas::openCliOutputStream())
                    ->addErrorChannel(Atlas::openCliErrorStream());
            } else {
                $stream = new Stream($channel->getStreamDescriptor());
                $broker->addOutputChannel($stream);
                $broker->addErrorChannel($stream);
            }
        }

        return $this->setIoBroker($broker);
    }
}
