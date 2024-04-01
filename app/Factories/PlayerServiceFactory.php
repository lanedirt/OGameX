<?php

namespace OGame\Factories;

use OGame\Services\PlayerService;

class PlayerServiceFactory
{
    protected $instances = [];

    /**
     * Returns a playerService either from local instances cache or creates a new one.
     *
     * @param $playerId
     * @return PlayerService
     */
    public function make($playerId): PlayerService
    {
        if (!isset($this->instances[$playerId])) {
            $playerService = app()->make(PlayerService::class, ['player_id' => $playerId]);
            $this->instances[$playerId] = $playerService;
        }

        return $this->instances[$playerId];
    }
}