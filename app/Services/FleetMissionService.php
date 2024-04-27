<?php

namespace OGame\Services;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\DeploymentMission;
use OGame\GameMissions\TransportMission;
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
     * Mission type to class mapping.
     * @var array<int, class-string> $missionTypeToClass
     */
    protected array $missionTypeToClass = [
        3 => TransportMission::class,
        4 => DeploymentMission::class,
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
        // Call static method on mission class.
        $className = $this->missionTypeToClass[$missionType];
        return $className::getName();
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
        if ($missionType == 3) {
            $transportMission = app()->make(TransportMission::class, [
                'fleetMissionService' => $this,
                'messageService' => $this->messageService,
            ]);
            $transportMission->start($planet, $targetPlanet, $missionType, $units, $resources, $parent_id);
        }
        elseif ($missionType == 4) {
            $deployMission = app()->make(DeploymentMission::class, [
                'fleetMissionService' => $this,
                'messageService' => $this->messageService,
            ]);
            $deployMission->start($planet, $targetPlanet, $missionType, $units, $resources, $parent_id);
        }
        return;
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
                $transportMission = app()->make(TransportMission::class, [
                    'fleetMissionService' => $this,
                    'messageService' => $this->messageService,
                ]);
                $transportMission->process($mission);
                break;
            case 4:
                // Deploy
                $deployMission = app()->make(DeploymentMission::class, [
                    'fleetMissionService' => $this,
                    'messageService' => $this->messageService,
                ]);
                $deployMission->process($mission);
                break;
        }
    }
}
