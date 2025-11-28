<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Test the fleet movement page functionality.
 */
class FleetMovementTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 3; // Transport

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Transport';

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('espionage_probe', 5);
        $this->planetAddResources(new Resources(5000, 5000, 100000, 0));
    }

    /**
     * Test that fleet movement page redirects to fleet index when no active fleets.
     */
    public function testFleetMovementRedirectsWhenNoFleets(): void
    {
        $response = $this->get('/fleet/movement');
        $response->assertRedirect('/fleet');
    }

    /**
     * Test that fleet movement page shows active fleets.
     */
    public function testFleetMovementShowsActiveFleets(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Check fleet movement page
        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show the fleet details
        $response->assertSee('Transport');
        $response->assertSee($this->secondPlanetService->getPlanetName());
    }

    /**
     * Test that fleet movement page shows correct friendly status for transport mission.
     */
    public function testFleetMovementTransportShowsNeutralStatus(): void
    {
        $this->basicSetup();

        // Send transport mission
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Transport missions should have neutral status
        $response->assertSee('neutral', false);
    }

    /**
     * Test that fleet movement page shows correct friendly status for espionage mission.
     */
    public function testFleetMovementEspionageShowsHostileStatus(): void
    {
        $this->basicSetup();

        // Get a foreign planet to spy on
        $foreignPlanet = $this->getNearbyForeignPlanet();

        // Get espionage probe object ID
        $espionageProbe = ObjectService::getUnitObjectByMachineName('espionage_probe');

        // Send espionage mission
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', [
            'galaxy' => $foreignPlanet->getPlanetCoordinates()->galaxy,
            'system' => $foreignPlanet->getPlanetCoordinates()->system,
            'position' => $foreignPlanet->getPlanetCoordinates()->position,
            'type' => PlanetType::Planet->value,
            'mission' => 6, // Espionage
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            '_token' => csrf_token(),
            'speed' => 10,
            'am' . $espionageProbe->id => 1,
        ]);
        $post->assertStatus(200);

        $this->reloadApplication();

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Espionage missions should have hostile status
        $response->assertSee('hostile', false);
    }

    /**
     * Test that return trip is shown in the same row.
     */
    public function testFleetMovementShowsReturnTripInSameRow(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show the return info in the same row (nextTimer element)
        $response->assertSee('nextTimer', false);
        $response->assertSee('Return', false);
    }

    /**
     * Test that recall button is shown for active missions.
     */
    public function testFleetMovementShowsRecallButton(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show recall button
        $response->assertSee('recallFleet', false);
    }

    /**
     * Test that recall button works from fleet movement page.
     */
    public function testFleetMovementRecallWorks(): void
    {
        $this->basicSetup();

        // Assert we start with 5 small cargo
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        // Get fleet mission ID
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time by 1 minute
        $this->travel(1)->minutes();

        // Recall the fleet
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMission->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Fleet movement page should show return mission
        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);
        $response->assertSee('Transport (R)');
    }

    /**
     * Test that fleet movement page shows correct planet icons.
     */
    public function testFleetMovementShowsPlanetIcons(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show planet icons
        $response->assertSee('planetIcon planet', false);
    }

    /**
     * Test that fleet movement page shows correct moon icons.
     */
    public function testFleetMovementShowsMoonIcons(): void
    {
        $this->basicSetup();

        // Send fleet to the moon
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);

        $coordinates = $this->moonService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $unitCollection, new Resources(100, 100, 0, 0), PlanetType::Moon);

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show moon icon
        $response->assertSee('planetIcon moon', false);
    }

    /**
     * Test that fleet movement page shows fleet unit count and details.
     */
    public function testFleetMovementShowsFleetDetails(): void
    {
        $this->basicSetup();

        // Send fleet with multiple unit types
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 3);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(500, 300, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show ship info in tooltip
        $response->assertSee('Small Cargo');
        $response->assertSee('Metal');
        $response->assertSee('Crystal');
    }

    /**
     * Test that multiple active fleets are shown.
     */
    public function testFleetMovementShowsMultipleFleets(): void
    {
        $this->basicSetup();

        // Increase fleet slots by setting computer technology
        $this->playerSetResearchLevel('computer_technology', 5);

        // Send first fleet
        $unitCollection1 = new UnitCollection();
        $unitCollection1->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection1, new Resources(100, 0, 0, 0));

        // Send second fleet to moon
        $unitCollection2 = new UnitCollection();
        $unitCollection2->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $coordinates = $this->moonService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $unitCollection2, new Resources(0, 100, 0, 0), PlanetType::Moon);

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show both planets as destinations
        $response->assertSee($this->secondPlanetService->getPlanetName());
        $response->assertSee($this->moonService->getPlanetName());
    }

    /**
     * Test that return trip is not shown as separate row.
     */
    public function testFleetMovementReturnTripNotSeparateRow(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Count fleetDetails divs - should only be 1 (not 2 for outbound + return)
        $content = $response->getContent();
        $fleetDetailsCount = $content !== false ? substr_count($content, 'class="fleetDetails') : 0;

        $this->assertEquals(1, $fleetDetailsCount, 'Return trip should be shown in the same row, not as a separate row.');
    }

    /**
     * Test that fleet slots info is shown on movement page.
     */
    public function testFleetMovementShowsFleetSlots(): void
    {
        $this->basicSetup();

        // Send fleet to the second planet
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(100, 100, 0, 0));

        $response = $this->get('/fleet/movement');
        $response->assertStatus(200);

        // Should show fleet slots info
        $response->assertSee('Fleets');
    }
}
