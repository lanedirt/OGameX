<?php

namespace OGame\Services;

use Exception;
use Hamcrest\Core\Set;
use Illuminate\Support\Carbon;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resource;
use OGame\Models\Resources;

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
     * Information about objects.
     *
     * @var ObjectService
     */
    public ObjectService $objects;

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
     * @param ?PlayerService $player
     *  Player object that the to be loaded planet belongs to. If none is provided, we will auto
     *  attempt to load the playerService object after loading the planet.
     *
     * @param int $planet_id
     *  If supplied the constructor will try to load the planet from the database.
     */
    public function __construct(ObjectService $objectService, PlayerServiceFactory $playerServiceFactory, SettingsService $settingsService, PlayerService|null $player = null, int $planet_id = 0)
    {
        // Load the planet object if a positive planet ID is given.
        // If no planet ID is given then planet context will not be available
        // but this can be fine for unittests or when creating a new planet.
        if ($planet_id !== 0) {
            $this->loadByPlanetId($planet_id);

            if ($player === null) {
                // No player has been provided, so we load it ourselves here.
                $playerService = $playerServiceFactory->make($this->planet->user_id);
                $this->player = $playerService;
            } else {
                $this->player = $player;
            }
        } elseif ($player !== null) {
            $this->player = $player;
        }

        $this->objects = $objectService;
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
     * @return ?PlayerService
     */
    public function getPlayer(): ?PlayerService
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
     * @throws Exception
     */
    public function abandonPlanet(): void
    {
        // Anonymize the planet in all tables where it is referenced.
        // This is done to prevent foreign key constraints from failing.

        // Fleet missions
        FleetMission::where('planet_id_from', $this->planet->id)->update(['planet_id_from' => null]);
        FleetMission::where('planet_id_to', $this->planet->id)->update(['planet_id_to' => null]);

        if ($this->player->planets->count() < 2) {
            throw new Exception('Cannot abandon only remaining planet.');
        }

        // Update the player's current planet if it is the planet being abandoned.
        if ($this->player->getCurrentPlanetId() === $this->planet->id) {
            $this->player->setCurrentPlanetId(0);
        }

        // TODO: add sanity check that a planet can only be abandoned if it has no active fleet missions going to or from it.
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
     * Get planet type (e.g. gas, ice, jungle etc.)
     *
     * @return string
     */
    public function getPlanetType(): string
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

        // Return a string
        return $base_for_system_1 + $system_between_1_and_10_modifier . "";
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
        return $this->planet->metal_production;
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
        return $this->planet->crystal_production;
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
        return $this->planet->deuterium_production;
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
     *
     * @throws Exception
     */
    public function deductResources(Resources $resources, bool $save_planet = true): void
    {
        // Sanity check that this planet has enough resources, if not throw
        // exception.
        if (!$this->hasResources($resources)) {
            throw new \RuntimeException('Planet does not have enough resources.');
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
        if (!empty($resources->metal->get()) && $this->metal()->get() < $resources->metal->get()) {
            return false;
        }
        if (!empty($resources->crystal->get()) && $this->crystal()->get() < $resources->crystal->get()) {
            return false;
        }
        if (!empty($resources->deuterium->get()) && $this->deuterium()->get() < $resources->deuterium->get()) {
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
     * Get the total amount of ship unit objects on this planet that can fly.
     *
     * @return int
     */
    public function getFlightShipAmount(): int
    {
        $totalCount = 0;

        $objects = $this->objects->getShipObjects();
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
        $price = $this->objects->getObjectPrice($machine_name, $this);

        $robotfactory_level = $this->getObjectLevel('robot_factory');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = $this->settingsService->economySpeed();

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (2500 * max((4 - ($next_level / 2)), 1) * (1 + $robotfactory_level) * $universe_speed * (2 ** $nanitefactory_level))
            );

        $time_seconds = $time_hours * 3600;

        return (int)floor($time_seconds);
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
        $object = $this->objects->getObjectByMachineName($machine_name);
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
        $object = $this->objects->getUnitObjectByMachineName($machine_name);

        $shipyard_level = $this->getObjectLevel('shipyard');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = $this->settingsService->economySpeed();

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($object->properties->structural_integrity->rawValue)
                /
                (2500 * (1 + $shipyard_level) * $universe_speed * (2 ** $nanitefactory_level))
            );

        return (int)($time_hours * 3600);
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
        $price = $this->objects->getObjectPrice($machine_name, $this);

        $research_lab_level = $this->getObjectLevel('research_lab');
        // Research speed is = (economy x research speed).
        $universe_speed = $this->settingsService->economySpeed() * $this->settingsService->researchSpeed();

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (1000 * (1 + $research_lab_level) * $universe_speed)
            );

        return (int)($time_hours * 3600);
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
        $building = $this->objects->getObjectById($building_id);

        // Sanity check: percentage inside allowed values.
        // Sanity check: model property exists.
        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 10 || !isset($this->planet->{$building->machine_name . '_percent'})) {
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
     */
    public function update(): void
    {
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

        // -----
        // 6. Update fleet missions that affect this planet
        // -----
        $this->updateFleetMissions(false);

        // Save the planet manually here to prevent it from happening 5+ times in the methods above.
        $this->save();
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
        $resources_add = [];

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

        if ($save_planet) {
            $this->save();
        }
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
     * @throws Exception
     */
    public function setObjectLevel(int $object_id, int $level, bool $save_planet = true): void
    {
        $object = $this->objects->getObjectById($object_id);
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
            $object = $this->objects->getUnitObjectById($item->object_id);

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
     * @throws Exception
     */
    public function addUnit(string $machine_name, int $amount, bool $save_planet = true): void
    {
        $object = $this->objects->getUnitObjectByMachineName($machine_name);
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
     * @throws Exception
     */
    public function addUnits(UnitCollection $units, bool $save_planet = true): void
    {
        foreach ($units->units as $unit) {
            $this->addUnit($unit->unitObject->machine_name, $unit->amount, $save_planet);
        }
    }

    /**
     * Remove a single unit from this planet by machine name.
     *
     * @param string $machine_name
     * @param int $amount
     * @param bool $save_planet
     * @return void
     * @throws Exception
     */
    public function removeUnit(string $machine_name, int $amount, bool $save_planet = true): void
    {
        $object = $this->objects->getUnitObjectByMachineName($machine_name);
        if ($this->planet->{$object->machine_name} < $amount) {
            throw new Exception('Planet does not have enough units.');
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
     * @throws Exception
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
        $production_total = new Resources(0, 0, 0, 0);
        $energy_production_total = 0;
        $energy_consumption_total = 0;

        // Get basic income resource values.
        $production_total->add($this->getPlanetBasicIncome());

        $energy_production_total += $production_total->energy->get();
        if ($production_total->energy->get() < 0) {
            // Multiplies the negative number with "-1" so it will become
            // a positive number, which is what the system expects.
            $production_total->energy->add(new Resource($production_total->energy->get() * -1));
            $energy_consumption_total += $production_total->energy->get() * -1;
        }

        // Calculate the production values twice:
        // 1. First one time in order for the energy production to be updated.
        // 2. Second time for the mine production to be updated according to the actual energy production.
        $this->updateResourceProductionStatsInner($production_total, $energy_production_total, $energy_consumption_total);
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
        $universe_resource_multiplier = $this->settingsService->economySpeed();

        return new Resources(
            $this->settingsService->basicIncomeMetal() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeCrystal() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeDeuterium() * $universe_resource_multiplier,
            $this->settingsService->basicIncomeEnergy() * $universe_resource_multiplier
        );
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
        foreach ($this->objects->getBuildingObjectsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->getBuildingProduction($building->machine_name);

            if ($production->energy->get() > 0) {
                $energy_production_total += $production->energy->get();
            } else {
                // Multiplies the negative number with "-1" so it will become
                // a positive number, which is what the system expects.
                $production_total->energy->add(new Resource($production->energy->get() * -1));
                $energy_consumption_total += $production->energy->get() * -1;
            }

            // Combine values to one array so we have the total production.
            $production_total->add($production);
        }

        // Write values to planet
        $this->planet->metal_production = (int)$production_total->metal->get();
        $this->planet->crystal_production = (int)$production_total->crystal->get();
        $this->planet->deuterium_production = (int)$production_total->deuterium->get();
        $this->planet->energy_used = (int)$energy_consumption_total;
        $this->planet->energy_max = (int)$energy_production_total;
    }

    /**
     * Gets the production value of a building on this planet.
     *
     * @param string $machine_name
     *  The machine name of the building to calculate the production for.
     *
     * @param int $building_level
     *  Optional parameter to calculate the production for a specific level
     *  of a building. Defaults to the current level.
     *
     * @return Resources
     * @throws Exception
     */
    public function getBuildingProduction(string $machine_name, int $building_level = 0): Resources
    {
        $building = $this->objects->getBuildingObjectsWithProductionByMachineName($machine_name);

        $resource_production_factor = 100; // Set default to 100, only override
        // when the building level is not set (which means current output is
        // asked for).

        // NOTE: building_level is used by eval() function in the formula.
        if (!$building_level) {
            $building_level = $this->getObjectLevel($machine_name);
            $resource_production_factor = $this->getResourceProductionFactor();
        }

        $building_percentage = $this->getBuildingPercent($machine_name); // Implement building percentage.
        $planet_temperature = $this->getPlanetTempAvg();
        $energy_technology_level = 0; // Implement energy technology level getter.
        $universe_resource_multiplier = $this->settingsService->economySpeed();

        $production = new Resources(0, 0, 0, 0);
        $production->metal->set((eval($building->production->metal) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->crystal->set((eval($building->production->crystal) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->deuterium->set((eval($building->production->deuterium) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->energy->set((eval($building->production->energy))); // Energy is not affected by production factor or universe economy speed.

        // Round down for energy.
        // Round up for positive resources, round down for negative resources.
        // This makes resource production better, and energy consumption worse.
        $production->metal->set(ceil($production->metal->get()));
        $production->crystal->set(ceil($production->crystal->get()));
        $production->deuterium->set(ceil($production->deuterium->get()));
        $production->energy->set(floor($production->energy->get()));

        return $production;
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
        if (empty($this->energyProduction()->get()) || empty($this->energyConsumption()->get())) {
            return 0;
        }

        $production_factor = $this->energyConsumption()->get() ? floor($this->energyProduction()->get() / $this->energyConsumption()->get() * 100) : 0;

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
        $building = $this->objects->getObjectByMachineName($machine_name);

        // Sanity check: model property exists.
        return $this->planet->{$building->machine_name . '_percent'} ?? 0;
    }

    /**
     * Get is the current planet is building something or not
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
        foreach ($this->objects->getBuildingObjectsWithStorage() as $building) {
            // Retrieve all buildings that have production values.
            $storage = $this->getBuildingMaxStorage($building->machine_name);

            // Combine values to one resource object so we have the total storage.
            $storage_sum->add($storage);
        }

        // Write values to planet
        $this->planet->metal_max = (int)$storage_sum->metal->get();
        $this->planet->crystal_max = (int)$storage_sum->crystal->get();
        $this->planet->deuterium_max = (int)$storage_sum->deuterium->get();
        if ($save_planet) {
            $this->save();
        }
    }

    /**
     * Gets the max storage value for resources of a building on this planet.
     *
     * @param string $machine_name
     * @param int|bool $building_level
     * Optional parameter to calculate the storage for a specific level
     *
     * @return Resources
     * @throws Exception
     */
    public function getBuildingMaxStorage(string $machine_name, int|bool $building_level = false): Resources
    {
        $building = $this->objects->getBuildingObjectByMachineName($machine_name);

        // NOTE: $building_level is used by eval() function in the formula.
        if (!$building_level) {
            $building_level = $this->getObjectLevel($machine_name);
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
        $building_objects = $this->objects->getBuildingObjects() + $this->objects->getStationObjects();
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object->machine_name, $i);
                $resources_spent->add($raw_price);
            }
        }
        $unit_objects = $this->objects->getShipObjects() + $this->objects->getDefenseObjects();
        foreach ($unit_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
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
     * @throws Exception
     */
    public function getObjectAmount(string $machine_name): int
    {
        $object = $this->objects->getUnitObjectByMachineName($machine_name);

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
        $building_objects = [ ...$this->objects->getBuildingObjects(), ...$this->objects->getStationObjects() ];
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object->machine_name, $i);
                $resources_spent += $raw_price->sum();
            }
        }

        // Defense (100%)
        $defense_objects = $this->objects->getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Civil ships (50%)
        $civil_ships = $this->objects->getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
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
        $defense_objects = $this->objects->getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Military ships (100%)
        $military_ships = $this->objects->getMilitaryShipObjects();
        foreach ($military_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum();
        }

        // Civil ships (50%)
        $civil_ships = $this->objects->getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
            $resources_spent += $raw_price->multiply($this->getObjectAmount($object->machine_name))->sum() * 0.5;
        }

        // TODO: add phalanx and jump gate (50%) when moon is implemented.

        // Divide the score by 1000 to get the amount of points. Floor the result.
        return (int)floor($resources_spent / 1000);
    }

    public function updateFleetMissions(bool $save_planet = true): void
    {
        try {
            $fleetMissionService = app()->make(FleetMissionService::class);
            $missions = $fleetMissionService->getMissionsByPlanetId($this->getPlanetId());

            foreach ($missions as $mission) {
                $fleetMissionService->updateMission($mission);
            }
        } catch (Exception $e) {
            throw new \RuntimeException('Fleet mission service not found.');
        }
    }
}
