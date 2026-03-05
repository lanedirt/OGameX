<?php

namespace Tests\Feature;

use Tests\MoonTestCase;

/**
 * Test shipyard behavior on moons.
 */
class ShipyardMoonTest extends MoonTestCase
{
    /**
     * Check that the shipyard page on a moon doesn't show the crawler.
     */
    public function testCrawlerNotShownOnMoonShipyard(): void
    {
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the crawler (ID 217) is not present in the response
        $content = $response->getContent() ?: '';
        $this->assertStringNotContainsString('data-technology="217"', $content, 'Crawler should not be shown on moon shipyard');
    }

    /**
     * Check that the crawler is shown on planet shipyard (for comparison).
     */
    public function testCrawlerShownOnPlanetShipyard(): void
    {
        // Build requirements for crawler: shipyard 4, combustion_drive 4, armor_technology 4, laser_technology 4
        $this->planetSetObjectLevel('shipyard', 4);
        $this->playerSetResearchLevel('combustion_drive', 4);
        $this->playerSetResearchLevel('armor_technology', 4);
        $this->playerSetResearchLevel('laser_technology', 4);

        // Switch back to the planet
        $response = $this->get('/overview?cp=' . $this->planetService->getPlanetId());
        $response->assertStatus(200);

        // Check shipyard on planet
        $response = $this->get('/shipyard');
        $response->assertStatus(200);

        // Assert that the crawler (ID 217) IS present in the response on planet
        $content = $response->getContent() ?: '';
        $this->assertStringContainsString('data-technology="217"', $content, 'Crawler should be shown on planet shipyard');
    }
}
