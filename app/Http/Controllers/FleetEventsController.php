<?php

namespace OGame\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Services\FleetMissionService;
use OGame\ViewModels\FleetEventRowViewModel;

class FleetEventsController extends OGameController
{
    /**
     * Returns fleet mission eventbox JSON.
     *
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function fetchEventBox() : JsonResponse
    {
        /*
         * {"components":[],"hostile":0,"neutral":0,"friendly":1,"eventType":"friendly","eventTime":3470,"eventText":"Transport","newAjaxToken":"6f0e9c23c750fcfc85de4833c79fec39"}
         */
        // Get all the fleet movements for the current user.
        $fleetMissionService = app()->make(FleetMissionService::class);
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        if ($friendlyMissionRows->isEmpty()) {
            $friendlyMissions = [
                'mission_count' => 0,
                'type_next_mission' => 'None',
                'time_next_mission' => 0,
            ];
        }
        else {
            // TODO: make it a (view)model return type
            // TODO: refactor data retrieval and processing... duplicate with fetchEventList
            $friendlyMissions = [
                'mission_count' => $friendlyMissionRows->count(),
                'type_next_mission' => $fleetMissionService->missionTypeToLabel($friendlyMissionRows->first()->mission_type),
                'time_next_mission' => $friendlyMissionRows->first()->time_arrival - (int)Carbon::now()->timestamp,
            ];
        }

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

    /**
     * Fetch the fleet event list HTML which contains all the fleet mission details.
     *
     * @return View
     * @throws BindingResolutionException
     */
    public function fetchEventList(): View
    {
        // Get all the fleet movements for the current user.
        $fleetMissionService = app()->make(FleetMissionService::class);
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        $fleet_events = [];
        foreach ($friendlyMissionRows as $row) {
            // Planet from service
            $planetServiceFactory = app()->make(PlanetServiceFactory::class);
            $planetFromService = $planetServiceFactory->make($row->planet_id_from);
            $planetToService = $planetServiceFactory->make($row->planet_id_to);

            $eventRowViewModel = new FleetEventRowViewModel();
            $eventRowViewModel->id = $row->id;
            $eventRowViewModel->mission_type = $row->mission_type;
            $eventRowViewModel->mission_time_arrival = $row->time_arrival;
            $eventRowViewModel->is_return_trip = false; // TODO: implement return trips
            $eventRowViewModel->origin_planet_name = $planetFromService->getPlanetName(); // TODO: implement null planet from/to checks
            $eventRowViewModel->origin_planet_coords = $planetFromService->getPlanetCoordinates();
            $eventRowViewModel->destination_planet_name = $planetToService->getPlanetName(); // TODO: implement null planet from/to checks
            $eventRowViewModel->destination_planet_coords = $planetToService->getPlanetCoordinates();
            $eventRowViewModel->fleet_unit_count = $fleetMissionService->getFleetUnitCount($row);
            $eventRowViewModel->fleet_units = $fleetMissionService->getFleetUnits($row);
            $eventRowViewModel->resources = $fleetMissionService->getResources($row);
            $fleet_events[] = $eventRowViewModel;
        }

        return view('ingame.fleetevents.eventlist')->with(
            [
                'fleet_events' => $fleet_events,
            ]
        );
    }
}
