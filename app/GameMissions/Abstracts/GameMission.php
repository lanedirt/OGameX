<?php

namespace OGame\GameMissions\Abstracts;

use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\MessageService;
use OGame\Services\PlanetService;

abstract class GameMission
{
    protected string $name;

    protected FleetMissionService $fleetMissionService;

    protected MessageService $messageService;

    public function __construct(FleetMissionService $fleetMissionService,  MessageService $messageService)
    {
        $this->fleetMissionService = $fleetMissionService;
        $this->messageService = $messageService;
    }

    /**
     * Start a new mission.
     *
     * @return void
     */
    public abstract function start(PlanetService $planet, PlanetService $targetPlanet, int $missionType, UnitCollection $units, Resources $resources, int $parent_id = 0): void;

    /**
     * Cancel an already started mission.
     *
     * @return void
     */
    public abstract function cancel(): void;


    /**
     * Process the mission.
     *
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
     * @return void
     */
    protected abstract function processArrival(FleetMission $mission): void;

    /**
     * Process the mission return (second stage, optional).
     *
     * @return void
     */
    protected abstract function processReturn(FleetMission $mission): void;
}