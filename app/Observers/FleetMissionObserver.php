<?php

namespace OGame\Observers;

use OGame\Models\FleetMission;
use OGame\Services\FleetMissionService;

class FleetMissionObserver
{
    public function created(FleetMission $mission): void
    {
        resolve(FleetMissionService::class)->syncMissionArrivalJobs($mission);
    }

    public function updated(FleetMission $mission): void
    {
        if (!$mission->wasChanged([
            'time_arrival',
            'time_arrival_ms',
            'time_holding',
            'processed',
            'processed_hold',
            'canceled',
            'planet_id_to',
            'galaxy_to',
            'system_to',
            'position_to',
            'type_to',
        ])) {
            return;
        }

        resolve(FleetMissionService::class)->syncMissionArrivalJobs($mission);
    }

    public function deleted(FleetMission $mission): void
    {
        resolve(FleetMissionService::class)->cancelMissionArrivalJobs($mission, persist: false);
    }
}
