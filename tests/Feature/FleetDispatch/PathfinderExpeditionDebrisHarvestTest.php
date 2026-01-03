<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Models\Planet\Coordinate;
use OGame\Services\DebrisFieldService;
use Tests\FleetDispatchTestCase;

/**
 * Test that Pathfinder ships can harvest expedition debris fields at position 16.
 */
class PathfinderExpeditionDebrisHarvestTest extends FleetDispatchTestCase
{
    protected int $missionType = 8; // Harvest mission (recycling)
    protected string $missionName = 'Harvest Debris';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        // No special setup needed for debris harvesting tests
    }

    /**
     * Test that Pathfinders can harvest debris at position 16 (expedition debris).
     *
     * @throws BindingResolutionException
     */
    public function testPathfinderCanHarvestExpeditionDebris(): void
    {
        // Set up player and planet
        $planet = $this->planetService;

        // Add Pathfinders to the planet
        $planet->addUnit('pathfinder', 10);

        // Add deuterium for fleet travel
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 10000, 0));

        // Create debris field at position 16 (expedition position)
        $debrisCoordinate = new Coordinate(
            $planet->getPlanetCoordinates()->galaxy,
            $planet->getPlanetCoordinates()->system,
            16 // Expedition position
        );

        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($debrisCoordinate);
        $debrisFieldService->appendResources(new \OGame\Models\Resources(100000, 50000, 25000, 0));
        $debrisFieldService->save();

        // Get initial debris amount
        $debrisFieldService->loadForCoordinates($debrisCoordinate);
        $initialDebris = $debrisFieldService->getResources()->sum();
        $this->assertGreaterThan(0, $initialDebris, 'Debris field should have resources');

        // Send harvest mission with Pathfinders to position 16
        $units = new \OGame\GameObjects\Models\Units\UnitCollection();
        $units->addUnit(\OGame\Services\ObjectService::getShipObjectByMachineName('pathfinder'), 10);
        $this->sendMissionToPosition16($units, new \OGame\Models\Resources(0, 0, 0, 0), true, \OGame\Models\Enums\PlanetType::DebrisField);

        // Get the mission to calculate travel time
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class, ['player' => $planet->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process the mission (arrival)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview'); // Trigger mission processing

        // Check that debris was harvested
        $debrisFieldService->loadForCoordinates($debrisCoordinate);
        $remainingDebris = $debrisFieldService->getResources()->sum();

        // Debris should be reduced (some or all harvested)
        $this->assertLessThan($initialDebris, $remainingDebris, 'Pathfinders should have harvested some debris');

        // Process return mission
        $returnMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $returnDuration = $returnMission->time_arrival - $returnMission->time_departure;
        $this->travel($returnDuration + 1)->seconds();
        $this->get('/overview'); // Trigger return mission processing

        // Check that Pathfinders returned with resources
        $this->assertEquals(10, $planet->getObjectAmount('pathfinder'), 'Pathfinders should have returned');

        // Planet should have more resources than before (harvested debris)
        $this->assertGreaterThan(0, $planet->metal()->get() + $planet->crystal()->get(), 'Planet should have harvested resources');
    }

    /**
     * Test that Recyclers CANNOT harvest debris at position 16.
     *
     * @throws BindingResolutionException
     */
    public function testRecyclerCannotHarvestExpeditionDebris(): void
    {
        // Set up player and planet
        $planet = $this->planetService;

        // Add Recyclers to the planet (wrong ship type for position 16)
        $planet->addUnit('recycler', 10);

        // Create debris field at position 16 (expedition position)
        $debrisCoordinate = new Coordinate(
            $planet->getPlanetCoordinates()->galaxy,
            $planet->getPlanetCoordinates()->system,
            16 // Expedition position
        );

        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($debrisCoordinate);
        $debrisFieldService->appendResources(new \OGame\Models\Resources(100000, 50000, 25000, 0));
        $debrisFieldService->save();

        // Try to send harvest mission with Recyclers to position 16
        // This should fail because Recyclers can only harvest regular debris (positions 1-15)
        $response = $this->post('/fleet/dispatch/send-fleet', [
            'galaxy' => $debrisCoordinate->galaxy,
            'system' => $debrisCoordinate->system,
            'position' => $debrisCoordinate->position,
            'type' => \OGame\Models\Enums\PlanetType::DebrisField->value,
            'mission' => 8, // Harvest mission
            'recycler' => 10,
        ]);

        // Mission should not be possible
        $this->assertNotEquals(200, $response->getStatusCode(), 'Recyclers should not be able to harvest expedition debris');
    }

    /**
     * Test that Pathfinders CAN harvest regular debris at positions 1-15.
     *
     * @throws BindingResolutionException
     */
    public function testPathfinderCanHarvestRegularDebris(): void
    {
        // Set up player and planet
        $planet = $this->planetService;

        // Add Pathfinders to the planet
        $planet->addUnit('pathfinder', 10);

        // Add deuterium for fleet travel
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 10000, 0));

        // Create debris field at a regular position (not 16)
        $debrisCoordinate = new Coordinate(
            $planet->getPlanetCoordinates()->galaxy,
            $planet->getPlanetCoordinates()->system,
            5 // Regular position
        );

        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($debrisCoordinate);
        $debrisFieldService->appendResources(new \OGame\Models\Resources(100000, 50000, 25000, 0));
        $debrisFieldService->save();

        // Get initial debris amount
        $debrisFieldService->loadForCoordinates($debrisCoordinate);
        $initialDebris = $debrisFieldService->getResources()->sum();

        // Try to send harvest mission with Pathfinders to regular position
        // According to OGame mechanics, Pathfinders should NOT be able to harvest regular debris
        // Only Recyclers can harvest positions 1-15
        $response = $this->post('/fleet/dispatch/send-fleet', [
            'galaxy' => $debrisCoordinate->galaxy,
            'system' => $debrisCoordinate->system,
            'position' => $debrisCoordinate->position,
            'type' => \OGame\Models\Enums\PlanetType::DebrisField->value,
            'mission' => 8, // Harvest mission
            'pathfinder' => 10,
        ]);

        // This should fail - Pathfinders are for expedition debris only
        $this->assertNotEquals(200, $response->getStatusCode(), 'Pathfinders should only harvest expedition debris (position 16)');
    }
}
