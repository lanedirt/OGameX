<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Models\Resources;
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
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects): View
    {
        $this->setBodyId('resources');

        // Prepare custom properties
        $this->header_filename_objects = [1, 2, 3, 4]; // Building ID's that make up the header filename.
        $this->objects = [
            0 => ['metal_mine', 'crystal_mine', 'deuterium_synthesizer', 'solar_plant', 'fusion_plant', 'solar_satellite'],
            1 => ['metal_store', 'crystal_store', 'deuterium_store', ],
        ];
        $this->view_name = 'ingame.resources.index';

        return parent::index($request, $player, $objects);
    }

    /**
     * Handles the resources page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws \Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        return $this->ajaxHandler($request, $player, $objects);
    }

    /**
     * Resources settings page.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws \Exception
     */
    public function settings(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        $this->setBodyId('resourceSettings');
        $this->planet = $player->planets->current();

        $building_resource_rows = [];
        $building_energy_rows = [];
        $production_total = new Resources(0,0,0,0);

        // Get basic income resource values.
        $production_total->add($this->planet->getPlanetBasicIncome());

        // Buildings that provide resource income
        // Get all buildings that have production values.
        foreach ($objects->getBuildingObjectsWithProduction() as $building) {
            // Retrieve all buildings that have production values.
            $production = $this->planet->getBuildingProduction($building->machine_name);
            $production_total->add($production);

            if ($production->energy->get() < 0) {
                // Building consumes energy (resource building)
                $building_resource_rows[] = [
                    'id' => $building->id,
                    'title' => $building->title,
                    'level' => $this->planet->getObjectLevel($building->machine_name),
                    'production' => $production,
                    'actual_energy_use' => floor($production->energy->get() * ($this->planet->getResourceProductionFactor() / 100)),
                    'percentage' => $this->planet->getBuildingPercent($building->machine_name),
                ];
            } else {
                // Building produces energy (energy building)
                $building_energy_rows[] = [
                    'id' => $building->id,
                    'title' => $building->title,
                    'level' => $this->planet->getObjectLevel($building->machine_name),
                    'production' => $production,
                    'percentage' => $this->planet->getBuildingPercent($building->machine_name),
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
            'metal' => $this->planet->metal()->get(),
            'metal_storage' => $this->planet->metalStorage()->get(),
            'metal_storage_formatted' => $this->planet->metalStorage()->getFormatted(),
            'crystal' => $this->planet->crystal()->get(),
            'crystal_storage' => $this->planet->crystalStorage()->get(),
            'crystal_storage_formatted' => $this->planet->crystalStorage()->getFormatted(),
            'deuterium' => $this->planet->deuterium()->get(),
            'deuterium_storage' => $this->planet->deuteriumStorage()->get(),
            'deuterium_storage_formatted' => $this->planet->deuteriumStorage()->getFormatted(),
        ]);
    }

    /**
     * Resources settings page.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     *
     * @return RedirectResponse
     */
    public function settingsUpdate(Request $request, PlayerService $player, ObjectService $objects) : RedirectResponse
    {
        $this->planet = $player->planets->current();

        foreach ($request->input() as $key => $value) {
            if (stristr($key, 'last')) {
                $object_id = (int)str_replace('last', '', $key);
                // Update percentage (in increments of 10)
                $this->planet->setBuildingPercent($object_id, (int)$value);
            }
        }

        // @TODO: add resource recalculation method here when this value will be
        // cached.
        return redirect()->route('resources.settings');
    }
}
