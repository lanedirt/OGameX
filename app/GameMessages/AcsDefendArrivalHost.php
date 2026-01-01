<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AcsDefendArrivalHost extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'acs_defend_arrival_host';
        $this->params = ['to'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
