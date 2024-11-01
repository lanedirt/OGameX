<?php

namespace Tests\Unit;

use Exception;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Class UnitCollectionTest
 * @package Tests\Unit
 *
 * Test class for unit collections.
 */
class UnitCollectionTest extends UnitTestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpPlanetService();
    }

    /**
     * Test that the slowest unit speed is calculated correctly.
     * @throws Exception
     */
    public function testSlowestFleetSpeed(): void
    {
        $this->createAndSetPlanetModel([
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
        ]);
        $this->createAndSetUserTechModel([
            'hyperspace_drive' => 1,
            'combustion_drive' => 20, // This will make small cargo faster than destroyer.
        ]);

        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), 10);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('destroyer'), 3);

        // Slowest ship should be the destroyer.
        // - 5.000 = destroyer base speed
        // - 1.500 = 30% speed bonus from hyperspace drive level 1
        // =  6.500 total expected speed.
        $this->assertEquals(6500, $unitCollection->getSlowestUnitSpeed($this->playerService));
    }
}
