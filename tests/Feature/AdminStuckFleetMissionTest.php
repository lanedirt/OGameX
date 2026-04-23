<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\User;
use Tests\AccountTestCase;

class AdminStuckFleetMissionTest extends AccountTestCase
{
    /** @var array<int, int> */
    private array $createdUserIds = [];

    /** @var array<int, int> */
    private array $createdMissionIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdMissionIds)) {
            FleetMission::whereIn('parent_id', $this->createdMissionIds)->delete();
            FleetMission::whereIn('id', $this->createdMissionIds)->delete();
        }

        if (!empty($this->createdUserIds)) {
            $planetIds = Planet::whereIn('user_id', $this->createdUserIds)->pluck('id')->all();

            if (!empty($planetIds)) {
                FleetMission::where(function ($query) use ($planetIds) {
                    $query->whereIn('planet_id_from', $planetIds)
                        ->orWhereIn('planet_id_to', $planetIds);
                })->delete();
            }

            Message::whereIn('user_id', $this->createdUserIds)->delete();
            DB::table('users_tech')->whereIn('user_id', $this->createdUserIds)->delete();
            Planet::whereIn('user_id', $this->createdUserIds)->delete();
            User::whereIn('id', $this->createdUserIds)->delete();
        }

        parent::tearDown();
    }

    public function testAdminCanSeeBrokenOverdueMissionInServerAdministration(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $victim = $this->createTrackedSecondaryUser();
        $returnMission = $this->createBrokenReturnMission($victim['user_id'], $victim['homeworld_id']);

        $response = $this->get(route('admin.server-administration.index'));

        $response->assertStatus(200);
        $response->assertSee('Stuck Fleet Missions');
        $response->assertSee((string) $returnMission->id);
        $response->assertSee('Broken return target');
        $response->assertSee('Recover to Homeworld');
    }

    public function testAdminCanRecoverBrokenReturnMissionToVictimHomeworld(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $victim = $this->createTrackedSecondaryUser();
        $returnMission = $this->createBrokenReturnMission($victim['user_id'], $victim['homeworld_id']);

        $homeworldBefore = Planet::findOrFail($victim['homeworld_id']);

        $response = $this->post(route('admin.server-administration.stuck-missions.recover-homeworld'), [
            'mission_id' => $returnMission->id,
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $returnMission->refresh();
        $homeworldAfter = Planet::findOrFail($victim['homeworld_id']);

        $this->assertSame(1, $returnMission->processed);
        $this->assertSame($homeworldBefore->small_cargo + 3, $homeworldAfter->small_cargo);
        $this->assertEquals($homeworldBefore->metal + 1234, $homeworldAfter->metal);
        $this->assertEquals($homeworldBefore->crystal + 567, $homeworldAfter->crystal);
        $this->assertEquals($homeworldBefore->deuterium + 89, $homeworldAfter->deuterium);
    }

    public function testAdminCanProcessOverdueMissionWithMissingDestination(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $victim = $this->createTrackedSecondaryUser();
        $outboundMission = $this->createMissingDestinationOutboundMission($victim['user_id'], $victim['homeworld_id']);

        $response = $this->post(route('admin.server-administration.stuck-missions.process'), [
            'mission_id' => $outboundMission->id,
        ]);

        $response->assertRedirect(route('admin.server-administration.index'));
        $response->assertSessionHas('status');

        $outboundMission->refresh();
        $returnMission = FleetMission::where('parent_id', $outboundMission->id)->first();

        $this->assertSame(1, $outboundMission->processed);
        $this->assertNotNull($returnMission);
        $this->assertSame($victim['homeworld_id'], $returnMission->planet_id_to);
    }

    /**
     * @return array{user_id:int,homeworld_id:int}
     */
    private function createTrackedSecondaryUser(): array
    {
        $adminUserId = $this->currentUserId;
        $adminUsername = $this->currentUsername;
        $adminPlanetId = $this->currentPlanetId;

        $this->createAndLoginUser();

        $victimUserId = $this->currentUserId;
        $victimHomeworldId = $this->planetService->getPlanetId();
        $this->createdUserIds[] = $victimUserId;

        $this->be(User::findOrFail($adminUserId));

        $this->currentUserId = $adminUserId;
        $this->currentUsername = $adminUsername;
        $this->currentPlanetId = $adminPlanetId;

        $adminPlayer = resolve(PlayerServiceFactory::class)->make($adminUserId, true);
        $this->planetService = $adminPlayer->planets->current();
        $allPlanets = $adminPlayer->planets->allPlanets();
        $this->secondPlanetService = $allPlanets[1] ?? null;

        return [
            'user_id' => $victimUserId,
            'homeworld_id' => $victimHomeworldId,
        ];
    }

    private function createBrokenReturnMission(int $userId, int $homeworldId): FleetMission
    {
        $parentMission = $this->createMission([
            'user_id' => $userId,
            'planet_id_from' => $homeworldId,
            'planet_id_to' => $homeworldId,
            'type_from' => PlanetType::Planet->value,
            'type_to' => PlanetType::Moon->value,
            'mission_type' => 3,
            'time_departure' => Date::now()->subMinutes(30)->timestamp,
            'time_arrival' => Date::now()->subMinutes(20)->timestamp,
            'processed' => 1,
        ]);

        return $this->createMission([
            'user_id' => $userId,
            'parent_id' => $parentMission->id,
            'planet_id_from' => null,
            'planet_id_to' => null,
            'type_from' => PlanetType::Moon->value,
            'type_to' => PlanetType::Planet->value,
            'mission_type' => 3,
            'time_departure' => Date::now()->subMinutes(15)->timestamp,
            'time_arrival' => Date::now()->subMinutes(10)->timestamp,
            'small_cargo' => 3,
            'metal' => 1234,
            'crystal' => 567,
            'deuterium' => 89,
        ]);
    }

    private function createMissingDestinationOutboundMission(int $userId, int $homeworldId): FleetMission
    {
        return $this->createMission([
            'user_id' => $userId,
            'planet_id_from' => $homeworldId,
            'planet_id_to' => null,
            'type_from' => PlanetType::Planet->value,
            'type_to' => PlanetType::Moon->value,
            'mission_type' => 3,
            'time_departure' => Date::now()->subMinutes(12)->timestamp,
            'time_arrival' => Date::now()->subMinutes(6)->timestamp,
            'small_cargo' => 2,
        ]);
    }

    /**
     * @param array<string, int|float|string|null> $attributes
     */
    private function createMission(array $attributes): FleetMission
    {
        $mission = new FleetMission();
        $mission->user_id = (int) $attributes['user_id'];
        $mission->parent_id = isset($attributes['parent_id']) ? (int) $attributes['parent_id'] : null;
        $mission->planet_id_from = isset($attributes['planet_id_from']) ? (int) $attributes['planet_id_from'] : null;
        $mission->planet_id_to = isset($attributes['planet_id_to']) ? (int) $attributes['planet_id_to'] : null;
        $mission->galaxy_from = isset($attributes['galaxy_from']) ? (int) $attributes['galaxy_from'] : 1;
        $mission->system_from = isset($attributes['system_from']) ? (int) $attributes['system_from'] : 1;
        $mission->position_from = isset($attributes['position_from']) ? (int) $attributes['position_from'] : 1;
        $mission->galaxy_to = isset($attributes['galaxy_to']) ? (int) $attributes['galaxy_to'] : 1;
        $mission->system_to = isset($attributes['system_to']) ? (int) $attributes['system_to'] : 1;
        $mission->position_to = isset($attributes['position_to']) ? (int) $attributes['position_to'] : 1;
        $mission->type_from = isset($attributes['type_from']) ? (int) $attributes['type_from'] : PlanetType::Planet->value;
        $mission->type_to = isset($attributes['type_to']) ? (int) $attributes['type_to'] : PlanetType::Planet->value;
        $mission->mission_type = isset($attributes['mission_type']) ? (int) $attributes['mission_type'] : 3;
        $mission->time_departure = isset($attributes['time_departure']) ? (int) $attributes['time_departure'] : (int) Date::now()->subMinutes(10)->timestamp;
        $mission->time_arrival = isset($attributes['time_arrival']) ? (int) $attributes['time_arrival'] : (int) Date::now()->subMinutes(5)->timestamp;
        $mission->processed = isset($attributes['processed']) ? (int) $attributes['processed'] : 0;
        $mission->processed_hold = 0;
        $mission->canceled = 0;
        $mission->metal = isset($attributes['metal']) ? (float) $attributes['metal'] : 0;
        $mission->crystal = isset($attributes['crystal']) ? (float) $attributes['crystal'] : 0;
        $mission->deuterium = isset($attributes['deuterium']) ? (float) $attributes['deuterium'] : 0;
        $mission->deuterium_consumption = 0;
        $mission->small_cargo = isset($attributes['small_cargo']) ? (int) $attributes['small_cargo'] : 0;
        $mission->save();

        $this->createdMissionIds[] = $mission->id;

        return $mission;
    }
}
