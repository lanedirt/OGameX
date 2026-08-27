<?php

namespace Tests\Console;

use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\User;
use OGame\Models\WreckField;
use OGame\Services\ObjectService;
use Tests\TestCase;

class CleanupWreckFieldsCommandTest extends TestCase
{
    private User $user;
    private Planet $planet;
    private PlanetServiceFactory $planetServiceFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planetServiceFactory = resolve(PlanetServiceFactory::class);
        $this->user = User::factory()->create();

        $anchor = new Coordinate(
            1 + ($this->user->id % 9),
            1 + ($this->user->id % 499),
            1 + ($this->user->id % 15)
        );
        $coordinate = $this->getSafeEmptyCoordinate($anchor, 1, 15);

        // Create a test planet with space dock
        $this->planet = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => $coordinate->galaxy,
            'system' => $coordinate->system,
            'planet' => $coordinate->position,
            'space_dock' => 5,
        ]);
    }

    protected function tearDown(): void
    {
        if (isset($this->user)) {
            Message::where('user_id', $this->user->id)->delete();
            WreckField::where('owner_player_id', $this->user->id)->delete();
            Planet::where('user_id', $this->user->id)->delete();
            DB::table('users_tech')->where('user_id', $this->user->id)->delete();
            User::where('id', $this->user->id)->delete();
        }

        parent::tearDown();
    }

    /**
     * Get the current amount of a unit on the planet.
     */
    private function getPlanetUnitAmount(Planet $planet, string $machineName): int
    {
        $objectService = app(ObjectService::class);
        $unitObject = $objectService->getUnitObjectByMachineName($machineName);
        return $planet->{$unitObject->machine_name} ?? 0;
    }

    public function test_auto_deployment_after_72_hours(): void
    {
        // Create a wreck field that started repairs more than 72 hours ago
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->addHours(10);
        $wreckField->repair_started_at = now()->subHours(73); // More than 72 hours ago
        $wreckField->repair_completed_at = now()->subHours(73)->addMinutes(30); // 30 min repair time
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify the wreck field was deleted
        $wreckFieldAfter = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertNull($wreckFieldAfter, 'Wreck field should be deleted after auto-deployment');
    }

    public function test_auto_deployment_adds_ships_to_planet(): void
    {
        // Create a wreck field with 100 light fighters
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->addHours(10);
        $wreckField->repair_started_at = now()->subHours(73);
        $wreckField->repair_completed_at = now()->subHours(73)->addMinutes(30);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        // Get initial ship count on planet
        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Refresh the planet from the database
        $this->planet->refresh();

        // Get final ship count on planet
        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->assertEquals($initialShips + 100, $finalShips);
    }

    public function test_auto_deployment_returns_all_stored_wreckage_ships(): void
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->addHours(10);
        $wreckField->repair_started_at = now()->subHours(73);
        $wreckField->repair_completed_at = now()->subHours(73)->addMinutes(30);
        $wreckField->space_dock_level = 1;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 1000, 'repair_progress' => 0]
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        $this->planet->refresh();

        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->assertEquals($initialShips + 1000, $finalShips);
    }

    public function test_auto_deployment_does_not_affect_recent_repairs(): void
    {
        // Create a wreck field that started repairs less than 72 hours ago
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(10);
        $wreckField->expires_at = now()->addHours(62);
        $wreckField->repair_started_at = now()->subHours(10); // Only 10 hours ago
        $wreckField->repair_completed_at = now()->subHours(9)->addMinutes(30);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify wreck field still exists
        $wreckFieldAfter = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertNotNull($wreckFieldAfter, 'Wreck field should not be deleted if repairs started less than 72 hours ago');
        $this->assertEquals('repairing', $wreckFieldAfter->status);

        // Verify no ships were added
        $this->planet->refresh();
        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->assertEquals($initialShips, $finalShips, 'No ships should be added for recent repairs');
    }

    public function test_auto_deployment_handles_multiple_ship_types(): void
    {
        // Create wreck field with multiple ship types
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->addHours(10);
        $wreckField->repair_started_at = now()->subHours(73);
        $wreckField->repair_completed_at = now()->subHours(73)->addMinutes(30);
        $wreckField->space_dock_level = 10; // 37.8% cap
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0],
            ['machine_name' => 'heavy_fighter', 'quantity' => 50, 'repair_progress' => 0],
            ['machine_name' => 'cruiser', 'quantity' => 20, 'repair_progress' => 0],
        ];
        $wreckField->save();

        // Get initial ship counts
        $initialLightFighters = $this->getPlanetUnitAmount($this->planet, 'light_fighter');
        $initialHeavyFighters = $this->getPlanetUnitAmount($this->planet, 'heavy_fighter');
        $initialCruisers = $this->getPlanetUnitAmount($this->planet, 'cruiser');

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Get final ship counts
        $this->planet->refresh();
        $finalLightFighters = $this->getPlanetUnitAmount($this->planet, 'light_fighter');
        $finalHeavyFighters = $this->getPlanetUnitAmount($this->planet, 'heavy_fighter');
        $finalCruisers = $this->getPlanetUnitAmount($this->planet, 'cruiser');

        $this->assertEquals($initialLightFighters + 100, $finalLightFighters);
        $this->assertEquals($initialHeavyFighters + 50, $finalHeavyFighters);
        $this->assertEquals($initialCruisers + 20, $finalCruisers);
    }

    public function test_cleanup_expired_wreck_fields(): void
    {
        // Create expired wreck fields (not repairing)
        $wreckField1 = new WreckField();
        $wreckField1->galaxy = 1;
        $wreckField1->system = 1;
        $wreckField1->planet = 2;
        $wreckField1->owner_player_id = $this->user->id;
        $wreckField1->status = 'active';
        $wreckField1->created_at = now()->subHours(80);
        $wreckField1->expires_at = now()->subHours(1); // Expired
        $wreckField1->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];
        $wreckField1->save();

        // Create non-expired wreck field
        $wreckField2 = new WreckField();
        $wreckField2->galaxy = 1;
        $wreckField2->system = 1;
        $wreckField2->planet = 3;
        $wreckField2->owner_player_id = $this->user->id;
        $wreckField2->status = 'active';
        $wreckField2->created_at = now()->subHours(10);
        $wreckField2->expires_at = now()->addHours(62); // Not expired
        $wreckField2->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];
        $wreckField2->save();

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify expired wreck field was deleted
        $wreckField1After = WreckField::where('galaxy', 1)
            ->where('system', 1)
            ->where('planet', 2)
            ->first();
        $this->assertNull($wreckField1After);

        // Verify non-expired wreck field still exists
        $wreckField2After = WreckField::where('galaxy', 1)
            ->where('system', 1)
            ->where('planet', 3)
            ->first();
        $this->assertNotNull($wreckField2After);
    }

    public function test_auto_deployment_does_not_delete_repairing_wreck_fields(): void
    {
        // Create a wreck field that is repairing but not yet 72 hours
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(10);
        $wreckField->expires_at = now()->addHours(62);
        $wreckField->repair_started_at = now()->subHours(10);
        $wreckField->repair_completed_at = now()->addHours(1);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify the wreck field still exists
        $wreckFieldAfter = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertNotNull($wreckFieldAfter, 'Repairing wreck field should not be deleted if less than 72 hours');
        $this->assertEquals('repairing', $wreckFieldAfter->status);
    }

    public function test_auto_return_repairing_wreck_field_deploys_repaired_ships_after_lifetime_window(): void
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->subMinute();
        $wreckField->repair_started_at = now()->subHours(73);
        $wreckField->repair_completed_at = now()->subHours(61);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0],
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        $this->planet->refresh();
        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->assertEquals($initialShips + 100, $finalShips);
        $this->assertNull(WreckField::find($wreckField->id));
    }

    public function test_auto_return_completed_wreck_field_deploys_ready_ships_after_lifetime_window(): void
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'completed';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->subMinute();
        $wreckField->repair_started_at = now()->subHours(73);
        $wreckField->repair_completed_at = now()->subHours(72);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 100],
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        $this->planet->refresh();
        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->assertEquals($initialShips + 100, $finalShips);
        $this->assertNull(WreckField::find($wreckField->id));
    }

    public function test_cleanup_expired_repairing_wreck_field_does_not_delete_before_auto_return_window(): void
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->subMinute();
        $wreckField->repair_started_at = now()->subHours(10);
        $wreckField->repair_completed_at = now()->addHours(2);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0],
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        $this->planet->refresh();
        $this->assertEquals($initialShips, $this->getPlanetUnitAmount($this->planet, 'light_fighter'));

        $wreckFieldAfter = WreckField::find($wreckField->id);
        $this->assertNotNull($wreckFieldAfter);
        $this->assertEquals('repairing', $wreckFieldAfter->status);
    }

    public function test_cleanup_expired_completed_wreck_field_does_not_delete_before_auto_return_window(): void
    {
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'completed';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->subMinute();
        $wreckField->repair_started_at = now()->subHours(10);
        $wreckField->repair_completed_at = now()->subHours(9);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 100],
        ];
        $wreckField->save();

        $initialShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        $this->planet->refresh();
        $this->assertEquals($initialShips, $this->getPlanetUnitAmount($this->planet, 'light_fighter'));

        $wreckFieldAfter = WreckField::find($wreckField->id);
        $this->assertNotNull($wreckFieldAfter);
        $this->assertEquals('completed', $wreckFieldAfter->status);
    }

    public function test_auto_deployment_sends_message_to_player(): void
    {
        // Create a wreck field that started repairs more than 72 hours ago
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(80);
        $wreckField->expires_at = now()->addHours(10);
        $wreckField->repair_started_at = now()->subHours(73); // More than 72 hours ago
        $wreckField->repair_completed_at = now()->subHours(73)->addMinutes(30); // 30 min repair time
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify the message was sent to the player
        $message = Message::where('user_id', $this->user->id)
            ->where('key', 'wreck_field_repair_completed')
            ->first();

        $this->assertNotNull($message, 'Message should be sent to player when ships are auto-deployed');
        $this->assertEquals('wreck_field_repair_completed', $message->key);
        $this->assertArrayHasKey('planet', $message->params);
        $this->assertArrayHasKey('ship_count', $message->params);

        $this->assertEquals('100', $message->params['ship_count']);
    }

    public function test_auto_deployment_does_not_send_message_for_recent_repairs(): void
    {
        // Create a wreck field that started repairs less than 72 hours ago
        $wreckField = new WreckField();
        $wreckField->galaxy = $this->planet->galaxy;
        $wreckField->system = $this->planet->system;
        $wreckField->planet = $this->planet->planet;
        $wreckField->owner_player_id = $this->user->id;
        $wreckField->status = 'repairing';
        $wreckField->created_at = now()->subHours(10);
        $wreckField->expires_at = now()->addHours(62);
        $wreckField->repair_started_at = now()->subHours(10); // Only 10 hours ago
        $wreckField->repair_completed_at = now()->subHours(9)->addMinutes(30);
        $wreckField->space_dock_level = 5;
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 100, 'repair_progress' => 0]
        ];
        $wreckField->save();

        // Run the command
        $this->artisan('ogamex:scheduler:cleanup-wreckfields');

        // Verify no message was sent
        $message = Message::where('user_id', $this->user->id)
            ->where('key', 'wreck_field_repair_completed')
            ->first();

        $this->assertNull($message, 'No message should be sent for repairs that started less than 72 hours ago');
    }
}
