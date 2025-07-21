<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionUnitsFoundGameMessage;

class ExpeditionUnitsFound2 extends ExpeditionUnitsFoundGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_units_found_2';
    }
}
