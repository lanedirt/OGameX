<?php

namespace Tests\Feature;

use Tests\AccountTestCase;
use OGame\Models\Resources;

class PlanetTest extends AccountTestCase
{
    /**
     * Check that a planet has default (base) production.
     */
    public function testPlanetHasBaseResourceProduction(): void
    {
        // Reload the planet to ensure it has the latest data.
        $this->planetService->reloadPlanet();

        // Assert that a planet has default production for metal and crystal.
        $this->assertGreaterThan(0, $this->planetService->getMetalProductionPerHour());
        $this->assertGreaterThan(0, $this->planetService->getCrystalProductionPerHour());

        // Assert that a planet has default production for deuterium of 0.
        $this->assertEquals(0, $this->planetService->getDeuteriumProductionPerHour());

        // Assert that a planet has default production for energy of 0.
        $this->assertEquals(0, $this->planetService->energyProduction()->get());
    }

    /**
     * Check that a planet has starting resources upon creation.
     */
    public function testPlanetHasStartingResources(): void
    {
        // Assert that planet has 500 metal, 500 crystal, and 0 deuterium.
        $this->assertEquals(500, $this->planetService->metal()->get());
        $this->assertEquals(500, $this->planetService->crystal()->get());
        $this->assertEquals(0, $this->planetService->deuterium()->get());
    }

    /**
     * Check that building a lunar base on a planet fails as that building can only be built on a moon.
     */
    public function testPlanetCannotBuildLunarBase(): void
    {
        // Give moon enough resources
        $this->planetService->addResources(new Resources(1000000, 1000000, 1000000, 0));

        // Try to build lunar base
        try {
            $this->addFacilitiesBuildRequest('lunar_base');
        } catch (\Exception $e) {
            // Expecting an exception to be thrown, continue with checks below that
            // assert the correct state.
        }

        // Assert that lunar base is not built after 24 hours
        $this->travel(24)->hours();
        $response = $this->get('/facilities');
        $response->assertStatus(200);

        // Reload planet to get updated data
        $this->planetService->reloadPlanet();
        $this->assertEquals(0, $this->planetService->getObjectLevel('lunar_base'), 'Lunar base is built on planet while it can only be built on a moon.');
    }
}
