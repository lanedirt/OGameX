<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\ViewModels\FleetEventRowViewModel;

class FleetEventsController extends OGameController
{
    /**
     * Returns fleet mission eventbox JSON.
     *
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse
     */
    public function fetchEventBox(FleetMissionService $fleetMissionService): JsonResponse
    {
        // Get all the fleet movements for the current user.
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        if ($friendlyMissionRows->isEmpty()) {
            $friendlyMissions = [
                'mission_count' => 0,
                'type_next_mission' => 'None',
                'time_next_mission' => 0,
            ];
        } else {
            $firstMission = $friendlyMissionRows->first();

            // Make sure $firstMission is an instance of FleetMission
            if (!$firstMission instanceof FleetMission) {
                throw new \UnexpectedValueException('Expected instance of FleetMission.');
            }

            // TODO: make it a (view)model return type
            // TODO: refactor data retrieval and processing... duplicate with fetchEventList
            $friendlyMissions = [
                'mission_count' => $friendlyMissionRows->count(),
                'type_next_mission' => $fleetMissionService->missionTypeToLabel($firstMission->mission_type) . ($firstMission->parent_id ? ' (R)' : ''),
                'time_next_mission' => $firstMission->time_arrival - (int)Carbon::now()->timestamp,
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
     * @param FleetMissionService $fleetMissionService
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View
     */
    public function fetchEventList(FleetMissionService $fleetMissionService, PlanetServiceFactory $planetServiceFactory): View
    {
        // Get all the fleet movements for the current user.
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        $fleet_events = [];
        foreach ($friendlyMissionRows as $row) {
            // Planet from service
            $eventRowViewModel = new FleetEventRowViewModel();
            $eventRowViewModel->id = $row->id;
            $eventRowViewModel->mission_type = $row->mission_type;
            $eventRowViewModel->mission_label = $fleetMissionService->missionTypeToLabel($row->mission_type);
            $eventRowViewModel->mission_time_arrival = $row->time_arrival;
            $eventRowViewModel->is_return_trip = !empty($row->parent_id); // If mission has a parent, it is a return trip

            $eventRowViewModel->origin_planet_name = '';
            $eventRowViewModel->origin_planet_coords = new Coordinate($row->galaxy_from, $row->system_from, $row->position_from);
            if ($row->planet_id_from !== null) {
                $planetFromService = $planetServiceFactory->make($row->planet_id_from);
                if ($planetFromService !== null) {
                    $eventRowViewModel->origin_planet_name = $planetFromService->getPlanetName();
                    $eventRowViewModel->origin_planet_coords = $planetFromService->getPlanetCoordinates();
                }
            }

            $eventRowViewModel->destination_planet_name = '';
            $eventRowViewModel->destination_planet_coords = new Coordinate($row->galaxy_to, $row->system_to, $row->position_to);
            if ($row->planet_id_to !== null) {
                $planetToService = $planetServiceFactory->make($row->planet_id_to);
                if ($planetToService !== null) {
                    $eventRowViewModel->destination_planet_name = $planetToService->getPlanetName();
                    $eventRowViewModel->destination_planet_coords = $planetToService->getPlanetCoordinates();
                }
            }

            $eventRowViewModel->fleet_unit_count = $fleetMissionService->getFleetUnitCount($row);
            $eventRowViewModel->fleet_units = $fleetMissionService->getFleetUnits($row);
            $eventRowViewModel->resources = $fleetMissionService->getResources($row);
            $fleet_events[] = $eventRowViewModel;

            // Check if this is a transport mission parent, if so, add the return trip to the list.
            // TODO: refactor this logic to abstracted classes per mission type where these eventList rendering are done.
            if ($fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                $returnTripRow = new FleetEventRowViewModel();
                $returnTripRow->is_return_trip = true;
                $returnTripRow->id = $row->parent_id + 999999; // Add a large number to avoid id conflicts
                $returnTripRow->mission_type = $eventRowViewModel->mission_type;
                $returnTripRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                $returnTripRow->mission_time_arrival = $row->time_arrival + ($row->time_arrival - $row->time_departure); // Round trip arrival time is double the time of the first trip
                $returnTripRow->origin_planet_name = $eventRowViewModel->destination_planet_name;
                $returnTripRow->origin_planet_coords = $eventRowViewModel->destination_planet_coords;
                $returnTripRow->destination_planet_name = $eventRowViewModel->origin_planet_name;
                $returnTripRow->destination_planet_coords = $eventRowViewModel->origin_planet_coords;
                $returnTripRow->fleet_unit_count = $eventRowViewModel->fleet_unit_count;
                $returnTripRow->fleet_units = $eventRowViewModel->fleet_units;
                $returnTripRow->resources = new Resources(0, 0, 0, 0);
                $fleet_events[] = $returnTripRow;
            }
        }

        return view('ingame.fleetevents.eventlist')->with(
            [
                'fleet_events' => $fleet_events,
            ]
        );
    }
}
