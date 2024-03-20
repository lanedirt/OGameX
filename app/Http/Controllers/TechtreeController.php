<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class TechtreeController extends Controller
{
  use IngameTrait;

  /**
   * Returns techtree ajax content.
   *
   * @param  int  $id
   * @return Response
   */
  public function ajax(Request $request, ObjectService $objects, PlayerService $player)
  {
    $object_id = $request->input('object_id');
    $tab = $request->input('tab');

    // @TODO: with a new account it shows 0 production for everything.
    // Fix this bug.
    $planet = $player->planets->current();

    // Load object
    $object = $objects->getBuildings($object_id);

    if ($tab ==1 ) {
      return view('ingame.techtree.techtree')->with([
        'object' => $object,
        'object_id' => $object_id,
        'planet' => $planet,
        'current_level' => $player->planets->current()->getBuildingLevel($object_id),
      ]);
    }
    elseif ($tab == 2) {
      $current_level = $player->planets->current()->getBuildingLevel($object_id);

      // Tech info (resource production tables etc)
      $production_table = [];
      if (!empty($object['production'])) {
        $production_amount_current_level = 0;
        foreach ($planet->getBuildingProduction($object_id, $current_level) as $type => $amount) {
          if ($amount > 0) {
            $production_amount_current_level = $amount;
          }
        }

        // Create production table array value
        $min_level = (($current_level - 2) > 1) ? $current_level - 2 : 1;
        for($i = $min_level; $i < $min_level + 15; $i++) {
          $production_amount_previous_level = 0;
          foreach ($planet->getBuildingProduction($object_id, $i-1) as $type => $amount) {
            if ($amount > 0) {
              $production_amount_previous_level = $amount;
            }
          }

          $production_array = $planet->getBuildingProduction($object_id, $i);
          $production_amount = 0;
          foreach ($production_array as $type => $amount) {
            if ($amount > 0) {
              $production_amount = $amount;
            }
          }

          $production_table[] = [
            'level' => $i,
            'production' => $production_amount,
            'production_difference' => $production_amount - $production_amount_current_level,
            'production_difference_per_level' => ($i == $current_level) ? 0 : (($i-1 < $current_level) ? ($production_amount - $production_amount_previous_level) * -1 : $production_amount - $production_amount_previous_level),
            'energy_balance' => $planet->getBuildingProduction($object_id, $i)['energy'],
            'energy_difference' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object_id, $i)['energy'] - $planet->getBuildingProduction($object_id, $current_level)['energy']),
            'deuterium_consumption' => $planet->getBuildingProduction($object_id, $i)['deuterium'],
            'deuterium_consumption_per_level' => ($i == $current_level) ? 0 : ($planet->getBuildingProduction($object_id, $i)['deuterium'] - $planet->getBuildingProduction($object_id, $current_level)['deuterium']),
            'protected' => 0,
          ];
        }
      }

      return view('ingame.techtree.techinfo')->with([
        'object' => $object,
        'object_id' => $object_id,
        'planet' => $planet,
        'current_level' => $player->planets->current()->getBuildingLevel($object_id),
        'production_table' => $production_table,
      ]);
    }
    elseif ($tab == 3) {
      return view('ingame.techtree.technology')->with([
        'object' => $object,
        'object_id' => $object_id,
        'planet' => $planet,
        'current_level' => $player->planets->current()->getBuildingLevel($object_id),
      ]);
    }
    elseif ($tab == 4) {
      return view('ingame.techtree.applications')->with([
        'object' => $object,
        'object_id' => $object_id,
        'planet' => $planet,
        'current_level' => $player->planets->current()->getBuildingLevel($object_id),
      ]);
    }

    return false;
  }
}
