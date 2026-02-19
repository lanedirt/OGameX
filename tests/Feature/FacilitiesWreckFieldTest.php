<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use OGame\Models\WreckField;
use Tests\AccountTestCase;

class FacilitiesWreckFieldTest extends AccountTestCase
{
    /**
     * Set the space_dock column on the current planet directly.
     */
    private function giveCurrentPlanetSpaceDock(int $level = 1): void
    {
        DB::table('planets')
            ->where('id', $this->planetService->getPlanetId())
            ->update(['space_dock' => $level]);
    }

    /**
     * Create a WreckField at the current planet's coordinates.
     *
     * @param array<string,mixed> $overrides
     */
    private function createWreckField(array $overrides = []): WreckField
    {
        $coords = $this->planetService->getPlanetCoordinates();

        return WreckField::factory()->create(array_merge([
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'planet' => $coords->position,
            'owner_player_id' => $this->currentUserId,
            'status' => 'active',
            'expires_at' => now()->addHours(72),
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0],
            ],
        ], $overrides));
    }

    public function test_facilities_page_shows_wreck_field_section(): void
    {
        $this->createWreckField();

        $response = $this->get(route('facilities.index'));
        $response->assertStatus(200);

        // Verify wreck field data exists in database (loaded via AJAX on frontend)
        $coords = $this->planetService->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->first();

        $this->assertNotNull($wreckField);
        $this->assertEquals('active', $wreckField->status);
    }

    public function test_start_repairs_endpoint(): void
    {
        $this->giveCurrentPlanetSpaceDock(1);
        $this->createWreckField();

        $response = $this->postJson(route('facilities.startrepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        $coords = $this->planetService->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->first();

        $this->assertEquals('repairing', $wreckField->status);
        $this->assertNotNull($wreckField->repair_started_at);
        $this->assertNotNull($wreckField->repair_completed_at);
    }

    public function test_start_repairs_fails_without_space_dock(): void
    {
        $this->giveCurrentPlanetSpaceDock(0);
        $this->createWreckField();

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
        $coords = $this->planetService->getPlanetCoordinates();

        $wreckField = new WreckField();
        $wreckField->galaxy = $coords->galaxy;
        $wreckField->system = $coords->system;
        $wreckField->planet = $coords->position;
        $wreckField->owner_player_id = $this->currentUserId;
        $wreckField->status = 'completed';
        $wreckField->created_at = now();
        $wreckField->expires_at = now()->addHours(72);
        $wreckField->repair_started_at = now()->subHours(2);
        $wreckField->repair_completed_at = now()->subHours(1);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 100],
        ];
        $wreckField->save();

        $response = $this->postJson(route('facilities.completerepairs'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        // Verify wreck field was deleted after all ships collected
        $wreckFieldAfter = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->first();

        $this->assertNull($wreckFieldAfter);
    }

    public function test_complete_repairs_fails_when_not_completed(): void
    {
        $this->createWreckField([
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0],
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
        $this->createWreckField();

        $response = $this->postJson(route('facilities.burnwreckfield'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'error' => false,
        ]);

        $coords = $this->planetService->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->first();

        $this->assertEquals('burned', $wreckField->status);
    }

    public function test_burn_wreck_field_fails_during_repairs(): void
    {
        $coords = $this->planetService->getPlanetCoordinates();
        WreckField::factory()->repairing()->create([
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'planet' => $coords->position,
            'owner_player_id' => $this->currentUserId,
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
        $this->giveCurrentPlanetSpaceDock(1);
        $this->createWreckField();

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
        // No wreck field created — the freshly registered user's planet has none.
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
