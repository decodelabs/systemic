<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Process\Launcher;

use DecodeLabs\Systemic\Process;
use DecodeLabs\Systemic\Process\Launcher;

class Windows extends Base
{
    public function launch()
    {
        Glitch::incomplete($this);
    }

    public function launchBackground()
    {
        Glitch::incomplete($this);
    }
}
