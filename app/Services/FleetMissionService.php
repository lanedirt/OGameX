<?php

namespace OGame\Services;

use Exception;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
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
     * The queue model where this class should get its data from.
     *
     * @var FleetMission
     */
    protected FleetMission $model;

    /**
     * FleetMissionService constructor.
     */
    public function __construct(PlayerService $player)
    {
        $this->player = $player;

        $model_name = 'OGame\Models\FleetMission';
        $this->model = new $model_name();
    }

    /**
     * Creates a new fleet mission for the current planet.
     *
     * @param PlanetService $planet
     * @param int $missionType
     * @param UnitCollection $units
     * @param Resources $resources
     * @return void
     * @throws Exception
     */
    public function create(PlanetService $planet, int $missionType, UnitCollection $units, Resources $resources): void
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
        $mission->galaxy_to = 1;
        $mission->system_to = 1;
        $mission->position_to = 1;

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
        return 600;
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
     * @return array<string,string|int>
     */
    public function getActiveFleetMissionsForCurrentPlayer() : array
    {
        $missions = $this->model->where([
                ['user_id', $this->player->getId()],
                ['processed', 0],
            ])
            ->orderBy('time_arrival', 'asc')
            ->get();

        if ($missions->isEmpty()) {
            return [
                'mission_count' => 0,
                'type_next_mission' => 'None',
                'time_next_mission' => 0,
            ];
        }

        // TODO: make it a (view)model return type
        return [
            'mission_count' => $missions->count(),
            'type_next_mission' => $this->missionTypeToLabel($missions->first()->mission_type),
            'time_next_mission' => (int)Carbon::now()->timestamp - $missions->first()->time_arrival ?? 0,
        ];
    }
}
