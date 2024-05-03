<?php

namespace OGame\GameMissions;

use Exception;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class ColonisationMission extends GameMission
{
    protected static string $name = 'Colonisation';
    protected static int $typeId = 7;
    protected static bool $hasReturnMission = true;

    public function startMissionSanityChecks(PlanetService $planet, UnitCollection $units, Resources $resources): void
    {
        // Call the parent method
        parent::startMissionSanityChecks($planet, $units, $resources);

        if ($units->getAmountByMachineName('colony_ship') == 0) {
            throw new Exception(__('You need a colony ship to colonize a planet.'));
        }

        if ($planet->getPlayer() != null) {
            throw new Exception(__('You can only colonize empty planets.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus
    {
        if ($targetPlanet == null) {
            // Check if a colony ship is present in the fleet
            if ($units->getAmountByMachineName('colony_ship') > 0) {
                return new MissionPossibleStatus(true);
            } else {
                // Return error message
                return new MissionPossibleStatus(false, __('You need a colony ship to colonize a planet.'));
            }
        }

        return new MissionPossibleStatus(false);
    }

    /**
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
