<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\PlayerService;
use OGame\ViewModels\FleetEventRowViewModel;

class FleetEventsController extends OGameController
{
    /**
     * Returns fleet mission eventbox JSON.
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse
     */
    public function fetchEventBox(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
        // Get all the fleet movements for the current user.
        $activeMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        $friendlyMissionCount = 0;
        $neutralMissionCount = 0;
        $hostileMissionCount = 0;
        $typeNextMission = '';
        $timeNextMission = 0;
        $eventType = '';

        if ($activeMissionRows->isNotEmpty()) {
            $firstMission = $activeMissionRows->first();
            $typeNextMission = $fleetMissionService->missionTypeToLabel($firstMission->mission_type) . ($firstMission->parent_id ? ' (R)' : '');

            // If the mission has not arrived yet, return the time_arrival.
            if ($firstMission->time_arrival >= Carbon::now()->timestamp) {
                $timeNextMission = $firstMission->time_arrival - (int)Carbon::now()->timestamp;
            } else {
                // If the mission has arrived AND has a waiting time, return the time_arrival + time_holding.
                $timeNextMission = $firstMission->time_arrival + ($firstMission->time_holding ?? 0) - (int)Carbon::now()->timestamp;
            }

            $eventType = $this->determineFriendly($firstMission, $player);

            // Loop through all missions to calculate all mission counts.
            foreach ($activeMissionRows as $row) {
                switch ($this->determineFriendly($row, $player)) {
                    case 'friendly':
                        $friendlyMissionCount++;
                        break;
                    case 'neutral':
                        $neutralMissionCount++;
                        break;
                    case 'hostile':
                        $hostileMissionCount++;
                        break;
                }
            }
        }

        return new JsonResponse([
            'components' => [],
            'hostile' => $hostileMissionCount,
            'neutral' => $neutralMissionCount,
            'friendly' => $friendlyMissionCount,
            'eventType' => $eventType,
            'eventTime' => $timeNextMission,
            'eventText' => $typeNextMission,
            'newAjaxToken' => csrf_token(),
        ]);
    }

    /**
     * Fetch the fleet event list HTML which contains all the fleet mission details.
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View
     */
    public function fetchEventList(PlayerService $player, FleetMissionService $fleetMissionService, PlanetServiceFactory $planetServiceFactory): View
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
            $eventRowViewModel->origin_planet_type = PlanetType::from($row->type_from);
            if ($row->planet_id_from !== null) {
                $planetFromService = $planetServiceFactory->make($row->planet_id_from);
                if ($planetFromService !== null) {
                    $eventRowViewModel->origin_planet_name = $planetFromService->getPlanetName();
                    $eventRowViewModel->origin_planet_coords = $planetFromService->getPlanetCoordinates();
                }
            }

            $eventRowViewModel->destination_planet_name = '';
            $eventRowViewModel->destination_planet_coords = new Coordinate($row->galaxy_to, $row->system_to, $row->position_to);
            $eventRowViewModel->destination_planet_type = PlanetType::from($row->type_to);

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

            $friendlyStatus = $this->determineFriendly($row, $player);

            $eventRowViewModel->is_recallable = false;
            if ($friendlyStatus === 'friendly') {
                $eventRowViewModel->is_recallable = true;
            }

            if ($row->time_holding > 0 && $row->time_arrival <= Carbon::now()->timestamp && $row->time_arrival + $row->time_holding > Carbon::now()->timestamp) {
                // Do not include this parent mission in the list if the "main mission" has already arrived but the time_holding is still active.
                // This applies to e.g. expedition mission that shows two rows:
                // 1. The main mission that shows the fleet arriving.
                // 2. A second main mission that shows the fleet arriving again after the time_holding --> this is the point at which the mission is actually processed.
            } else {
                $fleet_events[] = $eventRowViewModel;
            }

            // For missions with waiting time, add an additional row showing when the fleet will start its return journey
            if ($friendlyStatus === 'friendly' && $row->time_holding > 0 && !$eventRowViewModel->is_return_trip) {
                $waitEndRow = new FleetEventRowViewModel();
                $waitEndRow->is_return_trip = false;
                $waitEndRow->is_recallable = false;
                $waitEndRow->id = $row->id + 888888; // Add large number to avoid conflicts
                $waitEndRow->mission_type = $eventRowViewModel->mission_type;
                $waitEndRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                $waitEndRow->mission_time_arrival = $row->time_arrival + $row->time_holding;
                $waitEndRow->origin_planet_name = $eventRowViewModel->origin_planet_name;
                $waitEndRow->origin_planet_coords = $eventRowViewModel->origin_planet_coords;
                $waitEndRow->origin_planet_type = $eventRowViewModel->origin_planet_type;
                $waitEndRow->destination_planet_name = $eventRowViewModel->destination_planet_name;
                $waitEndRow->destination_planet_coords = $eventRowViewModel->destination_planet_coords;
                $waitEndRow->destination_planet_type = $eventRowViewModel->destination_planet_type;
                $waitEndRow->fleet_unit_count = $eventRowViewModel->fleet_unit_count;
                $waitEndRow->fleet_units = $eventRowViewModel->fleet_units;
                $waitEndRow->resources = $eventRowViewModel->resources;
                $fleet_events[] = $waitEndRow;
            }

            // Add return trip row if the mission has a return mission, even though the return mission does not exist yet in the database.
            if ($friendlyStatus === 'friendly' && $fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                $returnTripRow = new FleetEventRowViewModel();
                $returnTripRow->is_return_trip = true;
                $returnTripRow->is_recallable = false;
                $returnTripRow->id = $row->id + 999999; // Add a large number to avoid id conflicts
                $returnTripRow->mission_type = $eventRowViewModel->mission_type;
                $returnTripRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                $returnTripRow->mission_time_arrival = $row->time_arrival + ($row->time_arrival - $row->time_departure) + ($row->time_holding ?? 0);
                $returnTripRow->origin_planet_name = $eventRowViewModel->destination_planet_name;
                $returnTripRow->origin_planet_coords = $eventRowViewModel->destination_planet_coords;
                $returnTripRow->origin_planet_type = $eventRowViewModel->destination_planet_type;
                $returnTripRow->destination_planet_name = $eventRowViewModel->origin_planet_name;
                $returnTripRow->destination_planet_coords = $eventRowViewModel->origin_planet_coords;
                $returnTripRow->destination_planet_type = $eventRowViewModel->origin_planet_type;
                $returnTripRow->fleet_unit_count = $eventRowViewModel->fleet_unit_count;
                $returnTripRow->fleet_units = $eventRowViewModel->fleet_units;
                $returnTripRow->resources = new Resources(0, 0, 0, 0);
                $fleet_events[] = $returnTripRow;
            }
        }

        // Order the fleet events by mission time arrival.
        usort($fleet_events, function ($a, $b) {
            return $a->mission_time_arrival - $b->mission_time_arrival;
        });

        return view('ingame.fleetevents.eventlist')->with(
            [
                'fleet_events' => $fleet_events,
            ]
        );
    }

    /**
     * Returns whether the fleet mission is friendly, neutral or hostile.
     *
     * @param FleetMission $mission
     * @param PlayerService $player
     *
     * @return string ('friendly', 'neutral' or 'hostile')
     */
    private function determineFriendly(FleetMission $mission, PlayerService $player): string
    {
        // Determine if the next mission is a friendly, hostile or neutral mission
        if ($mission->user_id != $player->getId()) {
            // Not from the current player, check mission type.
            switch ($mission->mission_type) {
                case 1:
                case 2:
                case 6:
                case 9:
                    // Hostile
                    return 'hostile';
                case 3:
                    // Neutral;
                    return 'neutral';
            }
        }

        // From current player, it is a friendly mission.
        return 'friendly';
    }
}
