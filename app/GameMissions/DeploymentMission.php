<?php

namespace OGame\GameMissions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\PlanetService;

class DeploymentMission extends GameMission
{
    protected static string $name = 'Deployment';

    protected static bool $hasReturnMission = false;

    public function start(PlanetService $planet, PlanetService $targetPlanet, int $missionType, UnitCollection $units, Resources $resources, int $parent_id = 0): void
    {
        // TODO: add sanity checks for the input data that enough resources and enough units, enough cargospace etc.
        if (!$planet->hasResources($resources)) {
            throw new Exception('Not enough resources on the planet to send the fleet.');
        }
        if (!$planet->hasUnits($units)) {
            throw new Exception('Not enough units on the planet to send the fleet.');
        }

        // Time this fleet mission will depart (now)
        $time_start = (int)Carbon::now()->timestamp;

        // Time fleet mission will arrive
        //TODO: refactor calculate to gamemission base class?
        $time_end = $time_start + $this->fleetMissionService->calculateFleetMissionDuration();

        $mission = new FleetMission();

        // Set the parent mission if it exists. This indicates that this mission is a follow-up (return)
        // mission from a previous mission.
        if (!empty($parent_id)) {
            $parentMission = $this->fleetMissionService->getFleetMissionById($parent_id);
            $mission->parent_id = $parentMission->id;
        }

        $mission->user_id = $planet->getPlayer()->getId();
        $mission->planet_id_from = $planet->getPlanetId();
        // TODO: validate mission type
        $mission->mission_type = $missionType;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;

        // TODO: update these to the actual target coordinates

        $mission->planet_id_to = $targetPlanet->getPlanetId();
        // Coordinates
        $coords = $targetPlanet->getPlanetCoordinates();
        $mission->galaxy_to = $coords->galaxy;
        $mission->system_to = $coords->system;
        $mission->position_to = $coords->position;

        // Fill in the units
        foreach ($units->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        // TODO: deduct units from planet
        $planet->removeUnits($units, false);

        // Fill in the resources
        $mission->metal = $resources->metal->getRounded();
        $mission->crystal = $resources->crystal->getRounded();
        $mission->deuterium = $resources->deuterium->getRounded();

        // TODO: deduct resources from planet

        // All OK, deduct resources.
        $planet->deductResources($resources);

        // Save the new fleet mission.
        $mission->save();
    }

    public function cancel(FleetMission $mission): void
    {
        // Mark parent mission as canceled.
        $mission->canceled = 1;
        $mission->processed = 1;
        $mission->save();

        // TODO: cancel main mission, then start return mission
        // based on how long main mission actually took (set time_arrival to now)
        $this->startReturn($mission);
    }

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

    protected function startReturn(FleetMission $mission): void {
        // No need to check for resources and units, as the return mission takes the units from the original
        // mission and the resources are already delivered. Nothing is deducted from the planet.
        // Get parent mission
        $parentMission = $this->fleetMissionService->getFleetMissionById($mission->id);

        // Time this fleet mission will depart (arrival time of the parent mission)
        $time_start = $parentMission->time_arrival;

        // Time fleet mission will arrive (arrival time of the parent mission + duration of the parent mission)
        // Return mission duration is always the same as the parent mission duration.
        $time_end = $time_start + ($parentMission->time_arrival - $parentMission->time_departure);

        // Create new return mission object
        $mission = new FleetMission();
        $mission->parent_id = $parentMission->id;
        $mission->user_id = $parentMission->user_id;
        $mission->planet_id_from = $parentMission->planet_id_to;
        $mission->mission_type = $parentMission->mission_type;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;
        $mission->planet_id_to = $parentMission->planet_id_from;

        // Planet from service
        $planetServiceFactory = app()->make(PlanetServiceFactory::class);
        $planetFromService = $planetServiceFactory->make($mission->planet_id_from);
        $planetToService = $planetServiceFactory->make($mission->planet_id_to);

        // Coordinates
        $coords = $planetToService->getPlanetCoordinates();
        $mission->galaxy_to = $coords->galaxy;
        $mission->system_to = $coords->system;
        $mission->position_to = $coords->position;

        // Fill in the units
        foreach ($this->fleetMissionService->getFleetUnits($parentMission)->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        // Fill in the resources. Return missions do not carry resources as they have been
        // offloaded at the target planet.
        $mission->metal = 0;
        $mission->crystal = 0;
        $mission->deuterium = 0;

        // Save the new fleet return mission.
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