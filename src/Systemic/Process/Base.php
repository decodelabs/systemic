<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;

abstract class Base implements Process
{
    protected $processId;
    protected $title;

    /**
     * Wrap live process under PID
     */
    public static function fromPid(int $pid): Managed
    {
        $class = self::getSystemClass();
        return new $class($pid, 'PID: '.$pid);
    }

    /**
     * Get class for current system's managed process
     */
    public static function getSystemClass(): string
    {
        $class = '\\DecodeLabs\\Systemic\\Process\\'.Systemic::$os->getName().'Managed';

        if (!class_exists($class)) {
            $class = '\\DecodeLabs\\Systemic\\Process\\'.Systemic::$os->getPlatformType().'Managed';

            if (!class_exists($class)) {
                throw Glitch::EComponentUnavailable(
                    'Managed processes aren\'t currently supported on this platform!'
                );
            }
        }

        return $class;
    }


    /**
     * Init with PID and title
     */
    public function __construct(int $processId, ?string $title)
    {
        $this->processId = $processId;
        $this->title = $title;
    }

    /**
     * Get current PID
     */
    public function getProcessId(): int
    {
        return $this->processId;
    }

    /**
     * Get process title if available
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
