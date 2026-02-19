<?php

namespace Tests\Console;

use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
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

        // Create a test planet with space dock
        $this->planet = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => 1,
            'system' => 1,
            'planet' => 1,
            'space_dock' => 5, // Level 5 space dock (35.7% cap)
        ]);
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
        $this->artisan('ogame:wreck-fields:cleanup');

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
        $this->artisan('ogame:wreck-fields:cleanup');

        // Refresh the planet from the database
        $this->planet->refresh();

        // Get final ship count on planet
        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        // Level 5 = 35.7% cap, so 35 or 36 ships should be added
        $expectedShips = (int) floor(100 * 0.357);
        $this->assertEquals($initialShips + $expectedShips, $finalShips);
    }

    public function test_auto_deployment_respects_space_dock_level_cap(): void
    {
        // Create wreck field with level 1 space dock (31.5% cap)
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
        $this->artisan('ogame:wreck-fields:cleanup');

        $this->planet->refresh();

        $finalShips = $this->getPlanetUnitAmount($this->planet, 'light_fighter');

        // Level 1 = 31.5% cap
        $expectedShips = (int) floor(1000 * 0.315);
        $this->assertEquals($initialShips + $expectedShips, $finalShips);
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
        $this->artisan('ogame:wreck-fields:cleanup');

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
        $this->artisan('ogame:wreck-fields:cleanup');

        // Get final ship counts
        $this->planet->refresh();
        $finalLightFighters = $this->getPlanetUnitAmount($this->planet, 'light_fighter');
        $finalHeavyFighters = $this->getPlanetUnitAmount($this->planet, 'heavy_fighter');
        $finalCruisers = $this->getPlanetUnitAmount($this->planet, 'cruiser');

        // Level 10 = 37.8% cap
        $percentage = 0.378;
        $this->assertEquals($initialLightFighters + (int) floor(100 * $percentage), $finalLightFighters);
        $this->assertEquals($initialHeavyFighters + (int) floor(50 * $percentage), $finalHeavyFighters);
        $this->assertEquals($initialCruisers + (int) floor(20 * $percentage), $finalCruisers);
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
        $this->artisan('ogame:wreck-fields:cleanup');

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
        $this->artisan('ogame:wreck-fields:cleanup');

        // Verify the wreck field still exists
        $wreckFieldAfter = WreckField::where('galaxy', $this->planet->galaxy)
            ->where('system', $this->planet->system)
            ->where('planet', $this->planet->planet)
            ->first();

        $this->assertNotNull($wreckFieldAfter, 'Repairing wreck field should not be deleted if less than 72 hours');
        $this->assertEquals('repairing', $wreckFieldAfter->status);
    }
}
