<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use OGame\Services\FleetMissionService;

class FleetEventsController extends OGameController
{
    /**
     * Returns fleet movement eventbox JSON.
     *
     * @return JsonResponse
     */
    public function fetchEventBox() : JsonResponse
    {
        /*
         * {"components":[],"hostile":0,"neutral":0,"friendly":1,"eventType":"friendly","eventTime":3470,"eventText":"Transport","newAjaxToken":"6f0e9c23c750fcfc85de4833c79fec39"}
         */
        // Get all the fleet movements for the current user.
        $fleetMissionService = app()->make(FleetMissionService::class);
        $friendlyMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();


        return new JsonResponse([
            'components' => [],
            'hostile' => 0,
            'neutral' => 0,
            'friendly' => $friendlyMissions['mission_count'],
            'eventType' => 'friendly',
            'eventTime' => $friendlyMissions['time_next_mission'],
            'eventText' => $friendlyMissions['type_next_mission'],
            'newAjaxToken' => csrf_token(),
        ]);
    }
}
