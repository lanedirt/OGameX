<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OGame\Models\User;
use OGame\Models\Planet;
use OGame\Models\WreckField;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class FacilitiesWreckFieldTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Planet $planet;
    private PlayerService $playerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->playerService = app(PlayerService::class);
        $this->playerService->setUser($this->user);

        // Create a test planet with space dock
        $this->planet = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => 1,
            'system' => 1,
            'position' => 1,
            'building_space_dock' => 1, // Level 1 space dock
        ]);

        // Login the user
        $this->actingAs($this->user);
    }

    public function test_facilities_page_shows_wreck_field_section(): void
    {
        // Create a wreck field for the planet
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $response = $this->get(route('facilities.index'));

        $response->assertStatus(200);
        $response->assertSee('wreckFieldSection');
        $response->assertSee('Wreck Field');
    }

    public function test_start_repairs_endpoint(): void
    {
        // Create a wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
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
            ->where('planet', $this->planet->position)
            ->first();

        $this->assertEquals('repairing', $wreckField->status);
        $this->assertNotNull($wreckField->repair_started_at);
        $this->assertNotNull($wreckField->repair_completed_at);
    }

    public function test_start_repairs_fails_without_space_dock(): void
    {
        // Create planet without space dock
        $planetWithoutDock = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => 2,
            'system' => 1,
            'position' => 1,
            'building_space_dock' => 0, // No space dock
        ]);

        // Create a wreck field for this planet
        WreckField::factory()->create([
            'galaxy' => $planetWithoutDock->galaxy,
            'system' => $planetWithoutDock->system,
            'planet' => $planetWithoutDock->position,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
        ]);

        $response = $this->postJson(route('facilities.startrepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => 'Space dock is required for repairs',
        ]);
    }

    public function test_complete_repairs_endpoint(): void
    {
        // Create a completed wreck field
        WreckField::factory()->completed()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
            'owner_player_id' => $this->user->id,
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 100]
            ],
        ]);

        $response = $this->postJson(route('facilities.completerepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        // Check that the wreck field was deleted after repairs are completed
        $wreckField = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->position)
            ->first();

        $this->assertNull($wreckField);
    }

    public function test_complete_repairs_fails_when_not_completed(): void
    {
        // Create a wreck field that's still repairing
        WreckField::factory()->repairing()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
            'owner_player_id' => $this->user->id,
        ]);

        $response = $this->postJson(route('facilities.completerepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => 'Repairs are not yet completed',
        ]);
    }

    public function test_burn_wreck_field_endpoint(): void
    {
        // Create an active wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
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
            ->where('planet', $this->planet->position)
            ->first();

        $this->assertEquals('burned', $wreckField->status);
    }

    public function test_burn_wreck_field_fails_during_repairs(): void
    {
        // Create a repairing wreck field
        WreckField::factory()->repairing()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
            'owner_player_id' => $this->user->id,
        ]);

        $response = $this->postJson(route('facilities.burnwreckfield'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'error' => true,
            'message' => 'Cannot burn wreck field while repairs are in progress',
        ]);
    }

    public function test_get_wreck_field_status_endpoint(): void
    {
        // Create a wreck field
        WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->position,
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
            ['route' => 'facilities.startrepairs', 'method' => 'POST', 'expected_message' => 'No wreck field found'],
            ['route' => 'facilities.completerepairs', 'method' => 'POST', 'expected_message' => 'No wreck field found'],
            ['route' => 'facilities.burnwreckfield', 'method' => 'POST', 'expected_message' => 'No wreck field found'],
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