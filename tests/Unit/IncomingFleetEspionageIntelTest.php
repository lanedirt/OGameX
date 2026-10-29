<?php

namespace Tests\Unit;

use OGame\Enums\IncomingFleetIntelLevel;
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

        $cases = [
            // [espionage, expectedLevel, expectUnits, expectShipCount]
            // Resources are always zeroed for hostile foreign fleets regardless of espionage level.
            [0, IncomingFleetIntelLevel::None,       false, 0],
            [2, IncomingFleetIntelLevel::TotalCount,  false, 300],
            [4, IncomingFleetIntelLevel::ShipTypes,   true,  300],
            [8, IncomingFleetIntelLevel::Full,         true,  300],
        ];

        foreach ($cases as [$espionageLevel, $expectedLevel, $expectUnits, $expectedShipCount]) {
            $viewer = $this->createMock(PlayerService::class);
            $viewer->method('getId')->willReturn(1);
            $viewer->method('hasCommander')->willReturn(false);
            $viewer->method('getResearchLevel')->willReturn($espionageLevel);

            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $viewer]);

            $mission = new FleetMission();
            $mission->user_id = 2; // foreign
            $mission->mission_type = 1; // Attack (hostile)

            $mission->light_fighter = 200;
            $mission->small_cargo = 100;
            $mission->metal = 800;
            $mission->crystal = 1200;
            $mission->deuterium = 400;
            $mission->deuterium_consumption = 0;

            $intel = $intelService->shapeIncomingFleetIntel($mission, $viewer, $fleetMissionService);

            $this->assertSame($expectedLevel, $intel['intel_level'], "Wrong intel level at espionage $espionageLevel");
            $this->assertFalse($intel['show_shipment'], "show_shipment must be false for hostile foreign fleets (espionage $espionageLevel)");
            $this->assertSame($expectedShipCount, $intel['ship_count'], "Wrong ship_count at espionage $espionageLevel");

            if ($expectUnits) {
                // ShipTypes tier returns unit entries with 0 amounts; Full tier returns real counts.
                $this->assertNotEmpty($intel['units']->units, "Expected unit entries at espionage $espionageLevel");
            } else {
                $this->assertEmpty($intel['units']->units, "Expected no unit entries at espionage $espionageLevel");
            }

            // Hostile fleet cargo is always hidden regardless of espionage level.
            $this->assertEquals(0, $intel['resources']->metal->get(), "Expected no metal at espionage $espionageLevel");
        }
    }

    public function testFriendlyFleetIsNeverRedacted(): void
    {
        $intelService = resolve(IncomingFleetIntelService::class);

        // Friendly incoming fleet (e.g. Transport, mission_type=3 or ACS Defend, mission_type=5)
        // should always be shown in full regardless of espionage level.
        foreach ([3, 5] as $missionType) {
            $viewer = $this->createMock(PlayerService::class);
            $viewer->method('getId')->willReturn(1);
            $viewer->method('hasCommander')->willReturn(false);
            $viewer->method('getResearchLevel')->willReturn(0); // espionage 0

            $fleetMissionService = resolve(FleetMissionService::class, ['player' => $viewer]);

            $mission = new FleetMission();
            $mission->user_id = 2; // foreign
            $mission->mission_type = $missionType;

            $mission->light_fighter = 50;
            $mission->small_cargo = 20;
            $mission->metal = 500;
            $mission->crystal = 300;
            $mission->deuterium = 100;
            $mission->deuterium_consumption = 0;

            $intel = $intelService->shapeIncomingFleetIntel($mission, $viewer, $fleetMissionService);

            $this->assertSame(IncomingFleetIntelLevel::Full, $intel['intel_level'], "Friendly fleet must be Full intel (mission_type=$missionType)");
            $this->assertSame(70, $intel['ship_count'], "Friendly fleet ship count must be visible (mission_type=$missionType)");
            $this->assertNotEmpty($intel['units']->units, "Friendly fleet units must be visible (mission_type=$missionType)");
        }
    }

    public function testFriendlyFleetCargoRequiresCommander(): void
    {
        $intelService = resolve(IncomingFleetIntelService::class);

        // Without Commander: cargo is hidden.
        $viewer = $this->createMock(PlayerService::class);
        $viewer->method('getId')->willReturn(1);
        $viewer->method('hasCommander')->willReturn(false);
        $viewer->method('getResearchLevel')->willReturn(0);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $viewer]);

        $mission = new FleetMission();
        $mission->user_id = 2;
        $mission->mission_type = 3; // Transport (friendly)
        $mission->light_fighter = 10;
        $mission->metal = 1000;
        $mission->crystal = 500;
        $mission->deuterium = 200;
        $mission->deuterium_consumption = 0;

        $intel = $intelService->shapeIncomingFleetIntel($mission, $viewer, $fleetMissionService);

        $this->assertFalse($intel['show_shipment'], "Without Commander, show_shipment must be false for friendly fleet");
        $this->assertEquals(0, $intel['resources']->metal->get(), "Without Commander, cargo metal must be 0");

        // With Commander: cargo is visible.
        $viewerWithCommander = $this->createMock(PlayerService::class);
        $viewerWithCommander->method('getId')->willReturn(1);
        $viewerWithCommander->method('hasCommander')->willReturn(true);
        $viewerWithCommander->method('getResearchLevel')->willReturn(0);

        $fleetMissionServiceWithCommander = resolve(FleetMissionService::class, ['player' => $viewerWithCommander]);

        $intelWithCommander = $intelService->shapeIncomingFleetIntel($mission, $viewerWithCommander, $fleetMissionServiceWithCommander);

        $this->assertTrue($intelWithCommander['show_shipment'], "With Commander, show_shipment must be true for friendly fleet");
        $this->assertEquals(1000, $intelWithCommander['resources']->metal->get(), "With Commander, cargo metal must be visible");
    }
}
