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
            $missionCount = $activeMissionRows->count();
            $typeNextMission = $fleetMissionService->missionTypeToLabel($firstMission->mission_type) . ($firstMission->parent_id ? ' (R)' : '');
            $timeNextMission = $firstMission->time_arrival - (int)Carbon::now()->timestamp;
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

            $fleet_events[] = $eventRowViewModel;

            // Add return trip row if the mission has a return mission, even though the return mission does not exist yet in the database.
            if ($friendlyStatus === 'friendly' && $fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                $returnTripRow = new FleetEventRowViewModel();
                $returnTripRow->is_return_trip = true;
                $returnTripRow->id = $row->parent_id + 999999; // Add a large number to avoid id conflicts
                $returnTripRow->mission_type = $eventRowViewModel->mission_type;
                $returnTripRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                $returnTripRow->mission_time_arrival = $row->time_arrival + ($row->time_arrival - $row->time_departure); // Round trip arrival time is double the time of the first trip
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
