<?php

namespace Tests\Feature;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Jobs\ProcessFleetArrival;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

class FleetArrivalQueueTest extends FleetDispatchTestCase
{
    protected int $missionType = 3;

    protected string $missionName = 'Transport';

    protected function setUp(): void
    {
        parent::setUp();

        config(['queue.default' => 'database']);
        DB::table('jobs')->delete();
    }

    public function reloadApplication(): void
    {
        parent::reloadApplication();
        config(['queue.default' => 'database']);
    }

    protected function tearDown(): void
    {
        if ($this->currentUserId !== 0) {
            $planetIds = Planet::where('user_id', $this->currentUserId)->pluck('id')->all();

            if (!empty($planetIds)) {
                FleetMission::where(function ($query) use ($planetIds) {
                    $query->whereIn('planet_id_from', $planetIds)
                        ->orWhereIn('planet_id_to', $planetIds);
                })->whereNotNull('parent_id')->delete();

                FleetMission::where(function ($query) use ($planetIds) {
                    $query->whereIn('planet_id_from', $planetIds)
                        ->orWhereIn('planet_id_to', $planetIds);
                })->delete();
            }

            Message::where('user_id', $this->currentUserId)->delete();
            DB::table('users_tech')->where('user_id', $this->currentUserId)->delete();
            // Clear the FK on users.planet_current before deleting the planet rows it references.
            DB::table('users')->where('id', $this->currentUserId)->update(['planet_current' => null]);
            Planet::where('user_id', $this->currentUserId)->delete();
            User::where('id', $this->currentUserId)->delete();
        }

        DB::table('jobs')->delete();

        parent::tearDown();
    }

    protected function basicSetup(): void
    {
        $this->planetAddUnit('large_cargo', 5);
        $this->planetAddResources(new Resources(100000, 100000, 100000, 0));

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed_peaceful', 1);
    }

    public function testDispatchStoresMillisecondArrivalAndQueuesDelayedJob(): void
    {
        $this->travelTo(Date::create(2024, 1, 1, 0, 0, 0)->addMilliseconds(123));
        $this->basicSetup();

        $this->sendMissionToSecondPlanet($this->createCargoUnits(1), new Resources(1000, 500, 250, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(
            123,
            $mission->time_arrival_ms - ($mission->time_arrival * 1000),
            'Millisecond arrival precision was not stored on the mission.'
        );

        $this->assertNotNull($mission->arrival_job_id, 'Mission did not store a delayed arrival job ID.');
        $this->assertTrue(
            DB::table('jobs')->where('id', $mission->arrival_job_id)->exists(),
            'Delayed arrival job was not written to the database queue.'
        );
    }

    public function testJobHandlerProcessesAllDueMissionsAtDestinationInMillisecondOrder(): void
    {
        $this->basicSetup();

        $baseArrival = (int) now()->timestamp - 1;
        $firstMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 100);
        $secondMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 900);

        // The job is dispatched for the LATER mission only. When handled it must
        // discover and process the earlier mission too, in ms order, because both
        // share the same destination — the handler is destination-scoped, not
        // mission-scoped.
        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($firstMission, $secondMission) {
            /** @var \Mockery\Expectation $e1 */
            $e1 = $mock->shouldReceive('updateMission');
            $e1->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $firstMission->id));

            /** @var \Mockery\Expectation $e2 */
            $e2 = $mock->shouldReceive('updateMission');
            $e2->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $secondMission->id));
        });

        $job = new ProcessFleetArrival($secondMission->id);
        $job->handle($service);
    }

    public function testSameDestinationMissionsProcessInMillisecondOrder(): void
    {
        $this->basicSetup();

        $baseArrival = (int) now()->timestamp - 1;
        $firstMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 101);
        $secondMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 909);

        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($firstMission, $secondMission) {
            /** @var \Mockery\Expectation $e1 */
            $e1 = $mock->shouldReceive('updateMission');
            $e1->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $firstMission->id));

            /** @var \Mockery\Expectation $e2 */
            $e2 = $mock->shouldReceive('updateMission');
            $e2->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $secondMission->id));
        });

        $service->processDueMissionEventsForMission($secondMission);
    }

    public function testRecallRemovesPendingArrivalJobAndQueuesReturnMission(): void
    {
        $this->basicSetup();

        $this->sendMissionToSecondPlanet($this->createCargoUnits(1), new Resources(1000, 500, 250, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->latest('id')
            ->firstOrFail();

        $originalArrivalJobId = $mission->arrival_job_id;

        $this->assertNotNull($originalArrivalJobId, 'Mission did not store its delayed arrival job ID.');
        $this->assertTrue(
            DB::table('jobs')->where('id', $originalArrivalJobId)->exists(),
            'Expected the delayed arrival job to be present before recall.'
        );

        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $mission->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $mission->refresh();

        $this->assertSame(1, $mission->canceled);
        $this->assertSame(1, $mission->processed);
        $this->assertFalse(
            DB::table('jobs')->where('id', $originalArrivalJobId)->exists(),
            'Recall should delete the original delayed arrival job.'
        );

        $returnMission = FleetMission::query()
            ->where('parent_id', $mission->id)
            ->latest('id')
            ->firstOrFail();

        $this->assertNotNull($returnMission->arrival_job_id, 'Return mission was not queued after recall.');
        $this->assertTrue(
            DB::table('jobs')->where('id', $returnMission->arrival_job_id)->exists(),
            'Return mission delayed job was not written to the queue.'
        );
    }

    public function testSchedulerFallbackSkipsLockedDestinationAndContinues(): void
    {
        $this->basicSetup();

        // Remove all overdue unprocessed missions left by previous test runs so that
        // processMissedMissionEvents() only sees the two missions created below.
        // Without this, the partial mock receives unexpected calls for those leftovers
        // and falls through to the real implementation, which requires uninitialized
        // service dependencies.
        DB::table('fleet_missions')
            ->where('processed', 0)
            ->where('canceled', 0)
            ->where('time_arrival', '<=', (int) now()->timestamp)
            ->delete();

        $baseArrival = (int) now()->timestamp - 1;

        // Two missions at different destinations — A goes to secondPlanet, B goes to primaryPlanet.
        $missionAtA = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 100);
        $missionAtB = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 200, $this->planetService);

        // Simulate both destinations being encountered. A is held by a long-running
        // battle (throws LockTimeoutException). B proceeds normally (void return).
        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($missionAtA, $missionAtB) {
            /** @var \Mockery\Expectation $e1 */
            $e1 = $mock->shouldReceive('processDueMissionEventsForMission');
            $e1->once()->with(Mockery::on(fn (FleetMission $m) => $m->id === $missionAtA->id))->andThrow(new LockTimeoutException());

            /** @var \Mockery\Expectation $e2 */
            $e2 = $mock->shouldReceive('processDueMissionEventsForMission');
            $e2->once()->with(Mockery::on(fn (FleetMission $m) => $m->id === $missionAtB->id));
        });

        $processed = $service->processMissedMissionEvents();

        // Only B counted as processed; A was skipped due to lock timeout.
        $this->assertSame(1, $processed, 'Scheduler should skip locked destinations and count only those successfully processed.');
    }

    public function testQueueJobCatchesLockTimeoutAndDoesNotThrow(): void
    {
        $this->basicSetup();

        $baseArrival = (int) now()->timestamp - 1;
        $mission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 100);

        // Simulate a long-running battle holding the destination lock.
        /** @var FleetMissionService $service */
        $service = $this->mock(FleetMissionService::class, function (MockInterface $mock) {
            /** @var \Mockery\Expectation $e */
            $e = $mock->shouldReceive('processDueMissionEventsForMissionId');
            $e->once()->andThrow(new LockTimeoutException());
        });

        $job = new ProcessFleetArrival($mission->id);
        $job->handle($service);

        // If we reach here the exception was caught — the job does not re-throw.
        // The mission stays unprocessed; the queue runner will retry the job automatically.
        $mission->refresh();
        $this->assertSame(0, $mission->processed, 'Mission should remain unprocessed when destination lock timed out.');
    }

    public function testSchedulerFallbackProcessesOverdueMissionBacklog(): void
    {
        $this->basicSetup();

        $this->sendMissionToSecondPlanet($this->createCargoUnits(1), new Resources(1000, 500, 250, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->latest('id')
            ->firstOrFail();

        $this->travelTo(Date::createFromTimestamp($mission->time_arrival + 1));

        $this->artisan('ogamex:scheduler:process-fleet-arrivals');

        $mission->refresh();

        $this->assertSame(1, $mission->processed, 'Fallback scheduler did not process the overdue arrival.');
        $this->assertDatabaseHas('fleet_missions', [
            'parent_id' => $mission->id,
            'processed' => 0,
        ]);
    }

    private function createCargoUnits(int $amount): UnitCollection
    {
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), $amount);

        return $units;
    }

    private function createDueMission(int $arrivalTime, int $arrivalTimeMs, PlanetService|null $destination = null): FleetMission
    {
        $destination ??= $this->secondPlanetService;

        $mission = new FleetMission();
        $mission->user_id = $this->currentUserId;
        $mission->planet_id_from = $this->planetService->getPlanetId();
        $mission->planet_id_to = $destination->getPlanetId();
        $mission->galaxy_from = $this->planetService->getPlanetCoordinates()->galaxy;
        $mission->system_from = $this->planetService->getPlanetCoordinates()->system;
        $mission->position_from = $this->planetService->getPlanetCoordinates()->position;
        $mission->galaxy_to = $destination->getPlanetCoordinates()->galaxy;
        $mission->system_to = $destination->getPlanetCoordinates()->system;
        $mission->position_to = $destination->getPlanetCoordinates()->position;
        $mission->type_from = PlanetType::Planet->value;
        $mission->type_to = PlanetType::Planet->value;
        $mission->mission_type = 3;
        $mission->time_departure = $arrivalTime - 60;
        $mission->time_arrival = $arrivalTime;
        $mission->time_arrival_ms = $arrivalTimeMs;
        $mission->processed = 0;
        $mission->canceled = 0;
        $mission->large_cargo = 1;
        $mission->metal = 0;
        $mission->crystal = 0;
        $mission->deuterium = 0;
        $mission->saveQuietly();

        return $mission;
    }
}
