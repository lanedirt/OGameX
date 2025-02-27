<?php

namespace OGame\Services;

use Cache;
use Exception;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Facades\AppUtil;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Highscore;

/**
 * Class Highscore.
 *
 * Service object for calculating and retrieving highscores.
 *
 * @package OGame\Services
 */
class HighscoreService
{
    /**
     * Highscore type to calculate.
     * @var HighscoreTypeEnum
     */
    private HighscoreTypeEnum $highscoreType;

    private PlayerServiceFactory $playerServiceFactory;

    /**
     * Highscore constructor.
     */
    public function __construct(PlayerServiceFactory $playerServiceFactory)
    {
        $this->playerServiceFactory = $playerServiceFactory;
    }

    /**
     * Set the highscore type to calculate.
     *
     * @param int $type
     * @return void
     */
    public function setHighscoreType(int $type): void
    {
        // 0 = general score
        // 1 = economy points
        // 2 = research points
        // 3 = military points
        $this->highscoreType = HighscoreTypeEnum::cases()[$type];
    }

    /**
     * Get player score.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    public function getPlayerScore(PlayerService $player): int
    {
        $score = 0;
        // Get score for buildings and units on player owned planets
        foreach ($player->planets->all() as $planet) {
            $score += $planet->getPlanetScore();
        }

        // Get score for research levels of player
        $score += $player->getResearchScore();

        // TODO: add score for fleets that are not on a planet (flying missions).

        return $score;
    }

    /**
     * Get player research score.
     *
     * @param PlayerService $player
     * @return int
     */
    public function getPlayerScoreResearch(PlayerService $player): int
    {
        return $player->getResearchScore();
    }

    /**
     * Get player military score.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    public function getPlayerScoreMilitary(PlayerService $player): int
    {
        $points = 0;

        // Get points (sum of all unit amounts) for units on player owned planets.
        foreach ($player->planets->all() as $planet) {
            $points += $planet->getPlanetMilitaryScore();
        }

        return $points;
    }

    /**
     * Get player economy score.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    public function getPlayerScoreEconomy(PlayerService $player): int
    {
        $points = 0;

        // Get score for buildings and units on player owned planets (economy specific calculation).
        foreach ($player->planets->all() as $planet) {
            $points += $planet->getPlanetScoreEconomy();
        }

        return $points;
    }

    /**
     * Get highscores.
     *
     * @param int $perPage
     * @param int $pageOn
     * @return array<int, array<string,mixed>>
     */
    public function getHighscorePlayers(int $perPage = 100, int $pageOn = 1): array
    {
        // Get all player highscores
        return Cache::remember(sprintf('highscores-%s-%d', $this->highscoreType->name, $pageOn), now()->addMinutes(5), function () use ($perPage, $pageOn) {
            $parsedHighscores = [];

            $highscores = Highscore::query()
                ->whereHas('player.tech')
                ->with('player')
                ->validRanks()
                ->orderBy($this->highscoreType->name.'_rank')
                ->paginate(perPage: $perPage, page: $pageOn);

            foreach ($highscores as $playerScore) {
                // Load player object
                // TODO we only use this for the planet details now-- could we perhaps store the planet details in the highscore table too?.
                $playerService = $this->playerServiceFactory->make($playerScore->player_id);

                // Get player main planet coords
                $mainPlanet = $playerService->planets->first();

                $score = $playerScore->{$this->highscoreType->name} ?? 0;
                $score_formatted = AppUtil::formatNumber($score);

                $parsedHighscores[] = [
                    'id' => $playerScore->player_id,
                    'name' => $playerScore->player->username,
                    'points' => $score,
                    'points_formatted' => $score_formatted,
                    'planet_coords' => $mainPlanet->getPlanetCoordinates(),
                    'rank' => $playerScore->{$this->highscoreType->name.'_rank'}
                ];
            }
            return $parsedHighscores;
        });
    }

    /**
     * Return rank of player.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    public function getHighscorePlayerRank(PlayerService $player): int
    {
        // Find the player in the highscore list to determine its rank.
        return Highscore::where('player_id', $player->getId())->first()->general_rank ?? 0;
    }

    /**
     * Returns the amount of players in the game to determine paging for highscore page.
     *
     * @return int
     */
    public function getHighscorePlayerAmount(): int
    {
        return Cache::remember('highscore-player-count', now()->addMinutes(5), function () {
            return Highscore::query()->validRanks()->count();
        });
    }
}
