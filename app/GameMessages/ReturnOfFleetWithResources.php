<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ReturnOfFleetWithResources extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'return_of_fleet_with_resources';
        $this->params = ['from', 'to', 'metal', 'crystal', 'deuterium'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
