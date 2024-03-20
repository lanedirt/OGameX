<?php

namespace OGame\Http\Controllers\Abstracts;

use Illuminate\Http\Request;
use OGame\Http\Controllers\Controller;
use OGame\Http\Traits\IngameTrait;
use OGame\Http\Traits\ObjectAjaxTrait;
use OGame\Services\BuildingQueueService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

abstract class AbstractBuildingsController extends Controller
{
  use IngameTrait;
  use ObjectAjaxTrait;

  protected $planet;

  /**
   * @var Index view route (used for redirecting).
   */
  protected $route_view_index;

  /**
   * QueueService
   *
   * @var \OGame\Services\BuildingQueueService
   */
  protected $queue;

  /**
   * AbstractBuildingsController constructor.
   */
  public function __construct(BuildingQueueService $queue)
  {
    $this->queue = $queue;
  }

  /**
   * Shows the building index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request, PlayerService $player, ObjectService $objects)
  {
    $this->planet = $player->planets->current();

    $buildings_array = $objects->getBuildings();

    $count = 0;
    $header_filename_parts = [];

    // Parse build queue for this planet
    $build_full_queue = $this->queue->retrieveQueue($this->planet);
    $build_active = $this->queue->enrich($this->queue->retrieveCurrentlyBuildingFromQueue($build_full_queue));
    $build_queue = $this->queue->enrich($this->queue->retrieveQueuedFromQueue($build_full_queue));

    $buildings = [];
    foreach ($this->objects as $key_row => $objects_row) {
      $buildings[$key_row] = [];

      foreach ($objects_row as $building_id) {
        $count++;

        // Get current level of building
        $current_level = $this->planet->getBuildingLevel($building_id);

        // Check requirements of this building
        $requirements_met = $objects->objectRequirementsMet($building_id, $this->planet, $player);

        // Check if the current planet has enough resources to build this building.
        $enough_resources = $this->planet->hasResources($objects->getObjectPrice($building_id, $this->planet));

        // If building level is 1 or higher, add to header filename parts to
        // render the header of this planet.
        if (in_array($building_id, $this->header_filename_objects) && $current_level >= 1) {
          $header_filename_parts[$building_id] = $building_id;
        }

        $buildings[$key_row][$building_id] = array_merge($buildings_array[$building_id], [
          'current_level' => $current_level,
          'requirements_met' => $requirements_met,
          'count' => $count,
          'enough_resources' => $enough_resources,
          'currently_building' => (!empty($build_active['id']) && $build_active['object']['id'] == $building_id),
        ]);
      }
    }

    // Parse header filename for this planet
    ksort($header_filename_parts);
    $header_filename = $this->planet->getPlanetType();
    foreach ($header_filename_parts as $building_id) {
      $header_filename .= '_' . $building_id;
    }

    // Max amount of buildings that can be in the queue in a given time.
    $max_build_queue_count = 4; //@TODO: refactor into global / constant?
    $build_queue_max = false;
    if (count($build_queue) >= $max_build_queue_count) {
      $build_queue_max = true;
    }

    return view($this->view_name)->with([
      'planet_name' => $this->planet->getPlanetName(),
      'header_filename' => $header_filename,
      'buildings' => $buildings,
      'build_active' => $build_active,
      'build_queue' => $build_queue,
      'build_queue_max' => $build_queue_max,
      'body_id' => $this->body_id, // Sets <body> tag ID property.
    ]);
  }

  /**
   * Handles an incoming add buildrequest.
   */
  public function addBuildRequest(Request $request, PlayerService $player) {
    $building_id = $request->input('type');
    $planet_id = $request->input('planet_id');

    $planet = $player->planets->childPlanetById($planet_id);
    $this->queue->add($planet, $building_id);

    return redirect()->route($this->route_view_index);
  }

  /**
   * Handles an incoming cancel buildrequest.
   */
  public function cancelBuildRequest(Request $request, PlayerService $player) {
    $building_id = $request->input('building_id');
    $building_queue_id = $request->input('building_queue_id');

    $this->queue->cancel($player->planets->current(), $building_queue_id, $building_id);

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
