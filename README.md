# Systemic

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/systemic/integrate.yml?branch=develop)](https://github.com/decodelabs/systemic/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)

### System processes and information at your fingertips

Systemic offers an easy to use frontend to launching and controlling processes and accessing system information.

---


## Installation

Install via composer:

```bash
composer require decodelabs/systemic
```

## Usage

### Process launching

Launch new processes:

```php
use DecodeLabs\Monarch;
use DecodeLabs\Systemic;

$systemic = Monarch::getService(Systemic::class);

$dir = 'path/to/working-directory';

// Launch and capture output of a process
echo $systemic->capture(['ls', '-al'], $dir)->getOutput();

// Launch and capture output of a process with raw string command (not escaped)
echo $systemic->capture('ls -al', $dir)->getOutput();

// Launch and capture output of a script
echo $systemic->capture(['myPhpScript.php'], $dir)->getOutput();

// Launch a background task
$process = $systemic->launch(['make', 'install']);

// Launch a background script
$process = $systemic->launchScript(['myPhpScript.php'], $dir);

// Run a piped pseudo terminal process
$success = $systemic->run(['interactive-app', '--arg1'], $dir);

// Run a piped pseudo terminal script
$success = $systemic->runScript(['myPhpScript.php', '--arg1'], $dir);

// Custom escaped command
$success = $systemic->command(['escaped', 'arguments'])
    ->setWorkingDirectory($dir)
    ->addSignal('SIGSTOP') // Pass SIGSTOP through when caught
    ->setUser('someuser') // Attempt to use sudo to run as user
    ->run();

// Custom raw command with env arguments
$result = $systemic->command('echo ${:VARIABLE} | unescaped-command', [
        'VARIABLE' => 'Hello world'
    ])
    ->setWorkingDirectory($dir)
    ->capture();
```

### OS info

Get information about the current OS:

```php
// OS info
echo $systemic->os->name; // Linux | Windows | Darwin
echo $systemic->os->platformType; // Unix | Windows
echo $systemic->os->distribution; // eg Ubuntu or High Sierra, etc
echo $systemic->os->version; // System version info
echo $systemic->os->release; // System version number
echo $systemic->os->hostName; // System hostname

// Find binaries on the system
echo $systemic->os->which('php'); // eg /usr/local/bin/php
```


## Windows
Please note, OS and Process support on Windows is currently extremely sketchy - this will be fleshed out soon!

### Locale & Timezone

Looking for Locale and Timezone info? This has moved to [Cosmos](https://github.com/decodelabs/cosmos).

## Licensing
Systemic is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
