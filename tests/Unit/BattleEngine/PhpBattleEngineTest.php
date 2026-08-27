<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;

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
        // Create defenders array with planet's stationary forces
        $defenders = [\OGame\GameMissions\BattleEngine\Models\DefenderFleet::fromPlanet($this->planetService)];

        // Convert UnitCollection to AttackerFleet for the new multi-attacker architecture
        $attacker = new AttackerFleet();
        $attacker->units = $attackerFleet;
        $attacker->player = $this->playerService;
        $attacker->fleetMissionId = 0; // 0 for test battles without a real fleet mission
        $attacker->ownerId = $this->playerService->getId();
        $attacker->cargoResources = new Resources(0, 0, 0, 0);
        $attacker->isInitiator = true;
        $attacker->fleetMission = null;

        return new PhpBattleEngine(
            [$attacker],
            $this->planetService,
            $defenders,
            $this->settingsService
        );
    }
}
