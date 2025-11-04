<?php

namespace OGame\Services;

use OGame\GameObjects\Models\Units\UnitCollection;

class FleetHoldConsumptionService
{
    /**
     * Deuterium consumption per hour for each ship type while in hold/defend position
     * Based on official OGame mechanics
     *
     * @var array<string, float>
     */
    private const CONSUMPTION_RATES = [
        'small_cargo' => 1,
        'large_cargo' => 5,
        'light_fighter' => 2,
        'heavy_fighter' => 7,
        'cruiser' => 30,
        'battleship' => 50,
        'colony_ship' => 100,
        'recycler' => 30,
        'espionage_probe' => 0.1,
        'bomber' => 100,
        'destroyer' => 100,
        'battlecruiser' => 25,
        'deathstar' => 0.1,
        'solar_satellite' => 0, // Cannot be sent on missions
    ];

    /**
     * Calculate total deuterium consumption per hour for a fleet
     *
     * @param UnitCollection $units Fleet units
     * @return float Deuterium consumed per hour
     */
    public function calculateConsumptionPerHour(UnitCollection $units): float
    {
        $totalConsumption = 0;

        foreach ($units->units as $unit) {
            $machineName = $unit->unitObject->machine_name;
            $amount = $unit->amount;

            if (isset(self::CONSUMPTION_RATES[$machineName])) {
                $totalConsumption += self::CONSUMPTION_RATES[$machineName] * $amount;
            }
        }

        return $totalConsumption;
    }

    /**
     * Calculate total deuterium needed for a specific hold duration
     *
     * @param UnitCollection $units Fleet units
     * @param int $hours Hold duration in hours
     * @return float Total deuterium needed
     */
    public function calculateTotalConsumption(UnitCollection $units, int $hours): float
    {
        return $this->calculateConsumptionPerHour($units) * $hours;
    }

    /**
     * Get consumption rate for a specific ship type
     *
     * @param string $machineName Ship machine name
     * @return float Consumption per hour
     */
    public function getConsumptionRate(string $machineName): float
    {
        return self::CONSUMPTION_RATES[$machineName] ?? 0;
    }

    /**
     * Calculate maximum hold time based on available deuterium
     *
     * @param UnitCollection $units Fleet units
     * @param float $availableDeuterium Available deuterium
     * @return int Maximum hours the fleet can hold
     */
    public function calculateMaxHoldTime(UnitCollection $units, float $availableDeuterium): int
    {
        $consumptionPerHour = $this->calculateConsumptionPerHour($units);

        if ($consumptionPerHour <= 0) {
            return 32; // Max hold time if no consumption
        }

        $maxHours = floor($availableDeuterium / $consumptionPerHour);

        // Cap at maximum 32 hours
        return min((int)$maxHours, 32);
    }

    /**
     * Check if fleet has enough deuterium for hold duration
     *
     * @param UnitCollection $units Fleet units
     * @param int $hours Hold duration
     * @param float $availableDeuterium Available deuterium
     * @return bool True if enough deuterium
     */
    public function hasEnoughDeuterium(UnitCollection $units, int $hours, float $availableDeuterium): bool
    {
        $requiredDeuterium = $this->calculateTotalConsumption($units, $hours);
        return $availableDeuterium >= $requiredDeuterium;
    }
}
