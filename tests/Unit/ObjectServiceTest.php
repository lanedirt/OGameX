<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class ObjectServiceTest extends UnitTestCase
{
    public function testGetObjectMaxBuildAmount(): void
    {
        $this->createAndSetPlanetModel([]);

        // Test with requirement not met
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('plasma_turret', $this->planetService, false);
        $this->assertEquals(0, $maxBuildAmount);

        // Test with object limited to one instance
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(1, $maxBuildAmount);

        $this->createAndSetPlanetModel([
            'small_shield_dome' => 1,
        ]);

        // Test with object limited to one instance which already exists
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('small_shield_dome', $this->planetService, true);
        $this->assertEquals(0, $maxBuildAmount);

        $this->createAndSetPlanetModel([
            'metal' => 24000,
            'crystal' => 6000
        ]);

        // Test it calculates max amount correctly
        $maxBuildAmount = ObjectService::getObjectMaxBuildAmount('anti_ballistic_missile', $this->planetService, true);
        $this->assertEquals(3, $maxBuildAmount);
    }
}
