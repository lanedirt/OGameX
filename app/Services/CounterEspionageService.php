<?php

namespace OGame\Services;

/**
 * Service for handling counter-espionage calculations and logic.
 */
class CounterEspionageService
{
    /**
     * Calculate counter-espionage chance percentage.
     *
     * Formula: (defender_ships * (defender_esp_level - attacker_esp_level + 1)) / (attacker_probes * 4) * 100
     * Result is clamped between 0 and 100.
     *
     * @param int $attackerProbeCount Number of espionage probes sent by attacker
     * @param int $attackerEspionageLevel Attacker's espionage technology level
     * @param int $defenderEspionageLevel Defender's espionage technology level
     * @param int $defenderShipCount Number of ships on defender's planet (excluding defense)
     * @return int Counter-espionage chance percentage (0-100)
     */
    public function calculateChance(
        int $attackerProbeCount,
        int $attackerEspionageLevel,
        int $defenderEspionageLevel,
        int $defenderShipCount
    ): int {
        // Prevent division by zero
        if ($attackerProbeCount <= 0) {
            return 0;
        }

        // If defender has no ships, no counter-espionage possible
        if ($defenderShipCount <= 0) {
            return 0;
        }

        // Calculate tech difference factor
        $techFactor = $defenderEspionageLevel - $attackerEspionageLevel + 1;

        // If attacker has significantly higher tech, no counter-espionage
        if ($techFactor <= 0) {
            return 0;
        }

        // Apply formula: (defender_ships * tech_factor) / (attacker_probes * 4) * 100
        $chance = ($defenderShipCount * $techFactor) / ($attackerProbeCount * 4) * 100;

        // Clamp between 0 and 100
        return (int) min(100, max(0, floor($chance)));
    }

    /**
     * Perform random roll to determine if counter-espionage triggers.
     *
     * @param int $chance Counter-espionage chance percentage (0-100)
     * @return bool True if counter-espionage is triggered
     */
    public function rollCounterEspionage(int $chance): bool
    {
        if ($chance <= 0) {
            return false;
        }

        if ($chance >= 100) {
            return true;
        }

        $roll = random_int(1, 100);
        return $roll <= $chance;
    }

    /**
     * Get ship count from planet (excluding defense structures).
     *
     * @param PlanetService $planet The planet to count ships on
     * @return int Total number of ships on the planet
     */
    public function getDefenderShipCount(PlanetService $planet): int
    {
        $shipUnits = $planet->getShipUnits();
        return $shipUnits->getAmount();
    }

    /**
     * Get only ship units from planet for counter-espionage battle.
     * Defense structures are excluded from counter-espionage battles.
     *
     * @param PlanetService $planet The planet to get ships from
     * @return \OGame\GameObjects\Models\Units\UnitCollection Ships on the planet
     */
    public function getDefenderShipsForBattle(PlanetService $planet): \OGame\GameObjects\Models\Units\UnitCollection
    {
        return $planet->getShipUnits();
    }
}
