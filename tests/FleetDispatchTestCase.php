<?php

namespace Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

/**
 * Base class to test that fleet missions work as expected.
 * Extending this class will include basic tests for dispatching fleets for that mission type.
 */
abstract class FleetDispatchTestCase extends AccountTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName;

    /**
     * @var bool Whether the mission has a return mission by default.
     * Note: a mission that can be canceled still requires return mission logic.
     */
    protected bool $hasReturnMission = true;

    /**
     * @var bool Whether the mission is cancelable.
     */
    protected bool $isCancelable = true;

    /**
     * Prepare the planet for the test so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    abstract protected function basicSetup(): void;

    /**
     * Send a fleet to the second planet of the test user.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $assertStatus
     * @return void
     */
    protected function sendMissionToSecondPlanet(UnitCollection $units, Resources $resources, int $assertStatus = 200): void
    {
        // Convert units to array.
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        // Send fleet to the second planet of the test user.
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $this->secondPlanetService->getPlanetCoordinates()->galaxy,
            'system' => $this->secondPlanetService->getPlanetCoordinates()->system,
            'position' => $this->secondPlanetService->getPlanetCoordinates()->position,
            'type' => 1,
            'mission' => $this->missionType,
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        // Assert that the fleet was dispatched successfully.
        $post->assertStatus($assertStatus);

        // Assert that eventbox fetch works when a fleet mission is active.
        $this->get('/ajax/fleet/eventbox/fetch')->assertStatus(200);
        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);
    }

    /**
     * Send a fleet to a planet of another player.
     *
     * @throws BindingResolutionException
     */
    protected function sendMissionToOtherPlayer(UnitCollection $units, Resources $resources, int $assertStatus = 200): PlanetService
    {
        // Convert units to array.
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        // Get a planet of another nearby player.
        $secondPlayerPlanet = $this->getNearbyForeignPlanet();

        // Send fleet to the second planet of the test user.
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $secondPlayerPlanet->getPlanetCoordinates()->galaxy,
            'system' => $secondPlayerPlanet->getPlanetCoordinates()->system,
            'position' => $secondPlayerPlanet->getPlanetCoordinates()->position,
            'type' => 1,
            'mission' => $this->missionType,
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        // Assert that the fleet was dispatched successfully.
        $post->assertStatus($assertStatus);

        // Assert that eventbox fetch works when a fleet mission is active.
        $this->get('/ajax/fleet/eventbox/fetch')->assertStatus(200);
        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);

        return $secondPlayerPlanet;
    }

    /**
     * Send a fleet to an empty position.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $assertStatus
     * @return void
     */
    protected function sendMissionToEmptyPosition(UnitCollection $units, Resources $resources, int $assertStatus = 200): void
    {
        // Convert units to array.
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }

        // Get a planet of another nearby player.
        $emptyPosition = $this->getNearbyEmptyCoordinate();

        // Send fleet to the second planet of the test user.
        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $emptyPosition->galaxy,
            'system' => $emptyPosition->system,
            'position' => $emptyPosition->position,
            'type' => 1,
            'mission' => $this->missionType,
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        // Assert that the fleet was dispatched successfully.
        $post->assertStatus($assertStatus);

        // Assert that eventbox fetch works when a fleet mission is active.
        $this->get('/ajax/fleet/eventbox/fetch')->assertStatus(200);
        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);
    }
}
