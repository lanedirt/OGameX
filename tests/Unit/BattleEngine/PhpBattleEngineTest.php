<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Test class for the PHP BattleEngine. The actual tests that create the simulated battles
 * are defined in the abstract test class.
 */
class PhpBattleEngineTest extends BattleEngineTestAbstract
{
    /**
     * Create a new BattleEngine instance. This allows the test class itself to change the BattleEngine
     * that is used for the actual tests defined in the abstract test class.
     *
     * @param UnitCollection $attackerFleet
     * @return BattleEngine
     */
    protected function createBattleEngine(UnitCollection $attackerFleet): BattleEngine
    {
        return new PhpBattleEngine(
            $attackerFleet,
            $this->playerService,
            $this->planetService,
            $this->settingsService
        );
    }
}
