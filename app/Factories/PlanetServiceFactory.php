<?php

namespace OGame\Factories;

use Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use RuntimeException;

class PlanetServiceFactory
{
    /**
     * Cached instances of planetService.
     *
     * @var array<int, PlanetService>
     */
    protected array $instancesById = [];

    /**
     * Cached instances of planetService.
     *
     * @var array<string, PlanetService>
     */
    protected array $instancesByCoordinate = [];

    /**
     * SettingsService.
     *
     * @var SettingsService
     */
    protected SettingsService $settings;

    /**
     * PlanetServiceFactory constructor.
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settings = $settingsService;
    }

    /**
     * Returns a planetService either from local instances cache or creates a new one. Note:
     * it is advised to use makeForPlayer() method if playerService is already available.
     *
     * @param int $planetId
     * @param bool $reloadCache Whether to force retrieve the object and reload the cache. Defaults to false.
     * Note: for certain usecases such as updating planets/missions after gaining a lock, its essential to set this to
     * true in order to get the latest data from the database and update the cache accordingly to avoid stale data.
     *
     * @return PlanetService|null
     */
    public function make(int $planetId, bool $reloadCache = false): PlanetService|null
    {
        if ($reloadCache || !isset($this->instancesById[$planetId])) {
            try {
                $planetService = resolve(PlanetService::class, ['player' => null, 'planet_id' => $planetId]);
                $this->instancesById[$planetId] = $planetService;

                if ($planetService->planetInitialized()) {
                    $this->instancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
                }
            } catch (BindingResolutionException $e) {
                return null;
            }
        }

        return $this->instancesById[$planetId];
    }

    /**
     * Returns a planetService for a playerService that has already been loaded. This saves a
     * call to the database to get the player object.
     *
     * @param PlayerService $player
     * @param int $planetId
     * @param bool $useCache Whether to use the cache or not. Defaults to true. Note: for certain usecases
     *  such as updating planets/missions after gaining a lock, its essential to set this to false in order
     *  to get the latest data from the database.
     *
     * @return PlanetService
     */
    public function makeForPlayer(PlayerService $player, int $planetId, bool $useCache = true): PlanetService
    {
        if (!$useCache || !isset($this->instancesById[$planetId])) {
            try {
                $planetService = resolve(PlanetService::class, ['player' => $player, 'planet_id' => $planetId]);
                $this->instancesById[$planetId] = $planetService;
            } catch (BindingResolutionException $e) {
                throw new RuntimeException('Class not found: ' . PlayerService::class);
            }

            if ($planetService->planetInitialized()) {
                $this->instancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
            }
        }

        return $this->instancesById[$planetId];
    }

    /**
     * Returns a planetService for a given coordinate.
     *
     * @param Coordinate $coordinate
     * @param bool $useCache Whether to use the cache or not. Defaults to true. Note: for certain usecases
     *   such as updating planets/missions after gaining a lock, its essential to set this to false in order
     *   to get the latest data from the database.
     *
     * @return ?PlanetService
     */
    public function makeForCoordinate(Coordinate $coordinate, bool $useCache = true): ?PlanetService
    {
        if (!$useCache || !isset($this->instancesByCoordinate[$coordinate->asString()])) {
            $planetId = Planet::where('galaxy', $coordinate->galaxy)
                ->where('system', $coordinate->system)
                ->where('planet', $coordinate->position)
                ->value('id');

            if (!$planetId) {
                return null;
            }

            try {
                $planetService = resolve(PlanetService::class, ['player' => null, 'planet_id' => $planetId]);
                $this->instancesByCoordinate[$coordinate->asString()] = $planetService;
                $this->instancesById[$planetService->getPlanetId()] = $planetService;
                return $this->instancesByCoordinate[$coordinate->asString()];
            } catch (BindingResolutionException $e) {
                throw new RuntimeException('Class not found: ' . PlayerService::class);
            }
        }

        return $this->instancesByCoordinate[$coordinate->asString()];
    }

    /**
     * Description of planet not already colonized
     *
     * @param Coordinate $coordinates
     *
     * @return string
     */
    public function getPlanetDescription(Coordinate $coordinates): string
    {
        if ($coordinates->position >= 1 && $coordinates->position <= 3) {
            // Nearest planet from the sun.
            return __('t_galaxy.planet.description.nearest');
        } elseif ($coordinates->position >= 4 && $coordinates->position <= 6) {
            // Normal planet.
            return __('t_galaxy.planet.description.normal');
        } elseif ($coordinates->position >= 7 && $coordinates->position <= 9) {
            // Biggest planet.
            return __('t_galaxy.planet.description.biggest');
        } elseif ($coordinates->position >= 10 && $coordinates->position <= 12) {
            // Normal planet.
            return __('t_galaxy.planet.description.normal');
        } else {
            // ($coordinates->position >= 13 && $coordinates->position <= 15)
            // Farthest planet from the sun.
            return __('t_galaxy.planet.description.farthest');
        }
    }

    /**
     * Determine next available new planet position.
     *
     * @return Coordinate
     */
    public function determineNewPlanetPosition(): Coordinate
    {
        $lastAssignedGalaxy = (int)$this->settings->get('last_assigned_galaxy', 1);
        $lastAssignedSystem = (int)$this->settings->get('last_assigned_system', 1);

        $galaxy = $lastAssignedGalaxy;
        $system = $lastAssignedSystem;

        $tryCount = 0;
        while ($tryCount < 100) {
            $tryCount++;
            $planetCount = Planet::where('galaxy', $galaxy)->where('system', $system)->count();

            // 70% of the time max 2 planets per system, 30% of the time max 3.
            if ($planetCount < ((rand(1, 10) < 7) ? 2 : 3)) {
                // Find a random position between 4 and 12 that's not already taken
                $positions = range(4, 12);
                shuffle($positions); // Randomize the positions array

                foreach ($positions as $position) {
                    $existingPlanet = Planet::where('galaxy', $galaxy)->where('system', $system)->where('planet', $position)->first();
                    if (!$existingPlanet) {
                        return new Planet\Coordinate($galaxy, $system, $position);
                    }
                }

                // Increment system and galaxy accordingly if no position is found
                $system++;
                if ($system > 499) {
                    $system = 1;
                    $galaxy++;
                }
            } else {
                // Increment system and galaxy if the current one is full
                $system++;
                if ($system > 499) {
                    $system = 1;
                    $galaxy++;
                }
            }
        }

        // If more than 100 tries have been done with no success, give up.
        throw new RuntimeException('Unable to determine new planet position.');
    }

    /**
     * Creates a new random initial planet for a player and then return the planetService instance for it.
     *
     * @param PlayerService $player
     * @param string $planetName
     * @return PlanetService
     */
    public function createInitialForPlayer(PlayerService $player, string $planetName): PlanetService
    {
        // Use lock when using the determineNewPlanetPosition so that no other process can get the same position
        // which would result in an error.
        $lockKey = "planet_create_lock";
        $lock = Cache::lock($lockKey, 10);

        // Try to acquire the lock, waiting for up to 10 seconds.
        if ($lock->block(10)) {
            try {
                $new_position = $this->determineNewPlanetPosition();
                if (empty($new_position->galaxy) || empty($new_position->system) || empty($new_position->position)) {
                    // Failed to get a new position for the to be created planet. Throw exception.
                    throw new RuntimeException('Unable to determine new planet position.');
                }

                $createdPlanet = $this->createPlanet($player, $new_position, $planetName);

                // Update settings with the last assigned galaxy and system if they changed.
                $this->settings->set('last_assigned_galaxy', $createdPlanet->getPlanetCoordinates()->galaxy);
                $this->settings->set('last_assigned_system', $createdPlanet->getPlanetCoordinates()->system);

                // Reload player object so the new planet is added to the planetList.
                $player->load($player->getId());

                return $createdPlanet;
            } finally {
                $lock->release();
            }
        } else {
            throw new RuntimeException('Unable to acquire lock for planet creation.');
        }
    }

    /**
     * Creates a new planet for a player at the given coordinate and then return the planetService instance for it.
     *
     * @param PlayerService $player
     * @param Coordinate $coordinate
     * @return PlanetService
     */
    public function createAdditionalForPlayer(PlayerService $player, Coordinate $coordinate): PlanetService
    {
        return $this->createPlanet($player, $coordinate, 'Colony');
    }

    private function createPlanet(PlayerService $player, Coordinate $new_position, string $planet_name): PlanetService
    {
        // Position is available
        $planet = new Planet();
        $planet->user_id = $player->getId();
        $planet->name = $planet_name;
        $planet->galaxy = $new_position->galaxy;
        $planet->system = $new_position->system;
        $planet->planet = $new_position->position;
        $planet->destroyed = 0;
        $planet->field_current = 0;
        // TODO: figure out how to determine the properties below so it matches the game logic.
        $planet->planet_type = 1; //?
        $planet->diameter = 300;
        $planet->field_max = rand(140, 250);
        $planet->temp_min = rand(0, 100);
        $planet->temp_max = $planet->temp_min + 40;

        $planet->metal = 500;
        $planet->crystal = 500;
        $planet->deuterium = 0;

        // Set default mine production percentages to 10 (100%).
        $planet->metal_mine_percent = 10;
        $planet->crystal_mine_percent = 10;
        $planet->deuterium_synthesizer_percent = 10;
        $planet->solar_plant_percent = 10;
        $planet->fusion_plant_percent = 10;
        $planet->solar_satellite_percent = 10;

        $planet->time_last_update = (int)Carbon::now()->timestamp;
        $planet->save();

        // Now make and return the planetService.
        return $this->makeForPlayer($player, $planet->id);
    }
}
