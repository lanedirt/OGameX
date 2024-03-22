<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;

class ResearchController
{
  use IngameTrait;
  use ObjectAjaxTrait;

  /**
   * ResourcesController constructor.
   */
  public function  __construct(ResearchQueueService $queue)
  {
    $this->route_view_index = 'research.index';
    $this->queue = $queue;
  }

  /**
   * Shows the research index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request, PlayerService $player, ObjectService $objects)
  {
    $this->player = $player;
    $this->planet = $player->planets->current();

    // Prepare custom properties
    $this->objects = [
      0 => [113, 120, 121, 114, 122],
      1 => [115, 117, 118],
      2 => [106, 108, 124, 123, 199],
      3 => [109, 110, 111],
    ];
    $this->body_id = 'research';
    $this->view_name = 'ingame.research.index';

    $objects_array = $objects->getResearchObjects();

    $count = 0;

    // Parse build queue for this planet
    $research_full_queue = $this->queue->retrieveQueue($this->planet);
    $build_active = $this->queue->enrich($this->queue->retrieveCurrentlyBuildingFromQueue($research_full_queue));
    $build_queue = $this->queue->enrich($this->queue->retrieveQueuedFromQueue($research_full_queue));

    $research = [];
    foreach ($this->objects as $key_row => $objects_row) {
      $buildings[$key_row] = [];

      foreach ($objects_row as $object_id) {
        $count++;

        // Get current level of building
        $current_level = $this->player->getResearchLevel($object_id);

        // Check requirements of this building
        $requirements_met = $objects->objectRequirementsMet($object_id, $this->planet, $player);

        // Check if the current planet has enough resources to build this building.
        $enough_resources = $this->planet->hasResources($objects->getObjectPrice($object_id, $this->planet));

        $research[$key_row][$object_id] = array_merge($objects_array[$object_id], [
          'current_level' => $current_level,
          'requirements_met' => $requirements_met,
          'count' => $count,
          'enough_resources' => $enough_resources,
          'currently_building' => (!empty($build_active['id']) && $build_active['object']['id'] == $object_id),
        ]);
      }
    }

    // Max amount of buildings that can be in the queue in a given time.
    $max_build_queue_count = 4; //@TODO: refactor into global / constant?
    $build_queue_max = false;
    if (count($build_queue) >= $max_build_queue_count) {
      $build_queue_max = true;
    }

    return view($this->view_name)->with([
      'planet_name' => $this->planet->getPlanetName(),
      'research' => $research,
      'build_active' => $build_active,
      'build_queue' => $build_queue,
      'build_queue_max' => $build_queue_max,
      'body_id' => $this->body_id, // Sets <body> tag ID property.
    ]);
  }

  /**
   * Handles the research page AJAX requests.
   *
   * @param  int  $id
   * @return Response
   */
  public function ajax(Request $request, PlayerService $player, ObjectService $objects)
  {
    $this->building_type = 'research';

    return $this->ajaxHandler($request, $player, $objects);
  }

  /**
   * Handles an incoming add buildrequest.
   */
  public function addBuildRequest(Request $request, PlayerService $player) {
    $building_id = $request->input('type');
    $planet_id = $request->input('planet_id');

    $planet = $player->planets->childPlanetById($planet_id);
    $this->queue->add($player, $planet, $building_id);

    return redirect()->route($this->route_view_index);
  }

  /**
   * Handles an incoming cancel buildrequest.
   */
  public function cancelBuildRequest(Request $request, PlayerService $player) {
    $building_id = $request->input('building_id');
    $building_queue_id = $request->input('building_queue_id');

    $this->queue->cancel($player, $player->planets->current(), $building_queue_id, $building_id);

    // @TODO: add checks if current user is owner of this build queue item.

    // @TODO: refund build cost if the building is already actively building
    // and the costs have been deducted. Possibly add deducted amount to record?

    if (!empty($request->input('redirect')) && $request->input('redirect') == 'overview') {
      return redirect()->route('overview.index');
    }
    else {
      return redirect()->route($this->route_view_index);
    }
  }
}
