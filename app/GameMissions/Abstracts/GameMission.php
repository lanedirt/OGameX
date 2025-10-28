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
    protected static string $name;
    protected static int $typeId;
    protected static bool $hasReturnMission;

    protected FleetMissionService $fleetMissionService;
    protected MessageService $messageService;
    protected PlanetServiceFactory $planetServiceFactory;
    protected PlayerServiceFactory $playerServiceFactory;
    protected SettingsService $settings;

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

    abstract public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus;

    public function cancel(FleetMission $mission): void
    {
        $mission->time_arrival = (int)Carbon::now()->timestamp;
        $mission->canceled = 1;
        $mission->processed = 1;
        $mission->save();

        $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $this->fleetMissionService->getFleetUnits($mission));
    }

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

    public function deductMissionResources(PlanetService $planet, Resources $resources, UnitCollection $units): void
    {
        $planet->deductResources($resources, false);
        $planet->removeUnits($units, false);
        $planet->save();
    }

    public function start(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units, Resources $resources, float $speedPercent, int $holdingHours = 0, int $parentId = 0): FleetMission
    {
        $consumption = $this->fleetMissionService->calculateConsumption($planet, $units, $targetCoordinate, $holdingHours, $speedPercent);
        $consumption_resources = new Resources(0, 0, $consumption, 0);

        $total_deuterium = $resources->deuterium->get() + $consumption_resources->deuterium->get();
        $deduct_resources = new Resources($resources->metal->get(), $resources->crystal->get(), $total_deuterium, 0);

        $this->startMissionSanityChecks($planet, $targetCoordinate, $targetType, $units, $deduct_resources);

        $time_start = (int)Carbon::now()->timestamp;
        $time_end = $time_start + $this->fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, $speedPercent);

        $mission = new FleetMission();

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

        if (static::class === ExpeditionMission::class) {
            $mission->time_holding = $holdingHours * 3600;
        }

        // Deep Space fix: set type_to to DeepSpace for expeditions
        if (static::class === ExpeditionMission::class) {
            $mission->type_to = PlanetType::DeepSpace->value;
        } else {
            $mission->type_to = $targetType->value;
        }

        $mission->deuterium_consumption = $consumption_resources->deuterium->get();

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

        $this->deductMissionResources($planet, $deduct_resources, $units);
        $mission->save();

        if ($mission->time_arrival < Carbon::now()->timestamp) {
            $this->process($mission);
        }
        return $mission;
    }

    /**
     * Start the return mission.
     */
    protected function startReturn(FleetMission $parentMission, Resources $resources, UnitCollection $units, int $additionalReturnTripTime = 0): void
    {
        if ($units->getAmount() === 0) {
            return;
        }

        // Return mission: vanilla logic, no hold-time-based delay for all cases
        $time_start = $parentMission->time_arrival;
        $time_end = $time_start + ($parentMission->time_arrival - $parentMission->time_departure) + $additionalReturnTripTime;

        $mission = new FleetMission();
        $mission->parent_id = $parentMission->id;
        $mission->user_id = $parentMission->user_id;
        $mission->type_from = $parentMission->type_to;
        $mission->type_to = $parentMission->type_from;

        if ($mission->type_to === PlanetType::Planet->value) {
            if ($parentMission->planet_id_to === null) {
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

        $mission->galaxy_from = $parentMission->galaxy_to;
        $mission->system_from = $parentMission->system_to;
        $mission->position_from = $parentMission->position_to;

        $mission->galaxy_to = $parentMission->galaxy_from;
        $mission->system_to = $parentMission->system_from;
        $mission->position_to = $parentMission->position_from;

        foreach ($units->units as $unit) {
            $mission->{$unit->unitObject->machine_name} = $unit->amount;
        }

        $mission->metal = (int)$resources->metal->get();
        $mission->crystal = (int)$resources->crystal->get();
        $mission->deuterium = (int)$resources->deuterium->get();

        $mission->save();

        if ($mission->time_arrival < Carbon::now()->timestamp) {
            $this->process($mission);
        }
    }

    protected function sendFleetReturnMessage(FleetMission $mission, PlayerService $targetPlayer): void
    {
        $return_resources = $this->fleetMissionService->getResources($mission);

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

    public function process(FleetMission $mission): void
    {
        if (empty($mission->parent_id)) {
            $this->processArrival($mission);
        } else {
            $this->processReturn($mission);
        }
    }

    abstract protected function processArrival(FleetMission $mission): void;

    abstract protected function processReturn(FleetMission $mission): void;
}
