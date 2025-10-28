<?php

namespace OGame\GameMissions\Abstracts;

use Exception;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\GameMissions\ExpeditionMission;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

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

    protected SettingsService $settings;

    /**
     * @param FleetMissionService $fleetMissionService
     * @param MessageService $messageService
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PlayerServiceFactory $playerServiceFactory
     * @param SettingsService $settings
     */
    public function __construct(FleetMissionService $fleetMissionService, MessageService $messageService, PlanetServiceFactory $planetServiceFactory, PlayerServiceFactory $playerServiceFactory, SettingsService $settings)
    {
        $this->fleetMissionService = $fleetMissionService;
        $this->messageService = $messageService;
        $this->planetServiceFactory = $planetServiceFactory;
        $this->playerServiceFactory = $playerServiceFactory;
        $this->settings = $settings;
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
     * @param PlanetService $planet The planet from which the mission is sent.
     * @param Coordinate $targetCoordinate The target coordinate of the mission.
     * @param PlanetType $targetType The type of the target.
     * @param UnitCollection $units The units that are sent on the mission.
     * @return MissionPossibleStatus
     */
    abstract public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus;

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

        // Start the return mission with the resources and units of the original mission.
        $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $this->fleetMissionService->getFleetUnits($mission));
    }

    /**
     * Generic sanity checks before starting a mission to make sure all requirements are met.
     *
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param PlanetType $targetType
     * @param UnitCollection $units
     * @param Resources $resources
     * @return void
     * @throws Exception
     */
    public function startMissionSanityChecks(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units, Resources $resources): void
    {
        if (!$planet->hasResources($resources)) {
            throw new Exception('Not enough resources on the planet to send the fleet.');
        }

        if (!$planet->hasUnits($units)) {
            $unitNames = [];
            foreach ($units->units as $unit) {
                $unitNames[] = $unit->unitObject->machine_name;
            }

            $unitNames = implode(', ', $unitNames);
            throw new Exception('Not enough units on the planet to send the fleet. Units required: ' . $unitNames);
        }

        if ($planet->getPlayer()->getFleetSlotsInUse() >= $planet->getPlayer()->getFleetSlotsMax()) {
            throw new Exception('Maximum number of fleets reached.');
        }

        $missionPossibleStatus = $this->isMissionPossible($planet, $targetCoordinate, $targetType, $units);
        if (!$missionPossibleStatus->possible) {
            throw new Exception($missionPossibleStatus->reason ?? __('This mission is not possible.'));
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
     * @param PlanetService $planet The planet where the fleet is sent from.
     * @param Coordinate $targetCoordinate The target coordinate of the mission.
     * @param PlanetType $targetType The type of the target.
     * @param UnitCollection $units The units that are sent on the mission.
     * @param Resources $resources The resources that are sent on the mission.
     * @param float $speedPercent The speed percent of the fleet.
     * @param int $holdingHours The holding time of the fleet. The number represents the amount of hours the fleet will wait at the target planet and/or how long expedition will last.
     * @param int $parentId The parent mission ID if this is a follow-up mission.
     * @return FleetMission The created fleet mission.
     * @throws Exception
     */
    public function start(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units, Resources $resources, float $speedPercent, int $holdingHours = 0, int $parentId = 0): FleetMission
    {
        $consumption = $this->fleetMissionService->calculateConsumption($planet, $units, $targetCoordinate, $holdingHours, $speedPercent);
        $consumption_resources = new Resources(0, 0, $consumption, 0);

        $total_deuterium = $resources->deuterium->get() + $consumption_resources->deuterium->get();
        $deduct_resources = new Resources($resources->metal->get(), $resources->crystal->get(), $total_deuterium, 0);

        $this->startMissionSanityChecks($planet, $targetCoordinate, $targetType, $units, $deduct_resources);

        // Time this fleet mission will depart (now).
        $time_start = (int)Carbon::now()->timestamp;

        // Time fleet mission will arrive.
        // TODO: refactor calculate to gamemission base class?
        $time_end = $time_start + $this->fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, $speedPercent);

        $mission = new FleetMission();

        // Set the parent mission if it exists. This indicates that this mission is a follow-up (return)
        // mission linked to a previous mission.
        if (!empty($parentId)) {
            $parentMission = $this->fleetMissionService->getFleetMissionById($parentId);
            $mission->parent_id = $parentMission->id;
        }

        $mission->user_id = $planet->getPlayer()->getId();

        $mission->type_from = $planet->getPlanetType()->value;
        $mission->planet_id_from = $planet->getPlanetId();
        $mission->galaxy_from = $planet->getPlanetCoordinates()->galaxy;
        $mission->system_from = $planet->getPlanetCoordinates()->system;
        $mission->position_from = $planet->getPlanetCoordinates()->position;

        $mission->mission_type = static::$typeId;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;

        // Holding time is the amount of time the fleet will wait at the target planet and/or how long expedition will last.
        // The $holdingHours is in hours, so we convert it to seconds.
        // Only applies to expeditions (and ACS missions, but those are not implemented yet).
        if (static::class === ExpeditionMission::class) {
            $mission->time_holding = $holdingHours * 3600;
        }

        $mission->type_to = $targetType->value;
        $mission->deuterium_consumption = $consumption_resources->deuterium->get();

        // Only set the target planet ID if the target is a planet or moon.
        if ($targetType === PlanetType::Planet) {
            $targetPlanet = $this->planetServiceFactory->makePlanetForCoordinate($targetCoordinate);
            if ($targetPlanet !== null) {
                $mission->planet_id_to = $targetPlanet->getPlanetId();
            }
        } elseif ($targetType === PlanetType::Moon) {
            $targetPlanet = $this->planetServiceFactory->makeMoonForCoordinate($targetCoordinate);
            if ($targetPlanet !== null) {
                $mission->planet_id_to = $targetPlanet->getPlanetId();
            }
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
        $this->deductMissionResources($planet, $deduct_resources, $units);

        // Save the new fleet mission.
        $mission->save();

        // Check if the created mission arrival time is in the past. This can happen if the planet hasn't been updated
        // for some time and missions have already played out in the meantime.
        // If the mission is in the past, process it immediately.
        if ($mission->time_arrival < Carbon::now()->timestamp) {
            $this->process($mission);
        }

        return $mission;
    }

    /**
     * Start the return mission.
     *
     * @param FleetMission $parentMission The parent mission that the return mission is linked to.
     * @param Resources $resources The resources that are to be returned.
     * @param UnitCollection $units The units that are to be returned.
     * @param int $additionalReturnTripTime Time in seconds to add to the return trip duration (optional, used by expeditions). Can be positive or negative.
     * @return void
     */
    protected function startReturn(FleetMission $parentMission, Resources $resources, UnitCollection $units, int $additionalReturnTripTime = 0): void
    {
        if ($units->getAmount() === 0) {
            // No units to return, no need to create a return mission.
            // This can happen after a battle where all units were destroyed or after colonisation mission
            // which consumed the colony ship and had no other units.
            return;
        }

        // No need to check for resources and units, as the return mission takes the units from the original
        // mission and the resources are already delivered. Nothing is deducted from the planet.
        // Time this fleet mission will depart (arrival time of the parent mission)
        $time_start = $parentMission->time_arrival;

        // Time fleet mission will arrive (arrival time of the parent mission + duration of the parent mission)
        // Return mission duration is always the same as the parent mission duration.
        $time_end = $time_start + ($parentMission->time_arrival - $parentMission->time_departure) + $additionalReturnTripTime;

        // Create new return mission object
        $mission = new FleetMission();
        $mission->parent_id = $parentMission->id;
        $mission->user_id = $parentMission->user_id;

        // Set the type_from and type_to to the opposite of the parent mission.
        $mission->type_from = $parentMission->type_to;
        $mission->type_to = $parentMission->type_from;

        // If planet_id_to is not set, it can mean that the target planet was colonized or the mission was canceled.
        // In this case, we keep planet_id_from as null.
        if ($mission->type_to === PlanetType::Planet->value) {
            if ($parentMission->planet_id_to === null) {
                // Attempt to load it from the target coordinates.
                $targetPlanet = $this->planetServiceFactory->makeForCoordinate(new Coordinate($parentMission->galaxy_to, $parentMission->system_to, $parentMission->position_to));
                if ($targetPlanet !== null) {
                    $mission->planet_id_from = $targetPlanet->getPlanetId();
                } else {
                    $mission->planet_id_from = null;
                }
            } else {
                $mission->planet_id_from = $parentMission->planet_id_to;
            }
        }

        $mission->mission_type = $parentMission->mission_type;
        $mission->time_departure = $time_start;
        $mission->time_arrival = $time_end;
        $mission->planet_id_to = $parentMission->planet_id_from;

        // Coordinates
        $mission->galaxy_from = $parentMission->galaxy_to;
        $mission->system_from = $parentMission->system_to;
        $mission->position_from = $parentMission->position_to;

        $mission->galaxy_to = $parentMission->galaxy_from;
        $mission->system_to = $parentMission->system_from;
        $mission->position_to = $parentMission->position_from;

        // Fill in the units
        foreach ($units->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        // Set amount of resources to return based on provided resources in parameter.
        // This is the amount of resources that were gained and/or not used during the mission.
        // The logic is different for each mission type.
        // TODO: make this more smart: what if mission started with resources already, e.g. sending attack mission with resources?
        // With the current logic the resources from origin mission are lost, which is probably not correct?
        $mission->metal = (int)$resources->metal->get();
        $mission->crystal = (int)$resources->crystal->get();
        $mission->deuterium = (int)$resources->deuterium->get();

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
        switch ($mission->type_from) {
            case PlanetType::Planet->value:
            case PlanetType::Moon->value:
                if ($mission->planet_id_from !== null) {
                    $from = __('planet') . ' [planet]' . $mission->planet_id_from . '[/planet]';
                }
                break;
            case PlanetType::DebrisField->value:
                $from = '[debrisfield]' . $mission->galaxy_from . ':' . $mission->system_from . ':' . $mission->position_from . '[/debrisfield]';
                break;
        }

        $to = __('planet') . ' [planet]' . $mission->planet_id_to . '[/planet]';

        if ($return_resources->any()) {
            $params = [
                'from' => $from,
                'to' => $to,
                'metal' => (string)$mission->metal,
                'crystal' => (string)$mission->crystal,
                'deuterium' => (string)$mission->deuterium,
            ];

            $this->messageService->sendSystemMessageToPlayer($targetPlayer, ReturnOfFleetWithResources::class, $params);
        } else {
            $params = [
                'from' => $from,
                'to' => $to,
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
