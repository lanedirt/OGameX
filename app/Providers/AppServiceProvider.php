<?php

namespace OGame\Providers;

use Illuminate\Support\ServiceProvider;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Http\Controllers\FacilitiesController;
use OGame\Http\Controllers\ResourcesController;
use OGame\Http\Controllers\ResearchController;

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
    }
}
