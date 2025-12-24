<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Enums\HighscoreTypeEnum;
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
    protected $description = 'Generates Highscore rank data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (HighscoreTypeEnum::cases() as $type) {
            $this->updateTypeRank($type);
        }
    }

    private function updateTypeRank(HighscoreTypeEnum $type): void
    {
        $rank = 1;
        $this->info("\nUpdating highscore ranks for $type->name...");

        // Set Legor's rank to NULL (Legor is excluded from highscore)
        $legorHighscore = Highscore::where('player_id', 1)->first();
        if ($legorHighscore) {
            $legorHighscore->{$type->name.'_rank'} = null;
            $legorHighscore->save();
        }

        // Order by the highscore value in descending order, and by the player creation date in ascending order.
        // This ensures that:
        // - The highest ranked players are at the top of the list.
        // - If two players have the same highscore value, the player who joined the game first will be ranked higher.
        // Legor (player ID 1) is excluded from highscore ranking entirely.
        $query = Highscore::query()
            ->where('player_id', '!=', 1) // Exclude Legor
            ->select('highscores.*')
            ->orderByDesc($type->name)
            ->orderBy('created_at');

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($type, &$bar, &$rank) {
            /** @var \Illuminate\Support\Collection<int, Highscore> $highscores */
            foreach ($highscores as $highscore) {
                $highscore->{$type->name.'_rank'} = $rank;
                $highscore->save();
                $bar->advance();
                $rank++;
            }
        });
        $this->info("\nAll highscores for type $type->name completed!\n");
    }
}
