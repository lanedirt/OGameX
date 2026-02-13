<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet\Coordinate;
use OGame\Models\PlanetMove;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuildingQueueService;
use OGame\Services\DarkMatterService;
use OGame\Services\FleetMissionService;
use OGame\Services\JumpGateService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetMoveService;
use OGame\Services\ResearchQueueService;
use OGame\Services\SettingsService;
use OGame\Services\UnitQueueService;
use Tests\AccountTestCase;

class PlanetMoveTest extends AccountTestCase
{
    /**
     * Set up the test environment with sufficient Dark Matter.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Give the user enough Dark Matter for relocations.
        $user = User::find($this->currentUserId);
        $user->dark_matter = 500000;
        $user->save();
    }

    /**
     * Clean up after each test to avoid polluting other tests.
     */
    protected function tearDown(): void
    {
        // Clean up planet move records.
        PlanetMove::where('planet_id', $this->planetService->getPlanetId())->delete();

        // Clean up fleet missions created by relocation ship transfers.
        FleetMission::where('user_id', $this->currentUserId)->delete();

        // Clean up messages sent during relocation processing.
        Message::where('user_id', $this->currentUserId)->delete();

        // Clean up manually inserted queue records.
        DB::table('building_queues')->where('planet_id', $this->planetService->getPlanetId())->delete();
        DB::table('research_queues')->where('planet_id', $this->planetService->getPlanetId())->delete();

        parent::tearDown();
    }

    /**
     * Test successfully scheduling a planet relocation (creates pending move, does NOT move instantly).
     */
    public function testRelocationToEmptyPosition(): void
    {
        $originalCoordinates = $this->planetService->getPlanetCoordinates();
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => '']);

        // Verify a pending move record exists.
        $move = PlanetMove::where('planet_id', $this->planetService->getPlanetId())
            ->where('canceled', false)
            ->where('processed', false)
            ->first();
        $this->assertNotNull($move);
        $this->assertEquals($emptyCoordinate->galaxy, $move->target_galaxy);
        $this->assertEquals($emptyCoordinate->system, $move->target_system);
        $this->assertEquals($emptyCoordinate->position, $move->target_position);

        // Verify coordinates have NOT changed yet (move is only scheduled).
        $this->reloadApplication();
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        $updatedPlanet = $this->planetService->getPlayer()->planets->current();
        $currentCoordinates = $updatedPlanet->getPlanetCoordinates();

        $this->assertEquals($originalCoordinates->galaxy, $currentCoordinates->galaxy);
        $this->assertEquals($originalCoordinates->system, $currentCoordinates->system);
        $this->assertEquals($originalCoordinates->position, $currentCoordinates->position);

        // Verify DM was NOT deducted (only deducted when move executes).
        $user = User::find($this->currentUserId);
        $this->assertEquals(500000, $user->dark_matter);
    }

    /**
     * Test that relocating to an occupied position fails.
     */
    public function testRelocationToOccupiedPosition(): void
    {
        // The second planet of the user is occupied, try to relocate there.
        $occupiedCoordinate = $this->secondPlanetService->getPlanetCoordinates();

        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $occupiedCoordinate->galaxy,
            'system' => $occupiedCoordinate->system,
            'position' => $occupiedCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'The target position is not empty.']);

        // Verify DM was not deducted.
        $user = User::find($this->currentUserId);
        $this->assertEquals(500000, $user->dark_matter);
    }

    /**
     * Test that relocating without enough Dark Matter fails.
     */
    public function testRelocationInsufficientDarkMatter(): void
    {
        // Set DM to less than the cost.
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100;
        $user->save();

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'Insufficient Dark Matter. You need 240,000 DM.']);
    }

    /**
     * Test that relocating with an active building queue fails.
     */
    public function testRelocationWithActiveBuildingQueue(): void
    {
        // Add resources and start a building.
        $this->planetAddResources(new Resources(50000, 50000, 0, 0));
        $this->addResourceBuildRequest('metal_mine');

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'Cannot relocate while buildings are being constructed.']);
    }

    /**
     * Test that relocating with an active research queue fails.
     */
    public function testRelocationWithActiveResearchQueue(): void
    {
        // Insert a research queue item directly to avoid it being processed instantly.
        DB::table('research_queues')->insert([
            'planet_id' => $this->planetService->getPlanetId(),
            'object_id' => 113, // energy_technology
            'object_level_target' => 1,
            'time_duration' => 3600,
            'time_start' => time(),
            'time_end' => time() + 3600,
            'building' => 1,
            'processed' => 0,
            'canceled' => 0,
        ]);

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'Cannot relocate while research is in progress.']);
    }

    /**
     * Test that scheduling a second move while one is pending fails.
     */
    public function testRelocationAlreadyInProgress(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule the first move.
        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);
        $response->assertStatus(200);
        $response->assertJson(['error' => '']);

        // Try to schedule a second move.
        $emptyCoordinate2 = $this->getNearbyEmptyCoordinate();
        $response2 = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate2->galaxy,
            'system' => $emptyCoordinate2->system,
            'position' => $emptyCoordinate2->position,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson(['error' => 'A planet relocation is already in progress.']);
    }

    /**
     * Test that cancelling a pending move works correctly.
     */
    public function testRelocationCancelBeforeExpiry(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule a move.
        $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        // Cancel the move.
        $response = $this->get('/ajax/planet-move/cancel');
        $response->assertStatus(200);
        $response->assertJson(['error' => '']);

        // Verify the move is canceled.
        $move = PlanetMove::where('planet_id', $this->planetService->getPlanetId())->first();
        $this->assertTrue((bool) $move->canceled);

        // Verify DM was not deducted.
        $user = User::find($this->currentUserId);
        $this->assertEquals(500000, $user->dark_matter);
    }

    /**
     * Helper to schedule a move, fast-forward time, and process it.
     */
    private function scheduleAndProcessMove(Coordinate $emptyCoordinate): PlanetMove
    {
        $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $move = PlanetMove::where('planet_id', $this->planetService->getPlanetId())
            ->where('canceled', false)
            ->where('processed', false)
            ->first();
        $move->time_arrive = time() - 1;
        $move->save();

        $planetMoveService = resolve(PlanetMoveService::class);
        $planetMoveService->processDueMoves(
            resolve(PlanetServiceFactory::class),
            resolve(DarkMatterService::class),
            resolve(SettingsService::class),
            resolve(BuildingQueueService::class),
            resolve(ResearchQueueService::class),
            resolve(UnitQueueService::class),
            resolve(FleetMissionService::class),
        );

        return $move;
    }

    /**
     * Test that a move executes correctly after the countdown expires.
     */
    public function testRelocationCountdownExpiresAndMoves(): void
    {
        $originalCoordinates = $this->planetService->getPlanetCoordinates();
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Add ships to the planet.
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('light_fighter', 3);

        $move = $this->scheduleAndProcessMove($emptyCoordinate);

        // Verify the move was processed.
        $move->refresh();
        $this->assertTrue((bool) $move->processed);
        $this->assertFalse((bool) $move->canceled);

        // Verify DM was deducted.
        $user = User::find($this->currentUserId);
        $this->assertEquals(260000, $user->dark_matter);

        // Verify the planet coordinates changed.
        $this->reloadApplication();
        $this->planetService->getPlayer()->load($this->planetService->getPlayer()->getId());
        $updatedPlanet = $this->planetService->getPlayer()->planets->current();
        $newCoordinates = $updatedPlanet->getPlanetCoordinates();

        $this->assertEquals($emptyCoordinate->galaxy, $newCoordinates->galaxy);
        $this->assertEquals($emptyCoordinate->system, $newCoordinates->system);
        $this->assertEquals($emptyCoordinate->position, $newCoordinates->position);

        // Verify a deployment fleet mission was created from old to new coords.
        $fleetMission = FleetMission::where('user_id', $this->currentUserId)
            ->where('mission_type', 4)
            ->where('processed', 0)
            ->first();
        $this->assertNotNull($fleetMission, 'A deployment fleet mission should be created for ship transfer.');
        $this->assertEquals($originalCoordinates->galaxy, $fleetMission->galaxy_from);
        $this->assertEquals($originalCoordinates->system, $fleetMission->system_from);
        $this->assertEquals($originalCoordinates->position, $fleetMission->position_from);
        $this->assertEquals($emptyCoordinate->galaxy, $fleetMission->galaxy_to);
        $this->assertEquals($emptyCoordinate->system, $fleetMission->system_to);
        $this->assertEquals($emptyCoordinate->position, $fleetMission->position_to);
        $this->assertEquals(5, $fleetMission->small_cargo);
        $this->assertEquals(3, $fleetMission->light_fighter);

        // Verify ships were removed from the planet.
        $updatedPlanet->reloadPlanet();
        $shipUnits = $updatedPlanet->getShipUnits();
        $this->assertEquals(0, $shipUnits->getAmount(), 'Ships should be removed from the planet after relocation.');
    }

    /**
     * Test that ships arrive at the new coordinates via fleet mission after relocation.
     */
    public function testRelocationShipTransferArrives(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Add ships to the planet.
        $this->planetAddUnit('small_cargo', 5);
        $this->planetAddUnit('light_fighter', 3);

        $this->scheduleAndProcessMove($emptyCoordinate);

        // Find the fleet mission.
        $fleetMission = FleetMission::where('user_id', $this->currentUserId)
            ->where('mission_type', 4)
            ->where('processed', 0)
            ->first();
        $this->assertNotNull($fleetMission);

        // Advance time past the fleet arrival.
        $this->travelTo(Date::createFromTimestamp($fleetMission->time_arrival + 1));

        // Trigger fleet processing by requesting a page.
        $response = $this->get('/overview');
        $response->assertStatus(200);

        // Verify the fleet mission was processed.
        $fleetMission->refresh();
        $this->assertEquals(1, $fleetMission->processed, 'Fleet mission should be processed after arrival.');

        // Verify ships are back on the planet at new coordinates.
        $this->planetService->reloadPlanet();
        $shipUnits = $this->planetService->getShipUnits();
        $this->assertEquals(5, $shipUnits->getAmountByMachineName('small_cargo'), 'Small cargo ships should be on the planet after fleet arrival.');
        $this->assertEquals(3, $shipUnits->getAmountByMachineName('light_fighter'), 'Light fighters should be on the planet after fleet arrival.');
    }

    /**
     * Test that no fleet mission is created when there are no ships on the planet.
     */
    public function testRelocationNoShipsNoFleetMission(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $move = $this->scheduleAndProcessMove($emptyCoordinate);

        // Verify the move was processed.
        $move->refresh();
        $this->assertTrue((bool) $move->processed);

        // Verify no fleet mission was created.
        $fleetMission = FleetMission::where('user_id', $this->currentUserId)
            ->where('mission_type', 4)
            ->where('processed', 0)
            ->first();
        $this->assertNull($fleetMission, 'No fleet mission should be created when there are no ships.');
    }

    /**
     * Test that a success message is sent after a successful relocation.
     */
    public function testRelocationSuccessMessageSent(): void
    {
        $originalCoordinates = $this->planetService->getPlanetCoordinates();
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        $this->scheduleAndProcessMove($emptyCoordinate);

        // Verify a success message was sent containing the planet name and coordinates.
        $this->assertMessageReceivedAndContains('economy', 'economy', [
            $this->planetService->getPlanetName(),
            $originalCoordinates->asString(),
            $emptyCoordinate->asString(),
        ]);
    }

    /**
     * Test that a 24-hour cooldown is enforced after a successful relocation.
     */
    public function testRelocationCooldownAfterSuccess(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule and process a move successfully.
        $this->scheduleAndProcessMove($emptyCoordinate);

        // Immediately try to schedule another move on the same planet.
        $emptyCoordinate2 = $this->getNearbyEmptyCoordinate();
        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate2->galaxy,
            'system' => $emptyCoordinate2->system,
            'position' => $emptyCoordinate2->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'Relocation is on cooldown. Please wait before relocating again.']);
    }

    /**
     * Test that a 24-hour cooldown is enforced after cancelling a relocation.
     */
    public function testRelocationCooldownAfterCancel(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule a move.
        $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        // Cancel it.
        $response = $this->get('/ajax/planet-move/cancel');
        $response->assertStatus(200);
        $response->assertJson(['error' => '']);

        // Immediately try to schedule another move.
        $emptyCoordinate2 = $this->getNearbyEmptyCoordinate();
        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate2->galaxy,
            'system' => $emptyCoordinate2->system,
            'position' => $emptyCoordinate2->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => 'Relocation is on cooldown. Please wait before relocating again.']);
    }

    /**
     * Test that the jump gate on a relocated moon is deactivated for 24 hours.
     */
    public function testRelocationDeactivatesJumpGateFor24Hours(): void
    {
        // Create a moon for the current planet.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moonService = $planetServiceFactory->createMoonForPlanet($this->planetService, 100000, 20);

        // Build a jump gate on the moon.
        $jumpGateObject = ObjectService::getObjectByMachineName('jump_gate');
        $moonService->setObjectLevel($jumpGateObject->id, 1, true);
        $moonService->reloadPlanet();

        // Verify the jump gate is not on cooldown initially.
        $jumpGateService = resolve(JumpGateService::class);
        $this->assertFalse($jumpGateService->isOnCooldown($moonService));

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();
        $this->scheduleAndProcessMove($emptyCoordinate);

        // Reload the moon to pick up the cooldown change.
        $moonService->reloadPlanet();

        // Verify the jump gate is now on cooldown.
        $this->assertTrue($jumpGateService->isOnCooldown($moonService));

        // Verify the cooldown is approximately 24 hours (allow a few seconds tolerance).
        $remaining = $jumpGateService->getRemainingCooldown($moonService);
        $this->assertGreaterThan(86390, $remaining);
        $this->assertLessThanOrEqual(86400, $remaining);
    }

    /**
     * Test that a move is canceled at expiry if there is an active building queue.
     */
    public function testRelocationBlockedAtExpiry(): void
    {
        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule a move.
        $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        // Fast-forward the move's time_arrive to the past.
        $move = PlanetMove::where('planet_id', $this->planetService->getPlanetId())
            ->where('canceled', false)
            ->where('processed', false)
            ->first();
        $move->time_arrive = time() - 1;
        $move->save();

        // Add a building queue item (this blocks the move at processing time).
        DB::table('building_queues')->insert([
            'planet_id' => $this->planetService->getPlanetId(),
            'object_id' => 1, // metal_mine
            'object_level_target' => 1,
            'time_duration' => 3600,
            'time_start' => time(),
            'time_end' => time() + 3600,
            'building' => 1,
            'processed' => 0,
            'canceled' => 0,
        ]);

        // Trigger processing.
        $planetMoveService = resolve(PlanetMoveService::class);
        $planetMoveService->processDueMoves(
            resolve(PlanetServiceFactory::class),
            resolve(DarkMatterService::class),
            resolve(SettingsService::class),
            resolve(BuildingQueueService::class),
            resolve(ResearchQueueService::class),
            resolve(UnitQueueService::class),
            resolve(FleetMissionService::class),
        );

        // Verify the move was canceled (not executed).
        $move->refresh();
        $this->assertTrue((bool) $move->canceled);
        $this->assertFalse((bool) $move->processed);

        // Verify DM was NOT deducted.
        $user = User::find($this->currentUserId);
        $this->assertEquals(500000, $user->dark_matter);
    }

    /**
     * Test that moon ships are transferred via a separate deployment fleet mission during relocation.
     */
    public function testRelocationTransfersMoonShips(): void
    {
        // Create a moon for the current planet.
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moonService = $planetServiceFactory->createMoonForPlanet($this->planetService, 100000, 20);

        // Add ships to the moon.
        $moonService->addUnit('light_fighter', 7, true);
        $moonService->addUnit('heavy_fighter', 3, true);
        $moonService->reloadPlanet();

        // Add ships to the planet too.
        $this->planetAddUnit('small_cargo', 5);

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();
        $this->scheduleAndProcessMove($emptyCoordinate);

        // Verify two separate deployment fleet missions were created.
        $fleetMissions = FleetMission::where('user_id', $this->currentUserId)
            ->where('mission_type', 4)
            ->where('processed', 0)
            ->get();
        $this->assertCount(2, $fleetMissions, 'Two deployment fleet missions should be created (planet + moon).');

        // Identify planet and moon missions by their type_to.
        $planetMission = $fleetMissions->firstWhere('type_to', PlanetType::Planet->value);
        $moonMission = $fleetMissions->firstWhere('type_to', PlanetType::Moon->value);

        $this->assertNotNull($planetMission, 'Planet ship transfer mission should exist.');
        $this->assertNotNull($moonMission, 'Moon ship transfer mission should exist.');

        $this->assertEquals(5, $planetMission->small_cargo);
        $this->assertEquals(7, $moonMission->light_fighter);
        $this->assertEquals(3, $moonMission->heavy_fighter);
    }

    /**
     * Test that research on a different planet does not block relocation.
     */
    public function testRelocationAllowedWithResearchOnDifferentPlanet(): void
    {
        // Insert a research queue item on the SECOND planet (not the one being moved).
        DB::table('research_queues')->insert([
            'planet_id' => $this->secondPlanetService->getPlanetId(),
            'object_id' => 113, // energy_technology
            'object_level_target' => 1,
            'time_duration' => 3600,
            'time_start' => time(),
            'time_end' => time() + 3600,
            'building' => 1,
            'processed' => 0,
            'canceled' => 0,
        ]);

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Scheduling relocation on the first planet should succeed.
        $response = $this->post('/ajax/planet-move', [
            '_token' => csrf_token(),
            'galaxy' => $emptyCoordinate->galaxy,
            'system' => $emptyCoordinate->system,
            'position' => $emptyCoordinate->position,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['error' => '']);

        // Clean up the research queue item on the second planet.
        DB::table('research_queues')->where('planet_id', $this->secondPlanetService->getPlanetId())->delete();
    }

    /**
     * Test that a foreign fleet mission returns home after the target planet is relocated.
     */
    public function testForeignFleetReturnsAfterRelocation(): void
    {
        $originalCoordinates = $this->planetService->getPlanetCoordinates();

        // Get a foreign planet to use as the attacker origin.
        $foreignPlanet = $this->getNearbyForeignPlanet();
        $foreignPlayer = $foreignPlanet->getPlayer();

        $emptyCoordinate = $this->getNearbyEmptyCoordinate();

        // Schedule and process the relocation first (no foreign fleet yet).
        $move = $this->scheduleAndProcessMove($emptyCoordinate);
        $move->refresh();
        $this->assertTrue((bool) $move->processed);

        // Now create a foreign attack fleet mission that was targeting the OLD coordinates.
        // Simulate the scenario where the fleet was dispatched before relocation and
        // planet_id_to was nullified by the relocation process.
        $now = (int) Date::now()->timestamp;
        $attackMission = new FleetMission();
        $attackMission->user_id = $foreignPlayer->getId();
        $attackMission->planet_id_from = $foreignPlanet->getPlanetId();
        $attackMission->planet_id_to = null; // Nullified by relocation
        $attackMission->galaxy_from = $foreignPlanet->getPlanetCoordinates()->galaxy;
        $attackMission->system_from = $foreignPlanet->getPlanetCoordinates()->system;
        $attackMission->position_from = $foreignPlanet->getPlanetCoordinates()->position;
        $attackMission->type_from = PlanetType::Planet->value;
        $attackMission->galaxy_to = $originalCoordinates->galaxy;
        $attackMission->system_to = $originalCoordinates->system;
        $attackMission->position_to = $originalCoordinates->position;
        $attackMission->type_to = PlanetType::Planet->value;
        $attackMission->mission_type = 1; // Attack
        $attackMission->time_departure = $now;
        $attackMission->time_arrival = $now + 7200; // Arrives in 2 hours
        $attackMission->metal = 0;
        $attackMission->crystal = 0;
        $attackMission->deuterium = 0;
        $attackMission->deuterium_consumption = 0;
        $attackMission->processed = 0;
        $attackMission->canceled = 0;
        $attackMission->light_fighter = 10;
        $attackMission->save();

        // Advance time past the attack fleet's arrival.
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 1));

        // Trigger fleet processing for the foreign player (the attacker).
        // The mission is found via planet_id_from which belongs to the foreign player.
        $foreignPlayer->updateFleetMissions();

        // Verify the attack mission was processed (marked as completed) without executing an attack.
        $attackMission->refresh();
        $this->assertEquals(1, $attackMission->processed, 'Foreign attack mission should be marked as processed.');

        // Verify a return mission was created for the foreign fleet.
        $returnMission = FleetMission::where('parent_id', $attackMission->id)
            ->where('user_id', $foreignPlayer->getId())
            ->first();
        $this->assertNotNull($returnMission, 'A return mission should be created for the foreign fleet.');
        $this->assertEquals($foreignPlanet->getPlanetId(), $returnMission->planet_id_to, 'Return mission should target the foreign player\'s planet.');
    }
}
