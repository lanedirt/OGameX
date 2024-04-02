<?php

namespace OGame\Facades;

use Illuminate\Support\Facades\Facade;

class AppUtil extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'appUtil';
    }
}