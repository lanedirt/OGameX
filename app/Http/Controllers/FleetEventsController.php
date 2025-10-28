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
    public function fetchEventBox(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
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

            if ($firstMission->time_arrival >= Carbon::now()->timestamp) {
                $timeNextMission = $firstMission->time_arrival - (int)Carbon::now()->timestamp;
            } else {
                $timeNextMission = $firstMission->time_arrival + ($firstMission->time_holding ?? 0) - (int)Carbon::now()->timestamp;
            }

            $eventType = $this->determineFriendly($firstMission, $player);

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

    public function fetchEventList(PlayerService $player, FleetMissionService $fleetMissionService, PlanetServiceFactory $planetServiceFactory): View
    {
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        $fleet_events = [];
        foreach ($friendlyMissionRows as $row) {
            $eventRowViewModel = new FleetEventRowViewModel();
            $eventRowViewModel->id = $row->id;
            $eventRowViewModel->mission_type = $row->mission_type;
            $eventRowViewModel->mission_label = $fleetMissionService->missionTypeToLabel($row->mission_type);
            $eventRowViewModel->mission_time_arrival = $row->time_arrival;
            $eventRowViewModel->is_return_trip = !empty($row->parent_id);

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

            if ($eventRowViewModel->destination_planet_type === PlanetType::DeepSpace) {
                $eventRowViewModel->destination_planet_name = __('Deep space');
            } elseif ($row->planet_id_to !== null) {
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
                // Do not include mission rows still in hold time
            } else {
                $fleet_events[] = $eventRowViewModel;
            }

            if ($friendlyStatus === 'friendly' && $row->time_holding > 0 && !$eventRowViewModel->is_return_trip) {
                $waitEndRow = new FleetEventRowViewModel();
                $waitEndRow->is_return_trip = false;
                $waitEndRow->is_recallable = false;
                $waitEndRow->id = $row->id + 888888;
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

            if ($friendlyStatus === 'friendly' && $fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                $returnTripRow = new FleetEventRowViewModel();
                $returnTripRow->is_return_trip = true;
                $returnTripRow->is_recallable = false;
                $returnTripRow->id = $row->id + 999999;
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

        usort($fleet_events, function ($a, $b) {
            return $a->mission_time_arrival - $b->mission_time_arrival;
        });

        return view('ingame.fleetevents.eventlist', [
            'fleet_events' => $fleet_events,
        ]);
    }

    private function determineFriendly(FleetMission $mission, PlayerService $player): string
    {
        if ($mission->user_id != $player->getId()) {
            switch ($mission->mission_type) {
                case 1:
                case 2:
                case 6:
                case 9:
                    return 'hostile';
                case 3:
                    return 'neutral';
            }
        }
        return 'friendly';
    }
}
