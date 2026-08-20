<?php

namespace Tests\Feature;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\Expectation;
use Mockery\MockInterface;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Jobs\ProcessFleetArrival;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\SettingsService;
use RuntimeException;
use Tests\FleetDispatchTestCase;

class FleetArrivalQueueTest extends FleetDispatchTestCase
{
    protected int $missionType = 3;

    protected string $missionName = 'Transport';

    /**
     * @var array<int> Buddy user IDs created by ACS Defend tests, cleaned up in tearDown.
     */
    private array $createdBuddyUserIds = [];

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

        foreach ($this->createdBuddyUserIds as $buddyUserId) {
            DB::table('buddy_requests')
                ->where('sender_user_id', $buddyUserId)
                ->orWhere('receiver_user_id', $buddyUserId)
                ->delete();
            Message::where('user_id', $buddyUserId)->delete();
            DB::table('users_tech')->where('user_id', $buddyUserId)->delete();
            DB::table('users')->where('id', $buddyUserId)->update(['planet_current' => null]);
            Planet::where('user_id', $buddyUserId)->delete();
            User::where('id', $buddyUserId)->delete();
        }
        $this->createdBuddyUserIds = [];

        DB::table('jobs')->delete();

        config(['queue.default' => 'sync']);

        parent::tearDown();
    }

    /**
     * Create a buddy player with a planet the current user can send ACS Defend missions to.
     */
    private function createBuddyTargetPlanet(): PlanetService
    {
        $buddyUser = User::factory()->create();
        $this->createdBuddyUserIds[] = $buddyUser->id;

        $buddyPlanet = $this->createPlanetAtSafeCoordinate($buddyUser->id);

        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        return $buddyPlanet;
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

    public function testCombatMissionsRouteToHeavyLaneAndLogisticsRouteToLightLane(): void
    {
        $service = resolve(FleetMissionService::class);

        // Attack (1), ACS Attack (2), ACS Defend (5), Espionage (6) and Moon Destruction (9)
        // can run or batch a large battle at a contested destination: heavy lane.
        foreach ([1, 2, 5, 6, 9] as $type) {
            $mission = new FleetMission();
            $mission->mission_type = $type;
            $mission->parent_id = null;

            $this->assertSame(
                FleetMissionService::ARRIVAL_QUEUE_NAME_HEAVY,
                $service->arrivalQueueForMission($mission),
                "Outbound combat mission type {$type} must route to the heavy lane."
            );
        }

        // Transport (3), Deployment (4), Colonisation (7), Recycle (8), Missile (10) and
        // Expedition (15, bounded combat at an uncontested position) use the light lane.
        foreach ([3, 4, 7, 8, 10, 15] as $type) {
            $mission = new FleetMission();
            $mission->mission_type = $type;
            $mission->parent_id = null;

            $this->assertSame(
                FleetMissionService::ARRIVAL_QUEUE_NAME,
                $service->arrivalQueueForMission($mission),
                "Logistics mission type {$type} must route to the light lane."
            );
        }
    }

    public function testReturnMissionsAlwaysRouteToLightLane(): void
    {
        $service = resolve(FleetMissionService::class);

        // A return carries its outbound mission_type but only delivers resources home,
        // so even a returning attack (type 1) must use the light lane.
        $mission = new FleetMission();
        $mission->mission_type = 1;
        $mission->parent_id = 4242;

        $this->assertSame(
            FleetMissionService::ARRIVAL_QUEUE_NAME,
            $service->arrivalQueueForMission($mission),
            'Return missions never run a battle and must use the light lane.'
        );
    }

    public function testDispatchedTransportIsQueuedOnLightLane(): void
    {
        $this->basicSetup();
        $this->sendMissionToSecondPlanet($this->createCargoUnits(1), new Resources(1000, 500, 250, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->latest('id')
            ->firstOrFail();

        $this->assertNotNull($mission->arrival_job_id, 'Transport did not store a delayed arrival job ID.');

        $queue = DB::table('jobs')->where('id', $mission->arrival_job_id)->value('queue');
        $this->assertSame(
            FleetMissionService::ARRIVAL_QUEUE_NAME,
            $queue,
            'A dispatched transport must be queued on the light lane.'
        );
    }

    public function testDispatchedAttackIsQueuedOnHeavyLane(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('light_fighter', 5);

        // Dispatch a real attack (mission type 1) at a foreign planet.
        $this->missionType = 1;
        $attackUnits = new UnitCollection();
        $attackUnits->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->sendMissionToOtherPlayerPlanet($attackUnits, new Resources(0, 0, 0, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->where('mission_type', 1)
            ->latest('id')
            ->firstOrFail();

        $this->assertNotNull($mission->arrival_job_id, 'Attack did not store a delayed arrival job ID.');

        $queue = DB::table('jobs')->where('id', $mission->arrival_job_id)->value('queue');
        $this->assertSame(
            FleetMissionService::ARRIVAL_QUEUE_NAME_HEAVY,
            $queue,
            'A dispatched attack must be queued on the heavy lane.'
        );
    }

    public function testJobHandlerProcessesAllDueMissionsAtDestinationInMillisecondOrder(): void
    {
        $this->basicSetup();

        $baseArrival = (int) now()->timestamp - 1;
        // Create the later-ms mission FIRST so the earlier-ms mission has the HIGHER id.
        // The processing order (time_arrival, time_arrival_ms, id) then runs counter to
        // id order, so the id tiebreaker cannot mask a missing time_arrival_ms sort.
        $secondMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 900);
        $firstMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 100);

        // The job is dispatched for the LATER mission only. When handled it must
        // discover and process the earlier mission too, in ms order, because both
        // share the same destination — the handler is destination-scoped, not
        // mission-scoped.
        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($firstMission, $secondMission) {
            /** @var Expectation $e1 */
            $e1 = $mock->shouldReceive('updateMission');
            $e1->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $firstMission->id));

            /** @var Expectation $e2 */
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
        // Create the later-ms mission FIRST so the earlier-ms mission has the HIGHER id.
        // This ensures the id tiebreaker cannot mask a missing time_arrival_ms sort.
        $secondMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 909);
        $firstMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 101);

        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($firstMission, $secondMission) {
            /** @var Expectation $e1 */
            $e1 = $mock->shouldReceive('updateMission');
            $e1->once()->ordered()->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $firstMission->id));

            /** @var Expectation $e2 */
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
            /** @var Expectation $e1 */
            $e1 = $mock->shouldReceive('processDueMissionEventsForMission');
            $e1->once()->with(Mockery::on(fn (FleetMission $m) => $m->id === $missionAtA->id))->andThrow(new LockTimeoutException());

            /** @var Expectation $e2 */
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
            /** @var Expectation $e */
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

    public function testSchedulerFallbackSkipsFailedDestinationAndContinues(): void
    {
        $this->basicSetup();

        // Only the two missions created below should be seen by processMissedMissionEvents().
        DB::table('fleet_missions')
            ->where('processed', 0)
            ->where('canceled', 0)
            ->where('time_arrival', '<=', (int) now()->timestamp)
            ->delete();

        $baseArrival = (int) now()->timestamp - 1;
        $missionAtA = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 100);
        $missionAtB = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 200, $this->planetService);

        // Destination A throws a non-lock error (e.g. a broken/poison mission). It must not
        // abort the whole catch-up run: B still gets processed.
        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($missionAtA, $missionAtB) {
            /** @var Expectation $e1 */
            $e1 = $mock->shouldReceive('processDueMissionEventsForMission');
            $e1->once()->with(Mockery::on(fn (FleetMission $m) => $m->id === $missionAtA->id))->andThrow(new RuntimeException('boom'));

            /** @var Expectation $e2 */
            $e2 = $mock->shouldReceive('processDueMissionEventsForMission');
            $e2->once()->with(Mockery::on(fn (FleetMission $m) => $m->id === $missionAtB->id));
        });

        $processed = $service->processMissedMissionEvents();

        $this->assertSame(1, $processed, 'A failing destination must be skipped while others still process.');
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

    public function testAcsDefendSchedulesHoldJobAtPhysicalArrivalAndArrivalJobAtHoldExpiry(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('light_fighter', 10);

        $buddyPlanet = $this->createBuddyTargetPlanet();

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $service = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $mission = $service->createNewFromPlanet(
            $this->planetService,
            $buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend mission type
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        $mission->refresh();

        $this->assertNotNull($mission->time_holding, 'ACS Defend mission must carry a hold time.');
        $this->assertGreaterThan(0, $mission->time_holding, 'ACS Defend mission must carry a positive hold time.');
        $this->assertNotNull($mission->hold_job_id, 'ACS Defend mission did not store a hold job ID.');
        $this->assertNotNull($mission->arrival_job_id, 'ACS Defend mission did not store an arrival job ID.');

        // The hold job must fire at the physical arrival time (time_arrival - time_holding),
        // NOT at hold expiry: it triggers the hold-start event (processed_hold + arrival messages).
        $holdAvailableAt = DB::table('jobs')->where('id', $mission->hold_job_id)->value('available_at');
        $this->assertSame(
            (int) ($mission->time_arrival - $mission->time_holding),
            (int) $holdAvailableAt,
            'Hold job must be scheduled at the physical arrival time (time_arrival - time_holding).'
        );

        // The completion job fires at time_arrival, which for ACS Defend already includes the hold.
        $arrivalAvailableAt = DB::table('jobs')->where('id', $mission->arrival_job_id)->value('available_at');
        $this->assertSame(
            (int) $mission->time_arrival,
            (int) $arrivalAvailableAt,
            'Arrival job must be scheduled at the mission completion time (time_arrival).'
        );
    }

    public function testSchedulerFallbackProcessesOverdueAcsDefendHoldArrival(): void
    {
        $this->basicSetup();
        $this->planetAddUnit('light_fighter', 10);

        // Remove pre-existing overdue missions — both overdue completions (Branch B) and
        // overdue ACS Defend hold arrivals (Branch A) — left behind by previous runs
        // against the shared dev database. Without this, the limit(100) catch-up batch
        // fills up before it reaches the mission created below.
        DB::table('fleet_missions')
            ->where('processed', 0)
            ->where('canceled', 0)
            ->where('time_arrival', '<=', (int) now()->timestamp)
            ->delete();
        DB::table('fleet_missions')
            ->where('canceled', 0)
            ->where('mission_type', 5)
            ->where('processed_hold', 0)
            ->whereNotNull('time_holding')
            ->where('time_holding', '>', 0)
            ->whereRaw('(time_arrival - time_holding) <= ?', [(int) now()->timestamp])
            ->delete();

        $buddyPlanet = $this->createBuddyTargetPlanet();
        $buddyUserId = $buddyPlanet->getPlayer()?->getId();
        $this->assertNotNull($buddyUserId, 'Buddy planet has no player.');

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $service = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $mission = $service->createNewFromPlanet(
            $this->planetService,
            $buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend mission type
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Rewind the mission so its physical arrival (time_arrival - time_holding) is 60s
        // overdue while its completion (time_arrival) is still far in the future: only the
        // scheduler's Branch A (ACS hold arrival) matches it.
        $holdSeconds = (int) $mission->time_holding;
        $mission->time_arrival = (int) now()->timestamp + $holdSeconds - 60;
        $mission->time_arrival_ms = $mission->time_arrival * 1000;
        $mission->saveQuietly(); // Skip the observer so no delayed jobs are re-synced.

        // Simulate the delayed jobs being lost (e.g. downtime or a purged queue): the
        // scheduler fallback must fire the hold arrival on its own.
        DB::table('jobs')->delete();
        $mission->forceFill(['arrival_job_id' => null, 'hold_job_id' => null])->saveQuietly();

        $this->artisan('ogamex:scheduler:process-fleet-arrivals');

        $mission->refresh();
        $this->assertSame(1, $mission->processed_hold, 'Scheduler fallback must process the overdue ACS Defend hold arrival.');
        $this->assertSame(0, $mission->processed, 'Mission completion is still in the future and must not be processed.');

        $this->assertTrue(
            Message::where('user_id', $this->currentUserId)->where('key', 'acs_defend_arrival_sender')->exists(),
            'ACS Defend arrival message must be sent to the fleet sender.'
        );
        $this->assertTrue(
            Message::where('user_id', $buddyUserId)->where('key', 'acs_defend_arrival_host')->exists(),
            'ACS Defend arrival message must be sent to the host player.'
        );
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
        if ($destination === null) {
            $this->fail('Destination planet service not initialised.');
        }

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
