<?php

namespace OGame\Factories;

use Cache;
use Illuminate\Support\Carbon;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use OGame\Models\Enums\PlanetType;
use RuntimeException;

/**
 * Factory class for creating and caching planetService instances. A planetService can represent either a planet (PlanetType::Planet) or a moon (PlanetType::Moon).
 */
class PlanetServiceFactory
{
    /**
     * Cached instances of planetService for planets.
     *
     * @var array<string, PlanetService>
     */
    protected array $planetInstancesByCoordinate = [];

    /**
     * Cached instances of planetService for moons.
     *
     * @var array<string, PlanetService>
     */
    protected array $moonInstancesByCoordinate = [];

    /**
     * Cached instances of planetService by id (can be either planet or moon).
     *
     * @var array<int, PlanetService>
     */
    protected array $instancesById = [];

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
            /** @var PlanetService */
            $planetService = resolve(PlanetService::class, [
                'player' => null,
                'planet_id' => $planetId,
                'planet' => null,
            ]);

            // Verify planet type is valid
            if (!in_array($planetService->getPlanetType(), [PlanetType::Planet, PlanetType::Moon])) {
                return null;
            }

            $this->instancesById[$planetId] = $planetService;

            if ($planetService->planetInitialized()) {
                if ($planetService->getPlanetType() === PlanetType::Planet) {
                    $this->planetInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
                } else {
                    $this->moonInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
                }
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
            /** @var PlanetService $planetService */
            $planetService = resolve(PlanetService::class, [
                'player' => $player,
                'planet' => null,
                'planet_id' => $planetId,
            ]);
            $this->instancesById[$planetId] = $planetService;

            if ($planetService->planetInitialized()) {
                if ($planetService->getPlanetType() === PlanetType::Planet) {
                    $this->planetInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
                } else {
                    $this->moonInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
                }
            }
        }

        return $this->instancesById[$planetId];
    }

    /**
     * Returns a planetService for a playerService and planet model that has already been loaded.
     * This saves database queries when planet data is already available.
     *
     * @param Planet $planet
     * @param PlayerService|null $player PlayerService instance or null if not available.
     * @return PlanetService
     */
    public function makeFromModel(Planet $planet, PlayerService|null $player = null): PlanetService
    {
        /** @var PlanetService */
        $planetService = resolve(PlanetService::class, [
            'player' => $player,
            'planet' => $planet,
            'planet_id' => null,
        ]);
        $this->instancesById[$planet->id] = $planetService;

        if ($planetService->planetInitialized()) {
            if ($planetService->getPlanetType() === PlanetType::Planet) {
                $this->planetInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
            } else {
                $this->moonInstancesByCoordinate[$planetService->getPlanetCoordinates()->asString()] = $planetService;
            }
        }

        return $this->instancesById[$planet->id];
    }

    /**
     * Returns a planetService for a given coordinate.
     *
     * @param Coordinate $coordinate
     * @param bool $useCache Whether to use the cache or not. Defaults to true.
     * @param PlanetType $type The type of planet object to look for. Can be PlanetType::Planet or PlanetType::Moon. Defaults to Planet.
     *
     * @return PlanetService|null
     */
    public function makeForCoordinate(Coordinate $coordinate, bool $useCache = true, PlanetType $type = PlanetType::Planet): PlanetService|null
    {
        $instancesArray = $type === PlanetType::Planet ?
            $this->planetInstancesByCoordinate :
            $this->moonInstancesByCoordinate;

        if (!$useCache || !isset($instancesArray[$coordinate->asString()])) {
            $planet = Planet::where('galaxy', $coordinate->galaxy)
                ->where('system', $coordinate->system)
                ->where('planet', $coordinate->position)
                ->where('planet_type', $type->value)
                ->first();

            if (!$planet) {
                return null;
            }

            /** @var PlanetService */
            $planetService = resolve(PlanetService::class, [
                'player' => null,
                'planet' => null,
                'planet_id' => $planet->id,
            ]);

            if ($type === PlanetType::Planet) {
                $this->planetInstancesByCoordinate[$coordinate->asString()] = $planetService;
            } else {
                $this->moonInstancesByCoordinate[$coordinate->asString()] = $planetService;
            }

            $this->instancesById[$planetService->getPlanetId()] = $planetService;

            return $type === PlanetType::Planet ?
                $this->planetInstancesByCoordinate[$coordinate->asString()] :
                $this->moonInstancesByCoordinate[$coordinate->asString()];
        }

        return $instancesArray[$coordinate->asString()];
    }

    /**
     * Convenience method to get a planet at the given coordinate.
     *
     * @param Coordinate $coordinate
     * @param bool $useCache
     * @return PlanetService|null
     */
    public function makePlanetForCoordinate(Coordinate $coordinate, bool $useCache = true): PlanetService|null
    {
        return $this->makeForCoordinate($coordinate, $useCache, PlanetType::Planet);
    }

    /**
     * Convenience method to get a moon at the given coordinate.
     *
     * @param Coordinate $coordinate
     * @param bool $useCache
     * @return PlanetService|null
     */
    public function makeMoonForCoordinate(Coordinate $coordinate, bool $useCache = true): PlanetService|null
    {
        return $this->makeForCoordinate($coordinate, $useCache, PlanetType::Moon);
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
                        return new Coordinate($galaxy, $system, $position);
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
    public function createInitialPlanetForPlayer(PlayerService $player, string $planetName): PlanetService
    {
        // Use lock when using the determineNewPlanetPosition so that no other process can get the same position
        $lockKey = "planet_create_lock";
        $lock = Cache::lock($lockKey, 10);

        // Try to acquire the lock, waiting for up to 10 seconds.
        if ($lock->block(10)) {
            try {
                $new_position = $this->determineNewPlanetPosition();
                if (empty($new_position->galaxy) || empty($new_position->system) || empty($new_position->position)) {
                    throw new RuntimeException('Unable to determine new planet position.');
                }

                $createdPlanet = $this->createPlanet($player, $new_position, $planetName, PlanetType::Planet);

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
    public function createAdditionalPlanetForPlayer(PlayerService $player, Coordinate $coordinate): PlanetService
    {
        return $this->createPlanet($player, $coordinate, 'Colony', PlanetType::Planet);
    }

    /**
     * Creates a new moon for an existing planet and then return the planetService instance for the newly created moon.
     *
     * @param PlanetService $planet The planet to create the moon for.
     * @return PlanetService The new moon.
     */
    public function createMoonForPlanet(PlanetService $planet): PlanetService
    {
        return $this->createPlanet($planet->getPlayer(), $planet->getPlanetCoordinates(), 'Moon', PlanetType::Moon);
    }

    /**
     * Creates a new planet/moon for a player at the given coordinate.
     * @param PlayerService $player
     * @param Coordinate $new_position
     * @param string $planet_name
     * @param PlanetType $planet_type
     * @return PlanetService
     */
    private function createPlanet(PlayerService $player, Coordinate $new_position, string $planet_name, PlanetType $planet_type): PlanetService
    {
        $planet = new Planet();
        $planet->user_id = $player->getId();
        $planet->name = $planet_name;
        $planet->galaxy = $new_position->galaxy;
        $planet->system = $new_position->system;
        $planet->planet = $new_position->position;
        $planet->destroyed = 0;
        $planet->field_current = 0;
        $planet->planet_type = $planet_type->value;
        $planet->time_last_update = (int)Carbon::now()->timestamp;

        if ($planet_type === PlanetType::Moon) {
            $this->setupMoonProperties($planet);
        } else {
            $is_first_planet = $player->planets->planetCount() == 0;

            $this->setupPlanetProperties($planet, $is_first_planet);
        }

        $planet->save();

        return $this->makeForPlayer($player, $planet->id);
    }

    /**
     * Sets up moon-specific properties.
     * @param Planet $planet
     */
    private function setupMoonProperties(Planet $planet): void
    {
        // TODO: moon diameter should be made dependent on the moon chance percentage
        // that resulted from battle that created this moon.
        $planet->diameter = rand(7500, 8888);
        $planet->field_max = 1;

        // TODO: temperature range should be dependent on the moon position.
        $planet->temp_max = rand(0, 100);
        $planet->temp_min = $planet->temp_max - 40;

        // Moons start with no resources.
        $planet->metal = 0;
        $planet->crystal = 0;
        $planet->deuterium = 0;
    }

    /**
     * Sets up planet-specific properties.
     * @param Planet $planet
     */
    private function setupPlanetProperties(Planet $planet, bool $is_first_planet = false): void
    {
        $planet_data = $this->planetData($planet->planet, $is_first_planet);

        // Random field count between the min and max values and add the Server planet fields bonus setting.
        $planet->field_max = rand($planet_data['fields'][0], $planet_data['fields'][1]) + $this->settings->planetFieldsBonus();
        $planet->diameter = (int) (36.14 * $planet->field_max + 5697.23);

        // Random temperature between the min and max values is assigned to temp_max, then temp_min is calculated as temp_max - 40.
        $planet->temp_max = rand($planet_data['temperature'][0], $planet_data['temperature'][1]);
        $planet->temp_min = $planet->temp_max - 40;

        // Starting resources for planets.
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
    }

    /**
     * Returns an array of planet data:
     *   - fields => int[]
     *   - temperature => int[]
     *
     * @return array{
     *     fields: int[],
     *     temperature: int[],
     * }
     */
    public function planetData(int $planetPosition, bool $is_first_planet): array
    {
        // Each planet position (1-15) has its own data: field range, temperature range,
        // and production bonuses for metal, crystal, and deuterium.
        // The base production percent is 10 (which is effectively 100%).
        // "Max Deterium production" on position 15 is interpreted as a higher deuterium bonus.
        // You can tweak these values according to your game's balance.

        $data = [
            // Position 1
            1 => [
                'fields' => [96, 172],           // min:96, max:172
                'temperature' => [220, 260],     // min:220°C, max:260°C
            ],
            // Position 2
            2 => [
                'fields' => [104, 176],
                'temperature' => [170, 210],
            ],
            // Position 3
            3 => [
                'fields' => [112, 182],
                'temperature' => [120, 160],
            ],
            // Position 4
            4 => [
                'fields' => [118, 208],
                'temperature' => [70, 110],
            ],
            // Position 5
            5 => [
                'fields' => [133, 232],
                'temperature' => [60, 100],
            ],
            // Position 6
            6 => [
                'fields' => [146, 242],
                'temperature' => [50, 90],
            ],
            // Position 7
            7 => [
                'fields' => [152, 248],
                'temperature' => [40, 80],
            ],
            // Position 8
            8 => [
                'fields' => [156, 252],
                'temperature' => [30, 70],
            ],
            // Position 9
            9 => [
                'fields' => [150, 246],
                'temperature' => [20, 60],
            ],
            // Position 10
            10 => [
                'fields' => [142, 232],
                'temperature' => [10, 50],
            ],
            // Position 11
            11 => [
                'fields' => [136, 210],
                'temperature' => [0, 40],
            ],
            // Position 12
            12 => [
                'fields' => [125, 186],
                'temperature' => [-10, 30],
            ],
            // Position 13
            13 => [
                'fields' => [114, 172],
                'temperature' => [-50, -10],
            ],
            // Position 14
            14 => [
                'fields' => [100, 168],
                'temperature' => [-90, -50],
            ],
            // Position 15
            15 => [
                'fields' => [90, 164],
                'temperature' => [-130, -90],
            ],
        ];

        //first_planet static data
        if ($is_first_planet) {
            return $data[$planetPosition] ?? [
                'fields' => [163, 163],
                'temperature' => [20, 60],
            ];
        }

        // If the position doesn't exist, return some default values
        // Deuterium can be adjusted according to planet temperature as needed
        return $data[$planetPosition] ?? [
            'fields' => [100, 150],
            'temperature' => [0, 40],
        ];
    }
}
