<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\PlayerService;

class ServerSettingsController extends OGameController
{
    /**
     * Shows the server settings page.
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        return view('ingame.admin.serversettings')->with([
            'username' => $player->getUsername(),
            'current_email' => $player->getEmail(),
        ]);
    }
}
