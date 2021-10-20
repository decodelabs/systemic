<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;
use DecodeLabs\Veneer\Proxy;
use DecodeLabs\Veneer\ProxyTrait;
use DecodeLabs\Systemic\Context as Inst;
class Systemic implements Proxy { use ProxyTrait; 
const VENEER = 'Systemic';
const VENEER_TARGET = Inst::class;
const PLUGINS = Inst::PLUGINS;
public static $locale;
public static $os;
public static $process;
public static $timezone;};
