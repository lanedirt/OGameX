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
        $this->info("Updating highscore ranks for $type->name...");
        $query = Highscore::query()->whereHas('player.tech')
        ->where($type->name, '!=', null);
        $query->orderBy($type->name, 'DESC');
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
        $this->info("All highscores for type $type->name completed!");
    }
}
