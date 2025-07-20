<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Abstracts\ExpeditionGameMessage;

class ExpeditionBattle4 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_battle_4';
    }
}
