<?php

namespace OGame\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Services\SettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    final public function boot(): void
    {
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register composer file for the main ingame layout.
        view()->composer('ingame.layouts.main', 'OGame\Http\ViewComposers\IngameMainComposer');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    final public function register(): void
    {
        $this->app->singleton(PlayerServiceFactory::class, function ($app) {
            return new PlayerServiceFactory();
        });

        $this->app->singleton(PlanetServiceFactory::class, function ($app) {
            return new PlanetServiceFactory($app->make(SettingsService::class));
        });
    }
}
