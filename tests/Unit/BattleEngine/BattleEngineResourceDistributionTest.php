<?php

namespace Tests\Unit\BattleEngine;

use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\AttackerFleetResult;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use Tests\UnitTestCase;

class BattleEngineResourceDistributionTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createAndSetPlanetModel([
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ]);
        $this->createAndSetUserTechModel([]);
    }

    public function testDistributeResourcesScalesSurvivingCargoAfterPartialFleetLoss(): void
    {
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');

        $fleetOneUnits = new UnitCollection();
        $fleetOneUnits->addUnit($smallCargo, 2);

        $fleetTwoUnits = new UnitCollection();
        $fleetTwoUnits->addUnit($smallCargo, 1);

        $fleetOne = $this->makeAttackerFleet(101, $fleetOneUnits, new Resources(2000, 0, 0, 0));
        $fleetTwo = $this->makeAttackerFleet(102, $fleetTwoUnits, new Resources(500, 0, 0, 0));

        $engine = new BattleEngineResourceDistributionHarness(
            [$fleetOne, $fleetTwo],
            $this->planetService,
            [DefenderFleet::fromPlanet($this->planetService)],
            $this->settingsService
        );

        $result = new BattleResult();
        $result->loot = new Resources(3000, 0, 0, 0);

        $fleetOneResult = new AttackerFleetResult($fleetOne->fleetMissionId, $fleetOne->ownerId, $fleetOneUnits);
        $fleetOneResult->unitsResult = new UnitCollection();
        $fleetOneResult->unitsResult->addUnit($smallCargo, 1);
        $fleetOneResult->completelyDestroyed = false;

        $fleetTwoResult = new AttackerFleetResult($fleetTwo->fleetMissionId, $fleetTwo->ownerId, $fleetTwoUnits);
        $fleetTwoResult->unitsResult = clone $fleetTwoUnits;
        $fleetTwoResult->completelyDestroyed = false;

        $result->attackerFleetResults = [$fleetOneResult, $fleetTwoResult];

        $engine->runDistributeResources($result);

        $this->assertEquals(
            1000,
            $fleetOneResult->survivingCargo->metal->get(),
            'Fleet one should keep 50% of its carried cargo after losing half its cargo capacity'
        );
        $this->assertEquals(
            500,
            $fleetTwoResult->survivingCargo->metal->get(),
            'Fleet two should keep all carried cargo when all ships survive'
        );
        $this->assertEquals(
            1500,
            $fleetOneResult->lootShare->metal->get(),
            'Remaining loot should be split by surviving cargo capacity after surviving cargo is accounted for'
        );
        $this->assertEquals(1500, $fleetTwoResult->lootShare->metal->get());
        $this->assertEquals(
            3000,
            $result->loot->metal->get(),
            'Normalized battle loot should match the sum of the assigned per-fleet loot shares'
        );
    }

    public function testDistributeResourcesAssignsOddRemainderToInitiator(): void
    {
        $smallCargo = ObjectService::getUnitObjectByMachineName('small_cargo');

        $initiatorUnits = new UnitCollection();
        $initiatorUnits->addUnit($smallCargo, 1);

        $allyUnits = new UnitCollection();
        $allyUnits->addUnit($smallCargo, 1);

        $initiator = $this->makeAttackerFleet(201, $initiatorUnits, new Resources(0, 0, 0, 0));
        $ally = $this->makeAttackerFleet(202, $allyUnits, new Resources(0, 0, 0, 0));

        $engine = new BattleEngineResourceDistributionHarness(
            [$initiator, $ally],
            $this->planetService,
            [DefenderFleet::fromPlanet($this->planetService)],
            $this->settingsService
        );

        $result = new BattleResult();
        $result->loot = new Resources(1, 1, 0, 0);

        $initiatorResult = new AttackerFleetResult($initiator->fleetMissionId, $initiator->ownerId, $initiatorUnits);
        $initiatorResult->unitsResult = clone $initiatorUnits;
        $initiatorResult->completelyDestroyed = false;

        $allyResult = new AttackerFleetResult($ally->fleetMissionId, $ally->ownerId, $allyUnits);
        $allyResult->unitsResult = clone $allyUnits;
        $allyResult->completelyDestroyed = false;

        $result->attackerFleetResults = [$initiatorResult, $allyResult];

        $engine->runDistributeResources($result);

        $this->assertEquals(1, $initiatorResult->lootShare->metal->get());
        $this->assertEquals(0, $allyResult->lootShare->metal->get());
        $this->assertEquals(1, $initiatorResult->lootShare->crystal->get());
        $this->assertEquals(0, $allyResult->lootShare->crystal->get());
    }

    private function makeAttackerFleet(int $fleetMissionId, UnitCollection $units, Resources $cargoResources): AttackerFleet
    {
        $attacker = new AttackerFleet();
        $attacker->units = clone $units;
        $attacker->player = $this->playerService;
        $attacker->fleetMissionId = $fleetMissionId;
        $attacker->ownerId = $this->playerService->getId();
        $attacker->cargoResources = $cargoResources;
        $attacker->isInitiator = in_array($fleetMissionId, [101, 201], true);
        $attacker->fleetMission = null;

        return $attacker;
    }
}

class BattleEngineResourceDistributionHarness extends BattleEngine
{
    public function runDistributeResources(BattleResult $result): void
    {
        $this->distributeResources($result);
    }

    protected function fightBattleRounds(BattleResult $result): array
    {
        return [];
    }
}
