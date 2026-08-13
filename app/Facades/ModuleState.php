<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Facade;
use OGame\Services\ModuleStateService;

/**
 * @method static \OGame\Services\ModuleStateNamespace module(string $alias)
 *
 * @see ModuleStateService
 */
class ModuleState extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModuleStateService::class;
    }
}
