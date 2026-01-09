<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Models\Alliance;
use OGame\Models\AllianceHighscore;
use OGame\Models\Highscore;

class GenerateAllianceHighscores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:generate-alliance-highscores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Alliance Highscore data by aggregating member scores';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Generating alliance highscores...');

        $alliances = Alliance::query();
        $bar = $this->output->createProgressBar();
        $bar->start($alliances->count());

        $alliances->chunk(50, function ($allianceChunk) use (&$bar) {
            foreach ($allianceChunk as $alliance) {
                // Aggregate scores from all alliance members
                $memberScores = Highscore::query()
                    ->join('users', 'highscores.player_id', '=', 'users.id')
                    ->where('users.alliance_id', $alliance->id)
                    ->selectRaw('
                        COALESCE(SUM(highscores.general), 0) as total_general,
                        COALESCE(SUM(highscores.economy), 0) as total_economy,
                        COALESCE(SUM(highscores.research), 0) as total_research,
                        COALESCE(SUM(highscores.military), 0) as total_military
                    ')
                    ->first();

                // Update or create alliance highscore record
                AllianceHighscore::updateOrCreate(
                    ['alliance_id' => $alliance->id],
                    [
                        'general' => $memberScores->total_general ?? 0,
                        'economy' => $memberScores->total_economy ?? 0,
                        'research' => $memberScores->total_research ?? 0,
                        'military' => $memberScores->total_military ?? 0,
                    ]
                );

                $bar->advance();
            }
        });

        $bar->finish();
        $this->info("\nAlliance highscores generated successfully!");
    }
}
