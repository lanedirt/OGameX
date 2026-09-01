<?php

namespace Tests\Feature;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Services\SettingsService;
use Tests\TestCase;

/**
 * Guards the per-job singleton reset for queue workers.
 *
 * Queue workers keep the container (and its singletons) alive across jobs.
 * PlayerServiceFactory, PlanetServiceFactory and SettingsService all cache
 * database state in memory, so a worker that touched a player or setting in an
 * earlier job would otherwise process later battles with stale tech levels,
 * tactical retreat preferences, or server settings. AppServiceProvider listens
 * for JobProcessing and forgets these singletons so each job starts fresh.
 */
class QueueWorkerStateResetTest extends TestCase
{
    /**
     * A JobProcessing event from a real queue connection must reset the cached
     * singleton instances so the job resolves fresh ones.
     */
    public function testJobProcessingResetsCachedSingletons(): void
    {
        $settings = app(SettingsService::class);
        $playerFactory = app(PlayerServiceFactory::class);
        $planetFactory = app(PlanetServiceFactory::class);

        $this->fireJobProcessing('database');

        $this->assertNotSame($settings, app(SettingsService::class), 'SettingsService should be re-resolved after a job starts');
        $this->assertNotSame($playerFactory, app(PlayerServiceFactory::class), 'PlayerServiceFactory should be re-resolved after a job starts');
        $this->assertNotSame($planetFactory, app(PlanetServiceFactory::class), 'PlanetServiceFactory should be re-resolved after a job starts');
    }

    /**
     * The sync driver runs jobs inline in the dispatching request (or test),
     * where callers still hold references to the current singletons. Resetting
     * mid-request would detach those instances, so sync must be excluded.
     */
    public function testSyncConnectionDoesNotResetSingletons(): void
    {
        $settings = app(SettingsService::class);
        $playerFactory = app(PlayerServiceFactory::class);
        $planetFactory = app(PlanetServiceFactory::class);

        $this->fireJobProcessing('sync');

        $this->assertSame($settings, app(SettingsService::class), 'SettingsService should be untouched for sync jobs');
        $this->assertSame($playerFactory, app(PlayerServiceFactory::class), 'PlayerServiceFactory should be untouched for sync jobs');
        $this->assertSame($planetFactory, app(PlanetServiceFactory::class), 'PlanetServiceFactory should be untouched for sync jobs');
    }

    /**
     * Fire a JobProcessing event as the queue worker would for the given connection.
     */
    private function fireJobProcessing(string $connectionName): void
    {
        $job = $this->createStub(Job::class);

        Event::dispatch(new JobProcessing($connectionName, $job));
    }
}
