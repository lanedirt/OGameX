<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;

/**
 * Rust battle-engine coverage for tactical retreat.
 */
class RustTacticalRetreatTest extends TacticalRetreatTestAbstract
{
    /**
     * @return class-string<BattleEngine>
     */
    protected function battleEngineClass(): string
    {
        return RustBattleEngine::class;
    }
}
