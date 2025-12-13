<?php

namespace OGame\Services;

use Cache;
use Exception;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Facades\AppUtil;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Highscore;
use OGame\Models\Resources;

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
     * Get player fleet mission score for ships currently in transit.
     * This calculates the general score of all ships that are on active fleet missions.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    private function getPlayerFleetMissionScore(PlayerService $player): int
    {
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsSentByCurrentPlayer();

        $resources_spent = new Resources(0, 0, 0, 0);

        foreach ($activeMissions as $mission) {
            // Skip processed missions (already counted on planet)
            if ($mission->processed) {
                continue;
            }

            // Calculate score for all ships in this mission
            foreach (ObjectService::getShipObjects() as $ship) {
                $amount = $mission->{$ship->machine_name} ?? 0;
                if ($amount > 0) {
                    $raw_price = ObjectService::getObjectRawPrice($ship->machine_name);
                    $resources_spent->add($raw_price->multiply($amount));
                }
            }
        }

        return (int)floor($resources_spent->sum() / 1000);
    }

    /**
     * Get player fleet mission military score for ships currently in transit.
     * Military score includes:
     * - 100% military ships
     * - 50% civil ships
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    private function getPlayerFleetMissionScoreMilitary(PlayerService $player): int
    {
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsSentByCurrentPlayer();

        $resources_spent = 0;

        foreach ($activeMissions as $mission) {
            // Skip processed missions (already counted on planet)
            if ($mission->processed) {
                continue;
            }

            // Military ships (100%)
            foreach (ObjectService::getMilitaryShipObjects() as $ship) {
                $amount = $mission->{$ship->machine_name} ?? 0;
                if ($amount > 0) {
                    $raw_price = ObjectService::getObjectRawPrice($ship->machine_name);
                    $resources_spent += $raw_price->multiply($amount)->sum();
                }
            }

            // Civil ships (50%)
            foreach (ObjectService::getCivilShipObjects() as $ship) {
                $amount = $mission->{$ship->machine_name} ?? 0;
                if ($amount > 0) {
                    $raw_price = ObjectService::getObjectRawPrice($ship->machine_name);
                    $resources_spent += $raw_price->multiply($amount)->sum() * 0.5;
                }
            }
        }

        return (int)floor($resources_spent / 1000);
    }

    /**
     * Get player fleet mission economy score for ships currently in transit.
     * Economy score includes:
     * - 50% civil ships
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    private function getPlayerFleetMissionScoreEconomy(PlayerService $player): int
    {
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $activeMissions = $fleetMissionService->getActiveFleetMissionsSentByCurrentPlayer();

        $resources_spent = 0;

        foreach ($activeMissions as $mission) {
            // Skip processed missions (already counted on planet)
            if ($mission->processed) {
                continue;
            }

            // Civil ships (50%)
            foreach (ObjectService::getCivilShipObjects() as $ship) {
                $amount = $mission->{$ship->machine_name} ?? 0;
                if ($amount > 0) {
                    $raw_price = ObjectService::getObjectRawPrice($ship->machine_name);
                    $resources_spent += $raw_price->multiply($amount)->sum() * 0.5;
                }
            }
        }

        return (int)floor($resources_spent / 1000);
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

        // Get score for fleets that are on missions (in transit)
        $score += $this->getPlayerFleetMissionScore($player);

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

        // Get military score for fleets that are on missions (in transit)
        $points += $this->getPlayerFleetMissionScoreMilitary($player);

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

        // Get economy score for fleets that are on missions (in transit)
        $points += $this->getPlayerFleetMissionScoreEconomy($player);

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
                    'rank' => $playerScore->{$this->highscoreType->name.'_rank'},
                    'is_admin' => $playerService->isAdmin(),
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
