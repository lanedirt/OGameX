<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionFailed8 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failed_8';
    }
}
