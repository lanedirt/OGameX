<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class MoonDestructionMissionFailed extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'moon_destruction_mission_failed';
        $this->params = ['coordinates'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
