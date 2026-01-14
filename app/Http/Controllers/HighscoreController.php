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
        // 3 = military (defaults to military_built)
        // 4 = military lost
        // 5 = military built
        // 6 = military destroyed
        // 7 = honour points (not yet implemented)
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = (int)$type;
        } else {
            $type = 0;
        }

        // TODO: Implement honour points system (type 7)
        // Honour points should track honourable vs dishonourable battles based on military points comparison
        // For now, honour points show all zeros since the feature is not yet implemented
        if ($type === 7) {
            // Return empty highscore view with all zeros for honour points
            return view('ingame.highscore.players_points')->with([
                'highscorePlayers' => [], // Empty - no honour points data yet
                'highscorePlayerAmount' => 0,
                'highscoreCurrentPlayerRank' => 0,
                'highscoreCurrentPlayerPage' => 1,
                'highscoreCurrentPage' => 1,
                'highscoreCurrentType' => 7,
                'player' => $player,
                'highscoreAdminVisible' => $highscoreService->isAdminVisibleInHighscore(),
                'currentPlayerIsAdmin' => $player->isAdmin(),
            ]);
        }

        // Map frontend type values to backend type and subcategory
        // Frontend: 3=military_built (default), 4=military_lost, 5=military_built, 6=military_destroyed
        $militarySubcategory = null;
        if ($type >= 3 && $type <= 6) {
            // Map to military subcategories
            $militarySubcategory = match($type) {
                3, 5 => 0, // built (default and explicit)
                6 => 1,    // destroyed
                4 => 2,    // lost
            };
            $type = 3; // All military subcategories use type 3
        }

        $highscoreService->setHighscoreType($type, $militarySubcategory);

        // Check if we're searching for a specific player's rank
        $searchRelId = $request->input('searchRelId', null);
        if ($searchRelId) {
            // Get the rank of the searched player
            $searchedPlayer = resolve(PlayerService::class, ['player_id' => (int)$searchRelId]);
            $searchedPlayerRank = $highscoreService->getHighscorePlayerRank($searchedPlayer);
            $page = (int) (floor($searchedPlayerRank / 100) + 1);
        } else {
            // Current player rank.
            $currentPlayerRank = $highscoreService->getHighscorePlayerRank($player);
            $currentPlayerPage = floor($currentPlayerRank / 100) + 1;

            // Check if we received a page number, if so, use it instead of the current player rank.
            $page = $request->input('page', null);
            if (!empty($page)) {
                $page = (int)$page;
            } else {
                // Initial page based on current player rank (round to the nearest 100 floored).
                $page = (int)$currentPlayerPage;
            }
        }

        // Current player rank (for highlighting purposes)
        $currentPlayerRank = $highscoreService->getHighscorePlayerRank($player);
        $currentPlayerPage = floor($currentPlayerRank / 100) + 1;

        // Get highscore players content view statically to insert into page.
        return view('ingame.highscore.players_points')->with([
            'highscorePlayers' => $highscoreService->getHighscorePlayers(pageOn: $page),
            'highscorePlayerAmount' => $highscoreService->getHighscorePlayerAmount(),
            'highscoreCurrentPlayerRank' => $currentPlayerRank,
            'highscoreCurrentPlayerPage' => $currentPlayerPage,
            'highscoreCurrentPage' => $page,
            'highscoreCurrentType' => $type,
            'player' => $player,
            'highscoreAdminVisible' => $highscoreService->isAdminVisibleInHighscore(),
            'currentPlayerIsAdmin' => $player->isAdmin(),
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
        // Check if we received type parameter, if so, use it to determine which highscore type to show.
        // 0 = points
        // 1 = economy
        // 2 = research
        // 3 = military (defaults to military_built)
        // 4 = military lost
        // 5 = military built
        // 6 = military destroyed
        // 7 = honour points (not yet implemented)
        // Note: Alliance military subcategories not yet implemented, defaults to 'military' column
        $type = $request->input('type', '0');
        if (!empty($type)) {
            $type = (int)$type;
        } else {
            $type = 0;
        }

        // TODO: Implement honour points system (type 7) for alliances
        // Honour points should track honourable vs dishonourable battles based on military points comparison
        // For now, honour points show all zeros since the feature is not yet implemented
        if ($type === 7) {
            // Return empty highscore view with all zeros for honour points
            return view('ingame.highscore.alliance_points')->with([
                'highscoreAlliances' => [], // Empty - no honour points data yet
                'highscoreAllianceAmount' => 0,
                'highscoreCurrentAllianceRank' => 0,
                'highscoreCurrentAlliancePage' => 1,
                'highscoreCurrentPage' => 1,
                'highscoreCurrentType' => 7,
                'currentUserAllianceId' => auth()->user()->alliance_id,
                'player' => $player,
            ]);
        }

        // Map frontend type values to backend type
        // For alliances, military subcategories aren't implemented yet, so all military types use the same column
        if ($type >= 3 && $type <= 6) {
            $type = 3; // All military types default to military column for alliances
        }

        $highscoreService->setHighscoreType($type, null);

        // Check if we're searching for a specific alliance's rank
        $searchRelId = $request->input('searchRelId', null);
        if ($searchRelId) {
            // Get the rank of the searched alliance
            $searchedAllianceRank = $highscoreService->getHighscoreAllianceRank((int)$searchRelId);
            $page = $searchedAllianceRank > 0 ? (int) (floor($searchedAllianceRank / 100) + 1) : 1;
        } else {
            // Current player's alliance rank
            $currentAllianceRank = 0;
            $currentAlliancePage = 1;

            $userAllianceId = auth()->user()->alliance_id;
            if ($userAllianceId) {
                $currentAllianceRank = $highscoreService->getHighscoreAllianceRank($userAllianceId);
                if ($currentAllianceRank > 0) {
                    $currentAlliancePage = (int) floor($currentAllianceRank / 100) + 1;
                }
            }

            // Check if we received a page number, if so, use it instead of the current alliance rank.
            $page = $request->input('page', null);
            if (!empty($page)) {
                $page = (int)$page;
            } else {
                // Initial page based on current alliance rank (round to the nearest 100 floored).
                $page = (int)$currentAlliancePage;
            }
        }

        // Current alliance rank (for highlighting purposes)
        $currentAllianceRank = 0;
        $currentAlliancePage = 1;

        $userAllianceId = auth()->user()->alliance_id;
        if ($userAllianceId) {
            $currentAllianceRank = $highscoreService->getHighscoreAllianceRank($userAllianceId);
            if ($currentAllianceRank > 0) {
                $currentAlliancePage = (int) floor($currentAllianceRank / 100) + 1;
            }
        }

        // Get highscore alliances content view statically to insert into page.
        return view('ingame.highscore.alliance_points')->with([
            'highscoreAlliances' => $highscoreService->getHighscoreAlliances(pageOn: $page),
            'highscoreAllianceAmount' => $highscoreService->getHighscoreAllianceAmount(),
            'highscoreCurrentAllianceRank' => $currentAllianceRank,
            'highscoreCurrentAlliancePage' => $currentAlliancePage,
            'highscoreCurrentPage' => $page,
            'highscoreCurrentType' => $type,
            'currentUserAllianceId' => $userAllianceId,
            'player' => $player,
        ]);
    }
}
