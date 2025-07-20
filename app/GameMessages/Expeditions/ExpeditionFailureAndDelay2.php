<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionFailureAndDelay2 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failure_and_delay_2';
    }
}
