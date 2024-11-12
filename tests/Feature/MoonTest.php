<?php

namespace Tests\Feature;

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
}
