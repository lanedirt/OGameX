<?php

namespace Tests\Unit\BattleEngine;

use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameConstants\UniverseConstants;
use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\Models\TacticalRetreatDecision;
use OGame\GameMissions\BattleEngine\Services\TacticalRetreatService;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Highscore;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\NPCPlanetService;
use OGame\Services\NPCPlayerService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Tests for tactical retreat point weighting, gates, and battle integration.
 *
 * Battle-engine integration tests run for both PHP and Rust via subclasses,
 * matching BattleEngineTestAbstract. Service-level tests live here too so
 * blockedReason gates are asserted against the shared evaluator.
 */
abstract class TacticalRetreatTestAbstract extends AccountTestCase
{
    protected int $userPlanetAmount = 2;

    private TacticalRetreatService $service;

    /**
     * @return class-string<BattleEngine>
     */
    abstract protected function battleEngineClass(): string;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TacticalRetreatService();
    }

    public function testCombatShipsCountFullPointsAndCivilShipsQuarter(): void
    {
        $units = new UnitCollection();
        // Light fighter: 4000 resources → 4 points each → 40 points for 10
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);
        // Small cargo: 4000 resources * 25% → 1 point each → 5 points for 5
        $units->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), 5);

        $this->assertEquals(45, $this->service->calculateFleetPoints($units));
    }

    public function testZeroPointShipsAndDefensesDoNotCount(): void
    {
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 100);
        $units->addUnit(ObjectService::getUnitObjectByMachineName('solar_satellite'), 50);
        $units->addUnit(ObjectService::getUnitObjectByMachineName('crawler'), 20);
        $units->addUnit(ObjectService::getUnitObjectByMachineName('rocket_launcher'), 200);

        $this->assertEquals(0, $this->service->calculateFleetPoints($units));
    }

    public function testDeathstarCountsForPointsButCannotFlee(): void
    {
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getUnitObjectByMachineName('deathstar'), 1);
        $units->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        // Deathstar is a combat ship for ratio points (10M resources → 10000 pts) but cannot flee.
        $this->assertEquals(10000 + 20, $this->service->calculateFleetPoints($units));

        $fleeing = $this->service->extractFleeingUnits($units);
        $this->assertEquals(0, $fleeing->getAmountByMachineName('deathstar'));
        $this->assertEquals(5, $fleeing->getAmountByMachineName('light_fighter'));
    }

    public function testSupremacyRatioRounding(): void
    {
        $this->assertEquals(5, $this->service->calculateSupremacyRatio(100, 20));
        $this->assertEquals(1, $this->service->calculateSupremacyRatio(0, 20));
        $this->assertEquals(10, $this->service->calculateSupremacyRatio(10, 0));
    }

    public function testFleeTriggersAtExactFiveToOneRatio(): void
    {
        $settingsService = resolve(SettingsService::class);
        // 4 LF defender = 16 points; 20 LF attacker = 80 points → exact 5:1
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 4,
            'deuterium' => 100000,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 20);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertEquals(5, $result->tacticalRetreatRatio);
        $this->assertTrue(
            $result->tacticalRetreatDefenderFled,
            'OGame flees from a 5:1 ratio (attackerPoints >= 5 * defenderPoints)'
        );
    }

    public function testFleeDoesNotTriggerJustBelowFiveToOne(): void
    {
        $settingsService = resolve(SettingsService::class);
        // 4 LF defender = 16 points; 19 LF attacker = 76 points → 76 < 80 required for exact 5:1
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 4,
            'deuterium' => 100000,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 19);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertEquals(76, $result->tacticalRetreatAttackerPoints);
        $this->assertEquals(16, $result->tacticalRetreatDefenderPoints);
        $this->assertFalse(
            $result->tacticalRetreatDefenderFled,
            '19 LF vs 4 LF is 76:16 which is below exact 5:1 (80 required)'
        );
    }

    public function testFleeDeuteriumUsesNeighboringPlanetSlotNotSystem(): void
    {
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 10,
            'deuterium' => 100000,
        ]);
        $coords = $defenderPlanet->getPlanetCoordinates();
        $neighborPosition = $coords->position + 1;
        if ($neighborPosition > UniverseConstants::MAX_PLANET_POSITION) {
            $neighborPosition = $coords->position - 1;
        }

        $fleeing = new UnitCollection();
        $fleeing->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 10);

        $player = $defenderPlanet->getPlayer();
        $this->assertNotNull($player);
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);

        $neighborSlot = new Coordinate($coords->galaxy, $coords->system, $neighborPosition);
        $neighborSystem = new Coordinate($coords->galaxy, $coords->system + 1, $coords->position);

        $slotCost = (int)ceil($fleetMissionService->calculateConsumption($defenderPlanet, $fleeing, $neighborSlot, 0, 10.0) * 1.5);
        $systemCost = (int)ceil($fleetMissionService->calculateConsumption($defenderPlanet, $fleeing, $neighborSystem, 0, 10.0) * 1.5);

        $actual = $this->service->calculateFleeDeuteriumCost($defenderPlanet, $fleeing);

        $this->assertEquals($slotCost, $actual, 'Flee fuel must use neighbouring planet slot');
        $this->assertNotEquals($systemCost, $actual, 'Flee fuel must not use neighbouring system distance');
    }

    public function testDefenderFleetFleesWhenRatioMet(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'rocket_launcher' => 10,
            'deuterium' => 100000,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertTrue($result->tacticalRetreatDefenderFled, 'Defender should flee at 5:1+ supremacy');
        $this->assertGreaterThanOrEqual(5, $result->tacticalRetreatRatio);
        $this->assertFalse($result->tacticalRetreatAttackerAlsoRetreated);
        $this->assertEquals(5, $result->tacticalRetreatFleeingUnits?->getAmountByMachineName('light_fighter'));
        // Fleeing fighters must not count as combat losses.
        $this->assertEquals(0, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
        // Defenses remain in the combat composition after flee.
        $this->assertEquals(10, $result->defenderUnitsStart->getAmountByMachineName('rocket_launcher'));
        $this->assertGreaterThan(0, $result->tacticalRetreatDeuteriumCost);
    }

    public function testInsufficientDeuteriumPreventsFlee(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 0,
        ]);
        // Drain any leftover resources.
        $resources = $defenderPlanet->getResources();
        $defenderPlanet->deductResources(new Resources(
            (int)$resources->metal->get(),
            (int)$resources->crystal->get(),
            (int)$resources->deuterium->get(),
            0
        ));

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertFalse($result->tacticalRetreatDefenderFled, 'Flee should fail without deuterium');
        $this->assertEquals(5, $result->defenderUnitsStart->getAmountByMachineName('light_fighter'));
        $this->assertEquals(5, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
    }

    public function testNeverPreferencePreventsFlee(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 100000,
        ]);
        $defenderPlayer = $defenderPlanet->getPlayer();
        $this->assertNotNull($defenderPlayer);
        $user = $defenderPlayer->getUser();
        $user->tactical_retreat_ratio = 0;
        $user->save();

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertFalse($result->tacticalRetreatDefenderFled, 'Flee should be disabled when preference is never');
        $this->assertEquals(5, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
    }

    public function testPointsCutoffPreventsFlee(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 100000,
        ]);
        $defenderPlayer = $defenderPlanet->getPlayer();
        $this->assertNotNull($defenderPlayer);

        Highscore::updateOrCreate(
            ['player_id' => $defenderPlayer->getId()],
            [
                'general' => 500000,
                'economy' => 0,
                'research' => 0,
                'military_built' => 0,
                'military_destroyed' => 0,
                'military_lost' => 0,
                'general_rank' => 1,
                'economy_rank' => 1,
                'research_rank' => 1,
                'military_built_rank' => 1,
                'military_destroyed_rank' => 1,
                'military_lost_rank' => 1,
            ]
        );

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertFalse($result->tacticalRetreatDefenderFled, 'Players at 500k+ points must not flee');
    }

    public function testNpcDefenderBlockedReasonPreventsFlee(): void
    {
        $basePlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 100000,
        ]);

        $npcFleet = new UnitCollection();
        $npcFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $npcPlayer = new NPCPlayerService('pirate', 0, 0, 0);
        $npcPlanet = new NPCPlanetService(
            resolve(PlayerServiceFactory::class),
            resolve(SettingsService::class),
            $npcPlayer,
            $npcFleet,
            $basePlanet->getPlanetId()
        );

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $decision = $this->evaluateRetreat($npcPlanet, $attackerFleet);
        $this->assertSame('npc', $decision->blockedReason);
        $this->assertFalse($decision->defenderFled);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $npcPlanet, resolve(SettingsService::class));
        $result = $engine->simulateBattle();
        $this->assertFalse($result->tacticalRetreatDefenderFled, 'Expedition NPC fleets must never flee');
        $this->assertEquals(5, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
    }

    public function testInactiveDefenderBlockedReasonPreventsFlee(): void
    {
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 100000,
        ]);
        $defenderPlayer = $defenderPlanet->getPlayer();
        $this->assertNotNull($defenderPlayer);
        $user = $defenderPlayer->getUser();
        $user->time = (string) now()->subDays(8)->timestamp;
        $user->save();

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $decision = $this->evaluateRetreat($defenderPlanet, $attackerFleet);
        $this->assertSame('inactive', $decision->blockedReason);
        $this->assertFalse($decision->defenderFled);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, resolve(SettingsService::class));
        $result = $engine->simulateBattle();
        $this->assertFalse($result->tacticalRetreatDefenderFled, 'Inactive fleets must not flee');
        $this->assertEquals(5, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
    }

    public function testMoonStationedFleetFleesViaSharedBattleEngine(): void
    {
        $secondPlanet = $this->secondPlanetService;
        if ($secondPlanet === null) {
            $this->fail('Second planet not initialized');
        }

        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $moon = $planetServiceFactory->createMoonForPlanet($secondPlanet, 2000000, 20);
        $moon->addResources(new Resources(10000, 10000, 100000, 0));
        $moon->addUnit('light_fighter', 5);
        $moon->save();

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $moon, resolve(SettingsService::class));
        $result = $engine->simulateBattle();

        $this->assertTrue(
            $result->tacticalRetreatDefenderFled,
            'Moon-stationed fleets currently flee because MoonDestructionMission shares simulateBattle()'
        );
        $this->assertEquals(5, $result->tacticalRetreatFleeingUnits?->getAmountByMachineName('light_fighter'));
        $this->assertEquals(0, $result->defenderUnitsLost->getAmountByMachineName('light_fighter'));
    }

    public function testAcsDefendFleetDoesNotFlee(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'deuterium' => 100000,
        ]);
        $defenderPlayer = $defenderPlanet->getPlayer();
        $this->assertNotNull($defenderPlayer);
        $attackerPlayer = $this->attackerPlayer();

        $acsUnits = new UnitCollection();
        $acsUnits->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 5);

        $defenders = [DefenderFleet::fromPlanet($defenderPlanet)];
        $acsDefend = new DefenderFleet();
        $acsDefend->units = $acsUnits;
        $acsDefend->player = $defenderPlayer;
        $acsDefend->fleetMissionId = 999;
        $acsDefend->ownerId = $defenderPlayer->getId();
        $acsDefend->fleetMission = null;
        $defenders[] = $acsDefend;

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engineClass = $this->battleEngineClass();
        $engine = new $engineClass([$this->makeAttackerFleet($attackerFleet, $attackerPlayer)], $defenderPlanet, $defenders, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertFalse($result->tacticalRetreatDefenderFled);
        $this->assertEquals(0, $result->defenderUnitsResult->getAmountByMachineName('light_fighter'));
    }

    public function testAttackerAlsoRetreatsWhenConfigured(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'rocket_launcher' => 50,
            'deuterium' => 100000,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $engine->setRetreatAfterDefenderRetreat(true);
        $result = $engine->simulateBattle();

        $this->assertTrue($result->tacticalRetreatDefenderFled);
        $this->assertTrue($result->tacticalRetreatAttackerAlsoRetreated);
        $this->assertCount(0, $result->rounds);
        $this->assertEquals(0, $result->loot->sum());
        $this->assertEquals(100, $result->attackerUnitsResult->getAmountByMachineName('light_fighter'));
        $this->assertEquals(50, $result->defenderUnitsResult->getAmountByMachineName('rocket_launcher'));
        $this->assertEquals(0, $result->defenderUnitsLost->getAmountByMachineName('rocket_launcher'));
    }

    public function testAttackerRetreatGrantsNoLootWhenOnlyShipsFled(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'deuterium' => 100000,
        ]);
        $defenderPlanet->addResources(new Resources(50000, 50000, 0, 0));

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $engine->setRetreatAfterDefenderRetreat(true);
        $result = $engine->simulateBattle();

        $this->assertTrue($result->tacticalRetreatDefenderFled);
        $this->assertTrue($result->tacticalRetreatAttackerAlsoRetreated);
        $this->assertEquals(0, $result->defenderUnitsResult->getAmount());
        $this->assertEquals(0, $result->loot->sum(), 'Mutual retreat must not award loot even with empty combat defender side');
    }

    public function testFleedShipsAreNotPaddedIntoCombatRoundsAsDestroyed(): void
    {
        $settingsService = resolve(SettingsService::class);
        $defenderPlanet = $this->prepareDefenderPlanet([
            'light_fighter' => 5,
            'rocket_launcher' => 100,
            'deuterium' => 100000,
        ]);

        $attackerFleet = new UnitCollection();
        $attackerFleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 100);

        $engine = $this->createBattleEngine($attackerFleet, $this->attackerPlayer(), $defenderPlanet, $settingsService);
        $result = $engine->simulateBattle();

        $this->assertTrue($result->tacticalRetreatDefenderFled);
        $this->assertNotEmpty($result->rounds);

        foreach ($result->rounds as $round) {
            $this->assertEquals(
                0,
                $round->defenderShips->getAmountByMachineName('light_fighter'),
                'Fleed ships must not appear in combat rounds'
            );
            $this->assertFalse(
                $round->defenderShips->hasUnit(ObjectService::getUnitObjectByMachineName('light_fighter')),
                'Fleed ships must not be padded into round unit lists'
            );
        }
    }

    /**
     * @param array<string, int> $attributes
     */
    private function prepareDefenderPlanet(array $attributes): PlanetService
    {
        $secondPlanet = $this->secondPlanetService;
        if ($secondPlanet === null) {
            $this->fail('Second planet not initialized');
        }

        $deuterium = $attributes['deuterium'] ?? 10000;
        $secondPlanet->addResources(new Resources(10000, 10000, $deuterium, 0));

        foreach ($attributes as $key => $value) {
            if (in_array($key, ['metal', 'crystal', 'deuterium'], true)) {
                continue;
            }
            if ($value > 0) {
                $secondPlanet->addUnit($key, $value);
            }
        }

        return $secondPlanet;
    }

    private function attackerPlayer(): PlayerService
    {
        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Attacker planet has no player.');
        }

        return $player;
    }

    private function evaluateRetreat(PlanetService $defenderPlanet, UnitCollection $attackerFleet): TacticalRetreatDecision
    {
        $defenders = [DefenderFleet::fromPlanet($defenderPlanet)];

        return $this->service->evaluate(
            $defenderPlanet,
            [$this->makeAttackerFleet($attackerFleet, $this->attackerPlayer())],
            $defenders,
            false,
        );
    }

    private function makeAttackerFleet(UnitCollection $attackerFleet, PlayerService $player): AttackerFleet
    {
        $attacker = new AttackerFleet();
        $attacker->units = $attackerFleet;
        $attacker->player = $player;
        $attacker->fleetMissionId = 1;
        $attacker->ownerId = $player->getId();
        $attacker->cargoResources = new Resources(0, 0, 0, 0);
        $attacker->isInitiator = true;
        $attacker->fleetMission = null;

        return $attacker;
    }

    private function createBattleEngine(
        UnitCollection $attackerFleet,
        PlayerService $player,
        PlanetService $defenderPlanet,
        SettingsService $settingsService
    ): BattleEngine {
        $engineClass = $this->battleEngineClass();

        return new $engineClass(
            [$this->makeAttackerFleet($attackerFleet, $player)],
            $defenderPlanet,
            [DefenderFleet::fromPlanet($defenderPlanet)],
            $settingsService
        );
    }
}
