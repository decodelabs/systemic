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
use DecodeLabs\Terminus\Session;

interface Launcher extends Then
{
    /**
     * @return $this
     */
    public function setProcessName(string $name): static;
    public function getProcessName(): ?string;

    /**
     * @param array<string> $args
     * @return $this
     */
    public function setArgs(array $args): static;

    /**
     * @return array<string>
     */
    public function getArgs(): array;

    /**
     * @return $this
     */
    public function setPath(?string $path): static;

    public function getPath(): ?string;

    /**
     * @return $this
     */
    public function setUser(?string $user): static;

    public function getUser(): ?string;

    /**
     * @return $this
     */
    public function setTitle(?string $title): static;

    public function getTitle(): ?string;

    /**
     * @return $this
     */
    public function setPriority(?int $priority): static;

    public function getPriority(): ?int;

    /**
     * @return $this
     */
    public function setWorkingDirectory(?string $path): static;

    public function getWorkingDirectory(): ?string;

    /**
     * @return $this
     */
    public function setBroker(?Broker $broker): static;

    public function getBroker(): ?Broker;

    /**
     * @return $this
     */
    public function setSession(?Session $session): static;

    public function getSession(): ?Session;

    /**
     * @return $this
     */
    public function setInputGenerator(?callable $generator): static;

    public function getInputGenerator(): ?callable;

    /**
     * @return $this
     */
    public function setDecoratable(bool $flag): static;

    public function isDecoratable(): bool;

    public function launch(): Result;
    public function launchBackground(): Process;
}
