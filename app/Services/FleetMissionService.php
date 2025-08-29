<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\Factories\GameMissionFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
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
    public function __construct(PlayerService $player, MessageService $messageService, GameMissionFactory $gameMissionFactory, SettingsService $settingsService)
    {
        $this->player = $player;
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
    public function calculateFleetMissionDuration(PlanetService $fromPlanet, Coordinate $to, UnitCollection $units, float $speed_percent = 10): int
    {
        // Get slowest unit speed.
        $slowest_speed = $units->getSlowestUnitSpeed($fromPlanet->getPlayer());
        $distance = $this->calculateFleetMissionDistance($fromPlanet, $to);
        return (int) max(
            round(
                (35000 / $speed_percent * sqrt($distance * 10 / $slowest_speed) + 10) / $this->settingsService->fleetSpeed()
            ),
            1
        );
    }

    /**
     * Calculates the fleet mission distance between two coordinates in a galaxy map.
     *
     * The method determines the distance based on the differences in galaxy, system,
     * and planet positions, while accounting for features like donut-shaped galaxy
     * and system mechanics, empty systems, and inactive systems.
     *
     * Distance Calculation Rules:
     * 1. If the galaxies differ:
     *    Distance = 20,000 * |galaxy1 - galaxy2|
     *    If "donut galaxy" is enabled, the shortest path between galaxies is used.
     *
     * 2. If the systems differ (within the same galaxy):
     *    Distance = 2,700 + (19 * 5 * max(|system1 - system2| - emptySystems - inactiveSystems, 1))
     *    If "donut system" is enabled, the shortest path between systems is used.
     *
     * 3. If only the planets differ (within the same system):
     *    Distance = 1,000 + (5 * |planet1 - planet2|)
     *
     * 4. If the coordinates are identical (same galaxy, system, and planet):
     *    Distance = 5 (minimum distance for same location).
     *
     * Parameters:
     * - $fromPlanet: The starting planet's coordinates (PlanetService object).
     * - $to: The target coordinates (Coordinate object).
     * - $maxGalaxy: Maximum number of galaxies in the map.
     * - $maxSystem: Maximum number of systems in each galaxy.
     * - $emptySystems: Number of empty systems between start and target.
     * - $inactiveSystems: Number of inactive systems between start and target.
     *
     * Returns:
     * - int: The calculated fleet mission distance.
     */
    public function calculateFleetMissionDistance(PlanetService $fromPlanet, Coordinate $to): int
    {
        $fromCoordinate = $fromPlanet->getPlanetCoordinates();

        $diffGalaxy = abs($fromCoordinate->galaxy - $to->galaxy);
        $diffSystem = abs($fromCoordinate->system - $to->system);
        $diffPlanet = abs($fromCoordinate->position - $to->position);

        // If the galaxies are different
        if ($diffGalaxy != 0) {
            $diff2 = abs($diffGalaxy -  $this->settingsService->numberOfGalaxies());

            if ($diff2 < $diffGalaxy) {
                return $diff2 * 20000;
            } else {
                return $diffGalaxy * 20000;
            }
        }

        // If the system are different
        if ($diffSystem != 0) {
            $diff2 = abs($diffSystem - 499);
            $deltaSystem = 0;

            if ($diff2 < $diffSystem) {
                $deltaSystem = $diff2;
            } else {
                $deltaSystem = $diffSystem;
            }

            //deltaSystem = Math.max(deltaSystem - emptySystems - inactiveSystems, 1);
            $deltaSystem = max($deltaSystem, 1);
            return $deltaSystem * 5 * 19 + 2700;
        }

        // If the planet are different
        if ($diffPlanet != 0) {
            return $diffPlanet * 5 + 1000;
        }

        // If the coordinates are the same
        return 5;
    }

    /**
     * Calculate the consumption of a fleet mission based on the current planet, target coordinates and fleet.
     *
     * @param PlanetService $fromPlanet The planet where the fleet is sent from.
     * @param UnitCollection $ships The units that are sent on the mission.
     * @param Coordinate $targetCoordinate The target coordinate of the mission.
     * @param int $holdingHours The holding time of the fleet. The number represents the amount of hours the fleet will wait at the target planet and/or how long expedition will last.
     * @param float $speedPercent The speed percent of the fleet.
     * @return float|mixed The consumption of the fleet mission.
     */
    public function calculateConsumption(PlanetService $fromPlanet, UnitCollection $ships, Coordinate $targetCoordinate, int $holdingHours, float $speedPercent)
    {
        $consumption = 0;
        $holdingCosts = 0;

        $distance = $this->calculateFleetMissionDistance($fromPlanet, $targetCoordinate);
        $duration = $this->calculateFleetMissionDuration($fromPlanet, $targetCoordinate, $ships, $speedPercent);

        $speedValue = max(0.5, $duration * $this->settingsService->fleetSpeed() - 10);
        foreach ($ships->units as $shipEntry) {
            // Get the ship object and amount
            $ship = $shipEntry->unitObject; // Ship object
            $shipAmount = $shipEntry->amount; // Amount of ships

            // Calculate the speed of the ship
            $ship_speed = $ship->properties->speed->calculate($fromPlanet->getPlayer())->totalValue;

            if (!empty($shipAmount)) {
                $shipSpeedValue = 35000 / $speedValue * sqrt($distance * 10 / $ship_speed);
                $holdingCosts += $ship->properties->fuel->rawValue * $shipAmount * $holdingHours;

                $consumption += max(
                    $ship->properties->fuel->rawValue * $shipAmount * $distance / 35000 *
                    (pow(($shipSpeedValue / 10 + 1), 2)),
                    1
                );
            }
        }

        // Calculate the consumption based on the speed percent
        $consumption = round($consumption);

        // Holding costs
        if ($holdingHours > 0) {
            $consumption += max(floor($holdingCosts / 10), 1);
        }

        return $consumption;
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
     * Get all missions that the current player has sent themselves (excluding incoming missions by other players).
     *
     * @return Collection<int, FleetMission>
     */
    public function getActiveFleetMissionsSentByCurrentPlayer(): Collection
    {
        // Note: this only includes missions that the current player has sent themselves
        // so it does not include any incoming missions by other players (e.g. hostile attacks, espionage, transports etc.)
        $query = $this->model->where('user_id', $this->player->getId())->where('processed', 0);
        return $query->orderBy('time_arrival')->get();
    }

    /**
     * Get all active fleet missions for the current user.
     * This includes both hostile/friendly incoming missions and all player outgoing missions.
     *
     * @return Collection<int, FleetMission>
     */
    public function getActiveFleetMissionsForCurrentPlayer(): Collection
    {
        $query = $this->model;

        // Query returns:
        // 1. All missions sent by current user.
        // - AND -
        // 2. All missions against any of current users planets (by other players).
        $planetIds = [];
        foreach ($this->player->planets->all() as $planet) {
            $planetIds[] = $planet->getPlanetId();
        }

        $missions = $query->where(function ($query) use ($planetIds) {
            $query->where('user_id', $this->player->getId())
                ->orWhereIn('planet_id_to', $planetIds);
        })
            ->where('processed', 0)
            ->get();

        // Order the list taking into account the time_holding. This ensures that the order of missions is correct
        // for the event list that assumes the first mission is the next mission to arrive.
        $missions = $missions->sortBy(function ($mission) {
            // If the mission has not arrived yet, return the time_arrival.
            if ($mission->time_arrival >= Carbon::now()->timestamp) {
                return $mission->time_arrival;
            }

            // If the mission has arrived AND has a waiting time, return the time_arrival + time_holding.
            return $mission->time_arrival + ($mission->time_holding ?? 0);
        });

        return $missions;
    }

    /**
     * Returns whether the current user is under attack.
     *
     * @return bool
     */
    public function currentPlayerUnderAttack(): bool
    {
        $planetIds = [];
        foreach ($this->player->planets->all() as $planet) {
            $planetIds[] = $planet->getPlanetId();
        }

        // Mission types that are considered hostile:
        // 1: Attack
        // 2: ACS Attack
        // 6: Espionage
        // 9: Moon Destruction
        return $this->model->whereIn('planet_id_to', $planetIds)
            ->where('user_id', '!=', $this->player->getId())
            ->whereIn('mission_type', [1, 2, 6, 9])
            ->where('processed', 0)
            ->exists();
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

        foreach (ObjectService::getShipObjects() as $ship) {
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

        foreach (ObjectService::getShipObjects() as $ship) {
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
            $mission->deuterium + ($mission->deuterium_consumption / 2),  //if mission deployment: Add half of the consumed deuterium
            0
        );
    }

    /**
     * Get missions that are either from or to the given planets that have reached the arrival + waiting time
     * but are not processed yet.
     *
     * @param int[] $planetIds
     * @return Collection<int, FleetMission>
     */
    public function getArrivedMissionsByPlanetIds(array $planetIds): Collection
    {
        return $this->model
            ->where(function ($query) use ($planetIds) {
                $query->whereIn('planet_id_from', $planetIds)
                    ->orWhereIn('planet_id_to', $planetIds);
            })
            ->where(function ($query) {
                $query->whereRaw('time_arrival + COALESCE(time_holding, 0) <= ?', [Carbon::now()->timestamp]);
            })
            ->where('processed', 0)
            ->get();
    }

    /**
     * Get missions that are either from or to the given planets that are currently
     * underway and have not been processed yet.
     *
     * @param int[] $planetIds
     * @return Collection<int, FleetMission>
     */
    public function getActiveMissionsByPlanetIds(array $planetIds): Collection
    {
        return $this->model
            ->where(function ($query) use ($planetIds) {
                $query->whereIn('planet_id_from', $planetIds)
                    ->orWhereIn('planet_id_to', $planetIds);
            })
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
     * Get a fleet mission by its parent ID. E.g. this can be used to find the return trip mission launched for a given parent mission ID.
     *
     * @param int $parent_id
     * @param bool $only_active
     * @return FleetMission
     */
    public function getFleetMissionByParentId(int $parent_id, bool $only_active = true): FleetMission
    {
        if ($only_active) {
            return $this->model
                ->where('parent_id', $parent_id)
                ->where('processed', 0)
                ->first();
        } else {
            return $this->model
                ->where('parent_id', $parent_id)
                ->first();
        }
    }

    /**
     * Creates a new fleet mission for the current planet.
     *
     * @param PlanetService $planet The planet where the fleet is sent from.
     * @param Coordinate $targetCoordinate The target coordinate.
     * @param PlanetType $targetType The type of the target.
     * @param int $missionType The type of the mission.
     * @param UnitCollection $units The units that are sent.
     * @param Resources $resources The resources that are sent.
     * @param float $speedPercent The speed percent of the fleet.
     * @param int $holdingHours The holding time of the fleet.
     * @param int $parent_id Optionally the parent mission ID if this is a follow-up mission.
     * @return FleetMission
     * @throws Exception
     */
    public function createNewFromPlanet(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, int $missionType, UnitCollection $units, Resources $resources, float $speedPercent, int $holdingHours = 0, int $parent_id = 0): FleetMission
    {
        $missionObject = $this->gameMissionFactory->getMissionById($missionType, [
            'fleetMissionService' => $this,
            'messageService' => $this->messageService,
        ]);
        return $missionObject->start($planet, $targetCoordinate, $targetType, $units, $resources, $speedPercent, $holdingHours, $parent_id);
    }

    /**
     * Process a fleet mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function updateMission(FleetMission $mission): void
    {
        // Load the mission object again from database to ensure we have the latest data.
        $mission = $this->getFleetMissionById($mission->id, false);

        // Sanity check: only process missions that have arrived AND potential waiting time has passed.
        $arrivalTimeWithWaitingTime = $mission->time_arrival + ($mission->time_holding ?? 0);
        if ($arrivalTimeWithWaitingTime > Carbon::now()->timestamp) {
            return;
        }

        // Sanity check: only process missions that have not been processed yet.
        if ($mission->processed) {
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
        // Sanity check: only allow cancelling missions that have not yet arrived.
        // This applies to especially missions that have a time_holding (e.g. expeditions) where the main mission arrives first
        // but the mission itself is not processed before the time_holding has passed as well. However after the main mission
        // has arrived (even though it's not processed yet), canceling should no longer be allowed.
        if ($mission->time_arrival < Carbon::now()->timestamp) {
            return;
        }

        // Sanity check: only allow canceling missions that have not been processed yet.
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
