<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic;

use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Glitch;

trait ProcessTrait
{
    protected $processId;
    protected $title;

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
