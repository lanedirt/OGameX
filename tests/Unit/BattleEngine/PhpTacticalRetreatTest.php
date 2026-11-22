<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;

/**
 * PHP battle-engine coverage for tactical retreat.
 */
class PhpTacticalRetreatTest extends TacticalRetreatTestAbstract
{
    /**
     * @return class-string<BattleEngine>
     */
    protected function battleEngineClass(): string
    {
        return PhpBattleEngine::class;
    }
}
