<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Deliverance\Broker;
use DecodeLabs\Fluidity\Then;
use DecodeLabs\Systemic\Process;

interface Launcher extends Then
{
    /**
     * @return $this
     */
    public function setProcessName(string $name): Launcher;
    public function getProcessName(): ?string;

    /**
     * @param array<string> $args
     * @return $this
     */
    public function setArgs(array $args): Launcher;

    /**
     * @return array<string>
     */
    public function getArgs(): array;

    /**
     * @return $this
     */
    public function setPath(?string $path): Launcher;

    public function getPath(): ?string;

    /**
     * @return $this
     */
    public function setUser(?string $user): Launcher;

    public function getUser(): ?string;

    /**
     * @return $this
     */
    public function setTitle(?string $title): Launcher;

    public function getTitle(): ?string;

    /**
     * @return $this
     */
    public function setPriority(?int $priority): Launcher;

    public function getPriority(): ?int;

    /**
     * @return $this
     */
    public function setWorkingDirectory(?string $path): Launcher;

    public function getWorkingDirectory(): ?string;

    /**
     * @return $this
     */
    public function setBroker(?Broker $broker): Launcher;

    public function getBroker(): ?Broker;

    /**
     * @return $this
     */
    public function setInputGenerator(?callable $generator): Launcher;

    public function getInputGenerator(): ?callable;

    /**
     * @return $this
     */
    public function setDecoratable(bool $flag): Launcher;

    public function isDecoratable(): bool;

    public function launch(): Result;
    public function launchBackground(): Process;
}
