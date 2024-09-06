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
     * @param bool $reloadCache Whether to force retrieve the object and reload the cache. Defaults to false.
     * * Note: for certain usecases such as updating planets/missions after gaining a lock, its essential to set this to
     * * true in order to get the latest data from the database and update the cache accordingly to avoid stale data.
 *
     * @return PlayerService
     */
    public function make(int $playerId, bool $reloadCache = false): PlayerService
    {
        if ($reloadCache || !isset($this->instances[$playerId])) {
            try {
                $playerService = resolve(PlayerService::class, ['player_id' => $playerId]);
                $this->instances[$playerId] = $playerService;
            } catch (BindingResolutionException $e) {
                throw new RuntimeException('Class not found: ' . PlayerService::class);
            }
        }

        return $this->instances[$playerId];
    }
}
