<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use OGame\Models\Enums\PlanetType;

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
        ]);
    }

    /**
     * Get galaxy table (used for both static and AJAX requests).
     *
     * @param int $galaxy
     * @param int $system
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     *
     * @return array<mixed>
     * @throws Exception
     */
    public function getGalaxyArray(int $galaxy, int $system, PlayerService $player, PlanetServiceFactory $planetServiceFactory): array
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
                $galaxy_rows[] = $this->createPlanetRow($galaxy, $system, $i, $planets[$i]);
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
     * @return array<string, mixed>
     */
    private function createPlanetRow(int $galaxy, int $system, int $position, PlanetService $planet): array
    {
        $availableMissions = $this->getAvailableMissions($galaxy, $system, $position, $planet);
        $planets_array = $this->createPlanetsArray($planet, $availableMissions);
        $actions = $this->getPlanetActions($planet);
        $playerInfo = $this->getPlayerInfo($planet->getPlayer());

        // Add phalanx data to player info (expected by JavaScript)
        if (isset($actions['phalanx'])) {
            $playerInfo['phalanx'] = $actions['phalanx'];
            unset($actions['phalanx']); // Remove from actions as it's now in player
        }

        return [
            'actions' => $actions,
            'availableMissions' => [],
            'galaxy' => $galaxy,
            'planets' => $planets_array,
            'player' => $playerInfo,
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
        $availableMissions = [];

        // Transport.
        $availableMissions[] = [
            'missionType' => 3,
            'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 3]),
            'name' => __('Transport'),
        ];

        if ($planet->getPlayer()->getId() !== $this->playerService->getId()) {
            // Espionage (only if foreign planet).
            $availableMissions[] = [
                'missionType' => 6,
                'canSpy' => true,
                'reportId' => '',
                'reportLink' => '',
                'link' => route('fleet.dispatch.sendfleet', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 6, 'am210' => 1]),
                'name' => __('Espionage'),
            ];

            // Attack (only if foreign planet).
            $availableMissions[] = [
                'missionType' => 1,
                'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => $planet->getPlanetType()->value, 'mission' => 1]),
                'name' => __('Attack'),
            ];

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
     * @return array<string, bool|string>
     */
    private function getPlanetActions(PlanetService $planet): array
    {
        // Check if the current planet of the player has enough espionage probes and research level
        // to be able to spy on the target planet.
        $canEspionage = $this->playerService->planets->current()->getObjectAmount('espionage_probe') > 0 && $this->playerService->getResearchLevel('espionage_technology') > 0;

        // Check if player can launch missile attacks
        $currentPlanet = $this->playerService->planets->current();
        $hasMissiles = $currentPlanet->getObjectAmount('interplanetary_missile') > 0;
        $missileRange = $this->playerService->getMissileRange();

        // Calculate distance to target
        $distance = abs($currentPlanet->getPlanetCoordinates()->system - $planet->getPlanetCoordinates()->system);
        $inRange = ($currentPlanet->getPlanetCoordinates()->galaxy === $planet->getPlanetCoordinates()->galaxy) && ($distance <= $missileRange);

        // Can only attack other players' planets
        $canMissileAttack = $hasMissiles && $inRange && !$planet->getPlayer()->equals($this->playerService);

        // Build missile attack link with all necessary parameters
        $coords = $planet->getPlanetCoordinates();
        $missileAttackLink = route('galaxy.missile-attack', [
            'galaxy' => $coords->galaxy,
            'system' => $coords->system,
            'position' => $coords->position,
            'planetType' => $planet->getPlanetType()->value,
        ]);

        // Check if player can use sensor phalanx on this planet
        $canPhalanx = false;
        $phalanxLink = '';
        $phalanxInactive = false;

        // Can only phalanx planets (not own planets)
        if (!$planet->getPlayer()->equals($this->playerService)) {
            $phalanxMoon = $this->playerService->getMoonWithPhalanxInRange(
                $coords->galaxy,
                $coords->system,
                $coords->position
            );

            if ($phalanxMoon !== null) {
                // We have a phalanx in range
                $canPhalanx = true;

                // Calculate deuterium cost
                $moonCoords = $phalanxMoon->getPlanetCoordinates();
                $deuteriumCost = $this->playerService->calculatePhalanxCost(
                    $moonCoords->galaxy,
                    $moonCoords->system,
                    $coords->galaxy,
                    $coords->system
                );

                // Check if moon has enough deuterium
                if ($phalanxMoon->deuterium()->get() < $deuteriumCost) {
                    $phalanxInactive = true;
                }

                // Build phalanx link with coordinates as data attributes
                // Using javascript:void(0) to prevent navigation
                $phalanxLink = 'javascript:void(0);';
            }
        }

        return [
            'canBeIgnored' => false,
            'canBuddyRequests' => false,
            'canEspionage' => $canEspionage,
            'canMissileAttack' => $canMissileAttack,
            'canPhalanx' => $canPhalanx,
            'canSendProbes' => $canEspionage,
            'canWrite' => false,
            'discoveryUnlocked' => 'You haven\'t unlocked the research to discover new lifeforms yet.\n',
            'missileAttackLink' => $missileAttackLink,
            'phalanx' => [
                'inactive' => $phalanxInactive,
                'link' => $phalanxLink,
                'title' => 'Sensor Phalanx',
                'galaxy' => $coords->galaxy,
                'system' => $coords->system,
                'position' => $coords->position,
            ],
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
        return [
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
            'playerId' => $player->getId(),
            'playerName' => $player->getUsername(),
            'isAdmin' => $player->isAdmin(),
            'isInactive' => $player->isInactive(),
            'isLongInactive' => $player->isLongInactive(),
            'isNewbie' => $player->isNewbie($this->playerService),
            'isStrong' => $player->isStrong($this->playerService),

            // Not implemented yet:
            //'isHonorableTarget' => $player->isHonorableTarget(),
            //'isOutlaw' => $player->isOutlaw(),
            //'isBanned' => $player->isBanned(),
            //'isOnVacation' => $player->isOnVacation(),
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
     * @return JsonResponse
     */
    public function ajax(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): JsonResponse
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        $planet = $player->planets->current();
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');
        $galaxyContent = $this->getGalaxyArray($galaxy, $system, $player, $planetServiceFactory);
        $slotsColonized = $this->calculateColonizedSlots($galaxyContent);

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
                'canSystemPhalanx' => false,
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
     * Show the missile attack overlay.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @return View
     */
    public function missileAttackOverlay(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): View
    {
        $currentPlanet = $player->planets->current();

        // Get request parameters
        $galaxy = $request->input('galaxy');
        $system = $request->input('system');
        $position = $request->input('position');
        $type = $request->input('planetType', PlanetType::Planet->value);

        // Create target coordinate
        $targetCoordinate = new Planet\Coordinate($galaxy, $system, $position);

        // Get target planet
        $targetType = PlanetType::from($type);
        $targetPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);

        // Calculate missile info
        $availableMissiles = $currentPlanet->getObjectAmount('interplanetary_missile');
        $missileRange = $player->getMissileRange();
        $distance = abs($currentPlanet->getPlanetCoordinates()->system - $targetCoordinate->system);

        // Check if attack is possible
        $canAttack = true;
        $errorMessage = '';

        if ($targetPlanet === null) {
            $canAttack = false;
            $errorMessage = 'Target planet does not exist';
        } elseif ($targetPlanet->getPlayer()->equals($player)) {
            $canAttack = false;
            $errorMessage = 'Cannot attack own planet';
        } elseif ($currentPlanet->getPlanetCoordinates()->galaxy !== $targetCoordinate->galaxy) {
            $canAttack = false;
            $errorMessage = 'Missiles cannot cross galaxies';
        } elseif ($distance > $missileRange) {
            $canAttack = false;
            $errorMessage = "Target is out of range (max: {$missileRange} systems)";
        } elseif ($availableMissiles <= 0) {
            $canAttack = false;
            $errorMessage = 'No missiles available';
        }

        return view('ingame.galaxy.missileattack')->with([
            'galaxy' => $galaxy,
            'system' => $system,
            'position' => $position,
            'planetType' => $type,
            'availableMissiles' => $availableMissiles,
            'missileRange' => $missileRange,
            'distance' => $distance,
            'canAttack' => $canAttack,
            'errorMessage' => $errorMessage,
            'targetPlanet' => $targetPlanet,
        ]);
    }

    /**
     * Handle missile attack from galaxy view.
     *
     * @param Request $request
     * @param PlayerService $player
     * @param PlanetServiceFactory $planetServiceFactory
     * @return JsonResponse
     */
    public function missileAttack(Request $request, PlayerService $player, PlanetServiceFactory $planetServiceFactory): JsonResponse
    {
        try {
            $currentPlanet = $player->planets->current();

            // Get request parameters
            $galaxy = $request->input('galaxy');
            $system = $request->input('system');
            $position = $request->input('position');
            $type = $request->input('type', PlanetType::Planet->value);
            $missileCount = (int)$request->input('missiles', 1);

            // Validate missile count
            $availableMissiles = $currentPlanet->getObjectAmount('interplanetary_missile');
            if ($missileCount <= 0 || $missileCount > $availableMissiles) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid missile count',
                ], 400);
            }

            // Create target coordinate
            $targetCoordinate = new Planet\Coordinate($galaxy, $system, $position);

            // Get target planet
            $targetType = PlanetType::from($type);
            $targetPlanet = $planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);

            if ($targetPlanet === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target planet does not exist',
                ], 400);
            }

            // Check if target is own planet
            if ($targetPlanet->getPlayer()->equals($player)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot attack own planet',
                ], 400);
            }

            // Check range
            $missileRange = $player->getMissileRange();
            $distance = abs($currentPlanet->getPlanetCoordinates()->system - $targetCoordinate->system);

            if ($currentPlanet->getPlanetCoordinates()->galaxy !== $targetCoordinate->galaxy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missiles cannot cross galaxies',
                ], 400);
            }

            if ($distance > $missileRange) {
                return response()->json([
                    'success' => false,
                    'message' => "Target is out of range. Maximum range: {$missileRange} systems",
                ], 400);
            }

            // Remove missiles from planet
            $currentPlanet->removeUnit('interplanetary_missile', $missileCount);

            // Create the missile mission
            $settingsService = app(SettingsService::class);

            // Calculate flight time using OGame formula: (30 + 60 * systems) / universe_speed
            $universeSpeed = $settingsService->fleetSpeed();
            $flightTime = (int)ceil((30 + 60 * $distance) / $universeSpeed);

            // Create mission
            $mission = new \OGame\Models\FleetMission();
            $mission->user_id = $player->getId();
            $mission->mission_type = 10; // Missile Attack
            $mission->planet_id_from = $currentPlanet->getPlanetId();
            $mission->planet_id_to = $targetPlanet->getPlanetId();
            $mission->galaxy_from = $currentPlanet->getPlanetCoordinates()->galaxy;
            $mission->system_from = $currentPlanet->getPlanetCoordinates()->system;
            $mission->position_from = $currentPlanet->getPlanetCoordinates()->position;
            $mission->galaxy_to = $targetCoordinate->galaxy;
            $mission->system_to = $targetCoordinate->system;
            $mission->position_to = $targetCoordinate->position;
            $mission->type_from = $currentPlanet->getPlanetType()->value;
            $mission->type_to = $targetType->value;
            $mission->time_departure = (int)Carbon::now()->timestamp;
            $mission->time_arrival = (int)Carbon::now()->addSeconds($flightTime)->timestamp;
            // Store missile count in metal field (no dedicated column exists)
            $mission->metal = $missileCount;
            $mission->parent_id = null; // No parent mission for missile attacks
            $mission->canceled = 0;
            $mission->processed = 0;
            $mission->save();

            return response()->json([
                'success' => true,
                'message' => "{$missileCount} missile(s) launched successfully",
                'arrival_time' => $mission->time_arrival,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Perform a sensor phalanx scan on target coordinates.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     */
    public function ajaxPhalanxScan(Request $request, PlayerService $player): JsonResponse
    {
        try {
            // Validate input
            $galaxy = (int)$request->input('galaxy', 0);
            $system = (int)$request->input('system', 0);
            $position = (int)$request->input('position', 0);

            if ($galaxy < 1 || $system < 1 || $position < 1 || $position > 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coordinates provided',
                ]);
            }

            // Check if player has a moon with phalanx in range
            $moon = $player->getMoonWithPhalanxInRange($galaxy, $system, $position);
            if ($moon === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sensor phalanx in range of target coordinates',
                ]);
            }

            // Calculate deuterium cost
            $moonCoords = $moon->getPlanetCoordinates();
            $deuteriumCost = $player->calculatePhalanxCost(
                $moonCoords->galaxy,
                $moonCoords->system,
                $galaxy,
                $system
            );

            // Check if moon has enough deuterium
            if ($moon->deuterium()->get() < $deuteriumCost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough deuterium to deploy phalanx',
                ]);
            }

            // Deduct deuterium
            $moon->deductResources(new Resources(0, 0, $deuteriumCost, 0));

            // Find all fleet missions to or from the target coordinates
            $fleets = FleetMission::where(function ($query) use ($galaxy, $system, $position) {
                $query->where([
                    'galaxy_to' => $galaxy,
                    'system_to' => $system,
                    'position_to' => $position,
                ])->orWhere([
                    'galaxy_from' => $galaxy,
                    'system_from' => $system,
                    'position_from' => $position,
                ]);
            })
                ->where('processed', 0)
                ->where('canceled', 0)
                ->orderBy('time_arrival', 'asc')
                ->get();

            // Format fleet information for response
            $fleetData = [];
            foreach ($fleets as $fleet) {
                // Determine if fleet is incoming or outgoing
                $isIncoming = ($fleet->galaxy_to == $galaxy && $fleet->system_to == $system && $fleet->position_to == $position);

                $fleetData[] = [
                    'mission_type' => $fleet->mission_type,
                    'direction' => $isIncoming ? 'incoming' : 'outgoing',
                    'fleet_id' => $fleet->id,
                    'time_arrival' => $fleet->time_arrival,
                    'time_departure' => $fleet->time_departure,
                    'origin' => [
                        'galaxy' => $fleet->galaxy_from,
                        'system' => $fleet->system_from,
                        'position' => $fleet->position_from,
                    ],
                    'destination' => [
                        'galaxy' => $fleet->galaxy_to,
                        'system' => $fleet->system_to,
                        'position' => $fleet->position_to,
                    ],
                    'ships' => [
                        'small_cargo' => $fleet->small_cargo,
                        'large_cargo' => $fleet->large_cargo,
                        'light_fighter' => $fleet->light_fighter,
                        'heavy_fighter' => $fleet->heavy_fighter,
                        'cruiser' => $fleet->cruiser,
                        'battle_ship' => $fleet->battle_ship,
                        'battlecruiser' => $fleet->battlecruiser,
                        'bomber' => $fleet->bomber,
                        'destroyer' => $fleet->destroyer,
                        'deathstar' => $fleet->deathstar,
                        'colony_ship' => $fleet->colony_ship,
                        'recycler' => $fleet->recycler,
                        'espionage_probe' => $fleet->espionage_probe,
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Phalanx scan completed',
                'fleets' => $fleetData,
                'deuterium_cost' => $deuteriumCost,
                'scanned_coordinates' => [
                    'galaxy' => $galaxy,
                    'system' => $system,
                    'position' => $position,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
