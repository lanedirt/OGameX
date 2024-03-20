<?php

namespace OGame\Http\Traits;

use Illuminate\Http\Request;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

trait ObjectAjaxTrait
{
  /**
   * Handles the resources page AJAX requests.
   *
   * @param  int  $id
   * @return Response
   */
  public function ajaxHandler(Request $request, PlayerService $player, ObjectService $objects)
  {
    $this->planet = $player->planets->current();

    $building_id = $request->input('type');
    if (empty($building_id)) {
      throw new \Exception('No building ID provided.');
    }

    $building = $objects->getBuildings($building_id);
    if (empty($building)) {
      throw new \Exception('Incorrect building ID provided.');
    }

    $current_level = 0;
    if ($building['type'] == 'building') {
      $current_level = $this->planet->getBuildingLevel($building['id']);
    }
    elseif ($building['type'] == 'research') {
      $current_level = $player->getResearchLevel($building['id']);
    }
    $next_level = $current_level + 1;

    // Check requirements of this building
    $requirements_met = $objects->objectRequirementsMet($building_id, $this->planet, $player);

    $price = $objects->getObjectPrice($building['id'], $this->planet);
    $price_formatted = $objects->getObjectPrice($building['id'], $this->planet, true);

    // Get max build amount of this object (unit).
    $max_build_amount = $objects->getObjectMaxBuildAmount($building['id'], $this->planet);

    $production_time = $this->planet->getBuildingTime($building['id'], $player, true);

    $production_current = $this->planet->getBuildingProduction($building['id']);
    $production_next = $this->planet->getBuildingProduction($building['id'], $next_level);
    if (isset($production_current['energy'])) {
      $energy_difference = ($production_next['energy'] - $production_current['energy']) * -1;
    }
    else {
      $energy_difference = 0;
    }

    $enough_resources = $this->planet->hasResources($price);

    // Storage capacity bar
    $storage = !empty($building['storage']);
    $current_storage = 0;
    $max_storage = 0;
    if ($storage) {
      switch ($building['id']) {
        case 22:
          $max_storage = $this->planet->getMetalStorage();
          $current_storage = $this->planet->getMetal();
          break;

        case 23:
          $max_storage = $this->planet->getCrystalStorage();
          $current_storage = $this->planet->getCrystal();
          break;

        case 24:
          $max_storage = $this->planet->getDeuteriumStorage();
          $current_storage = $this->planet->getDeuterium();
          break;
      }
    }

    $build_queue = $this->queue->retrieveQueue($this->planet);
    $build_queue = $this->queue->enrich($build_queue);

    $build_active_current = false;
    if (!empty($build_queue)) {
      foreach ($build_queue as $record) {
        if ($building['id'] == $record['object']['id']) {
          $build_active_current = $record;
        }
      }
    }

    // @TODO: restore
    // Max amount of buildings that can be in the queue in a given time.
    $max_build_queue_count = 4; //@TODO: refactor into global / constant?
    $build_queue_max = false;
    if (count($build_queue) >= $max_build_queue_count) {
      $build_queue_max = true;
    }

    return view('ingame.ajax.object')->with([
      'id' => $building_id,
      'building_type' => $this->building_type,
      'planet_id' => $this->planet->getPlanetId(),
      'current_level' => $current_level,
      'next_level' => $next_level,
      'description' => $building['description'],
      'title' => $building['title'],
      'price' => $price,
      'price_formatted' => $price_formatted,
      'planet' => $this->planet,
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
    ]);
  }
}
