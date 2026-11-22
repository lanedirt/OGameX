<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AcsDefendArrivalSender extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'acs_defend_arrival_sender';
        $this->params = ['to'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
