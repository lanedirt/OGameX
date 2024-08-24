<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Models\Resources;
use Tests\UnitTestCase;

class PlanetServiceTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
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
    public function testGetObjectarrays(): void
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
}
