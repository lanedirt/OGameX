<?php

namespace Tests\Unit;

use OGame\Models\Enums\ResourceType;
use OGame\Models\Resources;
use Tests\UnitTestCase;

class PlanetServiceTest extends UnitTestCase
{
    /**
     * Set up common test components.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPlanetService();
    }

    public function testGetResources(): void
    {
        $this->createAndSetPlanetModel([
            'metal' => 1000,
            'crystal' => 2000,
            'deuterium' => 3000,
        ]);

        $this->assertEquals(1000, $this->planetService->metal()->get());
        $this->assertEquals(2000, $this->planetService->crystal()->get());
        $this->assertEquals(3000, $this->planetService->deuterium()->get());
        $this->assertEquals(0, $this->planetService->energy()->get());
    }

    /**
     * Test for espionage report getXXXArray() methods.
     */
    public function testGetObjectArrays(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 1,
            'crystal_mine' => 2,
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
            'rocket_launcher' => 1,
        ]);

        // Verify that getBuildingArray() returns the correct array.
        $this->assertEquals([
            'metal_mine' => 1,
            'crystal_mine' => 2,
        ], $this->planetService->getBuildingArray());

        // Verify that getShipArray() returns the correct array.
        $this->assertEquals([
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
        ], $this->planetService->getShipUnits()->toArray());

        // Verify that getDefenseArray() returns the correct array.
        $this->assertEquals([
            'rocket_launcher' => 1,
        ], $this->planetService->getDefenseUnits()->toArray());
    }

    /**
     * Test that deducting too many resources from planet throws an exception.
     */
    public function testDeductTooManyResources(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 1,
        ]);

        // Specify the type of exception you expect to be thrown
        $this->expectException(\Exception::class);

        // Call the method that should throw the exception
        $this->planetService->deductResources(new Resources(9999, 9999, 9999, 0));
    }

    public function testAddValidResourceIndividually(): void
    {
        $this->createAndSetPlanetModel([
            'metal' => 1000,
            'crystal' => 2000,
            'deuterium' => 3000,
        ]);
        foreach (ResourceType::cases() as $validResource) {
            $this->planetService->addResource($validResource, 100, false);
        }
        $this->assertEquals([
            'metal' => 1100,
            'crystal' => 2100,
            'deuterium' => 3100,
        ], [
            'metal' => $this->planetService->metal()->get(),
            'crystal' => $this->planetService->crystal()->get(),
            'deuterium' => $this->planetService->deuterium()->get(),
        ]);
    }

    /**
     * Test that the field max function returns expected values
     */
    public function testGetPlanetFieldMax(): void
    {
        $this->createAndSetPlanetModel([
            'field_max' => 90,
        ]);
        $this->assertEquals(90, $this->planetService->getPlanetFieldMax());

        $this->createAndSetPlanetModel([
            'field_max' => 14,
        ]);
        $this->assertEquals(14, $this->planetService->getPlanetFieldMax());
    }

    /**
     * Test that the field max function with terraformer (for planets) returns expected values
     */
    public function testGetPlanetFieldMaxWithTerraformer(): void
    {
        // Test none divisible by 2-- should only add 5.
        $this->createAndSetPlanetModel([
            'field_max' => 90,
            'terraformer' => 1,
        ]);

        $this->assertEquals(95, $this->planetService->getPlanetFieldMax(), 'Terraformer level 1 should add 5 to the max fields.');

        // Test a divisible of 2, should add 5, and +1 bonus.
        $this->createAndSetPlanetModel([
            'field_max' => 150,
            'terraformer' => 2,
        ]);

        $this->assertEquals(161, $this->planetService->getPlanetFieldMax(), 'Terraformer level 2 should add 11 to the max fields.');

        // Larger divisible
        $this->createAndSetPlanetModel([
            'field_max' => 100,
            'terraformer' => 20,
        ]);

        // each level + 5 max fields - 100 base, plus 20*5 = 200
        // every 2 levels + 1 max field- 20/2 = 10, so 200 + 10 = 210
        $this->assertEquals(210, $this->planetService->getPlanetFieldMax(), 'Terraformer level 20 should add 210 to the max fields.');

        // Ensure if it's not built it doesn't alter the max fields.
        $this->createAndSetPlanetModel([
            'field_max' => 100,
            'terraformer' => 0,
        ]);

        $this->assertEquals(100, $this->planetService->getPlanetFieldMax(), 'Terraformer level 0 should not alter the max fields.');
    }

    /**
     * Test that the field max function with lunar base (for moons) returns expected values
     */
    public function testGetPlanetFieldMaxWithLunarBase(): void
    {
        // Test lunar base level 0 for baseline.
        $this->createAndSetPlanetModel([
            'field_max' => 90,
            'lunar_base' => 0,
        ]);

        $this->assertEquals(0, $this->planetService->getBuildingCount());
        $this->assertEquals(90, $this->planetService->getPlanetFieldMax(), 'Lunar base level 0 should not alter the max fields.');

        // Test lunar base level 1-- should add 3 (lunar base itself takes up one field so 2 bonus).
        $this->createAndSetPlanetModel([
            'field_max' => 90,
            'lunar_base' => 1,
        ]);

        $this->assertEquals(1, $this->planetService->getBuildingCount());
        $this->assertEquals(93, $this->planetService->getPlanetFieldMax(), 'Lunar base level 1 should add 3 to the max fields.');

        // Test lunar base level 2-- should add 6.
        $this->createAndSetPlanetModel([
            'field_max' => 150,
            'lunar_base' => 2,
        ]);

        $this->assertEquals(2, $this->planetService->getBuildingCount());
        $this->assertEquals(156, $this->planetService->getPlanetFieldMax(), 'Lunar base level 2 should add 6 to the max fields.');
    }

    /**
     * Tests building count returns valid buildings, and specified levels.
     */
    public function testGetPlanetBuildingCount(): void
    {
        $this->createAndSetPlanetModel([
            'metal_mine' => 50,
            'crystal_mine' => 20,
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
            'rocket_launcher' => 1,
        ]);

        // Should only return valid buildings, ( ie metal_mine and crystal_mine )
        $this->assertEquals(70, $this->planetService->getBuildingCount());

        // Do another test to ensure sum is correct.
        $this->createAndSetPlanetModel([
            'metal_mine' => 50,
            'crystal_mine' => 50,
            'solar_plant' => 50,
            'destroyer' => 3,
            'espionage_probe' => 2,
            'rocket_launcher' => 44,
        ]);

        // Should only return valid buildings, ( ie metal_mine crystal_mine, solar_plant )
        $this->assertEquals(150, $this->planetService->getBuildingCount());
    }
}
