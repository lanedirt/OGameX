<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class AllianceDisbanded extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'alliance_disbanded';
        $this->params = ['alliance_name', 'alliance_tag'];
        $this->tab = 'alliance';
        $this->subtab = 'management';
    }
}
