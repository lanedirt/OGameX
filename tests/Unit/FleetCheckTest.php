<?php

namespace Tests\Unit;

use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\UnitTestCase;

class FleetCheckTest extends UnitTestCase
{
    /**
     * Set up common test components.
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpPlanetService();
    }

    /**
     * Mock test for checking positive fleet amount check on a planet.
     */
    public function testFleetAmountCheckPositive(): void
    {
        $this->createAndSetPlanetModel([
            'small_cargo' => 10,
            'destroyer' => 3,
            'espionage_probe' => 2,
        ]);

        // Verify that multiple ships count up to the sum of the ships.
        $this->assertEquals(15, $this->planetService->getFlightShipAmount());
    }

    /**
     * Mock test for checking zero fleet amount check on a planet.
     */
    public function testFleetAmountCheckZero(): void
    {
        $this->createAndSetPlanetModel([
            'solar_satellite' => 3,
        ]);

        // Verify that amount of ships returns 0 as there are no ships that can fly.
        $this->assertEquals(0, $this->planetService->getFlightShipAmount());
    }
}
