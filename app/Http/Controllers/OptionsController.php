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

        return view('ingame.options.index')->with([
            'username' => $player->getUsername(),
            'current_email' => $player->getEmail(),
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
        $password = $request->input('new_username_password');
        if (!empty($name)) {
            // Check if password matches.
            if (!$player->validatePassword($password)) {
                return array('error' => 'Wrong password!');
            }

            // Check if username validates.
            if (!$player->validateUsername($name)) {
                return array('error' => 'Illegal characters in username!');
            }

            // Update username
            $player->setUsername($name);
            $player->save();

            return array('success_logout' => 'Settings saved');
        }

        return [];
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
            'processChangeUsername'
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
