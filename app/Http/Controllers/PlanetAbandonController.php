<?php

namespace OGame\Http\Controllers;

use Exception;
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
            'isCurrentPlanetHomePlanet' => $player->planets->current()->getPlanetId() === $player->planets->first()->getPlanetId(),
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

    /**
     * Shows confirm popup for abandoning the current planet.
     *
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function abandonConfirm(PlayerService $player): JsonResponse
    {
        // Get form data
        $password = request('password');

        // Validate password
        if (!$player->isPasswordValid($password)) {
            return response()->json([
                'status' => 'error',
                'errorbox' => [
                    'type' => 'fadeBox',
                    'text' => __('Wrong password!'),
                    'failed' => true,
                ],
            ]);
        }

        $planetToDelete = $player->planets->current();

        // NOTE: We are abandoning the current planet. If the user has switched to another planet while this popup
        // is shown or deletion is being processed it is possible that the wrong planet will be deleted.
        // TODO: pass along planet ID explicitly to avoid this issue.

        // Return JSON response to ask user to confirm.
        return response()->json([
            'errorbox' => [
                'type' => 'decision',
                'title' => __('Confirm'),
                'text' => __('If you confirm the deletion of the planet [' . $planetToDelete->getPlanetCoordinates()->asString() . '] (' . $planetToDelete->getPlanetName() . '), all buildings, ships and defense systems that are located on that planet will be removed from your account. If you have items active on your planet, these will also be lost when you give up the planet. This process cannot be reversed!'),
                'buttonOk' => __('Yes'),
                'buttonNOk' => __('No'),
                'okFunction' => 'submit_planet_delete_form',
                'nokFunction' => 'reload',
            ],
            'password_checked' => true,
            'intent' => route('planetabandon.abandon'),
            // TODO: the original code includes "productionBox" key with HTML inside of it, check later if it's needed?
        ]);

    }

    /**
     * Actually abandon the current planet.
     *
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function abandon(PlayerService $player): JsonResponse
    {
        // Get form data
        $password = request('password');

        $planetToDelete = $player->planets->current();

        // Validate password
        if (!$player->isPasswordValid($password)) {
            return response()->json([
                'status' => 'error',
                'errorbox' => [
                    'type' => 'fadeBox',
                    'text' => __('Wrong password!'),
                    'failed' => true,
                ],
            ]);
        }

        // Abandon the planet.
        $planetToDelete->abandonPlanet();

        return response()->json([
            'status' => 'error',
            'errorbox' => [
                'type' => 'notify',
                'title' => __('Reference'),
                'text' => __('Planet has been abandoned succesfully!'),
                'buttonOk' => __('Ok'),
                'okFunction' => 'reloadPage',
            ],
            // TODO: the original code includes "productionBox" key with HTML inside of it, check later if it's needed?
        ]);
    }
}
