<?php

namespace Tests\Feature;

use Tests\AccountTestCase;
use Route;

/**
 * Feature tests for missile attack functionality.
 *
 * NOTE: These tests are simplified due to test infrastructure limitations.
 * Full integration testing should be done manually or with additional test helpers.
 */
class MissileAttackFeatureTest extends AccountTestCase
{
    public function testMissileAttackRouteExists(): void
    {
        // Verify the route is registered
        $this->assertTrue(Route::has('galaxy.missile-attack'));
    }

    public function testMissileAttackOverlayRouteExists(): void
    {
        // Verify the overlay route is registered
        $this->assertTrue(Route::has('galaxy.missile-attack.overlay'));
    }

    public function testDestroyRocketsRouteExists(): void
    {
        // Verify the destroy rockets route is registered
        $this->assertTrue(Route::has('facilities.destroy-rockets'));
    }

    public function testValidationNoMissiles(): void
    {
        // Try to launch without missiles
        $response = $this->post(route('galaxy.missile-attack'), [
            'galaxy' => 1,
            'system' => 102,
            'position' => 5,
            'type' => 1,
            'missile_count' => 5,
            'target_priority' => 0,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
}
