<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\PlayerService;

class OptionsController extends OGameController
{
    /**
     * Shows the overview index page
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        $this->setBodyId('preferences');

        $canUpdateUsername = true;
        if ($lastChange = $player->getLastUsernameChange()) {
            $canUpdateUsername = $lastChange->addWeek()->isPast();
        }

        return view('ingame.options.index')->with([
            'username' => $player->getUsername(),
            'current_email' => $player->getEmail(),
            'canUpdateUsername' => $canUpdateUsername,
            'player' => $player,
        ]);
    }

    /**
     * Process change username submit request.
     *
     * @param Request $request
     * @param PlayerService $player
     *
     * @return array<string,string>
     * @throws Exception
     */
    public function processChangeUsername(Request $request, PlayerService $player): array
    {
        $name = $request->input('new_username_username');
        if (!empty($name)) {
            // Check if username validates.
            $validationResult = $player->isUsernameValid($name);
            if (!$validationResult['valid']) {
                return array('error' => $validationResult['error']);
            }

            // Update username
            $player->setUsername($name);
            $player->save();

            return array('success' => __('Settings saved'));
        }

        return array();
    }

    /**
     * Process vacation mode activation/deactivation request.
     *
     * @param Request $request
     * @param PlayerService $player
     *
     * @return array<string,string>
     */
    public function processVacationMode(Request $request, PlayerService $player): array
    {
        $vacationModeChecked = $request->has('urlaubs_modus');

        // If player is currently in vacation mode
        if ($player->isInVacationMode()) {
            // Player wants to deactivate vacation mode
            if (!$vacationModeChecked) {
                if ($player->canDeactivateVacationMode()) {
                    $player->deactivateVacationMode();
                    return array('success' => __('Vacation mode has been deactivated.'));
                } else {
                    return array('error' => __('You can only deactivate vacation mode after the minimum duration of 48 hours has passed.'));
                }
            }
            // If checkbox is still checked while in vacation mode, do nothing
            return array();
        } else {
            // Player is not in vacation mode and wants to activate it
            if ($vacationModeChecked) {
                if ($player->canActivateVacationMode()) {
                    $player->activateVacationMode();
                    return array('success' => __('Vacation mode has been activated. It will protect you from new attacks for a minimum of 48 hours.'));
                } else {
                    return array('error' => __('You cannot activate vacation mode while you have fleets in transit.'));
                }
            }
        }

        return array();
    }

    /**
     * Save handler for index() form.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function save(Request $request, PlayerService $player): RedirectResponse
    {
        // Define change handlers.
        $change_handlers = [
            'processChangeUsername',
            'processVacationMode'
        ];

        // Loop through change handlers, execute them and if it triggers
        // return its message.
        foreach ($change_handlers as $method) {
            $change_handler = $this->{$method}($request, $player);
            if ($change_handler) {
                if (!empty($change_handler['success_logout'])) {
                    return redirect()->route('options.index')->with('success_logout', $change_handler['success_logout']);
                }

                if (!empty($change_handler[ 'success'])) {
                    return redirect()->route('options.index')->with('success', $change_handler['success']);
                }

                if (!empty($change_handler[ 'error'])) {
                    return redirect()->route('options.index')->with('error', $change_handler['error']);
                }
            }
        }

        // No actual change has been detected, return to index page.
        return redirect()->route('options.index');
    }
}
