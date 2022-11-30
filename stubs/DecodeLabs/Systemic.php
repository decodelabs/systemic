<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Systemic\Context as Inst;
use DecodeLabs\Systemic\Os as OsPlugin;
use DecodeLabs\Systemic\Plugins\Process as ProcessPlugin;
use DecodeLabs\Veneer\Plugin\Wrapper as PluginWrapper;
use DecodeLabs\Systemic\Process as Ref0;
use Stringable as Ref1;
use DecodeLabs\Systemic\Command as Ref2;
use DecodeLabs\Systemic\Result as Ref3;

class Systemic implements Proxy
{
    use ProxyTrait;

    const VENEER = 'DecodeLabs\Systemic';
    const VENEER_TARGET = Inst::class;

    public static Inst $instance;
    /** @var OsPlugin|PluginWrapper<OsPlugin> $os */
    public static OsPlugin|PluginWrapper $os;
    /** @var ProcessPlugin|PluginWrapper<ProcessPlugin> $process */
    public static ProcessPlugin|PluginWrapper $process;

    public static function getProcess(int $pid): Ref0 {
        return static::$instance->getProcess(...func_get_args());
    }
    public static function call(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): Ref3 {
        return static::$instance->call(...func_get_args());
    }
    public static function callScript(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): Ref3 {
        return static::$instance->callScript(...func_get_args());
    }
    public static function launch(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): Ref0 {
        return static::$instance->launch(...func_get_args());
    }
    public static function launchScript(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): Ref0 {
        return static::$instance->launchScript(...func_get_args());
    }
    public static function run(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): bool {
        return static::$instance->run(...func_get_args());
    }
    public static function runScript(Ref1|Ref2|array|string $command, Ref1|string|null $workingDirectory = NULL): bool {
        return static::$instance->runScript(...func_get_args());
    }
    public static function command(Ref1|Ref2|array|string $command, array $variables = []): Ref2 {
        return static::$instance->command(...func_get_args());
    }
    public static function scriptCommand(Ref1|Ref2|array|string $command, array $variables = []): Ref2 {
        return static::$instance->scriptCommand(...func_get_args());
    }
};
