<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

/**
 * Message sent to attacker after launching missiles
 */
class MissileAttackReport extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'missile_attack_report';
        $this->params = ['target_coords', 'missiles_sent', 'missiles_intercepted', 'missiles_hit', 'defenses_destroyed'];
        $this->tab = 'fleets';
        $this->subtab = 'combat_reports';
    }
}
