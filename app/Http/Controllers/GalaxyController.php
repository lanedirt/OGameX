<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\BuddyService;
use OGame\Services\DebrisFieldService;
use OGame\Services\PhalanxService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class GalaxyController extends OGameController
{
    /**
     * @var PlayerService
     */
    private PlayerService $playerService;

    /**
     * @var PlanetServiceFactory
     */
    private PlanetServiceFactory $planetServiceFactory;

    /**
     * @var BuddyService
     */
    private BuddyService $buddyService;

    /**
     * Shows the galaxy index page.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View
     */
    public function index(Request $request, PlayerService $player, SettingsService $settingsService, PlanetServiceFactory $planetServiceFactory): View
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

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
            'recycler_count' => $planet->getObjectAmount('recycler'),
            'interplanetary_missiles_count' => $planet->getObjectAmount('interplanetary_missile'),
            'used_slots' => 0,
            'max_slots' => 1,
            'max_galaxies' => $settingsService->numberOfGalaxies(),
            'is_in_vacation_mode' => $player->isInVacationMode(),
        ]);
    }

    /**
     * Get galaxy table (used for both static and AJAX requests).
     *
     * @param int $galaxy
     * @param int $system
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PhalanxService $phalanxService
     *
     * @return array<mixed>
     * @throws Exception
     */
    public function getGalaxyArray(int $galaxy, int $system, PlayerService $player, PlanetServiceFactory $planetServiceFactory, PhalanxService $phalanxService): array
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        // Retrieve all planets from this galaxy and system.
        $planet_list = Planet::where([
            'galaxy' => $galaxy,
            'system' => $system,
            'planet_type' => PlanetType::Planet->value
        ])->get();

        $planets = [];
        foreach ($planet_list as $planet) {
            $planetService = $planetServiceFactory->makeFromModel($planet);
            $planets[$planet->planet] = $planetService;
        }

        // Render galaxy rows.
        $galaxy_rows = [];
        for ($i = 1; $i <= 15; $i++) {
            if (!empty($planets[$i])) {
                $galaxy_rows[] = $this->createPlanetRow($galaxy, $system, $i, $planets[$i], $phalanxService);
            } else {
                $galaxy_rows[] = $this->createEmptySpaceRow($galaxy, $system, $i);
            }
        }

        return $galaxy_rows;
    }

    /**
     * Creates a row for a planet in the galaxy view.
     *
     * @param int $galaxy
     * @param int $system
     * @param int $position
     * @param PlanetService $planet
     * @param PhalanxService $phalanxService
     * @return array<string, mixed>
     */
    private function createPlanetRow(int $galaxy, int $system, int $position, PlanetService $planet, PhalanxService $phalanxService): array
    {
        $availableMissions = $this->getAvailableMissions($galaxy, $system, $position, $planet);
        $planets_array = $this->createPlanetsArray($planet, $availableMissions);

        return [
            'actions' => $this->getPlanetActions($planet, $galaxy, $system, $position, $phalanxService),
            'availableMissions' => [],
            'galaxy' => $galaxy,
            'planets' => $planets_array,
            'player' => $this->getPlayerInfo($planet->getPlayer()),
            'position' => $position,
            'positionFilters' => '',
            'system' => $system,
        ];
    }

    /**
     * Creates an array of planets for the galaxy view.
     *
     * @param PlanetService $planet
     * @param array<int, array<string, string>> $availableMissions
     * @return array<int, mixed>
     */
    private function createPlanetsArray(PlanetService $planet, array $availableMissions): array
    {
        $planets_array = [
            [
                'activity' => $this->getPlanetActivityStatus($planet),
                'availableMissions' => $availableMissions,
                'fleet' => [],
                'imageInformation' => $planet->getPlanetBiomeType() . '_' . $planet->getPlanetImageType(),
                'isDestroyed' => false,
                'planetId' => $planet->getPlanetId(),
                'planetName' => $planet->getPlanetName(),
                'playerId' => $planet->getPlayer()?->getId(),
                'planetType' => 1,
            ]
        ];

        $debrisField = app(DebrisFieldService::class);
        $debrisFieldExists = $debrisField->loadForCoordinates($planet->getPlanetCoordinates());
        if ($debrisFieldExists && $debrisField->getResources()->any()) {
            $planets_array[] = $this->createDebrisFieldArray($debrisField);
        }

        if ($planet->hasMoon()) {
            $planets_array[] = $this->createMoonArray($planet->moon());
        }

        return $planets_array;
    }

    /**
     * Creates an array for a debris field in the galaxy view.
     *
     * @param DebrisFieldService $debrisField
     * @return array<string, mixed>
     */
    private function createDebrisFieldArray(DebrisFieldService $debrisField): array
    {
        $debrisResources = $debrisField->getResources();

        return [
            'planetId' => 0,
            'planetName' => 'debris_field',
            'imageInformation' => 'debris_1',
            'availableMissions' => [
                [
                    'missionType' => 8,
                    'name' => 'Harvest',
                ],
            ],
            'requiredShips' => $debrisField->calculateRequiredRecyclers(),
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

    /**
     * Creates an array for a moon in the galaxy view.
     *
     * @param PlanetService $moon
     * @return array<string, mixed>
     */
    private function createMoonArray(PlanetService $moon): array
    {
        $availableMissions = $this->getAvailableMissions(
            $moon->getPlanetCoordinates()->galaxy,
            $moon->getPlanetCoordinates()->system,
            $moon->getPlanetCoordinates()->position,
            $moon
        );

        return [
            'activity' => $this->getPlanetActivityStatus($moon),
            'availableMissions' => $availableMissions,
            'fleet' => [],
            // TODO: moon_c appears as red (recently destroyed?)
            'imageInformation' => 'moon_a',
            'isDestroyed' => false,
            'planetId' => $moon->getPlanetId(),
            'planetName' => $moon->getPlanetName(),
            'playerId' => $moon->getPlayer()->getId(),
            'planetType' => 3,
            'size' => $moon->getPlanetDiameter(),
            'tooltipInfo' => [
                'diameter' => $moon->getPlanetDiameter(),
                'name' => $moon->getPlanetName(),
            ],
        ];
    }

    /**
     * Gets available missions for a planet
     *
     * @param int $galaxy
     * @param int $system
     * @param int $position
     * @param PlanetService $planet
     * @return array<int, mixed>
     */
    private function getAvailableMissions(int $galaxy, int $system, int $position, PlanetService $planet): array
    {
        $this->buddyService = app(BuddyService::class);
        $availableMissions = [];

        // Transport.
        $availableMissions[] = [
            'missionType' => 3,
            'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 3]),
            'name' => __('Transport'),
        ];

        if ($planet->getPlayer()->getId() !== $this->playerService->getId()) {
            // Skip aggressive missions (Espionage, Attack) against Legor
            $isLegor = $planet->getPlayer()->getUsername(false) === 'Legor';

            if (!$isLegor) {
                // Espionage (only if foreign planet and not Legor).
                $availableMissions[] = [
                    'missionType' => 6,
                    'canSpy' => true,
                    'reportId' => '',
                    'reportLink' => '',
                    'link' => route('fleet.dispatch.sendfleet', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 6, 'am210' => 1]),
                    'name' => __('Espionage'),
                ];

                // Attack (only if foreign planet and not Legor).
                $availableMissions[] = [
                    'missionType' => 1,
                    'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 1]),
                    'name' => __('Attack'),
                ];
            }

            // Check if target player is a buddy or ally member
            $currentUserId = $this->playerService->getUser()->id;
            $targetUserId = $planet->getPlayer()->getUser()->id;

            $isBuddy = $this->buddyService->areBuddies($currentUserId, $targetUserId);

            // ACS Defend (only if target is buddy or ally member).
            if ($isBuddy) {
                $availableMissions[] = [
                    'missionType' => 5,
                    'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 5]),
                    'name' => __('ACS Defend'),
                ];
            }

            // Moon destruction (only if planet is a moon).
            if ($planet->isMoon()) {
                $availableMissions[] = [
                    'missionType' => 10,
                    'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 10]),
                    'name' => __('Moon destruction'),
                ];
            }
        } else {
            // Deployment (only if own planet).
            $availableMissions[] = [
                'missionType' => 4,
                'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 4]),
                'name' => __('Deployment'),
            ];
        }

        return $availableMissions;
    }

    /**
     * Gets available actions for a planet.
     *
     * @param PlanetService $planet
     * @param int $galaxy
     * @param int $system
     * @param int $position
     * @param PhalanxService $phalanxService
     * @return array<string, bool|string>
     */
    private function getPlanetActions(PlanetService $planet, int $galaxy, int $system, int $position, PhalanxService $phalanxService): array
    {
        // Check if the current planet of the player has enough espionage probes and research level
        // to be able to spy on the target planet.
        $canEspionage = $this->playerService->planets->current()->getObjectAmount('espionage_probe') > 0 && $this->playerService->getResearchLevel('espionage_technology') > 0;

        // Check if current planet is a moon with sensor phalanx and target is in range
        // Note: Moons cannot be scanned (OGame rule)
        // Note: Cannot scan own planets
        // Note: Admin planets cannot be scanned
        $can_phalanx = false;
        $phalanx_inactive_reason = '';
        $current_planet = $this->playerService->planets->current();

        // Check if phalanx can be used: must be on moon, target must be planet, not own planet, not admin
        if ($current_planet->isMoon()
            && !$planet->isMoon()
            && $planet->getPlayer()->getId() !== $this->playerService->getId()
            && !$planet->getPlayer()->isAdmin()) {
            // All basic checks passed, now check phalanx level and range
            $phalanx_level = $current_planet->getObjectLevel('sensor_phalanx');
            if ($phalanx_level > 0) {
                $target_coordinate = new Coordinate($galaxy, $system, $position);
                $moon_coordinates = $current_planet->getPlanetCoordinates();
                $in_range = $phalanxService->canScanTarget(
                    $moon_coordinates->galaxy,
                    $moon_coordinates->system,
                    $phalanx_level,
                    $target_coordinate
                );

                if ($in_range) {
                    // Check deuterium
                    $has_deuterium = $phalanxService->hasEnoughDeuterium($current_planet->deuterium()->get());
                    if ($has_deuterium) {
                        $can_phalanx = true;
                    } else {
                        $phalanx_inactive_reason = 'Not enough deuterium to use phalanx';
                    }
                }
            }
        }

        // Check if buddy request can be sent:
        // - Must be foreign planet (not own)
        // - Target player must not be admin (can't send requests to admins)
        $canBuddyRequest = $planet->getPlayer()->getId() !== $this->playerService->getId()
            && !$planet->getPlayer()->isAdmin();

        // Check if missile attack is possible:
        // - Must be foreign planet (not own)
        // - Must have missiles available
        // - Target must be within range
        $canMissileAttack = false;
        $missileAttackLink = route('galaxy.index');

        if ($planet->getPlayer()->getId() !== $this->playerService->getId()) {
            $currentPlanet = $this->playerService->planets->current();
            $availableMissiles = $currentPlanet->getObjectAmount('interplanetary_missile');

            if ($availableMissiles > 0) {
                $missileRange = $this->playerService->getMissileRange();
                $targetCoordinate = new Coordinate($galaxy, $system, $position);
                $distance = $this->calculateSystemDistance($currentPlanet->getPlanetCoordinates(), $targetCoordinate);

                if ($distance <= $missileRange) {
                    $canMissileAttack = true;
                    $missileAttackLink = route('galaxy.missile-attack.overlay', [
                        'galaxy' => $galaxy,
                        'system' => $system,
                        'position' => $position,
                        'type' => $planet->getPlanetType()->value,
                    ]);
                }
            }
        }

        return [
            'canBeIgnored' => false,
            'canBuddyRequests' => $canBuddyRequest,
            'canEspionage' => $canEspionage,
            'canMissileAttack' => $canMissileAttack,
            'canPhalanx' => $can_phalanx || !empty($phalanx_inactive_reason),
            'phalanxActive' => $can_phalanx,
            'phalanxInactive' => !empty($phalanx_inactive_reason),
            'phalanxInactiveReason' => $phalanx_inactive_reason,
            'canSendProbes' => $canEspionage,
            'canWrite' => false,
            'discoveryUnlocked' => 'You haven\'t unlocked the research to discover new lifeforms yet.\n',
            'missileAttackLink' => $missileAttackLink,
        ];
    }

    /**
     * Gets player information for the galaxy view.
     *
     * @param PlayerService $player
     * @return array<string, mixed>
     */
    private function getPlayerInfo(PlayerService $player): array
    {
        // Check if this is a foreign player (not self)
        $isForeignPlayer = $player->getId() !== $this->playerService->getId();

        // Check if target player is admin (cannot send buddy requests or ignore admins)
        $isTargetAdmin = $player->isAdmin();

        return [
            'actions' => [
                'alliance' => [
                    'available' => false,
                ],
                'buddies' => [
                    'available' => $isForeignPlayer && !$isTargetAdmin,
                    'playerId' => $player->getId(),
                    'link' => 'javascript:void(0);',
                    'title' => 'Buddy request to player',
                    'playerName' => $player->getUsername(),
                ],
                'ignore' => [
                    'available' => $isForeignPlayer && !$isTargetAdmin,
                    'playerId' => $player->getId(),
                    'link' => 'javascript:void(0);',
                    'title' => 'Ignore player',
                    'playerName' => $player->getUsername(),
                ],
                'support' => [
                    'available' => $isTargetAdmin,
                    'playerId' => $player->getId(),
                    'link' => 'javascript:void(0);', // TODO: Implement proper support contact link when messaging system is ready
                    'title' => 'Contact support',
                    'playerName' => $player->getUsername(),
                ],
                'highscore' => [
                    'available' => false,
                ],
                'message' => [
                    'available' => false,
                ],
            ],
            'playerId' => $player->getId(),
            'playerName' => $player->getUsername(),
            'isAdmin' => $player->isAdmin(),
            'isInactive' => $player->isInactive(),
            'isLongInactive' => $player->isLongInactive(),
            'isNewbie' => $player->isNewbie($this->playerService),
            'isStrong' => $player->isStrong($this->playerService),
            'isOnVacation' => $player->isInVacationMode(),

            // Not implemented yet:
            //'isHonorableTarget' => $player->isHonorableTarget(),
            //'isOutlaw' => $player->isOutlaw(),
            //'isBanned' => $player->isBanned(),
        ];
    }

    /**
     * Creates a row for an empty space in the galaxy view.
     *
     * @param int $galaxy
     * @param int $system
     * @param int $position
     * @return array<string, mixed>
     */
    private function createEmptySpaceRow(int $galaxy, int $system, int $position): array
    {
        $planet_description = $this->planetServiceFactory->getPlanetDescription(new Planet\Coordinate($galaxy, $system, $position));
        $has_colonize_ship = $this->playerService->planets->current()->getObjectAmount('colony_ship') > 0;
        $colonize_ship_message = "<br><div><img src='/img/galaxy/activity.gif' />" . __('t_galaxy.mission.colonize.no_ship') . "</div>";

        $missions_available = [
            [
                'missionType' => 0,
                'planetMovePossible' => true,
                'moveAction' => 'prepareMove',
                'title' => 'Relocate'
            ],
            [
                'missionType' => 7,
                'link' => $has_colonize_ship ? "/fleet?galaxy={$galaxy}&system={$system}&position={$position}&type=1&mission=7" : '#',
                'description' => __('t_galaxy.mission.colonize.name') . "<br>{$planet_description}" . (!$has_colonize_ship ? $colonize_ship_message : '')
            ]
        ];

        return [
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
            'position' => $position,
            'positionFilters' => 'empty_filter',
            'system' => $system
        ];
    }

    /**
     * Handles AJAX requests for the galaxy view.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @param PhalanxService $phalanxService
     * @return JsonResponse
     */
    public function ajax(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory, PhalanxService $phalanxService): JsonResponse
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        if ($player->isInVacationMode()) {
            return response()->json([
                'success' => false,
                'error' => __('You cannot use the galaxy view whilst in vacation mode!'),
            ], 403);
        }

        $planet = $player->planets->current();
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');
        $galaxyContent = $this->getGalaxyArray($galaxy, $system, $player, $planetServiceFactory, $phalanxService);
        $slotsColonized = $this->calculateColonizedSlots($galaxyContent);

        // Check if current planet is a moon with sensor phalanx
        $can_system_phalanx = false;
        if ($planet->isMoon()) {
            $phalanx_level = $planet->getObjectLevel('sensor_phalanx');
            $can_system_phalanx = $phalanx_level > 0;
        }

        return response()->json([
            'components' => [],
            'filterSettings' => [],
            'lifeformEnabled' => false,
            'newAjaxToken' => csrf_token(),
            'reservedPositions' => [],
            'success' => true,
            'system' => [
                'availableMissiles' => $planet->getObjectAmount('interplanetary_missile'),
                'availablePathfinders' => 0,
                'availableProbes' => $planet->getObjectAmount('espionage_probe'),
                'availableRecyclers' => $planet->getObjectAmount('recycler'),
                'canColonize' => true,
                'canExpedition' => true,
                'canFly' => true,
                'canSendSystemDiscovery' => true,
                'canSwitchGalaxy' => true,
                'canSystemEspionage' => false,
                'canSystemPhalanx' => $can_system_phalanx,
                'currentPlanetId' => $planet->getPlanetId(),
                'deuteriumInDebris' => true,
                'galaxy' => $galaxy,
                'system' => $system,
                'galaxyContent' => $galaxyContent,
                'hasAdmiral' => false,
                'hasBirthdayPlanet' => false,
                'isOutlaw' => false,
                'maximumFleetSlots' => 13,
                'playerId' => $player->getId(),
                'settingsProbeCount' => 3,
                'showOutlawWarning' => true,
                'slotsColonized' => $slotsColonized,
                'switchGalaxyDeuteriumCosts' => 10,
                'toGalaxyLink' => route('galaxy.index', ['galaxy' => $galaxy, 'system' => $system]),
                'usedFleetSlots' => 1
            ],
        ]);
    }

    /**
     * Get the activity status of the planet based on the last update time.
     *
     * @param PlanetService $planet
     * @return array{
     *     idleTime: int|null,
     *     showActivity: int|bool,
     *     showMinutes: bool
     * }
     */
    private function getPlanetActivityStatus(PlanetService $planet): array
    {
        $lastActivity = $planet->getMinutesSinceLastUpdate();

        $result = [
            'showMinutes' => true, // TODO need to use the player option (Detailed activity display)
        ];

        if ($lastActivity > 60) {
            $result['idleTime'] = null;
            $result['showActivity'] = false;
        } elseif ($lastActivity >= 15) {
            $result['idleTime'] = $lastActivity;
            $result['showActivity'] = 60;
        } else {
            $result['idleTime'] = null;
            $result['showActivity'] = 15;
        }

        return $result;
    }

    /**
     * Calculate the number of colonized slots in the galaxy.
     *
     * @param array<int, array<string, mixed>> $galaxyContent
     * @return int
     */
    private function calculateColonizedSlots(array $galaxyContent): int
    {
        $slotsColonized = 0;

        foreach ($galaxyContent as $record) {
            if (!empty($record['planets'])) {
                $activePlanets = array_filter($record['planets'], function ($planet) {
                    return empty($planet['isDestroyed']) && !empty($planet['playerId']);
                });
                $slotsColonized += count($activePlanets);
            }
        }

        return $slotsColonized;
    }

    /**
     * Shows the missile attack overlay.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View
     */
    public function missileAttackOverlay(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): View
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        // Get target coordinates from request
        $galaxy = (int)$request->input('galaxy');
        $system = (int)$request->input('system');
        $position = (int)$request->input('position');
        $type = (int)$request->input('type', PlanetType::Planet->value);

        $data = [
            'galaxy' => $galaxy,
            'system' => $system,
            'position' => $position,
            'type' => $type,
            'target_coords' => "$galaxy:$system:$position",
            'target_planet_name' => '',
            'target_player_name' => '',
            'available_missiles' => 0,
            'missile_range' => 0,
            'target_abm_count' => 0,
            'error' => null,
        ];

        // Get current planet and missile info
        $currentPlanet = $player->planets->current();
        $data['available_missiles'] = $currentPlanet->getObjectAmount('interplanetary_missile');
        $data['missile_range'] = $player->getMissileRange();

        // Validate basic requirements
        if ($data['available_missiles'] <= 0) {
            $data['error'] = __('No missiles available');
            return view('ingame.galaxy.missileattack', $data);
        }

        // Load target planet
        $targetCoordinate = new Coordinate($galaxy, $system, $position);
        $targetPlanetType = PlanetType::from($type);
        $targetPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetPlanetType);

        if ($targetPlanet === null) {
            $data['error'] = __('Target planet does not exist');
            return view('ingame.galaxy.missileattack', $data);
        }

        // Check if target is own planet
        if ($currentPlanet->getPlayer()->equals($targetPlanet->getPlayer())) {
            $data['error'] = __('You cannot attack your own planet');
            return view('ingame.galaxy.missileattack', $data);
        }

        // Check if target is within range
        $distance = $this->calculateSystemDistance($currentPlanet->getPlanetCoordinates(), $targetCoordinate);
        if ($distance > $data['missile_range']) {
            $data['error'] = __('Target is out of missile range');
            return view('ingame.galaxy.missileattack', $data);
        }

        // Get target info
        $data['target_planet_name'] = $targetPlanet->getPlanetName();
        $data['target_player_name'] = $targetPlanet->getPlayer()->getUsername();

        // Get ABM count for warning
        $targetAbmCount = $targetPlanet->getObjectAmount('anti_ballistic_missile');

        // If target is a moon, also count parent planet's ABMs
        if ($targetPlanet->isMoon()) {
            $parentPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinate, true, PlanetType::Planet);
            if ($parentPlanet !== null) {
                $targetAbmCount += $parentPlanet->getObjectAmount('anti_ballistic_missile');
            }
        }

        $data['target_abm_count'] = $targetAbmCount;

        // Calculate flight time: (30 + 60 × distance) / universe_speed seconds
        $universeSpeed = 1; // TODO: Get from settings
        $flightTime = (int)((30 + 60 * $distance) / $universeSpeed);
        $arrivalTime = time() + $flightTime;

        $data['flight_duration'] = $flightTime;
        $data['flight_duration_formatted'] = \OGame\Facades\AppUtil::formatTimeDuration($flightTime);
        $data['arrival_time'] = date('d.m.y H:i:s', $arrivalTime);

        return view('ingame.galaxy.missileattack', $data);
    }

    /**
     * Handles missile attack submission.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @return JsonResponse
     */
    public function missileAttack(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): JsonResponse
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        // Validate input
        $validated = $request->validate([
            'galaxy' => 'required|integer|min:1',
            'system' => 'required|integer|min:1',
            'position' => 'required|integer|min:1|max:15',
            'type' => 'required|integer',
            'missile_count' => 'required|integer|min:1',
            'target_priority' => 'required|integer|min:0|max:7',
        ]);

        $galaxy = $validated['galaxy'];
        $system = $validated['system'];
        $position = $validated['position'];
        $type = $validated['type'];
        $missileCount = $validated['missile_count'];
        $targetPriority = $validated['target_priority'];

        // Get current planet
        $currentPlanet = $player->planets->current();

        // Check if player has enough missiles
        $availableMissiles = $currentPlanet->getObjectAmount('interplanetary_missile');
        if ($missileCount > $availableMissiles) {
            return response()->json([
                'success' => false,
                'error' => __('Not enough missiles available'),
            ], 400);
        }

        // Load target planet
        $targetCoordinate = new Coordinate($galaxy, $system, $position);
        $targetPlanetType = PlanetType::from($type);
        $targetPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetPlanetType);

        if ($targetPlanet === null) {
            return response()->json([
                'success' => false,
                'error' => __('Target planet does not exist'),
            ], 400);
        }

        // Check if target is own planet
        if ($currentPlanet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return response()->json([
                'success' => false,
                'error' => __('You cannot attack your own planet'),
            ], 403);
        }

        // Check range
        $missileRange = $player->getMissileRange();
        $distance = $this->calculateSystemDistance($currentPlanet->getPlanetCoordinates(), $targetCoordinate);
        if ($distance > $missileRange) {
            return response()->json([
                'success' => false,
                'error' => __('Target is out of missile range'),
            ], 400);
        }

        // Calculate flight time: (30 + 60 × distance) / universe_speed seconds
        $universeSpeed = 1; // TODO: Get from settings
        $flightTime = (int)((30 + 60 * $distance) / $universeSpeed);

        // Create fleet mission
        $mission = new \OGame\Models\FleetMission();
        $mission->user_id = $player->getId();
        $mission->planet_id_from = $currentPlanet->getPlanetId();
        $mission->planet_id_to = $targetPlanet->getPlanetId();
        $mission->galaxy_from = $currentPlanet->getPlanetCoordinates()->galaxy;
        $mission->system_from = $currentPlanet->getPlanetCoordinates()->system;
        $mission->position_from = $currentPlanet->getPlanetCoordinates()->position;
        $mission->galaxy_to = $galaxy;
        $mission->system_to = $system;
        $mission->position_to = $position;
        $mission->type_from = $currentPlanet->getPlanetType()->value;
        $mission->type_to = $type;
        $mission->mission_type = 10; // Missile attack mission
        $mission->time_departure = time();
        $mission->time_arrival = time() + $flightTime;
        $mission->canceled = 0;
        $mission->processed = 0;

        // Store missile count and priority in dedicated columns
        $mission->interplanetary_missile = $missileCount;
        $mission->target_priority = $targetPriority;

        // Save mission
        $mission->save();

        // Remove missiles from planet
        $currentPlanet->removeUnit('interplanetary_missile', $missileCount);
        $currentPlanet->save();

        \Log::info('Missile Attack Launched', [
            'player_id' => $player->getId(),
            'mission_id' => $mission->id,
            'from' => $currentPlanet->getPlanetCoordinates()->asString(),
            'to' => "$galaxy:$system:$position",
            'missile_count' => $missileCount,
            'target_priority' => $targetPriority,
            'flight_time' => $flightTime,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Missiles launched successfully!'),
            'mission_id' => $mission->id,
            'arrival_time' => $mission->time_arrival,
        ]);
    }

    /**
     * Calculate distance in systems between two coordinates.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int
     */
    private function calculateSystemDistance(Coordinate $from, Coordinate $to): int
    {
        // In the same galaxy and system = 0 distance
        if ($from->galaxy === $to->galaxy && $from->system === $to->system) {
            return 0;
        }

        // Different galaxy = not allowed (missiles can't cross galaxies)
        if ($from->galaxy !== $to->galaxy) {
            return PHP_INT_MAX; // Return very large number to make it out of range
        }

        // Same galaxy, different system
        return abs($from->system - $to->system);
    }
}
