<?php

namespace OGame\Services;

use Exception;
use http\Exception\RuntimeException;
use Illuminate\Support\Carbon;
use OGame\Facades\AppUtil;
use OGame\Planet;

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
     * SettingsService.
     *
     * @var SettingsService
     */
    protected SettingsService $settings;

    /**
     * Planet constructor.
     *
     * @param int planet_id
     *  If supplied the constructor will try to load the planet from the database.
     */
    public function __construct(PlayerService $player, SettingsService $settings, $planet_id)
    {
        // Load the planet object if a positive planet ID is given.
        // If no planet ID is given then planet context will not be available
        // but this can be fine for unittests or when creating a new planet.
        if ($planet_id != 0) {
            $this->loadByPlanetId($planet_id);
        }
        $this->player = $player;
        $this->settings = $settings;

        $this->objects = resolve('OGame\Services\ObjectService');
    }

    /**
     * Load planet object by planet ID.
     */
    public function loadByPlanetId($id)
    {
        // Fetch planet model
        $planet = Planet::where('id', $id)->first();

        $this->planet = $planet;
    }

    /**
     * Get the player object who owns this planet.
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set the planet model directly. This is primarily used by unittests in order to mock the planet model.
     *
     * @param $planet
     * @return void
     */
    public function setPlanet($planet)
    {
        $this->planet = $planet;
    }

    /**
     * Get planet name.
     *
     * @return mixed
     */
    public function getPlanetName()
    {
        return $this->planet->name;
    }

    /**
     * Get planet coordinates as string.
     */
    public function getPlanetCoordinatesAsString()
    {
        $coordinates = $this->getPlanetCoordinates();
        return $coordinates['galaxy'] . ':' . $coordinates['system'] . ':' . $coordinates['planet'];
    }

    /**
     * Get planet coordinates in array.
     *
     * @return array
     *  Array with coordinates (galaxy, system, planet)
     */
    public function getPlanetCoordinates()
    {
        return [
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
        ];
    }

    /**
     * Get planet diameter.
     */
    public function getPlanetDiameter()
    {
        return $this->planet->diameter;
    }

    /**
     * Get planet type (e.g. gas, ice, jungle etc.)
     */
    public function getPlanetType()
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
     */
    public function getPlanetImageType()
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

        return $base_for_system_1 + $system_between_1_and_10_modifier;
    }

    /**
     * Get planet metal production per second (decimal number).
     */
    public function getMetalProductionPerSecond()
    {
        return $this->getMetalProductionPerHour() / 3600;
    }

    /**
     * Get planet metal production per hour.
     */
    public function getMetalProductionPerHour($formatted = false)
    {
        $production = $this->planet->metal_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
    }

    /**
     * Get planet crystal production per second (decimal number).
     */
    public function getCrystalProductionPerSecond()
    {
        return $this->getCrystalProductionPerHour() / 3600;
    }

    /**
     * Get planet crystal production per hour.
     */
    public function getCrystalProductionPerHour($formatted = false)
    {
        $production = $this->planet->crystal_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
    }

    /**
     * Get planet deuterium production per second (decimal number).
     */
    public function getDeuteriumProductionPerSecond()
    {
        return $this->getDeuteriumProductionPerHour() / 3600;
    }

    /**
     * Get planet deuterium production per hour.
     */
    public function getDeuteriumProductionPerHour($formatted = false)
    {
        $production = $this->planet->deuterium_production;

        if ($formatted) {
            $production = AppUtil::formatNumber($production);
        }

        return $production;
    }

    /**
     * Get planet energy amount.
     */
    public function getEnergy($formatted = false)
    {
        $energy_max = $this->planet->energy_max;
        $energy_used = $this->planet->energy_used;

        $energy = $energy_max - $energy_used;

        if ($formatted) {
            $energy = AppUtil::formatNumber($energy);
        }

        return $energy;
    }

    /**
     * Removes resources from planet.
     */
    public function deductResources($resources)
    {
        // Sanity check that this planet has enough resources, if not throw
        // exception.
        if (!$this->hasResources($resources)) {
            throw new Exception('Planet does not have enough resources.');
        }

        if (!empty($resources['metal'])) {
            $this->planet->metal -= $resources['metal'];
        }
        if (!empty($resources['crystal'])) {
            $this->planet->crystal -= $resources['crystal'];
        }
        if (!empty($resources['deuterium'])) {
            $this->planet->deuterium -= $resources['deuterium'];
        }

        $this->planet->save();
    }

    /**
     * Checks if this planet has equal or more than the requested resources.
     */
    public function hasResources($resources)
    {
        if (!empty($resources['metal'])) {
            if ($this->getMetal() < $resources['metal']) {
                return false;
            }
        }
        if (!empty($resources['crystal'])) {
            if ($this->getCrystal() < $resources['crystal']) {
                return false;
            }
        }
        if (!empty($resources['deuterium'])) {
            if ($this->getDeuterium() < $resources['deuterium']) {
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
     * @return mixed
     */
    public function getMetal($formatted = false)
    {
        $metal = $this->planet->metal;

        if ($formatted) {
            $metal = AppUtil::formatNumber($metal);
        }

        return $metal;
    }

    /**
     * Get planet crystal amount.
     */
    public function getCrystal($formatted = false)
    {
        $crystal = $this->planet->crystal;

        if ($formatted) {
            $crystal = AppUtil::formatNumber($crystal);
        }

        return $crystal;
    }

    /**
     * Get planet deuterium amount.
     */
    public function getDeuterium($formatted = false)
    {
        $deuterium = $this->planet->deuterium;

        if ($formatted) {
            $deuterium = AppUtil::formatNumber($deuterium);
        }

        return $deuterium;
    }

    /**
     * Determine
     *
     * @return array|void
     */
    public function determineNewPlanetPosition() {
        $lastAssignedGalaxy = $this->settings->get('last_assigned_galaxy', 1);
        $lastAssignedSystem = $this->settings->get('last_assigned_system', 1);

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
                        return ['galaxy' => $galaxy, 'system' => $system, 'position' => $position];
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
    }

    /**
     * Creates a new random planet.
     *
     * @param $user_id
     *  The user_id of which to generate the planet for.
     */
    public function create($user_id): Planet
    {
        $new_position = $this->determineNewPlanetPosition();
        if (empty($new_position['galaxy']) || empty($new_position['system']) || empty($new_position['position'])) {
            // Failed to get a new position for the to be created planet. Throw exception.
            throw new RuntimeException('Unable to determine new planet position.');
        }

        // Position is available
        $planet = new Planet;
        $planet->user_id = $user_id;
        $planet->name = 'MyPlanet';
        $planet->galaxy = $new_position['galaxy'];
        $planet->system = $new_position['system'];
        $planet->planet = $new_position['position'];
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

        $planet->time_last_update = Carbon::now()->timestamp;
        $planet->save();

        $this->planet = $planet;

        // Update settings with the last assigned galaxy and system if they changed.
        $this->settings->set('last_assigned_galaxy', $new_position['galaxy']);
        $this->settings->set('last_assigned_system', $new_position['system']);

        return $this->planet;
    }

    /**
     * Get the amount of a specific unit object on this planet. E.g. ships or defense.
     */
    function getObjectAmount($object_id)
    {
        $object = $this->objects->getUnitObjects($object_id);

        return $this->planet->{$object['machine_name']};
    }

    /**
     * Get the total amount of ship unit objects on this planet that can fly.
     */
    function getFlightShipAmount()
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
     */
    public function getBuildingConstructionTime($object_id, $formatted = FALSE)
    {
        $object = $this->objects->getObjects($object_id);

        $current_level = $this->getObjectLevel($object_id);
        $next_level = $current_level + 1;
        $price = $this->objects->getObjectPrice($object_id, $this);

        $robotfactory_level = $this->getObjectLevel(14);
        $nanitefactory_level = $this->getObjectLevel(15);
        $universe_speed = 1; // @TODO: implement universe speed.

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($price['metal'] + $price['crystal'])
                /
                (2500 * max((4 - ($next_level / 2)), 1) * (1 + $robotfactory_level) * $universe_speed * pow(2, $nanitefactory_level))
            );

        $time_seconds = $time_hours * 3600;

        // @TODO: round this value up or down so it will be valid for
        // int storage in database.
        $time_seconds = ceil($time_seconds);

        // @TODO: calculation does not work correctly for all buildings yet.
        // Possible rounding error?
        if ($formatted) {
            return $this->formatBuildingTime($time_seconds);
        } else {
            return $time_seconds;
        }
    }

    /**
     * Gets the time of building a ship on this planet.
     */
    public function getUnitConstructionTime($object_id, $formatted = FALSE)
    {
        $object = $this->objects->getObjects($object_id);

        $shipyard_level = $this->getObjectLevel(21);
        $nanitefactory_level = $this->getObjectLevel(15);
        $universe_speed = 8; // @TODO: implement actual universe speed (development speed).

        // The actual formula which return time in seconds
        $time_hours =
            (
                ($object['properties']['structural_integrity'])
                /
                (2500 * (1 + $shipyard_level) * $universe_speed * pow(2, $nanitefactory_level))
            );

        $time_seconds = $time_hours * 3600;

        if ($formatted) {
            return $this->formatBuildingTime($time_seconds);
        } else {
            return $time_seconds;
        }
    }

    /**
     * Gets the level of a building on this planet.
     */
    public function getObjectLevel($object_id)
    {
        $object = $this->objects->getObjects($object_id);
        $object_level = $this->planet->{$object['machine_name']};

        if ($object_level) {
            return $object_level;
        } else {
            return 0;
        }
    }

    /**
     * Helper method to convert building time from seconds to human
     * readable format.
     */
    public function formatBuildingTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        $formatted_string = '';
        if ($hours > 0) {
            $formatted_string .= $hours . 'h ';
        }

        if ($minutes > 0) {
            $formatted_string .= $minutes . 'm ';
        }

        if ($seconds > 0) {
            $formatted_string .= $seconds . 's';
        }

        return $formatted_string;
    }

    /**
     * Sets the building production percentage.
     *
     * @param $building_id
     * @param $percentage
     */
    public function setBuildingPercent($building_id, $percentage)
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
     */
    public function update()
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
    public function updateResources($save_planet = true)
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

            $this->addResources($resources_add, $save_planet);
            $this->planet->time_last_update = $current_time;

            if ($save_planet) {
                $this->planet->save();
            }
        }
    }

    /**
     * Get planet metal storage (max amount this planet can contain).
     */
    public function getMetalStorage($formatted = false)
    {
        $storage = $this->planet->metal_max;

        if ($formatted) {
            $storage = AppUtil::formatNumber($storage);
        }

        return $storage;
    }

    /**
     * Get planet crystal storage (max amount this planet can contain).
     */
    public function getCrystalStorage($formatted = false)
    {
        $storage = $this->planet->crystal_max;

        if ($formatted) {
            $storage = AppUtil::formatNumber($storage);
        }

        return $storage;
    }

    /**
     * Get planet deuterium storage (max amount this planet can contain).
     */
    public function getDeuteriumStorage($formatted = false)
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
     * @param $resources
     *
     * @param bool $save_planet
     * Optional flag whether to save the planet in this method. This defaults to TRUE
     * but can be set to FALSE when update happens in bulk and the caller method calls
     * the save planet itself to prevent on unnecessary multiple updates.
     */
    public function addResources($resources, $save_planet = true)
    {
        if (!empty($resources['metal'])) {
            $this->planet->metal += $resources['metal'];
        }
        if (!empty($resources['crystal'])) {
            $this->planet->crystal += $resources['crystal'];
        }
        if (!empty($resources['deuterium'])) {
            $this->planet->deuterium += $resources['deuterium'];
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
     */
    public function updateBuildingQueue($save_planet = true)
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
    }

    public function setObjectLevel($object_id, $level, $save_planet = true)
    {
        $object = $this->objects->getObjects($object_id);
        $this->planet->{$object['machine_name']} = $level;
        if ($save_planet) {
            $this->planet->save();
        }
    }

    /**
     * Get planet ID.
     *
     * @return mixed
     */
    public function getPlanetId()
    {
        return $this->planet->id;
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
    public function updateUnitQueue($save_planet = true)
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
    public function updateResourceProductionStats($save_planet = true)
    {
        $production_total = [];
        $energy_production_total = 0;
        $energy_consumption_total = 0;

        // Get basic income resource values.
        foreach ($this->getPlanetBasicIncome() as $key => $value) {
            if (!empty($production_total[$key])) {
                $production_total[$key] += $value;
            } else {
                $production_total[$key] = $value;
            }

            if ($key == 'energy') {
                if ($value > 0) {
                    $energy_production_total += $value;
                } elseif ($value < 0) {
                    // Multiplies the negative number with "-1" so it will become
                    // a positive number, which is what the system expects.
                    $energy_consumption_total += $value * -1;
                }
            }
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
     */
    public function getPlanetBasicIncome()
    {
        $universe_resource_multiplier = 1; // @TODO: implement universe resource multiplier.

        // @TODO: make these settings configurable in backend.
        return [
            'metal' => 30 * $universe_resource_multiplier,
            'crystal' => 15 * $universe_resource_multiplier,
            'deuterium' => 0,
            'energy' => 0,
        ];
    }

    /**
     * Update the planets resource production stats inner logic.
     *
     * @param $production_total
     * @param $energy_production_total
     * @param $energy_consumption_total
     * @param $save_planet
     * @return void
     */
    private function updateResourceProductionStatsInner($production_total, $energy_production_total, $energy_consumption_total, $save_planet = true)
    {
        foreach ($this->objects->getBuildingObjectsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->getBuildingProduction($building['id']);

            // Combine values to one array so we have the total production.
            foreach ($production as $key => $value) {
                if (!empty($production_total[$key])) {
                    $production_total[$key] += $value;
                } else {
                    $production_total[$key] = $value;
                }
            }

            if ($production['energy'] > 0) {
                $energy_production_total += $production['energy'];
            } elseif ($production['energy'] < 0) {
                // Multiplies the negative number with "-1" so it will become
                // a positive number, which is what the system expects.
                $energy_consumption_total += $production['energy'] * -1;
            }
        }
        // Write values to planet
        $this->planet->metal_production = $production_total['metal'];
        $this->planet->crystal_production = $production_total['crystal'];
        $this->planet->deuterium_production = $production_total['deuterium'];
        $this->planet->energy_used = $energy_consumption_total;
        $this->planet->energy_max = $energy_production_total;
    }

    /**
     * Gets the production value of a building on this planet.
     *
     * @param $building_id
     *  The ID of the building to calculate the production for.
     *
     * @param $building_level
     *  Optional parameter to calculate the production for a specific level
     *  of a building. Defaults to the current level.
     */
    public function getBuildingProduction($building_id, $building_level = false)
    {
        $building = $this->objects->getBuildingObjectsWithProduction($building_id);

        $production = array();
        $resource_production_factor = 100; // Set default to 100, only override
        // when the building level is not set (which means current output is
        // asked for).
        if (!$building_level) {
            $building_level = $this->getObjectLevel($building_id);
            $resource_production_factor = $this->getResourceProductionFactor();
        }

        $building_percentage = $this->getBuildingPercent($building_id); // Implement building percentage.
        $planet_temperature = $this->getPlanetTempAvg();
        $energy_technology_level = 0; // Implement energy technology level getter.
        $universe_resource_multiplier = 1; // @TODO: implement universe resource multiplier.

        foreach ($building['production'] as $resource => $production_formula) {
            $production[$resource] = eval($production_formula) * $universe_resource_multiplier;

            // Apply production factor multiplier to all resources (except positive energy)
            if ($resource == 'energy') {
                // Do nothing
            } else {
                $production[$resource] = $production[$resource] * ($resource_production_factor / 100);
            }

            // Round down for energy.
            // Round up for positive resources, round down for negative resources.
            // This makes resource production better, and energy consumption worse.
            if ($resource == 'energy') {
                $production[$resource] = floor($production[$resource]);
            } elseif ($production[$resource] > 0) {
                $production[$resource] = ceil($production[$resource]);
            } else {
                $production[$resource] = floor($production[$resource]);
            }
        }

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
    public function getResourceProductionFactor()
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
     */
    public function getEnergyProduction($formatted = false)
    {
        $energy_production = $this->planet->energy_max;

        if ($formatted) {
            $energy_production = AppUtil::formatNumber($energy_production);
        }

        return $energy_production;
    }

    /**
     * Get planet energy consumption.
     */
    public function getEnergyConsumption($formatted = false)
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
     * @return int
     */
    public function getBuildingPercent($building_id)
    {
        $building = $this->objects->getObjects($building_id);

        // Sanity check: model property exists.
        if (!isset($this->planet->{$building['machine_name'] . '_percent'})) {
            return false;
        }

        return $this->planet->{$building['machine_name'] . '_percent'};
    }

    /**
     * Get planet average temperature.
     */
    public function getPlanetTempAvg()
    {
        return round(($this->getPlanetTempMin() + $this->getPlanetTempMax()) / 2);
    }

    /**
     * Get planet minimum temperature.
     */
    public function getPlanetTempMin()
    {
        return $this->planet->temp_min;
    }

    /**
     * Get planet maximum temperature.
     */
    public function getPlanetTempMax()
    {
        return $this->planet->temp_max;
    }

    /**
     * Update this planet's resource storage stats.
     * This should happen on every users page load and every time the planet is touched.
     */
    public function updateResourceStorageStats($save_planet = true)
    {
        $storage_total = [];
        foreach ($this->objects->getBuildingObjectsWithStorage() as $building) {
            // Retrieve all buildings that have production values.
            $storage = $this->getBuildingMaxStorage($building['id']);

            // Combine values to one array so we have the total storage.
            foreach ($storage as $key => $value) {
                if (!empty($storage_total[$key])) {
                    $storage_total[$key] += $value;
                } else {
                    $storage_total[$key] = $value;
                }
            }
        }

        // Write values to planet
        $this->planet->metal_max = $storage_total['metal'];
        $this->planet->crystal_max = $storage_total['crystal'];
        $this->planet->deuterium_max = $storage_total['deuterium'];
        if ($save_planet) {
            $this->planet->save();
        }
    }

    /**
     * Gets the max storage value for resources of a building on this planet.
     */
    public function getBuildingMaxStorage($building_id)
    {
        $building = $this->objects->getBuildingObjects($building_id);

        $building_level = $this->getObjectLevel($building_id);
        $storage = array();
        foreach ($building['storage'] as $resource => $storage_formula) {
            $storage[$resource] = eval($storage_formula);
        }

        return $storage;
    }

    /**
     * Calculate and return planet score based on levels of buildings and amount of units.
     */
    public function getPlanetScore()
    {
        // For every object in the game, calculate the score based on how much resources it costs to build it.
        // For buildings with levels it is the sum of resources needed for all levels up to the current level.
        // For units it is the sum of resources needed to build the full sum of all units.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Create object array
        $building_objects = $this->objects->getBuildingObjects() + $this->objects->getStationObjects();
        foreach ($building_objects as $object) {
            for ($i = 1; $i <= $this->getObjectLevel($object['id']); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object['id'], $i);
                $resources_spent += $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            }
        }
        $unit_objects = $this->objects->getShipObjects() + $this->objects->getDefenseObjects();
        foreach ($unit_objects as $object) {
            $raw_price = $this->objects->getObjectRawPrice($object['id']);
            $raw_price_sum = $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            // Multiply raw_price by the amount of units.
            $resources_spent += $raw_price_sum * $this->getObjectAmount($object['id']);
        }

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $score = floor($resources_spent / 1000);

        return $score;
    }

    /**
     * Calculate and return economy planet score based on levels of buildings and amount of units.
     */
    public function getPlanetScoreEconomy()
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
        $score = floor($resources_spent / 1000);

        return $score;
    }

    /**
     * Calculate planet military points.
     *
     * @return float
     */
    public function getPlanetMilitaryScore()
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
        $score = floor($resources_spent / 1000);

        return $score;
    }
}
