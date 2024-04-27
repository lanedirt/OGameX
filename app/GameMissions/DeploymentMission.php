<?php

namespace OGame\GameMissions;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\Models\FleetMission;

class DeploymentMission extends GameMission
{
    protected static string $name = 'Deployment';
    protected static int $typeId = 4;
    protected static bool $hasReturnMission = false;

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

        // TODO: message with resources delivered is different than message with no resources delivered
        /**

        Your fleet is returning from planet Homeworld [1:237:6] to planet FARRT [1:237:8].

        The fleet is delivering:

        Metal: 1
        Crystal: 0
        Deuterium: 0
        Food: 0
         */

        // Send a message to the player that the mission has arrived
        // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
        $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Fleet deployment', 'One of your fleets from [planet]' . $mission->planet_id_from . '[/planet] has reached [planet]' . $mission->planet_id_to . '[/planet]. The fleet doesn`t deliver goods.', 'fleet_deployment');
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