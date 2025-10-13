<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class DefenderEspionageDetected extends GameMessage
{
    /**
     * We only define the key, params, tab/subtab.
     * Rendering (subject/body/from) will come from resources/lang/*/t_messages.php.
     */
    protected function initialize(): void
    {
        $this->key    = 'espionage_detected';
        // Matches the placeholders in the translation body below.
        $this->params = ['planet', 'coords', 'attacker_name', 'chance'];
        $this->tab    = 'fleets';
        $this->subtab = 'espionage';
    }
}
