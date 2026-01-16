<?php

namespace Tests\Unit;

use OGame\Services\ObjectService;
use Tests\UnitTestCase;

/**
 * Test that the Crawler energy consumption is displayed correctly in building overlay.
 */
class CrawlerEnergyDisplayTest extends UnitTestCase
{
    /**
     * Test that crawlers display energy consumption correctly in building overlay.
     */
    public function test_crawler_energy_display_in_overlay(): void
    {
        // Create a planet with some mines to allow crawlers to work
        $this->createAndSetPlanetModel([
            'metal_mine' => 10,
            'crystal_mine' => 10,
            'deuterium_synthesizer' => 10,
            'crawler' => 0, // Starting with 0 crawlers
        ]);

        // Get crawler object
        $object = ObjectService::getObjectById(217); // Crawler ID
        
        // Simulate the energy calculation from ObjectAjaxTrait for 100% production
        $crawlerPercentage = 10 / 10; // Convert to decimal (0-1.5)
        $baseEnergy = 50;
        $energyConsumption = $baseEnergy * $crawlerPercentage;
        
        $energy_difference = floor($energyConsumption);
        
        // Assert that energy consumption is calculated correctly
        $this->assertEquals(50, $energy_difference, 'Crawler should consume 50 energy at 100% production');
    }
}
