<?php

namespace OGame\Console\Commands;

use OGame\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Console\Command;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Models\AllianceHighscore;
use OGame\Models\Highscore;

class GenerateHighscoreRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:generate-highscore-ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Highscore rank data for players and alliances';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (HighscoreTypeEnum::cases() as $type) {
            $this->updatePlayerRank($type);
            $this->updateAllianceRank($type);
        }
    }

    private function updatePlayerRank(HighscoreTypeEnum $type): void
    {
        $rank = 1;
        $this->info("\nUpdating player highscore ranks for $type->name...");

        // Set Legor's rank to 0 (Legor is excluded from highscore, ranked players start at 1)
        $legor = User::where('username', 'Legor')->first();
        if ($legor) {
            $legorHighscore = Highscore::where('player_id', $legor->id)->first();
            if ($legorHighscore) {
                $legorHighscore->{$type->name.'_rank'} = 0;
                $legorHighscore->save();
            }
        }

        // Order by the highscore value in descending order, and by the player creation date in ascending order.
        // This ensures that:
        // - The highest ranked players are at the top of the list.
        // - If two players have the same highscore value, the player who joined the game first will be ranked higher.
        // Legor is excluded from highscore ranking entirely.
        $query = Highscore::query()
            ->join('users', 'highscores.player_id', '=', 'users.id')
            ->where('users.username', '!=', 'Legor')
            ->select('highscores.*')
            ->orderByDesc($type->name)
            ->oldest('users.created_at');

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($type, &$bar, &$rank) {
            /** @var Collection<int, Highscore> $highscores */
            foreach ($highscores as $highscore) {
                $highscore->{$type->name.'_rank'} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll player highscores for type $type->name completed!\n");
    }

    private function updateAllianceRank(HighscoreTypeEnum $type): void
    {
        $rank = 1;
        $this->info("\nUpdating alliance highscore ranks for $type->name...");

        // Order by the highscore value in descending order, and by the alliance creation date in ascending order.
        // This ensures that:
        // - The highest ranked alliances are at the top of the list.
        // - If two alliances have the same highscore value, the alliance that was created first will be ranked higher.
        $query = AllianceHighscore::query()
            ->join('alliances', 'alliance_highscores.alliance_id', '=', 'alliances.id')
            ->select('alliance_highscores.*')
            ->orderByDesc($type->name)
            ->oldest('alliances.created_at');

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($type, &$bar, &$rank) {
            /** @var Collection<int, AllianceHighscore> $highscores */
            foreach ($highscores as $highscore) {
                $highscore->{$type->name.'_rank'} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll alliance highscores for type $type->name completed!\n");
    }
}
