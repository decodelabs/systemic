## v0.11.13 (2024-08-09)
* Fixed Manifold property in PHP8.2+

## v0.11.12 (2024-07-17)
* Updated Veneer dependency

## v0.11.11 (2024-04-29)
* Fixed Veneer stubs in gitattributes

## v0.11.10 (2024-04-26)
* Updated Archetype dependency
* Made PHP8.1 minimum version

## v0.11.9 (2023-10-27)
* Updated default ENV handling
* Refactored package file structure

## v0.11.8 (2023-09-26)
* Converted phpstan doc comments to generic

## v0.11.7 (2022-12-01)
* Fixed final custom provider loop on shutdown

## v0.11.6 (2022-12-01)
* Fixed final packet not read on process terminate

## v0.11.5 (2022-12-01)
* Disabled 0 byte write error check

## v0.11.4 (2022-12-01)
* Allow Stringable in array commands
* Fixed custom provideInput() ticker
* Use /dev/null for background manifold

## v0.11.3 (2022-12-01)
* Fixed DataReceiver empty write error check

## v0.11.2 (2022-12-01)
* Added Deliverance Broker interfaces to Commands / Controllers

## v0.11.1 (2022-11-30)
* Fixed run() calls over non TTY CLI sapis

## v0.11.0 (2022-11-30)
* Added Command interface structure
* Added Manifold / Controller interface structure
* Added custom Controller structure
* Moved timezone and locale plugins to Cosmos
* Refactored OS interface

## v0.10.5 (2022-11-27)
* Reverted PTY usage

## v0.10.4 (2022-11-25)
* Switched to PTY for decoratable commands

## v0.10.3 (2022-11-24)
* Added signal controls to launchers

## v0.10.2 (2022-11-23)
* Added signal passing to Unix launcher
* Migrated to use effigy in CI workflow

## v0.10.1 (2022-11-19)
* Added exit code handling to process results

## v0.10.0 (2022-11-18)
* Simplified launcher interfaces

## v0.9.6 (2022-11-18)
* Added Stringable to launcher interfaces
* Fixed PHP8.1 testing

## v0.9.6 (2022-10-04)
* Added Terminus STTY passthrough support

## v0.9.5 (2022-09-29)
* Updated Veneer plugin handling

## v0.9.4 (2022-09-27)
* Fixed $os plugin loading in process plugin

## v0.9.3 (2022-09-27)
* Updated Veneer stub
* Updated composer check script

## v0.9.2 (2022-09-27)
* Converted Veneer plugins to load with Attributes
* Updated CI environment

## v0.9.1 (2022-08-23)
* Added concrete types to all members

## v0.9.0 (2022-08-23)
* Removed PHP7 compatibility
* Updated ECS to v11
* Updated PHPUnit to v9

## v0.8.3 (2022-03-10)
* Transitioned from Travis to GHA
* Updated PHPStan and ECS dependencies

## v0.8.2 (2021-10-20)
* Updated Veneer dependency

## v0.8.1 (2021-04-11)
* Added Veneer IDE support stub

## v0.8.0 (2021-04-09)
* Swapped Atlas for Deliverance

## v0.7.1 (2021-04-07)
* Updated for max PHPStan conformance

## v0.7.0 (2021-03-18)
* Enabled PHP8 testing

## v0.6.13 (2020-10-06)
* Switched to Fluidity for Then dependency
* Applied full PSR12 standards
* Added PSR12 check to Travis build

## v0.6.12 (2020-10-05)
* Improved readme
* Updated PHPStan

## v0.6.11 (2020-10-05)
* Updated to Veneer 0.6

## v0.6.10 (2020-10-04)
* Switched to Glitch Proxy incomplete()

## v0.6.9 (2020-10-02)
* Updated glitch-support

## v0.6.8 (2020-10-02)
* Removed Glitch dependency

## v0.6.7 (2020-09-30)
* Switched to Exceptional for exception generation

## v0.6.6 (2020-09-25)
* Switched to Glitch Dumpable interface

## v0.6.5 (2020-09-24)
* Updated Composer dependency handling

## v0.6.4 (2019-11-06)
* Renamed launcher IO broker methods

## v0.6.3 (2019-10-31)
* Added canColorShell() helper
* Hide errors from tput
* Updated user handling in Unix launcher

## v0.6.2 (2019-10-16)
* Added PHPStan support
* Bugfixes and updates from max level PHPStan scan

## v0.6.1 (2019-10-11)
* Added r7 Multiplexer import support
* Added input generator to process launcher
* Added decoratable flag to launcher
* Added default path for HTTP process launching
* Various bug fixes in process launcher

## v0.6.0 (2019-10-06)
* Added Atlas Io Broker support to Process Launcher
* Added shell width and height fetcher

## v0.5.0 (2019-09-26)
* Added general Facade interface
* Added locale plugin
* Added timezone plugin
* Added process launcher and manager system
