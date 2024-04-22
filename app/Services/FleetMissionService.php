<?php

namespace OGame\Services;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

/**
 * Class UnitQueueService.
 *
 * UnitQueueService object.
 *
 * @package OGame\Services
 */
class FleetMissionService
{
    /**
     * Mission type to label mapping.
     *
     * @var string[]
     */
    protected array $type_to_label = [
        1 => 'Attack',
        2 => 'ACS Defend',
        3 => 'Transport',
        4 => 'Deploy',
        5 => 'ACS Attack',
        6 => 'Spy',
        7 => 'Colonize',
        8 => 'Recycle',
        9 => 'Destroy',
        15 => 'Expedition',
    ];

    /**
     * Player service
     *
     * @var PlayerService
     */
    protected PlayerService $player;

    /**
     * @var ObjectService $objects
     */
    protected ObjectService $objects;

    /**
     * @var MessageService $messageService
     */
    protected MessageService $messageService;

    /**
     * The queue model where this class should get its data from.
     *
     * @var FleetMission
     */
    protected FleetMission $model;

    /**
     * FleetMissionService constructor.
     */
    public function __construct(PlayerService $player, ObjectService $objects, MessageService $messageService)
    {
        $this->player = $player;
        $this->objects = $objects;
        $this->messageService = $messageService;

        $model_name = 'OGame\Models\FleetMission';
        $this->model = new $model_name();
    }

    /**
     * Creates a new fleet mission for the current planet.
     *
     * @param PlanetService $planet
     * @param PlanetService $targetPlanet
     * @param int $missionType
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $parent_id
     * @return void
     * @throws Exception
     */
    public function createNewFromPlanet(PlanetService $planet, PlanetService $targetPlanet, int $missionType, UnitCollection $units, Resources $resources, int $parent_id = 0): void
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
        $time_end = $time_start + $this->calculateFleetMissionDuration();

        $mission = new $this->model;

        // Set the parent mission if it exists. This indicates that this mission is a follow-up (return)
        // mission from a previous mission.
        if (!empty($parent_id)) {
            $parentMission = $this->model->find($parent_id);
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

    /**
     * Creates a new fleet mission for the current planet.
     *
     * @param int $parent_mission_id
     * @return void
     * @throws Exception
     */
    public function createReturnFromMission(int $parent_mission_id = 0): void
    {
        // No need to check for resources and units, as the return mission takes the units from the original
        // mission and the resources are already delivered. Nothing is deducted from the planet.
        // Get parent mission
        $parentMission = $this->model->find($parent_mission_id);

        // Time this fleet mission will depart (arrival time of the parent mission)
        $time_start = $parentMission->time_arrival;

        // Time fleet mission will arrive (arrival time of the parent mission + duration of the parent mission)
        // Return mission duration is always the same as the parent mission duration.
        $time_end = $time_start + ($parentMission->time_arrival - $parentMission->time_departure);

        // Create new return mission object
        $mission = new $this->model;
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
        foreach ($this->getFleetUnits($parentMission)->units as $unit) {
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
     * Calculate the max speed of a fleet based on the current planet and fleet content.
     *
     * @return int
     */
    public function calculateMaxSpeed(): int
    {
        return 100;
    }

    /**
     * Calculate the duration of a fleet mission based on the current planet, target coordinates and fleet.
     *
     * @return int
     */
    public function calculateFleetMissionDuration(): int
    {
        return 300;
    }

    /**
     * Convert a mission type to a human readable label.
     *
     * @param int $missionType
     * @return string
     */
    public function missionTypeToLabel(int $missionType): string
    {
        return $this->type_to_label[$missionType] ?? 'Unknown';
    }

    /**
     * Get all active fleet missions for the current user.
     *
     * @return Collection<FleetMission>
     */
    public function getActiveFleetMissionsForCurrentPlayer() : Collection
    {
        return $this->model->where([
                ['user_id', $this->player->getId()],
                ['processed', 0],
            ])
            ->orderBy('time_arrival', 'asc')
            ->get();
    }

    /**
     * Get the total unit count of a fleet mission.
     *
     * @param FleetMission $mission
     * @return int
     */
    public function getFleetUnitCount(FleetMission $mission): int
    {
        // Loop through all known unit types and sum them up.
        $unit_count = 0;

        foreach ($this->objects->getShipObjects() as $ship) {
            $unit_count += $mission->{$ship->machine_name};
        }

        return $unit_count;
    }

    /**
     * Returns the units of a fleet mission.
     *
     * @param FleetMission $mission
     * @return UnitCollection
     */
    public function getFleetUnits(FleetMission $mission): UnitCollection
    {
        $units = new UnitCollection();

        foreach ($this->objects->getShipObjects() as $ship) {
            $amount = $mission->{$ship->machine_name};
            if ($amount > 0) {
                $units->addUnit($ship, $mission->{$ship->machine_name});
            }
        }

        return $units;
    }

    /**
     * Returns the resources of a fleet mission.
     *
     * @param FleetMission $mission
     * @return Resources
     */
    public function getResources(FleetMission $mission): Resources
    {
        return new Resources(
            $mission->metal,
            $mission->crystal,
            $mission->deuterium,
            0
        );
    }

    /**
     * Get missions that are either from or to the given planet that have reached the arrival time
     * but are not processed yet.
     *
     * @param int $planetId
     * @return Collection
     */
    public function getMissionsByPlanetId(int $planetId): Collection
    {
        return $this->model
            ->where(function ($query) use ($planetId) {
                $query->where('planet_id_from', $planetId)
                    ->orWhere('planet_id_to', $planetId);
            })
            ->where('time_arrival', '<=', Carbon::now()->timestamp)
            ->where('processed', 0)
            ->where('canceled', 0)
            ->get();
    }

    /**
     * Get a fleet mission by its ID.
     *
     * @param int $id
     * @return FleetMission
     */
    public function getFleetMissionById(int $id): FleetMission
    {
        return $this->model->find($id);
    }

    /**
     * Process a fleet mission.
     *
     * @param FleetMission $mission
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function updateMission(FleetMission $mission): void
    {
        // Sanity check: only process missions that have arrived.
        if ($mission->time_arrival > Carbon::now()->timestamp) {
            return;
        }

        // TODO: make an abstraction layer where each mission is its own class and process/cancel logic is stored there.
        switch ($mission->mission_type) {
            case 3:
                // Transport
                // Get source planet

                // Get the target planet
                // Load the target planet
                $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
                $target_planet = $planetServiceFactory->make($mission->planet_id_to);

                // Add resources to the target planet
                $target_planet->addResources($this->getResources($mission));

                // Mark the mission as processed
                $mission->processed = 1;
                $mission->save();

                // If this was a return mission, add back the units to the source planet. Then we're done.
                if (!empty($mission->parent_id)) {
                    $target_planet->addUnits($this->getFleetUnits($mission));

                    // Send message to player that the return mission has arrived
                    $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Return of a fleet', 'Your fleet is returning from planet [planet]' . $mission->planet_id_from . '[/planet] to planet [planet]' . $mission->planet_id_to . '[/planet].
                    
                    The fleet doesn\'t deliver goods.', 'return_of_fleet');
                    return;
                }
                else {
                    // Otherwise, launch a return mission.
                    // Send a message to the player that the mission has arrived
                    // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
                    $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Reaching a planet', 'Your fleet from planet [planet]' . $mission->planet_id_from . '[/planet] reaches the planet [planet]' . $mission->planet_id_to . '[/planet] and delivers its goods:
Metal: ' . $mission->metal . ' Crystal: ' . $mission->crystal . ' Deuterium: ' . $mission->deuterium, 'transport_arrived');

                    // Launch a return trip from the target planet to the source planet
                    $this->createReturnFromMission($mission->id);
                }

                break;
        }
    }
}
