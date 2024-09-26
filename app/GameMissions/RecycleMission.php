<?php

namespace OGame\GameMissions;

use OGame\GameMessages\DebrisFieldHarvest;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
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

        // Check if debris field exists on the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisField->loadForCoordinates($targetCoordinate);
        if (!$debrisField->getResources()->any()) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        $originPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);
        $targetCoordinate = new Coordinate($mission->galaxy_to, $mission->system_to, $mission->position_to);

        // Load the debris field for the target coordinate.
        $debrisField = app(DebrisFieldService::class);
        $debrisField->loadForCoordinates($targetCoordinate);

        // Get recycler unit count
        $recycler = $originPlanet->objects->getShipObjectByMachineName('recycler');
        $recyclerCount = $this->fleetMissionService->getFleetUnits($mission)->getAmountByMachineName($recycler->machine_name);

        // Calculate total recycler capacity.
        $total_capacity = $recycler->properties->capacity->calculate($originPlanet->getPlayer())->totalValue * $recyclerCount;

        // Recycle the debris field resources.
        $resources = $debrisField->getResources();

        // TODO: Take the maximum amount of resources that the fleet can carry.
        $metal_to_harvest = $resources->metal;
        $crystal_to_harvest = $resources->crystal;
        $deuterium_to_harvest = $resources->deuterium;

        // If the fleet can't carry all the resources, take as much as possible.
        $metal_harvested = $metal_to_harvest;
        $crystal_harvested = $crystal_to_harvest;
        $deuterium_harvested = $deuterium_to_harvest;

        // Send a message to the player that the mission has arrived and the resources (if any) have been collected.
        $this->messageService->sendSystemMessageToPlayer($originPlanet->getPlayer(), DebrisFieldHarvest::class, [
            'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
            'to' => '[debrisfield]' . $targetCoordinate->asString(). '[/debrisfield]',
            'coordinates' => '[coordinates]' . $targetCoordinate->asString() . '[/coordinates]',
            'ship_name' => $recycler->title,
            'ship_amount' => $recyclerCount,
            'storage_capacity' => $total_capacity,
            'metal' => $metal_to_harvest->get(),
            'crystal' => $crystal_to_harvest->get(),
            'deuterium' => $deuterium_to_harvest->get(),
            'harvested_metal' => $metal_harvested->get(),
            'harvested_crystal' => $crystal_harvested->get(),
            'harvested_deuterium' => $deuterium_harvested->get(),
        ]);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $this->startReturn($mission, new Resources($metal_harvested->get(), $crystal_harvested->get(), $deuterium_harvested->get(), 0), $units);
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
