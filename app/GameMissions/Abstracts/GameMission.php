<?php

namespace OGame\GameMissions\Abstracts;

use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Services\PlanetService;

abstract class GameMission
{
    /**
     * @var string The name of the mission shown in the GUI.
     */
    protected static string $name;

    /**
     * @var bool Whether this mission has a return mission by default.
     */
    protected static bool $hasReturnMission;

    protected FleetMissionService $fleetMissionService;

    protected MessageService $messageService;

    public function __construct(FleetMissionService $fleetMissionService,  MessageService $messageService)
    {
        $this->fleetMissionService = $fleetMissionService;
        $this->messageService = $messageService;
    }

    public static function getName(): string
    {
        return static::$name;
    }

    public static function hasReturnMission(): bool
    {
        return static::$hasReturnMission;
    }

    /**
     * Start a new mission.
     *
     * @param PlanetService $planet
     * @param PlanetService $targetPlanet
     * @param int $missionType
     * @param UnitCollection $units
     * @param Resources $resources
     * @param int $parent_id
     * @return void
     */
    public abstract function start(PlanetService $planet, PlanetService $targetPlanet, int $missionType, UnitCollection $units, Resources $resources, int $parent_id = 0): void;

    /**
     * Cancel an already started mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public abstract function cancel(FleetMission $mission): void;


    /**
     * Process the mission.
     *
     * @param FleetMission $mission
     * @return void
     */
    public function process(FleetMission $mission): void {
        if (!empty($mission->parent_id)) {
            // This is a return mission as it has a parent mission.
            $this->processReturn($mission);
            return;
        }
        else {
            // This is an arrival mission as it has no parent mission.
            // Process arrival.
            $this->processArrival($mission);
        }
    }

    /**
     * Process the mission arrival (first stage, required).
     *
     * @param FleetMission $mission
     * @return void
     */
    protected abstract function processArrival(FleetMission $mission): void;

    /**
     * Process the mission return (second stage, optional).
     *
     * @param FleetMission $mission
     * @return void
     */
    protected abstract function processReturn(FleetMission $mission): void;
}