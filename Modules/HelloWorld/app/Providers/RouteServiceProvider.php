<?php

namespace Modules\HelloWorld\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'HelloWorld';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        Route::middleware('web')->group(module_path($this->name, 'routes/web.php'));
    }
}
