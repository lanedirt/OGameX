<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\PlanetMove;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuildingQueueService;
use OGame\Services\DarkMatterService;
use OGame\Services\FleetMissionService;
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
     * Test that a move executes correctly after the countdown expires.
     */
    public function testRelocationCountdownExpiresAndMoves(): void
    {
        $originalCoordinates = $this->planetService->getPlanetCoordinates();
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

        // Trigger processing by calling processDueMoves.
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
}
