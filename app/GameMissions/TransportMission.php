<?php

namespace OGame\GameMissions;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\Models\FleetMission;

class TransportMission extends GameMission
{
    protected static string $name = 'Transport';
    protected static int $typeId = 3;
    protected static bool $hasReturnMission = true;

    /**
     * @throws BindingResolutionException
     */
    protected function processArrival(FleetMission $mission): void
    {
        // Load the target planet
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $target_planet = $planetServiceFactory->make($mission->planet_id_to);

        // Add resources to the target planet
        $target_planet->addResources($this->fleetMissionService->getResources($mission));

        // Send a message to the player that the mission has arrived
        // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
        $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Reaching a planet', 'Your fleet from planet [planet]' . $mission->planet_id_from . '[/planet] reaches the planet [planet]' . $mission->planet_id_to . '[/planet] and delivers its goods:
Metal: ' . $mission->metal . ' Crystal: ' . $mission->crystal . ' Deuterium: ' . $mission->deuterium, 'transport_arrived');

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

        // Transport return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Send message to player that the return mission has arrived
        $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Return of a fleet', 'Your fleet is returning from planet [planet]' . $mission->planet_id_from . '[/planet] to planet [planet]' . $mission->planet_id_to . '[/planet].
                    
                    The fleet doesn\'t deliver goods.', 'return_of_fleet');

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}