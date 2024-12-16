<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

/**
 * Class BattleEngine.
 *
 * This class is responsible for handling the battle logic in the game, used primarily
 * by the AttackMission class.
 *
 * @package OGame\GameMissions\BattleEngine
 */
class BattleEngine
{
    private UnitCollection $attackerFleet;
    private PlayerService $attackerPlayer;
    private PlanetService $defenderPlanet;

    /**
     * @var LootService The service used to calculate the loot gained from a battle.
     */
    private LootService $lootService;

    /**
     * @var int The percentage of loot that is gained from a battle.
     * TODO: make this configurable. For inactive players loot percentage could be up to 75% for example.
     */
    private int $lootPercentage = 50;

    /**
     * @var SettingsService The settings service.
     */
    private SettingsService $settings;

    /**
     * BattleEngine constructor.
     *
     * @param UnitCollection $attackerFleet The fleet of the attacker player.
     * @param PlayerService $attackerPlayer The attacker player.
     * @param PlanetService $defenderPlanet The planet of the defender player.
     * @param SettingsService $settings The settings service.
     */
    public function __construct(UnitCollection $attackerFleet, PlayerService $attackerPlayer, PlanetService $defenderPlanet, SettingsService $settings)
    {
        $this->attackerFleet = $attackerFleet;
        $this->attackerPlayer = $attackerPlayer;
        $this->defenderPlanet = $defenderPlanet;

        $this->lootService = new LootService($this->attackerFleet, $this->attackerPlayer, $this->defenderPlanet, $this->lootPercentage);

        $this->settings = $settings;
    }

    /**
     * Simulate a battle between two players.
     *
     * @return BattleResult Information about the battle result.
     */
    public function simulateBattle(): BattleResult
    {
        $result = new BattleResult();

        // Initialize the battle result object with the attacker and defender information.
        $result->lootPercentage = $this->lootPercentage;
        $result->attackerWeaponLevel = $this->attackerPlayer->getResearchLevel('weapon_technology');
        $result->attackerShieldLevel = $this->attackerPlayer->getResearchLevel('shielding_technology');
        $result->attackerArmorLevel = $this->attackerPlayer->getResearchLevel('armor_technology');

        $result->defenderWeaponLevel = $this->defenderPlanet->getPlayer()->getResearchLevel('weapon_technology');
        $result->defenderShieldLevel = $this->defenderPlanet->getPlayer()->getResearchLevel('shielding_technology');
        $result->defenderArmorLevel = $this->defenderPlanet->getPlayer()->getResearchLevel('armor_technology');

        $result->attackerUnitsStart = clone $this->attackerFleet;
        $result->attackerUnitsResult = clone $this->attackerFleet;
        $result->defenderUnitsStart = new UnitCollection();
        $result->defenderUnitsStart->addCollection($this->defenderPlanet->getShipUnits());
        $result->defenderUnitsStart->addCollection($this->defenderPlanet->getDefenseUnits());
        $result->defenderUnitsResult = clone $result->defenderUnitsStart;

        // Execute the battle rounds, this will handle the actual combat logic.
        $result->rounds = $this->fightBattleRounds($result);

        // Get the result of the battle.
        if (count($result->rounds) > 0) {
            // Take the remaining ships in the last round as the result.
            $round = end($result->rounds);
            $result->attackerUnitsResult = $round->attackerShips;
            $result->defenderUnitsResult = $round->defenderShips;
        } else {
            // If no rounds were fought, the result is the same as the start.
            $result->attackerUnitsResult = $result->attackerUnitsStart;
            $result->defenderUnitsResult = $result->defenderUnitsStart;
        }

        // Calculate the resources lost by the attacker and defender.
        // Deduct defender's lost units from the defenders planet.
        $result->attackerUnitsLost = clone $result->attackerUnitsStart;
        $result->attackerUnitsLost->subtractCollection($result->attackerUnitsResult);
        $result->attackerResourceLoss = $result->attackerUnitsLost->toResources();

        $result->defenderUnitsLost = clone $result->defenderUnitsStart;
        $result->defenderUnitsLost->subtractCollection($result->defenderUnitsResult);
        $result->defenderResourceLoss = $result->defenderUnitsLost->toResources();

        // Determine winner of battle.
        if ($result->defenderUnitsResult->getAmount() === 0) {
            // ---
            // [WIN] - If attacker wins:
            // ---
            // Check if the attacker has enough cargo capacity to carry the loot.
            // If not, reduce the loot to the cargo capacity.
            $result->loot = $this->lootService->calculateLootCapacityConstrained();
        } else {
            $result->loot = new Resources(0, 0, 0, 0);
        }

        // Calculate debris.
        $result->debris = $this->calculateDebris($result->attackerUnitsLost, $result->defenderUnitsLost);

        // Determine if a moon already exists for defender's planet.
        $result->moonExisted = $this->defenderPlanet->hasMoon();

        // Calculate moon percentage if a moon does not exist yet.
        if ($result->moonExisted) {
            $result->moonChance = 0;
            $result->moonCreated = false;
        } else {
            $result->moonChance = $this->calculateMoonChance($result->debris);
            $result->moonCreated = $this->rollMoonCreation($result->moonChance);
        }

        return $result;
    }

    /**
     * Fight the battle in max 6 rounds.
     *
     * @param BattleResult $result
     * @return array<BattleResultRound>
     */
    private function fightBattleRounds(BattleResult $result): array
    {
        $rounds = [];

        // Convert attacker and defender units to BattleUnit objects to keep track of hull plating and shields.
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            // Create new object for each unique unit in the fleet.
            $structuralIntegrity = $unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue;
            $attackPower = $unit->unitObject->properties->attack->calculate($this->attackerPlayer)->totalValue;
            $unitObject = new BattleUnit($unit->unitObject, $structuralIntegrity, $shieldPoints, $attackPower);

            for ($i = 0; $i < $unit->amount; $i++) {
                // Clone the unit object for each individual entry of this ship add it to the array.
                $attackerUnits[] = clone $unitObject;
            }
        }

        $defenderUnits = [];
        foreach ($result->defenderUnitsStart->units as $unit) {
            // Create new object for each unique unit in the fleet.
            $structuralIntegrity = $unit->unitObject->properties->structural_integrity->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $attackPower = $unit->unitObject->properties->attack->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $unitObject = new BattleUnit($unit->unitObject, $structuralIntegrity, $shieldPoints, $attackPower);

            for ($i = 0; $i < $unit->amount; $i++) {
                // Clone the unit object for each individual entry of this ship add it to the array.
                $defenderUnits[] = clone $unitObject;
            }
        }

        $roundNumber = 0;
        $attackerRemainingShips = clone $result->attackerUnitsStart;
        $defenderRemainingShips = clone $result->defenderUnitsStart;
        $attackerLosses = new UnitCollection();
        $defenderLosses = new UnitCollection();
        while ($roundNumber < 6  && count($attackerUnits) > 0 && count($defenderUnits) > 0) {
            $roundNumber++;
            $round = new BattleResultRound();
            $round->defenderLossesInThisRound = new UnitCollection();
            $round->attackerLossesInThisRound = new UnitCollection();
            $round->absorbedDamageAttacker = 0;
            $round->absorbedDamageDefender = 0;

            // Let the attacker attack the defender.
            foreach ($attackerUnits as $unit) {
                // Every single unit attacks a random unit from the defender's units.
                // If the attacker has rapidfire against the defender and succesfully rolled a dice,
                // the attacker can attack a random unit again.
                do {
                    $targetUnitKey = array_rand($defenderUnits);
                    $targetUnit = $defenderUnits[$targetUnitKey];

                    $rapidfire = $this->attackUnit(true, $round, $unit, $targetUnit);
                } while ($rapidfire);
            }

            // Let the defender attack the attacker.
            foreach ($defenderUnits as $unit) {
                // If the attacker has rapidfire against the defender and succesfully rolled a dice,
                // the attacker can attack a random unit again.
                do {
                    $targetUnitKey = array_rand($attackerUnits);
                    $targetUnit = $attackerUnits[$targetUnitKey];

                    $rapidfire = $this->attackUnit(false, $round, $unit, $targetUnit);
                } while ($rapidfire);
            }

            // After all units have attacked each other, clean up the round. This removes destroyed units
            // and applies shield regeneration.
            $this->cleanupRound($round, $attackerUnits, $defenderUnits);

            // Subtract losses from the attacker and defender units.
            $attackerRemainingShips->subtractCollection($round->attackerLossesInThisRound, false);
            $defenderRemainingShips->subtractCollection($round->defenderLossesInThisRound, false);

            // Update the total losses for the attacker and defender.
            $attackerLosses->addCollection($round->attackerLossesInThisRound);
            $defenderLosses->addCollection($round->defenderLossesInThisRound);

            // Clone the losses to the round object to keep track of the total losses at round point-in-time.
            $round->attackerLosses = clone $attackerLosses;
            $round->defenderLosses = clone $defenderLosses;

            // Update the remaining ships for the next round.
            $round->attackerShips = clone $attackerRemainingShips;
            $round->defenderShips = clone $defenderRemainingShips;

            // Add the round to the list of rounds.
            $rounds[] = $round;
        }

        return $rounds;
    }

    /**
     * Let one unit attack another unit and apply the damage to the defending unit.
     *
     * @param bool $isAttacker True if the attacker is attacking, false if the defender is attacking. This is used
     * to determine which statistics to update.
     * @param BattleResultRound $round
     * @param BattleUnit $attacker
     * @param BattleUnit $defender
     *
     * @return bool True if the attacker has rapidfire against the defender and can attack again, false otherwise.
     */
    private function attackUnit(bool $isAttacker, BattleResultRound $round, BattleUnit $attacker, BattleUnit $defender): bool
    {
        // Calculate the damage dealt by the attacker to the defender.
        $damage = $attacker->attackPower;
        $shieldAbsorption = 0;

        if ($damage < (0.01 * $defender->currentShieldPoints)) {
            // If the damage is less than 1% of the shield points, the attack is bounced and no damage is dealt.
            return false;
        }

        if ($defender->currentShieldPoints > 0 && $damage <= $defender->currentShieldPoints) {
            // If the defender has a shield, first apply damage to the shield.
            $shieldAbsorption = $damage;
            $defender->currentShieldPoints -= $damage;
        } elseif ($defender->currentShieldPoints > 0 && $damage > $defender->currentShieldPoints) {
            // If the shield is destroyed, apply the remaining damage to the hull plating.
            $shieldAbsorption = $defender->currentShieldPoints;
            $defender->currentHullPlating -= $damage - $defender->currentShieldPoints;
            $defender->currentShieldPoints = 0;
        } else {
            // No shield, apply damage directly to the hull plating.
            $defender->currentHullPlating -= $damage;
        }

        if ($isAttacker) {
            $round->hitsAttacker += 1;
            $round->fullStrengthAttacker += $damage;
            $round->absorbedDamageDefender += $shieldAbsorption;
        } else {
            $round->hitsDefender += 1;
            $round->fullStrengthDefender += $damage;
            $round->absorbedDamageAttacker += $shieldAbsorption;
        }

        // Rapidfire: if the attacker has a rapidfire bonus against the defender, roll a dice to see if the
        // attacker can attack again.
        if ($attacker->unitObject->didSuccessfulRapidfire($defender->unitObject)) {
            // Rapidfire was successful, return true to indicate that the attacker can attack again.
            return true;
        }

        return false;
    }

    /**
     * Clean up the round after all units have attacked each other.
     *
     * This method handles:
     * - Removing destroyed units from the attacker and defender unit arrays.
     * - Rolling a dice for hull integrity < 70% of original if the unit is also destroyed.
     * - Applying shield regeneration.
     * - Calculate the total damage dealt by the attacker and defender and calculate shield absorption stats.
     *
     * @param BattleResultRound $round.
     * @param array<BattleUnit> $attackerUnits
     * @param array<BattleUnit> $defenderUnits
     * @return void
     */
    private function cleanupRound(BattleResultRound $round, array &$attackerUnits, array &$defenderUnits): void
    {
        // Cleanup attacker units.
        foreach ($attackerUnits as $key => $unit) {
            if ($unit->currentHullPlating <= 0) {
                // Remove destroyed units from the array.
                $round->attackerLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($attackerUnits[$key]);
            } elseif ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->attackerLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($attackerUnits[$key]);
            } else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }

        // Cleanup defender units.
        foreach ($defenderUnits as $key => $unit) {
            if ($unit->currentHullPlating <= 0) {
                // Remove destroyed units from the array.
                $round->defenderLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            } elseif ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->defenderLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            } else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }
    }

    /**
     * Calculate the debris field based on the units lost in the battle.
     *
     * @param UnitCollection $attackerUnitsLost
     * @param UnitCollection $defenderUnitsLost
     * @return Resources
     */
    private function calculateDebris(UnitCollection $attackerUnitsLost, UnitCollection $defenderUnitsLost): Resources
    {
        $metal = 0;
        $crystal = 0;
        $deuterium = 0;

        $shipsToDebrisPercentage = $this->settings->debrisFieldFromShips();
        $defenseToDebrisPercentage = $this->settings->debrisFieldFromDefense();
        $deuteriumOn = $this->settings->debrisFieldDeuteriumOn();

        // Combine the attacker and defender losses to calculate the debris.
        $allUnitsLost = new UnitCollection();
        $allUnitsLost->addCollection($attackerUnitsLost);
        $allUnitsLost->addCollection($defenderUnitsLost);

        // Handle attacker losses.
        foreach ($allUnitsLost->units as $unit) {
            if ($unit->unitObject->type === GameObjectType::Ship) {
                if ($shipsToDebrisPercentage > 0) {
                    $metal += floor(($unit->unitObject->price->resources->metal->get() * $unit->amount) * ($shipsToDebrisPercentage / 100));
                    $crystal += floor(($unit->unitObject->price->resources->crystal->get() * $unit->amount) * ($shipsToDebrisPercentage / 100));
                    if ($deuteriumOn) {
                        $deuterium += floor(($unit->unitObject->price->resources->deuterium->get() * $unit->amount) * ($shipsToDebrisPercentage / 100));
                    }
                }
            } elseif ($unit->unitObject->type === GameObjectType::Defense) {
                if ($defenseToDebrisPercentage > 0) {
                    $metal += floor(($unit->unitObject->price->resources->metal->get() * $unit->amount) * ($defenseToDebrisPercentage / 100));
                    $crystal += floor(($unit->unitObject->price->resources->crystal->get() * $unit->amount) * ($defenseToDebrisPercentage / 100));
                    if ($deuteriumOn) {
                        $deuterium += floor(($unit->unitObject->price->resources->deuterium->get() * $unit->amount) * ($defenseToDebrisPercentage / 100));
                    }
                }
            }
        }

        return new Resources($metal, $crystal, $deuterium, 0);
    }

    /**
     * Calculate moon chance based on the debris field.
     *
     * @param Resources $debris
     * @return int
     */
    private function calculateMoonChance(Resources $debris): int
    {
        $max_moon_chance = $this->settings->maximumMoonChance();

        // Every 100k debris results in 1% moon chance, up to a maximum
        // of max moon chance configured in server settings.
        $moon_chance = floor(($debris->sum()) / 100000);
        if ($moon_chance > $max_moon_chance) {
            $moon_chance = $max_moon_chance;
        }

        return (int)$moon_chance;
    }

    /**
     * Roll the dice to see if a moon is created based on the moon chance.
     *
     * @param int $moonChance
     * @return bool
     */
    private function rollMoonCreation($moonChance): bool
    {
        $dice = rand(1, 100);
        return $dice <= $moonChance;
    }
}
