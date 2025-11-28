<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class MoonDestructionSuccess extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'moon_destruction_success';
        $this->params = ['moon_name', 'moon_coords', 'destruction_chance', 'loss_chance'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
