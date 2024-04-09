<?php

namespace OGame\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Services\BuildingQueueService;
use OGame\Services\HighscoreService;
use OGame\Services\PlayerService;
use OGame\Services\ResearchQueueService;
use OGame\Services\UnitQueueService;

class OverviewController extends OGameController
{
    /**
     * Shows the overview index page
     *
     * @param PlayerService $player
     * @param BuildingQueueService $building_queue
     * @param ResearchQueueService $research_queue
     * @param UnitQueueService $ship_queue
     * @return View
     * @throws BindingResolutionException
     */
    public function index(PlayerService $player, BuildingQueueService $building_queue, ResearchQueueService $research_queue, UnitQueueService $ship_queue) : View
    {
        $this->setBodyId('overview');

        $planet = $player->planets->current();

        // Parse building queue for this planet
        $build_full_queue = $building_queue->retrieveQueue($planet);
        $build_active = $building_queue->enrich($building_queue->retrieveCurrentlyBuildingFromQueue($build_full_queue));
        $build_queue_enriched = $building_queue->enrich($building_queue->retrieveQueuedFromQueue($build_full_queue));

        // Parse research queue for this planet
        $research_full_queue = $research_queue->retrieveQueue($planet);
        $research_active = $research_queue->enrich($research_queue->retrieveCurrentlyBuildingFromQueue($research_full_queue));
        $research_queue_enriched = $research_queue->enrich($research_queue->retrieveQueuedFromQueue($research_full_queue));

        // Parse ship queue for this planet.
        $ship_full_queue = $ship_queue->retrieveQueue($planet);
        $ship_queue_enriched = $ship_queue->enrich($ship_full_queue);

        // Extract active from queue.
        $ship_active = [];
        if (!empty($ship_queue_enriched[0])) {
            $ship_active = $ship_queue_enriched[0];

            // Remove active from queue.
            unset($ship_queue_enriched[0]);
        }

        // Get total time of all items in queue
        $ship_queue_time_end = $ship_queue->retrieveQueueTimeEnd($planet);
        $ship_queue_time_countdown = 0;
        if ($ship_queue_time_end > 0) {
            $ship_queue_time_countdown = $ship_queue_time_end - Carbon::now()->timestamp;
        }

        $highscoreService = app()->make(HighscoreService::class);

        return view('ingame.overview.index')->with([
            'header_filename' => $player->planets->current()->getPlanetType(),
            'planet_name' => $player->planets->current()->getPlanetName(),
            'planet_diameter' => $player->planets->current()->getPlanetDiameter(),
            'planet_temp_min' => $player->planets->current()->getPlanetTempMin(),
            'planet_temp_max' => $player->planets->current()->getPlanetTempMax(),
            'planet_coordinates' => $player->planets->current()->getPlanetCoordinatesAsString(),
            'user_points' => $highscoreService->getPlayerScore($player, true), // @TODO
            'user_rank' => 0, // @TODO
            'max_rank' => 0, // @TODO
            'user_honor_points' => 0, // @TODO
            'build_active' => $build_active,
            'build_queue' => $build_queue_enriched,
            'research_active' => $research_active,
            'research_queue' => $research_queue_enriched,
            'ship_active' => $ship_active,
            'ship_queue' => $ship_queue_enriched,
            'ship_queue_time_countdown' => $ship_queue_time_countdown,
        ]);
    }
}
