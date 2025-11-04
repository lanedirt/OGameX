<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

final class DefenderEspionageDetected extends GameMessage
{
    protected function initialize(): void
    {
        // Must match the lang key under t_messages.php
        $this->key    = 'espionage_detected';

        // Keep names aligned with lang placeholders
        // Use :planet (BBCode [planet]{id}[/planet]), :attacker_name, :chance
        $this->params = ['planet', 'attacker_name', 'chance'];

        // Must match how Messages UI groups “Espionage report” messages
        $this->tab    = 'fleets';
        $this->subtab = 'espionage';
    }
}
