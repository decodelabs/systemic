<?php
/**
 * This file is part of the Systemic package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Systemic;

use DecodeLabs\Veneer\FacadeTarget;
use DecodeLabs\Veneer\FacadeTargetTrait;
use DecodeLabs\Veneer\FacadePluginAccessTarget;
use DecodeLabs\Veneer\FacadePluginAccessTargetTrait;
use DecodeLabs\Veneer\FacadePlugin;

class Context implements FacadeTarget, FacadePluginAccessTarget
{
    use FacadeTargetTrait;
    use FacadePluginAccessTargetTrait;

    const FACADE = 'Systemic';
}
