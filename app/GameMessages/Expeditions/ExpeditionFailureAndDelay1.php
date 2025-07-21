<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionFailureAndDelay1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failure_and_delay_1';
    }
}
