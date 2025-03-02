<?php

namespace OGame\GameMissions;

use Exception;
use OGame\GameMessages\DebrisFieldHarvest;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\DebrisFieldService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

class RecycleMission extends GameMission
{
    protected static string $name = 'Harvest';
    protected static int $typeId = 8;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Recycle mission is only possible for debris fields.
        if ($targetType !== PlanetType::DebrisField) {
            return new MissionPossibleStatus(false);
        }

        // The recycle mission has to contain at least one recycler.
        if ($units->getAmountByMachineName('recycler') === 0) {
            return new MissionPossibleStatus(false);
        }

        // Check if debris field exists on the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisFieldExists = $debrisField->loadForCoordinates($targetCoordinate);
        if (!$debrisFieldExists || !$debrisField->getResources()->any()) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     *
     * @throws Exception
     */
    protected function processArrival(FleetMission $mission): void
    {
        $originPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);
        $targetCoordinate = new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to);

        // Load the debris field for the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisField->loadOrCreateForCoordinates($targetCoordinate);

        // Get recycler unit count
        $recycler = ObjectService::getShipObjectByMachineName('recycler');
        $recyclerCount = $this->fleetMissionService->getFleetUnits($mission)->getAmountByMachineName($recycler->machine_name);

        // Calculate total recycler capacity.
        $total_cargo_capacity = $recycler->properties->capacity->calculate($originPlanet->getPlayer())->totalValue * $recyclerCount;

        // Get resources from the debris field and take as much as the recyclers can carry.
        $resourcesToHarvest = $debrisField->getResources();
        $resourcesHarvested = LootService::distributeLoot($resourcesToHarvest, $total_cargo_capacity);

        // Remove the harvested resources from the debris field.
        if ($resourcesHarvested->any()) {
            $debrisField->deductResources($resourcesHarvested);
            $debrisField->save();
        }

        // Send a message to the player that the mission has arrived and the resources (if any) have been collected.
        $this->messageService->sendSystemMessageToPlayer($originPlanet->getPlayer(), DebrisFieldHarvest::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[debrisfield]' . $targetCoordinate->asString(). '[/debrisfield]',
            'coordinates' => '[coordinates]' . $targetCoordinate->asString() . '[/coordinates]',
            'ship_name' => $recycler->title,
            'ship_amount' => $recyclerCount,
            'storage_capacity' => $total_cargo_capacity,
            'metal' => $resourcesToHarvest->metal->get(),
            'crystal' => $resourcesToHarvest->crystal->get(),
            'deuterium' => $resourcesToHarvest->deuterium->get(),
            'harvested_metal' => $resourcesHarvested->metal->get(),
            'harvested_crystal' => $resourcesHarvested->crystal->get(),
            'harvested_deuterium' => $resourcesHarvested->deuterium->get(),
        ]);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $this->startReturn($mission, $resourcesHarvested, $units);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Recycle return trip: add back the units to the source planet.
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
