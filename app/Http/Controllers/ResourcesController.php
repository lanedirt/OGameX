<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use OGame\Http\Controllers\Abstracts\AbstractBuildingsController;
use OGame\Models\{
    ProductionIndex,
    Resources,
};
use OGame\Services\BuildingQueueService;
use OGame\Services\CharacterClassService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

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
            0 => ['metal_mine', 'crystal_mine', 'deuterium_synthesizer', 'solar_plant', 'fusion_plant', 'solar_satellite', 'crawler', 'metal_store', 'crystal_store', 'deuterium_store'],
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
            $unit_queue_time_countdown = $unit_queue_time_end - (int)Date::now()->timestamp;
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

            // Calculate production without character class bonus for individual building display
            $production_without_class = new Resources();
            $production_without_class->add($productionindex->mine);
            $production_without_class->add($productionindex->plasma_technology);
            $production_without_class->add($productionindex->planet_slot);
            $production_without_class->add($productionindex->engineer);
            $production_without_class->add($productionindex->geologist);
            $production_without_class->add($productionindex->commanding_staff);
            $production_without_class->add($productionindex->items);

            // Round the values
            $production_without_class->metal->set(ceil($production_without_class->metal->get()));
            $production_without_class->crystal->set(ceil($production_without_class->crystal->get()));
            $production_without_class->deuterium->set(ceil($production_without_class->deuterium->get()));
            $production_without_class->energy->set(floor($production_without_class->energy->get()));

            $percentage = $this->planet->getBuildingPercent($building->machine_name);

            if ($production_without_class->energy->get() < 0) {
                // Building consumes energy (resource building)
                $building_resource_rows[] = [
                    'id' => $building->id,
                    'title' => $building->title,
                    'level' => $level,
                    'production' => $production_without_class,
                    'actual_energy_use' => floor($production_without_class->energy->get() * ($this->planet->getResourceProductionFactor() / 100)),
                    'percentage' => $percentage,
                ];
            } else {
                // Building produces energy (energy building)
                $building_energy_rows[] = [
                    'id' => $building->id,
                    'type' => $building->type,
                    'title' => $building->title,
                    'level' => $level,
                    'production' => $production_without_class,
                    'percentage' => $percentage,
                ];
            }
        }

        $production_factor = $this->planet->getResourceProductionFactor();

        $productionindex_total->total->metal->set($this->planet->getMetalProductionPerHour());
        $productionindex_total->total->crystal->set($this->planet->getCrystalProductionPerHour());
        $productionindex_total->total->deuterium->set($this->planet->getDeuteriumProductionPerHour());

        // Get crawler information
        $crawler_count = $this->planet->getObjectAmount('crawler');
        $metalMineLevel = $this->planet->getObjectLevel('metal_mine');
        $crystalMineLevel = $this->planet->getObjectLevel('crystal_mine');
        $deuteriumMineLevel = $this->planet->getObjectLevel('deuterium_synthesizer');
        $max_crawlers = ($metalMineLevel + $crystalMineLevel + $deuteriumMineLevel) * 8;
        $crawler_percentage = $this->planet->getBuildingPercent('crawler');

        // Get max crawler overload percentage (Collector: 150%, Others: 100%)
        $characterClassService = app(CharacterClassService::class);
        $max_crawler_overload = $characterClassService->getMaxCrawlerOverload($player->getUser());

        return view('ingame.resources.settings')->with([
            'currentPlayer' => $player,
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
            'crawler_count' => $crawler_count,
            'max_crawlers' => $max_crawlers,
            'crawler_percentage' => $crawler_percentage,
            'max_crawler_overload' => $max_crawler_overload,
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
