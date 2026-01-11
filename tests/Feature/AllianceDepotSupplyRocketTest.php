<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
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
    /** @var array<int> */
    private array $createdPlanetIds = [];

    /**
     * Track created planets for cleanup.
     */
    private function trackPlanet(Planet $planet): void
    {
        $this->createdPlanetIds[] = $planet->id;
    }

    /**
     * Clean up test data after each test to prevent test isolation issues.
     */
    protected function tearDown(): void
    {
        // Remove planets created during this test
        if (!empty($this->createdPlanetIds)) {
            // Delete fleet missions to/from these planets first (foreign key constraints)
            // Delete child missions first (return missions with parent_id)
            FleetMission::whereNotNull('parent_id')
                ->where(function ($query) {
                    $query->whereIn('planet_id_from', $this->createdPlanetIds)
                        ->orWhereIn('planet_id_to', $this->createdPlanetIds);
                })
                ->delete();

            // Then delete parent missions
            FleetMission::whereNull('parent_id')
                ->where(function ($query) {
                    $query->whereIn('planet_id_from', $this->createdPlanetIds)
                        ->orWhereIn('planet_id_to', $this->createdPlanetIds);
                })
                ->delete();

            // Now we can delete the planets
            Planet::whereIn('id', $this->createdPlanetIds)->delete();
        }

        // Remove all buddy relationships to prevent interference with other tests
        \DB::table('buddy_requests')->truncate();

        parent::tearDown();
    }

    /**
     * Test that supply rocket successfully extends hold time.
     *
     * @return void
     * @throws Exception
     */
    public function testSupplyRocketExtendsHoldTime(): void
    {
        // Create a buddy and their planet with Alliance Depot
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 6,
        ]);
        $this->trackPlanet($buddyPlanet);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship first
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet to buddy's planet with 4 hour hold
        $this->planetAddUnit('light_fighter', 10);
        $this->planetAddResources(new Resources(0, 0, 10000, 0)); // Add deuterium for fuel
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $fleetMissionService = app(FleetMissionService::class);
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed (7th parameter)
            4 // 4 hour hold time (8th parameter)
        );

        // Travel to just after arrival time but well before hold expires
        // Fleet arrives at $mission->time_arrival, holds until time_arrival + time_holding
        $arrivalTime = $mission->time_arrival;
        $currentTime = (int)Date::now()->timestamp; // Use Laravel's time, not system time()
        $travelSeconds = $arrivalTime - $currentTime + 1; // Travel to 1 second after arrival
        $this->travel($travelSeconds)->seconds();

        // Process the mission so it creates the return mission
        $fleetMissionService->updateMission($mission);

        // Store original planet ID for finding the mission
        $originalPlanetId = $this->planetService->getPlanetId();

        // Switch to buddy user and their planet (they need to send the supply rocket)
        $this->be($buddyUser);
        $this->planetService = $buddyPlanetService;

        // Give buddy Alliance Depot and deuterium
        $buddyPlanetService->setObjectLevel(34, 2); // 34 = alliance_depot
        $buddyPlanetService->addResources(new Resources(0, 0, 50000, 0));

        // Find the outbound mission (parent mission, not return)
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $originalPlanetId)
            ->where('planet_id_to', $buddyPlanetService->getPlanetId())
            ->whereNull('parent_id')
            ->first();

        $this->assertNotNull($outboundMission, 'Outbound mission should exist');

        // Get deuterium before (from buddy's planet now, reload to get current values)
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanetService->getPlanetId(), true);
        $deuteriumBefore = $buddyPlanetService->deuterium()->get();

        // Buddy sends supply rocket to extend hold time by 2 hours
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => $outboundMission->id,
            'extension_hours' => 2,
        ]);

        // Assert success
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert deuterium was deducted from buddy's planet (10 light fighters * 2 deut/hour * 2 hours = 40)
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanetService->getPlanetId(), true);
        $deuteriumAfter = $buddyPlanetService->deuterium()->get();
        $this->assertEquals(40, $deuteriumBefore - $deuteriumAfter, 'Should deduct 40 deuterium from buddy planet');

        // Assert hold time was extended by 2 hours (7200 seconds)
        // Reload the outbound mission to get updated time_holding
        $outboundMission->refresh();
        $this->assertEquals(14400 + 7200, $outboundMission->time_holding, 'Hold time should be extended from 4 hours to 6 hours (21600 seconds)');
    }

    /**
     * Test that supply rocket fails when not enough deuterium.
     *
     * @return void
     * @throws Exception
     */
    public function testSupplyRocketFailsWithoutDeuterium(): void
    {
        // Create a buddy and their planet with Alliance Depot but minimal deuterium
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 7,
        ]);
        $this->trackPlanet($buddyPlanet);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet
        $this->planetAddUnit('cruiser', 5);
        $this->planetAddResources(new Resources(0, 0, 10000, 0)); // Add deuterium for fuel
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('cruiser'), 5);

        $fleetMissionService = app(FleetMissionService::class);
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed (7th parameter)
            2 // 2 hour hold time (8th parameter)
        );

        // Travel to just after arrival time but well before hold expires
        $arrivalTime = $mission->time_arrival;
        $currentTime = (int)Date::now()->timestamp; // Use Laravel's time, not system time()
        $travelSeconds = $arrivalTime - $currentTime + 1; // Travel to 1 second after arrival
        $this->travel($travelSeconds)->seconds();

        // Process the mission so it creates the return mission
        $fleetMissionService->updateMission($mission);

        // Store original planet ID for finding the mission
        $originalPlanetId = $this->planetService->getPlanetId();

        // Switch to buddy user and their planet (they need to send the supply rocket)
        $this->be($buddyUser);
        $this->planetService = $buddyPlanetService;

        // Give buddy Alliance Depot but minimal deuterium
        $buddyPlanetService->setObjectLevel(34, 1); // 34 = alliance_depot
        $buddyPlanetService->addResources(new Resources(0, 0, 10, 0));

        // Find the outbound mission (parent mission, not return)
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $originalPlanetId)
            ->where('planet_id_to', $buddyPlanetService->getPlanetId())
            ->whereNull('parent_id')
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
     * @throws Exception
     */
    public function testSupplyRocketFailsForShortHold(): void
    {
        // Create a buddy and their planet with Alliance Depot
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 9,
        ]);
        $this->trackPlanet($buddyPlanet);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        // Add buddy relationship
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet with SHORT hold time (30 minutes)
        $this->planetAddUnit('light_fighter', 10);
        $this->planetAddResources(new Resources(0, 0, 10000, 0)); // Add deuterium for fuel
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $fleetMissionService = app(FleetMissionService::class);

        // Create mission with 0 hour hold (30 minutes minimum from game logic)
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed (7th parameter)
            0 // 0 hour hold (will be minimum duration) (8th parameter)
        );

        // Travel to just after arrival time
        $arrivalTime = $mission->time_arrival;
        $currentTime = (int)Date::now()->timestamp; // Use Laravel's time, not system time()
        $travelSeconds = $arrivalTime - $currentTime + 60; // Arrive + 1 minute
        $this->travel($travelSeconds)->seconds();

        // Store original planet ID for finding the mission
        $originalPlanetId = $this->planetService->getPlanetId();

        // Switch to buddy user and their planet (they need to send the supply rocket)
        $this->be($buddyUser);
        $this->planetService = $buddyPlanetService;

        // Give buddy Alliance Depot and deuterium
        $buddyPlanetService->setObjectLevel(34, 1); // 34 = alliance_depot
        $buddyPlanetService->addResources(new Resources(0, 0, 10000, 0));

        // Find the outbound mission (parent mission, not return)
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $originalPlanetId)
            ->where('planet_id_to', $buddyPlanetService->getPlanetId())
            ->whereNull('parent_id')
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
     * @throws Exception
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

    /**
     * Test that ACS Defend fleet can be recalled during hold time after extension.
     *
     * @return void
     * @throws Exception
     */
    public function testAcsDefendCanBeRecalledDuringExtendedHoldTime(): void
    {
        // Create buddy relationship with nearby planet
        // Use a far-away location to avoid conflicts with other tests
        $buddyUser = User::factory()->create();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => 4,  // Different galaxy to avoid conflicts
            'system' => 150,
            'planet' => 12,
        ]);
        $this->trackPlanet($buddyPlanet);
        $buddyPlanetService = $planetServiceFactory->make($buddyPlanet->id, true);

        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        // Send an ACS Defend fleet with 4 hour hold time (use large cargo for fuel capacity)
        $this->planetAddUnit('large_cargo', 5);
        $this->planetAddResources(new Resources(0, 0, 50000, 0)); // Add plenty of deuterium for fuel
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('large_cargo'), 5);

        $fleetMissionService = app(FleetMissionService::class);
        $mission = $fleetMissionService->createNewFromPlanet(
            $this->planetService,
            $buddyPlanetService->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend
            $units,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            4 // 4 hour hold time
        );

        // Travel to just after arrival time
        $arrivalTime = $mission->time_arrival;
        $currentTime = (int)Date::now()->timestamp;
        $travelSeconds = $arrivalTime - $currentTime + 60; // Arrive + 1 minute
        $this->travel($travelSeconds)->seconds();

        // Store original planet ID
        $originalPlanetId = $this->planetService->getPlanetId();

        // Switch to buddy user and extend hold time
        $this->be($buddyUser);
        $buddyPlanetService->setObjectLevel(34, 2); // Alliance Depot level 2
        $buddyPlanetService->addResources(new Resources(0, 0, 50000, 0));

        // Find the outbound mission
        $outboundMission = FleetMission::where('mission_type', 5)
            ->where('planet_id_from', $originalPlanetId)
            ->where('planet_id_to', $buddyPlanetService->getPlanetId())
            ->whereNull('parent_id')
            ->first();

        $this->assertNotNull($outboundMission, 'Outbound mission should exist');

        // Extend hold time by 2 hours
        $response = $this->post('/ajax/alliance-depot/send-supply-rocket', [
            'fleet_mission_id' => $outboundMission->id,
            'extension_hours' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Reload mission to get updated hold time
        $outboundMission->refresh();
        $this->assertEquals(14400 + 7200, $outboundMission->time_holding, 'Hold time should be extended to 6 hours');

        // Switch back to original user (fleet owner)
        $this->be(User::find($this->currentUserId));

        // Try to recall the fleet (should succeed)
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $outboundMission->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify the mission was actually recalled (canceled flag set)
        $outboundMission->refresh();
        $this->assertEquals(1, $outboundMission->canceled, 'Mission should be canceled');
    }
}
