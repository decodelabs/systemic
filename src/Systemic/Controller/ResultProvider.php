<?php

/**
 * Systemic
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Controller;

use DecodeLabs\Systemic\Result;

interface ResultProvider
{
    public function getResult(): Result;
}
