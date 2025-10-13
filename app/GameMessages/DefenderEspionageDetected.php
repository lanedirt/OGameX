<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class DefenderEspionageDetected extends GameMessage
{
    protected function initialize(): void
    {
        $this->key    = 'espionage_detected';
        $this->params = ['planet', 'coords', 'attacker_name', 'chance'];
        $this->tab    = 'fleets';
        $this->subtab = 'espionage';
    }
}
