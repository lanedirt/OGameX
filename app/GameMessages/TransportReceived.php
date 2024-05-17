<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class TransportReceived extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'transport_received';
        $this->params = ['from', 'to', 'metal', 'crystal', 'deuterium'];
        $this->tab = 'fleets';
        $this->subtab = 'other';
    }
}
