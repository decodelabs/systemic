# Systemic

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/systemic/integrate.yml?branch=develop)](https://github.com/decodelabs/systemic/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)

### System processes and information at your fingertips

Systemic offers an easy to use frontend to launching and controlling processes and accessing system information.

_Get news and updates on the [DecodeLabs blog](https://blog.decodelabs.com)._

---


## Installation

Install via composer:

```bash
composer require decodelabs/systemic
```

## Usage

### Importing

Systemic uses [Veneer](https://github.com/decodelabs/veneer) to provide a unified frontage under <code>DecodeLabs\Systemic</code>.
You can access all the primary functionality via this static frontage without compromising testing and dependency injection.



### Process launching

Launch new processes:

```php
use DecodeLabs\Systemic;

$dir = 'path/to/working-directory';

// Launch and capture output of a process
echo Systemic::capture(['ls', '-al'], $dir)->getOutput();

// Launch and capture output of a process with raw string command (not escaped)
echo Systemic::capture('ls -al', $dir)->getOutput();

// Launch and capture output of a script
echo Systemic::capture(['myPhpScript.php'], $dir)->getOutput();

// Launch a background task
$process = Systemic::launch(['make', 'install']);

// Launch a background script
$process = Systemic::launchScript(['myPhpScript.php'], $dir);

// Run a piped pseudo terminal process
$success = Systemic::run(['interactive-app', '--arg1'], $dir);

// Run a piped pseudo terminal script
$success = Systemic::runScript(['myPhpScript.php', '--arg1'], $dir);

// Custom escaped command
$success = Systemic::command(['escaped', 'arguments'])
    ->setWorkingDirectory($dir)
    ->addSignal('SIGSTOP') // Pass SIGSTOP through when caught
    ->setUser('someuser') // Attempt to use sudo to run as user
    ->run();

// Custom raw command with env arguments
$result = Systemic::command('echo ${:VARIABLE} | unescaped-command', [
        'VARIABLE' => 'Hello world'
    ])
    ->setWorkingDirectory($dir)
    ->capture();
```

### OS info

Get information about the current OS:

```php
use DecodeLabs\Systemic;

// OS info
echo Systemic::$os->getName(); // Linux | Windows | Darwin
echo Systemic::$os->getPlatformType(); // Unix | Windows
echo Systemic::$os->getDistribution(); // eg Ubuntu or High Sierra, etc
echo Systemic::$os->getVersion(); // System version info
echo Systemic::$os->getRelease(); // System version number
echo Systemic::$os->getHostName(); // System hostname

// Find binaries on the system
echo Systemic::$os->which('php'); // eg /usr/local/bin/php
```


## Windows
Please note, OS and Process support on Windows is currently extremely sketchy - this will be fleshed out soon!

### Locale & Timezone

Looking for Locale and Timezone info? This has moved to [Cosmos](https://github.com/decodelabs/cosmos).

## Licensing
Systemic is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
