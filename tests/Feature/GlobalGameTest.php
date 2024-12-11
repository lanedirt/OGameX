<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\AccountTestCase;

class GlobalGameTest extends AccountTestCase
{
    /**
     * Test that a page load updates only the current planet of player instead of all planets.
     */
    public function testPageLoadOnlyUpdatesCurrentPlanet(): void
    {
        // Check that the user has at least two planets.
        $startPlanetCount = $this->planetService->getPlayer()->planets->planetCount();
        $this->assertGreaterThanOrEqual(2, $startPlanetCount);

        // Set time to +1 hour, so we can verify that only the current planet will be updated with the new time.
        $testTime = Carbon::now()->addHour();
        $this->travelTo($testTime);

        // Request overview page.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Check in the database that only the current planet was updated.
        $this->assertDatabaseHas('planets', [
            'id' => $this->planetService->getPlanetId(),
            'time_last_update' => $testTime->getTimestamp(),
        ]);

        $this->assertDatabaseMissing('planets', [
            'id' => $this->secondPlanetService->getPlanetId(),
            'time_last_update' => $testTime->getTimestamp(),
        ]);
    }
}
