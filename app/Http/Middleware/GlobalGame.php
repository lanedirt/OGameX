<?php

namespace OGame\Http\Middleware;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class GlobalGame
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Get objects.
            $object = new ObjectService();
            app()->instance(ObjectService::class, $object);

            // Instantiate settings service.
            $settings = app()->make(\OGame\Services\SettingsService::class);
            app()->instance(\OGame\Services\SettingsService::class, $settings);

            // Load player.
            $player = app()->make(PlayerService::class, ['player_id' => $request->user()->id]);
            app()->instance(PlayerService::class, $player);

            // Check if current planet change querystring parameter exists, if so, change current planet.
            if (!empty($request->query('cp'))) {
                $player->setCurrentPlanetId($request->query('cp'));
            }

            // Update player.
            $player->update();

            // Update all planets.
            $player->planets->update();
        }

        return $next($request);
    }
}
