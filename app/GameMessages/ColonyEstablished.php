<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ColonyEstablished extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'colony_established';
        $this->params = ['coordinates'];
        $this->tab = 'economy';
        $this->subtab = 'economy';
    }
}
