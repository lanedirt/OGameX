<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Abstracts\GameObject;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Enums\ResourceType;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\ProductionIndex;
use OGame\Models\Resource;
use OGame\Models\Resources;
use OGame\Models\BuildingQueue;
use OGame\Models\ResearchQueue;
use OGame\Models\UnitQueue;
use RuntimeException;
use Throwable;

/**
 * Class PlanetService.
 *
 * Planet object.
 *
 * @package OGame\Services
 */
class PlanetService
{
    /**
     * The planet object from the model.
     *
     * @var Planet
     */
    private Planet $planet;

    /**
     * The player object who owns this planet.
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * @var SettingsService $settingsService
     */
    private SettingsService $settingsService;

    /**
     * Planet constructor.
     *
     * @param PlayerServiceFactory $playerServiceFactory
     * @param SettingsService $settingsService
     * @param PlayerService|null $player
     *  Player object that the to be loaded planet belongs to. If none is provided, we will auto
     *  attempt to load the playerService object after loading the planet.
     * @param Planet|null $planet
     *  If supplied the constructor will use this planet object directly, saving a database query.
     * @param int|null $planet_id
     *  If supplied the constructor will try to load the planet from the database.
     */
    public function __construct(PlayerServiceFactory $playerServiceFactory, SettingsService $settingsService, PlayerService|null $player = null, Planet|null $planet = null, int|null $planet_id = null)
    {
        // Load the planet object if a positive planet ID is given.
        // If no planet ID is given then planet context will not be available
        // but this can be fine for unittests or when creating a new planet.
        if ($planet !== null) {
            $this->planet = $planet;
        } elseif ($planet_id !== 0) {
            $this->loadByPlanetId($planet_id);
        }

        if ($player === null) {
            // If no player has been provided, we load it ourselves here.
            $playerService = $playerServiceFactory->make($this->planet->user_id);
            $this->player = $playerService;
        } else {
            $this->player = $player;
        }

        $this->settingsService = $settingsService;
    }

    /**
     * Load planet object by planet ID.
     *
     * @param int $id
     * The planet ID to load.
     *
     * @return void
     */
    public function loadByPlanetId(int $id): void
    {
        // Fetch planet model
        $planet = Planet::where('id', $id)->first();

        if ($planet === null) {
            throw new RuntimeException('Planet not found.');
        }

        $this->planet = $planet;
    }

    /**
     * Reloads the planet object from the database.
     *
     * @return void
     */
    public function reloadPlanet(): void
    {
        $this->loadByPlanetId($this->planet->id);
    }

    /**
     * Get the player object who owns this planet.
     *
     * @return PlayerService|null
     */
    public function getPlayer(): PlayerService|null
    {
        return $this->player;
    }

    /**
     * Set the planet model directly. This is primarily used by unittests in order to mock the planet model.
     *
     * @param Planet $planet
     * @return void
     */
    public function setPlanet(Planet $planet): void
    {
        $this->planet = $planet;
    }

    /**
     * Checks if the planet name is valid.
     *
     * @param string $name
     * @return bool
     */
    public function isValidPlanetName(string $name): bool
    {
        // Check if the length of the name is between 2 and 20 characters
        if (strlen($name) < 2 || strlen($name) > 20) {
            return false;
        }

        // Check if the name uses only allowed characters
        if (!preg_match('/^[a-zA-Z0-9-_ ]+$/', $name)) {
            return false;
        }

        // Check for invalid placement of hyphens, underscores, and spaces
        if (preg_match('/^[-_ ]|[-_ ]$/', $name)) {
            return false; // Disallow leading and trailing hyphens, underscores, and spaces
        }

        if (preg_match('/[-_ ]{2,}/', $name)) {
            return false; // Disallow consecutive hyphens, underscores, and spaces
        }

        // Check if there are more than three hyphens, underscores, or spaces in the name
        if (preg_match_all('/[-_ ]/', $name, $matches) > 3) {
            return false;
        }

        // If all checks pass
        return true;
    }

    /**
     * Abandon (delete) the current planet. Careful: this action is irreversible!
     *
     * @return void
     */
    public function abandonPlanet(): void
    {
        // Sanity check: disallow abandoning the last remaining planet of user.
        if ($this->isPlanet() && $this->player->planets->planetCount() < 2) {
            throw new RuntimeException('Cannot abandon only remaining planet.');
        }

        // Sanity check: disallow abandoning a planet with active fleet missions.
        $fleetMissionService = resolve(FleetMissionService::class);
        $activeMissions = $fleetMissionService->getActiveMissionsByPlanetIds([$this->planet->id]);
        if ($activeMissions->count() > 0) {
            throw new RuntimeException('Cannot abandon planet with active fleet missions.');
        }

        // If this is a planet and has a moon, delete the moon first
        if ($this->isPlanet() && $this->hasMoon()) {
            $this->moon()->abandonPlanet();
        }

        // Anonymize the planet in all tables where it is referenced.
        // This is done to prevent foreign key constraints from failing.

        // Fleet missions
        FleetMission::where('planet_id_from', $this->planet->id)->update(['planet_id_from' => null]);
        FleetMission::where('planet_id_to', $this->planet->id)->update(['planet_id_to' => null]);

        // Building queues
        BuildingQueue::where('planet_id', $this->planet->id)->delete();

        // Research queues
        ResearchQueue::where('planet_id', $this->planet->id)->delete();

        // Unit queues
        UnitQueue::where('planet_id', $this->planet->id)->delete();

        // Update the player's current planet if it is the planet being abandoned.
        if ($this->getPlayer()->getCurrentPlanetId() === $this->planet->id) {
            $this->getPlayer()->setCurrentPlanetId(0);
        }

        // TODO: add feature test to check that abandoning a planet works correctly in various scenarios.

        // Delete the planet from the database
        $this->planet->delete();
    }

    /**
     * Changes the name of the planet.
     *
     * @return bool True if the planet name was changed successfully.
     */
    public function setPlanetName(string $name, bool $save_planet = true): bool
    {
        $this->planet->name = $name;

        if ($save_planet) {
            $this->save();
        }

        return true;
    }

    /**
     * Save the planet model to persist changes to the database.
     */
    public function save(): void
    {
        $this->planet->save();
    }

    /**
     * Returns true if the underlying planet model has been initialized. For unittests this can be used to check
     * if the planet model itself has been set up already.
     */
    public function planetInitialized(): bool
    {
        return !empty($this->planet);
    }

    /**
     * Get planet name.
     *
     * @return string
     */
    public function getPlanetName(): string
    {
        return $this->planet->name;
    }

    /**
     * Get planet coordinates in array.
     *
     * @return Coordinate
     *  Array with coordinates (galaxy, system, planet)
     */
    public function getPlanetCoordinates(): Coordinate
    {
        return new Coordinate($this->planet->galaxy, $this->planet->system, $this->planet->planet);
    }

    /**
     * Get planet diameter.
     *
     * @return int
     */
    public function getPlanetDiameter(): int
    {
        return $this->planet->diameter;
    }

    /**
     * Get planet type as enum.
     *
     * @return PlanetType
     */
    public function getPlanetType(): PlanetType
    {
        return PlanetType::from($this->planet->planet_type);
    }

    /**
     * Returns true if the current planet is a moon, false otherwise.
     *
     * @return bool
     */
    public function isMoon(): bool
    {
        return $this->getPlanetType() === PlanetType::Moon;
    }

    /**
     * Returns true if the current planet is a planet, false otherwise.
     *
     * @return bool
     */
    public function isPlanet(): bool
    {
        return $this->getPlanetType() === PlanetType::Planet;
    }

    /**
     * Get planet biome type as string (e.g. gas, ice, jungle etc.)
     *
     * @return string
     */
    public function getPlanetBiomeType(): string
    {
        // Get system and planet.
        $coordinates = $this->getPlanetCoordinates();

        $map_array = [
            1 => ['odd' => 'dry', 'even' => 'desert'],
            2 => ['odd' => 'dry', 'even' => 'desert'],
            3 => ['odd' => 'dry', 'even' => 'desert'],
            4 => ['odd' => 'normal', 'even' => 'dry'],
            5 => ['odd' => 'normal', 'even' => 'dry'],
            6 => ['odd' => 'jungle', 'even' => 'normal'],
            7 => ['odd' => 'jungle', 'even' => 'normal'],
            8 => ['odd' => 'water', 'even' => 'jungle'],
            9 => ['odd' => 'water', 'even' => 'jungle'],
            10 => ['odd' => 'ice', 'even' => 'water'],
            11 => ['odd' => 'ice', 'even' => 'water'],
            12 => ['odd' => 'gas', 'even' => 'ice'],
            13 => ['odd' => 'gas', 'even' => 'ice'],
            14 => ['odd' => 'normal', 'even' => 'gas'],
            15 => ['odd' => 'normal', 'even' => 'gas'],
        ];

        if ($coordinates->system % 2 === 0) {
            $odd_even = 'even';
        } else {
            $odd_even = 'odd';
        }

        return $map_array[$coordinates->position][$odd_even];
    }

    /**
     * Get planet specific image type (e.g. which combination between type and variation).
     *
     * @return string
     */
    public function getPlanetImageType(): string
    {
        // Get system and planet.
        $coordinates = $this->getPlanetCoordinates();
        $system = $coordinates->system;
        $planet = $coordinates->position;

        // Mapping array starts at 1:1:x, for every system higher 1 gets added.
        $map_array = [
            1 => 3,
            2 => 4,
            3 => 5,
            4 => 6,
            5 => 7,
            6 => 8,
            7 => 9,
            8 => 10,
            9 => 1,
            10 => 2,
            11 => 3,
            12 => 4,
            13 => 5,
            14 => 6,
            15 => 7,
        ];

        $base_for_system_1 = $map_array[$planet];
        $system_between_1_and_10_modifier = ($system % 10) - 1;

        if ($system_between_1_and_10_modifier === -1) {
            $system_between_1_and_10_modifier = 9;
        }

        $type = $base_for_system_1 + $system_between_1_and_10_modifier;

        if ($type > 10) {
            $type -= 10;
        }

        // For a moon, we need to map planet types 1-10 to moon types 1-5.
        // Moon types are planet types 1-5 repeated.
        if ($this->isMoon()) {
            if ($type > 5) {
                $type -= 5;
            }
        }

        // Return a string
        return (string)$type;
    }

    /**
     * @return int
     */
    public function getPlanetFieldMax(): int
    {
        $extra_fields = 0;
        if ($this->planet->terraformer != 0) {
            // For every level, it increases by 5
            $extra_fields += $this->planet->terraformer * 5;

            // For every 2 levels, it adds another bonus field
            $two_level_bonus_count = (int)(floor($this->planet->terraformer / 2));
            $extra_fields += $two_level_bonus_count;
        }

        if ($this->planet->lunar_base != 0) {
            // For every level, it increases by 3
            $extra_fields += $this->planet->lunar_base * 3;
        }

        return $extra_fields + $this->planet->field_max;
    }

    /**
     * Get planet metal production per second (decimal number).
     *
     * @return float
     */
    public function getMetalProductionPerSecond(): float
    {
        return $this->getMetalProductionPerHour() / 3600;
    }

    /**
     * Get planet metal production per hour.
     *
     * @return float
     */
    public function getMetalProductionPerHour(): float
    {
        return $this->planet->metal_production ?? 0;
    }

    /**
     * Get planet crystal production per second (decimal number).
     *
     * @return float
     * Crystal production per second.
     */
    public function getCrystalProductionPerSecond(): float
    {
        return $this->getCrystalProductionPerHour() / 3600;
    }

    /**
     * Get planet crystal production per hour.
     *
     * @return float
     */
    public function getCrystalProductionPerHour(): float
    {
        return $this->planet->crystal_production ?? 0;
    }

    /**
     * Get planet deuterium production per second (decimal number).
     *
     * @return float
     */
    public function getDeuteriumProductionPerSecond(): float
    {
        return $this->getDeuteriumProductionPerHour() / 3600;
    }

    /**
     * Get planet deuterium production per hour.
     *
     * @return float
     */
    public function getDeuteriumProductionPerHour(): float
    {
        return $this->planet->deuterium_production ?? 0;
    }

    /**
     * Get planet energy amount.
     *
     * @return Resource
     * Energy amount.
     */
    public function energy(): Resource
    {
        $energy_max = $this->planet->energy_max;
        $energy_used = $this->planet->energy_used;

        $energy = $energy_max - $energy_used;

        return new Resource($energy);
    }

    /**
     * Removes resources from planet.
     *
     * @param Resources $resources
     * Array with resources to deduct.
     * @param bool $save_planet
     */
    public function deductResources(Resources $resources, bool $save_planet = true): void
    {
        // Sanity check that this planet has enough resources, if not throw
        // exception.
        if (!$this->hasResources($resources)) {
            throw new RuntimeException('Planet does not have enough resources.');
        }

        if (!empty($resources->metal->get())) {
            $this->planet->metal -= $resources->metal->get();
        }
        if (!empty($resources->crystal->get())) {
            $this->planet->crystal -= $resources->crystal->get();
        }
        if (!empty($resources->deuterium->get())) {
            $this->planet->deuterium -= $resources->deuterium->get();
        }

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Checks if this planet has equal or more than the requested resources.
     *
     * @param Resources $resources
     * Array with resources to check.
     *
     * @return bool
     */
    public function hasResources(Resources $resources): bool
    {
        if (!empty($resources->metal->get()) && ceil($this->metal()->get()) < $resources->metal->get()) {
            return false;
        }
        if (!empty($resources->crystal->get()) && ceil($this->crystal()->get()) < $resources->crystal->get()) {
            return false;
        }
        if (!empty($resources->deuterium->get()) && ceil($this->deuterium()->get()) < $resources->deuterium->get()) {
            return false;
        }
        if (!empty($resources->energy->get()) && ceil($this->energyProduction()->get()) < $resources->energy->get()) {
            return false;
        }

        return true;
    }

    /**
     * Check if this planet has equal or more than the requested units.
     *
     * @param UnitCollection $units
     * @return bool
     */
    public function hasUnits(UnitCollection $units): bool
    {
        foreach ($units->units as $unit) {
            if ($this->getObjectAmount($unit->unitObject->machine_name) < $unit->amount) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get planet metal amount.
     *
     * @return Resource
     */
    public function metal(): Resource
    {
        $metal = $this->planet->metal;

        if (empty($metal)) {
            $metal = 0;
        }

        return new Resource($metal);
    }

    /**
     * Get planet crystal amount.
     *
     * @return Resource
     */
    public function crystal(): Resource
    {
        $crystal = $this->planet->crystal;

        return new Resource($crystal);
    }

    /**
     * Get planet deuterium amount.
     *
     * @return Resource
     */
    public function deuterium(): Resource
    {
        $deuterium = $this->planet->deuterium;

        return new Resource($deuterium);
    }

    /**
     * Get the amount of resources on this planet.
     *
     * @return Resources
     */
    public function getResources(): Resources
    {
        return new Resources($this->metal()->get(), $this->crystal()->get(), $this->deuterium()->get(), $this->energy()->get());
    }

    /**
     * Get the total amount of ship unit objects on this planet that can fly.
     *
     * @return int
     */
    public function getFlightShipAmount(): int
    {
        $totalCount = 0;

        $objects = ObjectService::getShipObjects();
        foreach ($objects as $object) {
            if ($object->machine_name === 'solar_satellite') {
                // Do not count solar satellite as ship.
                continue;
            }
            $totalCount += $this->planet->{$object->machine_name};
        }

        return $totalCount;
    }

    /**
     * Get all ship objects currently placed on this planet.
     *
     * @return UnitCollection
     */
    public function getShipUnits(): UnitCollection
    {
        $units = new UnitCollection();
        $objects = ObjectService::getShipObjects();
        foreach ($objects as $object) {
            if ($this->planet->{$object->machine_name} > 0) {
                $units->addUnit($object, $this->planet->{$object->machine_name});
            }
        }

        return $units;
    }

    /**
     * Get all defense units currently placed on this planet.
     *
     * @return UnitCollection
     */
    public function getDefenseUnits(): UnitCollection
    {
        $units = new UnitCollection();
        $objects = ObjectService::getDefenseObjects();
        foreach ($objects as $object) {
            if ($this->planet->{$object->machine_name} > 0) {
                $units->addUnit($object, $this->planet->{$object->machine_name});
            }
        }

        return $units;
    }

    /**
     * Get array with all building objects on this planet.
     *
     * @return array<string, int>
     */
    public function getBuildingArray(): array
    {
        // TODO: can this logic be moved to the EspionageReport class if its not used elsewehere?
        $array = [];
        $objects = [...ObjectService::getBuildingObjects(), ...ObjectService::getStationObjects()];
        foreach ($objects as $object) {
            if ($this->planet->{$object->machine_name} > 0) {
                $array[$object->machine_name] = $this->planet->{$object->machine_name};
            }
        }

        return $array;
    }

    /**
     * Gets the time of upgrading a building on this planet to the next level.
     *
     * @param string $machine_name
     * @return int
     * @throws Exception
     */
    public function getBuildingConstructionTime(string $machine_name): int
    {
        $current_level = $this->getObjectLevel($machine_name);
        $next_level = $current_level + 1;
        $price = ObjectService::getObjectPrice($machine_name, $this);

        $robotfactory_level = $this->getObjectLevel('robot_factory');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = $this->settingsService->economySpeed();

        // Sanity check: if universe speed is 0, set it to 1 to prevent division by zero.
        if ($universe_speed == 0) {
            $universe_speed = 1;
        }

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (2500 * max((4 - ($next_level / 2)), 1) * (1 + $robotfactory_level) * $universe_speed * (2 ** $nanitefactory_level))
            );

        $time_seconds = (int)($time_hours * 3600);

        // Minimum time is always 1 second for all objects/units.
        if ($time_seconds < 1) {
            $time_seconds = 1;
        }

        return $time_seconds;
    }

    /**
     * Gets the level of a building on this planet.
     *
     * @param string $machine_name
     * The machine name of the object.
     *
     * @return int
     */
    public function getObjectLevel(string $machine_name): int
    {
        $object = ObjectService::getObjectByMachineName($machine_name);
        $level = $this->planet->{$object->machine_name};

        // Required for unittests to work because db factories do not always set initial values.
        if (empty($level)) {
            $level = 0;
        }

        return $level;
    }

    /**
     * Gets the time of building a ship/defense unit on this planet.
     *
     * @param string $machine_name
     * @return int
     * @throws Exception
     */
    public function getUnitConstructionTime(string $machine_name): int
    {
        $object = ObjectService::getUnitObjectByMachineName($machine_name);

        $shipyard_level = $this->getObjectLevel('shipyard');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = $this->settingsService->economySpeed();

        // Sanity check: if universe speed is 0, set it to 1 to prevent division by zero.
        if ($universe_speed == 0) {
            $universe_speed = 1;
        }

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($object->properties->structural_integrity->rawValue)
                /
                (2500 * (1 + $shipyard_level) * $universe_speed * (2 ** $nanitefactory_level))
            );

        $time_seconds = (int)($time_hours * 3600);

        // Minimum time is always 1 second for all objects/units.
        if ($time_seconds < 1) {
            $time_seconds = 1;
        }

        return $time_seconds;
    }

    /**
     * Gets the time of researching a technology.
     *
     * @param string $machine_name
     * @return float
     * @throws Exception
     */
    public function getTechnologyResearchTime(string $machine_name): float
    {
        $price = ObjectService::getObjectPrice($machine_name, $this);
        $research_lab_level = $this->getResearchNetworkLabLevel($machine_name);

        // Research speed is = (economy x research speed).
        $universe_speed = $this->settingsService->economySpeed() * $this->settingsService->researchSpeed();

        // Sanity check: if universe speed is 0, set it to 1 to prevent division by zero.
        if ($universe_speed == 0) {
            $universe_speed = 1;
        }

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (1000 * (1 + $research_lab_level) * $universe_speed)
            );

        $time_seconds = (int)($time_hours * 3600);

        // Minimum time is always 1 second for all objects/units.
        if ($time_seconds < 1) {
            $time_seconds = 1;
        }

        return $time_seconds;
    }

    /**
     * Gets the Intergalactic Research Network combined research lab level for object.
     *
     * @param string $machine_name
     * @return int
     */
    public function getResearchNetworkLabLevel(string $machine_name): int
    {
        $research_lab_level = $this->getObjectLevel('research_lab');

        // The Intergalactic Research Network technology enables multiple research labs
        // across different planets to collaborate, significantly reducing research times.
        $irn_level = $this->getPlayer()->getResearchLevel('intergalactic_research_network');
        if ($irn_level > 0) {
            // Get the research lab levels of all planets in the player's possession.
            $research_lab_levels = [];
            foreach ($this->getPlayer()->planets->allPlanets() as $planet) {
                // Check if the object's requirements are met on the planet;
                // otherwise, the planet's research lab cannot be included in the research network.
                if (!ObjectService::objectRequirementsMet($machine_name, $planet)) {
                    continue;
                }

                // Exclude the current planet, as it is already part of the research network.
                if ($planet->getPlanetId() === $this->getPlanetId()) {
                    continue;
                }

                $research_lab_levels[] = $planet->getObjectLevel('research_lab');
            }

            // Sort the research lab levels in descending order so the highest levels are first.
            rsort($research_lab_levels);

            // Take the first $irn_level research labs and append the sum of their levels to the current planet's
            // research lab level. This will result in the effective research lab level.
            $research_lab_level += array_sum(array_slice($research_lab_levels, 0, $irn_level));
        }

        return $research_lab_level;
    }

    /**
     * Sets the building production percentage.
     *
     * @param int $building_id
     * @param int $percentage
     * @return bool
     * @throws Exception
     */
    public function setBuildingPercent(int $building_id, int $percentage): bool
    {
        $building = ObjectService::getObjectById($building_id);

        // Sanity check: percentage inside allowed values.
        // Sanity check: model property exists.
        if ($percentage < 0 || $percentage > 10 || !isset($this->planet->{$building->machine_name . '_percent'})) {
            return false;
        }

        $this->planet->{$building->machine_name . '_percent'} = $percentage;
        $this->save();

        return true;
    }

    /**
     * Update this planet's resources, buildings, shipyard, defenses and research.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function update(): void
    {
        DB::transaction(function () {
            // Attempt to acquire a lock on the row for this planet. This is to prevent
            // race conditions when multiple requests are updating the same planet and
            // potentially doing double insertions or overwriting each other's changes.
            $planet = Planet::where('id', $this->getPlanetId())
                ->lockForUpdate()
                ->first();

            if ($planet) {
                // Refresh the planet object to ensure we have the latest data after retrieving the lock above.
                $this->reloadPlanet();

                // ------
                // 1. Update resources amount in planet based on hourly production values.
                // ------
                $this->updateResources(false);

                // ------
                // 2. Update building queue
                // ------
                $this->updateBuildingQueue(false);

                // ------
                // 3. Update unit queue
                // ------
                $this->updateUnitQueue(false);

                // ------
                // 4. Update resource production / consumption
                // ------
                $this->updateResourceProductionStats(false);

                // ------
                // 5. Update resource storage
                // ------
                $this->updateResourceStorageStats(false);

                // Save the planet manually here to prevent it from happening 5+ times in the methods above.
                $this->save();
            } else {
                throw new \Exception('Could not acquire planet update lock.');
            }
        });
    }

    /**
     * Get the time the planet was last updated.
     *
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return new Carbon($this->planet->time_last_update);
    }

    /**
     * Get the number of minutes since the planet was last updated.
     *
     * @return int
     */
    public function getMinutesSinceLastUpdate(): int
    {
        $lastUpdate = $this->getUpdatedAt();
        return (int) $lastUpdate->diffInMinutes(Carbon::now());
    }

    /**
     * Update this planet's resources according to production.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @param bool $save_planet
     *   Optional flag whether to save the planet in this method. This defaults to TRUE
     *   but can be set to FALSE when update happens in bulk and the caller method calls
     *   the save planet itself to prevent on unnecessary multiple updates.
     */
    public function updateResources(bool $save_planet = true): void
    {
        $time_last_update = $this->planet->time_last_update;
        $current_time = (int)Carbon::now()->timestamp;

        // TODO: add unittest to check that updating fractional resources
        // e.g. if planet has production of 30/hour.. that when it updates
        // every 30 seconds it still gets the 30 per hour overall instead
        // of getting 0 because every update the added resource rounds down to 0.

        // TODO: add unittest to check that adding resources to planet via transport
        // missions works correctly and that the resources are added to the planet.
        // But that resources are not added by mine production. And that when resources
        // are added in bulk e.g. after 2 days of inactivity it still maxes out at the
        // storage limit.

        // TODO: another possible issue can arise when there are multiple mines
        // in build queue and planet is refreshed at a later time so everything
        // is processed in bulk... in this case it means that resources would update
        // at old level and only after that the new resource level would come into effect.
        // NOTE: this issue can be circumvented with a continious job runner which can
        // update all planets periodically...

        if ($time_last_update < $current_time) {
            // Last updated time is in past, so update resources based on hourly
            // production.
            $hours_difference = ($current_time - $time_last_update) / 3600;

            $add_resources = new Resources(0, 0, 0, 0);

            // @TODO: add transactions for updating resources to prevent request collisions.
            // Metal calculation.
            $max_metal = $this->metalStorage()->get();
            if ($this->metal()->get() < $max_metal) {
                $add_resources->metal->add(new Resource($this->planet->metal_production * $hours_difference));

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->metal()->get() + $add_resources->metal->get()) > $max_metal) {
                    $add_resources->metal->set($max_metal - $this->metal()->get());
                }
            }

            // Crystal calculation.
            $max_crystal = $this->crystalStorage()->get();
            if ($this->crystal()->get() < $max_crystal) {
                $add_resources->crystal->add(new Resource($this->planet->crystal_production * $hours_difference));

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->crystal()->get() + $add_resources->crystal->get()) > $max_crystal) {
                    $add_resources->crystal->set($max_crystal - $this->crystal()->get());
                }
            }

            // Deuterium calculation.
            $max_deuterium = $this->deuteriumStorage()->get();
            if ($this->deuterium()->get() < $max_deuterium) {
                $add_resources->deuterium->add(new Resource($this->planet->deuterium_production * $hours_difference));

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->deuterium()->get() + $add_resources->deuterium->get()) > $max_deuterium) {
                    $add_resources->deuterium->set($max_deuterium - $this->deuterium()->get());
                }
            }

            $this->addResources($add_resources, $save_planet);
            $this->planet->time_last_update = $current_time;

            if ($save_planet) {
                $this->save();
            }
        }
    }

    /**
     * Get planet metal storage (max amount this planet can contain).
     *
     * @return Resource
     */
    public function metalStorage(): Resource
    {
        $storage = $this->planet->metal_max;

        return new Resource($storage);
    }

    /**
     * Get planet crystal storage (max amount this planet can contain).
     *
     * @return Resource
     */
    public function crystalStorage(): Resource
    {
        $storage = $this->planet->crystal_max;

        return new Resource($storage);
    }

    /**
     * Get planet deuterium storage (max amount this planet can contain).
     *
     * @return Resource
     */
    public function deuteriumStorage(): Resource
    {
        $storage = $this->planet->deuterium_max;

        return new Resource($storage);
    }

    /**
     * Adds resources to a planet.
     *
     * @param Resources $resources
     *
     * @param bool $save_planet
     * Optional flag whether to save the planet in this method. This defaults to TRUE
     * but can be set to FALSE when update happens in bulk and the caller method calls
     * the save planet itself to prevent on unnecessary multiple updates.
     */
    public function addResources(Resources $resources, bool $save_planet = true): void
    {
        if (!empty($resources->metal->get())) {
            $this->planet->metal += $resources->metal->get();
        }
        if (!empty($resources->crystal->get())) {
            $this->planet->crystal += $resources->crystal->get();
        }
        if (!empty($resources->deuterium->get())) {
            $this->planet->deuterium += $resources->deuterium->get();
        }

        // Ensure that resources cannot go below 0. This is to prevent negative values due to negative resource production
        // which could be caused by e.g. consumption of deuterium (high level fusion plant) and not enough
        // production (low level deuterium synthesizer).
        $this->planet->metal = max(0, $this->planet->metal);
        $this->planet->crystal = max(0, $this->planet->crystal);
        $this->planet->deuterium = max(0, $this->planet->deuterium);

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * @param ResourceType $resource
     * @param int|float $amount
     * @param bool $save_planet
     * @return void
     * @throws Exception
     */
    public function addResource(ResourceType $resource, int|float $amount, bool $save_planet = true): void
    {
        if (isset($this->planet->{$resource->value})) {
            $this->planet->{$resource->value} += $amount;
            if ($save_planet) {
                $this->save();
            }
        } else {
            throw new Exception('Invalid Resource');
        }
    }

    /**
     * Returns true if the planet has a moon, false otherwise.
     *
     * @return bool
     */
    public function hasMoon(): bool
    {
        // If this planet is a moon, it cannot have another moon.
        if ($this->isMoon()) {
            return false;
        }

        // Access all players planets and see if there is a moon with the same coordinates
        // as this planet.
        if ($this->getPlayer()->planets->getMoonByCoordinates($this->getPlanetCoordinates()) !== null) {
            return true;
        }

        return false;
    }

    /**
     * Returns moon object associated with this planet. If no moon is found, an exception is thrown.
     *
     * @return PlanetService
     */
    public function moon(): PlanetService
    {
        $moon = $this->getPlayer()->planets->getMoonByCoordinates($this->getPlanetCoordinates());

        if ($moon === null) {
            throw new RuntimeException('No moon found for this planet.');
        }

        return $moon;
    }

    /**
     * Returns true if the planet has a moon, false otherwise.
     *
     * @return bool
     */
    public function hasPlanet(): bool
    {
        // If this planet is a planet, it cannot have another planet.
        if ($this->isPlanet()) {
            return false;
        }

        // Access all players planets and see if there is a moon with the same coordinates
        // as this planet.
        if ($this->getPlayer()->planets->getPlanetByCoordinates($this->getPlanetCoordinates()) !== null) {
            return true;
        }

        return false;
    }

    /**
     * Returns planet object associated with this moon (on the same coordinates). If no planet is found, an exception is thrown.
     *
     * @return PlanetService
     */
    public function planet(): PlanetService
    {
        $moon = $this->getPlayer()->planets->getPlanetByCoordinates($this->getPlanetCoordinates());

        if ($moon === null) {
            throw new RuntimeException('No planet found for this moon.');
        }

        return $moon;
    }

    /**
     * Update this planet's buildings by checking the build queue.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @param bool $save_planet
     *  Optional flag whether to save the planet in this method. This defaults to TRUE
     *  but can be set to FALSE when update happens in bulk and the caller method calls
     *  the save planet itself to prevent on unnecessary multiple updates.
     *
     * @throws Exception
     */
    public function updateBuildingQueue(bool $save_planet = true): void
    {
        $queue = resolve(BuildingQueueService::class);
        $build_queue = $queue->retrieveFinished($this->getPlanetId());

        foreach ($build_queue as $item) {
            // Update build queue record
            $item->processed = 1;
            $item->save();

            // Update planet and update level of the object (building) that has been processed.
            $this->setObjectLevel($item->object_id, $item->object_level_target, $save_planet);

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        if (count($build_queue) === 0) {
            // If there were no finished queue item, we still check if we need to start the next one.
            $queue->start($this);
        }
    }

    /**
     * Get planet ID.
     *
     * @return int
     */
    public function getPlanetId(): int
    {
        return $this->planet->id;
    }

    /**
     * Set the level of an object on this planet.
     *
     * @param int $object_id
     * @param int $level
     * @param bool $save_planet
     * @return void
     */
    public function setObjectLevel(int $object_id, int $level, bool $save_planet = true): void
    {
        $object = ObjectService::getObjectById($object_id);
        $this->planet->{$object->machine_name} = $level;
        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Update this planet's shipyard and defenses.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @param bool $save_planet
     *   Optional flag whether to save the planet in this method. This defaults to TRUE
     *   but can be set to FALSE when update happens in bulk and the caller method calls
     *   the save planet itself to prevent on unnecessary multiple updates.
     * @throws Exception
     */
    public function updateUnitQueue(bool $save_planet = true): void
    {
        $queue = resolve(UnitQueueService::class);
        $unit_queue = $queue->retrieveBuilding($this->getPlanetId());

        // @TODO: add DB transaction wrapper
        foreach ($unit_queue as $item) {
            // Get object information.
            $object = ObjectService::getUnitObjectById($item->object_id);

            // Calculate if we can partially (or fully) complete this order
            // yet based on time per unit and amount of ordered units.
            $time_per_unit = ($item->time_end - $item->time_start) / $item->object_amount;

            // Get timestamp where a unit has been presented lastly.
            // @TODO: refactor this and abstract it as the UnitQueueService
            // uses the exact same logic for displaying purposes in the queue.
            $last_update = $item->time_progress;
            if ($last_update < $item->time_start) {
                $last_update = $item->time_start;
            }
            $last_update_diff = (int)Carbon::now()->timestamp - $last_update;

            // If difference between last update and now is equal to or bigger
            // than the time per unit, give the unit and record progress.
            if ($last_update_diff >= $time_per_unit) {
                // Get exact amount of units to reward
                $unit_amount = (int)floor($last_update_diff / $time_per_unit);

                // Unit amount cannot be more than the order in total.
                if ($item->object_amount_progress + $unit_amount > $item->object_amount) {
                    $unit_amount = $item->object_amount - $item->object_amount_progress;
                }

                $new_time_progress = $last_update + ($time_per_unit * $unit_amount);

                // Update build record
                $item->time_progress = $new_time_progress;
                $item->object_amount_progress += $unit_amount;

                if ($item->object_amount_progress >= $item->object_amount) {
                    $item->processed = 1;
                }

                $item->save();

                // Update planet fleet amount
                $this->addUnit($object->machine_name, $unit_amount, $save_planet);
            }
        }
    }

    /**
     * Add a unit to this planet.
     *
     * @param string $machine_name
     * @param int $amount
     * @param bool $save_planet
     * @return void
     */
    public function addUnit(string $machine_name, int $amount, bool $save_planet = true): void
    {
        $object = ObjectService::getUnitObjectByMachineName($machine_name);
        $this->planet->{$object->machine_name} += $amount;

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Add collection of units to this planet.
     *
     * @param UnitCollection $units
     * @param bool $save_planet
     * @return void
     */
    public function addUnits(UnitCollection $units, bool $save_planet = true): void
    {
        foreach ($units->units as $unit) {
            // Do not save the planet in this loop, but save it in the end if requested.
            $this->addUnit($unit->unitObject->machine_name, $unit->amount, false);
        }

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Remove a single unit from this planet by machine name.
     *
     * @param string $machine_name
     * @param int $amount
     * @param bool $save_planet
     * @return void
     */
    public function removeUnit(string $machine_name, int $amount, bool $save_planet = true): void
    {
        $object = ObjectService::getUnitObjectByMachineName($machine_name);
        if ($this->planet->{$object->machine_name} < $amount) {
            throw new RuntimeException('Planet does not have enough units.');
        }

        $this->planet->{$object->machine_name} -= $amount;

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Remove units from this planet by unit collection.
     *
     * @param UnitCollection $units
     * @param bool $save_planet
     * @return void
     */
    public function removeUnits(UnitCollection $units, bool $save_planet): void
    {
        foreach ($units->units as $unit) {
            $this->removeUnit($unit->unitObject->machine_name, $unit->amount, $save_planet);
        }
    }

    /**
     * Update this planet's resource production stats.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @param bool $save_planet
     *   Optional flag whether to save the planet in this method. This defaults to TRUE
     *   but can be set to FALSE when update happens in bulk and the caller method calls
     *   the save planet itself to prevent on unnecessary multiple updates.
     * @throws Exception
     */
    public function updateResourceProductionStats(bool $save_planet = true): void
    {
        $production_total = $this->getPlanetBasicIncome();

        $energy_production_total = 0;
        $energy_consumption_total = 0;

        // Calculate energy production and consumption from the basic income.
        if ($production_total->energy->get() > 0) {
            $energy_production_total += $production_total->energy->get();
        } else {
            // Convert negative energy production to positive consumption.
            $energy_consumption_total += abs($production_total->energy->get());
        }

        // Calculate building energy consumption and adjust production by the energy consumption ratio
        $this->updateResourceProductionStatsInner($production_total, $energy_production_total, $energy_consumption_total);

        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Returns basic income (resources) information for this planet.
     *
     * @return Resources
     */
    public function getPlanetBasicIncome(): Resources
    {
        // Moons do not have mines and therefore also do not have basic income.
        if ($this->isMoon()) {
            return new Resources(0, 0, 0, 0);
        }

        $universe_resource_multiplier = $this->settingsService->economySpeed();

        $baseIncome = new Resources(
            $this->settingsService->basicIncomeMetal() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeCrystal() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeDeuterium() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeEnergy() * $universe_resource_multiplier
        );

        return $this->calculatePlanetBonuses($baseIncome);
    }

    /**
     * Calculate the planet bonuses.
     *
     * @param Resources $baseIncome
     * @return Resources
     */
    public function calculatePlanetBonuses(Resources $baseIncome): Resources
    {
        // Calculate the planet position production bonuses.
        $baseIncome = $this->calculatePlanetProductionBonuses($baseIncome);
        return $baseIncome;
    }

    /**
     * Calculate the planet position production bonuses.
     *
     * @param Resources $baseIncome
     * @return Resources
     */
    public function calculatePlanetProductionBonuses(Resources $baseIncome): Resources
    {
        $position = $this->planet->planet;

        $bonus = $this->getProductionForPositionBonuses($position);

        $metalMultiplier = $bonus['metal'];
        $crystalMultiplier = $bonus['crystal'];
        $deuteriumMultiplier = $bonus['deuterium'];

        // Apply multipliers to the base income
        $baseIncome->metal->set($baseIncome->metal->get() * $metalMultiplier);
        $baseIncome->crystal->set($baseIncome->crystal->get() * $crystalMultiplier);
        $baseIncome->deuterium->set($baseIncome->deuterium->get() * $deuteriumMultiplier);

        return $baseIncome;
    }

    /**
     * Retrieves production bonuses for a given position.
     *
     * @param int $position
     * @return array{metal: float, crystal: float, deuterium: float}
     */
    public function getProductionForPositionBonuses(int $position): array
    {
        // Define production bonuses by position
        $productionBonuses = [
            1 => ['metal' => 1, 'crystal' => 1.4, 'deuterium' => 1],
            2 => ['metal' => 1, 'crystal' => 1.3, 'deuterium' => 1],
            3 => ['metal' => 1, 'crystal' => 1.2, 'deuterium' => 1],
            6 => ['metal' => 1.17, 'crystal' => 1, 'deuterium' => 1],
            7 => ['metal' => 1.23, 'crystal' => 1, 'deuterium' => 1],
            8 => ['metal' => 1.35, 'crystal' => 1, 'deuterium' => 1],
            9 => ['metal' => 1.23, 'crystal' => 1, 'deuterium' => 1],
            10 => ['metal' => 1.17, 'crystal' => 1, 'deuterium' => 1],
        ];

        // Return bonuses or default values
        return $productionBonuses[$position] ?? ['metal' => 1, 'crystal' => 1, 'deuterium' => 1];
    }

    /**
     * Update the planets resource production stats inner logic.
     *
     * @param Resources $production_total
     * @param int|float $energy_production_total
     * @param int|float $energy_consumption_total
     * @param bool $save_planet
     * @return void
     * @throws Exception
     */
    private function updateResourceProductionStatsInner(Resources $production_total, int|float $energy_production_total, int|float $energy_consumption_total, bool $save_planet = true): void
    {
        // create a separate variable for building production
        // $production_total includes basic income, and we need
        // to multiply building production by a production factor later
        // and basic income is unaffected by production factor
        $building_production_total = new Resources();

        foreach (ObjectService::getGameObjectsWithProduction() as $object) {
            // Retrieve all game objects that have production values.
            $production = $this->getObjectProduction($object->machine_name, null, true);

            if ($production->energy->get() > 0) {
                $energy_production_total += $production->energy->get();
            } else {
                // Convert negative production to positive consumption, same value.
                $energy_consumption = abs($production->energy->get());

                $building_production_total->energy->add(new Resource($energy_consumption));
                $energy_consumption_total += $energy_consumption;
            }

            // Combine values to one array, so we have the total production.
            $building_production_total->add($production);
        }

        // After all production values are calculated, we need to calculate the actual fusion plant energy production.
        // This is done by comparing deuterium consumption with deuterium production. If consumption is higher
        // than production and there is no deuterium in storage, we need to set the energy production to 0.
        foreach (ObjectService::getGameObjectsWithProduction() as $object) {
            if ($object->machine_name === 'fusion_plant') {
                // Retrieve the fusion plant production.
                $production = $this->getObjectProduction($object->machine_name, null, true);

                // If fusion plant deuterium production is negative (consumption)
                // and there is no deuterium in storage, we need to set the energy production to 0.
                $consumption_higher_than_production = abs($production->deuterium->get()) > $production_total->deuterium->get() + $building_production_total->deuterium->get();
                if ($consumption_higher_than_production && $this->planet->deuterium == 0) {
                    // Remove energy production from previously calculated total.
                    $energy_production_total -= $production->energy->get();
                }
            }
        }

        $this->planet->energy_used = (int) $energy_consumption_total;
        $this->planet->energy_max  = (int) $energy_production_total;

        $production_factor = $this->getResourceProductionFactor() / 100;

        // multiply() applies the multiplier to energy, which should not be altered
        $total_energy = $building_production_total->energy->get();
        $building_production_total->multiply($production_factor);
        $building_production_total->energy->set($total_energy);

        // add to $total_production, which contains the basic income
        $production_total->add($building_production_total);

        // Write values to planet.
        $this->planet->metal_production     = (int) $production_total->metal->get();
        $this->planet->crystal_production   = (int) $production_total->crystal->get();
        $this->planet->deuterium_production = (int) $production_total->deuterium->get();
    }

    /**
     * Calculates the ProductionIndex for a building on this planet.
     *
     * @param GameObject $object
     *  The building object to calculate for
     *
     * @param int $object_level
     *  The building level
     *
     * @param bool $force_factor
     *  Optional parameter to force production factor to 100%
     *
     * @return ProductionIndex
     */
    public function getObjectProductionIndex(GameObject $object, int $object_level = 0, bool $force_factor = false): ProductionIndex
    {
        // Set default to 1, only override
        // when the building level is not set (which means current output is
        // asked for).
        $resource_production_factor = 1;
        if (!$force_factor) {
            $resource_production_factor = $this->getResourceProductionFactor() / 100;
        }

        if ($object_level === 0) {
            if ($object->type === GameObjectType::Ship || $object->type == GameObjectType::Defense) {
                $object_level = $this->getObjectAmount($object->machine_name);
            } else {
                $object_level = $this->getObjectLevel($object->machine_name);
            }
        }

        $building_percentage = $this->getBuildingPercent($object->machine_name) / 10;

        $object->production->planetService = $this;
        $object->production->playerService = $this->player;
        $object->production->universe_speed = $this->settingsService->economySpeed();

        return $object->production->calculate($object_level, $resource_production_factor * $building_percentage);
    }

    /**
     * Gets the production value of a building on this planet.
     *
     * @param string $machine_name
     *  The machine name of the building to calculate the production for.
     *
     * @param int|null $object_level
     *  Optional parameter to calculate the production for a specific level/amount
     *  of a game object. Defaults to the current level/amount.
     *
     * @param bool $force_factor
     * Optional parameter use to force/simulate the production at 100%
     *
     * @return Resources
     * @throws Exception
     */
    public function getObjectProduction(string $machine_name, int|null $object_level = null, bool $force_factor = false): Resources
    {
        $gameObject = ObjectService::getGameObjectsWithProductionByMachineName($machine_name);

        $object_level = $object_level ?? 0;

        $productionIndex = $this->getObjectProductionIndex($gameObject, $object_level, $force_factor);

        // Round down for energy.
        // Round up for positive resources, round down for negative resources.
        // This makes resource production better, and energy consumption worse.
        $productionIndex->total->metal->set(ceil($productionIndex->total->metal->get()));
        $productionIndex->total->crystal->set(ceil($productionIndex->total->crystal->get()));
        $productionIndex->total->deuterium->set(ceil($productionIndex->total->deuterium->get()));
        $productionIndex->total->energy->set(floor($productionIndex->total->energy->get()));

        return $productionIndex->total;
    }

    /**
     * Returns the resource production factor percentage.
     *
     * This percentage indicates how efficient the resource buildings (mines)
     * are functioning.
     *
     * @return int
     *  The production factor expressed as a percentage (min 0, max 100).
     */
    public function getResourceProductionFactor(): int
    {
        // if no consumption, then there should be no impact to production factor
        if (empty($this->energyConsumption()->get())) {
            return 100;
        }

        // if there is consumption, but energy production is 0, then production factor = 0
        if (empty($this->energyProduction()->get())) {
            return 0;
        }

        $production_factor = floor($this->energyProduction()->get() / $this->energyConsumption()->get() * 100);

        // Force min 0, max 100.
        if ($production_factor > 100) {
            $production_factor = 100;
        } elseif ($production_factor < 0) {
            $production_factor = 0;
        }

        return (int)$production_factor;
    }

    /**
     * Get planet energy production.
     *
     * @return Resource
     */
    public function energyProduction(): Resource
    {
        $energy_production = $this->planet->energy_max;

        if (empty($energy_production)) {
            $energy_production = 0;
        }

        return new Resource((float)$energy_production);
    }

    /**
     * Get planet energy consumption.
     *
     * @return Resource
     */
    public function energyConsumption(): Resource
    {
        $energy_consumption = $this->planet->energy_used;

        return new Resource((float)$energy_consumption);
    }

    /**
     * Get building production percentage.
     *
     * @param string $machine_name
     * @return int
     * @throws Exception
     */
    public function getBuildingPercent(string $machine_name): int
    {
        $building = ObjectService::getObjectByMachineName($machine_name);

        // Sanity check: model property exists.
        return $this->planet->{$building->machine_name . '_percent'} ?? 0;
    }

    /**
     * Get is the current planet is building something or not.
     *
     * @return bool
     * @throws Exception
     */
    public function isBuilding(): bool
    {
        $queue = resolve(BuildingQueueService::class);
        $build_queue = $queue->retrieveQueue($this)->queue;

        return count($build_queue) > 0;
    }

    /**
     * Get is the current planet building the object or not
     *
     * @return bool
     */
    public function isBuildingObject(string $machine_name, int|null $level = null): bool
    {
        $object = ObjectService::getObjectByMachineName($machine_name);

        if ($level === null) {
            $level = $this->getObjectLevel($machine_name) + 1;
        }

        // Check only building queue objects
        if ($object->type !== GameObjectType::Building && $object->type !== GameObjectType::Station) {
            return false;
        }

        $build_queue = resolve(BuildingQueueService::class);
        return $build_queue->objectInBuildingQueue($this, $machine_name, $level);
    }

    /**
     * Get building count from planet
     *
     * @return int
     */
    public function getBuildingCount(): int
    {
        return collect($this->getBuildingArray())->sum();
    }

    /**
     * Get planet average temperature.
     *
     * @return int
     */
    public function getPlanetTempAvg(): int
    {
        return (int)round(($this->getPlanetTempMin() + $this->getPlanetTempMax()) / 2);
    }

    /**
     * Get planet minimum temperature.
     *
     * @return int
     */
    public function getPlanetTempMin(): int
    {
        if (!empty($this->planet->temp_min)) {
            return $this->planet->temp_min;
        }

        return 0;
    }

    /**
     * Get planet maximum temperature.
     *
     * @return int
     */
    public function getPlanetTempMax(): int
    {
        if (!empty($this->planet->temp_max)) {
            return $this->planet->temp_max;
        }

        return 0;
    }

    /**
     * Update this planet's resource storage stats.
     * This should happen on every users page load and every time the planet is touched.
     *
     * @param bool $save_planet
     *  Optional flag whether to save the planet in this method. This defaults to TRUE.
     * @throws Exception
     */
    public function updateResourceStorageStats(bool $save_planet = true): void
    {
        $storage_sum = new Resources(0, 0, 0, 0);
        foreach (ObjectService::getBuildingObjectsWithStorage() as $building) {
            // Retrieve all buildings that have production values.
            $storage = $this->getBuildingMaxStorage($building->machine_name);

            // Combine values to one resource object so we have the total storage.
            $storage_sum->add($storage);
        }

        // Write values to planet
        $this->planet->metal_max = $storage_sum->metal->get();
        $this->planet->crystal_max = $storage_sum->crystal->get();
        $this->planet->deuterium_max = $storage_sum->deuterium->get();
        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Gets the max storage value for resources of a building on this planet.
     *
     * @param string $machine_name
     * @param int|bool $object_level
     * Optional parameter to calculate the storage for a specific level
     *
     * @return Resources
     * @throws Exception
     */
    public function getBuildingMaxStorage(string $machine_name, int|bool $object_level = false): Resources
    {
        $building = ObjectService::getBuildingObjectByMachineName($machine_name);

        // NOTE: $object_level is used by eval() function in the formula.
        if (!$object_level) {
            $object_level = $this->getObjectLevel($machine_name);
        }

        $storage_metal = eval($building->storage->metal);
        $storage_crystal = eval($building->storage->crystal);
        $storage_deuterium = eval($building->storage->deuterium);

        return new Resources($storage_metal, $storage_crystal, $storage_deuterium, 0);
    }

    /**
     * Calculate and return planet score based on levels of buildings and amount of units.
     *
     * @return int
     * @throws Exception
     */
    public function getPlanetScore(): int
    {
        // For every object in the game, calculate the score based on how much resources it costs to build it.
        // For buildings with levels it is the sum of resources needed for all levels up to the current level.
        // For units, it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = new Resources(0, 0, 0, 0);

        // Create object array
        $building_objects = ObjectService::getBuildingObjects() + ObjectService::getStationObjects();
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = ObjectService::getObjectRawPrice($object->machine_name, $i);
                $resources_spent->add($raw_price);
            }
        }
        $unit_objects = ObjectService::getShipObjects() + ObjectService::getDefenseObjects();
        foreach ($unit_objects as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            // Multiply raw_price by the amount of units.
            $resources_spent->add($raw_price->multiply($this->getObjectAmount($object->machine_name)));
        }

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $resources_sum = $resources_spent->sum();
        return (int)floor($resources_sum / 1000);
    }

    /**
     * Get the amount of a specific unit object on this planet. E.g. ships or defense.
     *
     * @param string $machine_name
     * The machine name of the unit object.
     *
     * @return int
     */
    public function getObjectAmount(string $machine_name): int
    {
        $object = ObjectService::getUnitObjectByMachineName($machine_name);

        if (!empty($this->planet->{$object->machine_name})) {
            return $this->planet->{$object->machine_name};
        }

        return 0;
    }

    /**
     * Calculate and return economy planet score based on levels of buildings and amount of units.
     *
     * @return int
     * @throws Exception
     */
    public function getPlanetScoreEconomy(): int
    {
        // Economy score includes:
        // 100% buildings/facilities
        // 100% defense
        // 50% civil ships
        // 50% phalanx and jump gate

        // For every object in the game, calculate the score based on how much resources it costs to build it.
        // For buildings with levels it is the sum of resources needed for all levels up to the current level.
        // For units, it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Buildings (100%)
        $building_objects = [ ...ObjectService::getBuildingObjects(), ...ObjectService::getStationObjects() ];
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = ObjectService::getObjectRawPrice($object->machine_name, $i);
                $resources_spent += $raw_price->sum();
            }
        }

        // Defense (100%)
        $defense_objects = ObjectService::getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Civil ships (50%)
        $civil_ships = ObjectService::getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum() * 0.5;
        }

        // TODO: add phalanx and jump gate (50%) when moon is implemented.

        // Divide the score by 1000 to get the amount of points. Floor the result.
        return (int)floor($resources_spent / 1000);
    }

    /**
     * Calculate planet military points.
     *
     * @return int
     * @throws Exception
     */
    public function getPlanetMilitaryScore(): int
    {
        // Military score includes:
        // 100% defense
        // 100% military ships
        // 50% civil ships
        // 50% phalanx and jump gate

        // For every object in the game, calculate the score based on how much resources it costs to build it.
        // For buildings with levels it is the sum of resources needed for all levels up to the current level.
        // For units, it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Defense (100%)
        $defense_objects = ObjectService::getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Military ships (100%)
        $military_ships = ObjectService::getMilitaryShipObjects();
        foreach ($military_ships as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Civil ships (50%)
        $civil_ships = ObjectService::getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = ObjectService::getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum() * 0.5;
        }

        // TODO: add phalanx and jump gate (50%) when moon is implemented.

        // Divide the score by 1000 to get the amount of points. Floor the result.
        return (int)floor($resources_spent / 1000);
    }
}
