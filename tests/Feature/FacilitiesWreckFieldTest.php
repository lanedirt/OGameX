<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Models\WreckField;
use Tests\TestCase;

class FacilitiesWreckFieldTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Planet $planet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create a test planet with space dock
        $this->planet = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => 1,
            'system' => 1,
            'planet' => 1,
            'space_dock' => 1, // Level 1 space dock
        ]);

        // Login the user
        $this->actingAs($this->user);
    }

    public function test_facilities_page_shows_wreck_field_section(): void
    {
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->get(route('facilities.index'));

        $response->assertStatus(200);

        // Verify wreck field data exists in database (loaded via AJAX on frontend)
        $wreckField = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();
        $this->assertNotNull($wreckField);
        $this->assertEquals('active', $wreckField->status);
    }

    public function test_start_repairs_endpoint(): void
    {
        // Create a wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->postJson(route('facilities.startrepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        // Check that the wreck field status changed to repairing
        $wreckField = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertEquals('repairing', $wreckField->status);
        $this->assertNotNull($wreckField->repair_started_at);
        $this->assertNotNull($wreckField->repair_completed_at);
    }

    public function test_start_repairs_fails_without_space_dock(): void
    {
        // Set planet space dock level to 0
        \DB::table('planets')
            ->where('id', $this->planet->id)
            ->update(['space_dock' => 0]);

        // Create a wreck field for the current planet
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->postJson(route('facilities.startrepairs'));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => __('wreck_field.error_space_dock_required'),
        ]);
    }

    public function test_complete_repairs_endpoint(): void
    {
        // Create a completed wreck field with 100% repair progress
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'completed';
        $wreckField->created_at = now();
        $wreckField->expires_at = now()->addHours(72);
        $wreckField->repair_started_at = now()->subHours(2);
        $wreckField->repair_completed_at = now()->subHours(1);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 100]
        ];
        $wreckField->save();

        $response = $this->postJson(route('facilities.completerepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        // Verify wreck field was deleted after all ships collected
        $wreckFieldAfter = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertNull($wreckFieldAfter);
    }

    public function test_complete_repairs_fails_when_not_completed(): void
    {
        // Create a wreck field with active status (repairs not started)
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->postJson(route('facilities.completerepairs'));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => __('wreck_field.repairs_not_started'),
        ]);
    }

    public function test_burn_wreck_field_endpoint(): void
    {
        // Create an active wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
        ]);

        $response = $this->postJson(route('facilities.burnwreckfield'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        // Check that the wreck field status changed to burned
        $wreckField = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertEquals('burned', $wreckField->status);
    }

    public function test_burn_wreck_field_fails_during_repairs(): void
    {
        // Create a repairing wreck field
        WreckField::factory()->repairing()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
        ]);

        $response = $this->postJson(route('facilities.burnwreckfield'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => __('wreck_field.cannot_burn'),
        ]);
    }

    public function test_get_wreck_field_status_endpoint(): void
    {
        // Create a wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->getJson(route('facilities.wreckfieldstatus'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        $data = $response->json();
        $this->assertArrayHasKey('wreckField', $data);
        $this->assertNotNull($data['wreckField']);
        $this->assertTrue($data['wreckField']['can_repair']);
        $this->assertFalse($data['wreckField']['is_repairing']);
        $this->assertFalse($data['wreckField']['is_completed']);
    }

    public function test_all_endpoints_fail_when_not_authenticated(): void
    {
        auth()->logout();

        $endpoints = [
            'facilities.startrepairs' => 'POST',
            'facilities.completerepairs' => 'POST',
            'facilities.burnwreckfield' => 'POST',
            'facilities.wreckfieldstatus' => 'GET',
        ];

        foreach ($endpoints as $route => $method) {
            $response = $this->json($method, route($route));
            $response->assertStatus(401);
        }
    }

    public function test_wreck_field_not_found_responses(): void
    {
        // Don't create any wreck field

        $endpoints = [
            ['route' => 'facilities.startrepairs', 'method' => 'POST', 'expected_message' => __('wreck_field.error_no_wreck_field')],
            ['route' => 'facilities.completerepairs', 'method' => 'POST', 'expected_message' => __('wreck_field.error_no_wreck_field')],
            ['route' => 'facilities.burnwreckfield', 'method' => 'POST', 'expected_message' => __('wreck_field.error_no_wreck_field')],
        ];

        foreach ($endpoints as $endpoint) {
            $response = $this->json($endpoint['method'], route($endpoint['route']));
            $response->assertStatus(200);
            $response->assertJson([
                'success' => false,
                'error' => true,
                'message' => $endpoint['expected_message'],
            ]);
        }

        // Status endpoint should return null wreck field
        $response = $this->getJson(route('facilities.wreckfieldstatus'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        $data = $response->json();
        $this->assertNull($data['wreckField']);
    }
}
