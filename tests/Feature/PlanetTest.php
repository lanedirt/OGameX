<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

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
}
