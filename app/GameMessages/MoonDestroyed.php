<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class MoonDestroyed extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'moon_destroyed';
        $this->params = ['moon_name', 'moon_coords', 'attacker_name'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
