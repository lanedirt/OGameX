<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that multi-defender battles work as expected.
 * This tests the new functionality where multiple defending fleets (planet owner + ACS defend fleets)
 * can participate in a battle together, each using their own tech levels.
 */
class FleetDispatchMultiDefenderBattleTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test (Attack).
     */
    protected int $missionType = 1;

    /**
     * @var string The mission name for the test.
     */
    protected string $missionName = 'Attack';

    /**
     * @var PlanetService|null The buddy's planet (defender)
     */
    protected ?PlanetService $buddyPlanet = null;

    /**
     * @var User|null The buddy user (defender)
     */
    protected ?User $buddyUser = null;

    /**
     * @var array<int> Track all created buddy user IDs for cleanup
     */
    protected static array $allCreatedBuddyUserIds = [];

    /**
     * Set up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->buddyPlanet = null;
        $this->buddyUser = null;
    }

    /**
     * Clean up test data after each test.
     */
    protected function tearDown(): void
    {
        // Clean up buddy relationships
        while (!empty(self::$allCreatedBuddyUserIds)) {
            $buddyUserId = array_shift(self::$allCreatedBuddyUserIds);

            DB::table('buddy_requests')
                ->where(function ($query) use ($buddyUserId) {
                    $query->where('sender_user_id', $buddyUserId)
                        ->orWhere('receiver_user_id', $buddyUserId);
                })
                ->delete();
        }

        parent::tearDown();
    }

    /**
     * Prepare the attacker planet for testing.
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 100);
        $this->playerSetResearchLevel('computer_technology', 1);
        $this->playerSetResearchLevel('weapon_technology', 5);
        $this->playerSetResearchLevel('shielding_technology', 5);
        $this->playerSetResearchLevel('armor_technology', 5);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);

        $this->planetAddResources(new Resources(0, 0, 1000000, 0));
    }

    /**
     * Create a buddy player with a planet.
     */
    protected function createBuddyPlayer(): User
    {
        $buddyUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $buddyUser->id;

        $buddyPlanet = Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlayerService = resolve(PlayerService::class, ['player_id' => $buddyUser->id]);
        $this->buddyPlanet = $planetServiceFactory->makeForPlayer($buddyPlayerService, $buddyPlanet->id);
        $this->buddyUser = $buddyUser;

        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($this->currentUserId, $buddyUser->id);
        $buddyService->acceptRequest($request->id, $buddyUser->id);

        return $buddyUser;
    }

    /**
     * Create a third player (ACS defender) with a planet.
     */
    protected function createAcsDefender(): array
    {
        $defenderUser = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $defenderUser->id;

        $defenderPlanet = Planet::factory()->create([
            'user_id' => $defenderUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 6),
            'planet' => 9,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $defenderPlayerService = resolve(PlayerService::class, ['player_id' => $defenderUser->id]);
        $defenderPlanetService = $planetServiceFactory->makeForPlayer($defenderPlayerService, $defenderPlanet->id);

        // Create buddy relationship between defender and buddy (target planet owner)
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($defenderUser->id, $this->buddyUser->id);
        $buddyService->acceptRequest($request->id, $this->buddyUser->id);

        return [
            'user' => $defenderUser,
            'planet' => $defenderPlanetService,
        ];
    }

    protected function messageCheckMissionArrival(): void
    {
        $messageAttacker = Message::where('user_id', $this->planetService->getPlayer()->getId())
            ->whereIn('key', ['battle_report', 'fleet_lost_contact'])
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($messageAttacker, 'Attacker has not received a message after combat.');
    }

    protected function messageCheckMissionReturn(): void
    {
        $this->assertMessageReceivedAndContains('fleets', 'other', [
            'Your fleet is returning from',
            $this->planetService->getPlanetName(),
        ]);
    }

    /**
     * Test that an ACS Defend fleet participates in battle.
     */
    public function testAcsDefendFleetParticipatesInBattle(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Give buddy planet some defenses
        $this->buddyPlanet->addUnit('rocket_launcher', 10);

        // Create ACS defender and send defend fleet
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 20);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));

        // Send ACS defend fleet FIRST with 1 hour holding time
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $fleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5, // ACS Defend mission type
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours (need to ensure it's still there when attack arrives)
        );

        // Advance time for defend fleet to arrive and start holding
        // With the new architecture, time_arrival includes hold time, so calculate physical arrival
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();

        // Now send attack fleet - it should arrive while defend fleet is still holding
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 50);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time for attack to arrive (defend fleet should still be holding)
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        // Check that defender had more units than just the planet owner
        // Planet owner: 10 rocket launchers
        // ACS defender: 20 light fighters
        // Total should be 30+ units
        $defenderStartUnits = $battleReport->defender['units'];
        $totalDefenderUnits = array_sum($defenderStartUnits);
        $this->assertGreaterThan(25, $totalDefenderUnits, 'ACS defend fleet should participate in battle');
    }

    /**
     * Test that a completely destroyed ACS defend fleet does not return.
     */
    public function testDestroyedAcsDefendFleetDoesNotReturn(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Create ACS defender and send small defend fleet
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 5);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));

        // Send ACS defend fleet to buddy's planet
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $fleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Advance time so ACS defend fleet arrives (during hold period)
        // With the new architecture, time_arrival includes hold time, so calculate physical arrival
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send overwhelming attack to destroy everything
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time for attack to arrive
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Check that ACS defend mission is marked as processed
        $acsDefendMissionReloaded = FleetMission::find($acsDefendMission->id);
        $this->assertEquals(1, $acsDefendMissionReloaded->processed, 'Destroyed ACS defend fleet should be marked as processed');

        // Check that defender received fleet lost contact message
        $lostContactMessage = Message::where('user_id', $acsDefender['user']->id)
            ->where('key', 'fleet_lost_contact')
            ->orderByDesc('id')
            ->first();
        $this->assertNotNull($lostContactMessage, 'Defender should receive fleet lost contact message');

        // Advance time significantly to ensure no return mission would complete
        $this->travel(48)->hours();
        $this->reloadApplication();
        $this->get('/overview');

        // Reload planet and check units are still 0
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefenderPlanetReloaded = $planetServiceFactory->make($acsDefender['planet']->getPlanetId());
        $this->assertEquals(
            0,
            $acsDefenderPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter'),
            'Destroyed ACS defend fleet ships should not return'
        );
    }

    /**
     * Test that a surviving ACS defend fleet returns with correct units.
     *
     * With the fix: battle does NOT create a return mission. Instead, the outbound mission's
     * unit columns are updated with survivor counts. The single return mission is only created
     * when hold time expires (AcsDefendMission::processArrival).
     */
    public function testSurvivingAcsDefendFleetReturns(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Give buddy planet strong defenses to ensure defenders win
        $this->buddyPlanet->addUnit('rocket_launcher', 50);

        // Create ACS defender and send defend fleet
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 30);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));

        // Send ACS defend fleet
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 30);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $fleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Advance time so ACS defend fleet arrives (during hold period)
        // With the new architecture, time_arrival includes hold time, so calculate physical arrival
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send weak attack that will be defeated
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time for attack to arrive - battle occurs, outbound mission unit counts updated
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // After battle: NO return mission yet - fleet is still holding at target planet
        $returnMissionAfterBattle = FleetMission::where('parent_id', $acsDefendMission->id)->first();
        $this->assertNull($returnMissionAfterBattle, 'No return mission should exist immediately after battle - fleet continues holding');

        // Advance time past hold expiry - now AcsDefendMission::processArrival() creates the return mission
        $this->travelTo(Date::createFromTimestamp($acsDefendMission->time_arrival + 10));
        $this->reloadApplication();
        $acsDefenderPlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $acsDefenderPlayerService->updateFleetMissions();

        $returnMission = FleetMission::where('parent_id', $acsDefendMission->id)
            ->where('processed', 0)
            ->first();
        $this->assertNotNull($returnMission, 'ACS defend fleet should have a return mission after hold expires');

        // Advance time for return trip
        $this->travelTo(Date::createFromTimestamp($returnMission->time_arrival + 10));
        $this->reloadApplication();

        // Process missions for the ACS defender (return mission belongs to them)
        $playerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $playerService->updateFleetMissions();

        // Check that ships returned to ACS defender's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefenderPlanetReloaded = $planetServiceFactory->make($acsDefender['planet']->getPlanetId(), true);
        $returnedShips = $acsDefenderPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter');
        $this->assertGreaterThan(0, $returnedShips, 'Some ships should have returned');
    }

    /**
     * Regression test: ACS Defend fleet must NOT be duplicated when attacked during hold time.
     *
     * Bug: When an ACS Defend fleet was attacked during hold time, AttackMission::processArrival()
     * created a return mission with surviving units. However, the ACS defend outbound mission was
     * not marked as processed. When the hold time expired, AcsDefendMission::processArrival() would
     * then create a SECOND return mission (with the original full fleet), duplicating all ships.
     */
    public function testAcsDefendFleetNotDuplicatedWhenAttackedDuringHoldTime(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Create ACS defender with 100 light fighters (strong enough to survive 1 attacker LF)
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 100);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));
        $acsDefender['planet']->getPlayer()->setResearchLevel('weapon_technology', 10);
        $acsDefender['planet']->getPlayer()->setResearchLevel('shielding_technology', 10);
        $acsDefender['planet']->getPlayer()->setResearchLevel('armor_technology', 10);

        // Send ACS defend fleet to buddy's planet with 2 hours hold time
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $acsFleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $acsFleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2   // 2-hour hold
        );

        // Advance time so ACS defend fleet physically arrives and starts holding
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send 1 light fighter attack - it will be completely destroyed, all 100 ACS LFs survive
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time to attack arrival - battle occurs during ACS defend hold time
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // After battle: NO return mission yet - fleet is still holding, outbound mission unit counts updated
        $returnMissionsAfterBattle = FleetMission::where('parent_id', $acsDefendMission->id)->get();
        $this->assertCount(0, $returnMissionsAfterBattle, 'No return mission should exist right after battle - fleet continues holding at target planet');

        // Advance time past hold time expiration - this triggers AcsDefendMission::processArrival()
        // which creates the single return mission using the post-battle unit counts
        $this->travelTo(Date::createFromTimestamp($acsDefendMission->time_arrival + 10));
        $this->reloadApplication();
        $acsDefenderPlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $acsDefenderPlayerService->updateFleetMissions();

        // After hold expires: exactly 1 return mission created (no fleet duplication)
        $returnMissionsAfterHold = FleetMission::where('parent_id', $acsDefendMission->id)->get();
        $this->assertCount(1, $returnMissionsAfterHold, 'Exactly 1 return mission should be created when hold expires - fleet must not be duplicated');

        // Advance past return mission arrival and process it
        $returnMission = $returnMissionsAfterHold->first();
        $this->travelTo(Date::createFromTimestamp($returnMission->time_arrival + 10));
        $this->reloadApplication();
        $acsDefenderPlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $acsDefenderPlayerService->updateFleetMissions();

        // Reload ACS defender's planet and verify exactly 100 light fighters returned (no duplication)
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefenderPlanetReloaded = $planetServiceFactory->make($acsDefender['planet']->getPlanetId(), true);
        $returnedLightFighters = $acsDefenderPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter');
        $this->assertEquals(
            100,
            $returnedLightFighters,
            'ACS defend fleet should return with exactly 100 light fighters (no duplication after battle during hold time)'
        );
    }

    /**
     * Verify that when an ACS Defend fleet takes losses in battle, exactly the surviving ships
     * are returned — not the original pre-battle count.
     *
     * Without the fix, two return missions are created: one from the battle (with survivors) and
     * one from processArrival() on hold expiry (with the original full count). The result is that
     * the planet receives survivors + full-original-count ships instead of just survivors.
     *
     * This test captures the actual battle survivor count from the return mission and asserts the
     * planet receives exactly that, regardless of the random battle outcome.
     */
    public function testAcsDefendFleetPartialDestructionReturnsCorrectCount(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Give buddy planet some defenses to force real combat (so attacker doesn't just walk through)
        $this->buddyPlanet->addUnit('rocket_launcher', 20);

        // ACS defender: 100 LFs with same tech as the attacker (basicSetup sets weapon/shield/armor 5)
        // Attacker: 100 LFs (same tech, same numbers) → roughly 50% losses on both sides each battle
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 100);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));
        $acsDefender['planet']->getPlayer()->setResearchLevel('weapon_technology', 5);
        $acsDefender['planet']->getPlayer()->setResearchLevel('shielding_technology', 5);
        $acsDefender['planet']->getPlayer()->setResearchLevel('armor_technology', 5);

        // Send ACS defend fleet with 2 hours hold time
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $acsFleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $acsFleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2   // 2-hour hold
        );

        // Advance to physical arrival (fleet starts holding)
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send a moderate attack during hold time that causes real combat
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance to attack arrival - battle occurs during ACS defend hold time
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // After battle: no return mission yet - fleet is still holding with updated unit counts.
        // If completely destroyed, the outbound mission is marked processed=1 and no return ever arrives.
        $acsDefendMissionReloaded = FleetMission::find($acsDefendMission->id);

        if ($acsDefendMissionReloaded->processed === 1) {
            // Fleet was completely destroyed - nothing to duplicate, test ends here
            $this->assertCount(0, FleetMission::where('parent_id', $acsDefendMission->id)->get(), 'Completely destroyed fleet should have no return missions');
            return;
        }

        // Fleet survived: read the survivor count from the outbound mission's ship columns,
        // which were updated in-place by AttackMission::processArrival() instead of creating
        // a premature return mission. No return mission exists yet at this point.
        $survivorCount = $acsDefendMissionReloaded->light_fighter;
        $this->assertGreaterThan(0, $survivorCount, 'Outbound mission must have surviving ships recorded');
        $this->assertCount(0, FleetMission::where('parent_id', $acsDefendMission->id)->get(), 'No return mission should exist while fleet is still holding after battle');

        // Advance past hold time expiration - AcsDefendMission::processArrival() creates the single
        // return mission using the post-battle unit counts (survivorCount LFs, not the original 100)
        $this->travelTo(Date::createFromTimestamp($acsDefendMission->time_arrival + 10));
        $this->reloadApplication();
        $acsDefenderPlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $acsDefenderPlayerService->updateFleetMissions();

        // Exactly 1 return mission created with survivor counts
        $allReturnMissions = FleetMission::where('parent_id', $acsDefendMission->id)->get();
        $this->assertCount(1, $allReturnMissions, 'Exactly 1 return mission must be created when hold expires (no duplication)');
        $returnMission = $allReturnMissions->first();

        // Process the return trip
        $this->travelTo(Date::createFromTimestamp($returnMission->time_arrival + 10));
        $this->reloadApplication();
        $acsDefenderPlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $acsDefenderPlayerService->updateFleetMissions();

        // Planet must receive exactly the survivor count - not survivor + original 100 from a duplicate return
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefenderPlanetReloaded = $planetServiceFactory->make($acsDefender['planet']->getPlanetId(), true);
        $returnedLightFighters = $acsDefenderPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter');
        $this->assertEquals(
            $survivorCount,
            $returnedLightFighters,
            "Planet should receive exactly {$survivorCount} surviving LFs, not {$survivorCount} + original 100 from a duplicate return mission"
        );
    }

    /**
     * Test that planet owner's units are handled separately from ACS defend fleet units.
     */
    public function testPlanetOwnerUnitsHandledSeparately(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Give buddy planet some ships and defenses
        $this->buddyPlanet->addUnit('light_fighter', 10);
        $this->buddyPlanet->addUnit('rocket_launcher', 10);
        $originalBuddyShips = 10;
        $originalBuddyDefenses = 10;

        // Create ACS defender and send defend fleet
        $acsDefender = $this->createAcsDefender();
        $acsDefender['planet']->addUnit('light_fighter', 20);
        $acsDefender['planet']->addResources(new Resources(0, 0, 1000000, 0));

        // Send ACS defend fleet
        $acsDefendFleet = new UnitCollection();
        $acsDefendFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $acsDefender['planet']->getPlayer()]);
        $acsDefendMission = $fleetMissionService->createNewFromPlanet(
            $acsDefender['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Advance time so ACS defend fleet arrives (during hold period)
        // With the new architecture, time_arrival includes hold time, so calculate physical arrival
        $physicalArrivalTime = $acsDefendMission->time_arrival - $acsDefendMission->time_holding;
        $this->travelTo(Date::createFromTimestamp($physicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send attack
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 40);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time for attack to arrive
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Reload buddy's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlanetReloaded = $planetServiceFactory->make($this->buddyPlanet->getPlanetId());

        // Check that buddy's planet had units removed
        $currentBuddyShips = $buddyPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter');
        $currentBuddyDefenses = $buddyPlanetReloaded->getDefenseUnits()->getAmountByMachineName('rocket_launcher');

        // Planet owner's units should be affected (removed from planet)
        $this->assertLessThanOrEqual(
            $originalBuddyShips,
            $currentBuddyShips,
            'Planet owner ships should be removed from planet if destroyed'
        );

        // ACS defender's units should NOT be on buddy's planet (they were from a fleet)
        // Total ships on planet should only be from planet owner
        $totalShipsOnPlanet = $buddyPlanetReloaded->getShipUnits()->getAmount();
        $this->assertLessThanOrEqual(
            $originalBuddyShips,
            $totalShipsOnPlanet,
            'ACS defend fleet units should not be added to planet owner\'s units'
        );
    }

    /**
     * Test multiple defending fleets with different tech levels.
     */
    public function testMultipleDefendersWithDifferentTechLevels(): void
    {
        $this->basicSetup();
        $this->createBuddyPlayer();

        // Set buddy's tech levels (low)
        $this->buddyPlanet->getPlayer()->setResearchLevel('weapon_technology', 1);
        $this->buddyPlanet->getPlayer()->setResearchLevel('shielding_technology', 1);
        $this->buddyPlanet->getPlayer()->setResearchLevel('armor_technology', 1);
        $this->buddyPlanet->addUnit('light_fighter', 10);

        // Create first ACS defender with medium tech
        $acsDefender1 = $this->createAcsDefender();
        $acsDefender1['planet']->getPlayer()->setResearchLevel('weapon_technology', 5);
        $acsDefender1['planet']->getPlayer()->setResearchLevel('shielding_technology', 5);
        $acsDefender1['planet']->getPlayer()->setResearchLevel('armor_technology', 5);
        $acsDefender1['planet']->addUnit('light_fighter', 15);
        $acsDefender1['planet']->addResources(new Resources(0, 0, 1000000, 0));

        // Create second ACS defender with high tech
        $acsDefender2User = User::factory()->create();
        self::$allCreatedBuddyUserIds[] = $acsDefender2User->id;
        $acsDefender2Planet = Planet::factory()->create([
            'user_id' => $acsDefender2User->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 7),
            'planet' => 10,
        ]);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefender2PlayerService = resolve(PlayerService::class, ['player_id' => $acsDefender2User->id]);
        $acsDefender2PlanetService = $planetServiceFactory->makeForPlayer($acsDefender2PlayerService, $acsDefender2Planet->id);
        $acsDefender2PlanetService->getPlayer()->setResearchLevel('weapon_technology', 10);
        $acsDefender2PlanetService->getPlayer()->setResearchLevel('shielding_technology', 10);
        $acsDefender2PlanetService->getPlayer()->setResearchLevel('armor_technology', 10);
        $acsDefender2PlanetService->addUnit('light_fighter', 20);
        $acsDefender2PlanetService->addResources(new Resources(0, 0, 1000000, 0));

        // Create buddy relationships
        $buddyService = resolve(BuddyService::class);
        $request = $buddyService->sendRequest($acsDefender2User->id, $this->buddyUser->id);
        $buddyService->acceptRequest($request->id, $this->buddyUser->id);

        // Send first ACS defend fleet
        $acsDefendFleet1 = new UnitCollection();
        $acsDefendFleet1->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 15);
        $fleetMissionService1 = resolve(FleetMissionService::class, ['player' => $acsDefender1['planet']->getPlayer()]);
        $acsDefendMission1 = $fleetMissionService1->createNewFromPlanet(
            $acsDefender1['planet'],
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet1,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Send second ACS defend fleet
        $acsDefendFleet2 = new UnitCollection();
        $acsDefendFleet2->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);
        $fleetMissionService2 = resolve(FleetMissionService::class, ['player' => $acsDefender2PlanetService->getPlayer()]);
        $acsDefendMission2 = $fleetMissionService2->createNewFromPlanet(
            $acsDefender2PlanetService,
            $this->buddyPlanet->getPlanetCoordinates(),
            PlanetType::Planet,
            5,
            $acsDefendFleet2,
            new Resources(0, 0, 0, 0),
            10, // 100% speed
            2 // Hold for 2 hours
        );

        // Advance time so both ACS defend fleets arrive (during hold period)
        // With the new architecture, time_arrival includes hold time, so calculate physical arrival
        $physicalArrivalTime1 = $acsDefendMission1->time_arrival - $acsDefendMission1->time_holding;
        $physicalArrivalTime2 = $acsDefendMission2->time_arrival - $acsDefendMission2->time_holding;
        $maxPhysicalArrivalTime = max($physicalArrivalTime1, $physicalArrivalTime2);
        $this->travelTo(Date::createFromTimestamp($maxPhysicalArrivalTime + 10));
        $this->reloadApplication();
        $this->get('/overview');

        // Send attack
        $attackFleet = new UnitCollection();
        $attackFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 30);
        $this->dispatchFleet(
            $this->buddyPlanet->getPlanetCoordinates(),
            $attackFleet,
            new Resources(0, 0, 0, 0),
            PlanetType::Planet
        );

        $attackerFleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $attackMission = $attackerFleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();

        // Advance time for attack to arrive
        $this->travelTo(Date::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Get battle report
        $battleReport = BattleReport::orderBy('id', 'desc')->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        // Verify that defenders had combined forces
        // Planet owner: 10 light fighters (low tech)
        // ACS defender 1: 15 light fighters (medium tech)
        // ACS defender 2: 20 light fighters (high tech)
        // Total: 45 light fighters
        $defenderStartUnits = $battleReport->defender['units'];
        $totalDefenderLightFighters = $defenderStartUnits['light_fighter'] ?? 0;
        $this->assertEquals(45, $totalDefenderLightFighters, 'All defending fleets should participate with their units');
    }
}
