<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins\Os;

class Unix extends Base
{
    protected $platformType = 'Unix';

    /**
     * Get system user name from id
     */
    public function userIdToUserName(int $id): string
    {
        if (extension_loaded('posix')) {
            if (!$output = posix_getpwuid($id)) {
                throw Glitch::EInvalidArgument(
                    $id.' is not a valid user id'
                );
            }

            return $output['name'];
        } else {
            exec('getent passwd '.escapeshellarg($id), $output);

            if (isset($output[0])) {
                $parts = explode(':', $output[0]);
                return array_shift($parts);
            } else {
                throw Glitch::ERuntime(
                    'Unable to extract owner name'
                );
            }
        }
    }

    /**
     * Get system user id from name
     */
    public function userNameToUserId(string $name): int
    {
        if (extension_loaded('posix')) {
            if (!$output = posix_getpwnam($name)) {
                throw Glitch::EInvalidArgument(
                    $name.' is not a valid user name'
                );
            }

            return $output['uid'];
        } else {
            throw Glitch::EComponentUnavailable('POSIX extension is not available');
        }
    }

    /**
     * Get system group name from id
     */
    public function groupIdToGroupName(int $id): string
    {
        if (extension_loaded('posix')) {
            if (!$output = posix_getgrgid($id)) {
                throw Glitch::EInvalidArgument(
                    $id.' is not a valid group id'
                );
            }

            return $output['name'];
        } else {
            exec('getent group '.escapeshellarg($id), $output);

            if (isset($output[0])) {
                $parts = explode(':', $output[0]);
                return array_shift($parts);
            } else {
                throw Glitch::ERuntime(
                    'Unable to extract process group name'
                );
            }
        }
    }

    /**
     * Get system group id from name
     */
    public function groupNameToGroupId(string $name): int
    {
        if (extension_loaded('posix')) {
            if (!$output = posix_getgrnam($name)) {
                throw Glitch::EInvalidArgument(
                    $name.' is not a valid group name'
                );
            }
            return $output['gid'];
        } else {
            throw Glitch::EComponentUnavailable('POSIX extension is not available');
        }
    }

    /**
     * Lookup system binary location
     */
    public function which(string $binaryName): string
    {
        if ($binaryName == 'php') {
            $output = dirname(PHP_BINARY).'/php';

            if (false === strpos($output, '/Cellar/php')) {
                return $output;
            }
        }


        $result = Systemic::$process->launch('which '.$binaryName)->getOutput();

        if (empty($result)) {
            $result = Systemic::$process->launch('type '.$binaryName)->getOutput();

            if (empty($result)) {
                return $binaryName;
            }

            $result = trim($result);

            if (!preg_match('/^[^ ]+ is (.*)$/', $result, $matches)) {
                return $binaryName;
            }

            return $matches[1];
        } else {
            $result = trim($result);
        }

        return $result;
    }
}
