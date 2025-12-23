<?php

namespace OGame\Factories;

use Cache;
use Illuminate\Support\Carbon;
use OGame\GameConstants\UniverseConstants;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
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
     * PlayerServiceFactory.
     *
     * @var PlayerServiceFactory
     */
    protected PlayerServiceFactory $playerServiceFactory;

    /**
     * PlanetServiceFactory constructor.
     */
    public function __construct(SettingsService $settingsService, PlayerServiceFactory $playerServiceFactory)
    {
        $this->settings = $settingsService;
        $this->playerServiceFactory = $playerServiceFactory;
    }

    /**
     * Don't serialize anything - settings service will be resolved on wakeup.
     *
     * @return array
     */
    public function __sleep(): array
    {
        return [];
    }

    /**
     * Wake up after unserialization - reinitialize settings service and cache arrays.
     *
     * @return void
     */
    public function __wakeup(): void
    {
        $this->planetInstancesByCoordinate = [];
        $this->moonInstancesByCoordinate = [];
        $this->instancesById = [];
        $this->settings = app()->make(SettingsService::class);
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
     * Uses density tiers to progressively fill the universe:
     * - Tier 1: 2-3 planets per system (initial distribution)
     * - Tier 2: 6-7 planets per system (after first max galaxy wrap-around)
     * - Tier 3: 9 planets per system (maximum density, positions 4-12)
     *
     * Note: In case tier 3 cannot find any empty positions and the universe is really full,
     * new planets will be unable to be created and will start throwing exceptions.
     *
     * In this case the server admin either needs to increase the number of galaxies in
     * server settings or disable user registration and create a new separate server.
     *
     * @return Coordinate
     */
    public function determineNewPlanetPosition(): Coordinate
    {
        $maxGalaxies = $this->settings->numberOfGalaxies();
        $lastAssignedGalaxy = (int)$this->settings->get('last_assigned_galaxy', 1);
        $lastAssignedSystem = (int)$this->settings->get('last_assigned_system', 1);
        $densityTier = (int)$this->settings->get('planet_density_tier', 1);

        // Ensure starting galaxy is within valid bounds (wrap if needed)
        $galaxy = $lastAssignedGalaxy > $maxGalaxies ? UniverseConstants::MIN_GALAXY : $lastAssignedGalaxy;
        $system = $lastAssignedSystem;

        $maxPlanetsForTier = $this->getMaxPlanetsForDensityTier($densityTier);

        $tryCount = 0;
        while ($tryCount < 100) {
            $tryCount++;
            $planetCount = Planet::where('galaxy', $galaxy)->where('system', $system)->count();

            if ($planetCount < $maxPlanetsForTier) {
                // Find a random position between 4 and 12 that's not already taken
                $positions = range(4, 12);
                shuffle($positions);

                foreach ($positions as $position) {
                    $existingPlanet = Planet::where('galaxy', $galaxy)->where('system', $system)->where('planet', $position)->first();
                    if (!$existingPlanet) {
                        // Update last assigned position for next time
                        $this->settings->set('last_assigned_galaxy', $galaxy);
                        $this->settings->set('last_assigned_system', $system);
                        return new Coordinate($galaxy, $system, $position);
                    }
                }
            }

            // System is full, move to next system
            $system++;
            if ($system > UniverseConstants::MAX_SYSTEM_COUNT) {
                // Galaxy is full, move to next galaxy
                $system = UniverseConstants::MIN_SYSTEM;
                $galaxy++;

                // Max amount of galaxies reached: start from first galaxy and increase density tier
                if ($galaxy > $maxGalaxies) {
                    $galaxy = UniverseConstants::MIN_GALAXY;
                    $densityTier = min($densityTier + 1, 3);
                    $this->settings->set('planet_density_tier', $densityTier);
                    $maxPlanetsForTier = $this->getMaxPlanetsForDensityTier($densityTier);
                }
            }
        }

        // If more than 100 tries have been done with no success, give up.
        throw new RuntimeException('Unable to determine new planet position. Universe may be full.');
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
     * Create a planet at a specific coordinate (bypasses position determination).
     * Used for special accounts like Legor.
     *
     * @param PlayerService $player
     * @param Coordinate $coordinate
     * @param string $planetName
     * @return PlanetService
     * @throws RuntimeException if position is occupied
     */
    public function createPlanetAtPosition(PlayerService $player, Coordinate $coordinate, string $planetName): PlanetService
    {
        if ($this->planetExistsAtCoordinate($coordinate)) {
            throw new RuntimeException("Position {$coordinate->asString()} is already occupied");
        }

        return $this->createPlanet($player, $coordinate, $planetName, PlanetType::Planet);
    }

    /**
     * Check if a planet exists at the given coordinate.
     *
     * @param Coordinate $coordinate
     * @return bool
     */
    public function planetExistsAtCoordinate(Coordinate $coordinate): bool
    {
        return Planet::where('galaxy', $coordinate->galaxy)
            ->where('system', $coordinate->system)
            ->where('planet', $coordinate->position)
            ->exists();
    }

    /**
     * Creates a new moon for an existing planet and then return the planetService instance for the newly created moon.
     *
     * @param PlanetService $planet The planet to create the moon for.
     * @param int $debrisAmount The total amount of debris (metal + crystal + deuterium) that resulted from the battle.
     * @param int $moonChance The moon chance percentage that resulted from the battle.
     * @param int|null $xFactor Optional x factor (10-20) for moon size formula. If null, random value is used.
     * @return PlanetService The new moon.
     */
    public function createMoonForPlanet(PlanetService $planet, int $debrisAmount, int $moonChance, int|null $xFactor = null): PlanetService
    {
        return $this->createPlanet($planet->getPlayer(), $planet->getPlanetCoordinates(), 'Moon', PlanetType::Moon, $debrisAmount, $moonChance, $xFactor);
    }

    /**
     * Creates a new planet/moon for a player at the given coordinate.
     * @param PlayerService $player
     * @param Coordinate $new_position
     * @param string $planet_name
     * @param PlanetType $planet_type
     * @param int $debrisAmount The total debris amount (only used for moons).
     * @param int $moonChance The moon chance percentage (only used for moons).
     * @param int|null $xFactor Optional x factor for moon size formula (only used for moons).
     * @return PlanetService
     */
    private function createPlanet(PlayerService $player, Coordinate $new_position, string $planet_name, PlanetType $planet_type, int $debrisAmount = 0, int $moonChance = 0, int|null $xFactor = null): PlanetService
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
            $this->setupMoonProperties($planet, $debrisAmount, $moonChance, $xFactor, $player);
        } else {
            $is_first_planet = $player->planets->planetCount() == 0;

            $this->setupPlanetProperties($planet, $is_first_planet, $player);
        }

        $planet->save();

        return $this->makeForPlayer($player, $planet->id);
    }

    /**
     * Sets up moon-specific properties using the official OGame formula.
     *
     * Formula: diameter = floor((x + 3 * debris / 100000) ^ 0.5 * 1000) km
     * Where x is a value between 10 and 20 (random if not specified).
     *
     * Note: During moon chance events (e.g., 30% max instead of 20%), only the probability
     * of moon creation increases, not the size.
     *
     * @param Planet $planet
     * @param int $debrisAmount Total resources in debris field (metal + crystal + deuterium)
     * @param int $moonChance Moon chance percentage from the battle (unused for size, kept for future features)
     * @param int|null $xFactor Optional x factor (10-20). If null, random value is used.
     * @param PlayerService|null $player Optional PlayerService to avoid reloading from database
     */
    private function setupMoonProperties(Planet $planet, int $debrisAmount, int $moonChance, int|null $xFactor = null, PlayerService|null $player = null): void
    {
        // Calculate moon diameter using official formula:
        // diameter = floor((x + 3 * debris / 100000) ^ 0.5 * 1000)
        // where x is between 10 and 20 (random if not specified)
        if ($xFactor === null) {
            $x = rand(10, 20);
        } else {
            // Clamp x factor to valid range (10-20)
            $x = max(10, min(20, $xFactor));
        }

        // Apply the official formula
        $diameter = (int)floor(pow($x + (3 * $debrisAmount / 100000), 0.5) * 1000);

        // Cap the maximum diameter at what x=20 with 2M debris would generate (8944 km)
        // This ensures moons remain destructible by Deathstars
        $maxDiameter = (int)floor(pow(20 + (3 * 2000000 / 100000), 0.5) * 1000); // = 8944 km
        $planet->diameter = min($diameter, $maxDiameter);

        // Moon field size is base 1 + General class bonus (+5)
        // Only apply character class bonus if user has selected a class
        $moonFieldsBonus = 0;
        if ($planet->user_id) {
            // Use provided player if available, otherwise load from database
            if (!$player) {
                $player = $this->playerServiceFactory->make($planet->user_id, true);
            }
            if ($player && $player->getUser()->character_class !== null) {
                $characterClassService = app(\OGame\Services\CharacterClassService::class);
                $moonFieldsBonus = $characterClassService->getAdditionalMoonFields($player->getUser());
            }
        }
        $planet->field_max = 1 + $moonFieldsBonus;

        // Calculate temperature based on planet position (same as the planet at this position)
        $planetData = $this->planetData($planet->planet, false);
        // Use average of the temperature range for moons
        $avgTemp = (int)(($planetData['temperature'][0] + $planetData['temperature'][1]) / 2);
        $planet->temp_max = $avgTemp;
        $planet->temp_min = $avgTemp - 40;

        // Moons start with no resources.
        $planet->metal = 0;
        $planet->crystal = 0;
        $planet->deuterium = 0;
    }

    /**
     * Sets up planet-specific properties.
     * @param Planet $planet
     * @param bool $is_first_planet
     * @param PlayerService|null $player Optional PlayerService to avoid reloading from database
     */
    private function setupPlanetProperties(Planet $planet, bool $is_first_planet = false, PlayerService|null $player = null): void
    {
        $planet_data = $this->planetData($planet->planet, $is_first_planet);

        // Random field count between the min and max values and add the Server planet fields bonus setting.
        $base_fields = rand($planet_data['fields'][0], $planet_data['fields'][1]) + $this->settings->planetFieldsBonus();

        // Apply Discoverer class planet size bonus (+10%)
        // Only apply character class bonus if user has selected a class
        $planetSizeMultiplier = 1.0;
        if ($planet->user_id) {
            // Use provided player if available, otherwise load from database
            if (!$player) {
                $player = $this->playerServiceFactory->make($planet->user_id, true);
            }
            if ($player && $player->getUser()->character_class !== null) {
                $characterClassService = app(\OGame\Services\CharacterClassService::class);
                $planetSizeMultiplier = $characterClassService->getPlanetSizeBonus($player->getUser());
            }
        }

        $planet->field_max = (int)($base_fields * $planetSizeMultiplier);
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

        // First planet static data.
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

    /**
     * Get max planets per system based on density tier.
     *
     * @param int $densityTier The current density tier (1-3)
     * @return int Max planets allowed per system
     */
    private function getMaxPlanetsForDensityTier(int $densityTier): int
    {
        return match ($densityTier) {
            1 => (rand(1, 10) < 7) ? 2 : 3,  // 70% chance of 2, 30% chance of 3
            2 => (rand(1, 10) < 7) ? 6 : 7,  // 70% chance of 6, 30% chance of 7
            default => 9,                     // Max 9 planets (positions 4-12)
        };
    }
}
