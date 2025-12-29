<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceApplicationReceived extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_application_received';
        $this->params = ['applicant_name', 'application_message'];
        $this->tab = 'communication';
        $this->subtab = 'information';
    }
}
