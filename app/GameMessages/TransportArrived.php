<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class TransportArrived extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'transport_arrived';
        $this->params = ['from', 'to', 'metal', 'crystal', 'deuterium'];
        $this->tab = 'fleets';
        $this->subtab = 'transport';
    }
}
