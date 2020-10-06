<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

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
