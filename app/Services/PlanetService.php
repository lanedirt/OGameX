<?php

namespace OGame\Services;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Facades\AppUtil;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Planet;
use OGame\Models\Resource;
use OGame\Models\Resources;
use OGame\Services\Objects\ObjectService;
use OGame\Services\Objects\Properties\Abstracts\ObjectPropertyService;
use OGame\Services\Objects\Properties\AttackPropertyService;
use OGame\Services\Objects\Properties\CapacityPropertyService;
use OGame\Services\Objects\Properties\FuelPropertyService;
use OGame\Services\Objects\Properties\Models\ObjectProperties;
use OGame\Services\Objects\Properties\ShieldPropertyService;
use OGame\Services\Objects\Properties\SpeedPropertyService;
use OGame\Services\Objects\Properties\StructuralIntegrityPropertyService;

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
    protected Planet $planet;

    /**
     * The player object who owns this planet.
     *
     * @var PlayerService
     */
    protected PlayerService $player;

    /**
     * Cache for services.
     *
     * @var array<ObjectPropertyService>
     */
    private array $objectPropertyServiceCache = [];

    /**
     * Planet constructor.
     *
     * @param PlayerService|null $player
     *  Player object that the to be loaded planet belongs to. If none is provided, we will auto
     *  attempt to load the playerService object after loading the planet.
     *
     * @param int $planet_id
     *  If supplied the constructor will try to load the planet from the database.
     * @throws BindingResolutionException
     */
    public function __construct(PlayerService $player = null, int $planet_id = 0)
    {
        // Load the planet object if a positive planet ID is given.
        // If no planet ID is given then planet context will not be available
        // but this can be fine for unittests or when creating a new planet.
        if ($planet_id != 0) {
            $this->loadByPlanetId($planet_id);

            if (empty($player)) {
                // No player has been provided, so we load it ourselves here.
                $playerServiceFactory = app()->make(PlayerServiceFactory::class);
                $playerService = $playerServiceFactory->make($this->planet->user_id);
                $this->player = $playerService;
            } else {
                $this->player = $player;
            }
        } else {
            // If no planet ID is given, we still attempt to load the player object if it has been passed.
            if (!empty($player)) {
                $this->player = $player;
            }
        }

        $this->objects = resolve('OGame\Services\Objects\ObjectService');
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
     * Get the player object who owns this planet.
     *
     * @return PlayerService
     */
    public function getPlayer(): PlayerService
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
     * Get planet name.
     *
     * @return string
     */
    public function getPlanetName(): string
    {
        return $this->planet->name;
    }

    /**
     * Get planet coordinates as string.
     *
     * @return string
     */
    public function getPlanetCoordinatesAsString(): string
    {
        $coordinates = $this->getPlanetCoordinates();
        return $coordinates['galaxy'] . ':' . $coordinates['system'] . ':' . $coordinates['planet'];
    }

    /**
     * Get planet coordinates in array.
     *
     * @return array<string,int>
     *  Array with coordinates (galaxy, system, planet)
     */
    public function getPlanetCoordinates(): array
    {
        return [
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
        ];
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

        if ($coordinates['system'] % 2 == 0) {
            $odd_even = 'even';
        } else {
            $odd_even = 'odd';
        }

        return $map_array[$coordinates['planet']][$odd_even];
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
        $system = $coordinates['system'];
        $planet = $coordinates['planet'];

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
        if ($system_between_1_and_10_modifier == -1) {
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
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return float|string
     */
    public function getMetalProductionPerHour(bool $formatted = false): float|string
    {
        $production = $this->planet->metal_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
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
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return float|string
     */
    public function getCrystalProductionPerHour(bool $formatted = false): float|string
    {
        $production = $this->planet->crystal_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
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
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return float|string
     */
    public function getDeuteriumProductionPerHour($formatted = false): float|string
    {
        $production = $this->planet->deuterium_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
    }

    /**
     * Get planet energy amount.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     * Energy amount.
     */
    public function getEnergy(bool $formatted = false): int|string
    {
        $energy_max = $this->planet->energy_max;
        $energy_used = $this->planet->energy_used;

        $energy = $energy_max - $energy_used;

        if ($formatted) {
            $energy = AppUtil::formatNumberLong($energy);
        }

        return $energy;
    }

    /**
     * Removes resources from planet.
     *
     * @param Resources $resources
     * Array with resources to deduct.
     *
     * @throws Exception
     */
    public function deductResources(Resources $resources): void
    {
        // Sanity check that this planet has enough resources, if not throw
        // exception.
        if (!$this->hasResources($resources)) {
            throw new Exception('Planet does not have enough resources.');
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

        $this->planet->save();
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
        if (!empty($resources->metal->get())) {
            if ($this->getMetal() < $resources->metal->get()) {
                return false;
            }
        }
        if (!empty($resources->crystal->get())) {
            if ($this->getCrystal() < $resources->crystal->get()) {
                return false;
            }
        }
        if (!empty($resources->deuterium->get())) {
            if ($this->getDeuterium() < $resources->deuterium->get()) {
                return false;
            }
        }

        // None of the above checks failed which means the planet has
        // enough resources.
        return true;
    }

    /**
     * Get planet metal amount.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getMetal(bool $formatted = false): int|string
    {
        $metal = $this->planet->metal;

        if (empty($metal)) {
            $metal = 0;
        }

        if ($formatted) {
            $metal = AppUtil::formatNumberLong($metal);
        }

        return $metal;
    }

    /**
     * Get planet crystal amount.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getCrystal(bool $formatted = false): int|string
    {
        $crystal = $this->planet->crystal;

        if ($formatted) {
            $crystal = AppUtil::formatNumberLong($crystal);
        }

        return $crystal;
    }

    /**
     * Get planet deuterium amount.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getDeuterium(bool $formatted = false): int|string
    {
        $deuterium = $this->planet->deuterium;

        if ($formatted) {
            $deuterium = AppUtil::formatNumberLong($deuterium);
        }

        return $deuterium;
    }

    /**
     * Get the total amount of ship unit objects on this planet that can fly.
     *
     * @return int
     */
    function getFlightShipAmount(): int
    {
        $totalCount = 0;

        $objects = $this->objects->getShipObjects();
        foreach ($objects as $object) {
            if ($object['id'] == 212) {
                // Do not count solar satellite as ship.
                continue;
            }
            $totalCount += $this->planet->{$object['machine_name']};
        }

        return $totalCount;
    }

    /**
     * Gets the time of upgrading a building on this planet to the next level.
     *
     * @param string $machine_name
     * @param bool $formatted
     * Optional flag whether to format the time or not.
     *
     * @return int|string
     */
    public function getBuildingConstructionTime(string $machine_name, bool $formatted = FALSE): int|string
    {
        $current_level = $this->getObjectLevel($machine_name);
        $next_level = $current_level + 1;
        $price = $this->objects->getObjectPrice($machine_name, $this);

        $robotfactory_level = $this->getObjectLevel('robot_factory');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = 8; // @TODO: implement universe speed.

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (2500 * max((4 - ($next_level / 2)), 1) * (1 + $robotfactory_level) * $universe_speed * pow(2, $nanitefactory_level))
            );

        $time_seconds = $time_hours * 3600;

        // @TODO: round this value up or down so it will be valid for
        // int storage in database.
        $time_seconds = (int)floor($time_seconds);

        // @TODO: calculation does not work correctly for all buildings yet.
        // Possible rounding error?
        if ($formatted) {
            return AppUtil::formatTimeDuration($time_seconds);
        } else {
            return $time_seconds;
        }
    }

    /**
     * Gets the level of a building on this planet.
     *
     * @param string $machine_name
     * The machine name of the object.
     *
     * @return int
     * @throws Exception
     */
    public function getObjectLevel(string $machine_name): int
    {
        $object = $this->objects->getObjectByMachineName($machine_name);
        return $this->planet->{$object->machine_name};
    }

    /**
     * Gets the time of building a ship/defense unit on this planet.
     *
     * @param string $machine_name
     * @param bool $formatted
     * Optional flag whether to format the time or not.
     *
     * @return int|string
     * @throws Exception
     */
    public function getUnitConstructionTime(string $machine_name, bool $formatted = FALSE): int|string
    {
        $object = $this->objects->getObjectByMachineName($machine_name);

        $shipyard_level = $this->getObjectLevel('shipyard');
        $nanitefactory_level = $this->getObjectLevel('nano_factory');
        $universe_speed = 8; // @TODO: implement actual universe speed (development speed).

        // The actual formula which return time in seconds
        $time_hours = 0.5;
        /*$time_hours =
            (
                ($object['properties']['structural_integrity']) // TODO: implement dynamic property retrieval which takes into account research levels.
                /
                (2500 * (1 + $shipyard_level) * $universe_speed * pow(2, $nanitefactory_level))
            );*/

        $time_seconds = $time_hours * 3600;

        if ($formatted) {
            return AppUtil::formatTimeDuration($time_seconds);
        } else {
            return $time_seconds;
        }
    }

    /**
     * Gets the time of researching a technology.
     *
     * @param string $machine_name
     * @param bool $formatted
     * Optional flag whether to format the time or not.
     *
     * @return int|string
     * @throws Exception
     */
    public function getTechnologyResearchTime(string $machine_name, bool $formatted = FALSE): int|string
    {
        $price = $this->objects->getObjectPrice($machine_name, $this);

        $research_lab_level = $this->getObjectLevel('research_lab');
        $universe_speed = 16; // @TODO: implement actual universe speed (research speed).

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price->metal->get() + $price->crystal->get())
                /
                (1000 * (1 + $research_lab_level) * $universe_speed)
            );

        $time_seconds = $time_hours * 3600;

        if ($formatted) {
            return AppUtil::formatTimeDuration($time_seconds);
        } else {
            return $time_seconds;
        }
    }

    /**
     * Sets the building production percentage.
     *
     * @param int $building_id
     * @param int $percentage
     * @return bool
     */
    public function setBuildingPercent(int $building_id, int $percentage): bool
    {
        $building = $this->objects->getBuildingObjects($building_id);

        // Sanity check: building exists.
        if (empty($building)) {
            return false;
        }

        // Sanity check: percentage inside of allowed values.
        if (!is_numeric($percentage) || $percentage < 0 || $percentage > 10) {
            return false;
        }

        // Sanity check: model property exists.
        if (!isset($this->planet->{$building['machine_name'] . '_percent'})) {
            return false;
        }

        $this->planet->{$building['machine_name'] . '_percent'} = $percentage;
        $this->planet->save();

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

        // Save the planet manually here to prevent it from happening 5+ times in the methods above.
        $this->planet->save();
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
        $current_time = Carbon::now()->timestamp;
        $resources_add = [];

        // TODO: add unittest to check that updating fractional resources
        // e.g. if planet has production of 30/hour.. that when it updates
        // every 30 seconds it still gets the 30 per hour overall instead
        // of getting 0 because every update the added resource rounds down to 0.

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

            // @TODO: add transactions for updating resources to prevent request collisions.
            // Metal calculation.
            $max_metal = $this->getMetalStorage();
            if ($this->getMetal() < $max_metal) {
                $resources_add['metal'] = ($this->planet->metal_production * $hours_difference);

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->getMetal() + $resources_add['metal']) > $max_metal) {
                    $resources_add['metal'] = $max_metal - $this->getMetal();
                }
            }

            // Crystal calculation.
            $max_crystal = $this->getCrystalStorage();
            if ($this->getCrystal() < $max_crystal) {
                $resources_add['crystal'] = ($this->planet->crystal_production * $hours_difference);

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->getCrystal() + $resources_add['crystal']) > $max_crystal) {
                    $resources_add['crystal'] = $max_metal - $this->getCrystal();
                }
            }

            // Deuterium calculation.
            $max_deuterium = $this->getDeuteriumStorage();
            if ($this->getDeuterium() < $max_deuterium) {
                $resources_add['deuterium'] = ($this->planet->deuterium_production * $hours_difference);

                // Prevent adding more metal than the max limit can support (storage limit).
                if (($this->getDeuterium() + $resources_add['deuterium']) > $max_deuterium) {
                    $resources_add['deuterium'] = $max_deuterium - $this->getDeuterium();
                }
            }

            $this->addResources(new Resources($resources_add['metal'], $resources_add['crystal'], $resources_add['deuterium'], 0), $save_planet);
            $this->planet->time_last_update = $current_time;

            if ($save_planet) {
                $this->planet->save();
            }
        }
    }

    /**
     * Get planet metal storage (max amount this planet can contain).
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getMetalStorage(bool $formatted = false): int|string
    {
        $storage = $this->planet->metal_max;

        if ($formatted) {
            $storage = AppUtil::formatNumber($storage);
        }

        return $storage;
    }

    /**
     * Get planet crystal storage (max amount this planet can contain).
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     */
    public function getCrystalStorage(bool $formatted = false): int|string
    {
        $storage = $this->planet->crystal_max;

        if ($formatted) {
            $storage = AppUtil::formatNumber($storage);
        }

        return $storage;
    }

    /**
     * Get planet deuterium storage (max amount this planet can contain).
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     */
    public function getDeuteriumStorage(bool $formatted = false): int|string
    {
        $storage = $this->planet->deuterium_max;

        if ($formatted) {
            $storage = AppUtil::formatNumber($storage);
        }

        return $storage;
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
            $this->planet->save();
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
        $queue = resolve('OGame\Services\BuildingQueueService');
        $build_queue = $queue->retrieveFinished($this->getPlanetId());

        // @TODO: add DB transaction wrapper
        foreach ($build_queue as $item) {
            // Get object information of object (building).
            $object = $this->objects->getObjects($item->object_id);

            // Update build queue record
            $item->processed = 1;
            $item->save();

            // Update planet and update level of the object (building) that has been processed.
            $this->setObjectLevel($item->object_id, $item->object_level_target, $save_planet);

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        if (count($build_queue) == 0) {
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
        $object = $this->objects->getObjects($object_id);
        $this->planet->{$object['machine_name']} = $level;
        if ($save_planet) {
            $this->planet->save();
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
     */
    public function updateUnitQueue(bool $save_planet = true): void
    {
        $queue = resolve('OGame\Services\UnitQueueService');
        $unit_queue = $queue->retrieveBuilding($this->getPlanetId());

        // @TODO: add DB transaction wrapper
        foreach ($unit_queue as $item) {
            // Get object information.
            $object = $this->objects->getUnitObjects($item->object_id);

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
            $last_update_diff = Carbon::now()->timestamp - $last_update;

            // If difference between last update and now is equal to or bigger
            // than the time per unit, give the unit and record progress.
            if ($last_update_diff >= $time_per_unit) {
                // Get exact amount of units to reward
                $unit_amount = floor($last_update_diff / $time_per_unit);

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
                $this->planet->{$object['machine_name']} += $unit_amount;
                if ($save_planet) {
                    $this->planet->save();
                }
            }
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
     */
    public function updateResourceProductionStats(bool $save_planet = true): void
    {
        $production_total = new Resources(0,0,0,0);
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
            $this->planet->save();
        }
    }

    /**
     * Returns basic income (resources) information for this planet.
     *
     * @return Resources
     */
    public function getPlanetBasicIncome(): Resources
    {
        $universe_resource_multiplier = 1; // @TODO: implement universe resource multiplier.

        // @TODO: make these settings configurable in backend.
        return new Resources(
            30 * $universe_resource_multiplier,
            15 * $universe_resource_multiplier,
            0,
            0
        );
    }

    /**
     * Update the planets resource production stats inner logic.
     *
     * @param Resources $production_total
     * @param int $energy_production_total
     * @param int $energy_consumption_total
     * @param bool $save_planet
     * @return void
     * @throws Exception
     */
    private function updateResourceProductionStatsInner(Resources $production_total, int $energy_production_total, int $energy_consumption_total, bool $save_planet = true): void
    {
        foreach ($this->objects->getBuildingObjectsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->getBuildingProduction($building->machine_name);

            if ($production->energy->get() > 0) {
                $energy_production_total += $production->energy->get();
            }
            else {
                // Multiplies the negative number with "-1" so it will become
                // a positive number, which is what the system expects.
                $production_total->energy->add(new Resource($production->energy->get() * -1));
                $energy_consumption_total += $production->energy->get() * -1;
            }

            // Combine values to one array so we have the total production.
            $production_total->add($production);
        }

        // Write values to planet
        $this->planet->metal_production = $production_total->metal->get();
        $this->planet->crystal_production = $production_total->crystal->get();
        $this->planet->deuterium_production = $production_total->deuterium->get();
        $this->planet->energy_used = $energy_consumption_total;
        $this->planet->energy_max = $energy_production_total;
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
        $universe_resource_multiplier = 1; // @TODO: implement universe resource multiplier.

        // TODO: check if this works correctly by looping through object values.. would be better to refactor.
        $production = new Resources(0,0,0,0);
        $production->metal->set((eval($building->production->metal) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->crystal->set((eval($building->production->crystal) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->deuterium->set((eval($building->production->deuterium) * $universe_resource_multiplier) * ($resource_production_factor / 100));
        $production->energy->set((eval($building->production->energy) * $universe_resource_multiplier)); // Energy is not affected by production factor.

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
        if ($this->getEnergyProduction() == 0 || $this->getEnergyConsumption() == 0) {
            return 0;
        }

        $production_factor = floor($this->getEnergyProduction() / $this->getEnergyConsumption() * 100);

        // Force min 0, max 100.
        if ($production_factor > 100) {
            $production_factor = 100;
        } elseif ($production_factor < 0) {
            $production_factor = 0;
        }

        return $production_factor;
    }

    /**
     * Get planet energy production.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getEnergyProduction(bool $formatted = false): int|string
    {
        $energy_production = $this->planet->energy_max;

        if (empty($energy_production)) {
            $energy_production = 0;
        }

        if ($formatted) {
            $energy_production = AppUtil::formatNumber($energy_production);
        }

        return $energy_production;
    }

    /**
     * Get planet energy consumption.
     *
     * @param bool $formatted
     * Optional flag whether to format the number or not.
     *
     * @return int|string
     */
    public function getEnergyConsumption(bool $formatted = false): int|string
    {
        $energy_consumption = $this->planet->energy_used;

        if ($formatted) {
            $energy_consumption = AppUtil::formatNumber($energy_consumption);
        }

        return $energy_consumption;
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
        if (!isset($this->planet->{$building->machine_name . '_percent'})) {
            return 0;
        }

        return $this->planet->{$building->machine_name . '_percent'};
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
        $storage_sum = new Resources(0,0,0,0);
        foreach ($this->objects->getBuildingObjectsWithStorage() as $building) {
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
            $this->planet->save();
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
        $building = $this->objects->getBuildingObjectsByMachineName($machine_name);

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
     * Get all object properties for a specific object.
     *
     * @param int $objectId
     * @return ObjectProperties
     * @throws Exception
     */
    public function getObjectProperties(int $objectId): ObjectProperties
    {
        $properties = [
            'structural_integrity',
            'shield',
            'attack',
            'speed',
            'capacity',
            'fuel',
        ];

        $calculatedProperties = [];

        foreach ($properties as $propertyName) {
            $service = $this->getPropertyService($propertyName);
            $calculatedProperties[$propertyName] = $service->calculateProperty($objectId);
        }

        // Directly pass each calculated property to the ObjectProperties constructor
        return new ObjectProperties(
            $calculatedProperties['structural_integrity'],
            $calculatedProperties['shield'],
            $calculatedProperties['attack'],
            $calculatedProperties['speed'],
            $calculatedProperties['capacity'],
            $calculatedProperties['fuel']
        );
    }

    /**
     * Get the object property service for a specific property.
     *
     * @param string $propertyName
     * @return ObjectPropertyService
     * @throws Exception
     */
    public function getPropertyService(string $propertyName): ObjectPropertyService
    {
        if (!isset($this->objectPropertyServiceCache[$propertyName])) {
            switch ($propertyName) {
                case 'structural_integrity':
                    $this->objectPropertyServiceCache[$propertyName] = new StructuralIntegrityPropertyService($this->objects, $this);
                    break;
                case 'shield':
                    $this->objectPropertyServiceCache[$propertyName] = new ShieldPropertyService($this->objects, $this);
                    break;
                case 'attack':
                    $this->objectPropertyServiceCache[$propertyName] = new AttackPropertyService($this->objects, $this);
                    break;
                case 'speed':
                    $this->objectPropertyServiceCache[$propertyName] = new SpeedPropertyService($this->objects, $this);
                    break;
                case 'capacity':
                    $this->objectPropertyServiceCache[$propertyName] = new CapacityPropertyService($this->objects, $this);
                    break;
                case 'fuel':
                    $this->objectPropertyServiceCache[$propertyName] = new FuelPropertyService($this->objects, $this);
                    break;
                default:
                    throw new \Exception('Unknown object property name: ' . $propertyName);
            }
        }
        return $this->objectPropertyServiceCache[$propertyName];
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
        // For units it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = new Resources(0,0,0,0);

        // Create object array
        $building_objects = $this->objects->getBuildingObjectsNew() + $this->objects->getStationObjectsNew();
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object->machine_name, $i);
                $resources_spent->add($raw_price);
            }
        }
        $unit_objects = $this->objects->getShipObjectsNew() + $this->objects->getDefenseObjectsNew();
        foreach ($unit_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object->machine_name);
            // Multiply raw_price by the amount of units.
            for ($i = 0; $i < $this->getObjectAmount($object->machine_name); $i++) {
                $resources_spent->add($raw_price);
            }
        }

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $resources_sum = $resources_spent->metal->get() + $resources_spent->crystal->get() + $resources_spent->deuterium->get();
        $score = (int)floor($resources_sum / 1000);

        return $score;
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
    function getObjectAmount(string $machine_name): int
    {
        $object = $this->objects->getUnitByMachineName($machine_name);

        if (!empty($this->planet->{$object->machine_name})) {
            return $this->planet->{$object->machine_name};
        } else {
            return 0;
        }
    }

    /**
     * Calculate and return economy planet score based on levels of buildings and amount of units.
     *
     * @return int
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
        // For units it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Buildings (100%)
        $building_objects = $this->objects->getBuildingObjects() + $this->objects->getStationObjects();
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object['id']); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object['id'], $i);
                $resources_spent += $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            }
        }

        // Defense (100%)
        $defense_objects = $this->objects->getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // Civil ships (50%)
        $civil_ships = $this->objects->getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = ($raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium']) * 0.5;
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // TODO: add phalanx and jump gate (50%) when moon is implemented.

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $score = (int)floor($resources_spent / 1000);

        return $score;
    }

    /**
     * Calculate planet military points.
     *
     * @return int
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
        // For units it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Defense (100%)
        $defense_objects = $this->objects->getDefenseObjects();
        foreach ($defense_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // Military ships (100%)
        $military_ships = $this->objects->getMilitaryShipObjects();
        foreach ($military_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // Civil ships (50%)
        $civil_ships = $this->objects->getCivilShipObjects();
        foreach ($civil_ships as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = ($raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium']) * 0.5;
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // TODO: add phalanx and jump gate (50%) when moon is implemented.

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $score = (int)floor($resources_spent / 1000);

        return $score;
    }
}
