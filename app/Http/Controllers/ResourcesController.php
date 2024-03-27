<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class ResourcesController extends AbstractBuildingsController
{
    /**
     * ResearchController constructor.
     */
    public function __construct(BuildingQueueService $queue)
    {
        $this->route_view_index = 'resources.index';
        parent::__construct($queue);
    }

    /**
     * Shows the resources index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects)
    {
        // Prepare custom properties
        $this->header_filename_objects = [1, 2, 3, 4]; // Building ID's that make up the header filename.
        $this->objects = [
            0 => [1, 2, 3, 4, 12, 212],
            1 => [22, 23, 24],
        ];
        $this->body_id = 'resources';
        $this->view_name = 'ingame.resources.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the resources page AJAX requests.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->building_type = 'resources';

        return $this->ajaxHandler($request, $player, $objects);
    }

    /**
     * Resources settings page.
     */
    public function settings(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->planet = $player->planets->current();

        $building_resource_rows = [];
        $building_energy_rows = [];
        $production_total = [];

        // Get basic income resource values.
        foreach ($this->planet->getPlanetBasicIncome() as $key => $value) {
            if (!empty($production_total[$key])) {
                $production_total[$key] += $value;
            } else {
                $production_total[$key] = $value;
            }
        }

        // Buildings that provide resource income
        // Get all buildings that have production values.
        foreach ($objects->getBuildingObjectsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->planet->getBuildingProduction($building['id']);

            // Combine values to one array so we have the total production.
            foreach ($production as $key => $value) {
                if (!empty($production_total[$key])) {
                    $production_total[$key] += $value;
                } else {
                    $production_total[$key] = $value;
                }
            }

            if ($production['energy'] < 0) {
                // Building consumes energy (resource building)
                $building_resource_rows[] = [
                    'id' => $building['id'],
                    'title' => $building['title'],
                    'level' => $this->planet->getObjectLevel($building['id']),
                    'production' => $production,
                    'actual_energy_use' => floor($production['energy'] * ($this->planet->getResourceProductionFactor() / 100)),
                    'percentage' => $this->planet->getBuildingPercent($building['id']),
                ];
            } else {
                // Building produces energy (energy building)
                $building_energy_rows[] = [
                    'id' => $building['id'],
                    'title' => $building['title'],
                    'level' => $this->planet->getObjectLevel($building['id']),
                    'production' => $production,
                    'percentage' => $this->planet->getBuildingPercent($building['id']),
                ];
            }
        }

        // Ships that provide resource income
        // @TODO: add solar satellites as resource income (energy)

        // Research that provide resource income
        // @TODO: add plasms research as resource income (bonus to all)

        // @TODO: add item bonuses.

        // @TODO: add premium bonuses.

        $production_factor = $this->planet->getResourceProductionFactor();

        return view('ingame.resources.settings')->with([
            'basic_income' => $this->planet->getPlanetBasicIncome(),
            'planet_name' => $this->planet->getPlanetName(),
            'building_resource_rows' => $building_resource_rows,
            'building_energy_rows' => $building_energy_rows,
            'production_total' => $production_total,
            'production_factor' => $production_factor,
            'metal' => $this->planet->getMetal(),
            'metal_storage' => $this->planet->getMetalStorage(),
            'metal_storage_formatted' => $this->planet->getMetalStorage(true),
            'crystal' => $this->planet->getCrystal(),
            'crystal_storage' => $this->planet->getCrystalStorage(),
            'crystal_storage_formatted' => $this->planet->getCrystalStorage(true),
            'deuterium' => $this->planet->getDeuterium(),
            'deuterium_storage' => $this->planet->getDeuteriumStorage(),
            'deuterium_storage_formatted' => $this->planet->getDeuteriumStorage(true),
            'body_id' => 'resourceSettings', // Sets <body> tag ID property.
        ]);
    }

    /**
     * Resources settings page.
     */
    public function settingsUpdate(Request $request, PlayerService $player, ObjectService $objects)
    {
        $this->planet = $player->planets->current();

        foreach ($request->input() as $key => $value) {
            if (stristr($key, 'last')) {
                $object_id = str_replace('last', '', $key);
                if (is_numeric($object_id)) {
                    // Update percentage (in increments of 10)
                    $this->planet->setBuildingPercent($object_id, $value);
                }
            }
        }

        // @TODO: add resource recalculation method here when this value will be
        // cached.
        return redirect()->route('resources.settings');
    }
}
