<?php

namespace OGame\Http\Traits;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OGame\Facades\AppUtil;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

trait ObjectAjaxTrait
{
    /**
     * Handles the resources page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return JsonResponse
     * @throws Exception
     */
    public function ajaxHandler(Request $request, PlayerService $player, ObjectService $objects): JsonResponse
    {
        $planet = $player->planets->current();

        $object_id = $request->input('technology');
        if (empty($object_id)) {
            throw new Exception('No object ID provided.');
        }

        $object = $objects->getObjectById($object_id);

        $current_level = 0;
        if ($object->type == 'research') {
            $current_level = $player->getResearchLevel($object->machine_name);
        } else {
            $current_level = $planet->getObjectLevel($object->machine_name);
        }
        $next_level = $current_level + 1;

        // Check requirements of this object
        $requirements_met = $objects->objectRequirementsMet($object->machine_name, $planet, $player);

        $price = $objects->getObjectPrice($object->machine_name, $planet);

        // Get max build amount of this object (unit).
        $max_build_amount = $objects->getObjectMaxBuildAmount($object->machine_name, $planet);

        // Switch
        $production_time = '';
        $production_datetime = '';
        switch ($object->type) {
            case 'building':
            case 'station':
                $production_time = AppUtil::formatTimeDuration($planet->getBuildingConstructionTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getBuildingConstructionTime($object->machine_name));
                break;
            case 'ship':
            case 'defense':
                $production_time = AppUtil::formatTimeDuration($planet->getUnitConstructionTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getUnitConstructionTime($object->machine_name));
                break;
            case 'research':
                $production_time = AppUtil::formatTimeDuration($planet->getTechnologyResearchTime($object->machine_name));
                $production_datetime = AppUtil::formatDateTimeDuration($planet->getTechnologyResearchTime($object->machine_name));
                break;
            default:
                // Unknown object type, throw error.
                throw new Exception('Unknown object type: ' . $object->type);
        }

        // Get current amount of this object (unit) on the current planet.
        $current_amount = 0;
        if ($object->type == 'ship' || $object->type == 'defense') {
            $current_amount = $planet->getObjectAmount($object->machine_name);
        }

        $production_current = [];
        $production_next = [];
        $energy_difference = 0;
        if (!empty($object->production)) {
            $production_current = $planet->getBuildingProduction($object->machine_name);
            $production_next = $planet->getBuildingProduction($object->machine_name, $next_level);

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
            'id' => $object_id,
            'object' => $object,
            'object_type' => $object->type,
            'planet_id' => $planet->getPlanetId(),
            'current_level' => $current_level,
            'next_level' => $next_level,
            'description' => $object->description,
            'title' => $object->title,
            'price' => $price,
            'planet' => $planet,
            'production_time' => $production_time,
            'production_datetime' => $production_datetime,
            'production_next' => $production_next,
            'energy_difference' => $energy_difference,
            'enough_resources' => $enough_resources,
            'requirements_met' => $requirements_met,
            'build_active' => $build_queue->count(),
            'build_active_current' => $build_active_current,
            'build_queue_max' => $build_queue_max,
            'storage' => $storage,
            'current_storage' => $current_storage,
            'max_storage' => $max_storage,
            'max_build_amount' => $max_build_amount,
            'current_amount' => $current_amount,
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
}
