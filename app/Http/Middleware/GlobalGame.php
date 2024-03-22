<?php

namespace OGame\Http\Middleware;

use OGame\Services\ObjectService;
use OGame\Services\PlayerService;
use OGame\Services\PlanetListService;
use Illuminate\Support\Facades\Auth;
use Closure;

class GlobalGame
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Get objects
            $object = new ObjectService();
            app()->instance(ObjectService::class, $object);

            // Load player
            $player = new PlayerService($object);
            $player->load($request->user()->id);
            app()->instance(PlayerService::class, $player);

            // Check if current planet change querystring parameter exists, if so, change current planet.
            if (!empty($request->query('cp'))) {
             $player->setCurrentPlanetId($request->query('cp'));
            }

            // Update player
            $player->update();

            // Update all planets
            $player->planets->update();
        }

        return $next($request);
    }
}
