<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Highscore;
use OGame\Models\User;
use OGame\Services\HighscoreService;

class GenerateHighscore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:generate-highscore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Highscore data';

    /**
     * Execute the console command.
     */
    public function handle(HighscoreService $highscoreService, PlayerServiceFactory $playerServiceFactory): void
    {
        $users = User::query();
        $this->info('Updating highscores...');
        $bar = $this->output->createProgressBar();
        $bar->start($users->count());
        $users->chunkById(200, function ($players) use ($playerServiceFactory, $highscoreService, &$bar) {
            foreach ($players as $player) {
                // @phpstan-ignore-next-line TODO - Is there a better way than this?
                $playerService = $playerServiceFactory->make($player->id);
                // @phpstan-ignore-next-line TODO - Is there a better way than this?
                Highscore::updateOrCreate(['player_id' => $player->id], [
                    'general' => $highscoreService->getPlayerScore($playerService),
                    'economy' => $highscoreService->getPlayerScoreEconomy($playerService),
                    'research' => $highscoreService->getPlayerScoreResearch($playerService),
                    'military' => $highscoreService->getPlayerScoreMilitary($playerService),
                ]);
                $bar->advance();

            }
        });
        $this->info('All highscores completed!');

    }
}
