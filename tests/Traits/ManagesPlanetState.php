<?php

namespace Tests\Traits;

use Exception;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\ObjectService;
use OGame\Services\OfficerService;

/**
 * Helpers for reading and mutating the current test user's planets, resources, object
 * levels, units and research, plus switching the active planet context and time.
 *
 * @property \OGame\Services\PlanetService $planetService
 * @property \OGame\Services\PlanetService|null $secondPlanetService
 * @property \Carbon\Carbon $defaultTestTime
 * @method \OGame\Models\Planet\Coordinate getSafeEmptyCoordinate(\OGame\Models\Planet\Coordinate $anchor, int $minPosition = 4, int $maxPosition = 12, int $minSystemDistance = 0)
 * @method never fail(string $message = '')
 * @method \Illuminate\Testing\TestResponse get(string $uri, array $headers = [])
 * @method void travelTo(mixed $date)
 */
trait ManagesPlanetState
{
    /**
     * Gets a nearby empty coordinate for the current user. This is useful for testing interactions towards empty planets.
     */
    protected function getNearbyEmptyCoordinate(int $minPosition = 4, int $maxPosition = 12, int $minSystemDistance = 0): Coordinate
    {
        return $this->getSafeEmptyCoordinate(
            $this->planetService->getPlanetCoordinates(),
            $minPosition,
            $maxPosition,
            $minSystemDistance
        );
    }

    /**
     * Add resources to current users current planet.
     */
    protected function planetAddResources(Resources $resources): void
    {
        $this->planetService->addResources($resources);
    }

    /**
     * Deduct resources from current users current planet.
     *
     * @throws Exception
     */
    protected function planetDeductResources(Resources $resources): void
    {
        $this->planetService->deductResources($resources);
    }

    /**
     * Set object level on current users current planet.
     */
    protected function planetSetObjectLevel(string $machineName, int $objectLevel): void
    {
        $object = ObjectService::getObjectByMachineName($machineName);
        $this->planetService->setObjectLevel($object->id, $objectLevel, true);
    }

    /**
     * Add units to current users current planet.
     */
    protected function planetAddUnit(string $machineName, int $amount): void
    {
        $this->planetService->addUnit($machineName, $amount);
    }

    /**
     * Set object level on current users current planet.
     */
    protected function playerSetResearchLevel(string $machineName, int $objectLevel): void
    {
        try {
            $player = $this->planetService->getPlayer();
            if ($player === null) {
                $this->fail('Current planet has no owner.');
            }
            $player->setResearchLevel($machineName, $objectLevel);
        } catch (Exception $e) {
            $this->fail('Failed to set research level for player. Error: ' . $e->getMessage());
        }
    }

    /**
     * Switch the active planet context to the first planet of the current user which affects
     * interactive requests done such as building queue items or canceling build queue items.
     */
    /**
     * Activate the Commander for the current player.
     *
     * Queueing more than one building at a time is a Commander benefit, so tests that
     * line up multiple build orders need her active.
     */
    protected function playerActivateCommander(int $days = 7): void
    {
        $officerService = resolve(OfficerService::class);
        $user = User::findOrFail($this->currentUserId);

        $officer = $officerService->getOfficer($user);
        $officer->activate('commander', $days);
        $officer->save();

        $officerService->clearCache($user);
    }

    protected function switchToFirstPlanet(): void
    {
        $response = $this->get('/overview?cp=' . $this->planetService->getPlanetId());
        $response->assertStatus(200);
    }

    /**
     * Switch the active planet context to the second planet of the current user which affects
     * interactive requests done such as building queue items or canceling build queue items.
     */
    protected function switchToSecondPlanet(): void
    {
        $secondPlanetService = $this->secondPlanetService;
        if ($secondPlanetService === null) {
            $this->fail('Second planet service is not initialized.');
        }
        $response = $this->get('/overview?cp=' . $secondPlanetService->getPlanetId());
        $response->assertStatus(200);
    }

    /**
     * Add helper method to reset time to default.
     */
    protected function resetTestTime(): void
    {
        $this->travelTo($this->defaultTestTime);
    }
}
