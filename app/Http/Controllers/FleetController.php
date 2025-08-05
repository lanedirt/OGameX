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
}
