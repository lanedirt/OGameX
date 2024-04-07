<?php

namespace Tests\Unit;

use OGame\Planet;
use OGame\Services\Objects\Properties\AttackPropertyService;
use OGame\Services\Objects\Properties\CapacityPropertyService;
use OGame\Services\Objects\Properties\FuelPropertyService;
use OGame\Services\Objects\Properties\Models\ObjectPropertyDetails;
use OGame\Services\Objects\Properties\ShieldPropertyService;
use OGame\Services\Objects\Properties\SpeedPropertyService;
use OGame\Services\Objects\Properties\StructuralIntegrityPropertyService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\UserTech;
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
    public function testSpeedIntegrityPropertyCalculation(): void
    {
        $this->createAndConfigurePlanetModel([]);
        $this->createAndConfigureUserTechModel([
            'combustion_drive' => 5,
        ]);

        // Light fighter
        // Base 12.500 + 50% = 18.750
        $speed = new SpeedPropertyService($this->planetService->objects, $this->planetService);
        $this->assertEquals(18750, $speed->calculateProperty(204)->totalValue);

        // TODO: implement speed checks for all ships according to their base speed and research levels.
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
