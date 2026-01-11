<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\AllianceDepotService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class AllianceDepotController extends OGameController
{
    public function __construct(
        private AllianceDepotService $allianceDepotService,
        private PlanetServiceFactory $planetServiceFactory
    ) {
    }

    /**
     * Get the Alliance Depot dialog content (overlay view).
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
    {
        $current_planet = $player->planets->current();

        // Validate current planet is a planet (not moon)
        if (!$current_planet->isPlanet()) {
            return view('ingame.alliancedepot.error', [
                'error' => __('Alliance Depot can only be used on planets.'),
            ]);
        }

        // Check if Alliance Depot exists
        $alliance_depot_level = $current_planet->getObjectLevel('alliance_depot');
        if ($alliance_depot_level < 1) {
            return view('ingame.alliancedepot.error', [
                'error' => __('No Alliance Depot built on this planet.'),
            ]);
        }

        // Get all ACS Defend fleets currently holding at this planet
        $holding_fleets = $this->getHoldingFleets($current_planet->getPlanetId());

        // Also get fleets holding at the accompanying moon (if it exists)
        if ($current_planet->hasMoon()) {
            $moon = $current_planet->moon();
            $moon_holding_fleets = $this->getHoldingFleets($moon->getPlanetId());
            $holding_fleets = array_merge($holding_fleets, $moon_holding_fleets);
        }

        // Calculate deuterium cost per hour for each fleet
        foreach ($holding_fleets as &$fleet) {
            $outboundMission = FleetMission::find($fleet['id']);
            if ($outboundMission instanceof FleetMission) {
                $fleet['deut_cost_per_hour'] = $this->allianceDepotService->calculateSupplyRocketCost($outboundMission, 1);
            }
        }

        // Render the dialog view
        return view('ingame.alliancedepot.dialog', [
            'current_planet' => $current_planet,
            'alliance_depot_level' => $alliance_depot_level,
            'holding_fleets' => $holding_fleets,
        ]);
    }

    /**
     * Get all fleets currently holding at the specified planet.
     *
     * @param int $planetId
     * @return array<int, array<string, mixed>>
     */
    private function getHoldingFleets(int $planetId): array
    {
        $currentTime = (int)time();

        // Query for ACS Defend missions (type 5) that are currently holding
        // A fleet is "holding" when:
        // - It has arrived (time_arrival <= now)
        // - It hasn't been processed yet (processed = 0) OR has been processed but not finished holding
        //   (ACS Defend missions are processed when time_arrival + time_holding is reached)
        // - It's not canceled (canceled = 0)
        $missions = FleetMission::where('mission_type', 5)
            ->where('planet_id_to', $planetId)
            ->where('time_arrival', '<=', $currentTime)
            ->where('canceled', 0)
            ->get();

        $holdingFleets = [];

        foreach ($missions as $mission) {
            // Get the return mission if it exists
            $returnMission = FleetMission::where('planet_id_from', $mission->planet_id_to)
                ->where('planet_id_to', $mission->planet_id_from)
                ->where('mission_type', 5)
                ->where('time_departure', '>=', $mission->time_arrival)
                ->where('canceled', 0)
                ->orderBy('time_departure', 'asc')
                ->first();

            // Calculate expected return departure time
            $expectedReturnDeparture = $mission->time_arrival + $mission->time_holding;

            // Only include if:
            // 1. Return mission exists and hasn't departed yet (still holding), OR
            // 2. No return mission yet but fleet is still holding (expected return time is in future)
            if (($returnMission && $returnMission->time_departure > $currentTime) ||
                (!$returnMission && $expectedReturnDeparture > $currentTime)) {
                // Get fleet composition
                $ships = $this->getFleetShips($mission);

                // Get sender player name (strip HTML tags)
                $senderPlayer = new PlayerService($mission->user_id);
                $senderPlayerName = strip_tags($senderPlayer->getUsername());

                // Get sender planet info for coordinates
                $senderPlanetService = $this->planetServiceFactory->make($mission->planet_id_from, true);

                // Get destination planet info to show where fleet is holding
                $destinationPlanetService = $this->planetServiceFactory->make($mission->planet_id_to, true);
                $destinationName = $destinationPlanetService ? $destinationPlanetService->getPlanetName() : 'Unknown';
                $destinationType = $mission->type_to; // 1 = planet, 3 = moon

                // Use return mission times if it exists, otherwise calculate from outbound mission
                $returnTime = $returnMission ? $returnMission->time_departure : $expectedReturnDeparture;

                $holdingFleets[] = [
                    'id' => $mission->id,
                    'sender_planet_id' => $mission->planet_id_from,
                    'sender_player_name' => $senderPlayerName,
                    'sender_coordinates' => $senderPlanetService ? $senderPlanetService->getPlanetCoordinates()->asString() : 'Unknown',
                    'destination_name' => $destinationName,
                    'destination_type' => $destinationType,
                    'arrival_time' => $mission->time_arrival,
                    'return_time' => $returnTime,
                    'hold_duration' => $returnTime - $currentTime,
                    'ships' => $ships,
                ];
            }
        }

        return $holdingFleets;
    }

    /**
     * Get ship composition from a fleet mission.
     *
     * @param FleetMission $mission
     * @return array<string, array<string, mixed>>
     */
    private function getFleetShips(FleetMission $mission): array
    {
        $ships = [];
        $shipTypes = [
            'small_cargo', 'large_cargo', 'light_fighter', 'heavy_fighter',
            'cruiser', 'battle_ship', 'battlecruiser', 'bomber', 'destroyer',
            'deathstar', 'colony_ship', 'recycler', 'espionage_probe'
        ];

        foreach ($shipTypes as $shipType) {
            $amount = (int)$mission->$shipType;
            if ($amount > 0) {
                $shipObject = ObjectService::getObjectByMachineName($shipType);
                $ships[$shipType] = [
                    'machine_name' => $shipType,
                    'title' => $shipObject->title,
                    'amount' => $amount,
                ];
            }
        }

        return $ships;
    }

    /**
     * Send a supply rocket to extend a fleet's hold time.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function sendSupplyRocket(Request $request, PlayerService $player): JsonResponse
    {
        $current_planet = $player->planets->current();

        // Validate current planet is a planet (not moon)
        if (!$current_planet->isPlanet()) {
            return response()->json([
                'success' => false,
                'error' => __('Alliance Depot can only be used on planets.'),
            ]);
        }

        // Check if Alliance Depot exists
        $alliance_depot_level = $current_planet->getObjectLevel('alliance_depot');
        if ($alliance_depot_level < 1) {
            return response()->json([
                'success' => false,
                'error' => __('No Alliance Depot built on this planet.'),
            ]);
        }

        // Get fleet mission ID and extension hours from request
        $fleetMissionId = (int)$request->input('fleet_mission_id');
        $extensionHours = (int)$request->input('extension_hours', 1);

        // Validate extension hours (1-32 hours)
        if ($extensionHours < 1 || $extensionHours > 32) {
            return response()->json([
                'success' => false,
                'error' => __('Extension hours must be between 1 and 32.'),
            ]);
        }

        // Find the outbound mission
        $outboundMission = FleetMission::find($fleetMissionId);
        if (!$outboundMission) {
            return response()->json([
                'success' => false,
                'error' => __('Fleet mission not found.'),
            ]);
        }

        // Validate that the fleet is holding at this planet
        if ($outboundMission->planet_id_to !== $current_planet->getPlanetId()) {
            return response()->json([
                'success' => false,
                'error' => __('This fleet is not holding at your planet.'),
            ]);
        }

        // Get the return mission (may not exist yet if fleet is still in hold phase)
        $returnMission = $this->allianceDepotService->getReturnMission($outboundMission);

        // Check if hold time can be extended
        if (!$this->allianceDepotService->canExtendHoldTime($outboundMission, $returnMission)) {
            return response()->json([
                'success' => false,
                'error' => __('This fleet cannot have its hold time extended. Fleet must be holding for at least 1 hour.'),
            ]);
        }

        // Calculate deuterium cost
        $deuteriumCost = $this->allianceDepotService->calculateSupplyRocketCost($outboundMission, $extensionHours);

        // Check if player has enough deuterium
        $availableDeuterium = $current_planet->deuterium()->get();
        if ($availableDeuterium < $deuteriumCost) {
            return response()->json([
                'success' => false,
                'error' => __('Not enough deuterium. Required: :cost', ['cost' => number_format($deuteriumCost, 0, ',', '.')]),
            ]);
        }

        // Deduct deuterium from planet
        $current_planet->deductResources(new Resources(0, 0, $deuteriumCost, 0));

        // Extend hold time
        if (!$this->allianceDepotService->extendHoldTime($outboundMission, $returnMission, $extensionHours)) {
            // Refund deuterium if extension fails
            $current_planet->addResources(new Resources(0, 0, $deuteriumCost, 0));
            return response()->json([
                'success' => false,
                'error' => __('Failed to extend hold time.'),
            ]);
        }

        // Reload the outbound mission to get updated time_holding if it was modified
        $outboundMission->refresh();

        // Calculate the new return departure time
        $newReturnDeparture = $returnMission
            ? $returnMission->time_departure
            : $outboundMission->time_arrival + $outboundMission->time_holding;

        return response()->json([
            'success' => true,
            'message' => __('The fleet has been supplied successfully.'),
            'deuterium_cost' => $deuteriumCost,
            'new_return_time' => $newReturnDeparture,
        ]);
    }
}
