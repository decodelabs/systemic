<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Plugins\Os;

class Darwin extends Unix
{
    protected ?string $distribution = null;

    /**
     * Get OS distribution
     */
    public function getDistribution(): string
    {
        if ($this->distribution === null) {
            $this->distribution = $this->lookupDistribution();
        }

        return $this->distribution;
    }

    /**
     * Extract distribution info
     */
    private function lookupDistribution(): string
    {
        exec('sed -nE \'/SOFTWARE LICENSE AGREEMENT FOR/s/([A-Za-z]+ ){5}|\\$//gp\' /System/Library/CoreServices/Setup\ Assistant.app/Contents/Resources/en.lproj/OSXSoftwareLicense.rtf', $result);
        $result = trim($result[0], '\\/ ');

        if (empty($result)) {
            return 'Darwin';
        }

        return (string)$result;
    }
}
