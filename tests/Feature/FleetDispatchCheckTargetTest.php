<?php

namespace Tests\Feature;

use Tests\FleetDispatchTestCase;

/**
 * Test FleetController dispatchCheckTarget API response.
 */
class FleetDispatchCheckTargetTest extends FleetDispatchTestCase
{
    /**
     * Prepare the planet for the test.
     */
    protected function basicSetup(): void
    {
        $this->planetSetObjectLevel('shipyard', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('espionage_probe', 3);
    }

    /**
     * Test API returns both cargo and fuel capacity for all ships.
     */
    public function testDispatchCheckTargetCapacityFields(): void
    {
        $this->basicSetup();

        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => 1,
            'system' => 1,
            'position' => 5,
            'type' => 1,
        ]);

        $response->assertStatus(200);
        $data = $response->json();

        // All ships must have both capacity fields
        foreach ($data['shipsData'] as $shipId => $shipData) {
            $this->assertArrayHasKey('baseFuelCapacity', $shipData);
            $this->assertArrayHasKey('baseCargoCapacity', $shipData);
        }

        // Small cargo: equal capacities (backward compatibility)
        $this->assertEquals(5000, $data['shipsData'][202]['baseCargoCapacity']);
        $this->assertEquals(5000, $data['shipsData'][202]['baseFuelCapacity']);

        // Espionage probe: cargo=0, fuel=5
        $this->assertEquals(0, $data['shipsData'][210]['baseCargoCapacity']);
        $this->assertEquals(5, $data['shipsData'][210]['baseFuelCapacity']);
    }
}
