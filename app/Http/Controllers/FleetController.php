<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\GameMissionFactory;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\CoordinateDistanceCalculator;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use OGame\ViewModels\FleetEventRowViewModel;
use OGame\ViewModels\UnitViewModel;

class FleetController extends OGameController
{
    /**
     * Shows the fleet index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @param SettingsService $settings
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player, SettingsService $settings): View
    {
        // Define ship ids to include in the fleet screen.
        // 0 = military ships
        // 1 = civil ships
        $screen_objects = [
            ['light_fighter', 'heavy_fighter', 'cruiser', 'battle_ship', 'battlecruiser', 'bomber', 'destroyer', 'deathstar'],
            ['small_cargo', 'large_cargo', 'colony_ship', 'recycler', 'espionage_probe'],
        ];

        $planet = $player->planets->current();

        $units = [];
        $count = 0;

        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = ObjectService::getUnitObjectByMachineName($object_machine_name);

                // Get current level of building
                $amount = $planet->getObjectAmount($object_machine_name);

                $view_model = new UnitViewModel();
                $view_model->object = $object;
                $view_model->count = $count;
                $view_model->amount = $amount;

                $units[$key_row][$object->id] = $view_model;
            }
        }

        return view('ingame.fleet.index')->with([
            'player' => $player,
            'planet' => $planet,
            'units' => $units,
            'objects' => ObjectService::getShipObjects(),
            'shipAmount' => $planet->getFlightShipAmount(),
            'galaxy' => $request->get('galaxy'),
            'system' => $request->get('system'),
            'position' => $request->get('position'),
            'type' => $request->get('type'),
            'mission' => $request->get('mission'),
            'settings' => $settings,
            'fleetSlotsInUse' => $player->getFleetSlotsInUse(),
            'fleetSlotsMax' => $player->getFleetSlotsMax(),
            'expeditionSlotsInUse' => $player->getExpeditionSlotsInUse(),
            'expeditionSlotsMax' => $player->getExpeditionSlotsMax(),
        ]);
    }

    /**
     * Shows the fleet movement page
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View|RedirectResponse
     */
    public function movement(PlayerService $player, FleetMissionService $fleetMissionService, PlanetServiceFactory $planetServiceFactory): View|RedirectResponse
    {
        // Get all the fleet movements for the current user.
        $friendlyMissionRows = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer();

        // If no fleet movements, redirect to fleet dispatch page.
        if ($friendlyMissionRows->isEmpty()) {
            return redirect()->route('fleet.index');
        }

        $fleet_events = [];
        foreach ($friendlyMissionRows as $row) {
            $eventRowViewModel = new FleetEventRowViewModel();
            $eventRowViewModel->id = $row->id;
            $eventRowViewModel->mission_type = $row->mission_type;
            $eventRowViewModel->mission_label = $fleetMissionService->missionTypeToLabel($row->mission_type);
            $eventRowViewModel->mission_time_arrival = $row->time_arrival;
            $eventRowViewModel->time_departure = $row->time_departure;
            $eventRowViewModel->is_return_trip = !empty($row->parent_id);

            // For return trips, swap origin and destination for display purposes
            // (fleet is returning from destination back to origin)
            if ($eventRowViewModel->is_return_trip) {
                // Origin becomes destination (where fleet is coming from)
                $eventRowViewModel->origin_planet_name = '';
                $eventRowViewModel->origin_planet_coords = new Coordinate($row->galaxy_to, $row->system_to, $row->position_to);
                $eventRowViewModel->origin_planet_type = PlanetType::from($row->type_to);
                if ($row->planet_id_to !== null) {
                    $planetToService = $planetServiceFactory->make($row->planet_id_to);
                    if ($planetToService !== null) {
                        $eventRowViewModel->origin_planet_name = $planetToService->getPlanetName();
                        $eventRowViewModel->origin_planet_coords = $planetToService->getPlanetCoordinates();
                        $eventRowViewModel->origin_planet_image_type = $planetToService->getPlanetImageType();
                        $eventRowViewModel->origin_planet_biome_type = $planetToService->getPlanetBiomeType();
                    }
                }

                // Destination becomes origin (where fleet is going back to)
                $eventRowViewModel->destination_planet_name = '';
                $eventRowViewModel->destination_planet_coords = new Coordinate($row->galaxy_from, $row->system_from, $row->position_from);
                $eventRowViewModel->destination_planet_type = PlanetType::from($row->type_from);
                if ($row->planet_id_from !== null) {
                    $planetFromService = $planetServiceFactory->make($row->planet_id_from);
                    if ($planetFromService !== null) {
                        $eventRowViewModel->destination_planet_name = $planetFromService->getPlanetName();
                        $eventRowViewModel->destination_planet_coords = $planetFromService->getPlanetCoordinates();
                        $eventRowViewModel->destination_planet_image_type = $planetFromService->getPlanetImageType();
                        $eventRowViewModel->destination_planet_biome_type = $planetFromService->getPlanetBiomeType();
                    }
                }
            } else {
                // Normal trip - origin is where fleet started, destination is where it's going
                $eventRowViewModel->origin_planet_name = '';
                $eventRowViewModel->origin_planet_coords = new Coordinate($row->galaxy_from, $row->system_from, $row->position_from);
                $eventRowViewModel->origin_planet_type = PlanetType::from($row->type_from);
                if ($row->planet_id_from !== null) {
                    $planetFromService = $planetServiceFactory->make($row->planet_id_from);
                    if ($planetFromService !== null) {
                        $eventRowViewModel->origin_planet_name = $planetFromService->getPlanetName();
                        $eventRowViewModel->origin_planet_coords = $planetFromService->getPlanetCoordinates();
                        $eventRowViewModel->origin_planet_image_type = $planetFromService->getPlanetImageType();
                        $eventRowViewModel->origin_planet_biome_type = $planetFromService->getPlanetBiomeType();
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
                        $eventRowViewModel->destination_planet_image_type = $planetToService->getPlanetImageType();
                        $eventRowViewModel->destination_planet_biome_type = $planetToService->getPlanetBiomeType();
                    }
                }
            }

            $eventRowViewModel->fleet_unit_count = $fleetMissionService->getFleetUnitCount($row);
            $eventRowViewModel->fleet_units = $fleetMissionService->getFleetUnits($row);
            $eventRowViewModel->resources = $fleetMissionService->getResources($row);

            $eventRowViewModel->active_recall_time = time() + (time() - $row->time_departure);

            // Determine friendly status based on mission type for styling
            $mission = GameMissionFactory::getMissionById($row->mission_type, []);
            $eventRowViewModel->friendly_status = $mission::getFriendlyStatus()->value;
            $eventRowViewModel->is_recallable = true;

            // Add return trip info to the same row (not as separate row) if the mission has a return mission
            if ($fleetMissionService->missionHasReturnMission($eventRowViewModel->mission_type) && !$eventRowViewModel->is_return_trip) {
                $eventRowViewModel->has_return_trip = true;
                $eventRowViewModel->return_time_arrival = $row->time_arrival + ($row->time_arrival - $row->time_departure) + ($row->time_holding ?? 0);
            }

            // Calculate timer values
            $currentTime = time();
            $eventRowViewModel->is_at_destination = $eventRowViewModel->has_return_trip && $eventRowViewModel->mission_time_arrival <= $currentTime;

            // For expeditions at destination, use expedition end time for the main timer
            if ($eventRowViewModel->is_at_destination && $eventRowViewModel->return_time_arrival) {
                $travelTime = $eventRowViewModel->mission_time_arrival - $eventRowViewModel->time_departure;
                $expeditionEndTime = $eventRowViewModel->return_time_arrival - $travelTime;
                $eventRowViewModel->timer_time = $expeditionEndTime;
            } else {
                $eventRowViewModel->timer_time = $eventRowViewModel->mission_time_arrival;
            }

            $eventRowViewModel->remaining_time = max(0, $eventRowViewModel->timer_time - $currentTime);
            $eventRowViewModel->duration = max(1, $eventRowViewModel->mission_time_arrival - $eventRowViewModel->time_departure);

            // Calculate return trip remaining time
            if ($eventRowViewModel->has_return_trip && $eventRowViewModel->return_time_arrival) {
                $eventRowViewModel->return_remaining_time = max(0, $eventRowViewModel->return_time_arrival - $currentTime);
            }

            $fleet_events[] = $eventRowViewModel;
        }

        // Order the fleet events by mission time arrival.
        usort($fleet_events, function ($a, $b) {
            return $a->mission_time_arrival - $b->mission_time_arrival;
        });

        return view('ingame.fleet.movement')->with([
            'player' => $player,
            'planet' => $player->planets->current(),
            'fleet_events' => $fleet_events,
            'fleetSlotsInUse' => $player->getFleetSlotsInUse(),
            'fleetSlotsMax' => $player->getFleetSlotsMax(),
            'expeditionSlotsInUse' => $player->getExpeditionSlotsInUse(),
            'expeditionSlotsMax' => $player->getExpeditionSlotsMax(),
        ]);
    }

    /**
     * Checks the target planet for possible missions.
     *
     * @param PlayerService $currentPlayer
     * @param PlanetServiceFactory $planetServiceFactory
     * @param CoordinateDistanceCalculator $coordinateDistanceCalculator
     * @return JsonResponse
     * @throws Exception
     */
    public function dispatchCheckTarget(PlayerService $currentPlayer, PlanetServiceFactory $planetServiceFactory, CoordinateDistanceCalculator $coordinateDistanceCalculator): JsonResponse
    {
        $currentPlanet = $currentPlayer->planets->current();

        // Return ships data for this planet taking into account the current planet's properties and research levels.
        $shipsData = [];
        foreach (ObjectService::getShipObjects() as $shipObject) {
            $shipsData[$shipObject->id] = [
                'id' => $shipObject->id,
                'name' => $shipObject->title,
                'baseFuelCapacity' => $shipObject->properties->fuel_capacity->calculate($currentPlayer)->totalValue,
                'baseCargoCapacity' => $shipObject->properties->capacity->calculate($currentPlayer)->totalValue,
                'fuelConsumption' => $shipObject->properties->fuel->calculate($currentPlayer)->totalValue,
                'speed' => $shipObject->properties->speed->calculate($currentPlayer)->totalValue
            ];
        }

        // Get target coordinates
        $galaxy = request()->input('galaxy');
        $system = request()->input('system');
        $position = request()->input('position');
        $targetType = (int)request()->input('type');
        $planetType = PlanetType::from($targetType);

        // Load the target planet
        $targetCoordinates = new Coordinate($galaxy, $system, $position);
        $targetPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinates, true, $planetType);
        if ($targetPlanet !== null) {
            $targetPlayer = $targetPlanet->getPlayer();

            $targetPlayerId = $targetPlayer->getId();
            $targetPlanetName = $targetPlanet->getPlanetName();
            $targetPlayerName = $targetPlayer->getUsername(false);
            $targetCoordinates = $targetPlanet->getPlanetCoordinates();
        } else {
            $targetPlayerId = 99999;
            $targetPlanetName = '?';
            $targetPlayerName = 'Deep space';
            $targetCoordinates = new Coordinate($galaxy, $system, $position);
        }

        // Determine enabled/available missions based on the current user, planet and target planet's properties.
        $enabledMissions = [];
        $units = $this->getUnitsFromRequest($currentPlanet);
        $allMissions = GameMissionFactory::getAllMissions();
        foreach ($allMissions as $mission) {
            $possible = $mission->isMissionPossible($currentPlanet, $targetCoordinates, $planetType, $units);
            if ($possible->possible) {
                $enabledMissions[] = $mission::getTypeId();
            }
        }

        // Don't collect error messages during target checking phase.
        // Errors will be shown when actually dispatching the fleet if needed.
        $errors = [];

        // Build orders array set key to true if the mission is enabled. Set to false if not.
        $orders = [];
        $possible_mission_types = [1, 2, 3, 4, 5, 6, 7, 8, 9, 15];
        foreach ($possible_mission_types as $mission) {
            if (in_array($mission, $enabledMissions, true)) {
                $orders[$mission] = true;
            } else {
                $orders[$mission] = false;
            }
        }

        $status = 'success';

        // If there are errors and no possible missions, set status to failure.
        if (count($errors) > 0 && count($enabledMissions) === 0) {
            $status = 'failure';
        }

        // Calculate empty and inactive systems between current planet and target
        $currentCoordinates = $currentPlanet->getPlanetCoordinates();
        $emptySystems = $coordinateDistanceCalculator->getNumEmptySystems($currentCoordinates, $targetCoordinates);
        $inactiveSystems = $coordinateDistanceCalculator->getNumInactiveSystems($currentCoordinates, $targetCoordinates);

        return response()->json([
            'shipsData' => $shipsData,
            'status' => $status,
            'errors' => $errors,
            'additionalFlightSpeedinfo' => '',
            'targetInhabited' => true,
            'targetIsStrong' => false,
            'targetIsOutlaw' => false,
            'targetIsBuddyOrAllyMember' => true,
            'targetPlayerId' => $targetPlayerId,
            'targetPlayerName' => $targetPlayerName,
            'targetPlayerColorClass' => 'active',
            'targetPlayerRankIcon' => '',
            'playerIsOutlaw' => false,
            'targetPlanet' => [
                'galaxy' => $targetCoordinates->galaxy,
                'system' => $targetCoordinates->system,
                'position' => $targetCoordinates->position,
                'type' => $targetType,
                'name' => $targetPlanetName,
            ],
            'emptySystems' => $emptySystems,
            'inactiveSystems' => $inactiveSystems,
            'bashingSystemLimitReached' => false,
            'targetOk' => true,
            'components' => [],
            'newAjaxToken' => '91cf2833548771ba423894d1f3dddb3c',
            'orders' => $orders,
        ]);
    }

    /**
     * Handles the dispatch of a fleet.
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse
     * @throws Exception
     */
    public function dispatchSendFleet(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
        $galaxy = request()->input('galaxy');
        $system = request()->input('system');
        $position = request()->input('position');
        $target_type = (int)request()->input('type');

        // TODO: add sanity check if all required fields are present in the request.

        // Expected form data
        /*
         token: 91cf2833548771ba423894d1f3dddb3c
         am202: 1
         galaxy: 1
         system: 1
         position: 12
         type: 1
         metal: 0
         crystal: 0
         deuterium: 0
         food: 0
         prioMetal: 2
         prioCrystal: 3
         prioDeuterium: 4
         prioFood: 1
         mission: 3
         speed: 10
         retreatAfterDefenderRetreat: 0
         lootFoodOnAttack: 0
         union: 0
         holdingtime: 0
         */

        // Get the current player's planet
        $planet = $player->planets->current();

        // Create the target coordinate
        $target_coordinate = new Coordinate($galaxy, $system, $position);

        // Get speed percent from the request.
        $speed_percent = (float)request()->input('speed');

        // Holding hours is the amount of hours the fleet will wait at the target planet and/or how long expedition will last.
        $holding_hours = (int)request()->input('holdingtime');

        // Extract mission type from the request
        $mission_type = (int)request()->input('mission');

        // Validate holdingtime for expedition missions (mission type 15)
        if ($mission_type === 15) {
            // Holding time cannot exceed level of Astrophysics research
            $astrophysics_level = $player->getResearchLevel(machine_name: 'astrophysics');
            if ($holding_hours < 1 || $holding_hours > $astrophysics_level) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        [
                            'message' => __('Expedition duration must be between :min_hours and :max_hours hours.', [
                                'min_hours' => 1,
                                'max_hours' => $astrophysics_level,
                            ]),
                            'error' => 140019
                        ]
                    ],
                    'components' => [],
                    'newAjaxToken' => csrf_token(),
                ]);
            }
        }

        // Extract units from the request and create a unit collection.
        // Loop through all input fields and get all units prefixed with "am".
        $units = $this->getUnitsFromRequest($planet);

        // Extract resources from the request
        $metal = (int)request()->input('metal');
        $crystal = (int)request()->input('crystal');
        $deuterium = (int)request()->input('deuterium');
        $resources = new Resources($metal, $crystal, $deuterium, 0);

        // Create a new fleet mission
        $planetType = PlanetType::from($target_type);

        try {
            $fleetMissionService->createNewFromPlanet($planet, $target_coordinate, $planetType, $mission_type, $units, $resources, $speed_percent, $holding_hours);

            return response()->json([
                'success' => true,
                'message' => 'Your fleet has been successfully sent.',
                'components' => [],
                'newAjaxToken' => csrf_token(),
                'redirectUrl' => route('fleet.index'),
            ]);
        } catch (Exception $e) {
            // This can happen if the user tries to send a fleet when there are no free fleet slots.
            return response()->json([
                'success' => false,
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'error' => 140019
                    ]
                ],
                'components' => [],
                'newAjaxToken' => csrf_token(),
            ]);
        }
    }

    /**
     * Handles the dispatch of a fleet via shortcut buttons on galaxy page.
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse
     * @throws Exception
     */
    public function dispatchSendMiniFleet(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
        $galaxy = request()->input('galaxy');
        $system = request()->input('system');
        $position = request()->input('position');
        $targetType = (int)request()->input('type');
        $shipCount = request()->input('shipCount');
        $mission_type = (int)request()->input('mission');

        // Get the current player's planet.
        $planet = $player->planets->current();

        // Units to be sent are static dependent on mission type.
        $units = new UnitCollection();
        $responseMessage = '';
        switch ($mission_type) {
            case 6: // Espionage
                $responseMessage = __('Send espionage probe to:');
                $probeCount = $player->getEspionageProbesAmount() ?? 1;
                $units->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), $probeCount);
                break;
            case 8: // Recycle
                $responseMessage = __('Send recycler to:');
                $units->addUnit(ObjectService::getUnitObjectByMachineName('recycler'), $shipCount);
                break;
        }

        // Create the target coordinate.
        $targetCoordinate = new Coordinate($galaxy, $system, $position);
        $resources = new Resources(0, 0, 0, 0);

        // Check if current planet has enough units to send.
        if (!$planet->hasUnits($units)) {
            return response()->json([
                'response' => [
                    'message' => __('Error, no ships available'),
                    'coordinates' => [
                        'galaxy' => $galaxy,
                        'system' => $system,
                        'position' => $position,
                    ],
                    'success' => false,
                ],
                'newAjaxToken' => csrf_token(),
                'components' => [],
            ]);
        }

        // Create a new fleet mission
        $planetType = PlanetType::from($targetType);
        $planetType = PlanetType::from($targetType);

        try {
            $fleetMission = $fleetMissionService->createNewFromPlanet($planet, $targetCoordinate, $planetType, $mission_type, $units, $resources, 10);

            // Calculate the actual amount of units sent.
            $fleetUnitCount = $fleetMissionService->getFleetUnitCount($fleetMission);

            return response()->json([
                'response' => [
                    'message' => $responseMessage,
                    'type' => 1,
                    'slots' => 1,
                    'probes' => 11,
                    'recyclers' => 0,
                    'explorers' => 9,
                    'missiles' => 0,
                    'shipsSent' => $fleetUnitCount,
                    'coordinates' => [
                        'galaxy' => $galaxy,
                        'system' => $system,
                        'position' => $position,
                    ],
                    'planetType' => 1,
                    'success' => true,
                ],
                'newAjaxToken' => csrf_token(),
                'components' => [],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'response' => [
                    'message' => $e->getMessage(),
                    'coordinates' => [
                        'galaxy' => $galaxy,
                        'system' => $system,
                        'position' => $position,
                    ],
                    'success' => false,
                ],
                'newAjaxToken' => csrf_token(),
                'components' => [],
            ]);
        }
    }

    /**
     * Recall an active fleet that has not yet been processed.
     *
     * @param PlayerService $player
     * @param FleetMissionService $fleetMissionService
     * @return JsonResponse
     */
    public function dispatchRecallFleet(PlayerService $player, FleetMissionService $fleetMissionService): JsonResponse
    {
        // Get the fleet mission id
        $fleet_mission_id = request()->input('fleet_mission_id');

        // Get the fleet mission service
        $fleetMission = $fleetMissionService->getFleetMissionById($fleet_mission_id);

        // Sanity check: only owner of the fleet mission can recall it.
        if ($fleetMission->user_id !== $player->getId()) {
            return response()->json([
                'components' => [],
                'newAjaxToken' => csrf_token(),
                'success' => false,
            ]);
        }

        // Recall the fleet mission
        $fleetMissionService->cancelMission($fleetMission);

        return response()->json([
            'components' => [],
            'newAjaxToken' => csrf_token(),
            'success' => true,
        ]);
    }

    /**
     * Get units from the request and create a UnitCollection.
     *
     * @param PlanetService $planet
     * @return UnitCollection
     * @throws Exception
     */
    private function getUnitsFromRequest(PlanetService $planet): UnitCollection
    {
        $units = new UnitCollection();
        foreach (request()->all() as $key => $value) {
            if (str_starts_with($key, 'am')) {
                $unit_id = (int)str_replace('am', '', $key);
                // Create GameObject
                $unitObject = ObjectService::getUnitObjectById($unit_id);
                $units->addUnit($unitObject, (int)$value);
            }
        }

        return $units;
    }
}
