<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Services\PlayerService;

class ChangeNickController extends OGameController
{
    /**
     * Shows the username change popup page
     *
     * @param PlayerService $player
     * @return View
     */
    public function overlay(PlayerService $player): View
    {
        $canUpdateUsername = true;
        if ($lastChange = $player->getLastUsernameChange()) {
            $canUpdateUsername = $lastChange->addWeek()->isPast();
        }

        return view('ingame.changenick.overlay')->with([
            'currentUsername' => $player->getUsername(false),
            'canUpdateUsername' => $canUpdateUsername,
        ]);
    }

    /**
     * Rename the player username.
     *
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function rename(Request $request, PlayerService $player): RedirectResponse
    {
        // Get form data
        $name = $request->input('db_character');
        $password = $request->input('db_character_password');

        // Validate last username update
        if (($lastChange = $player->getLastUsernameChange()) && !$lastChange->addWeek()->isPast()) {
            return $this->errorResponse(__('You can only change your username once per week.'));
        }

        // Validate password
        if (empty($password) || !$player->validatePassword($password)) {
            return $this->errorResponse(__('Wrong password!'));
        }

        // Validate username
        $validationResult = $player->isUsernameValid($name);
        if (!$validationResult['valid']) {
            return $this->errorResponse($validationResult['error']);
        }

        // Update username
        $player->setUsername($name);
        $player->save();

        // Return success response
        return redirect()->route('overview.index')->with('success', __('Player name changed!'));
    }

    /**
     * Return an error response.
     *
     * @param string $message
     * @return RedirectResponse
     */
    private function errorResponse(string $message): RedirectResponse
    {
        return redirect()->route('overview.index')->with('error', $message);
    }
}
