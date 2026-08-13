<?php

namespace OGame\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired after a player successfully colonizes a new planet.
 */
class PlanetColonized implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public int $planetId,
        public int $playerId,
    ) {
    }
}
