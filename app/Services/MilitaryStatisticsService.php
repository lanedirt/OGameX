<?php

namespace OGame\Services;

use Exception;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\User;

/**
 * Service for tracking and updating military statistics for highscores.
 * Handles both destroyed and lost military units across combat, expeditions, and scrapping.
 */
class MilitaryStatisticsService
{
    /**
     * Calculate military points from units.
     * Military ships count 100%, civil ships count 50%, defenses count 100%.
     *
     * @param UnitCollection $units The units to calculate points for
     * @return int The military points value
     */
    public function calculateMilitaryPoints(UnitCollection $units): int
    {
        $points = 0;

        foreach ($units->units as $unit) {
            if ($unit->amount > 0) {
                $unitValue = $unit->unitObject->price->resources->sum();

                // Check unit type and apply appropriate multiplier
                if ($unit->unitObject->type === GameObjectType::Ship) {
                    // Check if it's a military or civil ship
                    $militaryShips = ObjectService::getMilitaryShipObjects();
                    $isMilitaryShip = false;
                    foreach ($militaryShips as $militaryShip) {
                        if ($militaryShip->machine_name === $unit->unitObject->machine_name) {
                            $isMilitaryShip = true;
                            break;
                        }
                    }

                    if ($isMilitaryShip) {
                        // Military ships: 100%
                        $points += ($unitValue * $unit->amount);
                    } else {
                        // Civil ships: 50%
                        $points += ($unitValue * $unit->amount * 0.5);
                    }
                } elseif ($unit->unitObject->type === GameObjectType::Defense) {
                    // Defense units: 100%
                    $points += ($unitValue * $unit->amount);
                }
            }
        }

        // Convert to points (divide by 1000, same as regular highscore calculation)
        return (int)floor($points / 1000);
    }

    /**
     * Calculate military points from a single unit by machine name and amount.
     * Used when we don't have a UnitCollection but individual unit data.
     *
     * @param string $machineName The unit's machine name
     * @param int $amount The amount of units
     * @return int The military points value
     */
    public function calculateMilitaryPointsFromMachineName(string $machineName, int $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }

        try {
            // Get the unit object (works for both ships and defenses)
            $unitObject = ObjectService::getUnitObjectByMachineName($machineName);
            $unitValue = $unitObject->price->resources->sum();

            // Apply appropriate multiplier based on unit type
            if ($unitObject->type === GameObjectType::Ship) {
                // Check if it's a military or civil ship
                $militaryShips = ObjectService::getMilitaryShipObjects();
                $isMilitaryShip = false;
                foreach ($militaryShips as $militaryShip) {
                    if ($militaryShip->machine_name === $unitObject->machine_name) {
                        $isMilitaryShip = true;
                        break;
                    }
                }

                if ($isMilitaryShip) {
                    // Military ships: 100%
                    $points = ($unitValue * $amount);
                } else {
                    // Civil ships: 50%
                    $points = ($unitValue * $amount * 0.5);
                }
            } elseif ($unitObject->type === GameObjectType::Defense) {
                // Defense units: 100%
                $points = ($unitValue * $amount);
            } else {
                return 0;
            }

            // Convert to points (divide by 1000)
            return (int)floor($points / 1000);
        } catch (Exception $e) {
            // Unit not found, return 0
            return 0;
        }
    }

    /**
     * Add lost military points to a user's statistics.
     * Used when a player loses units (combat, expedition, scrapping, etc.).
     *
     * @param User $user The user who lost units
     * @param int $points The military points lost
     * @return void
     */
    public function addLostPoints(User $user, int $points): void
    {
        if ($points > 0) {
            $user->military_units_lost_points += $points;
            $user->save();
        }
    }

    /**
     * Add destroyed military points to a user's statistics.
     * Used when a player destroys enemy units in combat.
     *
     * @param User $user The user who destroyed units
     * @param int $points The military points destroyed
     * @return void
     */
    public function addDestroyedPoints(User $user, int $points): void
    {
        if ($points > 0) {
            $user->military_units_destroyed_points += $points;
            $user->save();
        }
    }
}
