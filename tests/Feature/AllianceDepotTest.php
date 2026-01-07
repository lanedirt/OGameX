<?php

namespace Tests\Feature;

use Tests\AccountTestCase;

/**
 * Test that Alliance Depot functionality works as expected.
 */
class AllianceDepotTest extends AccountTestCase
{
    /**
     * Test that Alliance Depot dialog can be accessed when building is built.
     *
     * @return void
     * @throws \Exception
     */
    public function testAllianceDepotDialogAccessible(): void
    {
        // Build Alliance Depot on the planet
        $this->planetSetObjectLevel('alliance_depot', 1);

        // Access the Alliance Depot dialog
        $response = $this->get('/ajax/alliance-depot');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the dialog contains expected content
        $response->assertSee('Alliance Depot');
        $response->assertSee('Level 1');
    }

    /**
     * Test that Alliance Depot dialog shows error when building is not built.
     *
     * @return void
     * @throws \Exception
     */
    public function testAllianceDepotDialogNotAccessibleWithoutBuilding(): void
    {
        // Ensure Alliance Depot is not built (level 0)
        $this->planetSetObjectLevel('alliance_depot', 0);

        // Try to access the Alliance Depot dialog
        $response = $this->get('/ajax/alliance-depot');

        // Assert that the response is successful but shows error
        $response->assertStatus(200);

        // Assert that the dialog contains error message
        $response->assertSee('No Alliance Depot built on this planet');
    }

    /**
     * Test that Alliance Depot dialog shows "no fleets" message when no fleets are holding.
     *
     * @return void
     * @throws \Exception
     */
    public function testAllianceDepotDialogShowsNoFleets(): void
    {
        // Build Alliance Depot on the planet
        $this->planetSetObjectLevel('alliance_depot', 1);

        // Access the Alliance Depot dialog
        $response = $this->get('/ajax/alliance-depot');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the dialog shows "no fleets" message
        $response->assertSee('No fleets are currently holding at this planet');
    }

    /**
     * Test that Alliance Depot is not accessible on moons.
     *
     * @return void
     * @throws \Exception
     */
    public function testAllianceDepotNotAccessibleOnMoon(): void
    {
        // Create a moon for the current planet
        $moon = $this->createMoonForCurrentPlanet();

        // Switch to the moon
        $this->switchToMoon($moon);

        // Try to access the Alliance Depot dialog
        $response = $this->get('/ajax/alliance-depot');

        // Assert that the response is successful but shows error
        $response->assertStatus(200);

        // Assert that the dialog contains error message
        $response->assertSee('Alliance Depot can only be used on planets');
    }
}
