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
use DecodeLabs\Veneer\Plugin\Wrapper as PluginWrapper;
use DecodeLabs\Systemic\Process as Ref0;
use DecodeLabs\Systemic\ActiveProcess as Ref1;
use DecodeLabs\Eventful\Signal as Ref2;
use Stringable as Ref3;
use DecodeLabs\Systemic\Command as Ref4;
use DecodeLabs\Systemic\Result as Ref5;
use DecodeLabs\Systemic\Controller as Ref6;

class Systemic implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Systemic';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;
    /** @var OsPlugin|PluginWrapper<OsPlugin> $os */
    public static OsPlugin|PluginWrapper $os;

    public static function getProcess(int $pid): Ref0 {
        return static::$_veneerInstance->getProcess(...func_get_args());
    }
    public static function getCurrentProcess(): Ref1 {
        return static::$_veneerInstance->getCurrentProcess();
    }
    public static function newSignal(Ref2|string|int $signal): Ref2 {
        return static::$_veneerInstance->newSignal(...func_get_args());
    }
    public static function normalizeSignal(Ref2|string|int $signal): int {
        return static::$_veneerInstance->normalizeSignal(...func_get_args());
    }
    public static function capture(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref5 {
        return static::$_veneerInstance->capture(...func_get_args());
    }
    public static function captureScript(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref5 {
        return static::$_veneerInstance->captureScript(...func_get_args());
    }
    public static function liveCapture(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref5 {
        return static::$_veneerInstance->liveCapture(...func_get_args());
    }
    public static function liveCaptureScript(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref5 {
        return static::$_veneerInstance->liveCaptureScript(...func_get_args());
    }
    public static function launch(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref0 {
        return static::$_veneerInstance->launch(...func_get_args());
    }
    public static function launchScript(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): Ref0 {
        return static::$_veneerInstance->launchScript(...func_get_args());
    }
    public static function run(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): bool {
        return static::$_veneerInstance->run(...func_get_args());
    }
    public static function runScript(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory = NULL): bool {
        return static::$_veneerInstance->runScript(...func_get_args());
    }
    public static function start(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory, Ref6|callable $controller): Ref5 {
        return static::$_veneerInstance->start(...func_get_args());
    }
    public static function startScript(Ref3|Ref4|array|string $command, Ref3|string|null $workingDirectory, Ref6|callable $controller): Ref5 {
        return static::$_veneerInstance->startScript(...func_get_args());
    }
    public static function command(Ref3|Ref4|array|string $command, array $variables = []): Ref4 {
        return static::$_veneerInstance->command(...func_get_args());
    }
    public static function scriptCommand(Ref3|Ref4|array|string $command, array $variables = []): Ref4 {
        return static::$_veneerInstance->scriptCommand(...func_get_args());
    }
};
