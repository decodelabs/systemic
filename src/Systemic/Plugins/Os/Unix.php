<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic\Plugins\Os;

use DecodeLabs\Glitch;

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
        }

        exec('getent passwd '.escapeshellarg((string)$id), $output);

        if (isset($output[0])) {
            $parts = explode(':', $output[0]);

            if (null !== ($output = array_shift($parts))) {
                return $output;
            }
        }

        throw Glitch::ERuntime(
            'Unable to extract owner name'
        );
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
        }

        throw Glitch::EComponentUnavailable('POSIX extension is not available');
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
        }

        exec('getent group '.escapeshellarg((string)$id), $output);

        if (isset($output[0])) {
            $parts = explode(':', $output[0]);

            if (null !== ($output = array_shift($parts))) {
                return $output;
            }
        }

        throw Glitch::ERuntime(
            'Unable to extract process group name'
        );
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
        }

        throw Glitch::EComponentUnavailable('POSIX extension is not available');
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

        exec('which '.$binaryName, $result);

        if (empty($result)) {
            exec('type '.$binaryName, $result);

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

    /**
     * Get connected shell columns
     */
    public function getShellWidth(): int
    {
        exec('tput cols 2>/dev/null', $result);
        return (int)($result[0] ?? 80);
    }

    /**
     * Get connected shell lines
     */
    public function getShellHeight(): int
    {
        exec('tput lines 2>/dev/null', $result);
        return (int)($result[0] ?? 30);
    }

    /**
     * Can color shell
     */
    public function canColorShell(): bool
    {
        static $output;

        if (!isset($output)) {
            if (!defined('STDOUT')) {
                return $output = false;
            }

            if (function_exists('stream_isatty')) {
                return $output = @stream_isatty(\STDOUT);
            }

            if (function_exists('posix_isatty')) {
                return $output = @posix_isatty(\STDOUT);
            }

            if (($_SERVER['TERM'] ?? null) === 'xterm-256color') {
                return $output = true;
            }

            if (($_SERVER['CLICOLOR'] ?? null) === '1') {
                return $output = true;
            }

            return $output = false;
        }

        return $output;
    }
}
