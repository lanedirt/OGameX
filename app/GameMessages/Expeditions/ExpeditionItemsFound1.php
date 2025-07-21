<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionItemsFound1 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_items_found_1';
    }
}
