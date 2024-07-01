<?php

namespace OGame\GameMessages;

use OGame\GameMessages\Abstracts\GameMessage;

class ColonyEstablishFailAstrophysics extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'colony_establish_fail_astrophysics';
        $this->params = ['coordinates'];
        $this->tab = 'economy';
        $this->subtab = 'economy';
    }
}
