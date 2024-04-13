<?php

namespace Tests\Unit;

use Exception;
use Tests\UnitTestCase;

class ObjectPropertiesTest extends UnitTestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testStructuralIntegrityPropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'armor_technology' => 1,
        ]);

        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(4000, $lightFighter->properties->structural_integrity->rawValue);
        $this->assertEquals(4400, $lightFighter->properties->structural_integrity->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testShieldPropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'shielding_technology' => 3,
        ]);

        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(10, $lightFighter->properties->shield->rawValue);
        $this->assertEquals(13, $lightFighter->properties->shield->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testAttackPropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'weapon_technology' => 2,
        ]);

        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(50, $lightFighter->properties->attack->rawValue);
        $this->assertEquals(60, $lightFighter->properties->attack->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testSpeedBasePropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'combustion_drive' => 5,
            'impulse_drive' => 3,
            'hyperspace_drive' => 4,
        ]);

        // Light fighter
        // Base 12.500 + 5*10% = 18.750
        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(12500, $lightFighter->properties->speed->rawValue);
        $this->assertEquals(18750, $lightFighter->properties->speed->calculate($this->planetService)->totalValue);

        // Small cargo with combustion drive 5
        // Base 10.000 + 5*10% = 15.000
        $smallCargo = $this->planetService->objects->getShipObjectByMachineName('small_cargo');
        $this->assertEquals(10000, $smallCargo->properties->speed->rawValue);
        $this->assertEquals(15000, $smallCargo->properties->speed->calculate($this->planetService)->totalValue);

        // Recycler with combustion drive 5
        // Base 2.000 + 5*10% = 3.000
        $recycler = $this->planetService->objects->getShipObjectByMachineName('recycler');
        $this->assertEquals(2000, $recycler->properties->speed->rawValue);
        $this->assertEquals(3000, $recycler->properties->speed->calculate($this->planetService)->totalValue);

        // Cruiser with impulse drive level 3
        // Base 15.000 + 3*20% = 24.000
        $cruiser = $this->planetService->objects->getShipObjectByMachineName('cruiser');
        $this->assertEquals(15000, $cruiser->properties->speed->rawValue);
        $this->assertEquals(24000, $cruiser->properties->speed->calculate($this->planetService)->totalValue);

        // Battleship with hyperspace drive level 4
        // Base 10.000 + 4*30% = 22.000
        $battleship = $this->planetService->objects->getShipObjectByMachineName('battle_ship');
        $this->assertEquals(10000, $battleship->properties->speed->rawValue);
        $this->assertEquals(22000, $battleship->properties->speed->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that upgraded speed calculation logic works correctly.
     * @throws Exception
     */
    public function testSpeedUpgradePropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'impulse_drive' => 5,
            'hyperspace_drive' => 15,
        ]);

        // Small cargo with impulse drive level 5 (upgrade)
        // Base 10.000 + 5*20% = 20.000
        $smallCargo = $this->planetService->objects->getShipObjectByMachineName('small_cargo');
        $this->assertEquals(20000, $smallCargo->properties->speed->calculate($this->planetService)->totalValue);

        // Recycler with hyperspace drive level 15
        // Base 2.000 + 15*30% = 11.000
        $recycler = $this->planetService->objects->getShipObjectByMachineName('recycler');
        $this->assertEquals(11000, $recycler->properties->speed->calculate($this->planetService)->totalValue);

        // Bomber with hyperspace drive level 15
        // Base 4.000 + 15*30% = 22.000
        $recycler = $this->planetService->objects->getShipObjectByMachineName('bomber');
        $this->assertEquals(22000, $recycler->properties->speed->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testCapacityPropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);

        // TODO: Implement capacity property calculation per object id if it exists?
        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(50, $lightFighter->properties->capacity->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     * @throws Exception
     */
    public function testFuelPropertyCalculation(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);

        // TODO: Implement fuel property calculation per object id if it exists?
        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(20, $lightFighter->properties->fuel->calculate($this->planetService)->totalValue);
    }

    /**
     * Test that retrieving properties of an object works correctly.
     * @throws Exception
     */
    public function testPropertyRetrieval(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        // Light fighter check
        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $this->assertEquals(4000, $lightFighter->properties->structural_integrity->rawValue);
        $this->assertEquals(10, $lightFighter->properties->shield->rawValue);
        $this->assertEquals(50, $lightFighter->properties->attack->rawValue);
        $this->assertEquals(12500, $lightFighter->properties->speed->rawValue);
        $this->assertEquals(50, $lightFighter->properties->capacity->rawValue);
        $this->assertEquals(20, $lightFighter->properties->fuel->rawValue);
    }

    /**
     * Test that retrieving properties of an object works correctly including bonuses.
     */
    public function testPropertyBonusRetrieval(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);

        // Light fighter check
        $lightFighter = $this->planetService->objects->getShipObjectByMachineName('light_fighter');
        $calculated = $lightFighter->properties->structural_integrity->calculate($this->planetService);

        $this->assertEquals(4000, $calculated->rawValue);
        $this->assertEquals(400, $calculated->bonusValue);
        $this->assertEquals(4400, $calculated->totalValue);
    }
}
