<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\HighscoreService;
use OGame\Services\PlayerService;

class HighscoreController extends Controller
{
    use IngameTrait;

    /**
     * Shows the facilities index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player)
    {
        // Create highscore service
        $highscoreService = app()->make(HighscoreService::class);

        return view('ingame.highscore.index')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers(),
            'player' => $player,
        ]);
    }
}
