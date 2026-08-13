<?php

namespace OGame\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired after a research level completes for a player.
 */
class ResearchCompleted implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(
        public int $playerId,
        public string $machineName,
        public int $level,
    ) {
    }
}
