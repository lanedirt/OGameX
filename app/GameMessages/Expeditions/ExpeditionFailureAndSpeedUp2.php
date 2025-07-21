<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionFailureAndSpeedUp2 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failure_and_speed_up_2';
    }
}
