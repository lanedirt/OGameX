<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionUnitsFound1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_units_found_1';
    }
}
