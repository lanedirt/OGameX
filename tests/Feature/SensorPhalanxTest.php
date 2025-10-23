<?php

namespace Tests\Feature;

use Tests\AccountTestCase;
use OGame\Models\Resources;
use OGame\Models\FleetMission;
use OGame\Factories\PlanetServiceFactory;
use Illuminate\Support\Facades\DB;

/**
 * Test that the sensor phalanx functionality works as expected.
 */
class SensorPhalanxTest extends AccountTestCase
{
    /**
     * Test that a moon can build a sensor phalanx.
     */
    public function testMoonCanBuildSensorPhalanx(): void
    {
        // Create a moon for the player
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($this->planetService);

        // Give moon enough resources to build phalanx
        $moon->addResources(new Resources(20000, 40000, 20000, 0));

        // Build lunar base first (required)
        $this->planetSetObjectLevel('lunar_base', 1);
        $moon->setObjectLevel(41, 1, true); // lunar_base ID

        // Switch to moon
        $response = $this->get('/overview?cp=' . $moon->getPlanetId());
        $response->assertStatus(200);

        // Try to build sensor phalanx
        $response = $this->post('/facilities/add-buildrequest', [
            '_token' => csrf_token(),
            'technologyId' => 42, // sensor_phalanx ID
        ]);

        // Assert successful
        $response->assertStatus(200);

        // Travel time to allow building to complete
        $this->travel(1)->hours();

        // Verify sensor phalanx is built
        $response = $this->get('/facilities');
        $this->assertObjectLevelOnPage($response, 'sensor_phalanx', 1);
    }

    /**
     * Test that sensor phalanx can detect fleet movements.
     */
    public function testSensorPhalanxDetectsFleetMovements(): void
    {
        // Create a moon with sensor phalanx for the player
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($this->planetService);
        $moon->addResources(new Resources(100000, 100000, 100000, 0));
        $moon->setObjectLevel(41, 1, true); // lunar_base
        $moon->setObjectLevel(42, 5, true); // sensor_phalanx level 5

        // Get a nearby foreign planet to scan
        $targetPlanet = $this->getNearbyForeignPlanet();
        $targetCoords = $targetPlanet->getPlanetCoordinates();

        // Create a fleet mission going to the target planet
        $attackerPlanet = $this->secondPlanetService;
        $attackerPlanet->addUnit('small_cargo', 10);

        // Create a fleet mission manually in the database
        $fleetMission = new FleetMission();
        $fleetMission->user_id = $attackerPlanet->getPlayer()->getId();
        $fleetMission->planet_id_from = $attackerPlanet->getPlanetId();
        $fleetMission->planet_id_to = $targetPlanet->getPlanetId();
        $fleetMission->galaxy_from = $attackerPlanet->getPlanetCoordinates()->galaxy;
        $fleetMission->system_from = $attackerPlanet->getPlanetCoordinates()->system;
        $fleetMission->position_from = $attackerPlanet->getPlanetCoordinates()->position;
        $fleetMission->galaxy_to = $targetCoords->galaxy;
        $fleetMission->system_to = $targetCoords->system;
        $fleetMission->position_to = $targetCoords->position;
        $fleetMission->mission_type = 1; // Attack
        $fleetMission->time_departure = time();
        $fleetMission->time_arrival = time() + 3600; // 1 hour from now
        $fleetMission->small_cargo = 10;
        $fleetMission->processed = 0;
        $fleetMission->save();

        // Now scan the target with phalanx
        $response = $this->post('/ajax/galaxy/phalanx-scan', [
            '_token' => csrf_token(),
            'galaxy' => $targetCoords->galaxy,
            'system' => $targetCoords->system,
            'position' => $targetCoords->position,
        ]);

        // Should return success with fleet information
        $response->assertStatus(200);
        $responseData = $response->json();

        // Verify fleet is detected
        $this->assertTrue($responseData['success'] ?? false, 'Phalanx scan should succeed');
        $this->assertNotEmpty($responseData['fleets'] ?? [], 'Should detect at least one fleet');
    }

    /**
     * Test that sensor phalanx respects range limitations.
     */
    public function testSensorPhalanxRespectsRange(): void
    {
        // Create a moon with sensor phalanx level 1 (range limited)
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($this->planetService);
        $moon->addResources(new Resources(100000, 100000, 100000, 0));
        $moon->setObjectLevel(41, 1, true); // lunar_base
        $moon->setObjectLevel(42, 1, true); // sensor_phalanx level 1 (range: 1 system)

        // Try to scan a target that's too far away
        $moonCoords = $moon->getPlanetCoordinates();
        $farGalaxy = $moonCoords->galaxy;
        $farSystem = ($moonCoords->system + 50) % 499; // Far away system
        $farPosition = 5;

        // Try to scan the far target
        $response = $this->post('/ajax/galaxy/phalanx-scan', [
            '_token' => csrf_token(),
            'galaxy' => $farGalaxy,
            'system' => $farSystem,
            'position' => $farPosition,
        ]);

        // Should fail due to range
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertFalse($responseData['success'] ?? true, 'Phalanx scan should fail due to range');
    }

    /**
     * Test that sensor phalanx requires deuterium.
     */
    public function testSensorPhalanxRequiresDeuterium(): void
    {
        // Create a moon with sensor phalanx but no deuterium
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($this->planetService);
        $moon->setObjectLevel(41, 1, true); // lunar_base
        $moon->setObjectLevel(42, 5, true); // sensor_phalanx level 5

        // Ensure moon has NO deuterium
        $moon->deductResources(new Resources(0, 0, $moon->deuterium()->get(), 0));

        // Get a nearby target to scan
        $targetPlanet = $this->getNearbyForeignPlanet();
        $targetCoords = $targetPlanet->getPlanetCoordinates();

        // Try to scan without deuterium
        $response = $this->post('/ajax/galaxy/phalanx-scan', [
            '_token' => csrf_token(),
            'galaxy' => $targetCoords->galaxy,
            'system' => $targetCoords->system,
            'position' => $targetCoords->position,
        ]);

        // Should fail due to lack of deuterium
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertFalse($responseData['success'] ?? true, 'Phalanx scan should fail without deuterium');
    }
}
