<?php

namespace OGame\Services;

use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * NPCFleetGeneratorService - Generates NPC fleets for expedition battles.
 *
 * This service creates balanced enemy fleets (pirates or aliens) based on:
 * - Player fleet composition and strength
 * - NPC type (pirates have lower tech, aliens have higher tech)
 * - Random variation to keep battles unpredictable
 */
class NPCFleetGeneratorService
{
    /**
     * Generate an enemy fleet for an expedition battle.
     *
     * @param UnitCollection $playerFleet The player's expedition fleet
     * @param PlayerService $playerService The player service for tech level comparison
     * @param string $npcType 'pirate' or 'alien'
     * @return array{fleet: UnitCollection, player: NPCPlayerService}
     */
    public function generateEnemyFleet(UnitCollection $playerFleet, PlayerService $playerService, string $npcType): array
    {
        $objectService = app(ObjectService::class);

        // Calculate total fleet value (sum of ship costs)
        $playerFleetValue = $this->calculateFleetValue($playerFleet);

        // Determine battle size tier:
        // Level 1 (89%): Normal battle
        // Level 2 (10%): Large battle
        // Level 3 (1%): Extra-large battle
        $battleSizeTier = $this->selectBattleSizeTier();

        // Determine player's maximum ship tier (for balancing)
        $playerMaxTier = $this->determinePlayerMaxShipTier($playerFleet);

        // Generate tech levels based on player's tech
        [$weaponTech, $shieldTech, $armorTech] = $this->generateNPCTechLevels($playerService, $npcType);

        // Create NPC player service
        $npcPlayer = new NPCPlayerService($npcType, $weaponTech, $shieldTech, $armorTech);

        // Generate fleet composition based on tier
        $npcFleet = $this->generateFleetComposition($playerFleetValue, $npcType, $battleSizeTier, $playerMaxTier);

        return [
            'fleet' => $npcFleet,
            'player' => $npcPlayer,
        ];
    }

    /**
     * Calculate the total value of a fleet (sum of ship costs).
     *
     * @param UnitCollection $fleet
     * @return int
     */
    private function calculateFleetValue(UnitCollection $fleet): int
    {
        $totalValue = 0;

        foreach ($fleet->units as $unit) {
            $shipCost = $unit->unitObject->price->resources->sum();
            $totalValue += $shipCost * $unit->amount;
        }

        return (int)$totalValue;
    }

    /**
     * Determine the maximum ship tier in the player's fleet.
     * This ensures NPCs don't spawn ships significantly more advanced than what the player has.
     *
     * NOTE: colony_ship, solar_satellite, and recycler are EXCLUDED from tier calculation
     * because they never appear in expedition battles.
     *
     * Ship tiers (combat/cargo ships only):
     * - Tier 1: light_fighter, heavy_fighter, cruiser, small_cargo, large_cargo, espionage_probe
     * - Tier 2: battle_ship, battlecruiser, bomber
     * - Tier 3: destroyer
     * - Tier 4: deathstar
     *
     * @param UnitCollection $playerFleet
     * @return int Maximum tier (1-4)
     */
    private function determinePlayerMaxShipTier(UnitCollection $playerFleet): int
    {
        $maxTier = 1; // Default to tier 1 (basic ships)

        foreach ($playerFleet->units as $unit) {
            $machineName = $unit->unitObject->machine_name;

            // Skip non-combat support ships that never appear in expeditions
            if (in_array($machineName, ['colony_ship', 'solar_satellite', 'recycler'])) {
                continue;
            }

            // Tier 4: Deathstar (most advanced)
            if ($machineName === 'deathstar') {
                return 4; // Maximum tier, no need to check further
            }

            // Tier 3: Destroyer
            if ($machineName === 'destroyer') {
                $maxTier = max($maxTier, 3);
            }

            // Tier 2: Advanced combat ships
            if (in_array($machineName, ['battle_ship', 'battlecruiser', 'bomber'])) {
                $maxTier = max($maxTier, 2);
            }
        }

        return $maxTier;
    }

    /**
     * Generate NPC tech levels based on player's tech.
     *
     * Official OGame formula:
     * Pirates: Player tech - 3 levels (minimum 0)
     * Aliens: Player tech + 3 levels
     *
     * Note: Each tech level = +10% effectiveness, so ±3 levels = ±30% effectiveness
     *
     * Examples:
     * - Level 10 player: Pirates have 7, Aliens have 13
     * - Level 0 player: Pirates have 0, Aliens have 3
     *
     * @param PlayerService $playerService
     * @param string $npcType
     * @return array{int, int, int} [weapon, shield, armor]
     */
    private function generateNPCTechLevels(PlayerService $playerService, string $npcType): array
    {
        $playerWeapon = $playerService->getResearchLevel('weapon_technology');
        $playerShield = $playerService->getResearchLevel('shielding_technology');
        $playerArmor = $playerService->getResearchLevel('armor_technology');

        if ($npcType === 'pirate') {
            // Pirates: Player tech - 3 levels (minimum 0)
            $weaponTech = max(0, $playerWeapon - 3);
            $shieldTech = max(0, $playerShield - 3);
            $armorTech = max(0, $playerArmor - 3);
        } else {
            // Aliens: Player tech + 3 levels
            $weaponTech = $playerWeapon + 3;
            $shieldTech = $playerShield + 3;
            $armorTech = $playerArmor + 3;
        }

        return [$weaponTech, $shieldTech, $armorTech];
    }

    /**
     * Select battle size tier based on OGame probabilities.
     *
     * Level 1 (89%): Normal battle
     * Level 2 (10%): Large battle
     * Level 3 (1%): Extra-large battle
     *
     * @return int Battle size tier (1, 2, or 3)
     */
    private function selectBattleSizeTier(): int
    {
        $random = random_int(1, 100);

        if ($random <= 89) {
            return 1; // Normal (89%)
        } elseif ($random <= 99) {
            return 2; // Large (10%)
        } else {
            return 3; // Extra-large (1%)
        }
    }

    /**
     * Generate fleet composition based on official OGame specifications.
     *
     * Official OGame fleet composition with variance:
     * Pirates (always round DOWN):
     * - Normal: 30% ±3% + 5 Light Fighters
     * - Large: 50% ±5% + 3 Cruisers
     * - Very Large: 80% ±8% + 2 Battleships
     *
     * Aliens (always round UP):
     * - Normal: 40% ±4% + 5 Heavy Fighters
     * - Large: 60% ±6% + 3 Battlecruisers
     * - Very Large: 90% ±9% + 2 Destroyers
     *
     * @param int $playerFleetValue Total value of player's fleet
     * @param string $npcType 'pirate' or 'alien'
     * @param int $battleSizeTier Battle size (1, 2, or 3)
     * @param int $playerMaxTier Player's maximum ship tier (1-4)
     * @return UnitCollection
     */
    private function generateFleetComposition(int $playerFleetValue, string $npcType, int $battleSizeTier, int $playerMaxTier): UnitCollection
    {
        $objectService = app(ObjectService::class);
        $npcFleet = new UnitCollection();

        // Determine base percentage, variance, and bonus ships based on tier and type
        if ($battleSizeTier === 1) {
            // Level 1 (89%): Normal battle
            $basePercentage = $npcType === 'pirate' ? 0.30 : 0.40;
            $variance = $npcType === 'pirate' ? 0.03 : 0.04;
            $bonusShipType = $npcType === 'pirate' ? 'light_fighter' : 'heavy_fighter';
            $bonusShipCount = 5;
        } elseif ($battleSizeTier === 2) {
            // Level 2 (10%): Large battle
            $basePercentage = $npcType === 'pirate' ? 0.50 : 0.60;
            $variance = $npcType === 'pirate' ? 0.05 : 0.06;
            $bonusShipType = $npcType === 'pirate' ? 'cruiser' : 'battlecruiser';
            $bonusShipCount = 3;
        } else {
            // Level 3 (1%): Very large battle
            $basePercentage = $npcType === 'pirate' ? 0.80 : 0.90;
            $variance = $npcType === 'pirate' ? 0.08 : 0.09;
            $bonusShipType = $npcType === 'pirate' ? 'battle_ship' : 'destroyer';
            $bonusShipCount = 2;
        }

        // Apply random variance within range: base ± variance
        // Generate random value between -1.0 and 1.0
        $randomVariance = (random_int(-100, 100) / 100) * $variance;
        $finalPercentage = $basePercentage + $randomVariance;

        // Calculate fleet value with proper rounding
        if ($npcType === 'pirate') {
            // Pirates: Always round DOWN
            $npcFleetValue = (int)floor($playerFleetValue * $finalPercentage);
        } else {
            // Aliens: Always round UP
            $npcFleetValue = (int)ceil($playerFleetValue * $finalPercentage);
        }

        // Generate main fleet with mixed ship types
        $npcFleet = $this->generateMixedFleet($npcFleetValue, $npcType, $playerMaxTier);

        // Add bonus ships ON TOP of the base percentage
        // Official OGame: "30% ±3% + 5 Light Fighters" means bonus ships are additional
        try {
            $bonusShip = $objectService->getShipObjectByMachineName($bonusShipType);
            $npcFleet->addUnit($bonusShip, $bonusShipCount);
        } catch (\Exception $e) {
            // Bonus ship not found, continue without it
        }

        return $npcFleet;
    }

    /**
     * Generate a fleet of ships based on allocated value.
     *
     * Official OGame pattern: NPCs have mostly Large Cargo with just a few combat ships.
     * Typical composition:
     * - Bonus ships (added separately - 5 Light/Heavy Fighters, 3 Cruisers/Battlecruisers, or 2 Battleships/Destroyers)
     * - 1-2 additional random combat ships
     * - 1 Espionage Probe (sometimes)
     * - Rest filled with Large Cargo
     *
     * @param int $allocatedValue Total value to spend on ships
     * @param string $npcType 'pirate' or 'alien'
     * @param int $playerMaxTier Player's maximum ship tier (1-4)
     * @return UnitCollection
     */
    private function generateMixedFleet(int $allocatedValue, string $npcType, int $playerMaxTier): UnitCollection
    {
        $objectService = app(ObjectService::class);
        $npcFleet = new UnitCollection();
        $remainingValue = $allocatedValue;

        // Add 1-2 random combat ships (optional, 70% chance)
        if (random_int(1, 100) <= 70) {
            $combatShipCount = random_int(1, 2);

            // Select combat ships based on NPC type and player tier
            if ($npcType === 'pirate') {
                $possibleCombatShips = ['light_fighter', 'heavy_fighter', 'cruiser'];
                if ($playerMaxTier >= 2) {
                    $possibleCombatShips[] = 'battle_ship';
                }
            } else {
                // Aliens get stronger ships
                $possibleCombatShips = ['heavy_fighter', 'cruiser', 'battlecruiser'];
                if ($playerMaxTier >= 2) {
                    $possibleCombatShips[] = 'battle_ship';
                }
                if ($playerMaxTier >= 3) {
                    $possibleCombatShips[] = 'destroyer';
                    $possibleCombatShips[] = 'reaper';
                    $possibleCombatShips[] = 'pathfinder';
                }
            }

            // Add 1-2 combat ships
            for ($i = 0; $i < $combatShipCount; $i++) {
                $shipType = $possibleCombatShips[array_rand($possibleCombatShips)];
                try {
                    $ship = $objectService->getShipObjectByMachineName($shipType);
                    $shipCost = $ship->price->resources->sum();
                    if ($remainingValue >= $shipCost) {
                        $npcFleet->addUnit($ship, 1);
                        $remainingValue -= $shipCost;
                    }
                } catch (\Exception $e) {
                    // Ship not found, skip
                }
            }
        }

        // Add 1 Espionage Probe (optional, 50% chance)
        if (random_int(1, 100) <= 50) {
            try {
                $probe = $objectService->getShipObjectByMachineName('espionage_probe');
                $probeCost = $probe->price->resources->sum();
                if ($remainingValue >= $probeCost) {
                    $npcFleet->addUnit($probe, 1);
                    $remainingValue -= $probeCost;
                }
            } catch (\Exception $e) {
                // Probe not found, skip
            }
        }

        // Fill the rest with Large Cargo (this is the bulk of the fleet)
        try {
            $largeCargo = $objectService->getShipObjectByMachineName('large_cargo');
            $cargoCost = $largeCargo->price->resources->sum();
            $cargoCount = (int)floor($remainingValue / $cargoCost);

            if ($cargoCount > 0) {
                $npcFleet->addUnit($largeCargo, $cargoCount);
            }
        } catch (\Exception $e) {
            // Large Cargo not found, try Small Cargo as fallback
            try {
                $smallCargo = $objectService->getShipObjectByMachineName('small_cargo');
                $cargoCost = $smallCargo->price->resources->sum();
                $cargoCount = (int)floor($remainingValue / $cargoCost);

                if ($cargoCount > 0) {
                    $npcFleet->addUnit($smallCargo, $cargoCount);
                }
            } catch (\Exception $e) {
                // Even small cargo failed
            }
        }

        return $npcFleet;
    }
}
