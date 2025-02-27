<?php

namespace OGame\Http\Controllers;

use Cache;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Facades\AppUtil;
use OGame\Models\Highscore;
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
     * @param UnitQueueService $unit_queue
     * @return View
     * @throws Exception
     */
    public function index(PlayerService $player, BuildingQueueService $building_queue, ResearchQueueService $research_queue, UnitQueueService $unit_queue): View
    {
        $this->setBodyId('overview');

        $planet = $player->planets->current();

        // Parse building queue for this planet
        $build_full_queue = $building_queue->retrieveQueue($planet);
        $build_active = $build_full_queue->getCurrentlyBuildingFromQueue();
        $build_queue = $build_full_queue->getQueuedFromQueue();

        // Parse research queue for this planet
        $research_full_queue = $research_queue->retrieveQueue($planet);
        $research_active = $research_full_queue->getCurrentlyBuildingFromQueue();
        $research_queue = $research_full_queue->getQueuedFromQueue();

        // Parse ship queue for this planet.
        $ship_full_queue = $unit_queue->retrieveQueue($planet);
        $ship_active = $ship_full_queue->getCurrentlyBuildingFromQueue();
        $ship_queue = $ship_full_queue->getQueuedFromQueue();

        // Get total time of all items in queue
        $ship_queue_time_end = $unit_queue->retrieveQueueTimeEnd($planet);
        $ship_queue_time_countdown = 0;
        if ($ship_queue_time_end > 0) {
            $ship_queue_time_countdown = $ship_queue_time_end - (int)Carbon::now()->timestamp;
        }

        $planet = $player->planets->current();

        // Check if this planet has a moon or a planet on the same coordinates.
        // The other_planet is used for rendering the switch link to the other planet.
        $has_moon = $planet->hasMoon();
        $has_planet = $planet->hasPlanet();
        $other_planet = null;
        if ($has_moon) {
            $other_planet = $planet->moon();
        } elseif ($has_planet) {
            $other_planet = $planet->planet();
        }

        $highscoreService = resolve(HighscoreService::class);

        $user_rank = Cache::remember(sprintf('player-rank-%d', $player->getId()), now()->addMinutes(5), function () use ($highscoreService, $player) {
            return $highscoreService->getHighscorePlayerRank($player);
        });

        $max_ranks = Cache::remember('highscore-player-count', now()->addMinutes(5), function () {
            return Highscore::query()->validRanks()->count();
        });

        $user_score =  Cache::remember(sprintf('player-score-%d', $player->getId()), now()->addMinutes(5), function () use ($player) {
            return AppUtil::formatNumber(Highscore::where('player_id', $player->getId())->first()->general ?? 0);
        });

        return view('ingame.overview.index')->with([
            'header_filename' => $planet->isMoon() ? 'moon/' . $planet->getPlanetImageType() : $planet->getPlanetBiomeType(),
            'planet_name' => $planet->getPlanetName(),
            'planet_diameter' => $planet->getPlanetDiameter(),
            'planet_temp_min' => $planet->getPlanetTempMin(),
            'planet_temp_max' => $planet->getPlanetTempMax(),
            'planet_coordinates' => $planet->getPlanetCoordinates()->asString(),
            'user_points' => $user_score,
            'user_rank' => $user_rank,
            'max_rank' => $max_ranks,
            'user_honor_points' => 0, // @TODO
            'build_active' => $build_active,
            'building_count' => $player->planets->current()->getBuildingCount(),
            'max_building_count' => $player->planets->current()->getPlanetFieldMax(),
            'build_queue' => $build_queue,
            'research_active' => $research_active,
            'research_queue' => $research_queue,
            'ship_active' => $ship_active,
            'ship_queue' => $ship_queue,
            'ship_queue_time_countdown' => $ship_queue_time_countdown,
            'has_moon' => $has_moon,
            'has_planet' => $has_planet,
            'other_planet' => $other_planet,
        ]);
    }
}
