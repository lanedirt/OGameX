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
        // Create highscore service.
        $highscoreService = app()->make(HighscoreService::class);

        // Current player rank.
        $currentPlayerRank = $highscoreService->getHighscorePlayerRank($player);

        // Initial page based on current player rank (round to the nearest 100 floored).
        $page = floor($currentPlayerRank / 100)  + 1;
        $offset_start = ($page - 1) * 100;

        return view('ingame.highscore.index')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers($offset_start),
            'highscorePlayerAmount' => $highscoreService->getHighscorePlayerAmount(),
            'highscoreCurrentPlayerRank' => $currentPlayerRank,
            'highscoreCurrentPlayerPage' => $page,
            'highscoreCurrentPage' => $page,
            'player' => $player,
        ]);
    }
}
