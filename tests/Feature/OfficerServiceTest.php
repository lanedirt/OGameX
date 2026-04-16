<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use OGame\Enums\DarkMatterTransactionType;
use OGame\Models\Officer;
use OGame\Models\User;
use OGame\Services\DarkMatterService;
use OGame\Services\OfficerService;
use Tests\AccountTestCase;

/**
 * Tests for the Officers / Premium feature.
 *
 * Covers:
 * - Officer activation (new + extension)
 * - all_officers bundle activates all five individual officers
 * - Officer expiry is correctly detected
 * - Dark Matter is debited on purchase
 * - Insufficient Dark Matter is rejected
 * - Bonus helper methods return correct values
 * - Commanding Staff bonus requires all five officers active
 * - getKeyFromTypeId mapping
 * - getOfficer guard for user_id = 0
 */
class OfficerServiceTest extends AccountTestCase
{
    private OfficerService $officerService;
    private DarkMatterService $darkMatterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->officerService   = resolve(OfficerService::class);
        $this->darkMatterService = resolve(DarkMatterService::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function currentUser(): User
    {
        return User::findOrFail($this->currentUserId);
    }

    /** Credit Dark Matter directly to the test user. */
    private function addDarkMatter(int $amount): void
    {
        DB::table('users')
            ->where('id', $this->currentUserId)
            ->increment('dark_matter', $amount);
    }

    /** Remove all Dark Matter from the test user. */
    private function clearDarkMatter(): void
    {
        DB::table('users')
            ->where('id', $this->currentUserId)
            ->update(['dark_matter' => 0]);
    }

    /** Force an officer's `_until` column to the past so it appears expired. */
    private function expireOfficer(string $key): void
    {
        $officer = Officer::where('user_id', $this->currentUserId)->first();
        if ($officer) {
            $officer->{$key . '_until'} = now()->subMinute();
            $officer->save();
        }
        $this->officerService->clearCache($this->currentUser());
    }

    // ── TYPE MAP ─────────────────────────────────────────────────────────────

    public function testGetKeyFromTypeIdReturnsCorrectKeys(): void
    {
        $this->assertSame('commander',    $this->officerService->getKeyFromTypeId(2));
        $this->assertSame('admiral',      $this->officerService->getKeyFromTypeId(3));
        $this->assertSame('engineer',     $this->officerService->getKeyFromTypeId(4));
        $this->assertSame('geologist',    $this->officerService->getKeyFromTypeId(5));
        $this->assertSame('technocrat',   $this->officerService->getKeyFromTypeId(6));
        $this->assertSame('all_officers', $this->officerService->getKeyFromTypeId(12));
    }

    public function testGetKeyFromTypeIdReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->officerService->getKeyFromTypeId(99));
    }

    // ── GUARD: user_id = 0 ───────────────────────────────────────────────────

    public function testGetOfficerWithZeroUserIdReturnsUnsavedModel(): void
    {
        $fakeUser = new User();
        $fakeUser->id = 0;

        $officer = $this->officerService->getOfficer($fakeUser);

        $this->assertInstanceOf(Officer::class, $officer);
        $this->assertFalse($officer->exists, 'getOfficer(user_id=0) must return an unsaved Officer, not a DB record.');
    }

    // ── ACTIVATION ───────────────────────────────────────────────────────────

    public function testOfficerNotActiveByDefault(): void
    {
        $user = $this->currentUser();

        foreach (['commander', 'admiral', 'engineer', 'geologist', 'technocrat'] as $key) {
            $this->assertFalse(
                $this->officerService->isActive($user, $key),
                "Officer '{$key}' should not be active before any purchase."
            );
        }
    }

    public function testPurchaseActivatesOfficer(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();

        $this->officerService->purchase($user, 'commander', 7);

        $this->assertTrue($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseDebitsDarkMatter(): void
    {
        $this->addDarkMatter(20000);
        $user   = $this->currentUser();
        $before = $user->dark_matter;

        $this->officerService->purchase($user, 'commander', 7);

        $user->refresh();
        $cost = OfficerService::COSTS['commander'][7];
        $this->assertSame($before - $cost, $user->dark_matter);
    }

    public function testPurchaseExtendsDurationWhenAlreadyActive(): void
    {
        $this->addDarkMatter(30000);
        $user = $this->currentUser();

        $this->officerService->purchase($user, 'engineer', 7);
        $officer = Officer::where('user_id', $this->currentUserId)->first();
        $firstExpiry = $officer->engineer_until->copy();

        // Buy another 7 days while still active → should extend from current expiry
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'engineer', 7);
        $officer->refresh();

        $this->assertTrue(
            $officer->engineer_until->greaterThan($firstExpiry),
            'Second purchase should push expiry further into the future.'
        );
        $this->assertEqualsWithDelta(
            $firstExpiry->addDays(7)->timestamp,
            $officer->engineer_until->timestamp,
            5
        );
    }

    public function testPurchaseFor91DaysActivatesOfficer(): void
    {
        $this->addDarkMatter(200000);
        $user = $this->currentUser();

        $this->officerService->purchase($user, 'geologist', 91);

        $officer = Officer::where('user_id', $this->currentUserId)->first();
        $this->assertTrue($officer->geologist_until->isFuture());
        $this->assertEqualsWithDelta(
            now()->addDays(91)->timestamp,
            $officer->geologist_until->timestamp,
            5
        );
    }

    // ── EXPIRY ───────────────────────────────────────────────────────────────

    public function testExpiredOfficerIsNotActive(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'technocrat', 7);

        $this->expireOfficer('technocrat');

        $user = $this->currentUser();
        $this->assertFalse($this->officerService->isActive($user, 'technocrat'));
    }

    // ── ALL_OFFICERS BUNDLE ───────────────────────────────────────────────────

    public function testAllOfficersBundleActivatesEveryOfficer(): void
    {
        $this->addDarkMatter(100000);
        $user = $this->currentUser();

        $this->officerService->purchase($user, 'all_officers', 7);

        foreach (['commander', 'admiral', 'engineer', 'geologist', 'technocrat'] as $key) {
            $this->assertTrue(
                $this->officerService->isActive($user, $key),
                "Officer '{$key}' should be active via all_officers bundle."
            );
        }
        $this->assertTrue($this->officerService->isActive($user, 'all_officers'));
    }

    public function testExpiredAllOfficersDeactivatesBundle(): void
    {
        $this->addDarkMatter(100000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'all_officers', 7);

        $this->expireOfficer('all_officers');

        $user = $this->currentUser();
        $this->assertFalse($this->officerService->isActive($user, 'all_officers'));
        // Individual officers not purchased directly should also be inactive
        $officer = $this->officerService->getOfficer($user);
        $this->assertFalse($officer->isOfficerActive('commander'));
    }

    // ── INSUFFICIENT DARK MATTER ──────────────────────────────────────────────

    public function testPurchaseFailsWhenInsufficientDarkMatter(): void
    {
        $this->clearDarkMatter();
        $user = $this->currentUser();

        $this->expectException(\Exception::class);
        $this->officerService->purchase($user, 'admiral', 7);
    }

    public function testOfficerNotActivatedOnFailedPurchase(): void
    {
        $this->clearDarkMatter();
        $user = $this->currentUser();

        try {
            $this->officerService->purchase($user, 'admiral', 7);
        } catch (\Exception) {
            // Expected
        }

        $this->assertFalse($this->officerService->isActive($user, 'admiral'));
    }

    // ── INVALID INPUTS ────────────────────────────────────────────────────────

    public function testPurchaseThrowsOnInvalidOfficerKey(): void
    {
        $this->addDarkMatter(50000);
        $user = $this->currentUser();

        $this->expectException(\Exception::class);
        $this->officerService->purchase($user, 'invalid_officer', 7);
    }

    public function testPurchaseThrowsOnInvalidDuration(): void
    {
        $this->addDarkMatter(50000);
        $user = $this->currentUser();

        $this->expectException(\Exception::class);
        $this->officerService->purchase($user, 'commander', 30);
    }

    // ── BONUS HELPERS ─────────────────────────────────────────────────────────

    public function testMineProductionBonusWithoutGeologist(): void
    {
        $user = $this->currentUser();
        $this->assertSame(1.0, $this->officerService->getMineProductionBonus($user));
    }

    public function testMineProductionBonusWithGeologist(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'geologist', 7);

        $this->assertSame(1.10, $this->officerService->getMineProductionBonus($user));
    }

    public function testEnergyProductionBonusWithoutEngineer(): void
    {
        $user = $this->currentUser();
        $this->assertSame(1.0, $this->officerService->getEnergyProductionBonus($user));
    }

    public function testEnergyProductionBonusWithEngineer(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'engineer', 7);

        $this->assertSame(1.10, $this->officerService->getEnergyProductionBonus($user));
    }

    public function testAdmiralFleetSlotsWithoutAdmiral(): void
    {
        $user = $this->currentUser();
        $this->assertSame(0, $this->officerService->getAdmiralFleetSlots($user));
    }

    public function testAdmiralFleetSlotsWithAdmiral(): void
    {
        $this->addDarkMatter(10000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'admiral', 7);

        $this->assertSame(2, $this->officerService->getAdmiralFleetSlots($user));
    }

    public function testAdditionalExpeditionSlotsWithAdmiral(): void
    {
        $this->addDarkMatter(10000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'admiral', 7);

        $this->assertSame(1, $this->officerService->getAdditionalExpeditionSlots($user));
    }

    public function testResearchTimeMultiplierWithoutTechnocrat(): void
    {
        $user = $this->currentUser();
        $this->assertSame(1.0, $this->officerService->getResearchTimeMultiplier($user));
    }

    public function testResearchTimeMultiplierWithTechnocrat(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'technocrat', 7);

        $this->assertSame(0.75, $this->officerService->getResearchTimeMultiplier($user));
    }

    public function testAdditionalEspionageLevelsWithTechnocrat(): void
    {
        $this->addDarkMatter(20000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'technocrat', 7);

        $this->assertSame(2, $this->officerService->getAdditionalEspionageLevels($user));
    }

    // ── COMMANDING STAFF ──────────────────────────────────────────────────────

    public function testCommandingStaffBonusRequiresAllFiveOfficers(): void
    {
        // Activate only 4 officers — bonus must NOT apply
        $this->addDarkMatter(100000);
        $user = $this->currentUser();
        foreach (['commander', 'admiral', 'engineer', 'geologist'] as $key) {
            $this->officerService->purchase($user, $key, 7);
        }

        $user = $this->currentUser();
        $this->assertSame(0, $this->officerService->getCommandingStaffFleetSlots($user));
        $this->assertSame(0, $this->officerService->getCommandingStaffEspionageLevels($user));
    }

    public function testCommandingStaffBonusWithAllFiveOfficers(): void
    {
        $this->addDarkMatter(100000);
        $user = $this->currentUser();
        foreach (['commander', 'admiral', 'engineer', 'geologist', 'technocrat'] as $key) {
            $this->officerService->purchase($user, $key, 7);
        }

        $user = $this->currentUser();
        $this->assertSame(1, $this->officerService->getCommandingStaffFleetSlots($user));
        $this->assertSame(1, $this->officerService->getCommandingStaffEspionageLevels($user));
    }

    public function testCommandingStaffBonusViaAllOfficersBundle(): void
    {
        $this->addDarkMatter(100000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'all_officers', 7);

        $user = $this->currentUser();
        $this->assertSame(1, $this->officerService->getCommandingStaffFleetSlots($user));
    }

    // ── GET COST ──────────────────────────────────────────────────────────────

    public function testGetCostReturnsCorrectValues(): void
    {
        $this->assertSame(10000,  $this->officerService->getCost('commander',    7));
        $this->assertSame(100000, $this->officerService->getCost('commander',   91));
        $this->assertSame(5000,   $this->officerService->getCost('admiral',      7));
        $this->assertSame(50000,  $this->officerService->getCost('admiral',     91));
        $this->assertSame(5000,   $this->officerService->getCost('engineer',     7));
        $this->assertSame(12500,  $this->officerService->getCost('geologist',    7));
        $this->assertSame(10000,  $this->officerService->getCost('technocrat',   7));
        $this->assertSame(42500,  $this->officerService->getCost('all_officers', 7));
        $this->assertSame(425000, $this->officerService->getCost('all_officers', 91));
    }

    public function testGetCostReturnsZeroForUnknownKey(): void
    {
        $this->assertSame(0, $this->officerService->getCost('unknown', 7));
    }

    // ── PURCHASE ENDPOINT ─────────────────────────────────────────────────────

    public function testPurchaseEndpointActivatesOfficerViaHttp(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->get('premium/purchase?type=2&days=7');

        // Should redirect back to premium page with success
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $user = $this->currentUser();
        $this->assertTrue($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInsufficientDarkMatter(): void
    {
        $this->clearDarkMatter();

        $response = $this->get('premium/purchase?type=2&days=7');

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $user = $this->currentUser();
        $this->assertFalse($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInvalidType(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->get('premium/purchase?type=99&days=7');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInvalidDuration(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->get('premium/purchase?type=2&days=30');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
