<?php

namespace OGame\Observers;

use Cache;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Highscore;
use OGame\Models\UserTech;
use OGame\Services\HighscoreService;

class UserTechObserver
{
    /**
     * Handle the User tech "created" event.
     */
    public function created(UserTech $userTech): void
    {
        Highscore::updateOrCreate(['player_id' => $userTech->user->id], [
            'general' => 0,
            'economy' => 0,
            'research' => 0,
            'military' => 0,
            'general_rank' => Highscore::max('general_rank') + 1,
            'economy_rank' => Highscore::max('economy_rank') + 1,
            'research_rank' => Highscore::max('research_rank') + 1,
            'military_rank' => Highscore::max('military_rank') + 1,
        ]);

        // Clear highscore caches.
        Cache::forget('highscore-player-count');

        // Clear specific page cache where the new user score will be.
        $highscoreService = resolve(HighscoreService::class);
        $playerServiceFactory = resolve(PlayerServiceFactory::class);

        $playerService = $playerServiceFactory->make($userTech->user->id);

        foreach (HighscoreTypeEnum::cases() as $type) {
            $highscoreService->setHighscoreType($type->value);

            $currentPlayerRank = $highscoreService->getHighscorePlayerRank($playerService);

            $page = floor($currentPlayerRank / 100) + 1;

            Cache::forget(sprintf('highscores-%s-%d', $type->name, $page));
        }
    }
}
