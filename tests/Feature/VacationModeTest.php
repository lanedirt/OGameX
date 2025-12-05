<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Test that vacation mode works as expected.
 */
class VacationModeTest extends FleetDispatchTestCase
{
    protected int $missionType = 3; // Transport mission
    protected string $missionName = 'Transport';

    /**
     * Prepare the planet for the test.
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 5);
        $this->playerSetResearchLevel('computer_technology', 1);
        $this->planetAddResources(new Resources(5000, 5000, 5000, 0));
    }

    /**
     * Test that vacation mode can be activated when no fleets are in transit.
     */
    public function testVacationModeActivationSuccess(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Verify player is not in vacation mode initially
        $this->assertFalse($player->isInVacationMode());

        // Verify player can activate vacation mode
        $this->assertTrue($player->canActivateVacationMode());

        // Activate vacation mode
        $player->activateVacationMode();

        // Verify player is now in vacation mode
        $this->assertTrue($player->isInVacationMode());

        // Verify vacation_mode_until is set to 48 hours from now
        $this->assertNotNull($player->getVacationModeUntil());
        $expectedTime = now()->addHours(48);
        $this->assertTrue(
            $player->getVacationModeUntil()->greaterThanOrEqualTo($expectedTime->subMinute()) &&
            $player->getVacationModeUntil()->lessThanOrEqualTo($expectedTime->addMinute())
        );
    }

    /**
     * Test that vacation mode cannot be activated when fleets are in transit.
     */
    public function testVacationModeActivationFailsWithFleetInTransit(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Send a fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);

        // Dispatch fleet to second planet (transport mission with no resources)
        // This method already asserts success internally
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Verify player cannot activate vacation mode with fleet in transit
        $this->assertFalse($player->canActivateVacationMode());
    }

    /**
     * Test that vacation mode can be deactivated after 48 hours.
     */
    public function testVacationModeDeactivationAfter48Hours(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Activate vacation mode
        $player->activateVacationMode();
        $this->assertTrue($player->isInVacationMode());

        // Verify cannot deactivate immediately
        $this->assertFalse($player->canDeactivateVacationMode());

        // Travel 48 hours into the future
        $this->travel(48)->hours();

        // Verify can now deactivate
        $this->assertTrue($player->canDeactivateVacationMode());

        // Deactivate vacation mode
        $player->deactivateVacationMode();

        // Verify player is no longer in vacation mode
        $this->assertFalse($player->isInVacationMode());
    }

    /**
     * Test that vacation mode cannot be deactivated before 48 hours.
     */
    public function testVacationModeDeactivationFailsBefore48Hours(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Activate vacation mode
        $player->activateVacationMode();
        $this->assertTrue($player->isInVacationMode());

        // Travel only 24 hours into the future
        $this->travel(24)->hours();

        // Verify still cannot deactivate
        $this->assertFalse($player->canDeactivateVacationMode());
    }

    /**
     * Test that all hostile missions are blocked on players in vacation mode.
     */
    public function testAllMissionsBlockedOnVacationModePlayer(): void
    {
        $this->basicSetup();

        // Add espionage probes for espionage test
        $this->planetAddUnit('espionage_probe', 5);

        // Get a nearby foreign planet
        $otherPlanet = $this->getNearbyForeignPlanet();
        $otherPlayer = $otherPlanet->getPlayer();

        // Put the other player in vacation mode
        $otherPlayer->activateVacationMode();
        $this->assertTrue($otherPlayer->isInVacationMode());

        // Switch back to the first player
        $this->get('/overview');

        // Check if missions are possible
        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $otherPlanet->getPlanetCoordinates()->galaxy,
            'system' => $otherPlanet->getPlanetCoordinates()->system,
            'position' => $otherPlanet->getPlanetCoordinates()->position,
            'type' => PlanetType::Planet->value,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertArrayHasKey('orders', $responseData);

        // Verify all hostile missions are not enabled for vacation mode players
        // Mission type 1: Attack
        $this->assertArrayHasKey(1, $responseData['orders']);
        $this->assertFalse($responseData['orders'][1], 'Attack mission should not be available for players in vacation mode');

        // Mission type 3: Transport
        $this->assertArrayHasKey(3, $responseData['orders']);
        $this->assertFalse($responseData['orders'][3], 'Transport mission should not be available for players in vacation mode');

        // Mission type 6: Espionage
        $this->assertArrayHasKey(6, $responseData['orders']);
        $this->assertFalse($responseData['orders'][6], 'Espionage mission should not be available for players in vacation mode');
    }

    /**
     * Test that players in vacation mode cannot send any missions.
     */
    public function testCannotSendMissionsWhileInVacationMode(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Activate vacation mode
        $player->activateVacationMode();
        $this->assertTrue($player->isInVacationMode());

        // Try to send a transport mission to second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);

        // Check if missions are possible to own second planet
        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $this->secondPlanetService->getPlanetCoordinates()->galaxy,
            'system' => $this->secondPlanetService->getPlanetCoordinates()->system,
            'position' => $this->secondPlanetService->getPlanetCoordinates()->position,
            'type' => PlanetType::Planet->value,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertArrayHasKey('orders', $responseData);

        // Verify no missions are enabled while in vacation mode
        // Mission type 3: Transport
        $this->assertArrayHasKey(3, $responseData['orders']);
        $this->assertFalse($responseData['orders'][3], 'Transport mission should not be available while in vacation mode');

        // Mission type 4: Deployment
        $this->assertArrayHasKey(4, $responseData['orders']);
        $this->assertFalse($responseData['orders'][4], 'Deployment mission should not be available while in vacation mode');
    }

    /**
     * Test that resource production is zero during vacation mode.
     */
    public function testResourceProductionZeroDuringVacationMode(): void
    {
        $this->basicSetup();

        $planet = $this->planetService;
        $player = $planet->getPlayer();

        // Build a metal mine to have some production
        $this->planetSetObjectLevel('metal_mine', 5);

        // Update planet to calculate production
        $planet->updateResourceProductionStats();

        // Store initial production values (should be greater than zero)
        $initialMetalProduction = $planet->getMetalProductionPerHour();
        $this->assertGreaterThan(0, $initialMetalProduction, 'Metal production should be greater than 0 before vacation mode');

        // Activate vacation mode
        $player->activateVacationMode();

        // Reload planet to get updated percentages
        $this->reloadApplication();
        $planet = $this->planetService;

        // Update planet production again
        $planet->updateResourceProductionStats();

        // Verify all production is now zero
        $this->assertEquals(0, $planet->getMetalProductionPerHour(), 'Metal production should be 0 during vacation mode');
        $this->assertEquals(0, $planet->getCrystalProductionPerHour(), 'Crystal production should be 0 during vacation mode');
        $this->assertEquals(0, $planet->getDeuteriumProductionPerHour(), 'Deuterium production should be 0 during vacation mode');
    }

    /**
     * Test that production percentages are set to 0 and must be manually reset after vacation mode.
     */
    public function testProductionPercentagesResetAfterVacationMode(): void
    {
        $this->basicSetup();

        $planet = $this->planetService;
        $player = $planet->getPlayer();

        // Calculate base income (before building any mines)
        $planet->updateResourceProductionStats();
        $baseMetalIncome = $planet->getMetalProductionPerHour();

        // Build a metal mine
        $this->planetSetObjectLevel('metal_mine', 5);

        // Reload planet to get updated building levels
        $this->reloadApplication();
        $planet = $this->planetService;

        // Verify initial production percentage is 10 (representing 100% in 0-10 scale)
        $this->assertEquals(10, $planet->getBuildingPercent('metal_mine'));

        // Update planet to calculate initial production
        $planet->updateResourceProductionStats();
        $initialMetalProduction = $planet->getMetalProductionPerHour();
        $this->assertGreaterThanOrEqual($baseMetalIncome, $initialMetalProduction);

        // Activate vacation mode
        $player->activateVacationMode();

        // Reload planet to get updated percentages
        $this->reloadApplication();
        $planet = $this->planetService;

        // Verify production percentage is now 0
        $this->assertEquals(0, $planet->getBuildingPercent('metal_mine'));

        // Verify production is zero due to percentage
        $planet->updateResourceProductionStats();
        $this->assertEquals(0, $planet->getMetalProductionPerHour());

        // Travel 48 hours and deactivate vacation mode
        $this->travel(48)->hours();
        $player->deactivateVacationMode();

        // Reload planet
        $this->reloadApplication();
        $planet = $this->planetService;

        // Verify production percentage is still 0 (must be manually reset)
        $this->assertEquals(0, $planet->getBuildingPercent('metal_mine'));

        // Building production remains at 0 until manually reset (only base income restored)
        $planet->updateResourceProductionStats();
        $productionAfterDeactivation = $planet->getMetalProductionPerHour();
        $this->assertGreaterThan(0, $productionAfterDeactivation, 'Base income should be restored after deactivation');

        // Manually reset production to 100% (10 in 0-10 scale)
        $planet->setBuildingPercent(ObjectService::getObjectByMachineName('metal_mine')->id, 10);

        // Reload planet to get updated percentages
        $this->reloadApplication();
        $planet = $this->planetService;

        // Now full production resumes (base income + mine production)
        $planet->updateResourceProductionStats();
        $this->assertGreaterThanOrEqual($productionAfterDeactivation, $planet->getMetalProductionPerHour(), 'Mine production should be enabled after manually resetting to 100%');
    }

    /**
     * Test that galaxy view shows vacation mode status.
     */
    public function testGalaxyViewShowsVacationModeStatus(): void
    {
        $this->basicSetup();

        // Get a nearby foreign planet
        $otherPlanet = $this->getNearbyForeignPlanet();
        $otherPlayer = $otherPlanet->getPlayer();

        // Put the other player in vacation mode
        $otherPlayer->activateVacationMode();

        // Switch back to the first player
        $this->get('/overview');

        // Request galaxy view for the system where the other player's planet is
        $response = $this->post('/ajax/galaxy', [
            'galaxy' => $otherPlanet->getPlanetCoordinates()->galaxy,
            'system' => $otherPlanet->getPlanetCoordinates()->system,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        // Find the row for the other player's planet
        $this->assertArrayHasKey('system', $responseData);
        $this->assertArrayHasKey('galaxyContent', $responseData['system']);

        $planetRow = null;
        foreach ($responseData['system']['galaxyContent'] as $row) {
            if ($row['position'] == $otherPlanet->getPlanetCoordinates()->position) {
                $planetRow = $row;
                break;
            }
        }

        $this->assertNotNull($planetRow, 'Planet row should be found in galaxy view');
        $this->assertArrayHasKey('player', $planetRow);
        $this->assertArrayHasKey('isOnVacation', $planetRow['player']);
        $this->assertTrue($planetRow['player']['isOnVacation'], 'Player should be shown as in vacation mode in galaxy view');
    }

    /**
     * Test vacation mode activation through the options page.
     */
    public function testVacationModeActivationThroughOptionsPage(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Verify player is not in vacation mode
        $this->assertFalse($player->isInVacationMode());

        // Submit the options form with vacation mode checkbox
        $response = $this->post('/options', [
            '_token' => csrf_token(),
            'urlaubs_modus' => 'on',
        ]);

        // Should redirect back to options page
        $response->assertRedirect('/options');

        // Reload application and player to get updated data
        $this->reloadApplication();
        $player->load($player->getId());

        // Verify player is now in vacation mode
        $this->assertTrue($player->isInVacationMode());
    }

    /**
     * Test vacation mode deactivation through the options page after 48 hours.
     */
    public function testVacationModeDeactivationThroughOptionsPage(): void
    {
        $this->basicSetup();

        $player = $this->planetService->getPlayer();

        // Activate vacation mode first
        $player->activateVacationMode();
        $this->assertTrue($player->isInVacationMode());

        // Travel 48 hours
        $this->travel(48)->hours();

        // Submit the options form without vacation mode checkbox (deactivating it)
        $response = $this->post('/options', [
            '_token' => csrf_token(),
        ]);

        // Should redirect back to options page
        $response->assertRedirect('/options');

        // Reload application and player to get updated data
        $this->reloadApplication();
        $player->load($player->getId());

        // Verify player is no longer in vacation mode
        $this->assertFalse($player->isInVacationMode());
    }
}
