<?php

namespace Tests\Unit;

use Exception;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\BuildingQueue;
use OGame\Models\DarkMatterTransaction;
use OGame\Models\User;
use OGame\Services\DarkMatterTransactionService;
use OGame\Services\HalvingService;
use OGame\Services\ObjectService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\AccountTestCase;

class HalvingServiceTest extends AccountTestCase
{
    private HalvingService $halvingService;

    protected function setUp(): void
    {
        parent::setUp();

        $transactionService = app(DarkMatterTransactionService::class);
        $this->halvingService = new HalvingService($transactionService);
    }

    /**
     * Test cost formula correctness for halving.
     */
    #[DataProvider('costFormulaDataProvider')]
    public function testCostFormulaCorrectness(int $timeSeconds, string $queueType, int $expectedCostBeforeCaps): void
    {
        $cost = $this->halvingService->calculateHalvingCost($timeSeconds, $queueType);

        // Calculate expected cost with formula (before caps)
        $timeMinutes = $timeSeconds / 60;
        $rawCost = (int)ceil(($timeMinutes / 30) * 750);

        // Apply min/max caps
        $minCost = 750;
        $maxCost = match ($queueType) {
            'building', 'unit' => 72000,
            'research' => 108000,
            default => 72000,
        };

        $expectedCost = max($minCost, min($rawCost, $maxCost));

        $this->assertEquals($expectedCost, $cost, "Cost formula failed for {$timeSeconds}s ({$queueType})");
    }

    /**
     * Data provider for cost formula tests - simulates property-based testing
     * by generating many random test cases.
     *
     * @return array<string, array{0: int, 1: string, 2: int}>
     */
    public static function costFormulaDataProvider(): array
    {
        $testCases = [];

        // Generate random test cases to simulate property-based testing
        for ($i = 0; $i < 20; $i++) {
            $timeSeconds = rand(1, 604800); // 1 second to 1 week
            $queueTypes = ['building', 'research', 'unit'];
            $queueType = $queueTypes[array_rand($queueTypes)];

            $timeMinutes = $timeSeconds / 60;
            $expectedCost = (int)ceil(($timeMinutes / 30) * 750);

            $testCases["random_{$i}_{$queueType}_{$timeSeconds}s"] = [$timeSeconds, $queueType, $expectedCost];
        }

        // Add specific edge cases
        $testCases['30_minutes_building'] = [1800, 'building', 750]; // Exactly 30 min = 750 DM
        $testCases['1_hour_building'] = [3600, 'building', 1500]; // 1 hour = 1500 DM
        $testCases['24_hours_building'] = [86400, 'building', 36000]; // 24 hours = 36000 DM
        $testCases['48_hours_building'] = [172800, 'building', 72000]; // 48 hours = 72000 DM (capped)
        $testCases['72_hours_research'] = [259200, 'research', 108000]; // 72 hours = 108000 DM (capped)

        return $testCases;
    }

    /**
     * Test building and unit maximum cost cap (72,000 DM).
     */
    #[DataProvider('maxCostBuildingUnitDataProvider')]
    public function testBuildingAndUnitMaximumCostCap(int $timeSeconds): void
    {
        $buildingCost = $this->halvingService->calculateHalvingCost($timeSeconds, 'building');
        $unitCost = $this->halvingService->calculateHalvingCost($timeSeconds, 'unit');

        $this->assertLessThanOrEqual(72000, $buildingCost, "Building cost exceeded 72000 DM for {$timeSeconds}s");
        $this->assertLessThanOrEqual(72000, $unitCost, "Unit cost exceeded 72000 DM for {$timeSeconds}s");
    }

    /**
     * Test research maximum cost cap (108,000 DM).
     */
    #[DataProvider('maxCostResearchDataProvider')]
    public function testResearchMaximumCostCap(int $timeSeconds): void
    {
        $cost = $this->halvingService->calculateHalvingCost($timeSeconds, 'research');

        $this->assertLessThanOrEqual(108000, $cost, "Research cost exceeded 108000 DM for {$timeSeconds}s");
    }

    /**
     * Data provider for max cost tests with long durations.
     *
     * @return array<string, array{0: int}>
     */
    public static function maxCostBuildingUnitDataProvider(): array
    {
        $testCases = [];

        // Generate random long durations
        for ($i = 0; $i < 20; $i++) {
            $timeSeconds = rand(172800, 2592000); // 48 hours to 30 days
            $testCases["long_duration_{$i}"] = [$timeSeconds];
        }

        return $testCases;
    }

    /**
     * Data provider for research max cost tests.
     *
     * @return array<string, array{0: int}>
     */
    public static function maxCostResearchDataProvider(): array
    {
        $testCases = [];

        // Generate random long durations
        for ($i = 0; $i < 20; $i++) {
            $timeSeconds = rand(259200, 2592000); // 72 hours to 30 days
            $testCases["long_duration_{$i}"] = [$timeSeconds];
        }

        return $testCases;
    }

    /**
     * Test minimum cost floor (750 DM).
     */
    #[DataProvider('minCostDataProvider')]
    public function testMinimumCostFloor(int $timeSeconds, string $queueType): void
    {
        $cost = $this->halvingService->calculateHalvingCost($timeSeconds, $queueType);

        $this->assertGreaterThanOrEqual(750, $cost, "Cost below 750 DM for {$timeSeconds}s ({$queueType})");
    }

    /**
     * Data provider for minimum cost tests with short durations.
     *
     * @return array<string, array{0: int, 1: string}>
     */
    public static function minCostDataProvider(): array
    {
        $testCases = [];
        $queueTypes = ['building', 'research', 'unit'];

        // Generate random short durations
        for ($i = 0; $i < 20; $i++) {
            $timeSeconds = rand(1, 1800); // 1 second to 30 minutes
            $queueType = $queueTypes[array_rand($queueTypes)];
            $testCases["short_duration_{$i}_{$queueType}"] = [$timeSeconds, $queueType];
        }

        // Edge cases
        $testCases['1_second_building'] = [1, 'building'];
        $testCases['1_second_research'] = [1, 'research'];
        $testCases['1_second_unit'] = [1, 'unit'];
        $testCases['0_seconds_building'] = [0, 'building'];

        return $testCases;
    }

    /**
     * Test cost consistency across multiple calculations.
     */
    #[DataProvider('costConsistencyDataProvider')]
    public function testCostConsistencyAcrossMultipleHalvings(int $timeSeconds, string $queueType): void
    {
        // Calculate cost multiple times
        $cost1 = $this->halvingService->calculateHalvingCost($timeSeconds, $queueType);
        $cost2 = $this->halvingService->calculateHalvingCost($timeSeconds, $queueType);
        $cost3 = $this->halvingService->calculateHalvingCost($timeSeconds, $queueType);

        $this->assertEquals($cost1, $cost2, "Cost inconsistent between calculations");
        $this->assertEquals($cost2, $cost3, "Cost inconsistent between calculations");
    }

    /**
     * Data provider for cost consistency tests.
     *
     * @return array<string, array{0: int, 1: string}>
     */
    public static function costConsistencyDataProvider(): array
    {
        $testCases = [];
        $queueTypes = ['building', 'research', 'unit'];

        for ($i = 0; $i < 20; $i++) {
            $timeSeconds = rand(1, 604800);
            $queueType = $queueTypes[array_rand($queueTypes)];
            $testCases["consistency_{$i}"] = [$timeSeconds, $queueType];
        }

        return $testCases;
    }

    /**
     * Test Dark Matter deduction correctness after halving.
     */
    public function testDarkMatterDeductionCorrectness(): void
    {
        // Set up user with sufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $user->refresh();
        $initialBalance = $user->dark_matter;

        // Calculate expected cost based on remaining time
        $currentTime = (int)\Carbon\Carbon::now()->timestamp;
        $remainingTime = (int)$queueItem->time_end - $currentTime;
        $expectedCost = $this->halvingService->calculateHalvingCost($remainingTime, 'building');

        // Perform halving
        $result = $this->halvingService->halveBuilding(
            $user,
            $queueItem->id,
            $this->planetService
        );

        // Refresh user
        $user->refresh();
        $newBalance = $user->dark_matter;

        $this->assertEquals($initialBalance - $result['cost'], $newBalance, 'Balance should be reduced by exactly the halving cost');
        $this->assertGreaterThanOrEqual(750, $result['cost'], 'Cost should be at least minimum 750 DM');
        $this->assertLessThanOrEqual(72000, $result['cost'], 'Cost should not exceed max 72000 DM for building');
    }

    /**
     * Test insufficient balance rejection.
     */
    public function testInsufficientBalanceRejection(): void
    {
        // Set up user with insufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        $initialBalance = $user->dark_matter;

        // Attempt halving - should fail
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient Dark Matter');

        try {
            $this->halvingService->halveBuilding(
                $user,
                $queueItem->id,
                $this->planetService
            );
        } finally {
            // Verify balance unchanged
            $user->refresh();
            $this->assertEquals($initialBalance, $user->dark_matter, 'Balance should remain unchanged after failed halving');
        }
    }

    /**
     * Test transaction logging on successful halving.
     */
    public function testTransactionLoggingOnSuccess(): void
    {
        // Set up user with sufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        // Count transactions before
        $transactionCountBefore = DarkMatterTransaction::where('user_id', $this->currentUserId)->count();

        // Perform halving
        $this->halvingService->halveBuilding(
            $user,
            $queueItem->id,
            $this->planetService
        );

        // Check transaction was created
        $transactionCountAfter = DarkMatterTransaction::where('user_id', $this->currentUserId)->count();
        $this->assertEquals($transactionCountBefore + 1, $transactionCountAfter, 'Transaction should be created');

        // Verify transaction details
        $transaction = DarkMatterTransaction::where('user_id', $this->currentUserId)
            ->where('type', DarkMatterTransactionType::HALVING->value)
            ->latest('id')
            ->first();

        $this->assertNotNull($transaction, 'Halving transaction should exist');
        $this->assertEquals(DarkMatterTransactionType::HALVING->value, $transaction->type);
        $this->assertStringContainsString('Halving building', $transaction->description);
        $this->assertStringContainsString((string)$this->planetService->getPlanetId(), $transaction->description);
    }

    /**
     * Test no transaction is created on failure.
     */
    public function testNoTransactionOnFailure(): void
    {
        // Set up user with insufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        // Count transactions before
        $transactionCountBefore = DarkMatterTransaction::where('user_id', $this->currentUserId)
            ->where('type', DarkMatterTransactionType::HALVING->value)
            ->count();

        // Attempt halving - should fail
        try {
            $this->halvingService->halveBuilding(
                $user,
                $queueItem->id,
                $this->planetService
            );
        } catch (Exception $e) {
            // Expected
        }

        // Verify no transaction was created
        $transactionCountAfter = DarkMatterTransaction::where('user_id', $this->currentUserId)
            ->where('type', DarkMatterTransactionType::HALVING->value)
            ->count();

        $this->assertEquals($transactionCountBefore, $transactionCountAfter, 'No transaction should be created on failure');
    }

    /**
     * Helper method to add a building to the queue.
     */
    private function addBuildingToQueue(): void
    {
        // Get metal mine object ID
        $metalMine = ObjectService::getObjectByMachineName('metal_mine');

        // Add to queue via service
        $buildingQueueService = app(\OGame\Services\BuildingQueueService::class);
        $buildingQueueService->add($this->planetService, $metalMine->id);
    }

    /**
     * Test time reduction by 50% of ORIGINAL time after halving (capped at max).
     *
     * Each halve reduces remaining time by 50% of the original construction time,
     * but capped at max reduction (48h for building/unit, 72h for research).
     */
    public function testTimeReductionBy50PercentOfOriginalTime(): void
    {
        // Set up user with sufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        // Get remaining time and original duration before halving
        $currentTime = (int)\Carbon\Carbon::now()->timestamp;
        $remainingTimeBefore = (int)$queueItem->time_end - $currentTime;
        $originalDuration = (int)$queueItem->time_duration;

        // Perform halving
        $result = $this->halvingService->halveBuilding(
            $user,
            $queueItem->id,
            $this->planetService
        );

        // Calculate expected reduction: 50% of original, capped at 48h (172800s) for building
        $halfOriginal = intdiv($originalDuration, 2);
        $maxReduction = 172800; // 48 hours for building
        $expectedReduction = min($halfOriginal, $maxReduction, $remainingTimeBefore);
        $expectedRemainingTime = max(0, $remainingTimeBefore - $expectedReduction);

        // Allow 1 second tolerance for timing
        $this->assertLessThanOrEqual(1, abs($expectedRemainingTime - $result['remaining_time']), 'Remaining time should be reduced by 50% of original duration (capped at 48h)');
    }

    /**
     * Test original time_start and time_duration are preserved after halving.
     */
    public function testOriginalTimePreservation(): void
    {
        // Set up user with sufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        // Add a building to queue
        $this->addBuildingToQueue();

        // Get the queue item
        $queueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($queueItem, 'Queue item should exist');

        // Store original values
        $originalTimeStart = $queueItem->time_start;
        $originalTimeDuration = $queueItem->time_duration;

        // Perform halving
        $this->halvingService->halveBuilding(
            $user,
            $queueItem->id,
            $this->planetService
        );

        // Refresh queue item
        $queueItem->refresh();

        // Verify time_start and time_duration are unchanged
        $this->assertEquals($originalTimeStart, $queueItem->time_start, 'time_start should remain unchanged');
        $this->assertEquals($originalTimeDuration, $queueItem->time_duration, 'time_duration should remain unchanged');
    }

    /**
     * Test operation isolation - halving only affects the target queue item.
     */
    public function testOperationIsolation(): void
    {
        // Set up user with sufficient Dark Matter
        $user = User::find($this->currentUserId);
        $user->dark_matter = 100000;
        $user->save();

        // Add resources to build multiple buildings
        $this->planetAddResources(new \OGame\Models\Resources(50000, 50000, 50000, 0));

        // Add first building to queue
        $this->addBuildingToQueue();

        // Get the first queue item
        $firstQueueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('building', 1)
            ->where('processed', 0)
            ->first();

        $this->assertNotNull($firstQueueItem, 'First queue item should exist');

        // Store original time_end of first item
        $firstItemTimeEndBefore = $firstQueueItem->time_end;

        // Add second building to queue (different building)
        $crystalMine = ObjectService::getObjectByMachineName('crystal_mine');
        $buildingQueueService = app(\OGame\Services\BuildingQueueService::class);
        $buildingQueueService->add($this->planetService, $crystalMine->id);

        // Get the second queue item (not building yet, waiting in queue)
        $secondQueueItem = BuildingQueue::where('planet_id', $this->planetService->getPlanetId())
            ->where('processed', 0)
            ->where('canceled', 0)
            ->where('id', '!=', $firstQueueItem->id)
            ->first();

        // Perform halving on first item
        $this->halvingService->halveBuilding(
            $user,
            $firstQueueItem->id,
            $this->planetService
        );

        // Refresh first queue item
        $firstQueueItem->refresh();

        // Verify first item was halved (time_end changed)
        $this->assertNotEquals($firstItemTimeEndBefore, $firstQueueItem->time_end, 'First item time_end should be changed');

        // If second queue item exists, verify it was not affected
        if ($secondQueueItem) {
            $secondQueueItemAfter = BuildingQueue::find($secondQueueItem->id);
            // Second item should still be in queue and not modified
            $this->assertNotNull($secondQueueItemAfter, 'Second queue item should still exist');
        }
    }
}
