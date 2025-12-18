<?php

namespace OGame\Http\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OGame\Facades\AppUtil;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

trait ObjectAjaxTrait
{
    /**
     * Handles the resources page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function ajaxHandler(Request $request, PlayerService $player): JsonResponse
    {
        $planet = $player->planets->current();

        $object_id = $request->input('technology');
        if (empty($object_id)) {
            throw new Exception('No object ID provided.');
        }

        $object = ObjectService::getObjectById($object_id);

        $current_level = 0;
        if ($object->type == GameObjectType::Research) {
            $current_level = $player->getResearchLevel($object->machine_name);
        } elseif ($object->type == GameObjectType::Ship || $object->type == GameObjectType::Defense) {
            $current_level = $planet->getObjectAmount($object->machine_name);
        } else {
            $current_level = $planet->getObjectLevel($object->machine_name);
        }
        $next_level = $current_level + 1;

        // Check requirements of this object
        $requirements_met = ObjectService::objectRequirementsMetWithQueue($object->machine_name, $next_level, $planet);

        // Check if the current planet has the right type to build this object.
        $valid_planet_type = ObjectService::objectValidPlanetType($object->machine_name, $planet);

        $price = ObjectService::getObjectPrice($object->machine_name, $planet);

        // Get max build amount of this object (unit).
        $max_build_amount = ObjectService::getObjectMaxBuildAmount($object->machine_name, $planet, $requirements_met);

        // Switch
        $production_time = '';
        $production_datetime = '';
        switch ($object->type) {
            case GameObjectType::Building:
            case GameObjectType::Station:
                $production_time = AppUtil::formatTimeDuration($planet->getBuildingConstructionTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getBuildingConstructionTime($object->machine_name));

                // Research Lab upgrading is disallowed when research is in progress
                $research_in_progress = $player->isResearching();

                // Shipyard upgrading is not allowed when ships or defense units are in progress.
                $ship_or_defense_in_progress = $player->isBuildingShipsOrDefense();
                break;
            case GameObjectType::Ship:
                $production_time = AppUtil::formatTimeDuration($planet->getUnitConstructionTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getUnitConstructionTime($object->machine_name));

                $shipyard_upgrading = $player->planets->current()->isBuildingObject('shipyard');
                break;
            case GameObjectType::Defense:
                $production_time = AppUtil::formatTimeDuration($planet->getUnitConstructionTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getUnitConstructionTime($object->machine_name));

                $shipyard_upgrading = $player->planets->current()->isBuildingObject('shipyard');
                break;
            case GameObjectType::Research:
                $production_time = AppUtil::formatTimeDuration($planet->getTechnologyResearchTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getTechnologyResearchTime($object->machine_name));

                // Researching is disallowed when Research Lab is upgrading
                $research_lab_upgrading = $player->isBuildingObject('research_lab');
                break;
            default:
                // Unknown object type, throw error.
                throw new Exception('Unknown object type: ' . $object->type->name);
        }

        // Get current amount of this object (unit) on the current planet.
        $current_amount = 0;
        if ($object->type == GameObjectType::Ship || $object->type == GameObjectType::Defense) {
            $current_amount = $planet->getObjectAmount($object->machine_name);
        }

        $production_next = [];
        $energy_difference = 0;
        if (!empty($object->production)) {
            $production_current = $planet->getObjectProduction($object->machine_name);
            $production_next = $planet->getObjectProduction($object->machine_name, $next_level);

            if (!empty($production_current->energy->get())) {
                $energy_difference = ($production_next->energy->get() - $production_current->energy->get()) * -1;
            }
        }

        $enough_resources = $planet->hasResources($price);

        // Storage capacity bar
        // TODO: implement storage in new structure.
        $storage = !empty($object->storage);
        $current_storage = 0;
        $max_storage = 0;
        if ($storage) {
            switch ($object->machine_name) {
                case 'metal_store':
                    $max_storage = $planet->metalStorage()->get();
                    $current_storage = $planet->metal()->get();
                    break;

                case 'crystal_store':
                    $max_storage = $planet->crystalStorage()->get();
                    $current_storage = $planet->crystal()->get();
                    break;

                case 'deuterium_store':
                    $max_storage = $planet->deuteriumStorage()->get();
                    $current_storage = $planet->deuterium()->get();
                    break;
            }
        }

        $build_active_current = null;
        $build_queue = $this->queue->retrieveQueue($planet);
        $currently_building = $build_queue->getCurrentlyBuildingFromQueue();
        if (!empty($currently_building) && $currently_building->object->machine_name == $object->machine_name) {
            $build_active_current = $currently_building;
        }

        // Max amount of buildings that can be in the queue in a given time.
        $build_queue_max = false;
        if ($build_queue->isQueueFull()) {
            $build_queue_max = true;
        }

        // Calculate downgrade information for buildings and stations
        $downgrade_price = null;
        $downgrade_duration = null;
        $downgrade_duration_formatted = null;
        $ion_technology_level = 0;
        $ion_technology_bonus = 0;
        $can_downgrade = false;
        $downgrade_target_level = null;

        if (($object->type === GameObjectType::Building || $object->type === GameObjectType::Station) && $current_level > 0) {
            try {
                // Check if there are upgrades in queue for this building
                // If so, calculate downgrade price based on the target level after upgrades complete
                $max_target_level = $current_level;
                // @phpstan-ignore-next-line - method_exists check is needed for different queue service types
                if (method_exists($this->queue, 'retrieveQueueItems')) {
                    $queue_items = $this->queue->retrieveQueueItems($planet);
                    foreach ($queue_items as $item) {
                        $item_object = ObjectService::getObjectById($item->object_id);
                        if ($item_object->machine_name === $object->machine_name && !($item->is_downgrade ?? false)) {
                            // This is an upgrade for this building
                            if ($item->object_level_target > $max_target_level) {
                                $max_target_level = $item->object_level_target;
                            }
                        }
                    }
                }

                // Calculate downgrade price based on the highest target level (after all upgrades)
                $downgrade_target_level = $max_target_level - 1;
                $downgrade_price = ObjectService::getObjectDowngradePrice($object->machine_name, $planet, $max_target_level);

                // Calculate duration based on the target level
                // We need to create a temporary planet service with the target level to calculate duration
                $downgrade_duration = $planet->getBuildingDowngradeTime($object->machine_name, $max_target_level);
                $downgrade_duration_formatted = AppUtil::formatTimeDuration($downgrade_duration);

                // Get Ion technology level and calculate bonus
                // Ion Technology reduces teardown cost by 4% per level
                $player = $planet->getPlayer();
                if ($player !== null) {
                    $ion_technology_level = $player->getResearchLevel('ion_technology');
                    $ion_technology_bonus = $ion_technology_level * 4; // Percentage (e.g., level 24 = 96%)
                }

                // Check if building can be downgraded
                $can_downgrade = ObjectService::canDowngradeBuilding($object->machine_name, $planet);

                // Allow downgrade to be queued even if building is currently being upgraded
                // The downgrade will be processed after the upgrade completes
                // No need to block based on current building status or queue items
            } catch (Exception $e) {
                // If downgrade calculation fails, set can_downgrade to false
                $can_downgrade = false;
            }
        }

        $player = $planet->getPlayer();
        $is_in_vacation_mode = $player !== null && $player->isInVacationMode();

        $view_html = view('ingame.ajax.object')->with([
            'object' => $object,
            'object_type' => $object->type,
            'planet_id' => $planet->getPlanetId(),
            'current_level' => $current_level,
            'next_level' => $next_level,
            'description' => $this->getObjectDescription($object, $planet),
            'title' => $object->title,
            'price' => $price,
            'planet' => $planet,
            'production_time' => $production_time,
            'production_datetime' => $production_datetime,
            'production_next' => $production_next,
            'energy_difference' => $energy_difference,
            'enough_resources' => $enough_resources,
            'has_requirements' => $object->hasRequirements(),
            'requirements_met' => $requirements_met,
            'valid_planet_type' => $valid_planet_type,
            'build_active' => $build_queue->count(),
            'build_active_current' => $build_active_current,
            'build_queue_max' => $build_queue_max,
            'storage' => $storage,
            'current_storage' => $current_storage,
            'max_storage' => $max_storage,
            'max_build_amount' => $max_build_amount,
            'current_amount' => $current_amount,
            'research_lab_upgrading' => $research_lab_upgrading ?? false,
            'research_in_progress' => $research_in_progress ?? false,
            'shipyard_upgrading' => $shipyard_upgrading ?? false,
            'ship_or_defense_in_progress' => $ship_or_defense_in_progress ?? false,
            'downgrade_price' => $downgrade_price,
            'downgrade_duration' => $downgrade_duration,
            'downgrade_duration_formatted' => $downgrade_duration_formatted,
            'ion_technology_level' => $ion_technology_level,
            'ion_technology_bonus' => $ion_technology_bonus,
            'can_downgrade' => $can_downgrade,
            'is_in_vacation_mode' => $is_in_vacation_mode,
        ]);

        return response()->json([
            'target' => 'technologydetails',
            'content' => [
                'technologydetails' => $view_html->render(),
            ],
            'files' => [
                'js' => [],
                'css' => [],
            ],
            'newAjaxToken' => csrf_token(),
            'page' => [
                'stateObj' => [],
                'title' => 'OGameX',
                'url' => route('resources.index'),
            ],
            'serverTime' => time(),
        ]);
    }

    /**
     * Get the description for an object, with dynamic values for special cases.
     *
     * @param \OGame\GameObjects\Models\Abstracts\GameObject $object
     * @param \OGame\Services\PlanetService $planet
     * @return string
     */
    private function getObjectDescription($object, $planet): string
    {
        $description = $object->description;

        // Special handling for Solar Satellite to show correct energy production
        if ($object->machine_name === 'solar_satellite') {
            // Get the actual energy production per satellite considering production factor
            // This matches what the green (+X) number shows in the UI
            $current_amount = $planet->getObjectAmount('solar_satellite');
            $production_current = $planet->getObjectProduction('solar_satellite', $current_amount);
            $production_next = $planet->getObjectProduction('solar_satellite', $current_amount + 1);

            // Calculate energy per single satellite (the difference between current and next level)
            $energyPerUnit = abs($production_next->energy->get() - $production_current->energy->get());

            // Append the specific energy value to the description
            $description .= " A solar satellite produces {$energyPerUnit} energy on this planet.";
        }

        // Special handling for Interplanetary Missiles to show range based on impulse drive level
        if ($object->machine_name === 'interplanetary_missile') {
            $impulse_drive_level = $planet->getPlayer()->getResearchLevel('impulse_drive');
            $missile_range = max(0, $impulse_drive_level * 5 - 1);

            // Append the specific range value to the description
            $description .= " Your interplanetary missiles have got a coverage of {$missile_range} systems.";
        }

        return $description;
    }
}
