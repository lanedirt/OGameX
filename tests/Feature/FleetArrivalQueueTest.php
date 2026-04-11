<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
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

    protected function basicSetup(): void
    {
        $this->planetAddUnit('large_cargo', 5);
        $this->planetAddResources(new Resources(100000, 100000, 100000, 0));

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed_peaceful', 1);
    }

    public function testDispatchStoresMillisecondArrivalAndQueuesDelayedJob(): void
    {
        $this->travelTo(Carbon::create(2024, 1, 1, 0, 0, 0)->addMilliseconds(123));
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

    public function testSameDestinationMissionsProcessInMillisecondOrder(): void
    {
        $this->basicSetup();

        $baseArrival = (int) now()->timestamp - 1;
        $firstMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 101);
        $secondMission = $this->createDueMission($baseArrival, ($baseArrival * 1000) + 909);

        /** @var FleetMissionService $service */
        $service = $this->partialMock(FleetMissionService::class, function (MockInterface $mock) use ($firstMission, $secondMission) {
            $mock->shouldReceive('updateMission')
                ->once()
                ->ordered()
                ->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $firstMission->id));

            $mock->shouldReceive('updateMission')
                ->once()
                ->ordered()
                ->with(Mockery::on(fn (FleetMission $mission) => $mission->id === $secondMission->id));
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

    public function testSchedulerFallbackProcessesOverdueMissionBacklog(): void
    {
        $this->basicSetup();

        $this->sendMissionToSecondPlanet($this->createCargoUnits(1), new Resources(1000, 500, 250, 0));

        $mission = FleetMission::query()
            ->where('user_id', $this->currentUserId)
            ->whereNull('parent_id')
            ->latest('id')
            ->firstOrFail();

        $this->travelTo(Carbon::createFromTimestamp($mission->time_arrival + 1));

        $this->artisan('ogamex:scheduler:process-fleet-arrivals')
            ->assertExitCode(0);

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

    private function createDueMission(int $arrivalTime, int $arrivalTimeMs): FleetMission
    {
        $mission = new FleetMission();
        $mission->user_id = $this->currentUserId;
        $mission->planet_id_from = $this->planetService->getPlanetId();
        $mission->planet_id_to = $this->secondPlanetService->getPlanetId();
        $mission->galaxy_from = $this->planetService->getPlanetCoordinates()->galaxy;
        $mission->system_from = $this->planetService->getPlanetCoordinates()->system;
        $mission->position_from = $this->planetService->getPlanetCoordinates()->position;
        $mission->galaxy_to = $this->secondPlanetService->getPlanetCoordinates()->galaxy;
        $mission->system_to = $this->secondPlanetService->getPlanetCoordinates()->system;
        $mission->position_to = $this->secondPlanetService->getPlanetCoordinates()->position;
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
