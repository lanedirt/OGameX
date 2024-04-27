<?php

namespace Feature;

use Tests\FleetDispatchTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchTransportTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 3;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Transport';

    protected bool $hasReturnMission = true;

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'transport', [
            'reaches the planet',
            'Metal: 100',
            'Crystal: 100',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);
    }

    protected function messageCheckMissionReturn(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            'The fleet doesn\'t deliver goods',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);
    }
}
