<?php

namespace Tests\Unit;

use OGame\Services\PlayerService;
use Tests\UnitTestCase;

/**
 * Unit tests for MissileMission logic.
 *
 * NOTE: These tests are simplified to test core calculations without full mission setup.
 */
class MissileMissionTest extends UnitTestCase
{
    public function testMissileRangeCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        // Level 1: (1 × 5) - 1 = 4 systems
        $this->playerService->setResearchLevel('impulse_drive', 1, false);
        $this->assertEquals(4, $this->playerService->getMissileRange());

        // Level 5: (5 × 5) - 1 = 24 systems
        $this->playerService->setResearchLevel('impulse_drive', 5, false);
        $this->assertEquals(24, $this->playerService->getMissileRange());

        // Level 10: (10 × 5) - 1 = 49 systems
        $this->playerService->setResearchLevel('impulse_drive', 10, false);
        $this->assertEquals(49, $this->playerService->getMissileRange());
    }

    public function testPlayerHasMissileRangeMethod(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        // Verify the method exists and returns a number
        $this->playerService->setResearchLevel('impulse_drive', 1, false);
        $range = $this->playerService->getMissileRange();

        $this->assertIsInt($range);
        $this->assertGreaterThan(0, $range);
    }

}
