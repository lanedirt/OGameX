<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionFailureAndSpeedUp1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failure_and_speed_up_1';
    }
}
