<?php

namespace OGame\Services;

use Exception;
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
     * Highscore constructor.
     */
    public function __construct()
    {
    }

    public function getPlayerScore($player, $formatted = false) {
        // Calculate player score dynamically based on player levels and possessions.
        $score = 0;
        $score += $this->getPlayerPointsGeneral($player);
        $score += $this->getPlayerPointsResearch($player);
        $score += $this->getPlayerPointsMilitary($player);
        $score += $this->getPlayerPointsEconomy($player);

        // TODO: add score for fleets that are not on a planet (flying missions).

        if ($formatted) {
            $score = number_format($score, 0, ',', '.');
        }

        return $score;
    }

    public function getPlayerPointsGeneral(PlayerService $player) {
        $score = 0;
        foreach ($player->planets->all() as $planet) {
            $score += $planet->getGeneralScore();
        }

        //var_dump('playerpointsgeneral:');
        //var_dump($score);
        //die();

        return $score;
    }

    public function getPlayerPointsResearch(PlayerService $player) {

    }

    public function getPlayerPointsMilitary(PlayerService $player) {

    }

    public function getPlayerPointsEconomy(PlayerService $player) {

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
            // Load player object with all planets
            $playerService = app()->make(PlayerService::class, ['player_id' => $player->id]);
            $score = $this->getPlayerScore($playerService);

            // Get player main planet coords
            $mainPlanet = $playerService->planets->first();

            $score_formatted = number_format($score, 0, ',', '.');

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
