<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use OGame\Enums\FleetMissionStatus;
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
        $eventType = FleetMissionStatus::Friendly;
        $currentTime = (int)Date::now()->timestamp;

        // Find the next event time across all missions (matches fetchEventList display logic)
        // We need to iterate through all missions and calculate their display times because:
        // - ACS Defend: time_arrival includes hold time, so we show physical arrival (time_arrival - hold) during travel
        // - Expeditions: time_arrival is physical arrival, so we show hold end (time_arrival + hold) during hold
        // This ensures the eventbox "Next:" matches the first mission shown in the events list
        $nextEventTime = null;
        $nextMission = null;

        foreach ($activeMissionRows as $mission) {
            $actualHoldingTime = $mission->time_holding ?? 0;
            $isInHoldPeriod = false;
            $displayTime = $mission->time_arrival;

            // Determine if this mission is currently in its hold period and calculate the display time
            // Hold time mechanics differ between mission types:
            if ($actualHoldingTime > 0) {
                if ($mission->mission_type === 5) {
                    // ACS Defend: time_arrival = physical_arrival + hold_time
                    // Hold period: from physical arrival until time_arrival
                    $physicalArrivalTime = $mission->time_arrival - $actualHoldingTime;
                    $isInHoldPeriod = ($physicalArrivalTime <= $currentTime && $mission->time_arrival > $currentTime);
                    // During travel: show when fleet physically arrives
                    // During hold: show when hold expires (return departs)
                    $displayTime = $isInHoldPeriod ? $mission->time_arrival : $physicalArrivalTime;
                } else {
                    // Expeditions and other missions: time_arrival = physical_arrival
                    // Hold period: from time_arrival until time_arrival + hold_time
                    $isInHoldPeriod = ($mission->time_arrival <= $currentTime && ($mission->time_arrival + $actualHoldingTime) > $currentTime);
                    // During travel: show when fleet arrives
                    // During hold: show when hold expires (exploration ends)
                    $displayTime = $isInHoldPeriod ? ($mission->time_arrival + $actualHoldingTime) : $mission->time_arrival;
                }
            }

            // Track the soonest upcoming event across all missions
            // We check both missions in hold period (showing hold end) and regular missions (showing arrival)
            if ($isInHoldPeriod) {
                // Mission in hold period - show hold end time
                if ($displayTime > $currentTime && ($nextEventTime === null || $displayTime < $nextEventTime)) {
                    $nextEventTime = $displayTime;
                    $nextMission = $mission;
                }
            } else {
                // Mission traveling or no hold time - show arrival time
                if ($displayTime > $currentTime && ($nextEventTime === null || $displayTime < $nextEventTime)) {
                    $nextEventTime = $displayTime;
                    $nextMission = $mission;
                }
            }
        }

        if ($nextMission !== null) {
            $typeNextMission = $fleetMissionService->missionTypeToLabel($nextMission->mission_type) . ($nextMission->parent_id ? ' (R)' : '');
            $timeNextMission = $nextEventTime - $currentTime;
            $eventType = $this->determineFriendly($nextMission, $player);

            // Loop through all missions to calculate all mission counts.
            foreach ($activeMissionRows as $row) {
                switch ($this->determineFriendly($row, $player)) {
                    case FleetMissionStatus::Friendly:
                        $friendlyMissionCount++;
                        break;
                    case FleetMissionStatus::Neutral:
                        $neutralMissionCount++;
                        break;
                    case FleetMissionStatus::Hostile:
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
            'eventType' => $eventType->value,
            'eventTime' => $timeNextMission,
            'eventText' => $typeNextMission,
            'newAjaxToken' => csrf_token(),
        ]);
    }

    /**
     * Check which fleet events should be removed from the display.
     * Called by frontend countdown timers when they expire (shows "done" status).
     *
     * This endpoint determines which mission rows should be cleared from the fleet widget
     * when their countdown reaches zero. It's called via AJAX 2.5 seconds after a timer expires.
     *
     * Logic:
     * - When a mission with hold time reaches its arrival, the arrival row should disappear
     * - The fetchEventList controller filters out arrival rows for missions in hold period
     * - This endpoint tells the frontend which IDs to remove from the DOM
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse Returns JSON with format: {"rows": [123, 456]} listing mission IDs to remove
     */
    public function checkEvents(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
        // Get the mission IDs that the frontend wants to check (countdown timers that expired)
        $requestedIds = request()->input('ids', []);

        // Get all currently active missions
        $activeMissions = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // Build a list of mission IDs that should still be displayed
        // Missions in hold period are excluded because fetchEventList filters them out
        $displayedMissionIds = [];
        $currentTime = Date::now()->timestamp;

        foreach ($activeMissions as $mission) {
            // Determine if this mission is currently in its hold period
            // During hold period, the arrival row is hidden but synthetic rows (hold end, return) are shown
            $actualHoldingTime = $mission->time_holding ?? 0;
            $isInHoldPeriod = false;

            if ($actualHoldingTime > 0) {
                if ($mission->mission_type === 5) {
                    // ACS Defend: hold period is from physical arrival to time_arrival
                    $physicalArrivalTime = $mission->time_arrival - $actualHoldingTime;
                    $isInHoldPeriod = ($physicalArrivalTime <= $currentTime && $mission->time_arrival > $currentTime);
                } else {
                    // Expeditions: hold period is from time_arrival to time_arrival + hold_time
                    $isInHoldPeriod = ($mission->time_arrival <= $currentTime && ($mission->time_arrival + $actualHoldingTime) > $currentTime);
                }
            }

            // Only include missions that are NOT in hold period (their arrival row should be shown)
            if (!$isInHoldPeriod) {
                $displayedMissionIds[] = $mission->id;
            }
        }

        // Determine which requested IDs should be removed from the display
        // If a mission ID is not in the displayed set, it should be removed from the DOM
        $idsToRemove = [];
        foreach ($requestedIds as $requestedId) {
            if (!in_array($requestedId, $displayedMissionIds)) {
                $idsToRemove[] = $requestedId;
            }
        }

        // Return JSON with Content-Type: text/plain to prevent jQuery from auto-parsing
        // The legacy JavaScript calls $.parseJSON(data) which expects a string, not an object
        return response()->json([
            'rows' => $idsToRemove
        ])->header('Content-Type', 'text/plain');
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
            // For ACS Defend missions (type 5), show physical arrival time (when fleet actually arrives)
            // For Expeditions and other missions, show time_arrival directly
            $eventRowViewModel->mission_time_arrival = ($row->mission_type === 5 && $row->time_holding !== null)
                ? $row->time_arrival - $row->time_holding
                : $row->time_arrival;
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

            $eventRowViewModel->active_recall_time = time() + (time() - $row->time_departure);

            $friendlyStatus = $this->determineFriendly($row, $player);

            $eventRowViewModel->is_recallable = false;
            if ($friendlyStatus === FleetMissionStatus::Friendly) {
                // Missile attacks (mission type 10) cannot be recalled
                if ($row->mission_type !== 10) {
                    $eventRowViewModel->is_recallable = true;
                }
            }

            // Calculate holding time for display and logic
            // - ACS Defend (type 5): time_arrival includes hold time
            // - Expeditions (type 15): time_arrival is physical arrival, add holding time
            // Holding time is always real time (not affected by fleet speed)
            $actualHoldingTime = $row->time_holding ?? 0;
            $displayHoldingTime = $actualHoldingTime;

            if ($row->mission_type === 5) {
                $physicalArrivalTime = $row->time_arrival - $actualHoldingTime;
            } else {
                $physicalArrivalTime = $row->time_arrival;
            }

            // Determine if mission is currently in hold period
            $isInHoldPeriod = false;
            if ($actualHoldingTime > 0) {
                if ($row->mission_type === 5) {
                    // ACS Defend: hold from physical arrival to time_arrival
                    $isInHoldPeriod = ($physicalArrivalTime <= Date::now()->timestamp && $row->time_arrival > Date::now()->timestamp);
                } else {
                    // Expeditions: hold from time_arrival to time_arrival + actualHoldingTime
                    $isInHoldPeriod = ($row->time_arrival <= Date::now()->timestamp && ($row->time_arrival + $actualHoldingTime) > Date::now()->timestamp);
                }
            }

            if ($isInHoldPeriod) {
                // Do not include this parent mission in the list if the "main mission" has already arrived but the time_holding is still active.
                // This applies to expedition and ACS Defend missions:
                // 1. The main mission that shows the fleet arriving.
                // 2. A second row shows when the hold expires/return departs.
            } else {
                $fleet_events[] = $eventRowViewModel;
            }

            // For missions with waiting time, add an additional row showing when the fleet will start its return journey
            // Include both Friendly (own missions) and Neutral (incoming ACS Defend) missions
            if (($friendlyStatus === FleetMissionStatus::Friendly || $friendlyStatus === FleetMissionStatus::Neutral) && $actualHoldingTime > 0 && !$eventRowViewModel->is_return_trip) {
                $waitEndRow = new FleetEventRowViewModel();
                $waitEndRow->is_return_trip = false;
                // ACS Defend missions (type 5) should be recallable during hold time
                // Expeditions (type 15) should NOT be recallable during hold time
                $waitEndRow->is_recallable = ($friendlyStatus === FleetMissionStatus::Friendly && $row->mission_type === 5);
                $waitEndRow->id = $row->id + 888888; // Add large number to avoid conflicts
                $waitEndRow->mission_type = $eventRowViewModel->mission_type;
                $waitEndRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                // For ACS Defend, time_arrival already includes hold time (return departure time)
                // For other missions, add hold time to time_arrival
                $waitEndRow->mission_time_arrival = ($row->mission_type === 5) ? $row->time_arrival : $row->time_arrival + $actualHoldingTime;
                $waitEndRow->time_departure = $row->time_departure;
                $waitEndRow->active_recall_time = $eventRowViewModel->active_recall_time;
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
            // Only show return trips for own missions (Friendly), not for incoming ACS Defend missions (Neutral)
            if ($friendlyStatus === FleetMissionStatus::Friendly && $fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                // For ACS Defend (type 5), time_arrival already includes hold time (return departure time)
                // For other missions, add hold time to calculate return departure time
                if ($row->mission_type === 5) {
                    $returnDepartureTime = $row->time_arrival;
                    // One-way duration = physical travel time (time_arrival - hold - departure)
                    $oneWayDuration = ($row->time_arrival - $row->time_holding) - $row->time_departure;
                } else {
                    $returnDepartureTime = $row->time_arrival + $actualHoldingTime;
                    $oneWayDuration = $row->time_arrival - $row->time_departure;
                }

                $returnTripRow = new FleetEventRowViewModel();
                $returnTripRow->is_return_trip = true;
                $returnTripRow->is_recallable = false;
                $returnTripRow->id = $row->id + 999999; // Add a large number to avoid id conflicts
                $returnTripRow->mission_type = $eventRowViewModel->mission_type;
                $returnTripRow->mission_label = $fleetMissionService->missionTypeToLabel($eventRowViewModel->mission_type);
                $returnTripRow->mission_time_arrival = $returnDepartureTime + $oneWayDuration;
                $returnTripRow->time_departure = $returnDepartureTime;
                $returnTripRow->active_recall_time = 0; // Return trips cannot be recalled
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
     * @return FleetMissionStatus
     */
    private function determineFriendly(FleetMission $mission, PlayerService $player): FleetMissionStatus
    {
        // Determine if the next mission is a friendly, hostile or neutral mission
        if ($mission->user_id != $player->getId()) {
            // Not from the current player, check mission type.
            switch ($mission->mission_type) {
                case 1:
                case 2:
                case 6:
                case 9:
                case 10: // Missile attack
                    // Hostile
                    return FleetMissionStatus::Hostile;
                case 3:
                    // Neutral;
                    return FleetMissionStatus::Neutral;
                case 5: // ACS Defend
                    // Neutral (displays as "friendly" in UI with gold color)
                    return FleetMissionStatus::Neutral;
            }
        }

        // From current player, it is a friendly mission.
        return FleetMissionStatus::Friendly;
    }
}
