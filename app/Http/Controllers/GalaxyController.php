<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameMissions\ColonisationMission;
use OGame\Models\Planet;
use OGame\Services\PlayerService;

class GalaxyController extends OGameController
{
    /**
     * Shows the galaxy index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     */
    public function index(Request $request, PlayerService $player): View
    {
        $this->setBodyId('galaxy');

        // Get current galaxy and system from current planet.
        $planet = $player->planets->current();
        $coordinates = $planet->getPlanetCoordinates();
        $galaxy = $coordinates->galaxy;
        $system = $coordinates->system;

        // Get galaxy and system querystring params if set instead.
        $galaxy_qs = $request->input('galaxy', '0');
        $system_qs = $request->input('system', '0');
        if (!empty($galaxy_qs) && !empty($system_qs)) {
            $galaxy = (int)$galaxy_qs;
            $system = (int)$system_qs;
        }

        return view('ingame.galaxy.index')->with([
            'current_galaxy' => $galaxy,
            'current_system' => $system,
            'espionage_probe_count' => $planet->getObjectAmount('espionage_probe'),
            'recycler_count' => 0,
            'interplanetary_missiles_count' => 0,
            'used_slots' => 0,
            'max_slots' => 1,
        ]);
    }

    /**
     * Get galaxy table (used for both static and AJAX requests)
     *
     * @param int $galaxy
     * @param int $system
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @return array<int, array<string, array<int|string, array<int|string, array<string, bool>|bool|int|string>|bool|int|string>|int|string>>
     */
    public function getGalaxyArray(int $galaxy, int $system, PlayerService $player, PlanetServiceFactory $planetServiceFactory): array
    {
        $user_planet = $player->planets->current();

        // Retrieve all planets from this galaxy and system.
        $planet_list = Planet::where(['galaxy' => $galaxy, 'system' => $system])->get();
        $planets = [];
        foreach ($planet_list as $record) {
            $planetService = $planetServiceFactory->make($record->id);
            $planets[$record->planet] = $planetService;
        }

        // Render galaxy rows
        $galaxy_rows = [];
        for ($i = 1; $i <= 15; $i++) {
            if (!empty($planets[$i])) {
                // Planet with player
                $planet = $planets[$i];
                $row_player = $planet->getPlayer();
                $nameAbbreviations = [];
                if ($player->isAdmin()) {
                    $nameAbbreviations[] = 'admin';
                }

                $galaxy_rows[] = [
                    'actions' => [
                        'canBeIgnored' => false,
                        'canBuddyRequests' => false,
                        'canEspionage' => $user_planet->getObjectAmount('espionage_probe') > 0 && $player->getResearchLevel('espionage_technology') > 0,
                        'canMissileAttack' => false,
                        'canPhalanx' => false,
                        'canSendProbes' => $user_planet->getObjectAmount('espionage_probe') > 0 && $player->getResearchLevel('espionage_technology') > 0,
                        'canWrite' => false,
                        'discoveryUnlocked' => 'You haven’t unlocked the research to discover new lifeforms yet.\n',
                        // TODO: Implement this functionality
                        'missileAttackLink' => route('galaxy.index'),
                    ],
                    //
                    'availableMissions' => [
                    ],
                    'galaxy' => $galaxy,
                    'planets' => [
                        [
                            'activity'          => [
                                //'idleTime' => 31,
                                //'showActivity' => 60,
                                //'showMinutes' => false,
                            ],
                            'availableMissions' => [],
                            'fleet'             => [],
                            'imageInformation'  => $planet->getPlanetType() . '_' . $planet->getPlanetImageType(),
                            'isDestroyed'       => false,
                            'planetId'          => $planet->getPlanetId(),
                            'planetName'        => $planet->getPlanetName(),
                            'playerId'          => $row_player?->getId(),
                            'planetType'        => 1,
                        ]
                    ],
                    'player' => [
                        'actions' => [
                            'alliance' => [
                                'available' => false,
                            ],
                            'buddies' => [
                                'available' => false,
                            ],
                            'highscore' => [
                                'available' => false,
                            ],
                            'ignore' => [
                                'available' => false,
                            ],
                            'message' => [
                                'available' => false,
                            ],
                        ],
                        'playerId' => $row_player?->getId(),
                        'playerName' => $row_player?->getUsername(),
                        'nameAbbreviations' => $nameAbbreviations,
                        'isAdmin' => $row_player?->isAdmin(),
                        //'allianceId' => 1,
                        //'allianceName' => 'Test',
                    ],
                    'position' => $i,
                    'positionFilters' => '',
                    'system' => $system
                ];
            } else {
                // Empty deep space
                $galaxy_rows[] = [
                    'actions' => [],
                    'availableMissions' => [
                        [
                            'missionType' => 0,
                            'planetMovePossible' => true,
                            'moveAction' => 'prepareMove',
                            'title' => 'Relocate'
                        ],
                        $user_planet->getObjectAmount('colony_ship') > 0 ? [
                            'missionType' => 7,
                            'link' => "/fleet?galaxy={$galaxy}&system={$system}&position={$i}&type=1&mission=7",
                            'description' => "Coloniser<br>Cette position est normalement occupée par des planètes équilibrées, avec assez de deutérium et suffisamment d`énergie solaire, qui offrent assez de place pour le développement.<br><div style='display: flex;align-items: center;'><img src='/cdn/img/galaxy/activity.gif' style=''/>Sans vaisseau de colonisation, aucune colonisation de planète n`est possible!</div>"
                        ] : null,
                    ],
                    'galaxy' => $galaxy,
                    'planets' => [],
                    'player' => [
                        'playerId' => 99999,
                        'playerName' => 'Deep space'
                    ],
                    'playerId' => 99999,
                    'playerName' => 'Deep space',
                    'position' => $i,
                    'positionFilters' => 'empty_filter',
                    'system' => $system
                ];
            }
        }

        return $galaxy_rows;
    }

    /**
     * Shows the galaxy index page
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajax(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): JsonResponse
    {
        $planet = $player->planets->current();
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');

        return response()->json([
            'components' => [],
            'filterSettings' => [],
            'lifeformEnabled' => false,
            'newAjaxToken' => csrf_token(),
            'reservedPositions' => [],
            'success' => true,
            'system' => [
                'availableMissiles' => 0,
                'availablePathfinders' => 0,
                'availableProbes' => $planet->getObjectAmount('espionage_probe'),
                'availableRecyclers' => $planet->getObjectAmount('recycler'),
                'canColonize' => true,
                'canExpedition' => true,
                'canFly' => true,
                'canSendSystemDiscovery' => true,
                'canSwitchGalaxy' => true,
                'canSystemEspionage' => false,
                'canSystemPhalanx' => false,
                'currentPlanetId' => $planet->getPlanetId(),
                'deuteriumInDebris' => true,
                'galaxy' => $galaxy,
                'system' => $system,
                'galaxyContent' => $this->getGalaxyArray($galaxy, $system, $player, $planetServiceFactory),
                'hasAdmiral' => false,
                'hasBirthdayPlanet' => false,
                'isOutlaw' => false,
                'maximumFleetSlots' => 13,
                'playerId' => $player->getId(),
                'settingsProbeCount' => 3,
                'showOutlawWarning' => true,
                'slotsColonized' => 3,
                'switchGalaxyDeuteriumCosts' => 10,
                'toGalaxyLink' => route('galaxy.index', ['galaxy' => $galaxy, 'system' => $system]),
                'usedFleetSlots' => 1
            ],
        ]);
    }
}
