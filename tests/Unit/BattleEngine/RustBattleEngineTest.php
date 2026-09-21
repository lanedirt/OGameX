<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\RustBattleEngine;

class RustBattleEngineTest extends BattleEngineTestAbstract
{
    /**
     * Create a new BattleEngine instance. This allows the test class itself to change the BattleEngine
     * that is used for the actual tests defined in the abstract test class.
     *
     * @inheritdoc
     */
    protected function createBattleEngineForAttackers(array $attackers): BattleEngine
    {
        // Create defenders array with planet's stationary forces
        $defenders = [DefenderFleet::fromPlanet($this->planetService)];

        return new RustBattleEngine(
            $attackers,
            $this->planetService,
            $defenders,
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
