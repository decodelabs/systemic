<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Os;

class Darwin extends Unix
{
    protected function lookupDistribution(): string
    {
        exec('sed -nE \'/SOFTWARE LICENSE AGREEMENT FOR/s/([A-Za-z]+ ){5}|\\$//gp\' /System/Library/CoreServices/Setup\ Assistant.app/Contents/Resources/en.lproj/OSXSoftwareLicense.rtf', $result);
        $result = trim($result[0], '\\/ ');

        if (empty($result)) {
            return 'Darwin';
        }

        return (string)$result;
    }
}
