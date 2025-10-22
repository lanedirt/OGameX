<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

/**
 * Message sent to defender after being attacked by missiles
 */
class MissileDefenseReport extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'missile_defense_report';
        $this->params = ['attacker_name', 'planet_coords', 'missiles_incoming', 'missiles_intercepted', 'missiles_hit', 'defenses_destroyed'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
