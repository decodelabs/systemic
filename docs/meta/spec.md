# Systemic — Package Specification

> **Cluster:** `io`
> **Language:** `php`
> **Milestone:** `m2`
> **Repo:** `https://github.com/decodelabs/systemic`
> **Role:** Process management

## Overview

### Purpose

Systemic offers an easy to use frontend to launching and controlling processes and accessing system information. It provides a simple but powerful system for executing system commands, managing processes, and accessing OS information.

Key features:
- **Process execution**: Launch, capture, and run system processes
- **Process management**: Monitor and control running processes (signals, killing, priority)
- **OS information**: Access OS name, distribution, version, hostname, and binary locations
- **Multiple execution modes**: Capture output, live capture, background launch, interactive TTY
- **Command building**: Flexible command construction with environment variables and signal handling
- **Cross-platform**: Support for Unix/Linux, macOS (Darwin), and Windows (limited)
- **Custom controllers**: Extensible controller system for custom process handling

### Non-Goals

- Systemic does not provide process pools or worker management.
- It does not handle process scheduling or job queues.
- It does not provide remote process execution (SSH, etc.).
- It does not handle process monitoring or health checks beyond basic status.
- It does not provide process isolation or sandboxing.

## Role in the Ecosystem

### Cluster & Positioning

Systemic belongs to the **io** cluster, focusing on input/output operations. It complements other IO packages by providing system process execution and OS information access.

### Usage Contexts

- **System command execution**: Running system commands and capturing output
- **Script execution**: Executing PHP scripts and other scripts
- **Process management**: Managing long-running processes and background tasks
- **OS detection**: Detecting OS type, distribution, and version
- **Binary location**: Finding system binaries via PATH
- **Interactive processes**: Running interactive processes with TTY support

## Public Surface

### Key Types

- **`Systemic`** (class): Main service class providing process execution and OS information access. Implements `PureService` for Kingdom integration.

- **`Command`** (interface): Command interface for building and executing commands. Extends `BrokerConnector` and `Then` interfaces. Provides methods for command construction, execution, and configuration.

- **`Command\Unix`** (class): Unix command implementation providing argument escaping and command building.

- **`Process`** (interface): Process interface for managing running processes. Provides methods for process status, signals, priority, and ownership.

- **`Process\Unix`** (class): Unix process implementation.

- **`Process\Windows`** (class): Windows process implementation.

- **`ActiveProcess`** (interface): Active process interface extending `Process`. Provides methods for process identity and forking.

- **`ActiveProcess\Unix`** (class): Unix active process implementation.

- **`Result`** (class): Result class representing process execution results. Contains exit code, output, error, and timing information.

- **`Controller`** (interface): Controller interface for custom process handling. Defines methods for input/output handling and completion registration.

- **`Controller\BlindCapture`** (class): Controller that captures output silently.

- **`Controller\LiveCapture`** (class): Controller that captures output while displaying it live.

- **`Controller\Terminal`** (class): Controller for terminal/interactive processes.

- **`Controller\Http`** (class): Controller for HTTP output.

- **`Controller\Severed`** (class): Controller for severed processes.

- **`Controller\Custom`** (class): Custom controller implementation.

- **`Controller\ResultProvider`** (interface): Interface for controllers that provide results.

- **`Manifold`** (interface): Manifold interface for process I/O handling. Defines methods for descriptor management and stream access.

- **`Manifold\Pipe`** (class): Pipe manifold for standard pipe-based I/O.

- **`Manifold\Tty`** (class): TTY manifold for terminal I/O.

- **`Manifold\Pty`** (class): PTY manifold for pseudo-terminal I/O.

- **`Manifold\DevNull`** (class): DevNull manifold for null I/O.

- **`ManifoldAbstract`** (class): Abstract base class for manifold implementations.

- **`Os`** (interface): OS interface for system information access. Provides properties and methods for OS detection and binary location.

- **`Os\Linux`** (class): Linux OS implementation.

- **`Os\Darwin`** (class): macOS (Darwin) OS implementation.

- **`Os\Unix`** (class): Generic Unix OS implementation.

- **`Os\Windows`** (class): Windows OS implementation.

### Main Entry Points

**Systemic Service:**
- `new Systemic()` — Constructor
- `Systemic::getOs(): Os` — Get OS instance (static)
- `$systemic->os` — OS instance (readonly property)
- `Systemic::getProcess(int $pid): Process` — Get process by PID (static)
- `Systemic::getActiveProcess(): ActiveProcess` — Get current process (static)
- `$systemic->newSignal(Signal|string|int $signal): Signal` — Create signal
- `$systemic->normalizeSignal(Signal|string|int $signal): int` — Normalize signal to number

**Command Execution:**
- `$systemic->command(string|Stringable|array|Command $command, array $variables = []): Command` — Create command
- `$systemic->scriptCommand(string|Stringable|array|Command $command, array $variables = []): Command` — Create PHP script command
- `$systemic->capture(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Result` — Capture output
- `$systemic->captureScript(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Result` — Capture script output
- `$systemic->liveCapture(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Result` — Live capture output
- `$systemic->liveCaptureScript(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Result` — Live capture script output
- `$systemic->launch(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Process` — Launch background process
- `$systemic->launchScript(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): Process` — Launch background script
- `$systemic->run(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): bool` — Run interactive process
- `$systemic->runScript(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory = null): bool` — Run interactive script
- `$systemic->start(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory, callable|Controller $controller): Result` — Start with custom controller
- `$systemic->startScript(string|Stringable|array|Command $command, string|Stringable|null $workingDirectory, callable|Controller $controller): Result` — Start script with custom controller

**Command Interface:**
- `new Command(string|Stringable|array $command, array $variables = [])` — Constructor
- `$command->getRaw(): string|array` — Get raw command
- `$command->getRawString(): string` — Get raw command as string
- `$command->prepend(string|Stringable|array $prefix): static` — Prepend to command
- `$command->append(string|Stringable|array $suffix): static` — Append to command
- `$command->setVariables(array $variables): static` — Set environment variables
- `$command->getVariables(): array` — Get environment variables
- `$command->setVariable(string $name, string|Stringable|int|float $value): static` — Set single variable
- `$command->getVariable(string $name): string|Stringable|int|float|null` — Get variable
- `$command->hasVariable(string $name): bool` — Check if variable exists
- `$command->setWorkingDirectory(string|Stringable|null $path): static` — Set working directory
- `$command->getWorkingDirectory(): ?string` — Get working directory
- `$command->addSignal(Signal|string|int ...$signals): static` — Add signal handlers
- `$command->getSignals(): array` — Get signals
- `$command->hasSignals(): bool` — Check if has signals
- `$command->hasSignal(Signal|string|int $signal): bool` — Check if has signal
- `$command->removeSignal(Signal|string|int $signal): static` — Remove signal
- `$command->clearSignals(): static` — Clear all signals
- `$command->setUser(?string $user): static` — Set user (sudo)
- `$command->getUser(): ?string` — Get user
- `$command->capture(): Result` — Capture output
- `$command->liveCapture(): Result` — Live capture output
- `$command->launch(): Process` — Launch background process
- `$command->run(): bool` — Run interactive process
- `$command->start(callable|Controller $controller): Result` — Start with controller

**Process Interface:**
- `Process::isProcessIdLive(int $pid): bool` — Check if process is alive (static)
- `Process::getCurrentProcessId(): int` — Get current process ID (static)
- `$process->getProcessId(): int` — Get process ID
- `$process->getParentProcessId(): int` — Get parent process ID
- `$process->isAlive(): bool` — Check if process is alive
- `$process->kill(): bool` — Kill process (SIGTERM)
- `$process->sendSignal(Signal|string|int $signal): bool` — Send signal
- `$process->hasPidFile(): bool` — Check if has PID file
- `$process->setPidFilePath(?string $path): static` — Set PID file path
- `$process->getPidFilePath(): ?string` — Get PID file path
- `$process->setPriority(int $priority): static` — Set process priority
- `$process->getPriority(): int` — Get process priority
- `$process->getOwnerId(): int` — Get owner user ID
- `$process->getOwnerName(): string` — Get owner user name
- `$process->getGroupId(): int` — Get group ID
- `$process->getGroupName(): string` — Get group name
- `$process->isPrivileged(): bool` — Check if privileged

**ActiveProcess Interface:**
- `$process->setIdentity(string|int $uid, string|int $gid): static` — Set process identity
- `$process->setOwnerId(int $id): static` — Set owner ID
- `$process->setOwnerName(string $name): static` — Set owner name
- `$process->setGroupId(int $id): static` — Set group ID
- `$process->setGroupName(string $name): static` — Set group name
- `$process->canFork(): bool` — Check if can fork
- `$process->fork(): ?static` — Fork process

**Result:**
- `new Result()` — Constructor
- `$result->registerFailure(): static` — Register failure
- `$result->hasLaunched(): bool` — Check if launched
- `$result->registerCompletion(int $exit = 0): static` — Register completion
- `$result->hasCompleted(): bool` — Check if completed
- `$result->getExitCode(): ?int` — Get exit code
- `$result->wasSuccessful(): bool` — Check if successful
- `$result->setOutput(?string $output): static` — Set output
- `$result->appendOutput(?string $output): static` — Append output
- `$result->hasOutput(): bool` — Check if has output
- `$result->getOutput(): ?string` — Get output
- `$result->setError(?string $error): static` — Set error
- `$result->appendError(?string $error): static` — Append error
- `$result->hasError(): bool` — Check if has error
- `$result->getError(): ?string` — Get error

**Os Interface:**
- `$os->name` — OS name (readonly property)
- `$os->platformType` — Platform type (readonly property)
- `$os->distribution` — Distribution name (readonly property)
- `$os->version` — Version (readonly property)
- `$os->release` — Release number (readonly property)
- `$os->hostName` — Hostname (readonly property)
- `$os->isWindows(): bool` — Check if Windows
- `$os->isUnix(): bool` — Check if Unix
- `$os->isLinux(): bool` — Check if Linux
- `$os->isMac(): bool` — Check if macOS
- `$os->userIdToUserName(int $id): string` — Convert user ID to name
- `$os->userNameToUserId(string $name): int` — Convert user name to ID
- `$os->groupIdToGroupName(int $id): string` — Convert group ID to name
- `$os->groupNameToGroupId(string $name): int` — Convert group name to ID
- `$os->which(string $binaryName): ?string` — Find binary in PATH

**Controller Interface:**
- `$controller->execute(Command $command): ?Process` — Execute command
- `$controller->getInputStream(): ?Stream` — Get input stream
- `$controller->provideInput(): ?string` — Provide input
- `$controller->consumeOutput(string $data): void` — Consume output
- `$controller->consumeError(string $data): void` — Consume error
- `$controller->registerFailure(): void` — Register failure
- `$controller->registerCompletion(int $exit): void` — Register completion
- `$controller->wasSuccessful(): bool` — Check if successful

**Manifold Interface:**
- `$manifold->getDescriptors(): array` — Get file descriptors
- `$manifold->isOpen(): bool` — Check if open
- `$manifold->open(Command $command): ?Process` — Open manifold
- `$manifold->getStreams(): array` — Get streams
- `$manifold->getStatus(): ?array` — Get status
- `$manifold->close(): void` — Close manifold

## Dependencies

### Decode Labs

- **`decodelabs/coercion`**: Used for type coercion in command and process operations.
- **`decodelabs/deliverance`**: Used for stream handling and I/O operations in controllers and manifolds.
- **`decodelabs/eventful`**: Used for signal handling (`Signal` class).
- **`decodelabs/exceptional`**: Used for exception handling throughout the package.
- **`decodelabs/fluidity`**: Used for `Then` interface support in Command.
- **`decodelabs/kingdom`**: Used for service container integration (`PureService` interface).
- **`decodelabs/nuance`**: Used for debugging and inspection capabilities.

### External

- **PHP**: See `composer.json` for supported PHP versions.
- **`ext-intl`**: Required for internationalization support (used in OS detection).
- **`symfony/polyfill-mbstring`**: Used for multibyte string support.

### Optional

- **`ext-posix`**: Detected at runtime if installed, used for POSIX functions (process management, user/group lookups). Falls back to shell commands if not available.

## Behaviour & Contracts

### Invariants

- Commands are escaped by default (array format) or raw (string format).
- Process IDs are validated before operations.
- OS detection uses `php_uname()` and file system checks.
- Signal handling uses Eventful Signal class for normalization.
- Working directory must exist and be accessible.

### Input & Output Contracts

**Command Construction:**
- Array format: arguments are escaped individually.
- String format: command is used as-is (not escaped).
- Environment variables are substituted in string commands using `${:VARIABLE}` syntax.
- Script commands prepend PHP binary path automatically.

**Process Execution:**
- `capture()`: Executes command and captures all output (stdout and stderr).
- `liveCapture()`: Executes command and displays output live while capturing.
- `launch()`: Launches process in background, returns Process instance.
- `run()`: Runs interactive process over TTY, returns success boolean.
- `start()`: Starts process with custom controller, returns Result.

**Process Management:**
- Process IDs validated before operations.
- Signals normalized via Eventful Signal class.
- Process status checked via POSIX or shell commands.
- PID files managed automatically for certain signals.

**OS Detection:**
- OS name detected from `php_uname('s')`.
- Distribution detected from file system checks (Linux) or system commands.
- Version and release extracted from system information.
- Hostname retrieved from system.

**Binary Location:**
- `which()` searches PATH environment variable.
- Returns full path if found, null otherwise.

## Error Handling

- **Process not found**: `getProcess()` throws exception if process class not available for platform.
- **Command execution failure**: `capture()`, `launch()`, `run()` return failure status or throw exceptions.
- **Invalid process ID**: Process operations throw exceptions for invalid PIDs.
- **Platform not supported**: Windows commands throw `ComponentUnavailable` exception (not yet fully supported).
- **Binary not found**: `which()` returns null if binary not found in PATH.

## Configuration & Extensibility

### Custom Controllers

Implement `Controller` interface for custom process handling:

```php
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\Command;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Deliverance\Channel\Stream;

class MyController implements Controller
{
    public function execute(Command $command): ?Process
    {
        // Custom execution logic
    }

    public function getInputStream(): ?Stream
    {
        // Return input stream
    }

    // Implement other Controller methods...
}
```

### Custom Manifolds

Implement `Manifold` interface or extend `ManifoldAbstract`:

```php
use DecodeLabs\Systemic\ManifoldAbstract;

class MyManifold extends ManifoldAbstract
{
    public function getDescriptors(): array
    {
        return [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ];
    }
}
```

### Custom OS Implementations

Implement `Os` interface for custom OS support:

```php
use DecodeLabs\Systemic\Os;

class MyOs implements Os
{
    // Implement Os interface methods...
}
```

## Interactions with Other Packages

- **Deliverance**: Used for stream handling and I/O operations. Controllers use Deliverance streams for input/output.
- **Eventful**: Used for signal handling. Commands and processes use Eventful Signal class for signal normalization and handling.
- **Fluidity**: Used for `Then` interface support in Command, enabling method chaining.
- **Kingdom**: Used for service container integration. Systemic implements `PureService` interface.
- **Nuance**: Used for debugging and inspection capabilities.
- **Coercion**: Used for type coercion in command and process operations.

## Usage Examples

### Basic Command Execution

```php
use DecodeLabs\Monarch;
use DecodeLabs\Systemic;

$systemic = Monarch::getService(Systemic::class);

// Capture output
$result = $systemic->capture(['ls', '-al'], '/path/to/dir');
echo $result->getOutput();

// Launch background process
$process = $systemic->launch(['make', 'install']);

// Run interactive process
$success = $systemic->run(['interactive-app', '--arg1'], '/path/to/dir');
```

### Script Execution

```php
use DecodeLabs\Systemic;

$systemic = new Systemic();

// Capture script output
$result = $systemic->captureScript(['myScript.php', '--arg1'], '/path/to/dir');
echo $result->getOutput();

// Launch background script
$process = $systemic->launchScript(['myScript.php'], '/path/to/dir');
```

### Command Building

```php
use DecodeLabs\Systemic;

$systemic = new Systemic();

// Custom command with environment variables
$result = $systemic->command('echo ${:VARIABLE} | command', [
    'VARIABLE' => 'Hello world'
])
->setWorkingDirectory('/path/to/dir')
->capture();

// Custom command with signals and user
$success = $systemic->command(['escaped', 'arguments'])
    ->setWorkingDirectory('/path/to/dir')
    ->addSignal('SIGSTOP')
    ->setUser('someuser')
    ->run();
```

### Process Management

```php
use DecodeLabs\Systemic;

$process = Systemic::getProcess(12345);

// Check status
if ($process->isAlive()) {
    echo "Process is running\n";
}

// Send signal
$process->sendSignal('SIGTERM');

// Kill process
$process->kill();

// Get process info
echo "Owner: " . $process->getOwnerName() . "\n";
echo "Priority: " . $process->getPriority() . "\n";
```

### Active Process

```php
use DecodeLabs\Systemic;

$process = Systemic::getActiveProcess();

// Get current process ID
echo "PID: " . $process->getProcessId() . "\n";
echo "Parent PID: " . $process->getParentProcessId() . "\n";

// Set identity
$process->setIdentity(1000, 1000);

// Fork process
if ($process->canFork()) {
    $child = $process->fork();
    if ($child === null) {
        // Child process
    } else {
        // Parent process
    }
}
```

### OS Information

```php
use DecodeLabs\Systemic;

$systemic = new Systemic();

// OS info
echo $systemic->os->name; // Linux | Windows | Darwin
echo $systemic->os->platformType; // Unix | Windows
echo $systemic->os->distribution; // Ubuntu, High Sierra, etc.
echo $systemic->os->version; // System version
echo $systemic->os->release; // Version number
echo $systemic->os->hostName; // Hostname

// Find binary
$phpPath = $systemic->os->which('php'); // /usr/local/bin/php
```

### Live Capture

```php
use DecodeLabs\Systemic;

$systemic = new Systemic();

// Live capture (displays output while capturing)
$result = $systemic->liveCapture(['long-running-command']);
echo "Exit code: " . $result->getExitCode() . "\n";
```

### Custom Controller

```php
use DecodeLabs\Systemic;
use DecodeLabs\Systemic\Controller;
use DecodeLabs\Systemic\Command;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Deliverance\Channel\Stream;

class MyController implements Controller
{
    public function execute(Command $command): ?Process
    {
        // Custom execution
        return null;
    }

    public function getInputStream(): ?Stream
    {
        return null;
    }

    public function provideInput(): ?string
    {
        return null;
    }

    public function consumeOutput(string $data): void
    {
        // Custom output handling
    }

    public function consumeError(string $data): void
    {
        // Custom error handling
    }

    public function registerFailure(): void {}
    public function registerCompletion(int $exit): void {}
    public function wasSuccessful(): bool { return true; }
}

$systemic = new Systemic();
$result = $systemic->start(['command'], null, new MyController());
```

## Implementation Notes (for Contributors)

### Command Escaping

- Array format: arguments escaped individually using shell escaping.
- String format: command used as-is (not escaped, allows shell features).
- Environment variable substitution: `${:VARIABLE}` syntax in string commands.
- Unix escaping: single quotes with escaped single quotes (`'` → `'\''`).

### Process Execution

- `capture()`: Uses `BlindCapture` controller with `Pipe` manifold.
- `liveCapture()`: Uses `LiveCapture` controller with `Pipe` manifold.
- `launch()`: Uses `Severed` controller with appropriate manifold.
- `run()`: Uses `Terminal` controller with `Tty` manifold.
- `start()`: Uses provided controller with appropriate manifold.

### Process Management

- Process status checked via POSIX `posix_kill($pid, 0)` or `ps` command.
- Signals sent via POSIX `posix_kill()` or `kill` command.
- Process information retrieved via `ps` command or `/proc` filesystem (Linux).

### OS Detection

- OS name from `php_uname('s')`.
- Distribution detection:
  - Linux: checks `/etc/*-release` files and `lsb_release` command.
  - Darwin: checks system version files.
  - Windows: checks registry or system info.
- Version and release extracted from distribution-specific files or commands.

### Binary Location

- `which()` searches PATH environment variable.
- Uses `which` command if available, otherwise searches PATH manually.
- Returns full path if found, null otherwise.

### Manifold Types

- **Pipe**: Standard pipe-based I/O (`['pipe', 'r']`, `['pipe', 'w']`).
- **Tty**: Terminal I/O (`['file', '/dev/tty', 'r']`, etc.).
- **Pty**: Pseudo-terminal I/O (for interactive processes).
- **DevNull**: Null I/O (discards output).

### Signal Handling

- Signals normalized via Eventful Signal class.
- Signal names converted to numbers.
- Signal handlers registered via `addSignal()` method.
- Signals passed through to process when caught.

### Windows Support

- Windows support is currently limited (throws `ComponentUnavailable` exception).
- Windows process and command implementations exist but may be incomplete.
- Unix/Linux and macOS (Darwin) are fully supported.

## Testing & Quality

**Current Status:**
- Code quality: 4/5
- README quality: 3/5
- Documentation: 0/5 (no formal docs yet)
- Tests: 0/5 (no test suite yet)

**Testing Considerations:**
- Command execution should be tested for:
  - Array and string command formats
  - Environment variable substitution
  - Working directory handling
  - Signal handling
  - User switching (sudo)

- Process management should be tested for:
  - Process status checking
  - Signal sending
  - Process killing
  - Priority management
  - Ownership queries

- OS detection should be tested for:
  - Various Linux distributions
  - macOS versions
  - Windows versions (when supported)
  - Binary location

- Controllers should be tested for:
  - Output capture
  - Live capture
  - Terminal interaction
  - Error handling

- Edge cases should be tested for:
  - Invalid process IDs
  - Non-existent binaries
  - Permission errors
  - Signal handling edge cases
  - Concurrent process execution

## Roadmap & Future Ideas

- **Windows support**: Complete Windows process and command support
- **Process pools**: Support for process pools and worker management
- **Remote execution**: Support for remote process execution (SSH, etc.)
- **Process monitoring**: Enhanced process monitoring and health checks
- **Job queues**: Integration with job queue systems
- **Process isolation**: Support for process isolation and sandboxing
- **Performance optimization**: Connection pooling and process reuse
- **Better error messages**: More detailed error messages for execution failures

## References

- Package repository: https://github.com/decodelabs/systemic
- Composer package: https://packagist.org/packages/decodelabs/systemic
- Related packages:
  - `decodelabs/deliverance` — Stream handling
  - `decodelabs/eventful` — Signal handling
  - `decodelabs/fluidity` — Method chaining
  - `decodelabs/kingdom` — Service container
  - `decodelabs/nuance` — Debugging and inspection

