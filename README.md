# Systemic

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/systemic.svg?style=flat)](https://packagist.org/packages/decodelabs/systemic)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/decodelabs/systemic/Integrate)](https://github.com/decodelabs/systemic/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/systemic?style=flat)](https://packagist.org/packages/decodelabs/systemic)

Get access to useful global system and environment info all in one place.


## Installation

Install via composer:

```bash
composer require decodelabs/systemic
```

## Usage

### Importing

Systemic uses [Veneer](https://github.com/decodelabs/veneer) to provide a unified frontage under <code>DecodeLabs\Systemic</code>.
You can access all the primary functionality via this static frontage without compromising testing and dependency injection.


### Locale

Get and set the active Locale for output formatting:

```php
use DecodeLabs\Systemic;

// Set locale to German
Systemic::$locale->set('de_DE');

// Get local
Systemic::$locale->get();

// Add a listener for when the locale changes
Systemic::$locale->addListener('myListener', function($newLocale, $oldLocale) {
    // do something here
});
```


### Timezone

Get and set the active user timezone for output formatting:

```php
use DecodeLabs\Systemic;

// Set timezone to london
Systemic::$timezone->set('Europe/London');

// Get current
Systemic::$timezone->get();

// Add a listener for when the timezone changes
Systemic::$timezone->addListener('myListener', function($newLocale, $oldLocale) {
    // do something here
});
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


### Process launching

Launch new processes:

```php
use DecodeLabs\Systemic;

// Launch a normal process
echo Systemic::$process->launch('echo hello', ['-h'])->getOutput(); // hello -h

// Launch a background task
$process = Systemic::$process->launchBackground('make install');


// Launch a PHP script
$result = Systemic::$process->launchScript('myPhpScript.php');

// Launch a background PHP script
$result = Systemic::$process->launchBackgroundScript('myPhpScript.php');

// Custom launch something
Systemic::$process->newLauncher('binary', ['-a1', '--arg2=stuff'], 'path/to/thing')
    ->setWorkingDirectory('somewhere/else')
    ->setUser('root')

    ->setOutputWriter(function($outputChunk) {
        // send the output somewhere else...
    })
    ->setInputReader(function(int $chunkSize) {
        // read from input - usually from fread()
    })

    ->launch();
```


## Windows
Please note, OS and Process support on Windows is currently extremely sketchy - this will be fleshed out soon!


## Licensing
Systemic is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
