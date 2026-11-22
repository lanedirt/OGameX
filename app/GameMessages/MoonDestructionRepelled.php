<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class MoonDestructionRepelled extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'moon_destruction_repelled';
        $this->params = ['moon_name', 'moon_coords', 'attacker_name', 'destruction_chance', 'loss_chance'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
