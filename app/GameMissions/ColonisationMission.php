<?php

namespace OGame\GameMissions;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Services\PlanetService;

class ColonisationMission extends GameMission
{
    protected static string $name = 'Colonisation';
    protected static int $typeId = 7;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus
    {
        if ($targetPlanet == null) {
            // Check if a colony ship is present in the fleet
            if ($units->getAmountByMachineName('colony_ship') > 0) {
                return new MissionPossibleStatus(true);
            }
            else {
                // Return error message
                return new MissionPossibleStatus(false, __('You need a colony ship to colonize a planet.'));
            }
        }

        return new MissionPossibleStatus(false);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function processArrival(FleetMission $mission): void
    {
        // TOOD: Implement the colonisation logic
    }

    protected function processReturn(FleetMission $mission): void
    {
        // TODO: Implement the return logic
    }
}