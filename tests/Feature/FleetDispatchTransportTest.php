<?php

namespace Feature;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Message;
use OGame\Models\Resources;
use Tests\FleetDispatchSelfTestCase;

/**
 * Test that fleet dispatch works as expected.
 */
class FleetDispatchTransportTest extends FleetDispatchSelfTestCase
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

    /**
     * @throws BindingResolutionException
     */
    public function testDispatchFleetToOtherPlayer(): void
    {
        $this->basicSetup();

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Send fleet to a planet of another player.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit($this->planetService->objects->getUnitObjectByMachineName('small_cargo'), 1);
        $foreignPlanet = $this->sendMissionToOtherPlayer($unitCollection, new Resources(100, 0, 0, 0));

        // Increase time by 10 hours to ensure the mission is done.
        Carbon::setTestNow($startTime->copy()->addHours(10));

        // Do a request to trigger the update logic.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Assert that last message sent to second player contains the transport confirm message.
        $lastMessage = Message::where('user_id', $foreignPlanet->getPlayer()->getId())
            ->orderBy('id', 'desc')
            ->first();

        $this->assertStringContainsString('An incoming fleet from planet', $lastMessage->body);
        $this->assertStringContainsString('has reached your planet', $lastMessage->body);
    }
}
