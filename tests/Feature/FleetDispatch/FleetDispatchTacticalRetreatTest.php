<?php

namespace Tests\Feature\FleetDispatch;

use OGame\GameMissions\AttackMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Feature coverage for tactical retreat during attack missions.
 */
class FleetDispatchTacticalRetreatTest extends FleetDispatchTestCase
{
    protected int $missionType = 1;

    protected string $missionName = 'Attack';

    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 200);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddResources(new Resources(5000, 5000, 1000000, 0));
    }

    public function testTacticalRetreatPersistsRatioAndFleeInBattleReport(): void
    {
        $this->basicSetup();

        $foreignPlanet = $this->getNearbyForeignPlanet();
        $fightersBefore = $foreignPlanet->getObjectAmount('light_fighter');
        $foreignPlanet->addUnit('light_fighter', 5);
        $foreignPlanet->addUnit('rocket_launcher', 20);
        $foreignPlanet->addResources(new Resources(0, 0, 100000, 0));

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $this->dispatchFleet(
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $duration = $fleetMissionService->calculateFleetMissionDuration(
            $this->planetService,
            $foreignPlanet->getPlanetCoordinates(),
            $unitCollection,
            resolve(AttackMission::class)
        );

        $this->travel($duration + 1)->seconds();
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        $report = BattleReport::query()->orderByDesc('id')->first();
        $this->assertNotNull($report, 'Expected a battle report after the attack');

        $general = $report->general;
        $this->assertIsArray($general);
        $this->assertArrayHasKey('tactical_retreat', $general);
        $this->assertIsArray($general['tactical_retreat']);
        $this->assertTrue($general['tactical_retreat']['defender_fled']);
        $this->assertGreaterThanOrEqual(5, $general['tactical_retreat']['ratio']);
        $this->assertEquals('defender', $general['tactical_retreat']['by']);

        // Fleeing ships remain on the defender planet.
        $foreignPlanet->reloadPlanet();
        $this->assertEquals($fightersBefore + 5, $foreignPlanet->getObjectAmount('light_fighter'));
    }

    public function testTacticalRetreatPreferenceCanBeUpdated(): void
    {
        $response = $this->post('/ajax/fleet/tactical-retreat', [
            'tacticalRetreatState' => 0,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'tacticalRetreatRatio' => 0]);

        $this->planetPlayer()->getUser()->refresh();
        $this->assertEquals(0, $this->planetPlayer()->getUser()->tactical_retreat_ratio);
    }

    public function testRetreatAfterDefenderRetreatIsStoredOnMission(): void
    {
        $this->basicSetup();

        $foreignPlanet = $this->getNearbyForeignPlanet();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $unitsArray = [];
        foreach ($unitCollection->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        $coordinates = $foreignPlanet->getPlanetCoordinates();
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', [
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
            'position' => $coordinates->position,
            'type' => PlanetType::Planet->value,
            'mission' => $this->missionType,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            '_token' => csrf_token(),
            'holdingtime' => 0,
            'speed' => 10,
            'retreatAfterDefenderRetreat' => 1,
            ...$unitsArray,
        ]);

        $post->assertStatus(200);
        $post->assertJson(['success' => true]);

        $mission = \OGame\Models\FleetMission::query()->orderByDesc('id')->first();
        $this->assertNotNull($mission);
        $this->assertTrue((bool)$mission->retreat_after_defender_retreat);
    }

    private function planetPlayer(): \OGame\Services\PlayerService
    {
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Planet has no player.');
        }

        return $player;
    }
}
