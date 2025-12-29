<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Models\AllianceHighscore;

class GenerateAllianceHighscoreRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:generate-alliance-highscore-ranks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Alliance Highscore rank data';

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
        $this->info("\nUpdating alliance highscore ranks for $type->name...");

        // Order by the highscore value in descending order, and by the alliance creation date in ascending order.
        // This ensures that:
        // - The highest ranked alliances are at the top of the list.
        // - If two alliances have the same highscore value, the alliance that was created first will be ranked higher.
        $query = AllianceHighscore::query()
            ->join('alliances', 'alliance_highscores.alliance_id', '=', 'alliances.id')
            ->select('alliance_highscores.*')
            ->orderByDesc($type->name)
            ->orderBy('alliances.created_at');

        $bar = $this->output->createProgressBar();
        $bar->start($query->count());

        $query->chunk(200, function ($highscores) use ($type, &$bar, &$rank) {
            /** @var \Illuminate\Support\Collection<int, AllianceHighscore> $highscores */
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
