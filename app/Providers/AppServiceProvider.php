<?php

namespace OGame\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OGame\Exceptions\Handler;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Observers\UserObserver;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register composer file for the main ingame layout.
        view()->composer('ingame.layouts.main', 'OGame\Http\ViewComposers\IngameMainComposer');

        // Register model observers
        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    final public function register(): void
    {
        $this->app->singleton(function ($app): SettingsService {
            return new SettingsService();
        });

        $this->app->singleton(function ($app): PlayerServiceFactory {
            return new PlayerServiceFactory();
        });

        $this->app->singleton(function ($app): PlanetServiceFactory {
            return new PlanetServiceFactory(
                $app->make(SettingsService::class),
                $app->make(PlayerServiceFactory::class)
            );
        });

        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }
}
