<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Models\{
    ProductionIndex,
    Resources,
};
use OGame\Services\BuildingQueueService;
use OGame\Services\UnitQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use Carbon\Carbon;

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
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
    {
        $this->setBodyId('resources');
        $this->planet = $player->planets->current();

        // Prepare custom properties.
        // Header filename objects are the building IDs that make up the header filename
        // to be used in the background image of the page header.
        if ($this->planet->isPlanet()) {
            $this->header_filename_objects = [1, 2, 3, 4, 212];
        } elseif ($this->planet->isMoon()) {
            $this->header_filename_objects = [41, 42, 43];
        }

        $this->objects = [
            0 => ['metal_mine', 'crystal_mine', 'deuterium_synthesizer', 'solar_plant', 'fusion_plant', 'solar_satellite', 'metal_store', 'crystal_store', 'deuterium_store'],
        ];

        // Parse shipyard queue because the resources page shows both the
        // building queue (handled by parent) but also the shipyard queue.
        $unitQueue = resolve(UnitQueueService::class);
        $unit_full_queue = $unitQueue->retrieveQueue($this->planet);
        $unit_build_active = $unit_full_queue->getCurrentlyBuildingFromQueue();
        $unit_build_queue = $unit_full_queue->getQueuedFromQueue();

        // Get total time of all items in unit queue.
        $unit_queue_time_end = $unitQueue->retrieveQueueTimeEnd($this->planet);
        $unit_queue_time_countdown = 0;
        if ($unit_queue_time_end > 0) {
            $unit_queue_time_countdown = $unit_queue_time_end - (int)Carbon::now()->timestamp;
        }

        // Append unit queue data to the default view output handled by parent.
        return view('ingame.resources.index')->with(
            parent::indexPageParams($request, $player) + [
                'unit_build_active' => $unit_build_active,
                'unit_build_queue' => $unit_build_queue,
                'unit_queue_time_countdown' => $unit_queue_time_countdown,
            ]
        );
    }

    /**
     * Handles the resources page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player): JsonResponse
    {
        return $this->ajaxHandler($request, $player);
    }

    /**
     * Resources settings page.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function settings(Request $request, PlayerService $player): View
    {
        $this->setBodyId('resourceSettings');
        $this->planet = $player->planets->current();

        $building_resource_rows = [];
        $building_energy_rows = [];

        $productionindex_total = new ProductionIndex();
        $productionindex_total->total->add($this->planet->getPlanetBasicIncome());

        // Buildings that provide resource income
        // Get all buildings that have production values.
        foreach (ObjectService::getGameObjectsWithProduction() as $building) {
            $level = $this->planet->getObjectLevel($building->machine_name);
            $productionindex = $this->planet->getObjectProductionIndex($building, $level);
            $productionindex_total->add($productionindex);

            $productionindex->mine->add($productionindex->planet_slot);

            $percentage = $this->planet->getBuildingPercent($building->machine_name);

            if ($productionindex->mine->energy->get() < 0) {
                // Building consumes energy (resource building)
                $building_resource_rows[] = [
                    'id' => $building->id,
                    'title' => $building->title,
                    'level' => $level,
                    'production' => $productionindex->mine,
                    'actual_energy_use' => floor($productionindex->mine->energy->get() * ($this->planet->getResourceProductionFactor() / 100)),
                    'percentage' => $percentage,
                ];
            } else {
                // Building produces energy (energy building)
                $building_energy_rows[] = [
                    'id' => $building->id,
                    'type' => $building->type,
                    'title' => $building->title,
                    'level' => $level,
                    'production' => $productionindex->mine,
                    'percentage' => $percentage,
                ];
            }
        }

        $production_factor = $this->planet->getResourceProductionFactor();

        return view('ingame.resources.settings')->with([
            'basic_income' => $this->planet->getPlanetBasicIncome(),
            'planet_name' => $this->planet->getPlanetName(),
            'building_resource_rows' => $building_resource_rows,
            'building_energy_rows' => $building_energy_rows,
            'production_total' => $productionindex_total,
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
            'plasma_technology_level' => $player->getResearchLevel('plasma_technology'),
            'officers' => [
                'commanding_staff' => $player->hasCommandingStaff(),
                'engineer'  => $player->hasEngineer(),
                'geologist' => $player->hasGeologist(),
            ]
        ]);
    }

    /**
     * Resources settings page.
     *
     * @param Request $request
     * @param PlayerService $player
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function settingsUpdate(Request $request, PlayerService $player): RedirectResponse
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
