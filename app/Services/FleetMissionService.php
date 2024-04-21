<?php

namespace OGame\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Models\UnitQueue;
use OGame\ViewModels\Queue\UnitQueueListViewModel;
use OGame\ViewModels\Queue\UnitQueueViewModel;
use OGame\ViewModels\QueueListViewModel;
use OGame\ViewModels\QueueViewModel;

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
     * Planet service
     *
     * @var PlanetService
     */
    protected PlanetService $planet;

    /**
     * The queue model where this class should get its data from.
     *
     * @var FleetMission
     */
    protected FleetMission $model;

    /**
     * BuildingQueue constructor.
     */
    public function __construct(PlanetService $planet)
    {
        $this->planet = $planet;

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
        // @TODO: add checks that current logged in user is owner of planet
        // and is able to add this object to the building queue.

        // Time this fleet mission will depart (now)
        $time_start = (int)Carbon::now()->timestamp;

        // Time fleet mission will arrive
        $time_end = $time_start + $this->calculateFleetMissionDuration();

        $mission = new $this->model;
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
}
