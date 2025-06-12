<?php

namespace OGame\GameMissions;

use OGame\GameMessages\ColonyEstablished;
use OGame\GameMessages\ColonyEstablishFailAstrophysics;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

class ColonisationMission extends GameMission
{
    protected static string $name = 'Colonisation';
    protected static int $typeId = 7;
    protected static bool $hasReturnMission = false;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Colonisation mission is only possible for planets.
        if ($targetType !== PlanetType::Planet) {
            return new MissionPossibleStatus(false);
        }

        // If target planet already exists, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet !== null) {
            return new MissionPossibleStatus(false);
        }

        // Only possible for slots 1-15.
        if ($targetCoordinate->position < 1 || $targetCoordinate->position > 15) {
            return new MissionPossibleStatus(false);
        }

        // If no colony ships are present in the fleet, the mission is not possible.
        if ($units->getAmountByMachineName('colony_ship') === 0) {
            return new MissionPossibleStatus(false, __('You need a colony ship to colonize a planet.'));
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Sanity check: make sure the target coordinates are valid and the planet is (still) empty.
        $target_coordinates = new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to);
        $target_planet = $this->planetServiceFactory->makeForCoordinate($target_coordinates);

        // Load the mission owner user
        $player = $this->playerServiceFactory->make($mission->user_id, true);

        if ($target_planet != null) {
            // TODO: add unittest for this behavior.
            // Cancel the current mission.
            $this->cancel($mission);
            return;
        }

        // Sanity check: colonisation mission without a colony ship is not possible.
        if ($mission->colony_ship < 1) {
            // Cancel the current mission.
            $this->cancel($mission);
            return;
        }

        // Check if the astrophysics research level is high enough to colonize a new planet.
        $max_planets = $player->getMaxPlanetAmount();
        if ($player->planets->planetCount() + 1 > $max_planets) {
            // Astrophysics level is not high enough, send failed message and cancel the mission.
            $this->messageService->sendSystemMessageToPlayer($player, ColonyEstablishFailAstrophysics::class, [
                'coordinates' => $target_coordinates->asString(),
            ]);

            $this->cancel($mission);
            return;
        }

        // Create a new planet at the target coordinates.
        $target_planet = $this->planetServiceFactory->createAdditionalPlanetForPlayer($player, new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to));

        // Send success message
        $this->messageService->sendSystemMessageToPlayer($player, ColonyEstablished::class, [
            'coordinates' => $target_planet->getPlanetCoordinates()->asString(),
        ]);

        // Add resources to the target planet if the mission has any.
        $resources = $this->fleetMissionService->getResources($mission);
        $target_planet->addResources($resources);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Assembly new unit collection.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        // Remove one colony ship from the fleet as it was used to colonize the planet.
        $colony_ship = ObjectService::getUnitObjectByMachineName('colony_ship');
        $units->removeUnit($colony_ship, 1);

        // Create and start the return mission (if the colonisation mission had ships other than the colony ship itself).
        $this->startReturn($mission, new Resources(0, 0, 0, 0), $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Transport return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
