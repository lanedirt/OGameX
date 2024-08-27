<?php

namespace OGame\Factories;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Services\PlayerService;
use RuntimeException;

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
     * @param bool $useCache Whether to use the cache or not. Defaults to true. Note: for certain usecases
     *   such as updating planets/missions after gaining a lock, its essential to set this to false in order
     *   to get the latest data from the database.
     *
     * @return PlayerService
     */
    public function make(int $playerId, bool $useCache = true): PlayerService
    {
        if (!$useCache || !isset($this->instances[$playerId])) {
            try {
                $playerService = app()->make(PlayerService::class, ['player_id' => $playerId]);
                $this->instances[$playerId] = $playerService;
            } catch (BindingResolutionException $e) {
                throw new RuntimeException('Class not found: ' . PlayerService::class);
            }
        }

        return $this->instances[$playerId];
    }
}
