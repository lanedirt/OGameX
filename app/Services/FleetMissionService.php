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
     * @return void
     * @throws Exception
     */
    public function create(PlanetService $planet, PlanetService $targetPlanet, int $missionType, UnitCollection $units, Resources $resources): void
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
        return 15;
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
     * Process a fleet mission.
     *
     * @param FleetMission $mission
     * @return void
     * @throws BindingResolutionException
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

                // Send a message to the player that the mission has arrived
                // TODO: make message content translatable by using tokens instead of directly inserting dynamic content.
                $this->messageService->sendMessageToPlayer($target_planet->getPlayer(), 'Reaching a planet', 'Your fleet from planet [planet]' . $mission->planet_id_from . '[/planet] reaches the planet [planet]' . $mission->planet_id_to . '[/planet] and delivers its goods:
Metal: ' . $mission->metal . ' Crystal: ' . $mission->crystal . ' Deuterium: ' . $mission->deuterium, 'transport_arrived');
                break;
        }
    }
}
