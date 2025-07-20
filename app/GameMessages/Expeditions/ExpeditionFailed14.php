<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionFailed14 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_failed_14';
    }
}
