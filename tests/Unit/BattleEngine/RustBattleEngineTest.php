<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;

class RustBattleEngineTest extends BattleEngineTestAbstract
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
        return new RustBattleEngine(
            $attackerFleet,
            $this->playerService,
            $this->planetService,
            $this->settingsService
        );
    }

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the planet and user tech models with empty data to avoid errors.
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);
    }
}
