<?php

namespace OGame\GameMissions\Abstracts;

use Exception;
use Illuminate\Support\Facades\Date;
use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameMessages\ReturnOfFleet;
use OGame\GameMessages\ReturnOfFleetWithResources;
use OGame\GameMissions\AcsDefendMission;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\ExpeditionMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
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

    /**
     * @var FleetSpeedType The fleet speed type for this mission.
     */
    protected static FleetSpeedType $fleetSpeedType;

    /**
     * @var FleetMissionStatus The friendly status for UI styling.
     */
    protected static FleetMissionStatus $friendlyStatus;

    /**
     * @param FleetMissionService $fleetMissionService
     * @param MessageService $messageService
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PlayerServiceFactory $playerServiceFactory
     * @param SettingsService $settings
     */
    public function __construct(protected FleetMissionService $fleetMissionService, protected MessageService $messageService, protected PlanetServiceFactory $planetServiceFactory, protected PlayerServiceFactory $playerServiceFactory, protected SettingsService $settings)
    {
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
     * Get the fleet speed type for this mission.
     *
     * @return FleetSpeedType
     */
    public static function getFleetSpeedType(): FleetSpeedType
    {
        return static::$fleetSpeedType;
    }

    /**
     * Get the friendly status for UI styling.
     *
     * @return FleetMissionStatus
     */
    public static function getFriendlyStatus(): FleetMissionStatus
    {
        return static::$friendlyStatus;
    }

    /**
     * Checks if the mission is possible under the given circumstances.
     * Child classes should call parent::isMissionPossible() first and return early if not possible,
     * then add their own mission-specific checks.
     *
     * @param PlanetService $planet The planet from which the mission is sent.
     * @param Coordinate $targetCoordinate The target coordinate of the mission.
     * @param PlanetType $targetType The type of the target.
     * @param UnitCollection $units The units that are sent on the mission.
     * @return MissionPossibleStatus
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Cannot send missions while in vacation mode
        if ($planet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, __('You cannot send missions while in vacation mode!'));
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // Default: mission is possible. Child classes should call parent first and then add their own checks.
        return new MissionPossibleStatus(true);
    }

    /**
     * Cancel an already started mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function cancel(FleetMission $mission): void
    {
        $currentTime = (int)Date::now()->timestamp;

        // Store the original arrival time before modifying it.
        // This is needed to calculate the correct return trip adjustment for missions that have already arrived.
        $originalArrivalTime = $mission->time_arrival;

        // Check if the mission has already arrived (is holding at destination).
        $hasArrived = $mission->time_arrival <= $currentTime;

        // Always update time_arrival to now for consistency.
        // This ensures startReturn() calculates departure time as "now".
        $mission->time_arrival = $currentTime;

        // Clear the holding time for recalled missions (expeditions, ACS Defend, etc.)
        // The fleet should return immediately without waiting at the destination.
        // Only set to 0 if there was a holding time, to avoid changing null to 0 for missions that don't use holding time.
        if ($mission->time_holding !== null) {
            $mission->time_holding = 0;
        }

        // Mark parent mission as canceled.
        $mission->canceled = 1;
        $mission->processed = 1;
        $mission->save();

        // Start the return mission with the resources and units of the original mission.
        // getResources() already includes parent mission resources.
        // If the mission had already arrived, we need to adjust the return trip calculation.
        // The adjustment ensures the return takes the same time as the original outbound trip,
        // not including any elapsed hold time.
        $returnTripAdjustment = $hasArrived ? ($originalArrivalTime - $currentTime) : 0;
        $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $this->fleetMissionService->getFleetUnits($mission), $returnTripAdjustment);
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
     * Uses atomic database operations to prevent race conditions from concurrent fleet dispatches.
     * Both resources and units are deducted in a single transaction - if either fails, both are rolled back.
     *
     * @throws Exception If insufficient resources or units (atomic check failed).
     */
    public function deductMissionResources(PlanetService $planet, Resources $resources, UnitCollection $units): void
    {
        if (!$planet->deductResourcesAndUnitsAtomic($resources, $units)) {
            throw new Exception(__('Not enough resources or units on the planet to send the fleet.'));
        }
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

        $totalCargoCapacity = $units->getTotalCargoCapacity($planet->getPlayer());
        $totalFuelCapacity = $units->getTotalFuelCapacity($planet->getPlayer());

        // Check if the player has sufficient deuterium storage capacity for the fleet.
        if ($totalFuelCapacity < $consumption) {
            throw new Exception(__('You don\'t have sufficient storage capacity!'));
        }

        // Check if the fleet will exceed the fleet cargo capacity.
        $total_resources = $resources->sum();
        if ($total_resources > $totalCargoCapacity) {
            throw new Exception('Resources exceed fleet cargo capacity.');
        }

        // Time this fleet mission will depart (now).
        $time_start = (int)Date::now()->timestamp;

        // Time fleet mission will arrive.
        // TODO: refactor calculate to gamemission base class?
        $time_end = $time_start + $this->fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, $this, $speedPercent);

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
        // Applies to expeditions and ACS Defend missions.
        // Note: time_holding stores the "game time" (e.g., 1 hour = 3600 seconds) not the actual real-world duration.
        // The fleet_speed_holding multiplier is applied when calculating actual mission timings (see startReturn).
        if (static::class === ExpeditionMission::class) {
            $mission->time_holding = $holdingHours * 3600;
            $targetType = PlanetType::DeepSpace;
        } elseif (static::class === AcsDefendMission::class) {
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
        if ($mission->time_arrival < Date::now()->timestamp) {
            $this->process($mission);
        }

        return $mission;
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
     * Helper method for child classes to check if target player is in vacation mode.
     * Should be called after verifying target planet exists.
     *
     * @param PlanetService|null $targetPlanet The target planet/moon.
     * @return MissionPossibleStatus|null Returns MissionPossibleStatus if vacation mode blocks mission, null otherwise.
     */
    protected function checkTargetVacationMode(PlanetService|null $targetPlanet): MissionPossibleStatus|null
    {
        if ($targetPlanet !== null && $targetPlanet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, __('This player is in vacation mode!'));
        }
        return null;
    }

    /**
     * Helper method to check if target belongs to a protected admin user.
     *
     * @param PlanetService|null $targetPlanet The target planet/moon.
     * @param string $errorMessage Custom error message if protected.
     * @return MissionPossibleStatus|null Returns MissionPossibleStatus if protected, null otherwise.
     */
    protected function checkAdminProtection(PlanetService|null $targetPlanet, string $errorMessage): MissionPossibleStatus|null
    {
        if ($targetPlanet !== null && $targetPlanet->getPlayer()->getUsername(false) === 'Legor') {
            return new MissionPossibleStatus(false, $errorMessage);
        }
        return null;
    }

    /**
     * Helper method to check if target planet belongs to the same player (own planet check).
     *
     * @param PlanetService $planet The origin planet.
     * @param PlanetService|null $targetPlanet The target planet/moon.
     * @return MissionPossibleStatus|null Returns MissionPossibleStatus if same player, null otherwise.
     */
    protected function checkOwnPlanet(PlanetService $planet, PlanetService|null $targetPlanet): MissionPossibleStatus|null
    {
        if ($targetPlanet !== null && $planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }
        return null;
    }

    /**
     * Start the return mission.
     *
     * @param FleetMission $parentMission The parent mission that the return mission is linked to.
     * @param Resources $resources The resources that are to be returned. Should include parent mission resources if they need to be preserved.
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
        // Time this fleet mission will depart (arrival time of the parent mission + holding time if applicable)
        // For expeditions and ACS Defend, the holding time must be included as the mission doesn't complete until after the hold.
        // Apply fleet_speed_holding multiplier to convert "game time" to actual real-world time.
        $settingsService = app(SettingsService::class);
        $actualHoldingTime = $parentMission->time_holding !== null
            ? (int)($parentMission->time_holding / $settingsService->fleetSpeedHolding())
            : 0;

        $time_start = $parentMission->time_arrival + $actualHoldingTime;

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

        // Set planet_id_from if the return mission is coming from a planet or moon.
        // If planet_id_to is not set on the parent mission, it can mean that the target planet was colonized or the mission was canceled.
        // In this case, we attempt to load the planet from the target coordinates.
        if ($mission->type_from === PlanetType::Planet->value || $mission->type_from === PlanetType::Moon->value) {
            if ($parentMission->planet_id_to === null) {
                // Attempt to load it from the target coordinates.
                $targetPlanet = $this->planetServiceFactory->makeForCoordinate(new Coordinate($parentMission->galaxy_to, $parentMission->system_to, $parentMission->position_to));
                $mission->planet_id_from = $targetPlanet?->getPlanetId();
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

        // Set return mission resources.
        // Each mission type should explicitly add parent mission resources to the $resources parameter
        // before calling this method if they want to preserve them.
        $mission->metal = (int)$resources->metal->get();
        $mission->crystal = (int)$resources->crystal->get();
        $mission->deuterium = (int)$resources->deuterium->get();

        // Save the new fleet return mission.
        $mission->save();

        // Check if the created mission arrival time is in the past. This can happen if the planet hasn't been updated
        // for some time and missions have already played out in the meantime.
        // If the mission is in the past, process it immediately.
        if ($mission->time_arrival < Date::now()->timestamp) {
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
        $from = "[coordinates]{$mission->galaxy_from}:{$mission->system_from}:{$mission->position_from}[/coordinates]";
        switch ($mission->type_from) {
            case PlanetType::Planet->value:
            case PlanetType::Moon->value:
                if ($mission->planet_id_from !== null) {
                    $from = __('planet') . " [planet]{$mission->planet_id_from}[/planet]";
                }
                break;
            case PlanetType::DebrisField->value:
                $from = "[debrisfield]{$mission->galaxy_from}:{$mission->system_from}:{$mission->position_from}[/debrisfield]";
                break;
        }

        $to = __('planet') . " [planet]{$mission->planet_id_to}[/planet]";

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
     * Collect all defending fleets at a planet (planet owner + ACS defend fleets).
     *
     * @param PlanetService $planet The planet being defended.
     * @return array<DefenderFleet> Array of all defending fleets.
     */
    protected function collectDefendingFleets(PlanetService $planet): array
    {
        $defenders = [];

        // Always add the planet owner's forces first
        $defenders[] = DefenderFleet::fromPlanet($planet);

        // Find all ACS Defend fleets currently holding at this planet
        $defendMissions = FleetMission::query()
            ->where('mission_type', 5)  // ACS Defend
            ->where('planet_id_to', $planet->getPlanetId())
            ->where('processed', 0)  // Still active
            ->where('time_arrival', '<=', Date::now()->timestamp)  // Has arrived
            ->whereRaw('time_arrival + COALESCE(time_holding, 0) > ?', [Date::now()->timestamp])  // Still holding
            ->get();

        // Add each defending fleet
        foreach ($defendMissions as $mission) {
            $defenders[] = DefenderFleet::fromFleetMission(
                $mission,
                $this->fleetMissionService,
                $this->playerServiceFactory
            );
        }

        return $defenders;
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
