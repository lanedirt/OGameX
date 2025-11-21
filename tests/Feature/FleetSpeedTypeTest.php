<?php

namespace Tests\Feature;

use OGame\GameMissions\AttackMission;
use OGame\GameMissions\TransportMission;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

class FleetSpeedTypeTest extends AccountTestCase
{
    /**
     * Test exact duration with war fleet speed.
     * Verifies that with specific settings and units, the duration is calculated correctly.
     */
    public function testWarFleetSpeedWithLightFighter(): void
    {
        // Set specific speeds for testing
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed_war', 2);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);

        // Set combustion drive to level 10 for predictable speed
        $this->playerSetResearchLevel('combustion_drive', 10);

        // Add 1 light fighter
        $this->planetAddUnit('light_fighter', 1);

        $fleetMissionService = resolve(FleetMissionService::class);
        $currentPlanet = $this->planetService;

        // Target: 1 system away
        $targetCoords = clone $currentPlanet->getPlanetCoordinates();
        $targetCoords->system += 1;

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 1);

        $attackMission = resolve(AttackMission::class);
        $duration = $fleetMissionService->calculateFleetMissionDuration(
            $currentPlanet,
            $targetCoords,
            $units,
            $attackMission,
            10
        );

        // With war speed 2x, combustion 10, 1 system distance
        // Expected: 1855 seconds (30 minutes 55 seconds) (calculated from formula)
        $this->assertEquals(1855, $duration);
    }

    /**
     * Test exact duration with peaceful fleet speed.
     */
    public function testPeacefulFleetSpeedWithSmallCargo(): void
    {
        // Set specific speeds
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 3);

        // Set combustion drive to level 10
        $this->playerSetResearchLevel('combustion_drive', 10);

        // Add 1 small cargo
        $this->planetAddUnit('small_cargo', 1);

        $fleetMissionService = resolve(FleetMissionService::class);
        $currentPlanet = $this->planetService;

        // Target: 1 system away
        $targetCoords = clone $currentPlanet->getPlanetCoordinates();
        $targetCoords->system += 1;

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), 1);

        $transportMission = resolve(TransportMission::class);
        $duration = $fleetMissionService->calculateFleetMissionDuration(
            $currentPlanet,
            $targetCoords,
            $units,
            $transportMission,
            10
        );

        // With peaceful speed 3x, combustion 10, 1 system distance
        // Expected: 1954 seconds (32 minutes 34 seconds) (calculated from formula)
        $this->assertEquals(1954, $duration);
    }

    /**
     * Test that settings service stores and retrieves speeds correctly.
     */
    public function testSettingsServiceStoresSpeedsCorrectly(): void
    {
        $settingsService = resolve(SettingsService::class);

        // Set specific values
        $settingsService->set('fleet_speed_war', 7);
        $settingsService->set('fleet_speed_holding', 4);
        $settingsService->set('fleet_speed_peaceful', 5);

        // Verify they are stored and retrieved correctly
        $this->assertEquals(7, $settingsService->fleetSpeedWar());
        $this->assertEquals(4, $settingsService->fleetSpeedHolding());
        $this->assertEquals(5, $settingsService->fleetSpeedPeaceful());
    }

    /**
     * Test distance impact on duration with fixed speeds.
     */
    public function testDistanceImpactOnDuration(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('fleet_speed_war', 1);

        $this->playerSetResearchLevel('combustion_drive', 10);
        $this->planetAddUnit('light_fighter', 1);

        $fleetMissionService = resolve(FleetMissionService::class);
        $currentPlanet = $this->planetService;

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 1);

        $attackMission = resolve(AttackMission::class);

        // Test 1 system away
        $targetCoords1 = clone $currentPlanet->getPlanetCoordinates();
        $targetCoords1->system += 1;
        $duration1 = $fleetMissionService->calculateFleetMissionDuration($currentPlanet, $targetCoords1, $units, $attackMission, 10);

        // Test 5 systems away
        $targetCoords5 = clone $currentPlanet->getPlanetCoordinates();
        $targetCoords5->system += 5;
        $duration5 = $fleetMissionService->calculateFleetMissionDuration($currentPlanet, $targetCoords5, $units, $attackMission, 10);

        // 5 systems should take longer than 1 system
        $this->assertGreaterThan($duration1, $duration5);

        // Duration should not scale linearly (sqrt in formula)
        // 5 systems should be less than 5x the duration of 1 system
        $this->assertLessThan($duration1 * 5, $duration5);
    }
}
