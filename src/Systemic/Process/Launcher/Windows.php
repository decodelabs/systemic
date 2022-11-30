<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Glitch\Proxy as Glitch;
use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Launcher;
use DecodeLabs\Systemic\Process\LauncherTrait;
use DecodeLabs\Systemic\Result;

class Windows implements Launcher
{
    use LauncherTrait;

    public function launch(): Result
    {
        Glitch::incomplete($this);
    }

    public function launchBackground(): Process
    {
        Glitch::incomplete($this);
    }
}
