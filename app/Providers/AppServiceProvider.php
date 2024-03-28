<?php

namespace OGame\Providers;

use Illuminate\Support\ServiceProvider;
use OGame\Utils\AppUtil;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register composer file for the main ingame layout.
        view()->composer('ingame.layouts.main', 'OGame\Http\ViewComposers\IngameMainComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('appUtil', function ($app) {
            return new AppUtil();
        });
    }
}
