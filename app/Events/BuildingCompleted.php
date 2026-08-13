<?php

namespace OGame\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired after a building or station level change completes on a planet.
 */
class BuildingCompleted implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public int $planetId,
        public int $playerId,
        public string $machineName,
        public int $level,
        public bool $isDowngrade = false,
    ) {}
}
