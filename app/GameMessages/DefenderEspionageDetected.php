<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

final class DefenderEspionageDetected extends GameMessage
{
    protected function initialize(): void
    {
        // Must match the lang key under t_messages.php
        $this->key    = 'espionage';

        // Keep names aligned with lang placeholders
        // Use :planet (BBCode [planet]{id}[/planet]), :attacker_name, :chance
        $this->params = ['planet', 'defender', 'attacker_name', 'chance'];

        // Must match how Messages UI groups "Espionage report" messages
        $this->tab    = 'fleets';
        $this->subtab = 'espionage';
    }

    public function getSubject(): string
    {
        return __('messages.espionage.defender_warning_subject', [
            'planet' => $this->message->params['planet'] ?? ''
        ]);
    }

    public function getBody(): string
    {
        return __('messages.espionage.defender_warning_body', [
            'planet' => $this->message->params['planet'] ?? '',
            'defender' => $this->message->params['defender'] ?? '',
            'attacker_name' => $this->message->params['attacker_name'] ?? '',
            'chance' => $this->message->params['chance'] ?? 0,
        ]);
    }

    public function getFrom(): string
    {
        return __('messages.espionage.defender_warning_from');
    }
}
