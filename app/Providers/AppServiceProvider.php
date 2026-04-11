<?php

namespace OGame\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use OGame\Exceptions\Handler;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\FleetMission;
use OGame\Models\User;
use OGame\Observers\FleetMissionObserver;
use OGame\Observers\UserObserver;
use OGame\Services\SettingsService;
use RuntimeException;

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

        // Fleet arrival jobs are tracked by their database jobs-table ID. This only works
        // with the 'database' queue driver. Validate at boot so misconfiguration is caught
        // early rather than causing silent job-cancellation failures at runtime.
        $connection = config('queue.default');
        $driver = config("queue.connections.{$connection}.driver");
        if (!in_array($driver, ['database', 'sync', 'null'], true)) {
            throw new RuntimeException(
                "Queue driver \"{$driver}\" is not supported for fleet arrival job tracking. " .
                'OGameX requires the "database" queue driver (or "sync"/"null" for testing). ' .
                'Set QUEUE_CONNECTION=database in your .env file.'
            );
        }

        // Register model observers
        FleetMission::observe(FleetMissionObserver::class);
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
