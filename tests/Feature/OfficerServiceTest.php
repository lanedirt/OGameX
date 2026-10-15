<?php

namespace Tests\Feature;

use Exception;
use Illuminate\Support\Facades\DB;
use OGame\Models\Officer;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuildingQueueService;
use OGame\Services\DarkMatterService;
use OGame\Services\ObjectService;
use OGame\Services\OfficerService;
use Tests\IsolatedAccountTestCase;

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
 * - Bonuses take effect at the call sites the game actually reads
 * - The Commander is what makes the building queue possible at all
 * - Unknown officer names never report as active
 * - getKeyFromTypeId mapping
 * - getOfficer guard for user_id = 0
 * - The purchase endpoint only accepts CSRF protected POST requests
 */
class OfficerServiceTest extends IsolatedAccountTestCase
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

    /** Credit Dark Matter directly to the test user and refresh the auth guard. */
    private function addDarkMatter(int $amount): void
    {
        DB::table('users')
            ->where('id', $this->currentUserId)
            ->increment('dark_matter', $amount);

        // Refresh the auth guard so Auth::user() in controllers reflects the new balance.
        $this->be(User::findOrFail($this->currentUserId));
    }

    /** Remove all Dark Matter from the test user and refresh the auth guard. */
    private function clearDarkMatter(): void
    {
        DB::table('users')
            ->where('id', $this->currentUserId)
            ->update(['dark_matter' => 0]);

        // Refresh the auth guard so Auth::user() in controllers reflects the new balance.
        $this->be(User::findOrFail($this->currentUserId));
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
        $this->assertSame('commander', $this->officerService->getKeyFromTypeId(2));
        $this->assertSame('admiral', $this->officerService->getKeyFromTypeId(3));
        $this->assertSame('engineer', $this->officerService->getKeyFromTypeId(4));
        $this->assertSame('geologist', $this->officerService->getKeyFromTypeId(5));
        $this->assertSame('technocrat', $this->officerService->getKeyFromTypeId(6));
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
        $officer = Officer::where('user_id', $this->currentUserId)->firstOrFail();
        assert($officer->engineer_until !== null);
        $firstExpiry = $officer->engineer_until->copy();

        // Buy another 7 days while still active → should extend from current expiry
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'engineer', 7);
        $officer->refresh();

        assert($officer->engineer_until !== null);
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

    public function testPurchaseFor90DaysActivatesOfficer(): void
    {
        $this->addDarkMatter(200000);
        $user = $this->currentUser();

        $this->officerService->purchase($user, 'geologist', 90);

        $officer = Officer::where('user_id', $this->currentUserId)->firstOrFail();
        assert($officer->geologist_until !== null);
        $this->assertTrue($officer->geologist_until->isFuture());
        $this->assertEqualsWithDelta(
            now()->addDays(90)->timestamp,
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

        $this->expectException(Exception::class);
        $this->officerService->purchase($user, 'admiral', 7);
    }

    public function testOfficerNotActivatedOnFailedPurchase(): void
    {
        $this->clearDarkMatter();
        $user = $this->currentUser();

        try {
            $this->officerService->purchase($user, 'admiral', 7);
        } catch (Exception) {
            // Expected
        }

        $this->assertFalse($this->officerService->isActive($user, 'admiral'));
    }

    // ── INVALID INPUTS ────────────────────────────────────────────────────────

    public function testPurchaseThrowsOnInvalidOfficerKey(): void
    {
        $this->addDarkMatter(50000);
        $user = $this->currentUser();

        $this->expectException(Exception::class);
        $this->officerService->purchase($user, 'invalid_officer', 7);
    }

    public function testPurchaseThrowsOnInvalidDuration(): void
    {
        $this->addDarkMatter(50000);
        $user = $this->currentUser();

        $this->expectException(Exception::class);
        $this->officerService->purchase($user, 'commander', 30);
    }

    // ── BONUS HELPERS ─────────────────────────────────────────────────────────

    public function testPlayerServiceReportsGeologistAfterPurchase(): void
    {
        $player = $this->planetService->getPlayer();
        assert($player !== null);

        $this->assertFalse($player->hasGeologist());

        $this->addDarkMatter(20000);
        $this->officerService->purchase($this->currentUser(), 'geologist', 7);

        $this->assertTrue($player->hasGeologist());
    }

    public function testPlayerServiceReportsEngineerAfterPurchase(): void
    {
        $player = $this->planetService->getPlayer();
        assert($player !== null);

        $this->assertFalse($player->hasEngineer());

        $this->addDarkMatter(20000);
        $this->officerService->purchase($this->currentUser(), 'engineer', 7);

        $this->assertTrue($player->hasEngineer());
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
        $this->assertSame(10000, $this->officerService->getCost('commander', 7));
        $this->assertSame(100000, $this->officerService->getCost('commander', 90));
        $this->assertSame(5000, $this->officerService->getCost('admiral', 7));
        $this->assertSame(50000, $this->officerService->getCost('admiral', 90));
        $this->assertSame(5000, $this->officerService->getCost('engineer', 7));
        $this->assertSame(12500, $this->officerService->getCost('geologist', 7));
        $this->assertSame(10000, $this->officerService->getCost('technocrat', 7));
        $this->assertSame(42500, $this->officerService->getCost('all_officers', 7));
        $this->assertSame(425000, $this->officerService->getCost('all_officers', 90));
    }

    public function testGetCostReturnsZeroForUnknownKey(): void
    {
        $this->assertSame(0, $this->officerService->getCost('unknown', 7));
    }

    // ── COMMANDER BUILDING QUEUE BONUS ───────────────────────────────────────

    public function testBuildingQueueHoldsOneItemWithoutCommander(): void
    {
        $queue = resolve(BuildingQueueService::class)->retrieveQueue($this->planetService);

        $this->assertSame(
            1,
            $queue->maxItemsInQueue,
            'Without the Commander a planet builds one structure at a time.'
        );
    }

    public function testBuildingQueueHoldsFiveItemsWithCommander(): void
    {
        $this->addDarkMatter(20000);
        $this->officerService->purchase($this->currentUser(), 'commander', 7);

        $queue = resolve(BuildingQueueService::class)->retrieveQueue($this->planetService);

        $this->assertSame(
            5,
            $queue->maxItemsInQueue,
            'The Commander allows 4 additional building contracts on top of the active one.'
        );
    }

    /**
     * Without the Commander a second building cannot be queued at all.
     */
    public function testSecondBuildingIsRejectedWithoutCommander(): void
    {
        $this->planetAddResources(new Resources(10000, 10000, 10000, 0));
        $queueService = resolve(BuildingQueueService::class);

        $queueService->add($this->planetService, ObjectService::getObjectByMachineName('metal_mine')->id);

        $this->expectException(Exception::class);
        $queueService->add($this->planetService, ObjectService::getObjectByMachineName('crystal_mine')->id);
    }

    /**
     * With the Commander a second building can be queued behind the active one.
     */
    public function testSecondBuildingIsAcceptedWithCommander(): void
    {
        $this->addDarkMatter(20000);
        $this->officerService->purchase($this->currentUser(), 'commander', 7);

        $this->planetAddResources(new Resources(10000, 10000, 10000, 0));
        $queueService = resolve(BuildingQueueService::class);

        $queueService->add($this->planetService, ObjectService::getObjectByMachineName('metal_mine')->id);
        $queueService->add($this->planetService, ObjectService::getObjectByMachineName('crystal_mine')->id);

        $this->assertSame(2, $queueService->retrieveQueue($this->planetService)->count());
    }

    // ── BONUSES AT THE CALL SITES ────────────────────────────────────────────

    public function testAdmiralIncreasesFleetSlotsMax(): void
    {
        $player = $this->planetService->getPlayer();
        assert($player !== null);
        $before = $player->getFleetSlotsMax();

        $this->addDarkMatter(10000);
        $this->officerService->purchase($this->currentUser(), 'admiral', 7);

        $this->assertSame(
            $before + 2,
            $player->getFleetSlotsMax(),
            'The Admiral should add 2 fleet slots to the in-game maximum.'
        );
    }

    public function testAdmiralIncreasesExpeditionSlotsMax(): void
    {
        $player = $this->planetService->getPlayer();
        assert($player !== null);
        $before = $player->getExpeditionSlotsMax();

        $this->addDarkMatter(10000);
        $this->officerService->purchase($this->currentUser(), 'admiral', 7);

        $this->assertSame(
            $before + 1,
            $player->getExpeditionSlotsMax(),
            'The Admiral should add 1 expedition slot to the in-game maximum.'
        );
    }

    public function testCommandingStaffAddsOneMoreFleetSlot(): void
    {
        $player = $this->planetService->getPlayer();
        assert($player !== null);
        $before = $player->getFleetSlotsMax();

        $this->addDarkMatter(100000);
        $this->officerService->purchase($this->currentUser(), 'all_officers', 7);

        $this->assertSame(
            $before + 3,
            $player->getFleetSlotsMax(),
            'The bundle should add the Admiral 2 slots plus the Commanding Staff slot.'
        );
    }

    public function testTechnocratReducesResearchTime(): void
    {
        $before = $this->planetService->getTechnologyResearchTime('energy_technology');

        $this->addDarkMatter(20000);
        $this->officerService->purchase($this->currentUser(), 'technocrat', 7);

        $after = $this->planetService->getTechnologyResearchTime('energy_technology');

        $this->assertSame(
            (float)(int)($before * 0.75),
            $after,
            'The Technocrat should cut 25% off the actual research time.'
        );
    }

    // ── UNKNOWN OFFICER NAMES ────────────────────────────────────────────────

    /**
     * An unknown officer name must never report as active, not even while the bundle runs.
     */
    public function testUnknownOfficerIsNeverActive(): void
    {
        $this->addDarkMatter(100000);
        $user = $this->currentUser();
        $this->officerService->purchase($user, 'all_officers', 7);

        $this->assertTrue($this->officerService->isActive($user, 'commander'));
        $this->assertFalse($this->officerService->isActive($user, 'not_an_officer'));
        $this->assertFalse($this->officerService->getOfficer($user)->isOfficerActive(''));
    }

    // ── PURCHASE ENDPOINT ─────────────────────────────────────────────────────

    /**
     * The purchase endpoint spends Dark Matter, so it must not be reachable with a
     * side-effect-free verb such as GET (which would also bypass CSRF protection).
     */
    public function testPurchaseEndpointRejectsGetRequests(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->get('premium/purchase?type=2&days=7');
        $response->assertStatus(405);

        $user = $this->currentUser();
        $this->assertFalse($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseEndpointActivatesOfficerViaHttp(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->post('premium/purchase', ['type' => 2, 'days' => 7]);

        // Should redirect back to premium page with success
        $response->assertRedirect();
        $response->assertSessionHas('status');

        $user = $this->currentUser();
        $this->assertTrue($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInsufficientDarkMatter(): void
    {
        $this->clearDarkMatter();

        $response = $this->post('premium/purchase', ['type' => 2, 'days' => 7]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $user = $this->currentUser();
        $this->assertFalse($this->officerService->isActive($user, 'commander'));
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInvalidType(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->post('premium/purchase', ['type' => 99, 'days' => 7]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function testPurchaseEndpointRedirectsWithErrorOnInvalidDuration(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->post('premium/purchase', ['type' => 2, 'days' => 30]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * The officer detail panel must submit the purchase through a POST form that carries
     * a CSRF token, instead of a plain link.
     */
    public function testOfficerDetailPanelRendersCsrfProtectedPurchaseForm(): void
    {
        $this->addDarkMatter(50000);

        $response = $this->get('ajax/premium?type=2');

        $response->assertStatus(200);
        $response->assertSee('method="POST"', false);
        $response->assertSee('name="_token"', false);
        $response->assertDontSee('premium/purchase?type=', false);
    }
}
