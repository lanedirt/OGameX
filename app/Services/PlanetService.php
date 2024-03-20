<?php

namespace OGame\Services;

use Illuminate\Support\Facades\DB;
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
     * The planet object from the model.
     *
     * @var
     */
    protected $planet;

    /**
     * Information about objects.
     *
     * @var \OGame\Services\ObjectService
     */
    public $objects;

    /**
     * The player object who owns this planet.
     *
     * @var
     */
    protected $player;

    /**
     * Planet constructor.
     */
    public function __construct() {
        // Load object service.
        $this->objects = resolve('OGame\Services\ObjectService');
    }

    /**
     * Get the player object who owns this planet.
     */
    public function getPlayer() {
        // @TODO: implement static cache for player object.
        if (!$this->player) {
            $this->player = new PlayerService();
            $this->player->load($this->planet->user_id);
        }

        return $this->player;
    }

    /**
     * Load planet object by planet ID.
     */
    public function loadByPlanetId($id) {
        // Fetch planet model
        $planet = Planet::where('id', $id)->first();

        $this->planet = $planet;
    }

    /**
     * Get planet ID.
     *
     * @return mixed
     */
    public function getPlanetId() {
        return $this->planet->id;
    }

    /**
     * Get planet name.
     *
     * @return mixed
     */
    public function getPlanetName() {
        return $this->planet->name;
    }

    /**
     * Get planet coordinates in array.
     *
     * @return array
     *  Array with coordinates (galaxy, system, planet)
     */
    public function getPlanetCoordinates() {
        return [
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
        ];
    }

    /**
     * Get planet coordinates as string.
     */
    public function getPlanetCoordinatesAsString() {
        $coordinates = $this->getPlanetCoordinates();
        return $coordinates['galaxy'] . ':' . $coordinates['system'] . ':' . $coordinates['planet'];
    }

    /**
     * Get planet diameter.
     */
    public function getPlanetDiameter() {
        return $this->planet->diameter;
    }

    /**
     * Get planet minimum temperature.
     */
    public function getPlanetTempMin() {
        return $this->planet->temp_min;
    }

    /**
     * Get planet maximum temperature.
     */
    public function getPlanetTempMax() {
        return $this->planet->temp_max;
    }

    /**
     * Get planet average temperature.
     */
    public function getPlanetTempAvg() {
        return round(($this->getPlanetTempMin() + $this->getPlanetTempMax()) / 2);
    }

    /**
     * Get planet type (e.g. gas, ice, jungle etc.)
     */
    public function getPlanetType() {
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
      }
      else {
        $odd_even = 'odd';
      }

      return $map_array[$coordinates['planet']][$odd_even];
    }

    /**
     * Get planet specific image type (e.g. which combination between type and variation).
     */
    public function getPlanetImageType() {
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
     * Get planet metal amount.
     *
     * @return mixed
     */
    public function getMetal($formatted = false) {
        $metal = $this->planet->metal;

        if ($formatted) {
            $metal = number_format($metal, 0, ',', '.');
        }

        return $metal;
    }

    /**
     * Get planet metal production per hour.
     */
    public function getMetalProductionPerHour($formatted = false) {
        $production = $this->planet->metal_production;

        if ($formatted) {
            $production = number_format($production, 0, ',', '.');
        }

        return $production;
    }

    /**
     * Get planet metal production per second (decimal number).
     */
    public function getMetalProductionPerSecond() {
        return $this->getMetalProductionPerHour() / 3600;
    }

    /**
     * Get planet metal storage (max amount this planet can contain).
     */
    public function getMetalStorage($formatted = false) {
        $storage = $this->planet->metal_max;

        if ($formatted) {
            $storage = number_format($storage, 0, ',', '.');
        }

        return $storage;
    }

    /**
     * Get planet crystal amount.
     */
    public function getCrystal($formatted = false) {
        $crystal = $this->planet->crystal;

        if ($formatted) {
            $crystal = number_format($crystal, 0, ',', '.');
        }

        return $crystal;
    }

    /**
     * Get planet crystal production per hour.
     */
    public function getCrystalProductionPerHour($formatted = false) {
        $production = $this->planet->crystal_production;

        if ($formatted) {
            $production = number_format($production, 0, ',', '.');
        }

        return $production;
    }

    /**
     * Get planet crystal production per second (decimal number).
     */
    public function getCrystalProductionPerSecond() {
        return $this->getCrystalProductionPerHour() / 3600;
    }

    /**
     * Get planet crystal storage (max amount this planet can contain).
     */
    public function getCrystalStorage($formatted = false) {
        $storage = $this->planet->crystal_max;

        if ($formatted) {
            $storage = number_format($storage, 0, ',', '.');
        }

        return $storage;
    }

    /**
     * Get planet deuterium amount.
     */
    public function getDeuterium($formatted = false) {
        $deuterium = $this->planet->deuterium;

        if ($formatted) {
            $deuterium = number_format($deuterium, 0, ',', '.');
        }

        return $deuterium;
    }

    /**
     * Get planet deuterium production per hour.
     */
    public function getDeuteriumProductionPerHour($formatted = false) {
        $production = $this->planet->deuterium_production;

        if ($formatted) {
            $production = number_format($production, 0, ',', '.');
        }

        return $production;
    }

    /**
     * Get planet deuterium production per second (decimal number).
     */
    public function getDeuteriumProductionPerSecond() {
        return $this->getDeuteriumProductionPerHour() / 3600;
    }

    /**
     * Get planet deuterium storage (max amount this planet can contain).
     */
    public function getDeuteriumStorage($formatted = false) {
        $storage = $this->planet->deuterium_max;

        if ($formatted) {
            $storage = number_format($storage, 0, ',', '.');
        }

        return $storage;
    }

    /**
     * Get planet energy amount.
     */
    public function getEnergy($formatted = false) {
        $energy_max = $this->planet->energy_max;
        $energy_used = $this->planet->energy_used;

        $energy = $energy_max - $energy_used;

        if ($formatted) {
            $energy = number_format($energy, 0, ',', '.');
        }

        return $energy;
    }

    /**
     * Get planet energy consumption.
     */
    public function getEnergyConsumption($formatted = false) {
        $energy_consumption = $this->planet->energy_used;

        if ($formatted) {
            $energy_consumption = number_format($energy_consumption, 0, ',', '.');
        }

        return $energy_consumption;
    }

    /**
     * Get planet energy production.
     */
    public function getEnergyProduction($formatted = false) {
        $energy_production = $this->planet->energy_max;

        if ($formatted) {
            $energy_production = number_format($energy_production, 0, ',', '.');
        }

        return $energy_production;
    }

    /**
     * Checks if this planet has equal or more than the requested resources.
     */
    public function hasResources($resources) {
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
     * Adds resources to a planet.
     */
    public function addResources($resources) {
        if (!empty($resources['metal'])) {
            $this->planet->metal += $resources['metal'];
        }
        if (!empty($resources['crystal'])) {
            $this->planet->crystal += $resources['crystal'];
        }
        if (!empty($resources['deuterium'])) {
            $this->planet->deuterium += $resources['deuterium'];
        }

        $this->planet->save();
    }

    /**
     * Removes resources from planet.
     */
    public function deductResources($resources) {
        // Sanity check that this planet has enough resources, if not throw
        // exception.
        if (!$this->hasResources($resources)) {
            throw new \Exception('Planet does not have enough resources.');
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
     * Creates a new random planet.
     *
     * @param $user_id
     *  The user_id of which to generate the planet for.
     */
    public function create($user_id) {
        $planet = new Planet;
        $planet->user_id = $user_id;
        $planet->name = 'MyPlanet';
        $planet->galaxy = 1;
        $planet->system = rand(1,10); // @TODO: add check that the new position is random always (no collissions allowed!)
        $planet->planet = rand(1,15); // @TODO: add check that the new position is random always (no collissions allowed!)
        $planet->planet_type = 1; //?
        $planet->destroyed = 0;
        $planet->diameter = 300;
        $planet->field_current = 0;
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

        $planet->time_last_update = time();

        $planet['field_max'] = rand(140,250);
        $planet['temp_min'] = rand(0,100);
        $planet['temp_max'] = $planet['temp_min'] + 40;

        $planet->save();

        // Save planet model to object.
        $this->planet = $planet;
    }

    /**
     * Gets the level of a building on this planet.
     */
    public function getBuildingLevel($building_id) {
        $building = $this->objects->getBuildings($building_id);

        // Sanity check: if building does not exist yet then return 0.
        // @TODO: remove when all buildings have been included.
        if (empty($building)) {
            return 0;
        }

        $building_level = $this->planet->{$building['machine_name']};

        if ($building_level) {
            return $building_level;
        }
        else {
            return 0;
        }
    }

    /**
     * Get the amount of objects (units) on this planet. E.g. ships.
     */
    function getObjectAmount($object_id) {
        $object = $this->objects->getBuildings($object_id);

        return $this->planet->{$object['machine_name']};
    }

    /**
     * Gets the time of upgrading a building on this planet to the next level.
     */
    public function getBuildingTime($building_id, $formatted = FALSE) {
        $building = $this->objects->getBuildings($building_id);

        // Sanity check: if building does not exist yet then return empty array.
        // @TODO: remove when all buildings have been included.
        if (empty($building)) {
            return [];
        }

        $current_level = $this->getBuildingLevel($building_id);
        $next_level = $current_level + 1;
        $price = $this->objects->getObjectPrice($building_id, $this);

        $robotfactory_level = 0; // @TODO: implement robot factory.
        $nanitefactory_level = 0; // @TODO: implement nanite factory.
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
        }
        else {
            return $time_seconds;
        }
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
    public function getBuildingProduction($building_id, $building_level = false) {
        $building = $this->objects->getBuildings($building_id);

        // Sanity check: if building does not exist yet then return empty array.
        // @TODO: remove when all buildings have been included.
        if (empty($building['production'])) {
            return [];
        }

        $production = array();
        $resource_production_factor = 100; // Set default to 100, only override
        // when the building level is not set (which means current output is
        // asked for).
        if (!$building_level) {
            $building_level = $this->getBuildingLevel($building_id);
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
            }
            else {
                $production[$resource] = $production[$resource] * ($resource_production_factor / 100);
            }

            // Round down for energy.
            // Round up for positive resources, round down for negative resources.
            // This makes resource production better, and energy consumption worse.
            if ($resource == 'energy') {
                $production[$resource] = floor($production[$resource]);
            }
            elseif ($production[$resource] > 0) {
                $production[$resource] = ceil($production[$resource]);
            }
            else {
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
    public function getResourceProductionFactor() {
        if ($this->getEnergyProduction() == 0 || $this->getEnergyConsumption() == 0) {
            return 0;
        }

        $production_factor = floor($this->getEnergyProduction() / $this->getEnergyConsumption() * 100);

        // Force min 0, max 100.
        if ($production_factor > 100) {
            $production_factor = 100;
        }
        elseif ($production_factor < 0) {
            $production_factor = 0;
        }

        return $production_factor;
    }

    /**
     * Gets the storage value of a building on this planet.
     */
    public function getBuildingStorage($building_id) {
        $building = $this->objects->getBuildings($building_id);

        // Sanity check: if building does not exist yet then return empty array.
        // @TODO: remove when all buildings have been included.
        if (empty($building)) {
            return [];
        }

        // The actual formula which return time in seconds
        $building_level = $this->getBuildingLevel($building_id);
        $storage = array();
        foreach ($building['storage'] as $resource => $storage_formula) {
            $storage[$resource] = eval($storage_formula);
        }

        return $storage;
    }

    /**
     * Sets the building production percentage.
     *
     * @param $building_id
     * @param $percentage
     */
    public function setBuildingPercent($building_id, $percentage) {
        $building = $this->objects->getBuildings($building_id);

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
     * Get building production percentage.
     *
     * @return int
     */
    public function getBuildingPercent($building_id) {
        $building = $this->objects->getBuildings($building_id);

        // Sanity check: model property exists.
        if (!isset($this->planet->{$building['machine_name'] . '_percent'})) {
            return false;
        }

        return $this->planet->{$building['machine_name'] . '_percent'};
    }

    /**
     * Helper method to convert building time from seconds to human
     * readable format.
     */
    public function formatBuildingTime($seconds) {
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
     * Returns basic income (resources) information for this planet.
     */
    public function getPlanetBasicIncome() {
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
     * Update this planet's resources, buildings, shipyard, defenses and research.
     * This should happen on every users page load and every time the planet is touched.
     */
    public function update() {
        // ------
        // 1. Update building queue
        // ------
        $queue = resolve('OGame\Services\BuildingQueueService');
        $build_queue = $queue->retrieveFinished($this->getPlanetId());

        // @TODO: add DB transaction wrapper
        foreach ($build_queue as $item) {
            // Get object information of building.
            $building = $this->objects->getBuildings($item->object_id);

            // Update build queue record
            $item->processed = 1;
            $item->save();

            // Update planet and update level of the building that has been processed.
            $this->planet->{$building['machine_name']} = $item->object_level_target;
            $this->planet->save();

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        // ------
        // 2. Update unit queue
        // ------
        $queue = resolve('OGame\Services\UnitQueueService');
        $unit_queue = $queue->retrieveBuilding($this->getPlanetId());

        // @TODO: add DB transaction wrapper
        foreach ($unit_queue as $item) {
            // Get object information.
            $object = $this->objects->getBuildings($item->object_id);

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
            $last_update_diff = time() - $last_update;

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
                $this->planet->save();
            }
        }

        // ------
        // 3. Update resource production / consumption
        // ------
        $production_total = [];
        $energy_production_total = 0;
        $energy_consumption_total = 0;

        // Get basic income resource values.
        foreach ($this->getPlanetBasicIncome() as $key => $value) {
            if (!empty($production_total[$key])) {
                $production_total[$key] += $value;
            }
            else {
                $production_total[$key] = $value;
            }

            if ($key == 'energy') {
                if ($value > 0) {
                    $energy_production_total += $value;
                }
                elseif ($value < 0) {
                    // Multiplies the negative number with "-1" so it will become
                    // a positive number, which is what the system expects.
                    $energy_consumption_total += $value * -1;
                }
            }
        }

        // Get all buildings that have production values.
        foreach ($this->objects->getBuildingsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->getBuildingProduction($building['id']);

            // Combine values to one array so we have the total production.
            foreach ($production as $key => $value) {
                if (!empty($production_total[$key])) {
                    $production_total[$key] += $value;
                }
                else {
                    $production_total[$key] = $value;
                }
            }

            if ($production['energy'] > 0) {
                $energy_production_total += $production['energy'];
            }
            elseif ($production['energy'] < 0) {
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
        $this->planet->save();

        // ------
        // 4. Update resource storage
        // ------
        $storage_total = [];
        foreach ($this->objects->getBuildingsWithStorage() as $building) {
            // Retrieve all buildings that have production values.
            $storage = $this->getBuildingStorage($building['id']);

            // Combine values to one array so we have the total storage.
            foreach ($storage as $key => $value) {
                if (!empty($storage_total[$key])) {
                    $storage_total[$key] += $value;
                }
                else {
                    $storage_total[$key] = $value;
                }
            }
        }

        // Write values to planet
        $this->planet->metal_max = $storage_total['metal'];
        $this->planet->crystal_max = $storage_total['crystal'];
        $this->planet->deuterium_max = $storage_total['deuterium'];
        $this->planet->save();

        // ------
        // 5. Update resources amount in planet based on hourly production values.
        // ------
        $time_last_update = $this->planet->time_last_update;
        $current_time = time();
        $resources_add = [];

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

            $this->addResources($resources_add);

            $this->planet->time_last_update = $current_time;
            $this->planet->save();
        }
    }
}
