<?php

namespace OGame\GameMissions\Abstracts;

use Exception;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

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

    protected PlanetServiceFactory $planetServiceFactory;

    protected PlayerServiceFactory $playerServiceFactory;

    public function __construct(FleetMissionService $fleetMissionService, MessageService $messageService, PlanetServiceFactory $planetServiceFactory, PlayerServiceFactory $playerServiceFactory)
    {
        $this->fleetMissionService = $fleetMissionService;
        $this->messageService = $messageService;
        $this->planetServiceFactory = $planetServiceFactory;
        $this->playerServiceFactory = $playerServiceFactory;
    }

    public static function getName(): string
    {
        return static::$name;
    }

    public static function hasReturnMission(): bool
    {
        return static::$hasReturnMission;
    }

    public static function getTypeId(): int
    {
        return static::$typeId;
    }

    /**
     * Checks if the mission is possible under the given circumstances.
     *
     * @param PlanetService $planet
     * @param ?PlanetService $targetPlanet
     * @param UnitCollection $units
     * @return MissionPossibleStatus
     */
    abstract public function isMissionPossible(PlanetService $planet, ?PlanetService $targetPlanet, UnitCollection $units): MissionPossibleStatus;

    /**
     * Cancel an already started mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function cancel(FleetMission $mission): void
    {
        // Update the mission arrived time to now instead of original planned arrival time if the mission would finish by itself.
        // This arrival time is used by the return mission to calculate the return time.
        $mission->time_arrival = (int)Carbon::now()->timestamp;

        // Mark parent mission as canceled.
        $mission->canceled = 1;
        $mission->processed = 1;
        $mission->save();

        // Start the return mission.
        $this->startReturn($mission);
    }

    /**
     * Generic sanity checks before starting a mission to make sure all requirements are met.
     *
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param UnitCollection $units
     * @param Resources $resources
     * @return void
     * @throws Exception
     */
    public function startMissionSanityChecks(PlanetService $planet, Coordinate $targetCoordinate, UnitCollection $units, Resources $resources): void
    {
        if (!$planet->hasResources($resources)) {
            throw new Exception('Not enough resources on the planet to send the fleet.');
        }
        if (!$planet->hasUnits($units)) {
            throw new Exception('Not enough units on the planet to send the fleet.');
        }
    }

    /**
     * Deduct mission resources from the planet (when starting mission).
     */
    public function deductMissionResources(PlanetService $planet, Resources $resources, UnitCollection $units): void
    {
        $planet->deductResources($resources, false);
        $planet->removeUnits($units, false);

        // Save the planet to commit removed resources/units.
        $planet->save();
    }

    /**
     * Start a new mission.
     *
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $parent_id
     * @return void
     */
    public function start(PlanetService $planet, Coordinate $targetCoordinate, UnitCollection $units, Resources $resources, int $parent_id = 0): void
    {
        $this->startMissionSanityChecks($planet, $targetCoordinate, $units, $resources);

        // Time this fleet mission will depart (now)
        $time_start = (int)Carbon::now()->timestamp;

        // Time fleet mission will arrive
        // TODO: refactor calculate to gamemission base class?
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
        $mission->galaxy_from = $planet->getPlanetCoordinates()->galaxy;
        $mission->system_from = $planet->getPlanetCoordinates()->system;
        $mission->position_from = $planet->getPlanetCoordinates()->position;

        $mission->mission_type = static::$typeId;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;

        $target_planet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate);
        if ($target_planet !== null) {
            $mission->planet_id_to = $target_planet->getPlanetId();
        }
        $mission->galaxy_to = $targetCoordinate->galaxy;
        $mission->system_to = $targetCoordinate->system;
        $mission->position_to = $targetCoordinate->position;

        foreach ($units->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        $mission->metal = $resources->metal->getRounded();
        $mission->crystal = $resources->crystal->getRounded();
        $mission->deuterium = $resources->deuterium->getRounded();

        // Deduct mission resources from the planet.
        $this->deductMissionResources($planet, $resources, $units);

        // Save the new fleet mission.
        $mission->save();

        // Check if the created mission arrival time is in the past. This can happen if the planet hasn't been updated
        // for some time and missions have already played out in the meantime.
        // If the mission is in the past, process it immediately.
        if ($mission->time_arrival < Carbon::now()->timestamp) {
            $this->process($mission);
        }
    }

    /**
     * Start the return mission.
     *
     * @param FleetMission $parentMission
     * @return void
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

        // If planet_id_to is not set, it can mean that the target planet was colonized or the mission was canceled.
        // In this case, we keep planet_id_from as null.
        if ($parentMission->planet_id_to === null) {
            // Attempt to load it from the target coordinates.
            $targetPlanet = $this->planetServiceFactory->makeForCoordinate(new Coordinate($parentMission->galaxy_to, $parentMission->system_to, $parentMission->position_to));
            if ($targetPlanet !== null) {
                $mission->planet_id_from = $targetPlanet->getPlanetId();
                $mission->galaxy_from = $targetPlanet->getPlanetCoordinates()->galaxy;
                $mission->system_from = $targetPlanet->getPlanetCoordinates()->system;
                $mission->position_from = $targetPlanet->getPlanetCoordinates()->position;
            } else {
                $mission->planet_id_from = null;
                $mission->galaxy_from = $parentMission->galaxy_to;
                $mission->system_from = $parentMission->system_to;
                $mission->position_from = $parentMission->position_to;
            }
        } else {
            $mission->planet_id_from = $parentMission->planet_id_to;
            $mission->galaxy_from = $parentMission->galaxy_to;
            $mission->system_from = $parentMission->system_to;
            $mission->position_from = $parentMission->position_to;
        }
        $mission->mission_type = $parentMission->mission_type;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;
        $mission->planet_id_to = $parentMission->planet_id_from;

        // Planet from service
        $planetToService = $this->planetServiceFactory->make($mission->planet_id_to);

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
        // TODO: this assumption is not true for all mission types. Refactor this to be more flexible.
        // TODO: attack and expedition missions should be able to carry "new" resources back.
        // TODO: also if transport mission has been started but is then canceled, the resources should be returned.
        // Change this current implementation to be more flexible!
        // Add unittest for this in mission to self cancel test.
        $mission->metal = 0;
        $mission->crystal = 0;
        $mission->deuterium = 0;
        if ($parentMission->canceled == 1) {
            // If the parent mission was canceled, return the resources to the source planet via the return mission.
            // TODO: do we want to clear the resources from the parent mission or leave as-is for bookkeeping purposes?
            $mission->metal = $parentMission->metal;
            $mission->crystal = $parentMission->crystal;
            $mission->deuterium = $parentMission->deuterium;
        }

        // Save the new fleet return mission.
        $mission->save();

        // Check if the created mission arrival time is in the past. This can happen if the planet hasn't been updated
        // for some time and missions have already played out in the meantime.
        // If the mission is in the past, process it immediately.
        if ($mission->time_arrival < Carbon::now()->timestamp) {
            $this->process($mission);
        }
    }

    /**
     * Send a message to the player that a fleet has returned.
     *
     * @param FleetMission $mission
     * @param PlayerService $targetPlayer
     * @return void
     */
    protected function sendFleetReturnMessage(FleetMission $mission, PlayerService $targetPlayer): void
    {
        $return_resources = $this->fleetMissionService->getResources($mission);

        // Define from string based on whether the planet is available or not.
        $from = '[coordinates]' . $mission->galaxy_from . ':' . $mission->system_from . ':' . $mission->position_from . '[/coordinates]';
        if ($mission->planet_id_from !== null) {
            $from = '[planet]' . $mission->planet_id_from . '[/planet]';
        }

        if ($return_resources->sum() > 0) {
            $params = [
                'from' => $from,
                'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
                'metal' => (string)$mission->metal,
                'crystal' => (string)$mission->crystal,
                'deuterium' => (string)$mission->deuterium,
            ];
            $this->messageService->sendSystemMessageToPlayer($targetPlayer, ReturnOfFleetWithResources::class, $params);
        } else {
            $params = [
                'from' => $from,
                'to' => '[planet]' . $mission->planet_id_to . '[/planet]',
            ];
            $this->messageService->sendSystemMessageToPlayer($targetPlayer, ReturnOfFleet::class, $params);

        }
    }

    /**
     * Process the mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function process(FleetMission $mission): void
    {
        if (empty($mission->parent_id)) {
            // This is an arrival mission as it has no parent mission.
            // Process arrival.
            $this->processArrival($mission);
        } else {
            // This is a return mission as it has a parent mission.
            $this->processReturn($mission);
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
