<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Facade;
use OGame\Services\ModuleQueueService;

/** @see ModuleQueueService */
class ModuleQueues extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ModuleQueueService::class;
    }
}
