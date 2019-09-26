<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process;

use DecodeLabs\Systemic\Process;

interface Launcher
{
    public function setProcessName(string $name): Launcher;
    public function getProcessName(): ?string;
    public function setArgs(array $args): Launcher;
    public function getArgs(): array;
    public function setPath(?string $path): Launcher;
    public function getPath(): ?string;
    public function setUser(?string $user): Launcher;
    public function getUser(): ?string;
    public function setTitle(?string $title);
    public function getTitle(): ?string;
    public function setPriority(?int $priority);
    public function getPriority(): ?int;
    public function setWorkingDirectory(?string $path);
    public function getWorkingDirectory(): ?string;

    public function setOutputWriter(?callable $writer): Launcher;
    public function getOutputWriter(): ?callable;
    public function setErrorWriter(?callable $writer): Launcher;
    public function getErrorWriter(): ?callable;
    public function setInputReader(?callable $reader): Launcher;
    public function getInputReader(): ?callable;

    public function launch(): Result;
    public function launchBackground(): Process;
}
