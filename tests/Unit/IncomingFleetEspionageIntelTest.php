<?php

namespace Tests\Unit;

use OGame\Models\FleetMission;
use OGame\Services\FleetMissionService;
use OGame\Services\IncomingFleetIntelService;
use OGame\Services\PlayerService;
use Tests\UnitTestCase;

class IncomingFleetEspionageIntelTest extends UnitTestCase
{
    public function testHostileRedactionAtEspionageLevels(): void
    {
        $intelService = resolve(IncomingFleetIntelService::class);
        $originalUnitsLightFighter = 200;
        $originalUnitsSmallCargo = 100;
        $originalResourcesMetal = 800;
        $originalResourcesCrystal = 1200;
        $originalResourcesDeuterium = 400;

        foreach ([0, 2, 4, 8] as $espionageLevel) {
            $viewer = $this->createMock(PlayerService::class);
            $viewer->method('getId')->willReturn(1);
            $viewer->method('hasCommander')->willReturn(false);
            $viewer->expects($this->once())->method('getResearchLevel')->with('espionage_technology')->willReturn($espionageLevel);

            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $viewer]);

            $mission = new FleetMission();
            $mission->user_id = 2; // foreign
            $mission->mission_type = 1; // hostile (Attack)

            $mission->light_fighter = $originalUnitsLightFighter;
            $mission->small_cargo = $originalUnitsSmallCargo;
            $mission->metal = $originalResourcesMetal;
            $mission->crystal = $originalResourcesCrystal;
            $mission->deuterium = $originalResourcesDeuterium;
            $mission->deuterium_consumption = 0;

            $intel = $intelService->shapeIncomingFleetIntel($mission, $viewer, $fleetMissionService);

            $ratio = match (true) {
                $espionageLevel >= 8 => 1.0,
                $espionageLevel >= 4 => 0.75,
                $espionageLevel >= 2 => 0.5,
                default => 0.0,
            };

            $expectedLightFighter = (int) floor($originalUnitsLightFighter * $ratio);
            $expectedSmallCargo = (int) floor($originalUnitsSmallCargo * $ratio);

            $expectedMetal = (int) floor($originalResourcesMetal * $ratio);
            $expectedCrystal = (int) floor($originalResourcesCrystal * $ratio);
            $expectedDeuterium = (int) floor($originalResourcesDeuterium * $ratio);

            $this->assertEquals($expectedLightFighter, $intel['units']->getAmountByMachineName('light_fighter'));
            $this->assertEquals($expectedSmallCargo, $intel['units']->getAmountByMachineName('small_cargo'));

            $this->assertEquals($expectedMetal, $intel['resources']->metal->get());
            $this->assertEquals($expectedCrystal, $intel['resources']->crystal->get());
            $this->assertEquals($expectedDeuterium, $intel['resources']->deuterium->get());

            $this->assertEquals($expectedLightFighter + $expectedSmallCargo, $intel['ship_count']);
        }
    }
}
