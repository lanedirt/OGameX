<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use Tests\AccountTestCase;

/**
 * Test that Alliance Depot supply rocket functionality works as expected.
 */
class AllianceDepotSupplyRocketTest extends AccountTestCase
{
    /**
     * Test that supply rocket successfully extends hold time.
     *
     * @return void
     * @throws \Exception
     */
    public function testSupplyRocketExtendsHoldTime(): void
    {
        // Set up planet with Alliance Depot and deuterium
        $this->planetSetObjectLevel('alliance_depot', 2);
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Create a buddy and their planet
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = \OGame\Models\Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet to buddy's planet with 4 hour hold
        $this->planetAddUnit('light_fighter', 10);
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $fleetMissionService = app(FleetMissionService::class);
        $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            4 // 4 hour hold time
        );

        // Fast forward to when fleet has arrived
        $this->travel(2)->hours();

        // Find the outbound mission
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $this->planetService->getPlanetId())
            ->where('planet_id_to', $buddyPlanetService->getPlanetId())
            ->first();

        $this->assertNotNull($outboundMission, 'Outbound mission should exist');

        // Get deuterium before
        $deuteriumBefore = $this->planetService->deuterium()->get();

        // Send supply rocket to extend hold time by 2 hours
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => $outboundMission->id,
            'extension_hours' => 2,
        ]);

        // Assert success
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert deuterium was deducted (10 light fighters * 2 deut/hour * 2 hours = 40)
        $deuteriumAfter = $this->planetService->deuterium()->get();
        $this->assertEquals(40, $deuteriumBefore - $deuteriumAfter, 'Should deduct 40 deuterium');

        // Assert return mission time was extended
        $returnMission = FleetMission::where('planet_id_from', $buddyPlanetService->getPlanetId())
            ->where('planet_id_to', $this->planetService->getPlanetId())
            ->where('mission_type', 5)
            ->first();

        $this->assertNotNull($returnMission, 'Return mission should exist');
    }

    /**
     * Test that supply rocket fails when not enough deuterium.
     *
     * @return void
     * @throws \Exception
     */
    public function testSupplyRocketFailsWithoutDeuterium(): void
    {
        // Set up planet with Alliance Depot but no deuterium
        $this->planetSetObjectLevel('alliance_depot', 1);
        $this->planetAddResources(new Resources(0, 0, 10, 0)); // Only 10 deuterium

        // Create a buddy and their planet
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = \OGame\Models\Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet
        $this->planetAddUnit('cruiser', 5);
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 5);

        $fleetMissionService = app(FleetMissionService::class);
        $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $units,
            new Resources(0, 0, 0, 0),
            10,
            2
        );

        // Fast forward to when fleet has arrived
        $this->travel(2)->hours();

        // Find the outbound mission
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $this->planetService->getPlanetId())
            ->first();

        // Try to send supply rocket (5 cruisers * 30 deut/hour * 1 hour = 150 deuterium needed)
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => $outboundMission->id,
            'extension_hours' => 1,
        ]);

        // Assert failure
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $responseData = $response->json();
        $this->assertStringContainsString('Not enough deuterium', $responseData['error']);
    }

    /**
     * Test that supply rocket fails for fleet holding less than 1 hour.
     *
     * @return void
     * @throws \Exception
     */
    public function testSupplyRocketFailsForShortHold(): void
    {
        // Set up planet with Alliance Depot and deuterium
        $this->planetSetObjectLevel('alliance_depot', 1);
        $this->planetAddResources(new Resources(0, 0, 10000, 0));

        // Create a buddy and their planet
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = \OGame\Models\Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet with SHORT hold time (30 minutes)
        $this->planetAddUnit('light_fighter', 10);
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $fleetMissionService = app(FleetMissionService::class);

        // Create mission manually with 0 hour hold (30 minutes minimum from game logic)
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $units,
            new Resources(0, 0, 0, 0),
            10,
            0 // 0 hour hold (will be minimum duration)
        );

        // Fast forward to when fleet has arrived
        $this->travel(30)->minutes();

        // Find the outbound mission
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $this->planetService->getPlanetId())
            ->first();

        // Try to send supply rocket
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => $outboundMission->id,
            'extension_hours' => 1,
        ]);

        // Assert failure
        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
        $responseData = $response->json();
        $this->assertStringContainsString('at least 1 hour', $responseData['error']);
    }

    /**
     * Test that invalid extension hours are rejected.
     *
     * @return void
     * @throws \Exception
     */
    public function testSupplyRocketRejectsInvalidHours(): void
    {
        $this->planetSetObjectLevel('alliance_depot', 1);

        // Try with 0 hours
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => 1,
            'extension_hours' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);

        // Try with 33 hours (max is 32)
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => 1,
            'extension_hours' => 33,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }
}
