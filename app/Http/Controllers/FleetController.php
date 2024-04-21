<?php

namespace OGame\Http\Controllers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameObjects\Models\UnitCollection;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resource;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\ViewModels\UnitViewModel;

class FleetController extends OGameController
{
    /**
     * Shows the fleet index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @param ObjectService $objects
     * @return View
     * @throws \Exception
     */
    public function index(Request $request, PlayerService $player, ObjectService $objects) : View
    {
        // Define ship ids to include in the fleet screen.
        // 0 = military ships
        // 1 = civil ships
        $screen_objects = [
            0 => ['light_fighter', 'heavy_fighter', 'cruiser', 'battle_ship', 'battlecruiser', 'bomber', 'destroyer', 'deathstar'],
            1 => ['small_cargo', 'large_cargo', 'colony_ship', 'recycler', 'espionage_probe'],
        ];

        $planet = $player->planets->current();

        $units = [];
        $count = 0;

        foreach ($screen_objects as $key_row => $objects_row) {
            foreach ($objects_row as $object_machine_name) {
                $count++;

                $object = $objects->getUnitObjectByMachineName($object_machine_name);

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
            'planet' => $planet,
            'units' => $units,
            'objects' => $objects->getShipObjects(),
            'shipAmount' => $planet->getFlightShipAmount()
        ]);
    }

    /**
     * Shows the fleet movement page
     *
     * @return View
     */
    public function movement() : View
    {
        return view('ingame.fleet.movement');
    }

    /**
     * @throws BindingResolutionException
     */
    public function dispatchCheckTarget(PlayerService $currentPlayer, ObjectService $objects) : JsonResponse {
        $enabledMissions = [];

        // Get target coordinates
        $galaxy = request()->input('galaxy');
        $system = request()->input('system');
        $position = request()->input('position');
        $type = request()->input('type');
        // Load the target planet
        $planetServiceFactory =  app()->make(PlanetServiceFactory::class);
        $targetPlanet = $planetServiceFactory->makeForCoordinate(new Coordinate($galaxy, $system, $position));
        $targetPlayer = null;
        if ($targetPlanet != null) {
            $targetPlayer = $targetPlanet->getPlayer();

            $targetPlayerId = $targetPlayer->getId();
            $targetPlanetName = $targetPlanet->getPlanetName();
            $targetPlayerName = $targetPlayer->getUsername();
            $targetCoordinates = $targetPlanet->getPlanetCoordinates();
        }
        else {
            $targetPlayerId = 99999;
            $targetPlanetName = '?';
            $targetPlayerName = 'Deep space';
            $targetCoordinates = new Coordinate($galaxy, $system, $position);
            $enabledMissions[] = 7;
        }

        // If position is 16, only enable expedition mission
        if ($position == 16) {
            $enabledMissions = [15];
        }
        else if ($currentPlayer->equals($targetPlayer)) {
            // If target player is the same as the current player, enable transport/deployment missions.
            $enabledMissions = [3, 4];
        }

        $currentPlanet = $currentPlayer->planets->current();

        // Return ships data for this planet taking into account the current planet's properties and research levels.
        $shipsData = [];
        foreach ($objects->getShipObjects() as $shipObject) {
            $shipsData[$shipObject->id] = [
                'id' => $shipObject->id,
                'name' => $shipObject->title,
                'baseFuelCapacity' => $shipObject->properties->capacity->calculate($currentPlanet)->totalValue,
                'baseCargoCapacity' => $shipObject->properties->capacity->calculate($currentPlanet)->totalValue,
                'fuelConsumption' => $shipObject->properties->fuel->calculate($currentPlanet)->totalValue,
                'speed' => $shipObject->properties->speed->calculate($currentPlanet)->totalValue
            ];
        }

        // Build orders array set key to true if the mission is enabled. Set to false if not.
        $orders = [];
        // Possible mission types: 1, 2, 3, 4, 5, 6, 7, 8, 9, 15
        $possible_mission_types = [1, 2, 3, 4, 5, 6, 7, 8, 9, 15];
        foreach ($possible_mission_types as $mission) {
            if (in_array($mission, $enabledMissions)) {
                $orders[$mission] = true;
            }
            else {
                $orders[$mission] = false;
            }
        }

        return response()->json([
            'shipsData' => $shipsData,
            'status' => 'success',
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
                'type' => 1,
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
     * @return JsonResponse
     */
    public function dispatchSendFleet(PlayerService $player) : JsonResponse
    {
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

        // Extract units from the request and create a unit collection.
        // Loop through all input fields and get all units prefixed with "am".
        $units = new UnitCollection();
        foreach (request()->all() as $key => $value) {
            if (strpos($key, 'am') === 0) {
                $unit_id = (int)str_replace('am', '', $key);
                // Create GameObject
                $unitObject = $player->planets->current()->objects->getUnitObjectById($unit_id);
                $units->addUnit($unitObject, (int)$value);
            }
        }

        // Extract resources from the request
        $metal = (int)request()->input('metal');
        $crystal = (int)request()->input('crystal');
        $deuterium = (int)request()->input('deuterium');
        $resources = new Resources($metal, $crystal, $deuterium, 0);

        // Extract mission type from the request
        $mission_type = (int)request()->input('mission');

        // Create a new fleet mission
        $fleetMissionService = app()->make(FleetMissionService::class);
        $fleetMissionService->create($planet, $mission_type, $units, $resources);

        return response()->json([
            'components' => [],
            'message' => 'Your fleet has been successfully sent.',
            'newAjaxToken' => csrf_token(),
            'redirectUrl' => route('fleet.index'),
            'success' => true,
        ]);
    }
}
