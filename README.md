# Systemic
Get access to useful global system and environment info all in one place.


## Installation

Install via composer:

```bash
composer require decodelabs/systemic
```


## Usage

### Importing

Systemic uses a [Veneer Facade](https://github.com/decodelabs/veneer) so you don't _need_ to add any <code>use</code> declarations to your code, the class will be aliased into whatever namespace you are working in.

However, if you want to avoid filling your namespace with class aliases, you can import the Facade with:

```php
use DecodeLabs\Systemic;
```

### Locale

Get and set the active Locale for output formatting:

```php
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


## Licensing
Systemic is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
