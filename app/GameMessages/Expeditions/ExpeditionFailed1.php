<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\GameMessage;

class ExpeditionFailed1 extends GameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failed_1';
        $this->params = ['message'];
        $this->tab = 'fleets';
        $this->subtab = 'expeditions';
    }
}
