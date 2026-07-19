<?php

namespace Tests\Unit;

use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Support\FleetMissionPlanetFormatter;
use Tests\UnitTestCase;

class FleetMissionPlanetFormatterTest extends UnitTestCase
{
    public function testTagUsesPlanetIdWhenAvailable(): void
    {
        $mission = new FleetMission([
            'planet_id_from' => 42,
            'galaxy_from' => 1,
            'system_from' => 2,
            'position_from' => 3,
            'type_from' => PlanetType::Planet->value,
        ]);

        $this->assertSame('[planet]42[/planet]', FleetMissionPlanetFormatter::tag($mission, 'from'));
    }

    public function testTagFallsBackToCoordinatesWhenPlanetIdIsNull(): void
    {
        $mission = new FleetMission([
            'planet_id_from' => null,
            'galaxy_from' => 1,
            'system_from' => 2,
            'position_from' => 3,
            'type_from' => PlanetType::Moon->value,
        ]);

        $this->assertSame('[coordinates]1:2:3[/coordinates]', FleetMissionPlanetFormatter::tag($mission, 'from'));
    }

    public function testTagUsesDebrisFieldPlaceholder(): void
    {
        $mission = new FleetMission([
            'planet_id_to' => null,
            'galaxy_to' => 4,
            'system_to' => 5,
            'position_to' => 6,
            'type_to' => PlanetType::DebrisField->value,
        ]);

        $this->assertSame('[debrisfield]4:5:6[/debrisfield]', FleetMissionPlanetFormatter::tag($mission, 'to'));
    }
}
