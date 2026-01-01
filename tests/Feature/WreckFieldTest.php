<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\User;
use OGame\Models\WreckField;
use OGame\Services\WreckFieldService;
use OGame\Models\Planet\Coordinate;
use Exception;

class WreckFieldTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Planet $planet;
    private WreckFieldService $wreckFieldService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Create a test planet
        $this->planet = Planet::factory()->create([
            'user_id' => $this->user->id,
            'galaxy' => 1,
            'system' => 1,
            'planet' => 1,
        ]);

        // Create WreckFieldService with the correct player
        $playerService = new \OGame\Services\PlayerService($this->user->id);
        $settingsService = app(\OGame\Services\SettingsService::class);
        $this->wreckFieldService = new \OGame\Services\WreckFieldService($playerService, $settingsService);
    }

    public function test_wreck_field_can_be_created(): void
    {
        $coordinate = new Coordinate(1, 1, 1);
        $shipData = [
            [
                'machine_name' => 'light_fighter',
                'quantity' => 50,
                'repair_progress' => 0,
            ]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $shipData, $this->user->id);

        $this->assertInstanceOf(WreckField::class, $wreckField);
        $this->assertEquals($coordinate->galaxy, $wreckField->galaxy);
        $this->assertEquals($coordinate->system, $wreckField->system);
        $this->assertEquals($coordinate->position, $wreckField->planet);
        $this->assertEquals($this->user->id, $wreckField->owner_player_id);
        $this->assertEquals('active', $wreckField->status);
        $this->assertEquals($shipData, $wreckField->ship_data);
    }

    public function test_wreck_field_expiration(): void
    {
        $wreckField = WreckField::factory()->create([
            'expires_at' => now()->subHours(1), // Expired 1 hour ago
            'status' => 'active',
        ]);

        $this->assertTrue($wreckField->isExpired());
    }

    public function test_wreck_field_repair_status(): void
    {
        $wreckField = WreckField::factory()->create([
            'status' => 'repairing',
            'repair_started_at' => now()->subHours(1),
            'repair_completed_at' => now()->addHours(1),
        ]);

        $this->assertTrue($wreckField->isRepairing());
        $this->assertFalse($wreckField->isCompleted());
        $this->assertFalse($wreckField->canBeRepaired());
        $this->assertFalse($wreckField->canBeBurned());
    }

    public function test_wreck_field_can_be_repaired(): void
    {
        $wreckField = WreckField::factory()->create([
            'status' => 'active',
            'expires_at' => now()->addHours(72), // Not expired
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $this->assertTrue($wreckField->canBeRepaired());
        $this->assertTrue($wreckField->canBeBurned());
        $this->assertFalse($wreckField->isExpired());
        $this->assertFalse($wreckField->isRepairing());
        $this->assertFalse($wreckField->isCompleted());
    }

    public function test_start_repairs(): void
    {
        $wreckField = WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'status' => 'active',
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $this->wreckFieldService->loadForCoordinates(new Coordinate($this->planet->galaxy, $this->planet->system, $this->planet->planet));

        $result = $this->wreckFieldService->startRepairs(1);

        $this->assertTrue($result);

        $updatedWreckField = $this->wreckFieldService->getWreckField();
        $this->assertEquals('repairing', $updatedWreckField->status);
        $this->assertNotNull($updatedWreckField->repair_started_at);
        $this->assertNotNull($updatedWreckField->repair_completed_at);
        $this->assertEquals(1, $updatedWreckField->space_dock_level);
    }

    public function test_complete_repairs(): void
    {
        $wreckField = WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'status' => 'repairing',
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $this->wreckFieldService->loadForCoordinates(new Coordinate($this->planet->galaxy, $this->planet->system, $this->planet->planet));

        $repairedShips = $this->wreckFieldService->completeRepairs();

        $this->assertCount(1, $repairedShips);
        $this->assertEquals('light_fighter', $repairedShips[0]['machine_name']);
        $this->assertEquals(10, $repairedShips[0]['quantity']);
        $this->assertEquals(100, $repairedShips[0]['repair_progress']);

        $updatedWreckField = $this->wreckFieldService->getWreckField();
        $this->assertEquals('completed', $updatedWreckField->status);
    }

    public function test_burn_wreck_field(): void
    {
        $wreckField = WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'status' => 'active',
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $this->wreckFieldService->loadForCoordinates(new Coordinate($this->planet->galaxy, $this->planet->system, $this->planet->planet));

        $result = $this->wreckFieldService->burnWreckField();

        $this->assertTrue($result);

        $updatedWreckField = $this->wreckFieldService->getWreckField();
        $this->assertEquals('burned', $updatedWreckField->status);
    }

    public function test_burn_wreck_field_during_repairs_fails(): void
    {
        $wreckField = WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'status' => 'repairing',
            'ship_data' => [
                ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
            ],
        ]);

        $this->wreckFieldService->loadForCoordinates(new Coordinate($this->planet->galaxy, $this->planet->system, $this->planet->planet));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wreck field cannot be burned while repairs are in progress');

        $this->wreckFieldService->burnWreckField();
    }

    public function test_wreck_field_time_remaining(): void
    {
        $futureTime = now()->addHours(24);
        $wreckField = WreckField::factory()->create([
            'expires_at' => $futureTime,
        ]);

        $timeRemaining = $wreckField->getTimeRemaining();

        // Should be approximately 24 hours (allowing for a few seconds difference)
        $this->assertGreaterThan(24 * 3600 - 10, $timeRemaining);
        $this->assertLessThan(24 * 3600 + 10, $timeRemaining);
    }

    public function test_get_wreck_field_for_current_planet(): void
    {
        // Create a wreck field for the planet
        $wreckField = WreckField::factory()->create([
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

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $planetService = $planetServiceFactory->make($this->planet->id);
        $wreckFieldData = $this->wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

        $this->assertIsArray($wreckFieldData);
        $this->assertEquals($wreckField->id, $wreckFieldData['wreck_field']->id);
        $this->assertTrue($wreckFieldData['can_repair']);
        $this->assertFalse($wreckFieldData['is_repairing']);
        $this->assertFalse($wreckFieldData['is_completed']);
        $this->assertCount(1, $wreckFieldData['ship_data']);
    }

    public function test_get_wreck_field_for_current_planet_returns_null_when_expired(): void
    {
        // Create an expired wreck field
        $wreckField = WreckField::factory()->create([
            'galaxy' => $this->planet->galaxy,
            'system' => $this->planet->system,
            'planet' => $this->planet->planet,
            'owner_player_id' => $this->user->id,
            'status' => 'active',
            'expires_at' => now()->subHours(1), // Expired
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $planetService = $planetServiceFactory->make($this->planet->id);
        $wreckFieldData = $this->wreckFieldService->getWreckFieldForCurrentPlanet($planetService);

        $this->assertNull($wreckFieldData);
    }

    public function test_extend_wreck_field_with_new_ships(): void
    {
        // Create initial wreck field
        $coordinate = new Coordinate(1, 1, 1);
        $initialShips = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $initialShips, $this->user->id);
        $originalExpiresAt = $wreckField->expires_at;

        // Add new ships after some time has passed
        sleep(1);
        $newShips = [
            ['machine_name' => 'heavy_fighter', 'quantity' => 5, 'repair_progress' => 0]
        ];

        $this->wreckFieldService->extendWreckField($wreckField, $newShips);

        $wreckField->refresh();

        // Check that ships were added
        $this->assertCount(2, $wreckField->ship_data);

        // Check that expiration was extended
        $this->assertGreaterThan($originalExpiresAt, $wreckField->expires_at);

        // Verify the specific ships
        $lightFighter = null;
        $heavyFighter = null;

        foreach ($wreckField->ship_data as $ship) {
            if ($ship['machine_name'] === 'light_fighter') {
                $lightFighter = $ship;
            } elseif ($ship['machine_name'] === 'heavy_fighter') {
                $heavyFighter = $ship;
            }
        }

        $this->assertEquals(10, $lightFighter['quantity']);
        $this->assertEquals(5, $heavyFighter['quantity']);
    }

    public function test_create_wreck_field_combines_with_active_wreck_field_and_resets_expiration(): void
    {
        // Create initial wreck field that expires soon
        $coordinate = new Coordinate(1, 1, 1);
        $initialShips = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $initialShips, $this->user->id);

        // Manually set expiration to be near expiration (1 hour from now)
        $wreckField->expires_at = now()->addHour();
        $wreckField->created_at = now()->subHours(71); // Created 71 hours ago (assuming 72 hour lifetime)
        $wreckField->save();
        $originalExpiresAt = $wreckField->expires_at;

        // Create another wreck field at the same coordinates
        $newShips = [
            ['machine_name' => 'heavy_fighter', 'quantity' => 5, 'repair_progress' => 0]
        ];

        $updatedWreckField = $this->wreckFieldService->createWreckField($coordinate, $newShips, $this->user->id);

        // Check that ships were combined
        $this->assertCount(2, $updatedWreckField->ship_data);

        // Check that expiration was RESET (not just extended) - should be much later than original
        $this->assertGreaterThan($originalExpiresAt->addHours(10), $updatedWreckField->expires_at);

        // Verify the specific ships
        $lightFighter = null;
        $heavyFighter = null;

        foreach ($updatedWreckField->ship_data as $ship) {
            if ($ship['machine_name'] === 'light_fighter') {
                $lightFighter = $ship;
            } elseif ($ship['machine_name'] === 'heavy_fighter') {
                $heavyFighter = $ship;
            }
        }

        $this->assertEquals(10, $lightFighter['quantity']);
        $this->assertEquals(5, $heavyFighter['quantity']);
    }

    public function test_create_wreck_field_adds_to_ongoing_repairs_when_repairing(): void
    {
        // Create initial wreck field
        $coordinate = new Coordinate(1, 1, 1);
        $initialShips = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $initialShips, $this->user->id);

        // Start repairs
        $this->wreckFieldService->loadForCoordinates($coordinate);
        $this->wreckFieldService->startRepairs(1);

        $wreckField->refresh();
        $originalRepairCompletionTime = $wreckField->repair_completed_at;
        $originalShipCount = $wreckField->getTotalShips();

        // Create another wreck field at the same coordinates while repairs are ongoing
        $newShips = [
            ['machine_name' => 'heavy_fighter', 'quantity' => 5, 'repair_progress' => 0]
        ];

        $newWreckField = $this->wreckFieldService->createWreckField($coordinate, $newShips, $this->user->id);

        // With the new implementation, a separate blocked wreck field should be created
        $this->assertEquals('blocked', $newWreckField->status);
        $this->assertNotEquals($wreckField->id, $newWreckField->id);

        // Check that the new wreck field has only the new ships
        $this->assertCount(1, $newWreckField->ship_data);
        $this->assertEquals(5, $newWreckField->getTotalShips());

        // Check that the original wreck field was NOT modified
        $wreckField->refresh();
        $this->assertCount(1, $wreckField->ship_data);
        $this->assertEquals(10, $wreckField->getTotalShips());
        $this->assertEquals($originalRepairCompletionTime->timestamp, $wreckField->repair_completed_at->timestamp);

        // Verify the specific ships in the new wreck field
        $heavyFighter = null;
        foreach ($newWreckField->ship_data as $ship) {
            if ($ship['machine_name'] === 'heavy_fighter') {
                $heavyFighter = $ship;
            }
        }
        $this->assertEquals(5, $heavyFighter['quantity']);
    }

    public function test_extend_wreck_field_with_reset_resets_expiration_timer(): void
    {
        // Create initial wreck field
        $coordinate = new Coordinate(1, 1, 1);
        $initialShips = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $initialShips, $this->user->id);

        // Manually set expiration to be near expiration
        $wreckField->expires_at = now()->addHour();
        $wreckField->created_at = now()->subHours(71);
        $wreckField->save();
        $originalExpiresAt = $wreckField->expires_at;

        // Add new ships using extendWreckFieldWithReset
        $newShips = [
            ['machine_name' => 'heavy_fighter', 'quantity' => 5, 'repair_progress' => 0]
        ];

        $this->wreckFieldService->extendWreckFieldWithReset($wreckField, $newShips);

        $wreckField->refresh();

        // Check that ships were added
        $this->assertCount(2, $wreckField->ship_data);

        // Check that expiration was RESET (should be much later than original)
        $this->assertGreaterThan($originalExpiresAt->addHours(10), $wreckField->expires_at);

        // Verify the specific ships
        $lightFighter = null;
        $heavyFighter = null;

        foreach ($wreckField->ship_data as $ship) {
            if ($ship['machine_name'] === 'light_fighter') {
                $lightFighter = $ship;
            } elseif ($ship['machine_name'] === 'heavy_fighter') {
                $heavyFighter = $ship;
            }
        }

        $this->assertEquals(10, $lightFighter['quantity']);
        $this->assertEquals(5, $heavyFighter['quantity']);
    }

    public function test_add_ships_to_ongoing_repairs_does_not_change_repair_time(): void
    {
        // Create initial wreck field
        $coordinate = new Coordinate(1, 1, 1);
        $initialShips = [
            ['machine_name' => 'light_fighter', 'quantity' => 10, 'repair_progress' => 0]
        ];

        $wreckField = $this->wreckFieldService->createWreckField($coordinate, $initialShips, $this->user->id);

        // Start repairs
        $this->wreckFieldService->loadForCoordinates($coordinate);
        $this->wreckFieldService->startRepairs(1);

        $wreckField->refresh();
        $originalRepairCompletionTime = $wreckField->repair_completed_at;

        // Add new ships using addShipsToOngoingRepairs
        $newShips = [
            ['machine_name' => 'heavy_fighter', 'quantity' => 5, 'repair_progress' => 0]
        ];

        $this->wreckFieldService->addShipsToOngoingRepairs($wreckField, $newShips);

        $wreckField->refresh();

        // Check that ships were added
        $this->assertCount(2, $wreckField->ship_data);

        // Check that repair completion time was NOT modified
        $this->assertEquals($originalRepairCompletionTime->timestamp, $wreckField->repair_completed_at->timestamp);

        // Verify the specific ships
        $lightFighter = null;
        $heavyFighter = null;

        foreach ($wreckField->ship_data as $ship) {
            if ($ship['machine_name'] === 'light_fighter') {
                $lightFighter = $ship;
            } elseif ($ship['machine_name'] === 'heavy_fighter') {
                $heavyFighter = $ship;
            }
        }

        $this->assertEquals(10, $lightFighter['quantity']);
        $this->assertEquals(5, $heavyFighter['quantity']);
    }
}
