<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionBattle6 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_battle_6';
    }
}
