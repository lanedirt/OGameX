<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\PhalanxService;
use OGame\Services\PlayerService;

class PhalanxController extends OGameController
{
    /**
     * Scan a planet using sensor phalanx.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PhalanxService $phalanxService
     * @return JsonResponse
     * @throws Exception
     */
    public function scan(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory, PhalanxService $phalanxService): JsonResponse
    {
        // Validate request
        $request->validate([
            'galaxy' => 'required|integer|min:1',
            'system' => 'required|integer|min:1',
            'position' => 'required|integer|min:1|max:15',
        ]);

        // Create default response structure
        $response = [
            'success' => true,
            'server_time' => time(),
            'target' => [
                'galaxy' => (int)$request->input('galaxy'),
                'system' => (int)$request->input('system'),
                'position' => (int)$request->input('position'),
                'planet_name' => '',
                'player_name' => '',
            ],
        ];

        $current_planet = $player->planets->current();

        // Check if current planet is a moon
        if (!$current_planet->isMoon()) {
            $response['is_error'] = true;
            $response['error_message'] = 'Sensor Phalanx can only be used from a moon.';
            return response()->json($response);
        }

        // Get sensor phalanx level
        $phalanx_level = $current_planet->getObjectLevel('sensor_phalanx');

        if ($phalanx_level === 0) {
            $response['is_error'] = true;
            $response['error_message'] = 'No Sensor Phalanx built on this moon.';
            return response()->json($response);
        }

        // Create target coordinates
        $target_coordinate = new Coordinate(
            (int)$request->input('galaxy'),
            (int)$request->input('system'),
            (int)$request->input('position')
        );

        // Get moon coordinates
        $moon_coordinates = $current_planet->getPlanetCoordinates();

        // Check if target is in range
        if (!$phalanxService->canScanTarget($moon_coordinates->galaxy, $moon_coordinates->system, $phalanx_level, $target_coordinate)) {
            $max_range = $phalanxService->calculatePhalanxRange($phalanx_level);
            $response['is_error'] = true;
            $response['error_message'] = 'Target is out of range. Your sensor phalanx (Level ' . $phalanx_level . ') can scan up to ' . $max_range . ' systems away.';
            return response()->json($response);
        }

        // Check if enough deuterium
        if (!$phalanxService->hasEnoughDeuterium($current_planet->deuterium()->get())) {
            $response['is_error'] = true;
            $response['error_message'] = 'Not enough Deuterium!';
            return response()->json($response);
        }

        // Load target planet
        try {
            $target_planet = $planetServiceFactory->makePlanetForCoordinate($target_coordinate);
        } catch (Exception $e) {
            $response['is_error'] = true;
            $response['error_message'] = 'No planet found at these coordinates.';
            return response()->json($response);
        }

        // Cannot scan moons (OGame rule)
        if ($target_planet->isMoon()) {
            $response['target']['planet_name'] = $target_planet->getPlanetName();
            $response['target']['player_name'] = $target_planet->getPlayer()->getUsername();
            $response['is_error'] = true;
            $response['error_message'] = 'Moons cannot be scanned with Sensor Phalanx.';
            return response()->json($response);
        }

        // Cannot scan admin planets
        if ($target_planet->getPlayer()->isAdmin()) {
            $response['target']['planet_name'] = $target_planet->getPlanetName();
            $response['target']['player_name'] = $target_planet->getPlayer()->getUsername();
            $response['is_error'] = true;
            $response['error_message'] = 'Administrator planets cannot be scanned.';
            return response()->json($response);
        }

        // Cannot scan own planets
        if ($target_planet->getPlayer()->getId() === $player->getId()) {
            $response['target']['planet_name'] = $target_planet->getPlanetName();
            $response['target']['player_name'] = $target_planet->getPlayer()->getUsername();
            $response['is_error'] = true;
            $response['error_message'] = 'You cannot scan your own planets.';
            return response()->json($response);
        }

        // Perform scan
        $fleet_movements = $phalanxService->scanPlanetFleets($target_planet->getPlanetId(), $player->getId());

        // Deduct deuterium cost
        $scan_cost = new Resources(0, 0, $phalanxService->getScanCost(), 0);
        $current_planet->deductResources($scan_cost);

        $content_html = view('ingame.phalanx.content', [
            'fleet_movements' => $fleet_movements,
            'server_time' => time(),
        ])->render();

        // Return scan results
        return response()->json([
            'success' => true,
            'server_time' => time(),
            'target' => [
                'galaxy' => $target_coordinate->galaxy,
                'system' => $target_coordinate->system,
                'position' => $target_coordinate->position,
                'planet_name' => $target_planet->getPlanetName(),
                'player_name' => $target_planet->getPlayer()->getUsername(),
            ],
            'scan_cost' => $phalanxService->getScanCost(),
            'fleet_count' => count($fleet_movements),
            'content_html' => $content_html,
        ]);
    }
}
