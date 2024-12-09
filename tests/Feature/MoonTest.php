<?php

namespace Tests\Feature;

use Tests\MoonTestCase;
use OGame\Models\Resources;

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
}
