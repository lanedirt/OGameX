<?php

namespace Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet\Coordinate;
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

    protected function fleetCheckToSecondPlanet(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->checkTargetFleet($coordinates, $units, $assertSuccess);
    }

    protected function fleetCheckToOtherPlayer(UnitCollection $units, bool $assertSuccess): void
    {
        $nearbyForeignPlanet = $this->getNearbyForeignPlanet();
        $this->checkTargetFleet($nearbyForeignPlanet->getPlanetCoordinates(), $units, $assertSuccess);
    }

    protected function fleetCheckToEmptyPosition(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->getNearbyEmptyCoordinate();
        $this->checkTargetFleet($coordinates, $units, $assertSuccess);
    }

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
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $units, $resources, $assertStatus);
    }

    /**
     * Send a fleet to a planet of another player.
     */
    protected function sendMissionToOtherPlayer(UnitCollection $units, Resources $resources, int $assertStatus = 200): PlanetService
    {
        $nearbyForeignPlanet = $this->getNearbyForeignPlanet();
        $this->dispatchFleet($nearbyForeignPlanet->getPlanetCoordinates(), $units, $resources, $assertStatus);
        return $nearbyForeignPlanet;
    }

    /**
     * Send a fleet to an empty position.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $assertStatus
     * @return Coordinate
     */
    protected function sendMissionToEmptyPosition(UnitCollection $units, Resources $resources, int $assertStatus = 200): Coordinate
    {
        $coordinates = $this->getNearbyEmptyCoordinate();
        $this->dispatchFleet($coordinates, $units, $resources, $assertStatus);
        return $coordinates;
    }

    /**
     * Call check-target fleet method with the given units and coordinates.
     *
     * @param Coordinate $coordinates
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function checkTargetFleet(Coordinate $coordinates, UnitCollection $units, bool $assertSuccess): void
    {
        $unitsArray = $this->convertUnitsToArray($units);

        $post = $this->post('/ajax/fleet/dispatch/check-target', array_merge([
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
            'position' => $coordinates->position,
            'type' => 1,
            'mission' => $this->missionType,
            '_token' => csrf_token(),
        ], $unitsArray));

        // Check request should always result in success HTTP call, even if the mission is not possible.
        // All errors should be included in the JSON response.
        $post->assertStatus(200);

        // Assert that JSON response has the correct status and mission type.
        if ($assertSuccess) {
            $post->assertJson([
                'status' => 'success',
                'orders' => [
                    $this->missionType => true,
                ]
            ]);
        }

        $this->reloadApplication();
    }

    /**
     * Dispatch a fleet to a specified location.
     *
     * @param Coordinate $coordinates
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $assertStatus
     * @return void
     */
    protected function dispatchFleet(Coordinate $coordinates, UnitCollection $units, Resources $resources, int $assertStatus = 200): void
    {
        $unitsArray = $this->convertUnitsToArray($units);

        $post = $this->post('/ajax/fleet/dispatch/send-fleet', array_merge([
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
            'position' => $coordinates->position,
            'type' => 1,
            'mission' => $this->missionType,
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
        ], $unitsArray));

        $post->assertStatus($assertStatus);

        $this->reloadApplication();

        $this->get('/ajax/fleet/eventbox/fetch')->assertStatus(200);
        $this->get('/ajax/fleet/eventlist/fetch')->assertStatus(200);
    }

    /**
     * Convert units to dispatchable array format.
     *
     * @param UnitCollection $units
     * @return array<string, int>
     */
    private function convertUnitsToArray(UnitCollection $units): array
    {
        $unitsArray = [];
        foreach ($units->units as $unit) {
            $unitsArray['am' . $unit->unitObject->id] = $unit->amount;
        }
        return $unitsArray;
    }
}
