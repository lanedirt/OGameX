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

        // Get attacker and defender players for technology levels
        $attackerPlayer = $attackerPlanet->getPlayer();
        $defenderPlayer = $defenderPlanet->getPlayer();

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

        // Calculate defense destruction with proper OGame formula
        $destroyedDefenses = $this->calculateDefenseDestruction($defenderPlanet, $defenderPlayer, $effectiveMissiles, $attackerPlayer);

        // Remove destroyed defenses
        if ($destroyedDefenses->getAmount() > 0) {
            $defenderPlanet->removeUnits($destroyedDefenses, false);
        }

        $defenderPlanet->save();

        // Send battle reports to both players
        $this->sendMissileAttackMessages($attackerPlanet, $attackerPlayer, $defenderPlanet, $defenderPlayer, $missileCount, $interceptedMissiles, $effectiveMissiles, $destroyedDefenses);

        // Mark mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Calculate which defenses are destroyed by missiles using proper OGame formula.
     *
     * OGame Formula:
     * - Missile Damage = Number of IPMs × 12,000 × (1 + 0.1 × Attacker's Weapon Technology)
     * - Defense Armor = Defense Structure × (1 + 0.1 × Defender's Armor Technology) / 10
     * - Defenses Destroyed = floor(Missile Damage / Defense Armor)
     *
     * @param PlanetService $defenderPlanet
     * @param \OGame\Services\PlayerService $defenderPlayer
     * @param int $missileCount
     * @param \OGame\Services\PlayerService $attackerPlayer
     * @return UnitCollection
     */
    private function calculateDefenseDestruction(PlanetService $defenderPlanet, $defenderPlayer, int $missileCount, $attackerPlayer): UnitCollection
    {
        $destroyedDefenses = new UnitCollection();

        if ($missileCount <= 0) {
            return $destroyedDefenses;
        }

        // Get technology levels
        $weaponTech = $attackerPlayer->getResearchLevel('weapon_technology');
        $armorTech = $defenderPlayer->getResearchLevel('armor_technology');

        // Calculate total missile damage with weapon technology bonus
        // Formula: Number of IPMs × 12,000 × (1 + 0.1 × Weapon Tech)
        $totalDestructionPower = $missileCount * 12000 * (1 + 0.1 * $weaponTech);

        // Get all defense objects sorted by value (destroy cheapest first)
        $defenseObjects = ObjectService::getDefenseObjects();

        // Sort by price (metal + crystal + deuterium)
        usort($defenseObjects, function ($a, $b) {
            $priceA = $a->price->resources->metal->get() + $a->price->resources->crystal->get() + $a->price->resources->deuterium->get();
            $priceB = $b->price->resources->metal->get() + $b->price->resources->crystal->get() + $b->price->resources->deuterium->get();
            return $priceA <=> $priceB;
        });

        // Destroy defenses starting with cheapest
        foreach ($defenseObjects as $defense) {
            if ($totalDestructionPower <= 0) {
                break;
            }

            // Don't target missiles or shield domes (IPMs ignore shields)
            if (in_array($defense->machine_name, ['interplanetary_missile', 'anti_ballistic_missile', 'small_shield_dome', 'large_shield_dome'])) {
                continue;
            }

            $defenseCount = $defenderPlanet->getObjectAmount($defense->machine_name);
            if ($defenseCount <= 0) {
                continue;
            }

            // Calculate defense armor with armor technology bonus
            // Formula: Defense Structure × (1 + 0.1 × Armor Tech) / 10
            $defenseStructure = $defense->properties->structural_integrity->rawValue;
            $defenseArmor = $defenseStructure * (1 + 0.1 * $armorTech) / 10;

            // Calculate how many of this defense can be destroyed
            $maxDestroyable = (int)floor($totalDestructionPower / $defenseArmor);
            $actualDestroyed = min($maxDestroyable, $defenseCount);

            if ($actualDestroyed > 0) {
                $destroyedDefenses->addUnit($defense, $actualDestroyed);
                $totalDestructionPower -= $actualDestroyed * $defenseArmor;
            }
        }

        return $destroyedDefenses;
    }

    /**
     * Send battle reports to both attacker and defender.
     *
     * @param PlanetService $attackerPlanet
     * @param \OGame\Services\PlayerService $attackerPlayer
     * @param PlanetService $defenderPlanet
     * @param \OGame\Services\PlayerService $defenderPlayer
     * @param int $missileCount
     * @param int $interceptedMissiles
     * @param int $effectiveMissiles
     * @param UnitCollection $destroyedDefenses
     * @return void
     */
    private function sendMissileAttackMessages(PlanetService $attackerPlanet, $attackerPlayer, PlanetService $defenderPlanet, $defenderPlayer, int $missileCount, int $interceptedMissiles, int $effectiveMissiles, UnitCollection $destroyedDefenses): void
    {
        // Format destroyed defenses list
        $defensesDestroyedText = '';
        if ($destroyedDefenses->getAmount() > 0) {
            $defensesList = [];
            foreach ($destroyedDefenses->units as $unit) {
                $defensesList[] = $unit->unitObject->title . ': ' . $unit->amount;
            }
            $defensesDestroyedText = implode(', ', $defensesList);
        } else {
            $defensesDestroyedText = 'None';
        }

        // Get coordinates as strings
        $targetCoords = $defenderPlanet->getPlanetCoordinates()->asString();
        $attackerName = $attackerPlayer->getUsername();

        // Send message to attacker
        $messageService = resolve(\OGame\Services\MessageService::class, ['player' => $attackerPlayer]);
        $messageService->sendSystemMessageToPlayer(
            $attackerPlayer,
            \OGame\GameMessages\MissileAttackReport::class,
            [
                'target_coords' => $targetCoords,
                'missiles_sent' => $missileCount,
                'missiles_intercepted' => $interceptedMissiles,
                'missiles_hit' => $effectiveMissiles,
                'defenses_destroyed' => $defensesDestroyedText,
            ]
        );

        // Send message to defender
        $messageService = resolve(\OGame\Services\MessageService::class, ['player' => $defenderPlayer]);
        $messageService->sendSystemMessageToPlayer(
            $defenderPlayer,
            \OGame\GameMessages\MissileDefenseReport::class,
            [
                'attacker_name' => $attackerName,
                'planet_coords' => $targetCoords,
                'missiles_incoming' => $missileCount,
                'missiles_intercepted' => $interceptedMissiles,
                'missiles_hit' => $effectiveMissiles,
                'defenses_destroyed' => $defensesDestroyedText,
            ]
        );
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
