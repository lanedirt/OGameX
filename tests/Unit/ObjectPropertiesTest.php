<?php

namespace Tests\Unit;

use OGame\Models\Planet;
use OGame\Models\UserTech;
use OGame\Services\Objects\Properties\AttackPropertyService;
use OGame\Services\Objects\Properties\CapacityPropertyService;
use OGame\Services\Objects\Properties\FuelPropertyService;
use OGame\Services\Objects\Properties\ShieldPropertyService;
use OGame\Services\Objects\Properties\SpeedPropertyService;
use OGame\Services\Objects\Properties\StructuralIntegrityPropertyService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use PHPUnit\Framework\TestCase;

class ObjectPropertiesTest extends TestCase
{
    protected PlanetService $planetService;
    protected PlayerService $playerService;

    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize empty playerService object
        $this->playerService = app()->make(PlayerService::class, ['player_id' => 0]);
        // Initialize the planet service before each test
        $this->planetService = app()->make(PlanetService::class, ['player' => $this->playerService, 'planet_id' => 0]);
    }

    /**
     * Helper method to create a planet model with preconfigured levels.
     */
    protected function createAndConfigurePlanetModel(array $attributes): void
    {
        // Create fake planet eloquent model with additional attributes
        $planetModelFake = Planet::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->planetService->setPlanet($planetModelFake);
    }

    /**
     * Helper method to create a user tech model with preconfigured levels.
     */
    protected function createAndConfigureUserTechModel(array $attributes): void
    {
        // Create fake user tech eloquent model with additional attributes
        $userTechModelFake = UserTech::factory()->make($attributes);
        // Set the fake model to the planet service
        $this->playerService->setUserTech($userTechModelFake);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testStructuralIntegrityPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'armor_technology' => 1,
        ]);

        $structuralIntegrity = new StructuralIntegrityPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(4400, $structuralIntegrity->calculateProperty(204)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testShieldPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'shielding_technology' => 3,
        ]);

        $shield = new ShieldPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(13, $shield->calculateProperty(204)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testAttackPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'weapon_technology' => 2,
        ]);

        $attack = new AttackPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(60, $attack->calculateProperty(204)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testSpeedBasePropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'combustion_drive' => 5,
            'impulse_drive' => 3,
            'hyperspace_drive' => 4,
        ]);

        // Light fighter
        // Base 12.500 + 5*10% = 18.750
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(18750, $speed->calculateProperty(204)->totalValue);

        // Small cargo with combustion drive 5
        // Base 10.000 + 5*10% = 15.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(15000, $speed->calculateProperty(202)->totalValue);

        // Recycler with combustion drive 5
        // Base 2.000 + 5*10% = 3.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(3000, $speed->calculateProperty(209)->totalValue);

        // Cruiser with impulse drive level 3
        // Base 15.000 + 3*20% = 18.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(24000, $speed->calculateProperty(206)->totalValue);

        // Battleship with hyperspace drive level 4
        // Base 10.000 + 4*30% = 22.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(22000, $speed->calculateProperty(207)->totalValue);
    }

    /**
     * Test that upgraded speed calculation logic works correctly.
     */
    public function testSpeedUpgradePropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'impulse_drive' => 5,
            'hyperspace_drive' => 15,
        ]);

        // Small cargo with impulse drive level 5 (upgrade)
        // Base 10.000 + 5*20% = 20.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(20000, $speed->calculateProperty(202)->totalValue);

        // Recycler with hyperspace drive level 15
        // Base 2.000 + 15*30% = 11.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(11000, $speed->calculateProperty(209)->totalValue);

        // Bomber with hyperspace drive level 15
        // Base 4.000 + 15*30% = 22.000
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(22000, $speed->calculateProperty(211)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testCapacityPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);

        $capacity = new CapacityPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(50, $capacity->calculateProperty(204)->totalValue);
    }

    /**
     * Test that property calculation logic works correctly.
     */
    public function testFuelPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);

        // TODO: Implement fuel property calculation per object id if it exists?
        $capacity = new FuelPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(20, $capacity->calculateProperty(204)->totalValue);
    }

    /**
     * Test that retrieving properties of an object works correctly.
     */
    public function testPropertyRetrieval(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([]);

        // Light fighter check
        $objectProperties = $this->planetService->getObjectProperties(204);
        $this->assertEquals(4000, $objectProperties->structuralIntegrity->totalValue);
        $this->assertEquals(10, $objectProperties->shield->totalValue);
        $this->assertEquals(50, $objectProperties->attack->totalValue);
        $this->assertEquals(12500, $objectProperties->speed->totalValue);
        $this->assertEquals(50, $objectProperties->capacity->totalValue);
        $this->assertEquals(20, $objectProperties->fuel->totalValue);
    }

    /**
     * Test that retrieving properties of an object works correctly including bonuses.
     */
    public function testPropertyBonusRetrieval(): void
    {
        $this->createAndConfigureUserTechModel([
            'armor_technology' => 1,
            'weapons_technology' => 2,
            'shielding_technology' => 3,
        ]);
        $this->createAndConfigurePlanetModel([]);

        // Light fighter check
        $objectProperties = $this->planetService->getObjectProperties(204);
        $this->assertEquals(4000, $objectProperties->structuralIntegrity->rawValue);
        $this->assertEquals(400, $objectProperties->structuralIntegrity->bonusValue);
        $this->assertEquals(4400, $objectProperties->structuralIntegrity->totalValue);
    }
}
