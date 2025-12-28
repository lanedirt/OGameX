<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Models\Alliance;
use OGame\Models\Planet;
use OGame\Models\User;

class SearchController extends OGameController
{
    /**
     * Shows the search popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        return view('ingame.search.overlay');
    }

    /**
     * Perform search based on category
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $searchText = $request->input('searchtext', '');
        $category = (int)$request->input('category', 2); // Default to player search (2)

        if (empty($searchText)) {
            return response()->json([
                'status' => 'error',
                'message' => __('No search term entered'),
                'results' => [],
            ]);
        }

        $results = match ($category) {
            2 => $this->searchPlayers($searchText),
            3 => $this->searchPlanets($searchText),
            4 => $this->searchAlliances($searchText),
            default => [],
        };

        return response()->json([
            'status' => 'success',
            'results' => $results,
            'category' => $category,
        ]);
    }

    /**
     * Search for players by username
     *
     * @param string $searchText
     * @return array<int, array<string, mixed>>
     */
    private function searchPlayers(string $searchText): array
    {
        $users = User::where('username', 'LIKE', '%' . $searchText . '%')
            ->limit(50)
            ->get();

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'name' => $user->username,
                'type' => 'player',
            ];
        }

        return $results;
    }

    /**
     * Search for planets by name
     *
     * @param string $searchText
     * @return array<int, array<string, mixed>>
     */
    private function searchPlanets(string $searchText): array
    {
        $planets = Planet::where('planet_name', 'LIKE', '%' . $searchText . '%')
            ->where('planet_type', 1) // Only planets, not moons
            ->limit(50)
            ->get();

        $results = [];
        foreach ($planets as $planet) {
            $results[] = [
                'id' => $planet->id,
                'name' => $planet->planet_name,
                'coordinates' => '[' . $planet->galaxy . ':' . $planet->system . ':' . $planet->planet . ']',
                'owner_id' => $planet->user_id,
                'type' => 'planet',
            ];
        }

        return $results;
    }

    /**
     * Search for alliances by name or tag
     *
     * @param string $searchText
     * @return array<int, array<string, mixed>>
     */
    private function searchAlliances(string $searchText): array
    {
        $alliances = Alliance::where('alliance_name', 'LIKE', '%' . $searchText . '%')
            ->orWhere('alliance_tag', 'LIKE', '%' . $searchText . '%')
            ->with('highscore')
            ->limit(50)
            ->get();

        $results = [];
        foreach ($alliances as $alliance) {
            $results[] = [
                'id' => $alliance->id,
                'name' => $alliance->alliance_name,
                'tag' => $alliance->alliance_tag,
                'member_count' => $alliance->member_count,
                'rank' => $alliance->highscore?->general_rank ?? '?',
                'points' => $alliance->highscore?->general ?? 0,
                'is_open' => $alliance->is_open,
                'type' => 'alliance',
            ];
        }

        return $results;
    }
}
