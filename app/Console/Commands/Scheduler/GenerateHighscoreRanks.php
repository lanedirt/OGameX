<?php

namespace OGame\Console\Commands\Scheduler;

use Cache;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Enums\MilitaryHighscoreTypeEnum;
use OGame\Models\AllianceHighscore;
use OGame\Models\Highscore;
use OGame\Models\User;
use OGame\Services\SettingsService;

class GenerateHighscoreRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:scheduler:generate-highscore-ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Highscore rank data for players and alliances';

    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settingsService): void
    {
        $adminVisible = $settingsService->highscoreAdminVisible();

        foreach (HighscoreTypeEnum::cases() as $type) {
            // For military type, process each subcategory separately
            if ($type === HighscoreTypeEnum::military) {
                foreach (MilitaryHighscoreTypeEnum::cases() as $militaryType) {
                    $this->updatePlayerRankMilitary($militaryType, $adminVisible);
                    // TODO: Update alliance highscores when alliance military subcategories are added
                    // $this->updateAllianceRankMilitary($militaryType);
                }
            } else {
                $this->updatePlayerRank($type, $adminVisible);
                $this->updateAllianceRank($type);
            }
        }

        // Clear highscore cache so changes are reflected immediately
        $this->clearHighscoreCache();
        $this->info("\nHighscore cache cleared.");
    }

    /**
     * Get the database column name for the given highscore type.
     * Maps 'military' type to 'military_built' column.
     */
    private function getColumnName(HighscoreTypeEnum $type): string
    {
        if ($type === HighscoreTypeEnum::military) {
            return 'military_built';
        }
        return $type->name;
    }

    /**
     * Clear all highscore-related cache entries.
     */
    private function clearHighscoreCache(): void
    {
        // Clear player count cache for both admin visible states
        Cache::forget('highscore-player-count-0');
        Cache::forget('highscore-player-count-1');

        // Clear alliance count cache
        Cache::forget('highscore-alliance-count');

        // Clear highscore list cache for all types and pages
        foreach (HighscoreTypeEnum::cases() as $type) {
            for ($page = 1; $page <= 100; $page++) {
                Cache::forget(sprintf('highscores-%s-%d-0', $type->name, $page));
                Cache::forget(sprintf('highscores-%s-%d-1', $type->name, $page));
                Cache::forget(sprintf('alliance-highscores-%s-%d', $type->name, $page));
            }
        }

        // Clear military subcategory cache
        foreach (MilitaryHighscoreTypeEnum::cases() as $militaryType) {
            $columnName = 'military_' . $militaryType->name;
            for ($page = 1; $page <= 100; $page++) {
                Cache::forget(sprintf('highscores-%s-%d-0', $columnName, $page));
                Cache::forget(sprintf('highscores-%s-%d-1', $columnName, $page));
            }
        }
    }

    private function updatePlayerRank(HighscoreTypeEnum $type, bool $adminVisible): void
    {
        $rank = 1;
        $columnName = $this->getColumnName($type);
        $rankColumnName = $columnName . '_rank';
        $this->info("\nUpdating player highscore ranks for $columnName...");

        // Set Legor's rank to 0 (Legor is excluded from highscore, ranked players start at 1)
        $legor = User::where('username', 'Legor')->first();
        if ($legor) {
            $legorHighscore = Highscore::where('player_id', $legor->id)->first();
            if ($legorHighscore) {
                $legorHighscore->{$rankColumnName} = 0;
                $legorHighscore->save();
            }
        }

        // Set admin users' ranks to 0 if admins are excluded from highscore
        if (!$adminVisible) {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            foreach ($adminUsers as $adminUser) {
                $adminHighscore = Highscore::where('player_id', $adminUser->id)->first();
                if ($adminHighscore) {
                    $adminHighscore->{$rankColumnName} = 0;
                    $adminHighscore->save();
                }
            }
        }

        // Order by the highscore value in descending order, and by the player creation date in ascending order.
        // This ensures that:
        // - The highest ranked players are at the top of the list.
        // - If two players have the same highscore value, the player who joined the game first will be ranked higher.
        // Legor is excluded from highscore ranking entirely.
        // Admin users are excluded if the highscore_admin_visible setting is disabled.
        $query = Highscore::query()
            ->join('users', 'highscores.player_id', '=', 'users.id')
            ->where('users.username', '!=', 'Legor')
            ->select('highscores.*')
            ->orderByDesc($columnName)
            ->oldest('users.created_at');

        // Exclude admin users from ranking if setting is disabled
        if (!$adminVisible) {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.name', 'admin');
            });
        }

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($rankColumnName, &$bar, &$rank) {
            /** @var Collection<int, Highscore> $highscores */
            foreach ($highscores as $highscore) {
                $highscore->{$rankColumnName} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll player highscores for type $columnName completed!\n");
    }

    private function updateAllianceRank(HighscoreTypeEnum $type): void
    {
        $rank = 1;
        // Note: Alliance highscores don't have military subcategories yet, so military still uses 'military' column
        $columnName = $type->name;
        // Special handling: alliance military uses 'military_built_rank' instead of 'military_rank'
        $rankColumnName = ($type === HighscoreTypeEnum::military) ? 'military_built_rank' : $columnName . '_rank';
        $this->info("\nUpdating alliance highscore ranks for $columnName...");

        // Order by the highscore value in descending order, and by the alliance creation date in ascending order.
        // This ensures that:
        // - The highest ranked alliances are at the top of the list.
        // - If two alliances have the same highscore value, the alliance that was created first will be ranked higher.
        $query = AllianceHighscore::query()
            ->join('alliances', 'alliance_highscores.alliance_id', '=', 'alliances.id')
            ->select('alliance_highscores.*')
            ->orderByDesc($columnName)
            ->oldest('alliances.created_at');

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($rankColumnName, &$bar, &$rank) {
            /** @var Collection<int, AllianceHighscore> $highscores */
            foreach ($highscores as $highscore) {
                /** @phpstan-ignore property.notFound */
                $highscore->{$rankColumnName} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll alliance highscores for type $columnName completed!\n");
    }

    /**
     * Update player rank for military subcategories
     */
    private function updatePlayerRankMilitary(MilitaryHighscoreTypeEnum $type, bool $adminVisible): void
    {
        $rank = 1;
        $columnName = 'military_' . $type->name;
        $rankColumnName = $columnName . '_rank';
        $this->info("\nUpdating player highscore ranks for $columnName...");

        // Set Legor's rank to 0
        $legor = User::where('username', 'Legor')->first();
        if ($legor) {
            $legorHighscore = Highscore::where('player_id', $legor->id)->first();
            if ($legorHighscore) {
                $legorHighscore->{$rankColumnName} = 0;
                $legorHighscore->save();
            }
        }

        // Set admin users' ranks to 0 if admins are excluded
        if (!$adminVisible) {
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->get();

            foreach ($adminUsers as $adminUser) {
                $adminHighscore = Highscore::where('player_id', $adminUser->id)->first();
                if ($adminHighscore) {
                    $adminHighscore->{$rankColumnName} = 0;
                    $adminHighscore->save();
                }
            }
        }

        $query = Highscore::query()
            ->join('users', 'highscores.player_id', '=', 'users.id')
            ->where('users.username', '!=', 'Legor')
            ->select('highscores.*')
            ->orderByDesc($columnName)
            ->oldest('users.created_at');

        if (!$adminVisible) {
            $query->whereNotExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->whereColumn('model_has_roles.model_id', 'users.id')
                    ->where('model_has_roles.model_type', User::class)
                    ->where('roles.name', 'admin');
            });
        }

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($rankColumnName, &$bar, &$rank) {
            /** @var Collection<int, Highscore> $highscores */
            foreach ($highscores as $highscore) {
                $highscore->{$rankColumnName} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll player highscores for military $type->name completed!\n");
    }

    /**
     * Update alliance rank for military subcategories
     * TODO: Implement when alliance military subcategories are added to database
     */
    // private function updateAllianceRankMilitary(MilitaryHighscoreTypeEnum $type): void
    // {
    //     $rank = 1;
    //     $columnName = 'military_' . $type->name;
    //     $rankColumnName = $columnName . '_rank';
    //     $this->info("\nUpdating alliance highscore ranks for $columnName...");
    //
    //     $query = AllianceHighscore::query()
    //         ->join('alliances', 'alliance_highscores.alliance_id', '=', 'alliances.id')
    //         ->select('alliance_highscores.*')
    //         ->orderByDesc($columnName)
    //         ->oldest('alliances.created_at');
    //
    //     $bar = $this->output->createProgressBar();
    //     $bar->start($query->count());
    //
    //     $query->chunk(200, function ($highscores) use ($rankColumnName, &$bar, &$rank) {
    //         /** @var Collection<int, AllianceHighscore> $highscores */
    //         foreach ($highscores as $highscore) {
    //             $highscore->{$rankColumnName} = $rank;
    //             $highscore->save();
    //             $bar->advance();
    //             $rank++;
    //         }
    //     });
    //     $this->info("\nAll alliance highscores for military $type->name completed!\n");
    // }
}
