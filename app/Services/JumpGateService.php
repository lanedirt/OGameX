<?php

namespace OGame\Services;

use Illuminate\Support\Carbon;
use OGame\Models\FleetMission;

/**
 * Class JumpGateService
 *
 * Handles Jump Gate functionality including cooldown calculation,
 * ship transfer between moons, and target validation.
 *
 * @package OGame\Services
 */
class JumpGateService
{
    /**
     * Base cooldown time in minutes (at fleet speed 1).
     */
    private const BASE_COOLDOWN_MINUTES = 60;

    /**
     * Cooldown reduction percentage per Jump Gate level above 1.
     */
    private const COOLDOWN_REDUCTION_PER_LEVEL = 0.10;

    /**
     * Ships that cannot be transferred via Jump Gate.
     */
    private const NON_TRANSFERABLE_SHIPS = [
        'solar_satellite',
    ];

    private SettingsService $settingsService;

    /**
     * JumpGateService constructor.
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Calculate cooldown duration in seconds based on Jump Gate level and fleet speed.
     *
     * Formula: base_time = 60 / fleet_speed_war (in minutes)
     *          reduction = (level - 1) * 10%
     *          cooldown = base_time - (base_time * reduction)
     *
     * @param int $jump_gate_level
     * @return int Cooldown duration in seconds
     */
    public function calculateCooldown(int $jump_gate_level): int
    {
        $fleet_speed = $this->settingsService->fleetSpeedWar();
        if ($fleet_speed < 1) {
            $fleet_speed = 1;
        }

        // Base time in minutes
        $base_time_minutes = self::BASE_COOLDOWN_MINUTES / $fleet_speed;

        // Reduction based on Jump Gate level (level 1 = 0%, level 2 = 10%, etc.)
        $reduction = ($jump_gate_level - 1) * self::COOLDOWN_REDUCTION_PER_LEVEL;

        // Final cooldown in minutes
        $cooldown_minutes = $base_time_minutes - ($base_time_minutes * $reduction);

        // Ensure minimum cooldown of 1 minute
        if ($cooldown_minutes < 1) {
            $cooldown_minutes = 1;
        }

        // Convert to seconds
        return (int)($cooldown_minutes * 60);
    }

    /**
     * Check if a moon's Jump Gate is currently on cooldown.
     *
     * @param PlanetService $moon
     * @return bool
     */
    public function isOnCooldown(PlanetService $moon): bool
    {
        $cooldown_until = $moon->getJumpGateCooldown();

        if ($cooldown_until === null || $cooldown_until === 0) {
            return false;
        }

        return $cooldown_until > Carbon::now()->timestamp;
    }

    /**
     * Get the remaining cooldown time in seconds.
     *
     * @param PlanetService $moon
     * @return int Remaining seconds, or 0 if not on cooldown
     */
    public function getRemainingCooldown(PlanetService $moon): int
    {
        $cooldown_until = $moon->getJumpGateCooldown();

        if ($cooldown_until === null || $cooldown_until === 0) {
            return 0;
        }

        $now = (int) Carbon::now()->timestamp;

        if ($cooldown_until <= $now) {
            return 0;
        }

        return $cooldown_until - $now;
    }

    /**
     * Format remaining cooldown time as a human-readable string.
     *
     * @param int $seconds
     * @return string e.g., "10dk 42sn"
     */
    public function formatCooldown(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0s';
        }

        $minutes = floor($seconds / 60);
        $remaining_seconds = $seconds % 60;

        if ($minutes > 0 && $remaining_seconds > 0) {
            return $minutes . 'm ' . $remaining_seconds . 's';
        } elseif ($minutes > 0) {
            return $minutes . 'm';
        } else {
            return $remaining_seconds . 's';
        }
    }

    /**
     * Get all eligible target moons for jump.
     * A moon is eligible if:
     * - It has a Jump Gate (level > 0)
     * - It is not the current moon
     * - It is not on cooldown
     *
     * @param PlayerService $player
     * @param PlanetService $current_moon
     * @return array<int, PlanetService> Array of eligible PlanetService objects
     */
    public function getEligibleTargets(PlayerService $player, PlanetService $current_moon): array
    {
        $eligible_targets = [];

        foreach ($player->planets->allMoons() as $moon) {
            // Skip current moon
            if ($moon->getPlanetId() === $current_moon->getPlanetId()) {
                continue;
            }

            // Check if moon has Jump Gate
            if ($moon->getObjectLevel('jump_gate') < 1) {
                continue;
            }

            // Check if moon is on cooldown
            if ($this->isOnCooldown($moon)) {
                continue;
            }

            $eligible_targets[] = $moon;
        }

        return $eligible_targets;
    }

    /**
     * Get all target moons (including those on cooldown) for display purposes.
     *
     * @param PlayerService $player
     * @param PlanetService $current_moon
     * @return array<int, array{moon: PlanetService, on_cooldown: bool, cooldown_remaining: int}> Array of moons with their cooldown status
     */
    public function getAllTargetMoons(PlayerService $player, PlanetService $current_moon): array
    {
        $targets = [];

        foreach ($player->planets->allMoons() as $moon) {
            // Skip current moon
            if ($moon->getPlanetId() === $current_moon->getPlanetId()) {
                continue;
            }

            // Check if moon has Jump Gate
            if ($moon->getObjectLevel('jump_gate') < 1) {
                continue;
            }

            $targets[] = [
                'moon' => $moon,
                'on_cooldown' => $this->isOnCooldown($moon),
                'cooldown_remaining' => $this->getRemainingCooldown($moon),
            ];
        }

        return $targets;
    }

    /**
     * Get list of transferable ship machine names.
     *
     * @return array<int, string>
     */
    public function getTransferableShips(): array
    {
        $ships = [];

        $objects = ObjectService::getShipObjects();
        foreach ($objects as $ship) {
            if (!in_array($ship->machine_name, self::NON_TRANSFERABLE_SHIPS)) {
                $ships[] = $ship->machine_name;
            }
        }

        return $ships;
    }

    /**
     * Transfer ships from source moon to target moon.
     *
     * @param PlanetService $source
     * @param PlanetService $target
     * @param array<string, int> $ships Array of [machine_name => amount]
     * @return bool Success status
     */
    public function transferShips(PlanetService $source, PlanetService $target, array $ships): bool
    {
        // Validate all ships before transferring
        foreach ($ships as $ship_name => $amount) {
            if ($amount <= 0) {
                continue;
            }

            // Check if ship type is transferable
            if (in_array($ship_name, self::NON_TRANSFERABLE_SHIPS)) {
                return false;
            }

            // Check if source has enough ships
            $available = $source->getObjectAmount($ship_name);
            if ($available < $amount) {
                return false;
            }
        }

        // Perform the transfer
        foreach ($ships as $ship_name => $amount) {
            if ($amount <= 0) {
                continue;
            }

            // Remove from source
            $source->removeUnit($ship_name, $amount, false);

            // Add to target
            $target->addUnit($ship_name, $amount, false);
        }

        // Save both planets
        $source->save();
        $target->save();

        return true;
    }

    /**
     * Set cooldown on both source and target moons.
     * Uses the minimum Jump Gate level between the two moons for calculation.
     *
     * @param PlanetService $source
     * @param PlanetService $target
     * @return void
     */
    public function setCooldown(PlanetService $source, PlanetService $target): void
    {
        // Use the minimum level between source and target for cooldown calculation
        $source_level = $source->getObjectLevel('jump_gate');
        $target_level = $target->getObjectLevel('jump_gate');
        $min_level = min($source_level, $target_level);

        $cooldown_seconds = $this->calculateCooldown($min_level);
        $cooldown_until = (int) Carbon::now()->timestamp + $cooldown_seconds;

        // Set cooldown on source moon
        $source->setJumpGateCooldown($cooldown_until);

        // Set cooldown on target moon
        $target->setJumpGateCooldown($cooldown_until);
    }

    /**
     * Get the default Jump Gate target for a moon.
     *
     * @param PlanetService $moon
     * @param PlayerService $player
     * @return PlanetService|null
     */
    public function getDefaultTarget(PlanetService $moon, PlayerService $player): PlanetService|null
    {
        $default_target_id = $moon->getDefaultJumpGateTargetId();

        if ($default_target_id === null) {
            return null;
        }

        try {
            $target_moon = $player->planets->getById($default_target_id);

            // Verify target is a moon with Jump Gate
            if ($target_moon->isMoon() && $target_moon->getObjectLevel('jump_gate') > 0) {
                return $target_moon;
            }
        } catch (\Exception $e) {
            // Target moon no longer exists or is not accessible
        }

        return null;
    }

    /**
     * Set the default Jump Gate target for a moon.
     *
     * @param PlanetService $moon
     * @param int|null $target_moon_id
     * @return void
     */
    public function setDefaultTarget(PlanetService $moon, int|null $target_moon_id): void
    {
        $moon->setDefaultJumpGateTargetId($target_moon_id);
    }

    /**
     * Check if a moon has arrived but unprocessed fleets from other players.
     * This prevents race conditions during fleet mission processing.
     *
     * @param PlanetService $moon
     * @return bool True if there are unprocessed arrived fleets
     */
    public function hasUnprocessedArrivedFleet(PlanetService $moon): bool
    {
        return FleetMission::where('planet_id_to', $moon->getPlanetId())
            ->where('processed', 0)
            ->where('time_arrival', '<=', Carbon::now()->timestamp)
            ->where('user_id', '!=', $moon->getPlayer()->getId())
            ->exists();
    }
}
