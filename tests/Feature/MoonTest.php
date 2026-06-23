<?php

namespace Tests\Feature;

use OGame\Models\Resources;
use Tests\MoonTestCase;

class MoonTest extends MoonTestCase
{
    /**
     * Check that a moon has no resource base production.
     */
    public function testMoonHasNoBaseResourceProduction(): void
    {
        // Assert that moon has no resource production.
        $this->assertEquals(0, $this->moonService->getMetalProductionPerHour());
        $this->assertEquals(0, $this->moonService->getCrystalProductionPerHour());
        $this->assertEquals(0, $this->moonService->getDeuteriumProductionPerHour());
        $this->assertEquals(0, $this->moonService->energyProduction()->get());
    }

    /**
     * Check that a moon has no starting resources upon creation.
     */
    public function testMoonHasNoStartingResources(): void
    {
        // Assert that moon has no resource production.
        $this->assertEquals(0, $this->moonService->metal()->get());
        $this->assertEquals(0, $this->moonService->crystal()->get());
        $this->assertEquals(0, $this->moonService->deuterium()->get());
    }

    /**
     * Check that building a metal mine on a moon fails as that building can only be built on a planet.
     */
    public function testMoonCannotBuildMetalMine(): void
    {
        // Give moon enough resources
        $this->moonService->addResources(new Resources(1000, 1000, 1000, 0));

        // Try to build metal mine
        $this->addResourceBuildRequest('metal_mine', true);

        // Assert that metal mine is not built after 1 hour
        $this->travel(1)->hours();
        $response = $this->get('/resources');
        $this->assertObjectLevelOnPage($response, 'metal_mine', 0, 'Metal mine is built on moon while it can only be built on a planet.');
    }

    /**
     * Check that the crawler is not shown on the moon resources page (it is a planet-only unit).
     */
    public function testCrawlerNotShownOnMoonResources(): void
    {
        $response = $this->get('/resources');
        $response->assertStatus(200);

        $content = $response->getContent() ?: '';
        $this->assertStringNotContainsString('data-technology="217"', $content, 'Crawler should not be shown on moon resources page');
    }

    /**
     * Check that the crawler is shown on the planet resources page (for comparison).
     */
    public function testCrawlerShownOnPlanetResources(): void
    {
        // Switch back to the planet
        $response = $this->get('/overview?cp=' . $this->planetService->getPlanetId());
        $response->assertStatus(200);

        $response = $this->get('/resources');
        $response->assertStatus(200);

        $content = $response->getContent() ?: '';
        $this->assertStringContainsString('data-technology="217"', $content, 'Crawler should be shown on planet resources page');
    }
}
