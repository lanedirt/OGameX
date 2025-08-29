<?php

namespace Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

/**
 * Base class to test that fleet missions work as expected.
 * Extending this class will include basic tests for dispatching fleets for that mission type.
 */
abstract class FleetDispatchTestCase extends MoonTestCase
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
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     * @throws BindingResolutionException
     */
    abstract protected function basicSetup(): void;

    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Switch back to main planet as the tests default to sending fleet sfrom main planet.
        $this->switchToFirstPlanet();
    }

    /**
     * Check that fleet can be dispatched to the second planet of the test user.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToSecondPlanet(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->checkTargetFleet($coordinates, $units, PlanetType::Planet, $assertSuccess);
    }

    /**
     * Check that fleet can be dispatched to the first planet debris field of the test user.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToFirstPlanetDebrisField(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();
        $this->checkTargetFleet($coordinates, $units, PlanetType::DebrisField, $assertSuccess);
    }

    /**
     * Check that fleet can be dispatched to the second planet debris field of the test user.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToSecondPlanetDebrisField(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->checkTargetFleet($coordinates, $units, PlanetType::DebrisField, $assertSuccess);
    }

    /**
     * Check that fleet can be dispatched to a planet of another player.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToOtherPlayer(UnitCollection $units, bool $assertSuccess): void
    {
        $nearbyForeignPlanet = $this->getNearbyForeignPlanet();
        $this->checkTargetFleet($nearbyForeignPlanet->getPlanetCoordinates(), $units, PlanetType::Planet, $assertSuccess);
    }

    /**
     * Check that fleet can be dispatched to an empty position.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToEmptyPosition(UnitCollection $units, bool $assertSuccess): void
    {
        $coordinates = $this->getNearbyEmptyCoordinate();
        $this->checkTargetFleet($coordinates, $units, PlanetType::Planet, $assertSuccess);
    }

    /**
     * Check that fleet can be dispatched to position 16.
     *
     * @param UnitCollection $units
     * @param bool $assertSuccess
     * @return void
     */
    protected function fleetCheckToPosition16(UnitCollection $units, bool $assertSuccess): void
    {
        $currentPlanetCoords = $this->planetService->getPlanetCoordinates();
        $coordinates = new Coordinate($currentPlanetCoords->galaxy, $currentPlanetCoords->system, 16);
        $this->checkTargetFleet($coordinates, $units, PlanetType::Planet, $assertSuccess);
    }

    /**
     * Send a fleet to the first planet moon of the test user.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return void
     */
    protected function sendMissionToFirstPlanetMoon(UnitCollection $units, Resources $resources, bool $assertStatus = true): void
    {
        $coordinates = $this->moonService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::Moon, 0, $assertStatus);
    }

    /**
     * Send a fleet to the second planet of the test user.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return void
     */
    protected function sendMissionToSecondPlanet(UnitCollection $units, Resources $resources, bool $assertStatus = true): void
    {
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::Planet, 0, $assertStatus);
    }

    /**
     * Send a fleet to the first planet debris field of the test user.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return void
     */
    protected function sendMissionToFirstPlanetDebrisField(UnitCollection $units, Resources $resources, bool $assertStatus = true): void
    {
        $coordinates = $this->planetService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::DebrisField, 0, $assertStatus);
    }

    /**
     * Send a fleet to the second planet debris field of the test user.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return void
     */
    protected function sendMissionToSecondPlanetDebrisField(UnitCollection $units, Resources $resources, bool $assertStatus = true): void
    {
        $coordinates = $this->secondPlanetService->getPlanetCoordinates();
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::DebrisField, 0, $assertStatus);
    }

    /**
     * Send a fleet to position 16.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return void
     */
    protected function sendMissionToPosition16(UnitCollection $units, Resources $resources, bool $assertStatus = true): void
    {
        $planetCoords = $this->planetService->getPlanetCoordinates();
        $coordinates = new Coordinate($planetCoords->galaxy, $planetCoords->system, 16);

        // Position 16 means expedition mission, so we set holdingHours to 1 hour which is the default time_holding (in hours) for expeditions.
        $holdingHours = 1;
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::Planet, $holdingHours, $assertStatus);
    }

    /**
     * Send a fleet to a planet of another player.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return PlanetService
     */
    protected function sendMissionToOtherPlayerPlanet(UnitCollection $units, Resources $resources, bool $assertStatus = true): PlanetService
    {
        $nearbyForeignPlanet = $this->getNearbyForeignPlanet();

        $this->dispatchFleet($nearbyForeignPlanet->getPlanetCoordinates(), $units, $resources, PlanetType::Planet, 0, $assertStatus);
        return $nearbyForeignPlanet;
    }

    /**
     * Send a fleet to a new clean planet of another player.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return PlanetService
     */
    protected function sendMissionToOtherPlayerCleanPlanet(UnitCollection $units, Resources $resources, bool $assertStatus = true): PlanetService
    {
        $nearbyForeignCleanPlanet = $this->getNearbyForeignCleanPlanet();

        $this->dispatchFleet($nearbyForeignCleanPlanet->getPlanetCoordinates(), $units, $resources, PlanetType::Planet, 0, $assertStatus);
        return $nearbyForeignCleanPlanet;
    }

    /**
     * Send a fleet to a moon of another player.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return PlanetService
     */
    protected function sendMissionToOtherPlayerMoon(UnitCollection $units, Resources $resources, bool $assertStatus = true): PlanetService
    {
        $nearbyForeignMoon = $this->getNearbyForeignMoon();

        $this->dispatchFleet($nearbyForeignMoon->getPlanetCoordinates(), $units, $resources, PlanetType::Moon, 0, $assertStatus);
        return $nearbyForeignMoon;
    }

    /**
     * Send a fleet to an empty position.
     *
     * @param UnitCollection $units
     * @param Resources $resources
     * @param bool $assertStatus
     * @return Coordinate
     */
    protected function sendMissionToEmptyPosition(UnitCollection $units, Resources $resources, bool $assertStatus = true): Coordinate
    {
        $coordinates = $this->getNearbyEmptyCoordinate();
        $this->dispatchFleet($coordinates, $units, $resources, PlanetType::Planet, 0, $assertStatus);
        return $coordinates;
    }

    /**
     * Call check-target fleet method with the given units and coordinates.
     *
     * @param Coordinate $coordinates
     * @param UnitCollection $units
     * @param PlanetType $planetType The type of the target planet.
     * @param bool $assertSuccess
     * @return void
     */
    protected function checkTargetFleet(Coordinate $coordinates, UnitCollection $units, PlanetType $planetType, bool $assertSuccess): void
    {
        $unitsArray = $this->convertUnitsToArray($units);

        $post = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
            'position' => $coordinates->position,
            'type' => $planetType->value,
            'mission' => $this->missionType,
            '_token' => csrf_token(),
            ...$unitsArray
        ]);

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
     * @param PlanetType $planetType The type of the target planet.
     * @param int $holdingHours The amount of hours to wait before the fleet returns. This e.g. applies to expedition and ACS missions.
     * @param bool $assertStatus
     * @return void
     */
    protected function dispatchFleet(Coordinate $coordinates, UnitCollection $units, Resources $resources, PlanetType $planetType, int $holdingHours = 0, bool $assertStatus = true): void
    {
        $unitsArray = $this->convertUnitsToArray($units);

        $post = $this->post('/ajax/fleet/dispatch/send-fleet', [
            'galaxy' => $coordinates->galaxy,
            'system' => $coordinates->system,
            'position' => $coordinates->position,
            'type' => $planetType->value,
            'mission' => $this->missionType,
            'metal' => $resources->metal->get(),
            'crystal' => $resources->crystal->get(),
            'deuterium' => $resources->deuterium->get(),
            '_token' => csrf_token(),
            'holdingtime' => $holdingHours,
            'speed' => 10,
            ...$unitsArray
        ]);

        $post->assertStatus(200);
        $post->assertJson([
            'success' => $assertStatus,
        ]);

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
