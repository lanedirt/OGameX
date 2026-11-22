<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Services\PlanetService;

/**
 * Helpers for creating foreign players, planets and moons at collision-safe coordinates
 * (replacing the order-dependent getNearbyForeignPlanet()/getNearbyForeignMoon() probes).
 *
 * @method \OGame\Models\Planet\Coordinate getNearbyEmptyCoordinate(int $minPosition = 4, int $maxPosition = 12, int $minSystemDistance = 0)
 */
trait CreatesForeignFixtures
{
    /**
     * Creates a planet for the given user at a collision-safe coordinate.
     *
     * Uses a DB-existence check and defaults to positions 13-15 (outside the allocator's
     * assigned range of 4-12) so that this planet never collides with a home planet placed
     * by the allocator during registration — even when two test users land in the same system.
     *
     * @param int $userId The user_id to assign the new planet to.
     * @param int $minPosition Lower bound for position search (default 13).
     * @param int $maxPosition Upper bound for position search (default 15).
     * @param int $minSystemDistance Minimum system offset from the current player's planet (default 0 = any system).
     */
    protected function createPlanetAtSafeCoordinate(int $userId, int $minPosition = 13, int $maxPosition = 15, int $minSystemDistance = 0): PlanetService
    {
        $coordinate = $this->getNearbyEmptyCoordinate($minPosition, $maxPosition, $minSystemDistance);

        $planet = Planet::factory()->create([
            'user_id' => $userId,
            'galaxy'  => $coordinate->galaxy,
            'system'  => $coordinate->system,
            'planet'  => $coordinate->position,
            'time_last_update' => (int)Date::now()->timestamp,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $playerService = resolve(PlayerServiceFactory::class)->make($userId);

        return $planetServiceFactory->makeForPlayer($playerService, $planet->id);
    }

    /**
     * Create a non-admin foreign player with a single planet at a collision-safe coordinate
     * near the current player. Replaces the order-dependent getNearbyForeignPlanet() probe.
     */
    protected function createForeignPlanet(): PlanetService
    {
        $foreignUser = User::factory()->create();
        $playerService = resolve(PlayerServiceFactory::class)->make($foreignUser->id);
        $coordinate = $this->getNearbyEmptyCoordinate(13, 15);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);

        $planet = $planetServiceFactory->createAdditionalPlanetForPlayer($playerService, $coordinate);

        // Reload the player so its planets collection includes the new planet. Without this,
        // a moon created for this planet later resolves a stale (empty) planets collection and
        // throws "No planet found for this moon" during battle processing.
        $playerService->load($foreignUser->id);

        return $planet;
    }

    /**
     * Create a foreign planet owned by another player with a moon at its coordinates.
     * Replaces the order-dependent getNearbyForeignMoon() probe.
     */
    protected function createForeignMoon(): PlanetService
    {
        $foreignPlanet = $this->createForeignPlanet();
        $planetServiceFactory = resolve(PlanetServiceFactory::class);

        return $planetServiceFactory->createMoonForPlanet($foreignPlanet, 2000000, 20);
    }
}
