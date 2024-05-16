<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ReturnOfFleet extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'return_of_fleet';
        $this->params = ['from', 'to'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
