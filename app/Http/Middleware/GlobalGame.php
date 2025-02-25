<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OGame\Services\PlayerService;
use Throwable;

class GlobalGame
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Throwable
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check()) {
            // Load current player and make it available as a request singleton via PlayerService.
            $player = resolve(PlayerService::class, ['player_id' => $request->user()->id]);

            /** @var PlayerService $player */
            app()->instance(PlayerService::class, $player);

            // Check if current planet change querystring parameter exists, if so, change current planet.
            if (!empty($request->query('cp'))) {
                $player->setCurrentPlanetId((int)$request->query('cp'));
            }

            // Update player.
            $player->update();

            // Update current planet of player.
            // TODO: due to how planet update locking works, in the "load player" call above
            // the player object and all of its planets are loaded for the first time. Then here
            // in the update call we retrieve the current planet again to ensure we have the latest data.
            // This update mechanism could be improved by calling it directly in the place when the player and
            // planet objects are loaded for the first time. This would save one select call to the database.
            // So it's not a big deal, but it's a small performance improvement that could be done.
            $player->planets->current()->update();

            // Update all fleet missions of player that are associated with any of the player's planets.
            $player->updateFleetMissions();
        }

        return $next($request);
    }
}
