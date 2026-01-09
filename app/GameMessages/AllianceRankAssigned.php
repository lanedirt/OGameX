<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceRankAssigned extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_rank_assigned';
        $this->params = ['rank_name'];
        $this->tab = 'alliance';
        $this->subtab = 'management';
    }
}
