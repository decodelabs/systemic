<?php

/**
 * @package Systemic
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Systemic\Command;

use DecodeLabs\Systemic\Command;
use DecodeLabs\Systemic\CommandTrait;

class Unix implements Command
{
    use CommandTrait {
        __toString as traitToString;
    }

    public function __toString(): string
    {
        $output = $this->traitToString();

        if ($this->user !== null) {
            $output = 'sudo -k -u ' . $this->user . ' ' . $output;
        } elseif (is_array($this->command)) {
            $output = 'exec ' . $output;
        }

        if ($this->isSigChildEnabled()) {
            $output = '{ (' . $output . ') <&3 3<&- 3>/dev/null & } 3<&0;';
            $output .= 'pid=$!; echo $pid >&3; wait $pid; code=$?; echo $code >&3; exit $code';
        }

        return $output;
    }

    protected function escapeArgument(
        string $argument
    ): string {
        if ($argument === '') {
            return '""';
        }

        return "'" . str_replace("'", "'\\''", $argument) . "'";
    }
}
