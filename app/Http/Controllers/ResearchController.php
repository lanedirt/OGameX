<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\Objects\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;
use OGame\ViewModels\BuildingViewModel;

class ResearchController extends OGameController
{
    use ObjectAjaxTrait;

    protected PlanetService $planet;

    /**
     * @var string Index view route (used for redirecting).
     */
    protected string $route_view_index;

    /**
     * QueueService
     *
     * @var ResearchQueueService
     */
    protected ResearchQueueService $queue;

    /**
     * ResourcesController constructor.
     */
    public function __construct(ResearchQueueService $queue)
    {
        $this->route_view_index = 'research.index';
        $this->queue = $queue;
        parent::__construct();
    }

    /**
     * Shows the research index page
     *
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function index(PlayerService $player, ObjectService $objects) : View
    {
        $this->setBodyId('research');
        $planet = $player->planets->current();

        // Prepare custom properties
        $screen_objects = [
            0 => ['energy_technology', 'laser_technology', 'ion_technology', 'hyperspace_technology', 'plasma_technology'],
            1 => ['combustion_drive', 'impulse_drive', 'hyperspace_drive'],
            2 => ['espionage_technology', 'computer_technology', 'astrophysics', 'intergalactic_research_network', 'graviton_technology'],
            3 => ['weapon_technology', 'shielding_technology', 'armor_technology'],
        ];

        $count = 0;

        // Parse build queue for this planet
        $research_full_queue = $this->queue->retrieveQueue($planet);
        $build_active = $this->queue->enrich($this->queue->retrieveCurrentlyBuildingFromQueue($research_full_queue));
        $build_queue = $this->queue->enrich($this->queue->retrieveQueuedFromQueue($research_full_queue));

        $research = [];
        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = $objects->getResearchObjectByMachineName($object_machine_name);

                // Get current level of building
                $current_level = $player->getResearchLevel($object->machine_name);

                // Check requirements of this building
                $requirements_met = $objects->objectRequirementsMet($object->machine_name, $planet, $player);

                // Check if the current planet has enough resources to build this building.
                $enough_resources = $planet->hasResources($objects->getObjectPrice($object->machine_name, $planet));

                $view_model = new BuildingViewModel();
                $view_model->object = $object;
                $view_model->current_level = $current_level;
                $view_model->requirements_met = $requirements_met;
                $view_model->count = $count;
                $view_model->enough_resources = $enough_resources;
                $view_model->currently_building = (!empty($build_active['id']) && $build_active['object']['id'] == $object->id);

                $research[$key_row][$object->id] = $view_model;
            }
        }

        // Max amount of buildings that can be in the queue in a given time.
        $max_build_queue_count = 4; //@TODO: refactor into global / constant?
        $build_queue_max = false;
        if (count($build_queue) >= $max_build_queue_count) {
            $build_queue_max = true;
        }

        return view('ingame.research.index')->with([
            'planet_id' => $planet->getPlanetId(),
            'planet_name' => $planet->getPlanetName(),
            'research' => $research,
            'build_active' => $build_active,
            'build_queue' => $build_queue,
            'build_queue_max' => $build_queue_max,
        ]);
    }

    /**
     * Handles the research page AJAX requests.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws Exception
     */
    public function ajax(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        return $this->ajaxHandler($request, $player, $objects);
    }

    /**
     * Handles an incoming add buildrequest.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     * @throws Exception
     */
    public function addBuildRequest(Request $request, PlayerService $player) : RedirectResponse
    {
        // Explicitly verify CSRF token because this request supports both POST and GET.
        if (!hash_equals($request->session()->token(), $request->input('_token'))) {
            return redirect()->route($this->route_view_index);
        }

        $building_id = $request->input('type');
        $planet_id = $request->input('planet_id');

        $planet = $player->planets->childPlanetById($planet_id);
        $this->queue->add($player, $planet, $building_id);

        return redirect()->route($this->route_view_index);
    }

    /**
     * Handles an incoming cancel buildrequest.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function cancelBuildRequest(Request $request, PlayerService $player) : RedirectResponse
    {
        $building_id = $request->input('building_id');
        $building_queue_id = $request->input('building_queue_id');

        $this->queue->cancel($player, $player->planets->current(), $building_queue_id, $building_id);

        if (!empty($request->input('redirect')) && $request->input('redirect') == 'overview') {
            return redirect()->route('overview.index');
        } else {
            return redirect()->route($this->route_view_index);
        }
    }
}
