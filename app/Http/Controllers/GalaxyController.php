<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet;
use OGame\Services\DebrisFieldService;
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
            'recycler_count' => 0,
            'interplanetary_missiles_count' => 0,
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
     * @phpstan-ignore-next-line
     * @return mixed
     * @throws Exception
     */
    public function getGalaxyArray(int $galaxy, int $system, PlayerService $player, PlanetServiceFactory $planetServiceFactory): array
    {
        $this->playerService = $player;
        $this->planetServiceFactory = $planetServiceFactory;

        // Retrieve all planets from this galaxy and system.
        $planet_list = Planet::where(['galaxy' => $galaxy, 'system' => $system])->get();
        $planets = [];
        foreach ($planet_list as $record) {
            $planetService = $planetServiceFactory->make($record->id);
            $planets[$record->planet] = $planetService;
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

        return [
            'actions' => $this->getPlanetActions($planet),
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
                'imageInformation' => $planet->getPlanetType() . '_' . $planet->getPlanetImageType(),
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
            'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => 1, 'mission' => 3]),
            'name' => __('Transport'),
        ];

        if ($planet->getPlayer()->getId() !== $this->playerService->getId()) {
            // Espionage (only if foreign planet).
            $availableMissions[] = [
                'missionType' => 6,
                'canSpy' => true,
                'reportId' => '',
                'reportLink' => '',
                'link' => route('fleet.dispatch.sendfleet', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => 1, 'mission' => 6, 'am210' => 1]),
                'name' => __('Espionage'),
            ];

            // Attack (only if foreign planet).
            $availableMissions[] = [
                'missionType' => 1,
                'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => 1, 'mission' => 1]),
                'name' => __('Attack'),
            ];
        } else {
            // Deployment (only if own planet).
            $availableMissions[] = [
                'missionType' => 4,
                'link' => route('fleet.index', ['galaxy' => $galaxy, 'system' => $system, 'position' => $position, 'type' => 1, 'mission' => 4]),
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
        $canEspionage = $planet->getObjectAmount('espionage_probe') > 0 && $planet->getPlayer()->getResearchLevel('espionage_technology') > 0;

        return [
            'canBeIgnored' => false,
            'canBuddyRequests' => false,
            'canEspionage' => $canEspionage,
            'canMissileAttack' => false,
            'canPhalanx' => false,
            'canSendProbes' => $canEspionage,
            'canWrite' => false,
            'discoveryUnlocked' => 'You haven\'t unlocked the research to discover new lifeforms yet.\n',
            'missileAttackLink' => route('galaxy.index'),
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
            'nameAbbreviations' => $player->isAdmin() ? ['admin'] : [],
            'isAdmin' => $player->isAdmin(),
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
}
