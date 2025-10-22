<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

/**
 * Interplanetary Ballistic Missile (IBM) Attack Mission
 *
 * Flight time formula: (30 + 60 × distance_in_systems) / universe_speed seconds
 * Example: At 1x speed, same-system = 30s, 10 systems = 630s (10min 30s)
 */
class MissileMission extends GameMission
{
    protected static string $name = 'Missile Attack';
    protected static int $typeId = 10;
    protected static bool $hasReturnMission = false;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Missile attack is only possible for planets and moons.
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // If target planet does not exist, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // If planet belongs to current player, the mission is not possible.
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // Check if target is within missile range
        $attackerPlayer = $planet->getPlayer();
        $missileRange = $attackerPlayer->getMissileRange();

        // Calculate distance in systems
        $distance = $this->calculateSystemDistance($planet->getPlanetCoordinates(), $targetCoordinate);

        if ($distance > $missileRange) {
            return new MissionPossibleStatus(false, 'Target is out of missile range');
        }

        // Check if attacker has missiles
        $missileCount = $planet->getObjectAmount('interplanetary_missile');
        if ($missileCount <= 0) {
            return new MissionPossibleStatus(false, 'No missiles available');
        }

        return new MissionPossibleStatus(true);
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

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        $defenderPlanet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        $attackerPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Trigger defender planet update
        $defenderPlanet->update();

        // Get number of missiles sent (stored in metal field as no dedicated column exists)
        $missileCount = (int)$mission->metal;

        // Get defender's ABM count
        $abmCount = $defenderPlanet->getObjectAmount('anti_ballistic_missile');

        // Calculate how many missiles get through
        $interceptedMissiles = min($missileCount, $abmCount);
        $effectiveMissiles = $missileCount - $interceptedMissiles;

        // Remove intercepted ABMs from defender
        if ($interceptedMissiles > 0) {
            $defenderPlanet->removeUnit('anti_ballistic_missile', $interceptedMissiles);
        }

        // Calculate defense destruction
        $destroyedDefenses = $this->calculateDefenseDestruction($defenderPlanet, $effectiveMissiles);

        // Remove destroyed defenses
        if ($destroyedDefenses->getAmount() > 0) {
            $defenderPlanet->removeUnits($destroyedDefenses, false);
        }

        $defenderPlanet->save();

        // TODO: Send messages to both players about missile attack
        // This requires creating proper GameMessage classes with language file entries
        // $this->sendMissileAttackMessages($attackerPlanet, $defenderPlanet, $missileCount, $interceptedMissiles, $effectiveMissiles, $destroyedDefenses);

        // Mark mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Calculate which defenses are destroyed by missiles.
     * Each missile destroys defenses worth up to its attack power (12,000).
     *
     * @param PlanetService $defenderPlanet
     * @param int $missileCount
     * @return UnitCollection
     */
    private function calculateDefenseDestruction(PlanetService $defenderPlanet, int $missileCount): UnitCollection
    {
        $destroyedDefenses = new UnitCollection();

        if ($missileCount <= 0) {
            return $destroyedDefenses;
        }

        // Each missile has 12,000 attack power
        $totalDestructionPower = $missileCount * 12000;

        // Get all defense objects sorted by value (destroy cheapest first)
        $defenseObjects = ObjectService::getDefenseObjects();

        // Sort by price (metal + crystal + deuterium)
        usort($defenseObjects, function($a, $b) {
            $priceA = $a->price->resources->metal->get() + $a->price->resources->crystal->get() + $a->price->resources->deuterium->get();
            $priceB = $b->price->resources->metal->get() + $b->price->resources->crystal->get() + $b->price->resources->deuterium->get();
            return $priceA <=> $priceB;
        });

        // Destroy defenses starting with cheapest
        foreach ($defenseObjects as $defense) {
            if ($totalDestructionPower <= 0) {
                break;
            }

            // Don't target missiles or shield domes
            if (in_array($defense->machine_name, ['interplanetary_missile', 'anti_ballistic_missile', 'small_shield_dome', 'large_shield_dome'])) {
                continue;
            }

            $defenseCount = $defenderPlanet->getObjectAmount($defense->machine_name);
            if ($defenseCount <= 0) {
                continue;
            }

            // Calculate how many of this defense can be destroyed
            $defenseHullStrength = $defense->properties->structural_integrity->rawValue;
            $maxDestroyable = (int)($totalDestructionPower / $defenseHullStrength);
            $actualDestroyed = min($maxDestroyable, $defenseCount);

            if ($actualDestroyed > 0) {
                $destroyedDefenses->addUnit($defense, $actualDestroyed);
                $totalDestructionPower -= $actualDestroyed * $defenseHullStrength;
            }
        }

        return $destroyedDefenses;
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Missile missions don't have a return trip
        $mission->processed = 1;
        $mission->save();
    }
}
