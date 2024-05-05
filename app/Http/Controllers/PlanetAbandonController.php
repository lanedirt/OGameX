<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use OGame\Services\PlayerService;

class PlanetAbandonController extends OGameController
{
    /**
     * Returns the planet abandon/rename overlay popup.
     *
     * @param PlayerService $player
     * @return View
     */
    public function overlay(PlayerService $player): View
    {
        return view('ingame.planetabandon.overlay')->with([
            'currentPlanet' => $player->planets->current(),
        ]);
    }

    /**
     * Rename the current planet.
     *
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function rename(PlayerService $player): JsonResponse
    {
        // Get form data
        $planetName = request('newPlanetName');

        // Validate planet name
        if ($player->planets->current()->isValidPlanetName($planetName) === false) {
            return response()->json([
                'status' => 'error',
                'errorbox' => [
                    'type' => 'fadeBox',
                    'text' => __('The new planet name is invalid. Please try again.'),
                    'failed' => true,
                ],
            ]);
        }

        // Update planet name
        $player->planets->current()->setPlanetName($planetName);

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'errorbox' => [
                'type' => 'fadeBox',
                'text' => __('Planet renamed successfully.'),
                'failed' => false,
                ],
        ]);
    }
}
