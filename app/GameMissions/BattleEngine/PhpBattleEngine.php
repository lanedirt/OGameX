<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\Models\BattleResultRound;
use OGame\GameMissions\BattleEngine\Models\BattleUnit;
use OGame\GameObjects\Models\Units\UnitCollection;

/**
 * Class BattleEngine.
 *
 * This class is responsible for handling the battle logic in the game, used primarily
 * by the AttackMission class. This is the PHP version of the BattleEngine which is slower
 * and less memory efficientthan the Rust version but has less dependencies and is easier to debug.
 *
 * @package OGame\GameMissions\BattleEngine
 */
class PhpBattleEngine extends BattleEngine
{
    /**
     * Fight the battle in max 6 rounds.
     *
     * @param BattleResult $result
     * @return array<BattleResultRound>
     */
    protected function fightBattleRounds(BattleResult $result): array
    {
        $rounds = [];

        // Convert attacker and defender units to BattleUnit objects to keep track of hull plating and shields.
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            // Create new object for each unique unit in the fleet.
            $structuralIntegrity = $unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue;
            $attackPower = $unit->unitObject->properties->attack->calculate($this->attackerPlayer)->totalValue;
            $unitObject = new BattleUnit($unit->unitObject, $structuralIntegrity, $shieldPoints, $attackPower, $this->attackerFleetMissionId, $this->attackerOwnerId);

            for ($i = 0; $i < $unit->amount; $i++) {
                // Clone the unit object for each individual entry of this ship add it to the array.
                $attackerUnits[] = clone $unitObject;
            }
        }

        $defenderUnits = [];
        // Create BattleUnits for each defending fleet separately to preserve ownership and tech levels
        foreach ($this->defenders as $defenderFleet) {
            foreach ($defenderFleet->units->units as $unit) {
                // Create new object for each unique unit type in this fleet
                // Use THIS fleet owner's tech levels for calculations
                $structuralIntegrity = $unit->unitObject->properties->structural_integrity->calculate($defenderFleet->player)->totalValue;
                $shieldPoints = $unit->unitObject->properties->shield->calculate($defenderFleet->player)->totalValue;
                $attackPower = $unit->unitObject->properties->attack->calculate($defenderFleet->player)->totalValue;

                $unitObject = new BattleUnit(
                    $unit->unitObject,
                    $structuralIntegrity,
                    $shieldPoints,
                    $attackPower,
                    $defenderFleet->fleetMissionId,  // Track which fleet this unit belongs to
                    $defenderFleet->ownerId          // Track which player owns this unit
                );

                // Create individual BattleUnit for each ship
                for ($i = 0; $i < $unit->amount; $i++) {
                    $defenderUnits[] = clone $unitObject;
                }
            }
        }

        // Hamill Manoeuvre: General class Light Fighters have a chance to destroy one Deathstar before battle
        $this->checkHamillManoeuvre($result, $attackerUnits, $defenderUnits);

        $roundNumber = 0;
        $attackerRemainingShips = clone $result->attackerUnitsStart;
        $defenderRemainingShips = clone $result->defenderUnitsStart;
        $attackerLosses = new UnitCollection();
        $defenderLosses = new UnitCollection();
        while ($roundNumber < 6  && count($attackerUnits) > 0 && count($defenderUnits) > 0) {
            $roundNumber++;
            $round = new BattleResultRound();
            $round->defenderLossesInRound = new UnitCollection();
            $round->attackerLossesInRound = new UnitCollection();
            $round->absorbedDamageAttacker = 0;
            $round->absorbedDamageDefender = 0;

            // Let the attacker attack the defender.
            foreach ($attackerUnits as $unit) {
                // Every single unit attacks a random unit from the defender's units.
                // If the attacker has rapidfire against the defender and successfully rolled a dice,
                // the attacker can attack a random unit again.
                do {
                    $targetUnitKey = array_rand($defenderUnits);
                    $targetUnit = $defenderUnits[$targetUnitKey];

                    $rapidfire = $this->attackUnit(true, $round, $unit, $targetUnit);
                } while ($rapidfire);
            }

            // Let the defender attack the attacker.
            foreach ($defenderUnits as $unit) {
                // If the attacker has rapidfire against the defender and successfully rolled a dice,
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
            $attackerRemainingShips->subtractCollection($round->attackerLossesInRound);
            $defenderRemainingShips->subtractCollection($round->defenderLossesInRound);

            // Update the total losses for the attacker and defender.
            $attackerLosses->addCollection($round->attackerLossesInRound);
            $defenderLosses->addCollection($round->defenderLossesInRound);

            // Clone the losses to the round object to keep track of the total losses at round point-in-time.
            $round->attackerLosses = clone $attackerLosses;
            $round->defenderLosses = clone $defenderLosses;

            // Update the ships remaining at the end of this round.
            $round->attackerShips = clone $attackerRemainingShips;
            $round->defenderShips = clone $defenderRemainingShips;

            // Add the round to the list of rounds.
            $rounds[] = $round;
        }

        // Populate per-fleet defender results by scanning surviving units
        foreach ($result->defenderFleetResults as $fleetResult) {
            // Count surviving units for this fleet
            foreach ($defenderUnits as $battleUnit) {
                if ($battleUnit->fleetMissionId === $fleetResult->fleetMissionId) {
                    $fleetResult->unitsResult->addUnit($battleUnit->unitObject, 1);
                }
            }

            // Calculate losses for this fleet
            $fleetResult->unitsLost = clone $fleetResult->unitsStart;
            $fleetResult->unitsLost->subtractCollection($fleetResult->unitsResult);

            // Check if completely destroyed
            $fleetResult->completelyDestroyed = $fleetResult->unitsResult->getAmount() === 0;
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

        if ($damage < (0.01 * $defender->originalShieldPoints)) {
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

        // If the defender's hull integrity is less than 70%, the unit can explode randomly.
        if ($defender->damagedHullExplosion()) {
            // Hull was damaged and dice roll was successful, destroy the unit.
            $defender->currentShieldPoints = 0;
            $defender->currentHullPlating = 0;
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
                $round->attackerLossesInRound->addUnit($unit->unitObject, 1);
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
                $round->defenderLossesInRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            } else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }
    }

    /**
     * Check and execute the Hamill Manoeuvre special ability.
     * General class Light Fighters have a small chance to instantly destroy one Deathstar before battle.
     *
     * @param BattleResult $result
     * @param array<BattleUnit> $attackerUnits
     * @param array<BattleUnit> $defenderUnits
     * @return void
     */
    private function checkHamillManoeuvre(BattleResult $result, array &$attackerUnits, array &$defenderUnits): void
    {
        // Check if attacker is General class
        $characterClassService = app(\OGame\Services\CharacterClassService::class);
        if (!$characterClassService->isGeneral($this->attackerPlayer->getUser())) {
            return;
        }

        // Check if attacker has at least one Light Fighter
        $hasLightFighter = false;
        foreach ($attackerUnits as $unit) {
            if ($unit->unitObject->machine_name === 'light_fighter') {
                $hasLightFighter = true;
                break;
            }
        }

        if (!$hasLightFighter) {
            return;
        }

        // Check if defender has at least one Deathstar
        $deathstarKey = null;
        foreach ($defenderUnits as $key => $unit) {
            if ($unit->unitObject->machine_name === 'deathstar') {
                $deathstarKey = $key;
                break;
            }
        }

        if ($deathstarKey === null) {
            return;
        }

        // Roll the dice for Hamill Manoeuvre
        $settings = app(\OGame\Services\SettingsService::class);
        $probability = $settings->hamillManoeuvreChance();
        $dice = random_int(1, $probability);

        if ($dice === 1) {
            // Hamill Manoeuvre triggered! Destroy one Deathstar
            $result->hamillManoeuvreTriggered = true;

            // Remove the Deathstar from defender units array (battle simulation)
            // This prevents it from participating in battle rounds
            unset($defenderUnits[$deathstarKey]);

            // NOTE: We do NOT remove it from defenderUnitsStart or defenderUnitsResult here.
            // The loss will be properly added to defenderUnitsLost in BattleEngine::simulateBattle() (line 142-145).
        }
    }
}
