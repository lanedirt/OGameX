<?php

namespace OGame\GameMissions\BattleEngine\Services;

use OGame\GameConstants\UniverseConstants;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\Models\TacticalRetreatDecision;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet\Coordinate;
use OGame\Services\FleetMissionService;
use OGame\Services\NPCPlayerService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

/**
 * Evaluates OGame tactical retreat (fleet flee) before combat rounds.
 *
 * Authentic OGame rules (Gameforge patch notes / in-game tooltips):
 * - Flee from a power ratio of 5:1 (3:1 with Admiral), i.e. attackerPoints >= threshold * defenderPoints
 * - Civil ships count at 25% (rounded down); defenses / probes / sats / crawlers do not count
 * - Deathstars count for points but cannot flee (they remain in combat)
 * - ACS defend fleets count for points but cannot flee
 * - Deuterium cost = 1.5 × fuel for a 100% flight to a neighbouring planet position (slot)
 * - Ends at 500,000 general points; inactive fleets do not flee; honourable fights do not flee
 *   (honour system not yet implemented in OGameX)
 */
class TacticalRetreatService
{
    private const int POINTS_CUTOFF = 500000;

    /**
     * Ship types that contribute 0 retreat points (and also cannot flee).
     *
     * @var list<string>
     */
    private const array ZERO_POINT_SHIPS = [
        'espionage_probe',
        'solar_satellite',
        'crawler',
    ];

    /**
     * Ship types that cannot flee (may still contribute retreat points).
     *
     * @var list<string>
     */
    private const array CANNOT_FLEE = [
        'deathstar',
        'espionage_probe',
        'solar_satellite',
        'crawler',
    ];

    /**
     * Cached military ship machine names (machine_name => true).
     *
     * @var array<string, true>|null
     */
    private static ?array $militaryShipNames = null;

    /**
     * Cached civil ship machine names (machine_name => true).
     *
     * @var array<string, true>|null
     */
    private static ?array $civilShipNames = null;

    /**
     * Cached all ship machine names (machine_name => true).
     *
     * @var array<string, true>|null
     */
    private static ?array $allShipNames = null;

    /**
     * Calculate retreat-weighted fleet points for a unit collection.
     * Combat ships (incl. Deathstars): 100%. Civil ships: 25% (floored with total).
     * Probes, sats, crawlers, defenses: 0%.
     */
    public function calculateFleetPoints(UnitCollection $units): int
    {
        $resourcesSpent = 0.0;
        $militaryMachineNames = $this->militaryShipNames();
        $civilMachineNames = $this->civilShipNames();

        foreach ($units->units as $entry) {
            $machineName = $entry->unitObject->machine_name;
            if ($entry->amount <= 0) {
                continue;
            }
            if (in_array($machineName, self::ZERO_POINT_SHIPS, true)) {
                continue;
            }

            $rawPrice = ObjectService::getObjectRawPrice($machineName)->multiply($entry->amount)->sum();

            if (isset($militaryMachineNames[$machineName])) {
                $resourcesSpent += $rawPrice;
            } elseif (isset($civilMachineNames[$machineName])) {
                // Civil ships at 25%, rounded down via final floor(/1000).
                $resourcesSpent += $rawPrice * 0.25;
            }
            // Defenses and anything else: ignored.
        }

        return (int)floor($resourcesSpent / 1000);
    }

    /**
     * Extract ships that are allowed to flee from a unit collection.
     */
    public function extractFleeingUnits(UnitCollection $units): UnitCollection
    {
        $fleeing = new UnitCollection();
        $shipMachineNames = $this->allShipNames();

        foreach ($units->units as $entry) {
            $machineName = $entry->unitObject->machine_name;
            if ($entry->amount <= 0) {
                continue;
            }
            if (!isset($shipMachineNames[$machineName])) {
                continue;
            }
            if (in_array($machineName, self::CANNOT_FLEE, true)) {
                continue;
            }
            $fleeing->addUnit($entry->unitObject, $entry->amount);
        }

        return $fleeing;
    }

    /**
     * Display ratio of attacker supremacy as N in "1:N". Minimum 1.
     */
    public function calculateSupremacyRatio(int $attackerPoints, int $defenderPoints): int
    {
        if ($attackerPoints <= 0) {
            return 1;
        }
        if ($defenderPoints <= 0) {
            return max(1, $attackerPoints);
        }

        return max(1, (int)floor($attackerPoints / $defenderPoints));
    }

    /**
     * Deuterium cost to flee: 1.5 × fuel for a 100% flight to a neighbouring planet position.
     *
     * Official Gameforge wording: "neighbouring position" / "neighbouring slot"
     * (same system, adjacent planet slot) — not a neighbouring system.
     */
    public function calculateFleeDeuteriumCost(PlanetService $planet, UnitCollection $fleeingUnits): int
    {
        if ($fleeingUnits->getAmount() === 0) {
            return 0;
        }

        $player = $planet->getPlayer();
        if ($player === null) {
            return 0;
        }

        $coords = $planet->getPlanetCoordinates();
        $neighborPosition = $coords->position + 1;
        if ($neighborPosition > UniverseConstants::MAX_PLANET_POSITION) {
            $neighborPosition = $coords->position - 1;
        }
        if ($neighborPosition < UniverseConstants::MIN_PLANET_POSITION) {
            $neighborPosition = UniverseConstants::MIN_PLANET_POSITION;
        }

        $neighbor = new Coordinate($coords->galaxy, $coords->system, $neighborPosition);

        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $player]);
        $baseConsumption = (int)$fleetMissionService->calculateConsumption($planet, $fleeingUnits, $neighbor, 0, 10.0);

        return (int)ceil($baseConsumption * 1.5);
    }

    /**
     * Evaluate whether the defending planet fleet should flee before combat.
     *
     * @param array<AttackerFleet> $attackers
     * @param array<DefenderFleet> $defenders
     */
    public function evaluate(
        PlanetService $defenderPlanet,
        array $attackers,
        array $defenders,
        bool $retreatAfterDefenderRetreat,
    ): TacticalRetreatDecision {
        $attackerUnits = new UnitCollection();
        foreach ($attackers as $attacker) {
            $attackerUnits->addCollection($attacker->units);
        }

        $defenderPointsUnits = new UnitCollection();
        $planetOwnerFleet = null;
        foreach ($defenders as $defender) {
            $defenderPointsUnits->addCollection($defender->units);
            if ($defender->fleetMissionId === 0) {
                $planetOwnerFleet = $defender;
            }
        }

        $attackerPoints = $this->calculateFleetPoints($attackerUnits);
        $defenderPoints = $this->calculateFleetPoints($defenderPointsUnits);
        $ratio = $this->calculateSupremacyRatio($attackerPoints, $defenderPoints);

        $emptyFleeing = new UnitCollection();
        $decision = new TacticalRetreatDecision(
            $attackerPoints,
            $defenderPoints,
            $ratio,
            false,
            false,
            0,
            $emptyFleeing,
        );

        $defenderPlayer = $defenderPlanet->getPlayer();
        if ($defenderPlayer === null || $planetOwnerFleet === null) {
            $decision->blockedReason = 'no_defender';
            return $decision;
        }

        // Expedition NPC battles reuse the battle engine; NPCs must never flee.
        if ($defenderPlayer instanceof NPCPlayerService || $defenderPlayer->getId() <= 0) {
            $decision->blockedReason = 'npc';
            return $decision;
        }

        $threshold = $this->resolveRetreatThreshold($defenderPlayer);
        if ($threshold === 0) {
            $decision->blockedReason = 'disabled';
            return $decision;
        }

        if ($defenderPlayer->getCachedGeneralScore() >= self::POINTS_CUTOFF) {
            $decision->blockedReason = 'points_cutoff';
            return $decision;
        }

        if ($defenderPlayer->isInactive()) {
            $decision->blockedReason = 'inactive';
            return $decision;
        }

        $fleeingUnits = $this->extractFleeingUnits($planetOwnerFleet->units);
        if ($fleeingUnits->getAmount() === 0) {
            $decision->blockedReason = 'nothing_to_flee';
            return $decision;
        }

        $deuteriumCost = $this->calculateFleeDeuteriumCost($defenderPlanet, $fleeingUnits);
        $decision->deuteriumCost = $deuteriumCost;
        $decision->fleeingUnits = $fleeingUnits;

        // Gameforge: "From a ratio of 5:1" / "reaches 5:1"; community simulators use >=.
        // With 0 defender fleet points, any positive attacker force meets the threshold.
        $requiredAttackerPoints = $threshold * $defenderPoints;
        if ($attackerPoints < $requiredAttackerPoints || ($defenderPoints === 0 && $attackerPoints <= 0)) {
            $decision->blockedReason = 'ratio_not_met';
            return $decision;
        }

        $availableDeuterium = (int)$defenderPlanet->getResources()->deuterium->get();
        if ($availableDeuterium < $deuteriumCost) {
            $decision->blockedReason = 'insufficient_deuterium';
            return $decision;
        }

        $decision->defenderFled = true;
        $decision->attackerAlsoRetreated = $retreatAfterDefenderRetreat;

        return $decision;
    }

    /**
     * Resolve the defender's configured retreat threshold (0, 3, or 5).
     * OGame only offers Never / 5:1 / 3:1 (Admiral) — no other ratios.
     */
    public function resolveRetreatThreshold(PlayerService $defenderPlayer): int
    {
        $ratio = (int)($defenderPlayer->getUser()->tactical_retreat_ratio ?? 5);

        if ($ratio === 3) {
            // Admiral required for 3:1; fall back to 5:1 if not available.
            if (!$defenderPlayer->hasAdmiral()) {
                return 5;
            }
            return 3;
        }

        if ($ratio === 0) {
            return 0;
        }

        return 5;
    }

    /**
     * @return array<string, true>
     */
    private function militaryShipNames(): array
    {
        if (self::$militaryShipNames === null) {
            self::$militaryShipNames = [];
            foreach (ObjectService::getMilitaryShipObjects() as $ship) {
                self::$militaryShipNames[$ship->machine_name] = true;
            }
        }

        return self::$militaryShipNames;
    }

    /**
     * @return array<string, true>
     */
    private function civilShipNames(): array
    {
        if (self::$civilShipNames === null) {
            self::$civilShipNames = [];
            foreach (ObjectService::getCivilShipObjects() as $ship) {
                self::$civilShipNames[$ship->machine_name] = true;
            }
        }

        return self::$civilShipNames;
    }

    /**
     * @return array<string, true>
     */
    private function allShipNames(): array
    {
        if (self::$allShipNames === null) {
            self::$allShipNames = [];
            foreach (ObjectService::getShipObjects() as $ship) {
                self::$allShipNames[$ship->machine_name] = true;
            }
        }

        return self::$allShipNames;
    }
}
