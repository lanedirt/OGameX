<?php

namespace OGame\Observers;

use Cache;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Models\Highscore;
use OGame\Models\User;
use OGame\Models\UserTech;

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

        foreach (HighscoreTypeEnum::cases() as $type) {
            $pages = floor(Highscore::count() / 100) + 1;
            for ($page = 1; $page <= $pages; $page++) {
                Cache::forget('highscores'.'-'.$type->name.'-'.$page);
            }
        }
    }
}
