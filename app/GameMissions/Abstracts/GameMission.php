<?php

namespace OGame\GameMissions\Abstracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Services\PlanetService;

abstract class GameMission
{
    /**
     * @var string The name of the mission shown in the GUI.
     */
    protected static string $name;

    /**
     * @var int The type ID of the mission.
     */
    protected static int $typeId;

    /**
     * @var bool Whether this mission has a return mission by default.
     */
    protected static bool $hasReturnMission;

    protected FleetMissionService $fleetMissionService;

    protected MessageService $messageService;

    public function __construct(FleetMissionService $fleetMissionService, MessageService $messageService)
    {
        $this->fleetMissionService = $fleetMissionService;
        $this->messageService = $messageService;
    }

    public static function getName(): string
    {
        return static::$name;
    }

    public static function hasReturnMission(): bool
    {
        return static::$hasReturnMission;
    }

    /**
     * Cancel an already started mission.
     *
     * @param FleetMission $mission
     * @return void
     * @throws BindingResolutionException
     */
    public function cancel(FleetMission $mission): void
    {
        // Mark parent mission as canceled.
        $mission->canceled = 1;
        $mission->processed = 1;
        $mission->save();

        // Start the return mission.
        $this->startReturn($mission);
    }

    /**
     * Generic sanity checks before starting a mission to make sure the planet has enough resources and units.
     *
     * @param PlanetService $planet
     * @param UnitCollection $units
     * @param Resources $resources
     * @return void
     * @throws \Exception
     */
    public function startMissionSanityChecks(PlanetService $planet, UnitCollection $units, Resources $resources): void
    {
        if (!$planet->hasResources($resources)) {
            throw new \Exception('Not enough resources on the planet to send the fleet.');
        }
        if (!$planet->hasUnits($units)) {
            throw new \Exception('Not enough units on the planet to send the fleet.');
        }
    }

    /**
     * Deduct mission resources from the planet (when starting mission).
     *
     * @throws \Exception
     */
    public function deductMissionResources(PlanetService $planet, Resources $resources, UnitCollection $units): void
    {
        $planet->deductResources($resources, false);
        $planet->removeUnits($units, false);
    }

    /**
     * Start a new mission.
     *
     * @param PlanetService $planet
     * @param PlanetService $targetPlanet
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $parent_id
     * @return void
     * @throws \Exception
     */
    public function start(PlanetService $planet, PlanetService $targetPlanet, UnitCollection $units, Resources $resources, int $parent_id = 0): void
    {
        $this->startMissionSanityChecks($planet, $units, $resources);

        // Time this fleet mission will depart (now)
        $time_start = (int)Carbon::now()->timestamp;

        // Time fleet mission will arrive
        //TODO: refactor calculate to gamemission base class?
        $time_end = $time_start + $this->fleetMissionService->calculateFleetMissionDuration();

        $mission = new FleetMission();

        // Set the parent mission if it exists. This indicates that this mission is a follow-up (return)
        // mission linked to a previous mission.
        if (!empty($parent_id)) {
            $parentMission = $this->fleetMissionService->getFleetMissionById($parent_id);
            $mission->parent_id = $parentMission->id;
        }

        $mission->user_id = $planet->getPlayer()->getId();
        $mission->planet_id_from = $planet->getPlanetId();
        $mission->mission_type = static::$typeId;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;

        $mission->planet_id_to = $targetPlanet->getPlanetId();
        // Coordinates
        $coords = $targetPlanet->getPlanetCoordinates();
        $mission->galaxy_to = $coords->galaxy;
        $mission->system_to = $coords->system;
        $mission->position_to = $coords->position;

        // Define units
        foreach ($units->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }
        // Define resources
        $mission->metal = $resources->metal->getRounded();
        $mission->crystal = $resources->crystal->getRounded();
        $mission->deuterium = $resources->deuterium->getRounded();

        // Deduct mission resources from the planet.
        $this->deductMissionResources($planet, $resources, $units);

        // Save the new fleet mission.
        $mission->save();
    }

    /**
     * Start the return mission.
     *
     * @param FleetMission $parentMission
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function startReturn(FleetMission $parentMission): void
    {
        // No need to check for resources and units, as the return mission takes the units from the original
        // mission and the resources are already delivered. Nothing is deducted from the planet.
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

    /**
     * Process the mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function process(FleetMission $mission): void
    {
        if (!empty($mission->parent_id)) {
            // This is a return mission as it has a parent mission.
            $this->processReturn($mission);
            return;
        } else {
            // This is an arrival mission as it has no parent mission.
            // Process arrival.
            $this->processArrival($mission);
        }
    }

    /**
     * Process the mission arrival (first stage, required).
     *
     * @param FleetMission $mission
     * @return void
     */
    abstract protected function processArrival(FleetMission $mission): void;

    /**
     * Process the mission return (second stage, optional).
     *
     * @param FleetMission $mission
     * @return void
     */
    abstract protected function processReturn(FleetMission $mission): void;
}
