<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected for colonisation missions.
 */
class FleetDispatchColoniseTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 7;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Colonisation';

    protected bool $hasReturnMission = false;

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('research_lab', 3);
        $this->playerSetResearchLevel('computer_technology', 5);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('impulse_drive', 3);
        $this->playerSetResearchLevel('espionage_technology', 4);
        // Astrophysics level 3 gives option for two colonies.
        $this->playerSetResearchLevel('astrophysics', 3);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('colony_ship', 1);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
    }

    /**
     * Assert that check request to dispatch fleet to empty position succeeds with colony ship.
     */
    public function testFleetCheckWithColonyShipSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails without colony ship.
     */
    public function testFleetCheckWithoutColonyShipError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Send fleet to a planet position that is already colonized.
     */
    public function testDispatchFleetToNotEmptyPositionFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        // Expecting mission to fail (assertStatus is false)
        $this->sendMissionToOtherPlayerPlanet($unitCollection, new Resources(0, 0, 0, 0), false);
    }

    /**
     * Send fleet to empty planet without colony ship.
     */
    public function testDispatchFleetWithoutColonyShipFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 1);
        // Expecting mission to fail (assertStatus is false)
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(0, 0, 0, 0), false);
    }

    /**
     * Main test for colonizing an empty planet (happy path).
     */
    public function testDispatchFleetColonizeEmptyPlanet(): void
    {
        $this->basicSetup();

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(100, 0, 0, 0));

        // Increase time by 10 hours to ensure the mission is done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($newPlanetCoordinates);

        $this->assertNotNull($newPlanet, 'New planet cannot be loaded while it should have been created.');

        // Assert that last message sent to current player contains the new planet colonize confirm message.
        $this->assertMessageReceivedAndContainsDatabase($this->planetService->getPlayer(), [
            'The fleet has arrived',
            'found a new planet there and are beginning to develop upon it immediately.',
        ]);
    }

    /**
     * Test that colonisation mission takes into account astrophysics technology to determine max amount
     * of colonies that can be established.
     */
    public function testDispatchFleetColonizeAstrophysics(): void
    {
        $this->basicSetup();

        // Astrophysics level 5 should support three colonies.
        // During tests a new account gets 1 colony already by default. So with astrophysics level 5, 2 more planet
        // should be able to be created.
        $this->playerSetResearchLevel('astrophysics', 5);
        $this->planetAddUnit('colony_ship', 5);

        $created_planets = [];
        // Send 5 colony ships to empty planets with colonization mission. Only 2 should be created.
        for ($i = 0; $i < 5; $i++) {
            // Send fleet to an empty planet.
            $unitCollection = new UnitCollection();
            $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);

            $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(10, 0, 0, 0));

            // Increase time by 10 hours to ensure the mission is done.
            $this->travel(10)->hours();

            // Do a request to trigger the update logic.
            $response = $this->get('/overview');
            $response->assertStatus(200);

            // Assert that the new planet has been created.
            $planetServiceFactory = resolve(PlanetServiceFactory::class);
            $newPlanet = $planetServiceFactory->makeForCoordinate($newPlanetCoordinates);
            if ($newPlanet !== null) {
                $created_planets[] = $newPlanet;
            }
        }

        // Assert that only two planets have been successfully created.
        $this->assertCount(2, $created_planets, 'Exactly two planets should have been created with astrophysics level 5. Check astrophysics logic.');

        // Check that 3 messages have been sent to the player about failed colonization attempts.
        $this->assertMessageReceivedAndContainsDatabase($this->planetService->getPlayer(), [
            'The fleet has arrived',
            'knowledge of astrophysics is not sufficient',
        ], 3);
    }

    /**
     * Test that combination of sending a colonization mission with insufficient astrophysics research still succeeds
     * if research is in progress and will be completed before the mission arrives.
     */
    public function testDispatchFleetColonizeAstroResearchInProgress(): void
    {
        $this->basicSetup();
        $this->planetAddResources(new Resources(12300, 24500, 12300, 0));

        // Astrophysics level 2 should support only one colony. Test user has 1 colony already.
        // So with these presets it should not be possible to create a new planet.
        $this->playerSetResearchLevel('astrophysics', 2);
        $this->planetAddUnit('colony_ship', 5);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(10, 0, 0, 0));

        // Upgrade astrophysics research to level 3 via research page.
        $this->addResearchBuildRequest('astrophysics');

        // Increase time by 10 hours to ensure the mission and research should both be done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        $response = $this->get('/research');
        $response->assertStatus(200);

        // Assert that astro research is now level 3.
        $this->assertObjectLevelOnPage($response, 'astrophysics', 3, 'Astrophysics research is not at level 3 after 10 hours of research.');

        // Assert that the new planet has been created.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($newPlanetCoordinates);

        $this->assertNotNull($newPlanet, 'New planet cannot be loaded while it should have been created.');
    }

    /**
     * Test that when sending cargos along with the colony ship, the resources are added to the new planet
     * and the cargo ships return without the colony ship.
     */
    public function testDispatchFleetColonizeReturnTripCargo(): void
    {
        $this->basicSetup();

        // Assert that we begin with 1 colony ship and 5 small cargos.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 3);
        $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(400, 400, 0, 0));

        // Assert that the cargo ships have been sent.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 0);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 2);

        // Increase time by 10 hours to ensure the arrival and return missions are done.
        $this->travel(10)->hours();

        // Do a request to trigger the update logic.
        // Note: we only make one request here, as the arrival and return missions should be processed in the same request
        // since enough time has passed.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($newPlanetCoordinates);
        $this->assertNotNull($newPlanet, 'New planet cannot be loaded while it should have been created.');

        // Assert that the cargo ships have returned without the colony ship.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 0);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);
    }

    /**
     * Test that when a mission has been sent and update happens long time later, both the arrival
     * and return missions are processed in the same request.
     */
    public function testDispatchFleetColonizeReturnTripProcessSingleRequest(): void
    {
        $this->basicSetup();

        // Assert that we begin with 1 colony ship and 5 small cargos.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 3);
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(400, 400, 0, 0));

        // Increase time by 10 hours to ensure the arrival and return missions are done.
        $this->travel(10)->hours();

        // Reload the application to ensure all caches are cleared and changed units are reflected.
        $this->reloadApplication();
        $this->planetService->reloadPlanet();

        // Do a request to trigger the update logic.
        // Note: we only make one request here, as the arrival and return missions should be processed in the same request
        // since enough time has passed.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the cargo ships have returned without the colony ship.
        $this->assertObjectLevelOnPage($response, 'colony_ship', 0);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);
    }

    /**
     * Test that positions 3 and 13 require Astrophysics level 4.
     */
    public function testDispatchFleetColonizePosition3And13RequiresAstro4(): void
    {
        $this->basicSetup();

        // Set Astrophysics to level 3 (insufficient for positions 3 and 13)
        $this->playerSetResearchLevel('astrophysics', 3);

        // Try to colonize position 3 with Astrophysics level 3 (should fail)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);

        // Create custom coordinates for position 3
        $planetCoords = $this->planetService->getPlanetCoordinates();
        $targetCoordinates = new \OGame\Models\Planet\Coordinate($planetCoords->galaxy, $planetCoords->system, 3);

        // This should fail with 400 status
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, false);

        // Now set Astrophysics to level 4 (sufficient)
        $this->playerSetResearchLevel('astrophysics', 4);

        // Try again with position 3 (should succeed)
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, true);

        // Increase time by 10 hours to ensure the mission is done
        $this->travel(10)->hours();

        // Do a request to trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinates);
        $this->assertNotNull($newPlanet, 'Planet at position 3 should be created with Astrophysics level 4.');
    }

    /**
     * Test that positions 2 and 14 require Astrophysics level 6.
     */
    public function testDispatchFleetColonizePosition2And14RequiresAstro6(): void
    {
        $this->basicSetup();

        // Set Astrophysics to level 5 (insufficient for positions 2 and 14)
        $this->playerSetResearchLevel('astrophysics', 5);

        // Try to colonize position 2 with Astrophysics level 5 (should fail)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);

        // Create custom coordinates for position 2
        $planetCoords = $this->planetService->getPlanetCoordinates();
        $targetCoordinates = new \OGame\Models\Planet\Coordinate($planetCoords->galaxy, $planetCoords->system, 2);

        // This should fail with 400 status
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, false);

        // Now set Astrophysics to level 6 (sufficient)
        $this->playerSetResearchLevel('astrophysics', 6);

        // Try again with position 2 (should succeed)
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, true);

        // Increase time by 10 hours to ensure the mission is done
        $this->travel(10)->hours();

        // Do a request to trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinates);
        $this->assertNotNull($newPlanet, 'Planet at position 2 should be created with Astrophysics level 6.');
    }

    /**
     * Test that positions 1 and 15 require Astrophysics level 8.
     */
    public function testDispatchFleetColonizePosition1And15RequiresAstro8(): void
    {
        $this->basicSetup();

        // Set Astrophysics to level 7 (insufficient for positions 1 and 15)
        $this->playerSetResearchLevel('astrophysics', 7);

        // Try to colonize position 1 with Astrophysics level 7 (should fail)
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);

        // Create custom coordinates for position 1
        $planetCoords = $this->planetService->getPlanetCoordinates();
        $targetCoordinates = new \OGame\Models\Planet\Coordinate($planetCoords->galaxy, $planetCoords->system, 1);

        // This should fail with 400 status
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, false);

        // Now set Astrophysics to level 8 (sufficient)
        $this->playerSetResearchLevel('astrophysics', 8);

        // Try again with position 1 (should succeed)
        $this->dispatchFleet($targetCoordinates, $unitCollection, new Resources(0, 0, 0, 0), PlanetType::Planet, 0, true);

        // Increase time by 10 hours to ensure the mission is done
        $this->travel(10)->hours();

        // Do a request to trigger the update logic
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinates);
        $this->assertNotNull($newPlanet, 'Planet at position 1 should be created with Astrophysics level 8.');
    }

    /**
     * Verify that canceling/recalling an active mission works.
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Assert that we begin with 5 small cargo ships and 1 colony ship on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);
        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 5);
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('colony_ship'), 1);
        $emptyPositionCoordinate = $this->sendMissionToEmptyPosition($unitCollection, new Resources(5000, 5000, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $fleetParentTime = Carbon::getTestNow()->addMinute();
        $this->travelTo($fleetParentTime);

        // Cancel the mission
        $response = $this->post('/ajax/fleet/dispatch/recall-fleet', [
            'fleet_mission_id' => $fleetMissionId,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);

        // Assert that the original mission is now canceled.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->canceled == 1, 'Fleet mission is not canceled after fleet recall is requested.');

        // Assert that only the return trip is now visible.
        // The eventbox should only show 1 mission (the parent).
        $response = $this->get('/ajax/fleet/eventbox/fetch');
        $response->assertStatus(200);
        $response->assertJsonFragment(['friendly' => 1]);
        $response->assertJsonFragment(['eventText' => $this->missionName . ' (R)']);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);

        // Assert that the return trip arrival time is exactly 1 minute after the cancellation time.
        // Because the return trip should take exactly as long as the original trip has traveled until it was canceled.
        $this->assertTrue($fleetMission->time_arrival == $fleetParentTime->addSeconds(60)->timestamp, 'Return trip duration is not the same as the original mission has been active.');

        // Advance time by amount it takes for the return trip to arrive.
        $this->travelTo(Carbon::createFromTimestamp($fleetMission->time_arrival));

        // Do a request to trigger the update logic.
        $this->get('/overview');

        // Assert that the return trip is processed.
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);
        $this->assertTrue($fleetMission->processed == 1, 'Return trip is not processed after fleet has arrived back at origin planet.');

        // Assert that the units have been returned to the origin planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1, 'Colony ship is not at original 1 units after recalled trip has been processed.');
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5, 'Small Cargo ships are not at original 5 units after recalled trip has been processed.');
        // Assert that the resources have been returned to the origin planet.
        $this->planetService->reloadPlanet();
        $this->assertTrue($this->planetService->hasResources(new Resources(5000, 5000, 0, 0)), 'Resources are not returned to origin planet after recalling mission.');

        // Assert that the last message sent contains the return trip message.
        $this->assertMessageReceivedAndContainsDatabase($this->planetService->getPlayer(), [
            'Your fleet is returning from',
            '[' . $emptyPositionCoordinate->asString() . ']',
            'Metal: 5,000',
        ]);
    }
}
