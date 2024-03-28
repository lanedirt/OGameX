<?php

namespace OGame\Services;

use Exception;
use OGame\Facades\AppUtil;
use OGame\Planet as Planet;
use OGame\User;

/**
 * Class Highscore.
 *
 * Service object for calculating and retrieving highscores.
 *
 * @package OGame\Services
 */
class HighscoreService
{
    /**
     * Highscore type to calculate.
     * @var numeric
     */
    protected $highscoreType;

    /**
     * Highscore constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set the highscore type to calculate.
     *
     * @param $type
     * @return void
     */
    public function setHighscoreType($type)
    {
        // 0 = general score
        // 1 = economy points
        // 2 = research points
        // 3 = military points
        $this->highscoreType = $type;
    }

    public function getPlayerScore($player, $formatted = false) {
        $score = 0;
        // Get score for buildings and units on player owned planets
        foreach ($player->planets->all() as $planet) {
            $score += $planet->getPlanetScore();
        }

        // Get score for research levels of player
        $score += $player->getResearchScore();

        // TODO: add score for fleets that are not on a planet (flying missions).

        if ($formatted) {
            $score = AppUtil::formatNumber($score);
        }

        return $score;
    }

    public function getPlayerScoreResearch(PlayerService $player) {
        return $player->getResearchScore();
    }

    public function getPlayerScoreMilitary(PlayerService $player) {
        $points = 0;

        // Get points (sum of all unit amounts) for units on player owned planets.
        foreach ($player->planets->all() as $planet) {
            $points += $planet->getPlanetMilitaryScore();
        }

        return $points;
    }

    public function getPlayerScoreEconomy(PlayerService $player) {
        $points = 0;

        // Get score for buildings and units on player owned planets (economy specific calculation).
        foreach ($player->planets->all() as $planet) {
            $points += $planet->getPlanetScoreEconomy();
        }

        return $points;
    }

    public function getHighscorePlayers($offset_start = 0, $return_amount = 100)
    {
        // Get all players
        $players = User::all();
        $highscore = [];
        $count = 0;
        foreach ($players as $player) {
            $count++;

            // TODO: we get the player score per player now, but we should get it from a cached highscore table
            // to improve performance. Currently it works but is slow for large amounts of players.
            // Load player object with all planets.
            $playerService = app()->make(PlayerService::class, ['player_id' => $player->id]);
            $score = 0;
            switch ($this->highscoreType) {
                case 1:
                    $score = $this->getPlayerScoreEconomy($playerService);
                    break;
                case 2:
                    $score = $this->getPlayerScoreResearch($playerService);
                    break;
                case 3:
                    $score = $this->getPlayerScoreMilitary($playerService);
                    break;
                default:
                    $score = $this->getPlayerScore($playerService);
                    break;
            }

            // Get player main planet coords
            $mainPlanet = $playerService->planets->first();

            $score_formatted = AppUtil::formatNumber($score);

            $highscore[] = [
                'id' => $player->id,
                'name' => $player->username,
                'points' => $score,
                'points_formatted' => $score_formatted,
                'planet_coords' => $mainPlanet->getPlanetCoordinates(),
                'rank' => $count,
            ];
        }

        // Order the array by points descending and reset the rank by counting up again.
        usort($highscore, function ($a, $b) {
            return $b['points'] <=> $a['points'];
        });
        $count = 0;
        foreach($highscore as $key => &$value) {
            $count++;
            $value['rank'] = $count;
        }

        // Only return the requested 100 players based on starting rank.
        if ($return_amount > 0) {
            $highscore = array_slice($highscore, $offset_start, 100);
        }

        return $highscore;
    }

    /**
     * Return rank of player.
     *
     * @param $player
     * @return int
     */
    public function getHighscorePlayerRank($player)
    {
        // TODO: this is a slow method, we should cache the highscore list and get the rank from there.
        // Get all players
        $highscorePlayers = $this->getHighscorePlayers(0, 0);

        // Find the player in the highscore list to determine its rank.
        $rank = 0;
        foreach ($highscorePlayers as $highscorePlayer) {
            $rank++;
            if ($highscorePlayer['id'] == $player->getId()) {
                return $rank;
            }
        }

        return 0;
    }

    /**
     * Returns the amount of players in the game to determine paging for highscore page.
     *
     * @return numeric
     */
    public function getHighscorePlayerAmount()
    {
        return User::count();
    }
}
