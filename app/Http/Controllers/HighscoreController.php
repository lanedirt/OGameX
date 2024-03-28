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
     * Shows the highscore index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request, PlayerService $player)
    {
        return view('ingame.highscore.index')->with([
            'initialContent' => $this->ajax($request, $player),
        ]);
    }

    /**
     * Returns highscore AJAX paging content.
     *
     * @param int $id
     * @return Response
     */
    public function ajax(Request $request, PlayerService $player)
    {
        // Check if we received category parameter, if so, use it to determine which highscore category to show.
        // 1 = players
        // 2 = alliances
        $category = $request->input('category', '1');
        if (!empty($category)) {
            $category = intval($category);
        }
        else {
            $category = 0;
        }

        if ($category == 1) {
            return $this->ajaxPlayer($request, $player);
        }
        else {
            return $this->ajaxAlliance($request, $player);
        }
    }

    /**
     * Returns highscore AJAX paging content.
     *
     * @param int $id
     * @return Response
     */
    public function ajaxPlayer(Request $request, PlayerService $player)
    {
        // Create highscore service.
        $highscoreService = app()->make(HighscoreService::class);

        // Check if we received type parameter, if so, use it to determine which highscore type to show.
        // 0 = points
        // 1 = economy
        // 2 = research
        // 3 = military
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = intval($type);
        }
        else {
            $type = 0;
        }

        $highscoreService->setHighscoreType($type);

        // Current player rank.
        $currentPlayerRank = $highscoreService->getHighscorePlayerRank($player);
        $currentPlayerPage = floor($currentPlayerRank / 100)  + 1;

        // Check if we received a page number, if so, use it instead of the current player rank.
        $page = $request->input('page', null);
        if (!empty($page))  {
            $page = intval($page);
        }
        else {
            // Initial page based on current player rank (round to the nearest 100 floored).
            $page = $currentPlayerPage;
        }

        $offset_start = ($page - 1) * 100;

        // Get highscore players content view statically to insert into page.
        return view('ingame.highscore.players_points')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers($offset_start),
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
     * @param int $id
     * @return Response
     */
    public function ajaxAlliance(Request $request, PlayerService $player)
    {
        // TODO: implement alliance highscore.
        // Create highscore service.
        $highscoreService = app()->make(HighscoreService::class);

        // Check if we received type parameter, if so, use it to determine which highscore type to show.
        // 0 = points
        // 1 = economy
        // 2 = research
        // 3 = military
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = intval($type);
        }
        else {
            $type = 0;
        }

        $highscoreService->setHighscoreType($type);

        // Current player rank.
        $currentPlayerRank = 1;
        $currentPlayerPage = 1;

        // Check if we received a page number, if so, use it instead of the current player rank.
        $page = $request->input('page', null);
        if (!empty($page))  {
            $page = intval($page);
        }
        else {
            // Initial page based on current player rank (round to the nearest 100 floored).
            $page = $currentPlayerPage;
        }

        $offset_start = ($page - 1) * 100;

        // Get highscore players content view statically to insert into page.
        return view('ingame.highscore.alliance_points')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers($offset_start),
            'highscorePlayerAmount' => $highscoreService->getHighscorePlayerAmount(),
            'highscoreCurrentPlayerRank' => $currentPlayerRank,
            'highscoreCurrentPlayerPage' => $currentPlayerPage,
            'highscoreCurrentPage' => $page,
            'highscoreCurrentType' => $type,
            'player' => $player,
        ]);
    }
}
