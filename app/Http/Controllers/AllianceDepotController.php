<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use OGame\Models\FleetMission;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class AllianceDepotController extends OGameController
{
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
        // - It hasn't returned yet (we need to check if return mission exists and hasn't arrived)
        // - It's not processed yet (processed = 0)
        // - It's not canceled (canceled = 0)
        $missions = FleetMission::where('mission_type', 5)
            ->where('planet_id_to', $planetId)
            ->where('time_arrival', '<=', $currentTime)
            ->where('processed', 0)
            ->where('canceled', 0)
            ->get();

        $holdingFleets = [];

        foreach ($missions as $mission) {
            // Get the return mission to check hold time
            $returnMission = FleetMission::where('planet_id_from', $mission->planet_id_to)
                ->where('planet_id_to', $mission->planet_id_from)
                ->where('mission_type', 5)
                ->where('time_departure', '>=', $mission->time_arrival)
                ->where('canceled', 0)
                ->orderBy('time_departure', 'asc')
                ->first();

            // Only include if return mission exists and hasn't arrived yet
            if ($returnMission && $returnMission->time_arrival > $currentTime) {
                // Get fleet composition
                $ships = $this->getFleetShips($mission);

                // Get sender planet info
                $senderPlanet = $mission->planetFrom;

                $holdingFleets[] = [
                    'id' => $mission->id,
                    'sender_planet_id' => $mission->planet_id_from,
                    'sender_planet_name' => $senderPlanet ? $senderPlanet->planet_name : 'Unknown',
                    'sender_coordinates' => $senderPlanet ? $senderPlanet->getPlanetCoordinates()->asString() : 'Unknown',
                    'arrival_time' => $mission->time_arrival,
                    'return_time' => $returnMission->time_arrival,
                    'hold_duration' => $returnMission->time_arrival - $currentTime,
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
}
