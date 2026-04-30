<?php

namespace OGame\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Log;
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

        // Fleet arrival jobs are tracked by their database jobs-table row ID (an integer).
        // Non-database drivers (e.g. Redis) return UUID string job IDs, which cannot be
        // stored in the arrival_job_id column. In that case job tracking is silently skipped
        // and the scheduler fallback (ProcessFleetArrivals, runs every minute) ensures
        // overdue missions are still processed. Log a warning so operators are aware.
        $connection = config('queue.default');
        $driver = config("queue.connections.{$connection}.driver");
        if (!in_array($driver, ['database', 'sync', 'null'], true)) {
            Log::warning(
                "Queue driver \"{$driver}\" does not support fleet arrival job tracking. " .
                'Fleet missions will be processed by the scheduler fallback instead of ' .
                'precise delayed jobs. Use QUEUE_CONNECTION=database for sub-second precision.'
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
