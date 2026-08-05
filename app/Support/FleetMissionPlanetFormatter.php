<?php

namespace OGame\Support;

use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;

/**
 * Builds planet/coordinate placeholders for fleet messages that survive deleted bodies.
 */
final class FleetMissionPlanetFormatter
{
    public static function tag(FleetMission $mission, string $endpoint): string
    {
        $planetId = $endpoint === 'from' ? $mission->planet_id_from : $mission->planet_id_to;
        $galaxy = $endpoint === 'from' ? $mission->galaxy_from : $mission->galaxy_to;
        $system = $endpoint === 'from' ? $mission->system_from : $mission->system_to;
        $position = $endpoint === 'from' ? $mission->position_from : $mission->position_to;
        $type = $endpoint === 'from' ? $mission->type_from : $mission->type_to;

        if ($type === PlanetType::DebrisField->value) {
            return "[debrisfield]{$galaxy}:{$system}:{$position}[/debrisfield]";
        }

        if ($planetId !== null) {
            return "[planet]{$planetId}[/planet]";
        }

        return "[coordinates]{$galaxy}:{$system}:{$position}[/coordinates]";
    }

    public static function returnEndpointLabel(FleetMission $mission, string $endpoint): string
    {
        $tag = self::tag($mission, $endpoint);
        $type = $endpoint === 'from' ? $mission->type_from : $mission->type_to;

        if (in_array($type, [PlanetType::Planet->value, PlanetType::Moon->value], true)
            && str_starts_with($tag, '[planet]')) {
            return __('planet') . ' ' . $tag;
        }

        return $tag;
    }
}
