<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Os;

class Linux extends Unix
{
    protected const Distributions = [
        'Debian' => ['/etc/debian_release', '/etc/debian_version'],
        'SuSE' => ['/etc/SuSE-release', '/etc/UnitedLinux-release'],
        'Mandrake' => '/etc/mandrake-release',
        'Gentoo' => '/etc/gentoo-release',
        'Fedora' => '/etc/fedora-release',
        'RedHat' => ['/etc/redhat-release', '/etc/redhat_version'],
        'Slackware' => ['/etc/slackware-release', '/etc/slackware-version'],
        'Trustix' => ['/etc/trustix-release', '/etc/trustix-version'],
        'FreeEOS' => '/etc/eos-version',
        'Arch' => '/etc/arch-release',
        'Cobalt' => '/etc/cobalt-release',
        'LFS' => '/etc/lfs-release',
        'Rubix' => '/etc/rubix-release',
        'Ubuntu' => '/etc/lsb-release',
        'PLD' => '/etc/pld-release',
        'HLFS' => '/etc/hlfs-release',
        'Synology' => '/etc/synoinfo.conf'
    ];

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
        exec('lsb_release -a', $result);

        if (!empty($result) && is_array($result)) {
            foreach ($result as $line) {
                $parts = explode(':', $line, 2);
                $key = trim((string)array_shift($parts));

                if ($key == 'Description') {
                    return trim((string)array_shift($parts));
                }
            }
        }

        foreach (self::Distributions as $name => $files) {
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                if (file_exists($file)) {
                    return $name;
                }
            }
        }

        return 'Unknown';
    }
}
