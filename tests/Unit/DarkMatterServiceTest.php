<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\User;
use OGame\Services\DarkMatterService;
use OGame\Services\DarkMatterTransactionService;
use OGame\Services\SettingsService;
use Tests\UnitTestCase;

class DarkMatterServiceTest extends UnitTestCase
{
    use RefreshDatabase;

    private DarkMatterService $darkMatterService;

    protected function setUp(): void
    {
        parent::setUp();

        $transactionService = app(DarkMatterTransactionService::class);
        $settingsService = app(SettingsService::class);
        $this->darkMatterService = new DarkMatterService($transactionService, $settingsService);
    }

    /**
     * Test that credit() increases user's Dark Matter balance.
     */
    public function testCreditIncreasesBalance(): void
    {
        $user = User::factory()->create();
        $user->refresh(); // Get the balance after initial bonus
        $initialBalance = $user->dark_matter;

        $this->darkMatterService->credit(
            $user,
            500,
            DarkMatterTransactionType::ADMIN_ADJUSTMENT->value,
            'Test credit'
        );

        $user->refresh();
        $this->assertEquals($initialBalance + 500, $user->dark_matter);
    }

    /**
     * Test that debit() decreases user's Dark Matter balance.
     */
    public function testDebitDecreasesBalance(): void
    {
        $user = User::factory()->create();
        $user->refresh(); // Get the balance after initial bonus
        $initialBalance = $user->dark_matter;

        $this->darkMatterService->debit(
            $user,
            300,
            DarkMatterTransactionType::ADMIN_ADJUSTMENT->value,
            'Test debit'
        );

        $user->refresh();
        $this->assertEquals($initialBalance - 300, $user->dark_matter);
    }

    /**
     * Test that debit() throws exception when balance is insufficient.
     */
    public function testDebitThrowsExceptionWhenInsufficientBalance(): void
    {
        $user = User::factory()->create();
        // Set balance to a low amount
        $user->dark_matter = 100;
        $user->save();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient Dark Matter');

        $this->darkMatterService->debit(
            $user,
            500,
            DarkMatterTransactionType::ADMIN_ADJUSTMENT->value,
            'Test debit'
        );
    }

    /**
     * Test that getBalance() returns correct balance.
     */
    public function testGetBalanceReturnsCorrectValue(): void
    {
        $user = User::factory()->create(['dark_matter' => 5000]);

        $balance = $this->darkMatterService->getBalance($user);

        $this->assertEquals(5000, $balance);
    }

    /**
     * Test that canAfford() returns true when user has enough Dark Matter.
     */
    public function testCanAffordReturnsTrueWhenSufficient(): void
    {
        $user = User::factory()->create(['dark_matter' => 1000]);

        $this->assertTrue($this->darkMatterService->canAfford($user, 500));
        $this->assertTrue($this->darkMatterService->canAfford($user, 1000));
    }

    /**
     * Test that canAfford() returns false when user doesn't have enough Dark Matter.
     */
    public function testCanAffordReturnsFalseWhenInsufficient(): void
    {
        $user = User::factory()->create(['dark_matter' => 100]);

        $this->assertFalse($this->darkMatterService->canAfford($user, 500));
    }

    /**
     * Test that calculateExpeditionReward() returns value within bounds.
     */
    public function testCalculateExpeditionRewardWithinBounds(): void
    {
        // Test with Pathfinder
        $rewardWithPathfinder = $this->darkMatterService->calculateExpeditionReward(true);
        $this->assertGreaterThanOrEqual(300, $rewardWithPathfinder);
        $this->assertLessThanOrEqual(400, $rewardWithPathfinder);

        // Test without Pathfinder
        $rewardWithoutPathfinder = $this->darkMatterService->calculateExpeditionReward(false);
        $this->assertGreaterThanOrEqual(150, $rewardWithoutPathfinder);
        $this->assertLessThanOrEqual(200, $rewardWithoutPathfinder);
    }

    /**
     * Test that calculateSpeedupCost() calculates correctly.
     */
    public function testCalculateSpeedupCost(): void
    {
        // 10 hours remaining, 1x speed: ceil((10 / 2) * 1) = 5 DM
        $cost = $this->darkMatterService->calculateSpeedupCost(36000, 1.0);
        $this->assertEquals(5, $cost);

        // 10 hours remaining, 2x speed: ceil((10 / 2) * 0.5) = 3 DM
        $cost = $this->darkMatterService->calculateSpeedupCost(36000, 2.0);
        $this->assertEquals(3, $cost);

        // 30 minutes remaining, 1x speed: minimum 1 DM
        $cost = $this->darkMatterService->calculateSpeedupCost(1800, 1.0);
        $this->assertEquals(1, $cost);
    }
}
