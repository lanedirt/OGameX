<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceApplicationAccepted extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_application_accepted';
        $this->params = ['alliance_name', 'alliance_tag'];
        $this->tab = 'alliance';
        $this->subtab = 'membership';
    }
}
