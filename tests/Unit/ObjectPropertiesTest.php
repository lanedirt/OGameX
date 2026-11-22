<?php

namespace Tests\Unit;

use Exception;
use OGame\Services\ObjectService;
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

        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(4000, $lightFighter->properties->structural_integrity->rawValue);
        $this->assertEquals(4400, $lightFighter->properties->structural_integrity->calculate($this->playerService)->totalValue);
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

        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(10, $lightFighter->properties->shield->rawValue);
        $this->assertEquals(13, $lightFighter->properties->shield->calculate($this->playerService)->totalValue);
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

        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(50, $lightFighter->properties->attack->rawValue);
        $this->assertEquals(60, $lightFighter->properties->attack->calculate($this->playerService)->totalValue);
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
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(12500, $lightFighter->properties->speed->rawValue);
        $this->assertEquals(18750, $lightFighter->properties->speed->calculate($this->playerService)->totalValue);

        // Small cargo with combustion drive 5
        // Base 5.000 + 5*10% = 7.500
        $smallCargo = ObjectService::getShipObjectByMachineName('small_cargo');
        $this->assertEquals(5000, $smallCargo->properties->speed->rawValue);
        $this->assertEquals(7500, $smallCargo->properties->speed->calculate($this->playerService)->totalValue);

        // Recycler with combustion drive 5
        // Base 2.000 + 5*10% = 3.000
        $recycler = ObjectService::getShipObjectByMachineName('recycler');
        $this->assertEquals(2000, $recycler->properties->speed->rawValue);
        $this->assertEquals(3000, $recycler->properties->speed->calculate($this->playerService)->totalValue);

        // Cruiser with impulse drive level 3
        // Base 15.000 + 3*20% = 24.000
        $cruiser = ObjectService::getShipObjectByMachineName('cruiser');
        $this->assertEquals(15000, $cruiser->properties->speed->rawValue);
        $this->assertEquals(24000, $cruiser->properties->speed->calculate($this->playerService)->totalValue);

        // Battleship with hyperspace drive level 4
        // Base 10.000 + 4*30% = 22.000
        $battleship = ObjectService::getShipObjectByMachineName('battle_ship');
        $this->assertEquals(10000, $battleship->properties->speed->rawValue);
        $this->assertEquals(22000, $battleship->properties->speed->calculate($this->playerService)->totalValue);
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
        // Base upgrades to 10,000 at impulse level 5, then: 10,000 + 5*20% = 20,000
        $smallCargo = ObjectService::getShipObjectByMachineName('small_cargo');
        $this->assertEquals(20000, $smallCargo->properties->speed->calculate($this->playerService)->totalValue);

        // Recycler with hyperspace drive level 15
        // Base upgrades to 6,000 at hyperspace level 15, then: 6,000 + 15*30% = 33,000
        $recycler = ObjectService::getShipObjectByMachineName('recycler');
        $this->assertEquals(33000, $recycler->properties->speed->calculate($this->playerService)->totalValue);

        // Bomber with hyperspace drive level 15
        // Base 5.000 (upgraded at level 8) + 15*30% = 27.500
        $bomber = ObjectService::getShipObjectByMachineName('bomber');
        $this->assertEquals(27500, $bomber->properties->speed->calculate($this->playerService)->totalValue);
    }

    /**
     * Test that Bomber speed upgrade works correctly at hyperspace drive level 8.
     * @throws Exception
     */
    public function testBomberSpeedUpgradeAtHyperspaceDrive8(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'hyperspace_drive' => 8,
        ]);

        // Bomber with hyperspace drive level 8 (exact upgrade threshold)
        // Base should be upgraded from 4.000 to 5.000 at level 8
        // Speed: 5.000 + 8*30% = 17.000 (actual calculation)
        $bomber = ObjectService::getShipObjectByMachineName('bomber');
        $speed = $bomber->properties->speed->calculate($this->playerService);
        $this->assertEquals(5000, $speed->rawValue); // Base speed should be upgraded
        $this->assertEquals(17000, $speed->totalValue); // Total speed with bonus
    }

    /**
     * Test that Bomber speed does NOT upgrade before hyperspace drive level 8.
     * @throws Exception
     */
    public function testBomberSpeedBeforeHyperspaceDrive8(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([
            'hyperspace_drive' => 7,
        ]);

        // Bomber with hyperspace drive level 7 (before upgrade threshold)
        // Base should remain 4.000, uses impulse drive bonus: 20% per level
        // Speed: 4.000 + 7*20% = 4.000 (actual calculation - no bonus applied)
        $bomber = ObjectService::getShipObjectByMachineName('bomber');
        $speed = $bomber->properties->speed->calculate($this->playerService);
        $this->assertEquals(4000, $speed->rawValue); // Base speed should NOT be upgraded
        $this->assertEquals(4000, $speed->totalValue); // Total speed with impulse drive bonus
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
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(50, $lightFighter->properties->capacity->calculate($this->playerService)->totalValue);
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
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $this->assertEquals(20, $lightFighter->properties->fuel->calculate($this->playerService)->totalValue);
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
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
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
        $lightFighter = ObjectService::getShipObjectByMachineName('light_fighter');
        $calculated = $lightFighter->properties->structural_integrity->calculate($this->playerService);

        $this->assertEquals(4000, $calculated->rawValue);
        $this->assertEquals(400, $calculated->bonusValue);
        $this->assertEquals(4400, $calculated->totalValue);
    }

    /**
     * Test fuel capacity: defaults to cargo capacity for backward compatibility,
     * but can be set independently (e.g., espionage probe).
     *
     * @throws Exception
     */
    public function testFuelCapacityProperty(): void
    {
        $this->createAndSetPlanetModel([]);
        $this->createAndSetUserTechModel([]);

        // Backward compatibility: fuel_capacity defaults to capacity
        $smallCargo = ObjectService::getShipObjectByMachineName('small_cargo');
        $this->assertEquals(5000, $smallCargo->properties->capacity->rawValue);
        $this->assertEquals(5000, $smallCargo->properties->fuel_capacity->rawValue);

        // Independent values: espionage probe has cargo=0, fuel=5
        $espionageProbe = ObjectService::getShipObjectByMachineName('espionage_probe');
        $this->assertEquals(0, $espionageProbe->properties->capacity->rawValue);
        $this->assertEquals(5, $espionageProbe->properties->fuel_capacity->rawValue);
    }
}
