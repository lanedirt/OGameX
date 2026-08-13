<?php

namespace Modules\HelloWorld\Listeners;

use Illuminate\Support\Facades\Log;
use OGame\Events\PlanetColonized;

class LogPlanetColonized
{
    public function handle(PlanetColonized $event): void
    {
        Log::info('HelloWorld observed a planet colonization.', [
            'planet_id' => $event->planetId,
            'player_id' => $event->playerId,
        ]);
    }
}
