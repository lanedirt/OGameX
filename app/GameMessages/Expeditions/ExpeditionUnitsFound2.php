<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionUnitsFound2 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_units_found_2';
    }
}
