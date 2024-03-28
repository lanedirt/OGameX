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

    public function getPlayerScore($playerId, $formatted = false) {
        // Load player object with all planets
        $player = app()->make(PlayerService::class, ['player_id' => $playerId]);

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

    public function getHighscorePlayers()
    {
        // Get all players
        $players = User::all();
        $highscore = [];
        $count = 0;
        foreach ($players as $player) {
            $count++;

            // TODO: we get the player score per player now, but we should get it from a cached highscore table
            // to improve performance. Currently it works but is slow for large amounts of players.
            $score = $this->getPlayerScore($player->id);
            $score_formatted = number_format($score, 0, ',', '.');

            $highscore[] = [
                'id' => $player->id,
                'name' => $player->username,
                'points' => $score,
                'points_formatted' => $score_formatted,
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

        return $highscore;
    }

}
