<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionBattle2 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_battle_2';
    }
}
