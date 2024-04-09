<?php

namespace OGame\Http\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\Objects\ObjectService;
use OGame\Services\PlayerService;

trait ObjectAjaxTrait
{
    /**
     * Handles the resources page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function ajaxHandler(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        $planet = $player->planets->current();

        $object_id = $request->input('type');
        if (empty($object_id)) {
            throw new Exception('No building ID provided.');
        }

        $object = $objects->getObjects($object_id);
        if (empty($object)) {
            throw new Exception('Incorrect building ID provided.');
        }

        $current_level = 0;
        if ($object['type'] == 'building') {
            $current_level = $planet->getObjectLevel($object['id']);
        } elseif ($object['type'] == 'research') {
            $current_level = $player->getResearchLevel($object['id']);
        }
        $next_level = $current_level + 1;

        // Check requirements of this object
        $requirements_met = $objects->objectRequirementsMet($object_id, $planet, $player);

        $price = $objects->getObjectPrice($object['id'], $planet);
        $price_formatted = $objects->getObjectPrice($object['id'], $planet, true);

        // Get max build amount of this object (unit).
        $max_build_amount = $objects->getObjectMaxBuildAmount($object['id'], $planet);

        // Switch
        switch ($object['type']) {
            case 'building':
            case 'station':
                $production_time = $planet->getBuildingConstructionTime($object['id'], true);
                break;
            case 'ship':
            case 'defense':
                $production_time = $planet->getUnitConstructionTime($object['id'], true);
                break;
            case 'research':
                $production_time = $planet->getTechnologyResearchTime($object['id'], true);
                break;
            default:
                // Unknown object type, throw error.
                throw new Exception('Unknown object type: ' . $object['type']);
        }

        // Get current amount of this object (unit) on the current planet.
        $current_amount = 0;
        if ($object['type'] == 'ship' || $object['type'] == 'defense') {
            $current_amount = $planet->getObjectAmount($object['id']);
        }

        $production_current = [];
        $production_next = [];
        $energy_difference = 0;
        if (!empty($object['production'])) {
            $production_current = $planet->getBuildingProduction($object['id']);
            $production_next = $planet->getBuildingProduction($object['id'], $next_level);
            if (!empty($production_current['energy'])) {
                $energy_difference = ($production_next['energy'] - $production_current['energy']) * -1;
            }
        }

        $enough_resources = $planet->hasResources($price);

        // Storage capacity bar
        $storage = !empty($object['storage']);
        $current_storage = 0;
        $max_storage = 0;
        if ($storage) {
            switch ($object['id']) {
                case 22:
                    $max_storage = $planet->getMetalStorage();
                    $current_storage = $planet->getMetal();
                    break;

                case 23:
                    $max_storage = $planet->getCrystalStorage();
                    $current_storage = $planet->getCrystal();
                    break;

                case 24:
                    $max_storage = $planet->getDeuteriumStorage();
                    $current_storage = $planet->getDeuterium();
                    break;
            }
        }

        $build_queue = $this->queue->retrieveQueue($planet);
        $build_queue = $this->queue->enrich($build_queue);

        $build_active_current = false;
        if (!empty($build_queue)) {
            foreach ($build_queue as $record) {
                if ($object['id'] == $record['object']['id']) {
                    $build_active_current = $record;
                }
            }
        }

        // Max amount of buildings that can be in the queue in a given time.
        $max_build_queue_count = 4; //@TODO: refactor into global / constant?
        $build_queue_max = false;
        if (count($build_queue) >= $max_build_queue_count) {
            $build_queue_max = true;
        }

        return view('ingame.ajax.object')->with([
            'id' => $object_id,
            'object_type' => $object['type'],
            'planet_id' => $planet->getPlanetId(),
            'current_level' => $current_level,
            'next_level' => $next_level,
            'description' => $object['description'],
            'title' => $object['title'],
            'price' => $price,
            'price_formatted' => $price_formatted,
            'planet' => $planet,
            'production_time' => $production_time,
            'production_next' => $production_next,
            'energy_difference' => $energy_difference,
            'enough_resources' => $enough_resources,
            'requirements_met' => $requirements_met,
            'build_active' => count($build_queue),
            'build_active_current' => $build_active_current,
            'build_queue_max' => $build_queue_max,
            'storage' => $storage,
            'current_storage' => $current_storage,
            'max_storage' => $max_storage,
            'max_build_amount' => $max_build_amount,
            'current_amount' => $current_amount,
        ]);
    }
}
