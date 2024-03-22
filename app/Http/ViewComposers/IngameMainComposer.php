<?php

namespace OGame\Http\ViewComposers;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use OGame\Services\PlayerService;

/**
 * Class IngameMainComposer
 * @package OGame\Http\Composers
 *
 * Contains all preprocessor logic for parsing the ingame.layouts.main
 * blade theme file.
 */
class IngameMainComposer {

  public $request;
  public $player;

  /**
   * IngameMainComposer constructor.
   *
   * Construct view composer and get all required data via dependency
   * injection.
   *
   * @param \Illuminate\Http\Request $request
   * @param \OGame\Services\PlayerService $player
   */
  public function __construct(Request $request, PlayerService $player) {
    $this->request = $request;
    $this->player = $player;
  }

  /**
   * Compose the view and pass any required variables.
   *
   * @param \Illuminate\Contracts\View\View $view
   */
  public function compose(View $view)
  {
    $current_planet = $this->player->planets->current();
    $resources = [
      'metal' => [
        'amount' => $current_planet->getMetal(),
        'amount_formatted' => $current_planet->getMetal(true),
        'production_hour' => $current_planet->getMetalProductionPerHour(),
        'production_hour_formatted' => $current_planet->getMetalProductionPerHour(true),
        'production_second' => $current_planet->getMetalProductionPerSecond(),
        'storage' => $current_planet->getMetalStorage(),
        'storage_formatted' => $current_planet->getMetalStorage(true),
        'storage_almost_full' => ($current_planet->getMetal() >= ($current_planet->getMetalStorage() * 0.9) && $current_planet->getMetal() < $current_planet->getMetalStorage()) ? true : false,
      ],
      'crystal' => [
        'amount' => $current_planet->getCrystal(),
        'amount_formatted' => $current_planet->getCrystal(true),
        'production_hour' => $current_planet->getCrystalProductionPerHour(),
        'production_hour_formatted' => $current_planet->getCrystalProductionPerHour(true),
        'production_second' => $current_planet->getCrystalProductionPerSecond(),
        'storage' => $current_planet->getCrystalStorage(),
        'storage_formatted' => $current_planet->getCrystalStorage(true),
        'storage_almost_full' => ($current_planet->getCrystal() >= ($current_planet->getCrystalStorage() * 0.9) && $current_planet->getCrystal() < $current_planet->getCrystalStorage()) ? true : false,
      ],
      'deuterium' => [
        'amount' => $current_planet->getDeuterium(),
        'amount_formatted' => $current_planet->getDeuterium(true),
        'production_hour' => $current_planet->getDeuteriumProductionPerHour(),
        'production_hour_formatted' => $current_planet->getDeuteriumProductionPerHour(true),
        'production_second' => $current_planet->getDeuteriumProductionPerSecond(),
        'storage' => $current_planet->getDeuteriumStorage(),
        'storage_formatted' => $current_planet->getDeuteriumStorage(true),
        'storage_almost_full' => ($current_planet->getDeuterium() >= ($current_planet->getDeuteriumStorage() * 0.9) && $current_planet->getDeuterium() < $current_planet->getDeuteriumStorage()) ? true : false,
      ],
      'energy' => [
        'amount' => $current_planet->getEnergy(),
        'amount_formatted' => $current_planet->getEnergy(true),
        'production' => $current_planet->getEnergyProduction(),
        'production_formatted' => $current_planet->getEnergyProduction(true),
        'consumption' => $current_planet->getEnergyConsumption(),
        'consumption_formatted' => $current_planet->getEnergyConsumption(true),
      ],
    ];

    $view->with([
      'username' => $this->player->getUsername(),
      'resources' => $resources,
      'currentPlanetId' => $this->player->planets->current()->getPlanetId(),
      'planets' => $this->player->planets,
    ]);
  }

}