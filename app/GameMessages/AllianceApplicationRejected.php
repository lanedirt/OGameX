<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceApplicationRejected extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_application_rejected';
        $this->params = ['alliance_name', 'alliance_tag'];
        $this->tab = 'alliance';
        $this->subtab = 'membership';
    }
}
