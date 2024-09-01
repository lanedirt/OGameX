<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Services\DebrisFieldService;
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
     *
     * @phpstan-ignore-next-line
     * @return mixed
     * @throws Exception
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

                $availableMissions = [];

                // Transport
                $availableMissions[] = [
                    'missionType' => 3,
                    'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $i, 'type' => 1, 'mission' => 3]),
                    'name' => __('Transport'),
                ];
                if ($planet->getPlayer()->getId() !== $player->getId()) {
                    // Espionage (only if foreign planet)
                    $availableMissions[] = [
                        'missionType' => 6,
                        'canSpy' => true,
                        'reportId' => '',
                        'reportLink' => '',
                        'link' => route('fleet.dispatch.sendfleet', ['galaxy' => $galaxy, 'system' => $system, 'position' => $i, 'type' => 1, 'mission' => 6, 'am210' => 1]),
                        'name' => __('Espionage'),
                    ];

                    // Attack (only if foreign planet)
                    $availableMissions[] = [
                        'missionType' => 1,
                        'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $i, 'type' => 1, 'mission' => 1]),
                        'name' => __('Attack'),
                    ];
                } else {
                    // Deployment (only if own planet)
                    $availableMissions[] = [
                        'missionType' => 4,
                        'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $i, 'type' => 1, 'mission' => 4]),
                        'name' => __('Deployment'),
                    ];
                }

                // TODO: refactor this to a separate method.
                // The planets array (can) consist of:
                // - The actual planet (if any)
                // - The moon (?)
                // - The debris field (if any)
                $planets_array = [];

                // Add the actual planet
                $planets_array[] = [
                    'activity'          => [
                        //'idleTime' => 31,
                        //'showActivity' => 60,
                        //'showMinutes' => false,
                    ],
                    'availableMissions' => $availableMissions,
                    'fleet'             => [],
                    'imageInformation'  => $planet->getPlanetType() . '_' . $planet->getPlanetImageType(),
                    'isDestroyed'       => false,
                    'planetId'          => $planet->getPlanetId(),
                    'planetName'        => $planet->getPlanetName(),
                    'playerId'          => $row_player?->getId(),
                    'planetType'        => 1,
                ];

                // If debris field exists, add it.
                $debrisFieldService = resolve(DebrisFieldService::class);
                $debrisFieldService->loadByCoordinates($planet->getPlanetCoordinates());

                $debrisResources = $debrisFieldService->getResources();
                if ($debrisResources->any()) {
                    $planets_array[] = [
                        'planetId' => 0,
                        'planetName' => 'debris_field',
                        'imageInformation' => 'debris_1',
                        'availableMissions' => [
                            [
                                'missionType' => 8,
                                'name' => 'Harvest',
                            ],
                        ],
                        'requiredShips' => 99,
                        'planetType' => 2,
                        'resources' => [
                            'metal' => [
                                'name' => 'Metal',
                                'amount' => $debrisResources->metal->get(),
                            ],
                            'crystal' => [
                                'name' => 'Crystal',
                                'amount' => $debrisResources->crystal->get(),
                            ],
                            'deuterium' => [
                                'name' => 'Deuterium',
                                'amount' => $debrisResources->deuterium->get(),
                            ],
                        ],
                        'recyclePossible' => true,
                    ];
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
                    'availableMissions' => [],
                    'galaxy' => $galaxy,
                    'planets' => $planets_array,
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
                    'system' => $system,
                ];
            } else {
                $planet_description = $planetServiceFactory->getPlanetDescription(new Planet\Coordinate($galaxy, $system, $i));
                $has_colonize_ship = $user_planet->getObjectAmount('colony_ship') > 0;
                $colonize_ship_message = "<br><div><img src='/img/galaxy/activity.gif' />" . __('t_galaxy.mission.colonize.no_ship') . "</div>";

                $missions_available = [
                    [
                       'missionType' => 0,
                       'planetMovePossible' => true,
                       'moveAction' => 'prepareMove',
                       'title' => 'Relocate'
                   ]
                ];

                $missions_available[] = [
                    'missionType' => 7,
                    'link'        => $user_planet->getObjectAmount('colony_ship') > 0 ? "/fleet?galaxy={$galaxy}&system={$system}&position={$i}&type=1&mission=7" : '#',
                    'description' => __('t_galaxy.mission.colonize.name')."<br>{$planet_description}" . (!$has_colonize_ship ? $colonize_ship_message : '')
                ];


                // Empty deep space
                $galaxy_rows[] = [
                    'actions' => [],
                    'availableMissions' => $missions_available,
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
