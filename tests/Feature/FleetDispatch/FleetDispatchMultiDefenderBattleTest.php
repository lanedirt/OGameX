<?php

namespace Tests\Feature\FleetDispatch;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Message;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\BuddyService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
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

        $buddyPlanet = \OGame\Models\Planet::factory()->create([
            'user_id' => $buddyUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 5),
            'planet' => 8,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $buddyPlayerService = resolve(\OGame\Services\PlayerService::class, ['player_id' => $buddyUser->id]);
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

        $defenderPlanet = \OGame\Models\Planet::factory()->create([
            'user_id' => $defenderUser->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 6),
            'planet' => 9,
        ]);

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $defenderPlayerService = resolve(\OGame\Services\PlayerService::class, ['player_id' => $defenderUser->id]);
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
        $this->travelTo(Carbon::createFromTimestamp($physicalArrivalTime + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($attackMission->time_arrival + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($physicalArrivalTime + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($attackMission->time_arrival + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($physicalArrivalTime + 10));
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

        // Advance time for attack to arrive
        $this->travelTo(Carbon::createFromTimestamp($attackMission->time_arrival + 10));
        $this->reloadApplication();
        $this->playerSetAllMessagesRead();
        $this->get('/overview');

        // Check that ACS defend mission created a return trip
        $returnMission = FleetMission::where('parent_id', $acsDefendMission->id)
            ->where('processed', 0)
            ->first();
        $this->assertNotNull($returnMission, 'ACS defend fleet should have a return mission');

        // Advance time for return trip
        $this->travelTo(Carbon::createFromTimestamp($returnMission->time_arrival + 10));
        $this->reloadApplication();

        // Process missions for the ACS defender (return mission belongs to them)
        $playerService = resolve(\OGame\Services\PlayerService::class, ['player_id' => $acsDefender['user']->id]);
        $playerService->updateFleetMissions();

        // Check that ships returned to ACS defender's planet
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefenderPlanetReloaded = $planetServiceFactory->make($acsDefender['planet']->getPlanetId(), true);
        $returnedShips = $acsDefenderPlanetReloaded->getShipUnits()->getAmountByMachineName('light_fighter');
        $this->assertGreaterThan(0, $returnedShips, 'Some ships should have returned');
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
        $this->travelTo(Carbon::createFromTimestamp($physicalArrivalTime + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($attackMission->time_arrival + 10));
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
        $acsDefender2Planet = \OGame\Models\Planet::factory()->create([
            'user_id' => $acsDefender2User->id,
            'galaxy' => $this->planetService->getPlanetCoordinates()->galaxy,
            'system' => min(499, $this->planetService->getPlanetCoordinates()->system + 7),
            'planet' => 10,
        ]);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $acsDefender2PlayerService = resolve(\OGame\Services\PlayerService::class, ['player_id' => $acsDefender2User->id]);
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
        $this->travelTo(Carbon::createFromTimestamp($maxPhysicalArrivalTime + 10));
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
        $this->travelTo(Carbon::createFromTimestamp($attackMission->time_arrival + 10));
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
