<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Services\PlayerService;

class PlayerServiceFactory
{
    /**
     * Cached instances of playerService.
     *
     * @var array<PlayerService>
     */
    protected array $instances = [];

    /**
     * Returns a playerService either from local instances cache or creates a new one.
     *
     * @param int $playerId
     * @return PlayerService
     */
    public function make(int $playerId): PlayerService
    {
        if (!isset($this->instances[$playerId])) {
            try {
                $playerService = app()->make(PlayerService::class, ['player_id' => $playerId]);
                $this->instances[$playerId] = $playerService;
            } catch (BindingResolutionException $e) {
                throw new \RuntimeException('Class not found: ' . PlayerService::class);
            }
        }

        return $this->instances[$playerId];
    }
}
