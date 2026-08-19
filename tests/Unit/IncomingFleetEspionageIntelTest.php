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
            // Resources are always zeroed for foreign fleets regardless of espionage level.
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
            $mission->mission_type = 1; // Attack

            $mission->light_fighter = 200;
            $mission->small_cargo = 100;
            $mission->metal = 800;
            $mission->crystal = 1200;
            $mission->deuterium = 400;
            $mission->deuterium_consumption = 0;

            $intel = $intelService->shapeIncomingFleetIntel($mission, $viewer, $fleetMissionService);

            $this->assertSame($expectedLevel, $intel['intel_level'], "Wrong intel level at espionage $espionageLevel");
            $this->assertFalse($intel['show_shipment'], "show_shipment must be false for foreign fleets (espionage $espionageLevel)");
            $this->assertSame($expectedShipCount, $intel['ship_count'], "Wrong ship_count at espionage $espionageLevel");

            if ($expectUnits) {
                // ShipTypes tier returns unit entries with 0 amounts; Full tier returns real counts.
                $this->assertNotEmpty($intel['units']->units, "Expected unit entries at espionage $espionageLevel");
            } else {
                $this->assertEmpty($intel['units']->units, "Expected no unit entries at espionage $espionageLevel");
            }

            // Foreign fleet cargo is always hidden regardless of espionage level.
            $this->assertEquals(0, $intel['resources']->metal->get(), "Expected no metal at espionage $espionageLevel");
        }
    }
}
