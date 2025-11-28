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

    /**
     * Property test: Total cargo capacity equals sum of individual ship capacities.
     */
    public function testPropertyCargoCapacityFromAllShips(): void
    {
        // Run 100 iterations with randomized fleet compositions
        $iterations = 100;
        $seed = time();
        mt_srand($seed);

        // All ship types with their known capacities
        $shipTypes = [
            'small_cargo' => 5000,
            'large_cargo' => 25000,
            'light_fighter' => 50,
            'heavy_fighter' => 100,
            'cruiser' => 800,
            'battle_ship' => 1500,
            'battlecruiser' => 750,
            'bomber' => 500,
            'destroyer' => 2000,
            'deathstar' => 1000000,
            'colony_ship' => 7500,
            'recycler' => 20000,
            'espionage_probe' => 5,
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Create fresh planet and player for each iteration
            $this->createAndSetPlanetModel([]);
            $this->createAndSetUserTechModel([]);

            // Generate random fleet composition (1-5 different ship types)
            $numShipTypes = mt_rand(1, 5);
            $selectedShips = array_rand($shipTypes, $numShipTypes);
            if (!is_array($selectedShips)) {
                $selectedShips = [$selectedShips];
            }

            $unitCollection = new UnitCollection();
            $expectedCapacity = 0;

            foreach ($selectedShips as $shipType) {
                // Random amount between 1 and 100
                $amount = mt_rand(1, 100);

                $unitCollection->addUnit(
                    ObjectService::getShipObjectByMachineName($shipType),
                    $amount
                );

                // Calculate expected capacity manually
                $expectedCapacity += $shipTypes[$shipType] * $amount;
            }

            // Get actual capacity from method
            $actualCapacity = $unitCollection->getTotalCargoCapacity($this->playerService);

            // Property: Total capacity should equal sum of individual capacities
            $this->assertEquals(
                $expectedCapacity,
                $actualCapacity,
                "Iteration $i (seed: $seed): Cargo capacity mismatch. Expected: $expectedCapacity, Got: $actualCapacity"
            );
        }
    }

    /**
     * Test mixed fleet capacity calculation.
     */
    public function testMixedFleetCargoCapacity(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        $unitCollection = new UnitCollection();

        // Add cargo ships
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), 10);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('large_cargo'), 5);

        // Add combat ships
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 20);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 8);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('battle_ship'), 3);

        // Expected capacity:
        // Small cargo: 10 * 5,000 = 50,000
        // Large cargo: 5 * 25,000 = 125,000
        // Light fighter: 20 * 50 = 1,000
        // Cruiser: 8 * 800 = 6,400
        // Battleship: 3 * 1,500 = 4,500
        // Total: 186,900
        $expectedCapacity = 186900;

        $actualCapacity = $unitCollection->getTotalCargoCapacity($this->playerService);

        $this->assertEquals($expectedCapacity, $actualCapacity, 'Mixed fleet capacity calculation incorrect');
    }

    /**
     * Test combat ships only capacity.
     */
    public function testCombatShipsOnlyCargoCapacity(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        $unitCollection = new UnitCollection();

        // Add only combat ships
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 50);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('heavy_fighter'), 30);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 15);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('battlecruiser'), 10);

        // Expected capacity:
        // Light fighter: 50 * 50 = 2,500
        // Heavy fighter: 30 * 100 = 3,000
        // Cruiser: 15 * 800 = 12,000
        // Battlecruiser: 10 * 750 = 7,500
        // Total: 25,000
        $expectedCapacity = 25000;

        $actualCapacity = $unitCollection->getTotalCargoCapacity($this->playerService);

        $this->assertEquals($expectedCapacity, $actualCapacity, 'Combat-only fleet capacity calculation incorrect');
    }

    /**
     * Test cargo ships only capacity.
     */
    public function testCargoShipsOnlyCargoCapacity(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        $unitCollection = new UnitCollection();

        // Add only cargo ships
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), 25);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('large_cargo'), 15);
        $unitCollection->addUnit(ObjectService::getShipObjectByMachineName('recycler'), 5);

        // Expected capacity:
        // Small cargo: 25 * 5,000 = 125,000
        // Large cargo: 15 * 25,000 = 375,000
        // Recycler: 5 * 20,000 = 100,000
        // Total: 600,000
        $expectedCapacity = 600000;

        $actualCapacity = $unitCollection->getTotalCargoCapacity($this->playerService);

        $this->assertEquals($expectedCapacity, $actualCapacity, 'Cargo-only fleet capacity calculation incorrect');
    }
}
