<?php

namespace OGame\GameMissions;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Services\PlanetService;

class TransportMission extends GameMission
{
    protected static string $name = 'Transport';
    protected static int $typeId = 3;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus
    {
        if ($targetPlanet != null) {
            if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
                // If target player is the same as the current player, this mission is possible.
                return new MissionPossibleStatus(true);
            }
        }

        return new MissionPossibleStatus(false);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function processArrival(FleetMission $mission): void
    {
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        // Load origin planet
        $origin_planet = $planetServiceFactory->make($mission->planet_id_from);
        // Load the target planet
        $target_planet = $planetServiceFactory->make($mission->planet_id_to);

        // Add resources to the target planet
        $target_planet->addResources($this->fleetMissionService->getResources($mission));

        // Send a message to the origin player that the mission has arrived
        // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
        $this->messageService->sendMessageToPlayer($origin_planet->getPlayer(), 'Reaching a planet', 'Your fleet from planet [planet]' . $mission->planet_id_from . '[/planet] reaches the planet [planet]' . $mission->planet_id_to . '[/planet] and delivers its goods:
Metal: ' . $mission->metal . ' Crystal: ' . $mission->crystal . ' Deuterium: ' . $mission->deuterium, 'transport_arrived');

        if ($origin_planet->getPlayer()->getId() !== $target_planet->getPlayer()->getId()) {
            // Send a message to the target player that the mission has arrived
            $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Incoming fleet', 'An incoming fleet from planet [planet]' . $mission->planet_id_from . '[/planet] has reached your planet [planet]' . $mission->planet_id_to . '[/planet] and delivered its goods:
Metal: ' . $mission->metal . ' Crystal: ' . $mission->crystal . ' Deuterium: ' . $mission->deuterium, 'transport_received');
        }

        // Create and start the return mission.
        $this->startReturn($mission);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $target_planet = $planetServiceFactory->make($mission->planet_id_to);

        // Transport return trip: add back the units to the source planet.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        // TODO: make messages translatable by using tokens instead of directly inserting dynamic content.
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->sum() > 0) {
            $target_planet->addResources($return_resources);

            // Send message to player that the return mission has arrived
            $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Return of a fleet', 'Your fleet is returning from planet [planet]' . $mission->planet_id_from . '[/planet] to planet [planet]' . $mission->planet_id_to . '[/planet] and delivered its goods:
            
Metal: ' . $mission->metal . '
Crystal: ' . $mission->crystal . '
Deuterium: ' . $mission->deuterium, 'return_of_fleet');
        } else {
            // Send message to player that the return mission has arrived
            $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Return of a fleet', 'Your fleet is returning from planet [planet]' . $mission->planet_id_from . '[/planet] to planet [planet]' . $mission->planet_id_to . '[/planet].
                    
                    The fleet doesn\'t deliver goods.', 'return_of_fleet');
        }

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
