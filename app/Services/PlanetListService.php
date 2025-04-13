<?php

namespace OGame\Services;

use Exception;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet as Planet;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;

/**
 * Class PlanetList.
 *
 * Wrapper object which can contain one or more Planet objects.
 *
 * @package OGame\Services
 */
class PlanetListService
{
    /**
     * Array of planets.
     *
     * @var array<PlanetService>
     */
    private array $planets = [];

    /**
     * Array of moons.
     *
     * @var array<PlanetService>
     */
    private array $moons = [];

    /**
     * PlayerService
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * @var PlanetServiceFactory $planetServiceFactory
     */
    private PlanetServiceFactory $planetServiceFactory;

    /**
     * Planets constructor.
     */
    public function __construct(PlayerService $player, PlanetServiceFactory $planetServiceFactory)
    {
        $this->planetServiceFactory = $planetServiceFactory;
        $this->player = $player;

        // Get all planets (and moons) of user.
        $planets = Planet::where('user_id', $player->getId())->get();
        foreach ($planets as $planetModel) {
            $planetService = $this->planetServiceFactory->makeFromModel($planetModel, $this->player);

            if ($planetService->getPlanetType() === PlanetType::Planet) {
                $this->planets[] = $planetService;
            } elseif ($planetService->getPlanetType() === PlanetType::Moon) {
                $this->moons[] = $planetService;
            }
        }
    }

    /**
     * Get already loaded planet or moon by ID. Invokes an exception if the
     * planet is not found.
     * @throws Exception
     */
    public function getById(int $id): PlanetService
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $id) {
                return $planet;
            }
        }
        foreach ($this->moons as $moon) {
            if ($moon->getPlanetId() === $id) {
                return $moon;
            }
        }

        throw new Exception('Requested planet or moon is not owned by this player.');
    }

    /**
     * Try to find a planet by its coordinates. Returns null if no planet is found.
     */
    public function getPlanetByCoordinates(Coordinate $coordinate): PlanetService|null
    {
        foreach ($this->planets as $planet) {
            $planetCoordinates = $planet->getPlanetCoordinates();
            if ($planetCoordinates->galaxy === $coordinate->galaxy && $planetCoordinates->system === $coordinate->system && $planetCoordinates->position === $coordinate->position) {
                return $planet;
            }
        }

        return null;
    }

    /**
     * Try to find a moon by its coordinates. Returns null if no moon is found.
     */
    public function getMoonByCoordinates(Coordinate $coordinate): PlanetService|null
    {
        foreach ($this->moons as $moon) {
            $moonCoordinates = $moon->getPlanetCoordinates();
            if ($moonCoordinates->galaxy === $coordinate->galaxy && $moonCoordinates->system === $coordinate->system && $moonCoordinates->position === $coordinate->position) {
                return $moon;
            }
        }

        return null;
    }

    /**
     * Checks whether planet with given ID exists and is owned by the current user.
     *
     * @param int $id
     * @return bool
     */
    public function planetExistsAndOwnedByPlayer(int $id): bool
    {
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $id) {
                return true;
            }
        }

        foreach ($this->moons as $moon) {
            if ($moon->getPlanetId() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns current planet of player.
     */
    public function current(): PlanetService
    {
        // Get current planet from PlayerService object.
        $currentPlanetId = $this->player->getCurrentPlanetId();

        // Check if this planet actually exists before returning it.
        foreach ($this->planets as $planet) {
            if ($planet->getPlanetId() === $currentPlanetId) {
                return $planet;
            }
        }

        foreach ($this->moons as $moon) {
            if ($moon->getPlanetId() === $currentPlanetId) {
                return $moon;
            }
        }

        // No valid current planet set, return first planet instead.
        return $this->first();
    }

    /**
     * Get first planet of player.
     *
     * @return PlanetService
     */
    public function first(): PlanetService
    {
        return $this->planets[0];
    }

    /**
     * Return array of all planet and moon objects, ordered so that each planet is followed by its moon (if it exists).
     *
     * @return array<PlanetService>
     */
    public function all(): array
    {
        $result = [];

        // First add all planets
        foreach ($this->planets as $planet) {
            $result[] = $planet;

            // Check if this planet has a moon
            if ($planet->hasMoon()) {
                $result[] = $planet->moon();
            }
        }

        return $result;
    }

    /**
     * Return array of all planet objects.
     *
     * @return array<PlanetService>
     */
    public function allPlanets(): array
    {
        return $this->planets;
    }

    /**
     * Return array of all moon objects.
     *
     * @return array<PlanetService>
     */
    public function allMoons(): array
    {
        return $this->moons;
    }

    /**
     * Return array of planet ids.
     *
     * @return array<int>
     */
    public function allIds(): array
    {
        $planetIds = [];
        foreach ($this->planets as $planet) {
            $planetIds[] = $planet->getPlanetId();
        }

        foreach ($this->moons as $moon) {
            $planetIds[] = $moon->getPlanetId();
        }

        return $planetIds;
    }

    /**
     * Get amount of planets.
     *
     * @return int
     */
    public function planetCount(): int
    {
        return count($this->planets);
    }

    /**
     * Get amount of planets and moons combined.
     *
     * @return int
     */
    public function allCount(): int
    {
        return count($this->planets) + count($this->moons);
    }
}
