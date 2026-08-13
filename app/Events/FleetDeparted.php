<?php

namespace OGame\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OGame\Models\FleetMission;

/**
 * Fired after a fleet mission departs.
 */
class FleetDeparted implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public FleetMission $mission,
    ) {}
}
