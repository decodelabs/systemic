<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Os;

use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Nuance\Dumpable;
use DecodeLabs\Systemic\Os;
use DecodeLabs\Systemic\OsTrait;

class Unix implements
    Os,
    Dumpable
{
    use OsTrait {
        OsTrait::__construct as __parentConstruct;
    }

    public function __construct(
        string $name
    ) {
        $this->__parentConstruct($name);
        $this->platformType = 'Unix';
    }

    /**
     * Get system user name from id
     */
    public function userIdToUserName(
        int $id
    ): string {
        if (extension_loaded('posix')) {
            if (!$output = posix_getpwuid($id)) {
                throw Exceptional::InvalidArgument(
                    $id . ' is not a valid user id'
                );
            }

            return $output['name'];
        }

        exec('getent passwd ' . escapeshellarg((string)$id), $output);

        if (isset($output[0])) {
            return explode(':', $output[0])[0];
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract owner name'
        );
    }

    /**
     * Get system user id from name
     */
    public function userNameToUserId(
        string $name
    ): int {
        if (extension_loaded('posix')) {
            if (!$output = posix_getpwnam($name)) {
                throw Exceptional::InvalidArgument(
                    $name . ' is not a valid user name'
                );
            }

            return Coercion::asInt($output['uid']);
        }

        throw Exceptional::ComponentUnavailable(
            message: 'POSIX extension is not available'
        );
    }

    /**
     * Get system group name from id
     */
    public function groupIdToGroupName(
        int $id
    ): string {
        if (extension_loaded('posix')) {
            if (!$output = posix_getgrgid($id)) {
                throw Exceptional::InvalidArgument(
                    $id . ' is not a valid group id'
                );
            }

            return $output['name'];
        }

        exec('getent group ' . escapeshellarg((string)$id), $output);

        if (isset($output[0])) {
            return explode(':', $output[0])[0];
        }

        throw Exceptional::Runtime(
            message: 'Unable to extract process group name'
        );
    }

    /**
     * Get system group id from name
     */
    public function groupNameToGroupId(
        string $name
    ): int {
        if (extension_loaded('posix')) {
            if (!$output = posix_getgrnam($name)) {
                throw Exceptional::InvalidArgument(
                    $name . ' is not a valid group name'
                );
            }
            return $output['gid'];
        }

        throw Exceptional::ComponentUnavailable(
            message: 'POSIX extension is not available'
        );
    }

    /**
     * Lookup system binary location
     */
    public function which(
        string $binaryName
    ): string {
        if ($binaryName == 'php') {
            $output = dirname(PHP_BINARY) . '/php';

            if (false === strpos($output, '/Cellar/php')) {
                return $output;
            }
        }

        exec('which ' . $binaryName, $result);

        if (empty($result)) {
            exec('type ' . $binaryName, $result);

            if (empty($result)) {
                return $binaryName;
            }

            $result = trim($result[0]);

            if (!preg_match('/^[^ ]+ is (.*)$/', $result, $matches)) {
                return $binaryName;
            }

            return $matches[1];
        } else {
            $result = trim($result[0]);
        }

        return $result;
    }
}
