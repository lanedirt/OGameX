<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\BuildingQueueService;
use OGame\Services\ResearchQueueService;
use OGame\Services\UnitQueueService;

class OverviewController extends Controller
{
  use IngameTrait;

  protected $player;
  protected $queue;
  protected $planet;

  /**
   * Shows the overview index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request, PlayerService $player, BuildingQueueService $building_queue, ResearchQueueService $research_queue, UnitQueueService $ship_queue)
  {
    $this->player = $player;
    $this->building_queue = $building_queue;
    $this->research_queue = $research_queue;
    $this->ship_queue = $ship_queue;
    $this->planet = $this->player->planets->current();

    // Parse building queue for this planet
    $build_full_queue = $this->building_queue->retrieveQueue($this->planet);
    $build_active = $this->building_queue->enrich($this->building_queue->retrieveCurrentlyBuildingFromQueue($build_full_queue));
    $build_queue = $this->building_queue->enrich($this->building_queue->retrieveQueuedFromQueue($build_full_queue));

    // Parse research queue for this planet
    $research_full_queue = $this->research_queue->retrieveQueue($this->planet);
    $research_active = $this->research_queue->enrich($this->research_queue->retrieveCurrentlyBuildingFromQueue($research_full_queue));
    $research_queue = $this->research_queue->enrich($this->research_queue->retrieveQueuedFromQueue($research_full_queue));

    // Parse ship queue for this planet.
    // @TODO: change this $queue object to be dependency injected instead.
    $ship_queue = $this->ship_queue->retrieveQueue($this->planet);
    $ship_queue = $this->ship_queue->enrich($ship_queue);

    // Extract active from queue.
    $ship_active = [];
    if (!empty($ship_queue[0])) {
      $ship_active = $ship_queue[0];

      // Remove active from queue.
      unset($ship_queue[0]);
    }

    // Get total time of all items in queue
    $ship_queue_time_end = $this->ship_queue->retrieveQueueTimeEnd($this->planet);
    $ship_queue_time_countdown = 0;
    if ($ship_queue_time_end > 0) {
      $ship_queue_time_countdown = $ship_queue_time_end - time();
    }

    return view('ingame.overview.index')->with([
      'header_filename' => $this->player->planets->current()->getPlanetType(),
      'planet_name' => $this->player->planets->current()->getPlanetName(),
      'planet_diameter' => $this->player->planets->current()->getPlanetDiameter(),
      'planet_temp_min' => $this->player->planets->current()->getPlanetTempMin(),
      'planet_temp_max' => $this->player->planets->current()->getPlanetTempMax(),
      'planet_coordinates' => $this->player->planets->current()->getPlanetCoordinatesAsString(),
      'user_points' => 0, // @TODO
      'user_rank' => 0, // @TODO
      'max_rank' => 0, // @TODO
      'user_honor_points' => 0, // @TODO
      'build_active' => $build_active,
      'build_queue' => $build_queue,
      'research_active' => $research_active,
      'research_queue' => $research_queue,
      'ship_active' => $ship_active,
      'ship_queue' => $ship_queue,
      'ship_queue_time_countdown' => $ship_queue_time_countdown,
      'body_id' => 'overview', // Sets <body> tag ID property.
    ]);
  }
}
