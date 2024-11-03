<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class ObjectServiceTest extends UnitTestCase
{
    /**
     * Tests maximum building amount returns correct value.
     */
    public function testGetObjectMaxBuildAmount(): void
    {
        $object_service = new ObjectService();
        $this->createAndSetPlanetModel([]);

        // Test with requirements not met
        $max_build_amount = $object_service->getObjectMaxBuildAmount('plasma_turret', $this->planetService, false);
        $this->assertEquals(0, $max_build_amount);

        // Test with object limited to one instance per user
        $max_build_amount = $object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(1, $max_build_amount);

        $this->createAndSetPlanetModel([
            'small_shield_dome' => 1,
        ]);

        // Test with object limited to one instance which already exists
        $max_build_amount = $object_service->getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(0, $max_build_amount);

        $this->createAndSetPlanetModel([
            'metal' => 24000,
            'crystal' => 6000
        ]);

        // Test it calculates max amount correctly
        $max_build_amount = $object_service->getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $max_build_amount);
    }
}
