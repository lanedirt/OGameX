<?php

namespace OGame\Observers;

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
    }
}
