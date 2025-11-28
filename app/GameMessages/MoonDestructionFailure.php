<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class MoonDestructionFailure extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'moon_destruction_failure';
        $this->params = ['moon_name', 'moon_coords', 'destruction_chance', 'loss_chance'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
