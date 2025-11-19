<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

class PhalanxTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for fleet tests (Attack)
     */
    protected int $missionType = 1;

    /**
     * @var string The mission name for fleet tests
     */
    protected string $missionName = 'Attack';

    /**
     * Basic setup required by FleetDispatchTestCase
     */
    protected function basicSetup(): void
    {
        // Add some ships for fleet dispatch tests
        $this->planetAddUnit('light_fighter', 10);
        $this->planetAddUnit('small_cargo', 10);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
    }

    /**
     * Override setUp to switch back to moon for phalanx tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        // FleetDispatchTestCase switches to first planet, but we need moon for phalanx
        $this->switchToMoon();
    }

    /**
     * Prepare the moon for the test, so it has the required buildings and resources.
     *
     * @return void
     */
    protected function setupPhalanxOnMoon(): void
    {
        // Build sensor phalanx level 1 on moon (range: 0, can only scan same system)
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->moonService->setObjectLevel($phalanxObject->id, 1);

        // Add enough deuterium for scanning (cost: 5000)
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));
    }

    /**
     * Test that scanning from a planet (not moon) fails.
     */
    public function testScanFromPlanetFails(): void
    {
        // Switch to planet instead of moon
        $this->switchToFirstPlanet();

        // Build sensor phalanx on planet (shouldn't work but let's try)
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->planetService->setObjectLevel($phalanxObject->id, 1);
        $this->planetService->addResources(new Resources(0, 0, 10000, 0));

        // Try to scan a nearby planet
        $targetPlanet = $this->getNearbyForeignPlanet();
        $coords = $targetPlanet->getPlanetCoordinates();

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_error' => true,
        ]);
        $this->assertStringContainsString('can only be used from a moon', $response->json('error_message'));
    }

    /**
     * Test that scanning without sensor phalanx built fails.
     */
    public function testScanWithoutPhalanxFails(): void
    {
        // Don't build sensor phalanx, just add deuterium
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));

        // Try to scan a nearby planet
        $targetPlanet = $this->getNearbyForeignPlanet();
        $coords = $targetPlanet->getPlanetCoordinates();

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_error' => true,
        ]);
        $this->assertStringContainsString('No Sensor Phalanx built', $response->json('error_message'));
    }

    /**
     * Test that scanning without enough deuterium fails.
     */
    public function testScanWithoutDeuteriumFails(): void
    {
        // Build sensor phalanx level 5 for good range
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->moonService->setObjectLevel($phalanxObject->id, 5);
        $this->moonService->addResources(new Resources(0, 0, 1000, 0)); // Only 1000, need 5000

        // Try to scan a nearby planet
        $targetPlanet = $this->getNearbyForeignPlanet();
        $coords = $targetPlanet->getPlanetCoordinates();

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_error' => true,
        ]);
        $this->assertStringContainsString('Not enough Deuterium', $response->json('error_message'));
    }

    /**
     * Test that scanning own planet fails.
     */
    public function testScanOwnPlanetFails(): void
    {
        $this->setupPhalanxOnMoon();

        // Try to scan our own planet
        $coords = $this->planetService->getPlanetCoordinates();

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_error' => true,
        ]);
        $this->assertStringContainsString('cannot scan your own planets', $response->json('error_message'));
    }

    /**
     * Test that scanning a planet out of range fails.
     */
    public function testScanOutOfRangeFails(): void
    {
        $this->setupPhalanxOnMoon();

        // Build phalanx level 1 (range: 0, can only scan same system)
        $moonCoords = $this->moonService->getPlanetCoordinates();

        // Try to scan a different system (out of range for level 1)
        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $moonCoords->galaxy,
            'system' => $moonCoords->system + 2, // 2 systems away, out of range
            'position' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_error' => true,
        ]);
        $this->assertStringContainsString('out of range', $response->json('error_message'));
    }

    /**
     * Test successful scan of a planet with no fleet movements.
     */
    public function testSuccessfulScanNoFleets(): void
    {
        // Build phalanx level 5 for good range (24 systems)
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->moonService->setObjectLevel($phalanxObject->id, 5);
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));

        // Get a nearby foreign planet
        $targetPlanet = $this->getNearbyForeignPlanet();
        $coords = $targetPlanet->getPlanetCoordinates();

        // Check deuterium before scan
        $deuteriumBefore = $this->moonService->deuterium()->get();

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Assert that error is not set (successful scan)
        $this->assertNull($response->json('is_error'));

        // Assert response contains expected data
        $this->assertEquals($coords->galaxy, $response->json('target.galaxy'));
        $this->assertEquals($coords->system, $response->json('target.system'));
        $this->assertEquals($coords->position, $response->json('target.position'));
        $this->assertEquals(5000, $response->json('scan_cost'));
        $this->assertEquals(0, $response->json('fleet_count'));

        // Assert content_html contains "No fleet movements"
        $this->assertStringContainsString('No fleet movements detected', $response->json('content_html'));

        // Verify deuterium was deducted in database
        $this->moonService->reloadPlanet();
        $deuteriumAfter = $this->moonService->deuterium()->get();
        $this->assertEquals($deuteriumBefore - 5000, $deuteriumAfter);
    }

    /**
     * Test successful scan showing incoming enemy fleet.
     */
    public function testSuccessfulScanWithIncomingFleet(): void
    {
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->moonService->setObjectLevel($phalanxObject->id, 5);
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));

        // Setup: Add ships to our main planet for sending
        $this->switchToFirstPlanet();
        $this->basicSetup();

        // Send an attack fleet to a foreign planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);
        $targetPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0));

        $coords = $targetPlanet->getPlanetCoordinates();

        // Switch back to moon to scan
        $this->switchToMoon();

        // Scan the target planet - should see our incoming attack fleet
        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(1, $response->json('fleet_count'));

        $contentHtml = $response->json('content_html');
        $this->assertStringContainsString('Your fleet', $contentHtml);
        $this->assertStringContainsString('Attack', $contentHtml);
    }

    /**
     * Test that scanning shows "Your fleet" label for own transport missions.
     */
    public function testScanShowsYourFleetLabelForTransport(): void
    {
        // Setup: Build phalanx on moon
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');
        $this->moonService->setObjectLevel($phalanxObject->id, 5);
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));

        // Setup: Add ships to our main planet
        $this->switchToFirstPlanet();
        $this->basicSetup();

        // Change mission type to Transport for this test
        $this->missionType = 3;

        // Send a transport fleet to a foreign planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 5);
        $targetPlanet = $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(100, 100, 100, 0));

        $coords = $targetPlanet->getPlanetCoordinates();

        // Switch back to moon to scan
        $this->switchToMoon();

        // Scan the target planet
        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('fleet_count'));

        // Check that it shows "Your fleet"
        $contentHtml = $response->json('content_html');
        $this->assertStringContainsString('Your fleet', $contentHtml);

        // Reset mission type
        $this->missionType = 1;
    }

    /**
     * Test phalanx range calculation.
     */
    public function testPhalanxRangeCalculation(): void
    {
        $phalanxObject = ObjectService::getObjectByMachineName('sensor_phalanx');

        // Level 1: range 0 (same system only)
        $this->moonService->setObjectLevel($phalanxObject->id, 1);
        $this->moonService->addResources(new Resources(0, 0, 10000, 0));

        $moonCoords = $this->moonService->getPlanetCoordinates();

        // Try to scan 1 system away - should fail
        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $moonCoords->galaxy,
            'system' => $moonCoords->system + 1,
            'position' => 5,
        ]);

        $response->assertJson(['is_error' => true]);
        $this->assertStringContainsString('out of range', $response->json('error_message'));

        // Upgrade to level 2: range 3
        $this->moonService->setObjectLevel($phalanxObject->id, 2);

        // Upgrade to level 4: range 15
        $this->moonService->setObjectLevel($phalanxObject->id, 4);

        // Test galaxy restriction - different galaxy should fail
        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => $moonCoords->galaxy + 1,
            'system' => $moonCoords->system,
            'position' => 5,
        ]);

        $response->assertJson(['is_error' => true]);
        $this->assertStringContainsString('out of range', $response->json('error_message'));
    }
}
