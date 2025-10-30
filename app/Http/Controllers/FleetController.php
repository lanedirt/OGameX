<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\GameMissionFactory;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\ACSService;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
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

        // Get available ACS groups for the target (if target is specified)
        $acsGroups = [];
        $targetGalaxy = $request->get('galaxy');
        $targetSystem = $request->get('system');
        $targetPosition = $request->get('position');
        $targetType = $request->get('type');

        \Log::debug('ACS Groups Query', [
            'galaxy' => $targetGalaxy,
            'system' => $targetSystem,
            'position' => $targetPosition,
            'type' => $targetType,
            'has_coordinates' => (bool)($targetGalaxy && $targetSystem && $targetPosition),
        ]);

        if ($targetGalaxy && $targetSystem && $targetPosition) {
            // Find active ACS groups targeting this coordinate
            $currentTime = time();

            // First, get ALL ACS groups to debug
            $allGroups = \OGame\Models\AcsGroup::all();
            \Log::debug('Total ACS groups in database: ' . $allGroups->count());

            $acsGroups = \OGame\Models\AcsGroup::where('galaxy_to', $targetGalaxy)
                ->where('system_to', $targetSystem)
                ->where('position_to', $targetPosition)
                ->where('type_to', $targetType ?? 1)
                ->whereIn('status', ['pending', 'active'])
                ->where('arrival_time', '>', $currentTime)
                ->get()
                ->map(function ($group) use ($player) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'target' => $group->galaxy_to . ':' . $group->system_to . ':' . $group->position_to,
                        'arrival_time' => $group->arrival_time,
                        'arrival_time_formatted' => date('Y-m-d H:i:s', $group->arrival_time),
                        'fleet_count' => $group->fleetMembers()->count(),
                        'is_creator' => $group->creator_id === $player->getId(),
                    ];
                })
                ->toArray();

            \Log::debug('ACS Groups found: ' . count($acsGroups), [
                'current_time' => $currentTime,
                'groups' => $acsGroups,
            ]);
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
            'acsGroups' => $acsGroups,
        ]);
    }

    /**
     * Shows the fleet movement page
     *
     * @return View
     */
    public function movement(): View
    {
        return view('ingame.fleet.movement');
    }

    /**
     * Checks the target planet for possible missions.
     *
     * @param PlayerService $currentPlayer
     * @param PlanetServiceFactory $planetServiceFactory
     * @return JsonResponse
     * @throws Exception
     */
    public function dispatchCheckTarget(PlayerService $currentPlayer, PlanetServiceFactory $planetServiceFactory): JsonResponse
    {
        $currentPlanet = $currentPlayer->planets->current();

        // Return ships data for this planet taking into account the current planet's properties and research levels.
        $shipsData = [];
        foreach (ObjectService::getShipObjects() as $shipObject) {
            $shipsData[$shipObject->id] = [
                'id' => $shipObject->id,
                'name' => $shipObject->title,
                'baseFuelCapacity' => $shipObject->properties->capacity->calculate($currentPlayer)->totalValue,
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
        $errors = [];
        $units = $this->getUnitsFromRequest($currentPlanet);
        $allMissions = GameMissionFactory::getAllMissions();
        foreach ($allMissions as $mission) {
            $possible = $mission->isMissionPossible($currentPlanet, $targetCoordinates, $planetType, $units);
            if ($possible->possible) {
                $enabledMissions[] = $mission::getTypeId();
            } elseif (!empty($possible->error)) {
                // If the mission is not possible and has an error message, return error message in JSON.
                $errors[] = [
                    'message' => $possible->error,
                    'error' => 140035 // TODO: is this actually required by the frontend?
                ];
            }
        }

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
            'emptySystems' => 0,
            'inactiveSystems' => 0,
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

        // Extract units from the request and create a unit collection.
        // Loop through all input fields and get all units prefixed with "am".
        $units = $this->getUnitsFromRequest($planet);

        // Extract resources from the request
        $metal = (int)request()->input('metal');
        $crystal = (int)request()->input('crystal');
        $deuterium = (int)request()->input('deuterium');
        $resources = new Resources($metal, $crystal, $deuterium, 0);

        // Extract mission type from the request
        $mission_type = (int)request()->input('mission');

        // Extract ACS union parameter (0 = create new group, >0 = join existing group)
        $union = (int)request()->input('union', 0);

        // Create a new fleet mission
        $planetType = PlanetType::from($target_type);

        try {
            // For ACS missions, check if we need to adjust speed to match group arrival time
            $adjustedSpeed = $speed_percent;
            $targetArrivalTime = null;

            if ($mission_type === 2 && $union > 0) {
                // Joining existing ACS group - need to sync arrival time
                $acsGroup = ACSService::findGroup($union);
                if (!$acsGroup) {
                    throw new Exception('ACS group not found.');
                }

                \Log::debug('Joining ACS group', [
                    'group_id' => $acsGroup->id,
                    'group_arrival_time' => $acsGroup->arrival_time,
                    'group_arrival_formatted' => date('Y-m-d H:i:s', $acsGroup->arrival_time),
                    'current_time' => time(),
                    'time_until_arrival' => $acsGroup->arrival_time - time(),
                ]);

                // Verify target matches
                if ($acsGroup->galaxy_to !== $target_coordinate->galaxy ||
                    $acsGroup->system_to !== $target_coordinate->system ||
                    $acsGroup->position_to !== $target_coordinate->position ||
                    $acsGroup->type_to !== $planetType->value) {
                    throw new Exception('ACS group target does not match your fleet target.');
                }

                // Verify player can join
                if (!ACSService::canJoinGroup($acsGroup, $player->getId())) {
                    throw new Exception('You cannot join this ACS group.');
                }

                $targetArrivalTime = $acsGroup->arrival_time;

                // Calculate required speed to match arrival time
                $adjustedSpeed = $this->calculateRequiredSpeed(
                    $fleetMissionService,
                    $planet,
                    $target_coordinate,
                    $units,
                    $targetArrivalTime
                );

                \Log::debug('Speed adjustment calculated', [
                    'original_speed' => $speed_percent,
                    'adjusted_speed' => $adjustedSpeed,
                    'target_arrival_time' => $targetArrivalTime,
                ]);

                if ($adjustedSpeed === null) {
                    throw new Exception('Cannot synchronize with ACS group - target too far or fleet too slow.');
                }
            }

            $fleetMission = $fleetMissionService->createNewFromPlanet($planet, $target_coordinate, $planetType, $mission_type, $units, $resources, $adjustedSpeed, $holding_hours);

            \Log::debug('Fleet mission created', [
                'mission_id' => $fleetMission->id,
                'mission_type' => $mission_type,
                'time_arrival' => $fleetMission->time_arrival,
                'time_arrival_formatted' => date('Y-m-d H:i:s', $fleetMission->time_arrival),
                'speed_used' => $adjustedSpeed,
                'target_arrival_time' => $targetArrivalTime,
                'arrival_matches' => $targetArrivalTime ? (abs($fleetMission->time_arrival - $targetArrivalTime) <= 1) : 'N/A',
            ]);

            // Handle ACS group creation/joining for ACS Attack (type 2) missions
            if ($mission_type === 2) {
                $this->handleACSGroupForMission($fleetMission, $union, $player, $target_coordinate, $planetType);
            }

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
                // TODO: make espionage probe amount configurable in user settings and use that value here.
                $responseMessage = __('Send espionage probe to:');
                $units->addUnit(ObjectService::getUnitObjectByMachineName('espionage_probe'), 1);
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
            $fleetMission = $fleetMissionService->createNewFromPlanet($planet, $targetCoordinate, $planetType, $mission_type, $units, $resources, 100);

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

    /**
     * Handle ACS group creation or joining for a fleet mission.
     *
     * @param \OGame\Models\FleetMission $fleetMission
     * @param int $union The union/ACS group ID (0 = create new, >0 = join existing)
     * @param PlayerService $player
     * @param Coordinate $targetCoordinate
     * @param PlanetType $targetType
     * @return void
     * @throws Exception
     */
    private function handleACSGroupForMission(\OGame\Models\FleetMission $fleetMission, int $union, PlayerService $player, Coordinate $targetCoordinate, PlanetType $targetType): void
    {
        $acsGroup = null;

        if ($union === 0) {
            // Create a new ACS group with this fleet's arrival time
            $acsGroup = ACSService::createGroup(
                $player->getId(),
                'ACS Attack ' . $targetCoordinate->galaxy . ':' . $targetCoordinate->system . ':' . $targetCoordinate->position,
                $targetCoordinate->galaxy,
                $targetCoordinate->system,
                $targetCoordinate->position,
                $targetType->value,
                $fleetMission->time_arrival
            );
        } else {
            // Join existing ACS group (validation already done earlier)
            $acsGroup = ACSService::findGroup($union);

            if (!$acsGroup) {
                throw new Exception('ACS group not found.');
            }
        }

        // Link the fleet mission to the ACS group (passing FleetMission object, not ID)
        if ($acsGroup) {
            ACSService::addFleetToGroup($acsGroup, $fleetMission, $player->getId());
        }
    }

    /**
     * Calculate the required speed percentage to arrive at a specific time.
     *
     * @param FleetMissionService $fleetMissionService
     * @param PlanetService $planet
     * @param Coordinate $targetCoordinate
     * @param UnitCollection $units
     * @param int $targetArrivalTime
     * @return float|null The required speed percentage (10-100), or null if impossible
     */
    private function calculateRequiredSpeed(
        FleetMissionService $fleetMissionService,
        PlanetService $planet,
        Coordinate $targetCoordinate,
        UnitCollection $units,
        int $targetArrivalTime
    ): ?float {
        $currentTime = time();
        $requiredDuration = $targetArrivalTime - $currentTime;

        // If arrival time is in the past or too soon, it's impossible
        if ($requiredDuration <= 0) {
            return null;
        }

        // Calculate duration at 100% speed
        $durationAt100 = $fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, 100);

        // If even at 100% speed we can't arrive in time, it's impossible
        if ($durationAt100 > $requiredDuration) {
            return null;
        }

        // Calculate duration at 10% speed (slowest)
        $durationAt10 = $fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, 10);

        // If even at 10% speed we arrive too soon, it's impossible
        if ($durationAt10 < $requiredDuration) {
            return null;
        }

        // Binary search to find the right speed percentage
        $minSpeed = 10.0;
        $maxSpeed = 100.0;
        $tolerance = 1; // 1 second tolerance

        for ($i = 0; $i < 20; $i++) { // Max 20 iterations for binary search
            $midSpeed = ($minSpeed + $maxSpeed) / 2;
            $duration = $fleetMissionService->calculateFleetMissionDuration($planet, $targetCoordinate, $units, $midSpeed);

            if (abs($duration - $requiredDuration) <= $tolerance) {
                return round($midSpeed);
            }

            if ($duration > $requiredDuration) {
                // Too slow, need to go faster
                $minSpeed = $midSpeed;
            } else {
                // Too fast, need to slow down
                $maxSpeed = $midSpeed;
            }
        }

        // Return the closest speed we found
        return round(($minSpeed + $maxSpeed) / 2);
    }

    /**
     * Get available ACS groups for specific coordinates (AJAX endpoint)
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function getACSGroups(Request $request, PlayerService $player): JsonResponse
    {
        $targetGalaxy = $request->input('galaxy');
        $targetSystem = $request->input('system');
        $targetPosition = $request->input('position');
        $targetType = $request->input('type', 1);

        \Log::debug('AJAX ACS Groups Query', [
            'galaxy' => $targetGalaxy,
            'system' => $targetSystem,
            'position' => $targetPosition,
            'type' => $targetType,
        ]);

        if (!$targetGalaxy || !$targetSystem || !$targetPosition) {
            return response()->json([
                'success' => false,
                'message' => 'Missing target coordinates',
                'groups' => []
            ]);
        }

        $currentTime = time();

        // Find active ACS groups targeting this coordinate
        $acsGroups = \OGame\Models\AcsGroup::where('galaxy_to', $targetGalaxy)
            ->where('system_to', $targetSystem)
            ->where('position_to', $targetPosition)
            ->where('type_to', $targetType)
            ->whereIn('status', ['pending', 'active'])
            ->where('arrival_time', '>', $currentTime)
            ->get()
            ->map(function ($group) use ($player) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'target' => $group->galaxy_to . ':' . $group->system_to . ':' . $group->position_to,
                    'arrival_time' => $group->arrival_time,
                    'arrival_time_formatted' => date('Y-m-d H:i:s', $group->arrival_time),
                    'fleet_count' => $group->fleetMembers()->count(),
                    'is_creator' => $group->creator_id === $player->getId(),
                ];
            })
            ->values()
            ->toArray();

        \Log::debug('AJAX ACS Groups found: ' . count($acsGroups), [
            'groups' => $acsGroups,
        ]);

        return response()->json([
            'success' => true,
            'groups' => $acsGroups
        ]);
    }
}
