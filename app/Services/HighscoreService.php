<?php

namespace OGame\Services;

use Cache;
use Exception;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Facades\AppUtil;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\CivilShipObjects;
use OGame\GameObjects\MilitaryShipObjects;
use OGame\Models\Alliance;
use OGame\Models\AllianceHighscore;
use OGame\Models\FleetMission;
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

    /**
     * Highscore constructor.
     *
     * @param PlayerServiceFactory $playerServiceFactory PlayerServiceFactory object.
     * @param SettingsService $settingsService SettingsService object.
     */
    public function __construct(private PlayerServiceFactory $playerServiceFactory, private SettingsService $settingsService)
    {
    }

    /**
     * Check if admin users should be visible in highscores.
     *
     * @return bool
     */
    public function isAdminVisibleInHighscore(): bool
    {
        return $this->settingsService->highscoreAdminVisible();
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

        // Cap at PHP_INT_MAX to prevent overflow on PHP 8.5+
        if ($score > PHP_INT_MAX) {
            return PHP_INT_MAX;
        }

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
     * Get player's total ship count across all planets and fleets.
     *
     * @param PlayerService $player
     * @return int
     * @throws Exception
     */
    public function getPlayerTotalShipCount(PlayerService $player): int
    {
        $totalShips = 0;

        // Get all ship objects (military + civil)
        $shipObjects = [...MilitaryShipObjects::get(), ...CivilShipObjects::get()];

        // Count ships on all planets
        foreach ($player->planets->all() as $planet) {
            foreach ($shipObjects as $ship) {
                $totalShips += $planet->getObjectAmount($ship->machine_name);
            }
        }

        // Count ships in active fleet missions (exclude processed missions)
        $fleetMissions = FleetMission::where('user_id', $player->getId())
            ->where('processed', false)
            ->get();
        foreach ($fleetMissions as $mission) {
            // Count ships in the mission
            foreach ($shipObjects as $ship) {
                $shipAmount = $mission->{$ship->machine_name} ?? 0;
                $totalShips += $shipAmount;
            }
        }

        return $totalShips;
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
        $adminVisible = $this->isAdminVisibleInHighscore();
        return Cache::remember(sprintf('highscores-%s-%d-%s', $this->highscoreType->name, $pageOn, $adminVisible ? '1' : '0'), now()->addMinutes(5), function () use ($perPage, $pageOn, $adminVisible) {
            $parsedHighscores = [];

            $query = Highscore::query()
                ->whereHas('player.tech')
                ->with(['player', 'player.alliance', 'player.roles'])
                ->validRanks()
                ->orderBy($this->highscoreType->name.'_rank');

            // Filter out admin users if setting is disabled
            if (!$adminVisible) {
                $query->whereHas('player', function ($q) {
                    $q->whereDoesntHave('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'admin');
                    });
                });
            }

            $highscores = $query->paginate(perPage: $perPage, page: $pageOn);

            foreach ($highscores as $playerScore) {
                // Load player object
                // TODO we only use this for the planet details now-- could we perhaps store the planet details in the highscore table too?.
                $playerService = $this->playerServiceFactory->make($playerScore->player_id);

                // Get player main planet coords
                $mainPlanet = $playerService->planets->first();

                // Skip players without any planets
                if ($mainPlanet === null) {
                    continue;
                }

                $score = $playerScore->{$this->highscoreType->name} ?? 0;
                $score_formatted = AppUtil::formatNumber($score);

                // Get player's alliance information if they're in one
                $allianceTag = null;
                $allianceId = null;
                if ($playerScore->player->alliance_id) {
                    /** @var Alliance|null $alliance */
                    $alliance = $playerScore->player->alliance;
                    if ($alliance) {
                        $allianceTag = $alliance->alliance_tag;
                        $allianceId = $alliance->id;
                    }
                }

                // Get total ship count for military highscore
                $totalShips = null;
                if ($this->highscoreType === HighscoreTypeEnum::military) {
                    $totalShips = $this->getPlayerTotalShipCount($playerService);
                }

                $parsedHighscores[] = [
                    'id' => $playerScore->player_id,
                    'name' => $playerScore->player->username,
                    'points' => $score,
                    'points_formatted' => $score_formatted,
                    'planet_coords' => $mainPlanet->getPlanetCoordinates(),
                    'rank' => $playerScore->{$this->highscoreType->name.'_rank'},
                    'is_admin' => $playerService->isAdmin(),
                    'alliance_tag' => $allianceTag,
                    'alliance_id' => $allianceId,
                    'total_ships' => $totalShips,
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
        $adminVisible = $this->isAdminVisibleInHighscore();
        return Cache::remember('highscore-player-count-' . ($adminVisible ? '1' : '0'), now()->addMinutes(5), function () use ($adminVisible) {
            $query = Highscore::query()->validRanks();

            // Filter out admin users if setting is disabled
            if (!$adminVisible) {
                $query->whereHas('player', function ($q) {
                    $q->whereDoesntHave('roles', function ($roleQuery) {
                        $roleQuery->where('name', 'admin');
                    });
                });
            }

            return $query->count();
        });
    }

    /**
     * Get alliance highscores.
     *
     * @param int $perPage
     * @param int $pageOn
     * @return array<int, array<string,mixed>>
     */
    public function getHighscoreAlliances(int $perPage = 100, int $pageOn = 1): array
    {
        // Get all alliance highscores
        return Cache::remember(sprintf('alliance-highscores-%s-%d', $this->highscoreType->name, $pageOn), now()->addMinutes(5), function () use ($perPage, $pageOn) {
            $parsedHighscores = [];

            $highscores = AllianceHighscore::query()
                ->with('alliance.members')
                ->validRanks()
                ->orderBy($this->highscoreType->name.'_rank')
                ->paginate(perPage: $perPage, page: $pageOn);

            foreach ($highscores as $allianceScore) {
                // Skip if alliance doesn't exist
                if (!$allianceScore->alliance) {
                    continue;
                }

                $score = $allianceScore->{$this->highscoreType->name} ?? 0;
                $score_formatted = AppUtil::formatNumber($score);
                $memberCount = $allianceScore->alliance->members->count();
                $averageScore = $memberCount > 0 ? $score / $memberCount : 0;
                $averageScore_formatted = AppUtil::formatNumber($averageScore);

                $parsedHighscores[] = [
                    'id' => $allianceScore->alliance_id,
                    'name' => $allianceScore->alliance->alliance_name,
                    'tag' => $allianceScore->alliance->alliance_tag,
                    'points' => $score,
                    'points_formatted' => $score_formatted,
                    'average_points' => $averageScore,
                    'average_points_formatted' => $averageScore_formatted,
                    'member_count' => $memberCount,
                    'rank' => $allianceScore->{$this->highscoreType->name.'_rank'},
                ];
            }
            return $parsedHighscores;
        });
    }

    /**
     * Return rank of alliance.
     *
     * @param int $allianceId
     * @return int
     */
    public function getHighscoreAllianceRank(int $allianceId): int
    {
        // Find the alliance in the highscore list to determine its rank.
        $allianceHighscore = AllianceHighscore::where('alliance_id', $allianceId)->first();
        if (!$allianceHighscore) {
            return 0;
        }
        return $allianceHighscore->{$this->highscoreType->name.'_rank'} ?? 0;
    }

    /**
     * Returns the amount of alliances in the game to determine paging for highscore page.
     *
     * @return int
     */
    public function getHighscoreAllianceAmount(): int
    {
        return Cache::remember('highscore-alliance-count', now()->addMinutes(5), function () {
            return AllianceHighscore::query()->validRanks()->count();
        });
    }
}
