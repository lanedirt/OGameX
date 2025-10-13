<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class DefenderEspionageDetected extends GameMessage
{
    protected function initialize(): void
    {
        $this->key    = 'espionage_detected';
        $this->params = ['attacker', 'coords']; // coords as "g:s:p"
        $this->tab    = 'fleets';
        $this->subtab = 'espionage';
    }

    public function getFrom(): string
    {
        return 'Fleet Command';
    }

    public function getSubject(): string
    {
        return 'Enemy Espionage Probe Detected';
    }

    public function getBody(): string
    {
        $p = $this->message->params ?? [];
        $attacker = $p['attacker'] ?? 'Unknown';
        $coords   = $p['coords']   ?? '';

        // Wrap raw coords so the BBCode replacer turns them into a link
        $body = "An enemy espionage probe was detected on your planet. Attacker: {$attacker}. Coordinates: [coordinates]{$coords}[/coordinates].";

        // Use built-in placeholder/BBCode replacer
        return $this->replacePlaceholders($body);
    }
}
