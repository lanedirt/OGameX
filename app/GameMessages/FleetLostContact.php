<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class FleetLostContact extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'fleet_lost_contact';
        $this->params = ['coordinates'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
