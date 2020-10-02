<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Result;
use DecodeLabs\Systemic\Process\Launcher;
use DecodeLabs\Systemic\Process\LauncherTrait;

use DecodeLabs\Exceptional;

class Windows implements Launcher
{
    use LauncherTrait;

    public function launch(): Result
    {
        Exceptional::incomplete($this);
    }

    public function launchBackground(): Process
    {
        Exceptional::incomplete($this);
    }
}
