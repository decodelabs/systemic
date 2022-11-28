<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic;

use DecodeLabs\Systemic\Plugins\Os;
use DecodeLabs\Systemic\Plugins\Os\Base as OsBase;
use DecodeLabs\Systemic\Plugins\Process as ProcessPlugin;

use DecodeLabs\Veneer\LazyLoad;
use DecodeLabs\Veneer\Plugin;
use DecodeLabs\Veneer\Plugin\Wrapper;

class Context
{
    /**
     * @phpstan-var Os|Wrapper<Os>
     */
    #[Plugin(OsBase::class)]
    #[LazyLoad]
    public Os|Wrapper $os;

    #[Plugin]
    #[LazyLoad]
    public ProcessPlugin $process;
}
