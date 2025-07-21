<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionFailed6 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failed_6';
    }
}
