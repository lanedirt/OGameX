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
            'isMoon' => $player->planets->current()->isMoon(),
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
            $errorText = $player->planets->current()->isMoon() ? __('The new moon name is invalid. Please try again.') : __('The new planet name is invalid. Please try again.');

            return response()->json([
                'status' => 'error',
                'errorbox' => [
                    'type' => 'fadeBox',
                    'text' => $errorText,
                    'failed' => true,
                ],
            ]);
        }

        // Update planet name
        $player->planets->current()->setPlanetName($planetName);

        $successText = $player->planets->current()->isMoon() ? __('Moon renamed successfully.') : __('Planet renamed successfully.');

        // Return JSON response
        return response()->json([
            'status' => 'success',
            'errorbox' => [
                'type' => 'fadeBox',
                'text' => $successText,
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
        $isMoon = $planetToDelete->isMoon();

        // Return JSON response to ask user to confirm.
        return response()->json([
            'errorbox' => [
                'type' => 'decision',
                'title' => __('Confirm'),
                'text' => __('If you confirm the deletion of the :type [:coordinates] (:name), all buildings, ships and defense systems that are located on that :type will be removed from your account. If you have items active on your :type, these will also be lost when you give up the :type. This process cannot be reversed!', [
                    'type' => $isMoon ? __('moon') : __('planet'),
                    'coordinates' => $planetToDelete->getPlanetCoordinates()->asString(),
                    'name' => $planetToDelete->getPlanetName()
                ]),
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
        $isMoon = $planetToDelete->isMoon();

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

        try {
            // Abandon the planet.
            $planetToDelete->abandonPlanet();
        } catch (Exception $e) {
            // Exception occured, return error.
            return response()->json([
                'status' => 'error',
                'errorbox' => [
                    'type' => 'fadeBox',
                    'text' => $e->getMessage(),
                    'failed' => true,
                ],
            ]);
        }

        // Return success message.
        return response()->json([
            'status' => 'error',
            'errorbox' => [
                'type' => 'notify',
                'title' => __('Reference'),
                'text' => __(':type has been abandoned successfully!', [
                    'type' => $isMoon ? __('Moon') : __('Planet')
                ]),
                'buttonOk' => __('Ok'),
                'okFunction' => 'reloadPage',
            ],
            // TODO: the original code includes "productionBox" key with HTML inside of it, check later if it's needed?
        ]);
    }
}
