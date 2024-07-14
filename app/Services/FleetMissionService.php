<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Factories\GameMissionFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;

/**
 * Class FleetMissionService.
 *
 * FleetMissionService object.
 *
 * @package OGame\Services
 */
class FleetMissionService
{
    /**
     * Player service
     *
     * @var PlayerService
     */
    private PlayerService $player;

    /**
     * @var ObjectService $objects
     */
    private ObjectService $objects;

    /**
     * @var MessageService $messageService
     */
    private MessageService $messageService;

    /**
     * @var GameMissionFactory $gameMissionFactory
     */
    private GameMissionFactory $gameMissionFactory;

    /**
     * The queue model where this class should get its data from.
     *
     * @var FleetMission
     */
    private FleetMission $model;

    private SettingsService $settingsService;

    /**
     * FleetMissionService constructor.
     */
    public function __construct(PlayerService $player, ObjectService $objects, MessageService $messageService, GameMissionFactory $gameMissionFactory, SettingsService $settingsService)
    {
        $this->player = $player;
        $this->objects = $objects;
        $this->messageService = $messageService;
        $this->gameMissionFactory = $gameMissionFactory;
        $this->settingsService = $settingsService;

        $this->model = new FleetMission();
    }

    /**
     * Calculate the duration of a fleet mission based on the current planet, target coordinates and fleet.
     *
     * @param PlanetService $fromPlanet
     * @param Coordinate $to
     * @param UnitCollection $units
     * @return int
     */
    public function calculateFleetMissionDuration(PlanetService $fromPlanet, Coordinate $to, UnitCollection $units): int
    {
        // Get slowest unit speed.
        $slowest_speed = $units->getSlowestUnitSpeed($fromPlanet);

        // Calculate distance between current planet and target planet.
        // ----------------------------------------
        // Between galaxies:
        // 20.000 x (galaxy2 - galaxy1)
        // Between systems:
        // 2.700 + (95 x (system2 - system1))
        // Between planets:
        // 1.000 + (5 x (position2 - position1))
        // Between moon or debris field and planet:
        // 5
        // ----------------------------------------
        $fromCoordinate = $fromPlanet->getPlanetCoordinates();
        $distance = 0;
        if ($fromCoordinate->galaxy !== $to->galaxy) {
            $distance = 20000 * abs($to->galaxy - $fromCoordinate->galaxy);
        }
        if ($fromCoordinate->system !== $to->system) {
            $distance = 2700 + (95 * abs($to->system - $fromCoordinate->system));
        }
        if ($fromCoordinate->position !== $to->position) {
            $distance = 1000 + (5 * abs($to->position - $fromCoordinate->position));
        }

        // If the target is a moon or debris field on the same coordinate, the distance is always 5.
        if ($distance === 0) {
            $distance = 5;
        }

        // The duration is calculated as follows:
        // duration = (10 + (3500 / speed modifier as decimal) * ((distance * 10) / lowest fleet speed) ^ 0.5) / universe fleet speed
        return (int)((10 + (3500 / 1) * (($distance * 10) / $slowest_speed) ** 0.5) / $this->settingsService->fleetSpeed());
    }

    /**
     * Convert a mission type to a human readable label.
     *
     * @param int $missionType
     * @return string
     */
    public function missionTypeToLabel(int $missionType): string
    {
        return GameMissionFactory::getMissionById($missionType, [])->getName();
    }

    /**
     * Returns whether a mission type has a return mission by default.
     *
     * @param int $missionType
     * @return bool
     */
    public function missionHasReturnMission(int $missionType): bool
    {
        return GameMissionFactory::getMissionById($missionType, [])->hasReturnMission();
    }

    /**
     * Get all active fleet missions for the current user.
     *
     * @return Collection<FleetMission>
     */
    public function getActiveFleetMissionsForCurrentPlayer(): Collection
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
            ->get();
    }

    /**
     * Get a fleet mission by its ID.
     *
     * @param int $id
     * @param bool $only_active
     * @return FleetMission
     */
    public function getFleetMissionById(int $id, bool $only_active = true): FleetMission
    {
        if ($only_active) {
            return $this->model
                ->where('id', $id)
                ->where('processed', 0)
                ->first();
        } else {
            return $this->model
                ->where('id', $id)
                ->first();
        }
    }

    /**
     * Creates a new fleet mission for the current planet.
     *
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param int $missionType
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $parent_id
     * @return void
     * @throws Exception
     */
    public function createNewFromPlanet(PlanetService $planet, Coordinate $targetCoordinate, int $missionType, UnitCollection $units, Resources $resources, int $parent_id = 0): void
    {
        $missionObject = $this->gameMissionFactory->getMissionById($missionType, [
            'fleetMissionService' => $this,
            'messageService' => $this->messageService,
        ]);
        $missionObject->start($planet, $targetCoordinate, $units, $resources, $parent_id);
    }

    /**
     * Process a fleet mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function updateMission(FleetMission $mission): void
    {
        // Sanity check: only process missions that have arrived.
        if ($mission->time_arrival > Carbon::now()->timestamp) {
            return;
        }

        $missionObject = $this->gameMissionFactory->getMissionById($mission->mission_type, [
            'fleetMissionService' => $this,
            'messageService' => $this->messageService,
        ]);
        $missionObject->process($mission);
    }

    /**
     * Cancel a fleet mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function cancelMission(FleetMission $mission): void
    {
        // Sanity check: only process missions that have not been processed yet.
        if ($mission->processed) {
            return;
        }

        $missionObject = $this->gameMissionFactory->getMissionById($mission->mission_type, [
            'fleetMissionService' => $this,
            'messageService' => $this->messageService,
        ]);
        $missionObject->cancel($mission);
    }
}
