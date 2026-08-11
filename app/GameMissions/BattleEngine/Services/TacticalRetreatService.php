<?php

namespace OGame\GameMissions\BattleEngine\Services;

use OGame\GameConstants\UniverseConstants;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\DefenderFleet;
use OGame\GameMissions\BattleEngine\Models\TacticalRetreatDecision;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Highscore;
use OGame\Models\Planet\Coordinate;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

/**
 * Evaluates OGame tactical retreat (fleet flee) before combat rounds.
 */
class TacticalRetreatService
{
    private const int POINTS_CUTOFF = 500000;

    /**
     * Ship types that never contribute to retreat points and never flee.
     *
     * @var list<string>
     */
    private const array EXCLUDED_FROM_POINTS = [
        'deathstar',
        'espionage_probe',
        'solar_satellite',
        'crawler',
    ];

    /**
     * Ship types that cannot flee (even if they somehow had points).
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
     * Calculate retreat-weighted fleet points for a unit collection.
     * Combat ships: 100% of points. Civil ships: 25%. Excluded types: 0%. Defenses: 0%.
     */
    public function calculateFleetPoints(UnitCollection $units): int
    {
        $resourcesSpent = 0.0;

        $militaryMachineNames = [];
        foreach (ObjectService::getMilitaryShipObjects() as $ship) {
            $militaryMachineNames[$ship->machine_name] = true;
        }

        $civilMachineNames = [];
        foreach (ObjectService::getCivilShipObjects() as $ship) {
            $civilMachineNames[$ship->machine_name] = true;
        }

        foreach ($units->units as $entry) {
            $machineName = $entry->unitObject->machine_name;
            if ($entry->amount <= 0) {
                continue;
            }
            if (in_array($machineName, self::EXCLUDED_FROM_POINTS, true)) {
                continue;
            }

            $rawPrice = ObjectService::getObjectRawPrice($machineName)->multiply($entry->amount)->sum();

            if (isset($militaryMachineNames[$machineName])) {
                $resourcesSpent += $rawPrice;
            } elseif (isset($civilMachineNames[$machineName])) {
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

        $shipMachineNames = [];
        foreach (ObjectService::getShipObjects() as $ship) {
            $shipMachineNames[$ship->machine_name] = true;
        }

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
     * Deuterium cost to flee: 1.5 × fuel for a 100% flight to a neighboring system.
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
        $neighborSystem = $coords->system + 1;
        if ($neighborSystem > UniverseConstants::MAX_SYSTEM_COUNT) {
            $neighborSystem = UniverseConstants::MIN_SYSTEM;
        }

        $neighbor = new Coordinate($coords->galaxy, $neighborSystem, $coords->position);

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

        $threshold = $this->resolveRetreatThreshold($defenderPlayer);
        if ($threshold === 0) {
            $decision->blockedReason = 'disabled';
            return $decision;
        }

        if ($this->getGeneralPoints($defenderPlayer) >= self::POINTS_CUTOFF) {
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

        // Flee when attackerPoints >= threshold * defenderPoints.
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

    private function getGeneralPoints(PlayerService $player): int
    {
        $highscore = Highscore::where('player_id', $player->getId())->first();

        return (int)($highscore->general ?? 0);
    }
}
