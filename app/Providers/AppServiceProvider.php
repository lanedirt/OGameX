<?php

namespace OGame\Providers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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

        // Legacy JS bundles are plain concatenated scripts that rely on jQuery globals
        // and must not be treated as ES modules.
        Vite::useScriptTagAttributes(['type' => false]);

        // Register composer file for the main ingame layout.
        view()->composer('ingame.layouts.main', 'OGame\Http\ViewComposers\IngameMainComposer');

        // Fleet arrival jobs are tracked by their database jobs-table row ID (an integer).
        // Non-database drivers (e.g. Redis) return UUID string job IDs, which cannot be
        // stored in the arrival_job_id column. Delayed jobs are still dispatched and
        // processed on those drivers, but they cannot be tracked: mission updates dispatch
        // duplicate jobs instead of reusing the existing one, and recalls cannot delete
        // stale jobs. This is benign — processing is idempotent under the per-destination
        // lock — but wasteful. Log a warning so operators are aware.
        $connection = config('queue.default');
        $driver = config("queue.connections.{$connection}.driver");
        if (!in_array($driver, ['database', 'sync', 'null'], true)) {
            Log::warning(
                "Queue driver \"{$driver}\" does not support fleet arrival job tracking. " .
                'Delayed arrival jobs are still dispatched, but mission updates cannot reuse ' .
                'existing jobs and recalls cannot delete stale ones, so duplicate jobs may run. ' .
                'This is harmless (processing is idempotent under the destination lock) but ' .
                'wasteful. Use QUEUE_CONNECTION=database for tracked delayed jobs.'
            );
        }

        // Register model observers
        FleetMission::observe(FleetMissionObserver::class);
        User::observe(UserObserver::class);

        // Queue workers are long-lived processes that keep container singletons across
        // jobs. The service factories and settings service cache database state in
        // memory, so without a reset a worker would battle players with stale tech
        // levels, retreat preferences, or server settings cached by an earlier job.
        // Forget the singleton instances before each job so it starts with fresh state.
        // The sync driver is excluded: it runs inline in a web request (or test), where
        // flushing mid-request would detach instances the caller still holds.
        Event::listen(JobProcessing::class, function (JobProcessing $event): void {
            if ($event->connectionName === 'sync') {
                return;
            }

            $this->app->forgetInstance(SettingsService::class);
            $this->app->forgetInstance(PlayerServiceFactory::class);
            $this->app->forgetInstance(PlanetServiceFactory::class);
        });
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
