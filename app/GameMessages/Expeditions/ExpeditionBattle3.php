<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionBattle3 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_battle_3';
    }
}
