<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

class PlanetTest extends AccountTestCase
{
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
