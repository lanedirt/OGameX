<?php

namespace OGame\GameMissions;

use Exception;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMessages\ColonyEstablished;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class ColonisationMission extends GameMission
{
    protected static string $name = 'Colonisation';
    protected static int $typeId = 7;
    protected static bool $hasReturnMission = false;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function startMissionSanityChecks(PlanetService $planet, Coordinate $targetCoordinate, UnitCollection $units, Resources $resources): void
    {
        // Call the parent method
        parent::startMissionSanityChecks($planet, $targetCoordinate, $units, $resources);

        if ($units->getAmountByMachineName('colony_ship') == 0) {
            throw new Exception(__('You need a colony ship to colonize a planet.'));
        }

        // Try to load planet. If it succeeds it means the planet is not empty.
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        if ($planetServiceFactory->makeForCoordinate($targetCoordinate) != null) {
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
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Sanity check: make sure the target coordinates are valid and the planet is (still) empty.
        $target_planet = $this->planetServiceFactory->makeForCoordinate(new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to));

        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id);

        if ($target_planet != null) {
            // TODO: add unittest for this behavior.
            // Cancel the current mission.
            $this->cancel($mission);
            // Send fleet back.
            $this->startReturn($mission);
            return;
        }

        // Sanity check: colonisation mission without a colony ship is not possible.
        if ($mission->colony_ship < 1) {
            // Cancel the current mission.
            $this->cancel($mission);
            // Send fleet back.
            $this->startReturn($mission);
        }

        // Create a new planet at the target coordinates.
        $target_planet = $this->planetServiceFactory->createAdditionalForPlayer($player, new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to));

        // Send success message
        $this->messageService->sendSystemMessageToPlayer($player, ColonyEstablished::class, [
            'coordinates' => $target_planet->getPlanetCoordinates()->asString(),
        ]);

        // Add resources to the target planet if the mission has any.
        $resources = $this->fleetMissionService->getResources($mission);
        $target_planet->addResources($resources);

        // Remove the colony ship from the fleet as it is consumed in the colonization process.
        $mission->colony_ship -= 1;

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Check if the mission has any ships left. If yes, start a return mission to send them back.
        if ($this->fleetMissionService->getFleetUnitCount($mission) > 0) {
            // Create and start the return mission.
            $this->startReturn($mission);
        }
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to);

        // Transport return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->sum() > 0) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
