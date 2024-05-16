<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class FleetDeployment extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'fleet_deployment';
        $this->params = ['from', 'to'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
