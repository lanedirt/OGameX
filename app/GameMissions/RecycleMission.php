<?php

namespace OGame\GameMissions;

use Exception;
use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMessages\DebrisFieldHarvest;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

class RecycleMission extends GameMission
{
    protected static string $name = 'Harvest';
    protected static int $typeId = 8;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::war;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Neutral;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Cannot send missions while in vacation mode
        if ($planet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'You cannot send missions while in vacation mode!');
        }

        // Recycle mission is only possible for debris fields.
        if ($targetType !== PlanetType::DebrisField) {
            return new MissionPossibleStatus(false);
        }

        // Check if this is an expedition debris field (position 16)
        $isExpeditionDebris = $targetCoordinate->position === 16;

        // For expedition debris (position 16): require Pathfinders
        // For regular debris (positions 1-15): require Recyclers
        if ($isExpeditionDebris) {
            // Expedition debris can only be harvested by Pathfinders
            if ($units->getAmountByMachineName('pathfinder') === 0) {
                return new MissionPossibleStatus(false);
            }
        } else {
            // Regular debris requires at least one recycler
            if ($units->getAmountByMachineName('recycler') === 0) {
                return new MissionPossibleStatus(false);
            }
        }

        // Check if debris field exists (including "ghost" fields with 0 resources).
        // In OGame, debris fields persist as invisible "ghost" fields after being fully harvested
        // until the weekly reset (Monday 1:00 AM). This allows players to send recyclers to
        // coordinates where a debris field existed, even if it currently has no resources.
        $debrisField = app(DebrisFieldService::class);
        $debrisFieldExists = $debrisField->loadForCoordinates($targetCoordinate);

        if (!$debrisFieldExists) {
            return new MissionPossibleStatus(false);
        }

        // Note: Debris fields can be harvested regardless of whether the planet owner
        // is in vacation mode. Only the sending player's vacation mode status is checked.

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

        // Check if this is expedition debris (position 16) - harvested by Pathfinders
        // or regular debris (positions 1-15) - harvested by Recyclers
        $isExpeditionDebris = $targetCoordinate->position === 16;

        if ($isExpeditionDebris) {
            // Get pathfinder unit count and capacity
            $harvesterShip = ObjectService::getShipObjectByMachineName('pathfinder');
            $harvesterCount = $this->fleetMissionService->getFleetUnits($mission)->getAmountByMachineName($harvesterShip->machine_name);
        } else {
            // Get recycler unit count and capacity
            $harvesterShip = ObjectService::getShipObjectByMachineName('recycler');
            $harvesterCount = $this->fleetMissionService->getFleetUnits($mission)->getAmountByMachineName($harvesterShip->machine_name);
        }

        // Calculate total cargo capacity.
        $total_cargo_capacity = $harvesterShip->properties->capacity->calculate($originPlanet->getPlayer())->totalValue * $harvesterCount;

        // Get resources from the debris field and take as much as the harvesters can carry.
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
            'ship_name' => $harvesterShip->title,
            'ship_amount' => $harvesterCount,
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
        // Add parent mission resources to harvested resources.
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $totalResources = new Resources(
            $mission->metal + $resourcesHarvested->metal->get(),
            $mission->crystal + $resourcesHarvested->crystal->get(),
            $mission->deuterium + $resourcesHarvested->deuterium->get(),
            0
        );
        $this->startReturn($mission, $totalResources, $units);
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
