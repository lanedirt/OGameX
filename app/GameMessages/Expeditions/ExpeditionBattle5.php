<?php

namespace OGame\GameMessages\Expeditions;

use OGame\GameMessages\Expeditions\Abstracts\ExpeditionGameMessage;

class ExpeditionBattle5 extends ExpeditionGameMessage
{
    protected function initialize(): void
    {
        $this->key = 'expedition_battle_5';
    }
}
