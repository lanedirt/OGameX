<?php

namespace Tests\Unit;

use Tests\UnitTestCase;

/**
 * Test that level-0 mines show required energy in building overlay (#1320).
 */
class MineEnergyAtLevelZeroTest extends UnitTestCase
{
    public function testLevelZeroMineShowsEnergyRequirement(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 0,
            'crystal_mine' => 0,
            'deuterium_synthesizer' => 0,
            'solar_plant' => 1,
        ]);

        $productionCurrent = $this->planetService->getObjectProduction('metal_mine');
        $productionNext = $this->planetService->getObjectProduction('metal_mine', 1);

        $energyDifference = ($productionNext->energy->get() - $productionCurrent->energy->get()) * -1;

        $this->assertGreaterThan(0, $energyDifference, 'Level-0 metal mine should show energy required for level 1');
    }
}
