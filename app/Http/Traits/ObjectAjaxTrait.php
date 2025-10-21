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

            // Replace any occurrence of "produces [number] energy" with the calculated value
            $description = preg_replace(
                '/produces \d+ energy/',
                "produces {$energyPerUnit} energy",
                $description
            );
        }

        return $description;
    }
}
