<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Services\HighscoreService;
use OGame\Services\PlayerService;

class HighscoreController extends OGameController
{
    /**
     * Shows the highscore index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HighscoreService $highscoreService
     * @return View
     */
    public function index(Request $request, PlayerService $player, HighscoreService $highscoreService): View
    {
        $this->setBodyId('highscore');

        return view('ingame.highscore.index')->with([
            'initialContent' => $this->ajax($request, $player, $highscoreService),
        ]);
    }

    /**
     * Returns highscore AJAX paging content.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HighscoreService $highscoreService
     * @return View
     */
    public function ajax(Request $request, PlayerService $player, HighscoreService $highscoreService): View
    {
        // Check if we received category parameter, if so, use it to determine which highscore category to show.
        // 1 = players
        // 2 = alliances
        $category = $request->input('category', '1');
        if (!empty($category)) {
            $category = (int)$category;
        } else {
            $category = 0;
        }

        if ($category == 1) {
            return $this->ajaxPlayer($request, $player, $highscoreService);
        } else {
            return $this->ajaxAlliance($request, $player, $highscoreService);
        }
    }

    /**
     * Returns highscore AJAX paging content.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     */
    public function ajaxPlayer(Request $request, PlayerService $player, HighscoreService $highscoreService): View
    {
        // Check if we received type parameter, if so, use it to determine which highscore type to show.
        // 0 = points
        // 1 = economy
        // 2 = research
        // 3 = military
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = (int)$type;
        } else {
            $type = 0;
        }

        $highscoreService->setHighscoreType($type);

        // Current player rank.
        $currentPlayerRank = $highscoreService->getHighscorePlayerRank($player);

        $currentPlayerPage = floor($currentPlayerRank / 100)  + 1;

        // Check if we received a page number, if so, use it instead of the current player rank.
        $page = $request->input('page', null);
        if (!empty($page)) {
            $page = (int)$page;
        } else {
            // Initial page based on current player rank (round to the nearest 100 floored).
            $page = (int)$currentPlayerPage;
        }

        // Get highscore players content view statically to insert into page.
        return view('ingame.highscore.players_points')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers(pageOn: $page),
            'highscorePlayerAmount' => $highscoreService->getHighscorePlayerAmount(),
            'highscoreCurrentPlayerRank' => $currentPlayerRank,
            'highscoreCurrentPlayerPage' => $currentPlayerPage,
            'highscoreCurrentPage' => $page,
            'highscoreCurrentType' => $type,
            'player' => $player,
        ]);
    }

    /**
     * Returns highscore AJAX paging content.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param HighscoreService $highscoreService
     * @return View
     */
    public function ajaxAlliance(Request $request, PlayerService $player, HighscoreService $highscoreService): View
    {
        // TODO: implement alliance highscore.

        // Check if we received type parameter, if so, use it to determine which highscore type to show.
        // 0 = points
        // 1 = economy
        // 2 = research
        // 3 = military
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = (int)$type;
        } else {
            $type = 0;
        }

        $highscoreService->setHighscoreType($type);

        // Current player rank.
        $currentPlayerRank = 1;
        $currentPlayerPage = 1;

        // Check if we received a page number, if so, use it instead of the current player rank.
        $page = $request->input('page', null);
        if (!empty($page)) {
            $page = (int)$page;
        } else {
            // Initial page based on current player rank (round to the nearest 100 floored).
            $page = $currentPlayerPage;
        }

        // Get highscore players content view statically to insert into page.
        return view('ingame.highscore.alliance_points')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers(pageOn: $page),
            'highscorePlayerAmount' => $highscoreService->getHighscorePlayerAmount(),
            'highscoreCurrentPlayerRank' => $currentPlayerRank,
            'highscoreCurrentPlayerPage' => $currentPlayerPage,
            'highscoreCurrentPage' => $page,
            'highscoreCurrentType' => $type,
            'player' => $player,
        ]);
    }
}
