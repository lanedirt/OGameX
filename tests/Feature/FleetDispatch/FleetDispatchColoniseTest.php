<?php

namespace Feature\FleetDispatch;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Message;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
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
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 1);
        $this->planetSetObjectLevel('research_lab', 1);
        $this->playerSetResearchLevel('energy_technology', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('colony_ship', 1);
    }

    /**
     * Assert that check request to dispatch fleet to empty position succeeds with colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testFleetCheckWithColonyShipSuccess(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, true);
    }

    /**
     * Assert that check request to dispatch fleet to empty position fails without colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testFleetCheckWithoutColonyShipError(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->fleetCheckToEmptyPosition($unitCollection, false);
    }

    /**
     * Send fleet to a planet position that is already colonized.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetToNotEmptyPositionFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        // Expecting 500 error.
        $this->sendMissionToOtherPlayer($unitCollection, new Resources(0, 0, 0, 0), 500);
    }

    /**
     * Send fleet to empty planet without colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetWithoutColonyShipFails(): void
    {
        $this->basicSetup();
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        // Expecting 500 error.
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(0, 0, 0, 0), 500);
    }

    /**
     * Main test for colonizing an empty planet (happy path).
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetColonizeEmptyPlanet(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(100, 0, 0, 0));

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $newPlanet = $planetServiceFactory->makeForCoordinate($newPlanetCoordinates);
        $this->assertNotNull($newPlanet, 'New planet cannot be loaded while it should have been created.');

        // Assert that last message sent to current player contains the new planet colonize confirm message.
        $this->assertMessageReceivedAndContainsDatabase($this->planetService->getPlayer(), [
            'The fleet has arrived',
            'found a new planet there and are beginning to develop upon it immediately.',
        ]);
    }

    /**
     * Test that when sending cargos along with the colony ship, the resources are added to the new planet
     * and the cargo ships return without the colony ship.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetColonizeReturnTripCargo(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that we begin with 1 colony ship and 5 small cargos.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 3);
        $newPlanetCoordinates = $this->sendMissionToEmptyPosition($unitCollection, new Resources(400, 400, 0, 0));

        // Assert that the cargo ships have been sent.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 0);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 2);

        // Increase time by 10 hours to ensure the arrival and return missions are done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        // Note: we only make one request here, as the arrival and return missions should be processed in the same request
        // since enough time has passed.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that the new planet has been created.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
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
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetColonizeReturnTripProcessSingleRequest(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that we begin with 1 colony ship and 5 small cargos.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);

        // Send fleet to an empty planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 3);
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(400, 400, 0, 0));

        // Increase time by 10 hours to ensure the arrival and return missions are done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        // Note: we only make one request here, as the arrival and return missions should be processed in the same request
        // since enough time has passed.
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the cargo ships have returned without the colony ship.
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);
        $this->assertObjectLevelOnPage($response, 'colony_ship', 0);
    }

    /**
     * Verify that canceling/recalling an active mission works.
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testDispatchFleetRecallMission(): void
    {
        $this->basicSetup();

        // Add resources for test.
        $this->planetAddResources(new Resources(5000, 5000, 0, 0));

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Assert that we begin with 5 small cargo ships and 1 colony ship on planet.
        $response = $this->get('/shipyard');
        $this->assertObjectLevelOnPage($response, 'colony_ship', 1);
        $this->assertObjectLevelOnPage($response, 'small_cargo', 5);
        // Send fleet to the second planet of the test user.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 5);
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('colony_ship'), 1);
        $this->sendMissionToEmptyPosition($unitCollection, new Resources(5000, 5000, 0, 0));

        // Get just dispatched fleet mission ID from database.
        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;

        // Advance time by 1 minute
        $fleetParentTime = $startTime->copy()->addMinutes(1);
        Carbon::setTestNow($fleetParentTime);

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

        $fleetMissionService = app()->make(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionId = $fleetMission->id;
        $fleetMission = $fleetMissionService->getFleetMissionById($fleetMissionId, false);

        // Assert that the return trip arrival time is exactly 1 minute after the cancelation time.
        // Because the return trip should take exactly as long as the original trip has traveled until it was canceled.
        $this->assertTrue($fleetMission->time_arrival == $fleetParentTime->addSeconds(60)->timestamp, 'Return trip duration is not the same as the original mission has been active.');

        // Advance time by amount it takes for the return trip to arrive.
        Carbon::setTestNow(Carbon::createFromTimestamp($fleetMission->time_arrival));

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
            'Your fleet is returning from planet',
            'Metal: 5,000',
        ]);
    }
}
