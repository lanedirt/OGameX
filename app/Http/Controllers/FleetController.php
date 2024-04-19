<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
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

    public function dispatchCheckTarget(PlanetService $planet, ObjectService $objects) : \Illuminate\Http\JsonResponse {
        /*
         {"additionalFlightSpeedinfo":"","shipsData":{"204":{"id":204,"name":"Light Fighter","baseFuelCapacity":62,"baseCargoCapacity":62,"fuelConsumption":7,"speed":32500},"205":{"id":205,"name":"Heavy Fighter","baseFuelCapacity":125,"baseCargoCapacity":125,"fuelConsumption":27,"speed":28000},"206":{"id":206,"name":"Cruiser","baseFuelCapacity":1000,"baseCargoCapacity":1000,"fuelConsumption":112,"speed":42000},"207":{"id":207,"name":"Battleship","baseFuelCapacity":1875,"baseCargoCapacity":1875,"fuelConsumption":187,"speed":35000},"215":{"id":215,"name":"Battlecruiser","baseFuelCapacity":937,"baseCargoCapacity":937,"fuelConsumption":93,"speed":35000},"211":{"id":211,"name":"Bomber","baseFuelCapacity":625,"baseCargoCapacity":625,"fuelConsumption":262,"speed":11200},"213":{"id":213,"name":"Destroyer","baseFuelCapacity":2500,"baseCargoCapacity":2500,"fuelConsumption":375,"speed":17500},"214":{"id":214,"name":"Deathstar","baseFuelCapacity":1250000,"baseCargoCapacity":1250000,"fuelConsumption":1,"speed":250},"218":{"id":218,"name":"Reaper","baseFuelCapacity":12500,"baseCargoCapacity":12500,"fuelConsumption":412,"speed":24500},"219":{"id":219,"name":"Pathfinder","baseFuelCapacity":14500,"baseCargoCapacity":14500,"fuelConsumption":112,"speed":42000},"202":{"id":202,"name":"Small Cargo","baseFuelCapacity":6250,"baseCargoCapacity":6250,"fuelConsumption":3,"speed":8000},"203":{"id":203,"name":"Large Cargo","baseFuelCapacity":31250,"baseCargoCapacity":31250,"fuelConsumption":18,"speed":12000},"208":{"id":208,"name":"Colony Ship","baseFuelCapacity":9375,"baseCargoCapacity":9375,"fuelConsumption":375,"speed":4500},"209":{"id":209,"name":"Recycler","baseFuelCapacity":29000,"baseCargoCapacity":29000,"fuelConsumption":112,"speed":5200},"210":{"id":210,"name":"Espionage Probe","baseFuelCapacity":6,"baseCargoCapacity":0,"fuelConsumption":1,"speed":160000000},"217":{"id":217,"name":"Crawler","baseFuelCapacity":0,"baseCargoCapacity":0,"fuelConsumption":1,"speed":0}},"status":"success","orders":{"15":false,"7":false,"8":false,"3":true,"4":true,"5":false,"6":false,"1":false,"9":false,"2":false},"targetInhabited":true,"targetIsStrong":false,"targetIsOutlaw":false,"targetIsBuddyOrAllyMember":true,"targetPlayerId":113970,"targetPlayerName":"President Hati2","targetPlayerColorClass":"active","targetPlayerRankIcon":"","playerIsOutlaw":false,"targetPlanet":{"galaxy":7,"system":158,"position":10,"type":1,"name":"MyBaseYo"},"emptySystems":0,"inactiveSystems":0,"bashingSystemLimitReached":false,"targetOk":true,"components":[],"newAjaxToken":"91cf2833548771ba423894d1f3dddb3c"}

         */

        // Return ships data for this planet taking into account the current planet's properties and research levels.
        $shipsData = [];
        foreach ($objects->getShipObjects() as $shipObject) {
            $shipsData[$shipObject->id] = [
                'id' => $shipObject->id,
                'name' => $shipObject->title,
                'baseFuelCapacity' => $shipObject->properties->capacity->calculate($planet)->totalValue,
                'baseCargoCapacity' => $shipObject->properties->capacity->calculate($planet)->totalValue,
                'fuelConsumption' => $shipObject->properties->fuel->calculate($planet)->totalValue,
                'speed' => $shipObject->properties->speed->calculate($planet)->totalValue
            ];
        }

        return response()->json([
            'shipsData' => $shipsData,
            'status' => 'success',
            'additionalFlightSpeedinfo' => '',
            'targetInhabited' => true,
            'targetIsStrong' => false,
            'targetIsOutlaw' => false,
            'targetIsBuddyOrAllyMember' => true,
            'targetPlayerId' => 113970,
            'targetPlayerName' => 'President Hati2',
            'targetPlayerColorClass' => 'active',
            'targetPlayerRankIcon' => '',
            'playerIsOutlaw' => false,
            'targetPlanet' => [
                'galaxy' => 1,
                'system' => 2,
                'position' => 3,
                'type' => 1,
                'name' => 'PlanetNameFleet'
            ],
            'emptySystems' => 0,
            'inactiveSystems' => 0,
            'bashingSystemLimitReached' => false,
            'targetOk' => true,
            'components' => [],
            'newAjaxToken' => '91cf2833548771ba423894d1f3dddb3c'
        ]);
    }
}
