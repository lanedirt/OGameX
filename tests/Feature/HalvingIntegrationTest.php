<?php

namespace Tests\Feature;

use OGame\Models\BuildingQueue;
use OGame\Models\DarkMatterTransaction;
use OGame\Models\ResearchQueue;
use OGame\Models\UnitQueue;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Integration tests for the Dark Matter halving feature.
 */
class HalvingIntegrationTest extends AccountTestCase
{
    /**
     * Test building halving end-to-end workflow.
     */
    public function testBuildingHalvingEndToEnd(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $this->addResourceBuildRequest('metal_mine');

        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $initialBalance = $user->dark_matter;
        $initialTimeEnd = $queueItem->time_end;

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertTrue($responseData['success'], 'Halving should succeed');
        $this->assertArrayHasKey('new_time_end', $responseData);
        $this->assertArrayHasKey('cost', $responseData);
        $this->assertArrayHasKey('new_balance', $responseData);

        $user->refresh();
        $this->assertLessThan($initialBalance, $user->dark_matter, 'Dark Matter should be deducted');

        $queueItem->refresh();
        $this->assertLessThan($initialTimeEnd, $queueItem->time_end, 'time_end should be reduced');

        $transaction = DarkMatterTransaction::where('user_id', $this->currentUserId)
            ->where('type', 'halving')
            ->latest('id')
            ->first();

        $this->assertNotNull($transaction, 'Transaction should be logged');
    }

    /**
     * Test research halving end-to-end workflow.
     */
    public function testResearchHalvingEndToEnd(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $this->planetSetObjectLevel('research_lab', 1);
        $this->planetAddResources(new \OGame\Models\Resources(10000, 10000, 10000, 0));
        $this->addResearchBuildRequest('energy_technology');

        $queueItem = ResearchQueue::query()
            ->join('planets', 'research_queues.planet_id', '=', 'planets.id')
            ->where('planets.user_id', $this->currentUserId)
            ->where('research_queues.building', 1)
            ->where('research_queues.processed', 0)
            ->select('research_queues.*')
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $initialBalance = $user->dark_matter;
        $initialTimeEnd = $queueItem->time_end;

        $response = $this->post('/ajax/research/halve-research', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertTrue($responseData['success'], 'Halving should succeed');

        $user->refresh();
        $this->assertLessThan($initialBalance, $user->dark_matter, 'Dark Matter should be deducted');

        $queueItem = ResearchQueue::find($queueItem->id);
        $this->assertLessThan($initialTimeEnd, $queueItem->time_end, 'time_end should be reduced');
    }

    /**
     * Test unit halving end-to-end workflow.
     *
     * Halving removes 50% of original duration from remaining time and instantly
     * awards the corresponding units. object_amount is preserved; progress and
     * dm_halved flag are updated. Time per unit rate stays the same.
     */
    public function testUnitHalvingEndToEnd(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $this->planetSetObjectLevel('robot_factory', 2);
        $this->planetSetObjectLevel('shipyard', 2);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddResources(new \OGame\Models\Resources(50000, 50000, 50000, 0));
        $this->addShipyardBuildRequest('light_fighter', 10);

        $queueItem = UnitQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $initialBalance = $user->dark_matter;
        $initialObjectAmount = (int)$queueItem->object_amount;
        $initialTimePerUnit = ((int)$queueItem->time_end - (int)$queueItem->time_start) / $initialObjectAmount;

        // Count light fighters on planet before halving
        $this->planetService->reloadPlanet();
        $fightersBefore = $this->planetService->getObjectAmount('light_fighter');

        $response = $this->post('/ajax/shipyard/halve-unit', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertTrue($responseData['success'], 'Halving should succeed');

        $user->refresh();
        $this->assertLessThan($initialBalance, $user->dark_matter, 'Dark Matter should be deducted');

        // Verify queue state: object_amount unchanged, progress updated, dm_halved set
        $queueItem->refresh();
        $expectedAwarded = 5; // floor(50% of 10-unit duration / time_per_unit) = 5

        $this->assertEquals($initialObjectAmount, (int)$queueItem->object_amount, 'object_amount should remain unchanged');
        $this->assertEquals($expectedAwarded, (int)$queueItem->object_amount_progress, 'Progress should reflect instantly awarded units');
        $this->assertEquals(1, (int)$queueItem->dm_halved, 'dm_halved flag should be set');

        // Verify time per unit is preserved (both time_start and time_end shifted equally)
        $newTimePerUnit = ((int)$queueItem->time_end - (int)$queueItem->time_start) / (int)$queueItem->object_amount;
        $this->assertEqualsWithDelta($initialTimePerUnit, $newTimePerUnit, 1, 'Time per unit should remain the same');

        // Verify units were awarded to the planet
        $this->planetService->reloadPlanet();
        $fightersAfter = $this->planetService->getObjectAmount('light_fighter');
        $this->assertEquals($fightersBefore + $expectedAwarded, $fightersAfter, 'Half the units should be awarded instantly');
    }

    /**
     * Test double halving completes task immediately.
     */
    /**
     * Test double halving for short duration tasks completes instantly.
     *
     * Each halve reduces remaining time by 50% of ORIGINAL construction time,
     * capped at max reduction (48h for building).
     */
    public function testDoubleHalvingCompletesTask(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 200000;
        $user->save();

        $this->addResourceBuildRequest('metal_mine');

        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $originalDuration = (int)$queueItem->time_duration;

        // First halve - reduces by min(50% of original, 48h, remaining)
        $response1 = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response1->assertStatus(200);
        $this->assertTrue($response1->json('success'), 'First halving should succeed');

        $remainingAfterFirst = $response1->json('remaining_time');

        // If task is short enough (original <= 96h), second halve should complete it
        if ($originalDuration <= 345600) { // 96 hours in seconds
            // Second halve should complete the task
            $response2 = $this->post('/ajax/facilities/halve-building', [
                '_token' => csrf_token(),
                'queue_item_id' => $queueItem->id,
            ]);

            $response2->assertStatus(200);
            $responseData = $response2->json();

            $this->assertTrue($responseData['success'], 'Second halving should succeed');
            $this->assertLessThanOrEqual(1, $responseData['remaining_time'], 'Remaining time should be near zero after double halving');
        } else {
            // For very long tasks, just verify first halve reduced time
            $this->assertLessThan($originalDuration, $remainingAfterFirst, 'First halve should reduce remaining time');
        }
    }

    /**
     * Test halving with insufficient Dark Matter returns error.
     */
    public function testHalvingWithInsufficientDarkMatter(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100;
        $user->save();

        $this->addResourceBuildRequest('metal_mine');

        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertFalse($responseData['success'], 'Halving should fail');
        $this->assertStringContainsString('Insufficient Dark Matter', $responseData['message']);
    }

    /**
     * Test halving with invalid queue item ID returns error.
     */
    public function testHalvingWithInvalidQueueItemId(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => 999999,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertFalse($responseData['success'], 'Halving should fail');
        $this->assertStringContainsString('not found', $responseData['message']);
    }

    /**
     * Test halving queue item with very short remaining time.
     */
    public function testHalvingWithVeryShortRemainingTime(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $this->addResourceBuildRequest('metal_mine');

        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $queueItem->time_end = (int)\Carbon\Carbon::now()->timestamp + 2;
        $queueItem->save();

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertTrue($responseData['success'], 'Halving should succeed');
        $this->assertLessThanOrEqual(1, $responseData['remaining_time'], 'Remaining time should be very small');
    }

    /**
     * Test halving with exactly the required Dark Matter balance.
     */
    public function testHalvingWithExactlyRequiredBalance(): void
    {
        $this->addResourceBuildRequest('metal_mine');

        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $halvingService = app(\OGame\Services\HalvingService::class);
        $cost = $halvingService->calculateHalvingCost($queueItem->time_duration, 'building');

        $user = User::find($this->currentUserId);
        $user->dark_matter = $cost;
        $user->save();

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertTrue($responseData['success'], 'Halving should succeed with exact balance');
        $this->assertEquals(0, $responseData['new_balance'], 'Balance should be exactly 0 after halving');
    }

    /**
     * Test error cases for invalid queue items.
     */
    public function testHalvingInvalidQueueItems(): void
    {
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        $initialBalance = $user->dark_matter;

        // Test non-existent queue item
        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => 999999,
        ]);

        $response->assertStatus(200);
        $this->assertFalse($response->json('success'), 'Should fail for non-existent queue item');

        $user->refresh();
        $this->assertEquals($initialBalance, $user->dark_matter, 'Dark Matter should not be deducted');

        // Test already processed queue item
        $this->addResourceBuildRequest('metal_mine');
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $queueItem->processed = 1;
        $queueItem->save();

        $response = $this->post('/ajax/facilities/halve-building', [
            '_token' => csrf_token(),
            'queue_item_id' => $queueItem->id,
        ]);

        $response->assertStatus(200);
        $this->assertFalse($response->json('success'), 'Should fail for processed queue item');

        $user->refresh();
        $this->assertEquals($initialBalance, $user->dark_matter, 'Dark Matter should not be deducted');
    }
}
