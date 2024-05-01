<?php

namespace Feature;

use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Resources;
use Tests\FleetDispatchSelfTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchDeployTest extends FleetDispatchSelfTestCase
{
    /**
     * @var int The mission type for the test.
     */
    protected int $missionType = 4;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Deployment';

    protected bool $hasReturnMission = false;

    protected function messageCheckMissionArrival(): void
    {
        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'One of your fleets from',
            'has reached',
            'Metal: 100',
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

    public function testDispatchFleetReturnTripWithoutResources(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to the second planet of the test user WITHOUT resources.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $this->sendMissionToSecondPlanet($unitCollection, new Resources(0, 0, 0, 0));

        // Set all messages as read to avoid unread messages count in the overview.
        $this->playerSetAllMessagesRead();

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that message has been sent to player and contains the correct information.
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'has reached',
            'The fleet doesn`t deliver goods.',
            $this->planetService->getPlanetName(),
            $this->secondPlanetService->getPlanetName()
        ]);
    }
}
