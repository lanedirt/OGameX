<?php

namespace OGame\Services\NpcEmpire;

use Exception;
use Illuminate\Support\Facades\Date;
use OGame\Factories\PlanetServiceFactory;
use OGame\GameConstants\UniverseConstants;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Models\UnitQueue;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\UnitQueueService;

/**
 * Class NpcExpansionAi.
 *
 * Autonomous expansion for NPC empires: keeps one colony ship in
 * production when more planets are allowed, then dispatches it to a
 * nearby free position. Uses the regular colonisation mission, so all
 * OGame rules (astrophysics limits, position requirements, fleet slots)
 * apply identically.
 *
 * @package OGame\Services\NpcEmpire
 */
class NpcExpansionAi
{
    /**
     * Max system distance from the empire's homeworld to search for a
     * colonization target.
     */
    private const SYSTEM_SEARCH_RADIUS = 15;

    /**
     * Amount of random candidate coordinates checked per decision.
     */
    private const MAX_COORDINATE_ATTEMPTS = 25;

    public function __construct(
        private FleetMissionService $fleetMissionService,
        private PlanetServiceFactory $planetServiceFactory,
    ) {
    }

    /**
     * Decide whether and where to expand.
     *
     * @param PlayerService $player
     * @param NpcEmpire $empire
     */
    public function decide(PlayerService $player, NpcEmpire $empire): void
    {
        $now = (int)Date::now()->timestamp;
        $state = $empire->ai_state ?? [];

        if ($now < (int)($state['next_expansion_at'] ?? 0)) {
            return;
        }

        $homeworld = $player->planets->first();
        if ($homeworld === null) {
            return;
        }

        // Research-gated expansion room: wait until astrophysics catches up.
        $planetCount = $player->planets->planetCount();
        if ($planetCount >= $player->getMaxPlanetAmount()) {
            // Recheck an hour later; development AI keeps astrophysics moving.
            $state['next_expansion_at'] = $now + 3600;
            $empire->ai_state = $state;
            return;
        }

        // Ensure a colony ship is available on the homeworld.
        $colonyShips = $homeworld->getObjectAmount('colony_ship');
        if ($colonyShips < 1) {
            $this->queueColonyShipProduction($homeworld);

            // Recheck quickly: when the ship finished building, the next
            // decision cycle dispatches the colonization mission.
            $state['next_expansion_at'] = $now + random_int(300, 900);
            $empire->ai_state = $state;

            return;
        }

        $target = $this->findTargetCoordinate($homeworld->getPlanetCoordinates());
        if ($target === null) {
            // Region is crowded; look again later.
            $state['next_expansion_at'] = $now + random_int(1800, 3600);
            $empire->ai_state = $state;

            return;
        }

        try {
            $units = new UnitCollection();
            $units->addUnit(ObjectService::getShipObjectByMachineName('colony_ship'), 1);

            $this->fleetMissionService->createNewFromPlanet(
                $homeworld,
                $target,
                PlanetType::Planet,
                7,
                $units,
                new Resources(),
                10
            );

            // Success: expansion cadence scales inversely with the empire's
            // randomized expansion_rate trait (higher rate = fewer minutes).
            $baseMinutes = random_int(60, 180);
            $cooldown = (int)($baseMinutes / max(0.25, $empire->expansion_rate));
            $state['next_expansion_at'] = $now + max(600, $cooldown * 60);
        } catch (Exception $e) {
            // Not enough fuel, fleet slots full or another transient reason.
            // Try again on a short retry window.
            $state['next_expansion_at'] = $now + random_int(600, 1200);
            logger()->debug('NPC expansion dispatch failed', [
                'npc_user_id' => $empire->user_id,
                'error'       => $e->getMessage(),
            ]);
        }

        $empire->ai_state = $state;
    }

    /**
     * Queue production of one colony ship on the homeworld when the
     * shipyard exists and no colony ship is already pending.
     *
     * @param PlanetService $homeworld
     */
    private function queueColonyShipProduction(PlanetService $homeworld): void
    {
        // Do not stack duplicate colony ship orders.
        $pending = UnitQueue::where('planet_id', $homeworld->getPlanetId())
            ->where('processed', 0)
            ->where('object_id', ObjectService::getShipObjectByMachineName('colony_ship')->id)
            ->exists();
        if ($pending) {
            return;
        }

        // add() silently no-ops when the shipyard is missing or the planet
        // cannot afford a colony ship yet.
        resolve(UnitQueueService::class)->add(
            $homeworld,
            ObjectService::getShipObjectByMachineName('colony_ship')->id,
            1
        );
    }

    /**
     * Find a free colonization target near the given origin coordinate.
     * Positions 4-12 only (no astrophysics gating this way) within a
     * random system offset around the homeworld, same galaxy.
     *
     * @param Coordinate $origin
     * @return Coordinate|null
     */
    private function findTargetCoordinate(Coordinate $origin): Coordinate|null
    {
        for ($attempt = 0; $attempt < self::MAX_COORDINATE_ATTEMPTS; $attempt++) {
            $systemOffset = random_int(-self::SYSTEM_SEARCH_RADIUS, self::SYSTEM_SEARCH_RADIUS);
            if ($systemOffset === 0) {
                continue;
            }

            $system = $origin->system + $systemOffset;
            if ($system < UniverseConstants::MIN_SYSTEM || $system > UniverseConstants::MAX_SYSTEM_COUNT) {
                continue;
            }

            $position = random_int(4, 12);
            $coordinate = new Coordinate($origin->galaxy, $system, $position);

            if (!$this->planetServiceFactory->planetExistsAtCoordinate($coordinate)) {
                return $coordinate;
            }
        }

        return null;
    }
}
