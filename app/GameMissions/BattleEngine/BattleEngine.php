<?php

namespace OGame\GameMissions\BattleEngine;

use Exception;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

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
     * BattleEngine constructor.
     *
     * @param UnitCollection $attackerFleet The fleet of the attacker player.
     * @param PlayerService $attackerPlayer The attacker player.
     * @param PlanetService $defenderPlanet The planet of the defender player.
    */
    public function __construct(UnitCollection $attackerFleet, PlayerService $attackerPlayer, PlanetService $defenderPlanet)
    {
        $this->attackerFleet = $attackerFleet;
        $this->attackerPlayer = $attackerPlayer;
        $this->defenderPlanet = $defenderPlanet;

        $this->lootService = new LootService($this->attackerFleet, $this->attackerPlayer, $this->defenderPlanet, $this->lootPercentage);
    }

    /**
     * Simulate a battle between two players.
     *
     * @return BattleResult Information about the battle result.
     */
    public function simulateBattle(): BattleResult
    {
        $result = new BattleResult();

        // Set battle properties that are already known.
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
        // ---

        $result->rounds = $this->executeRounds($result);

        if (count($result->rounds) > 0) {
            // Take the remaining ships in the last round as the result.
            $round = end($result->rounds);
            $result->attackerUnitsResult = $round->attackerShips;
            $result->defenderUnitsResult = $round->defenderShips;
        }
        else {
            // If no rounds were fought, the result is the same as the start.
            $result->attackerUnitsResult = $result->attackerUnitsStart;
            $result->defenderUnitsResult = $result->defenderUnitsStart;
        }

        // Check if defender still has units left. If not, attacker wins.
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

        return $result;
    }

    /**
     * Execute the battle rounds.
     *
     * @param BattleResult $result
     * @return array<BattleResultRound>
     * @throws Exception
     */
    private function executeRounds(BattleResult $result): array {
        $rounds = [];

        // Convert attacker and defender units to BattleUnit objects to keep track of hitpoints/shields etc.
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
        while ($roundNumber < 6  && count($attackerUnits) > 0 && count($defenderUnits) > 0) {
            $roundNumber++;
            $round = new BattleResultRound();
            $round->defenderLossesInThisRound = new UnitCollection();
            $round->defenderLosses = new UnitCollection();
            $round->attackerLossesInThisRound = new UnitCollection();
            $round->attackerLosses = new UnitCollection();
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
                }
                while ($rapidfire);
            }

            // Let the defender attack the attacker.
            foreach ($defenderUnits as $unit) {
                // If the attacker has rapidfire against the defender and succesfully rolled a dice,
                // the attacker can attack a random unit again.
                do {
                    $targetUnitKey = array_rand($attackerUnits);
                    $targetUnit = $attackerUnits[$targetUnitKey];

                    $rapidfire = $this->attackUnit(false, $round, $unit, $targetUnit);
                }
                while ($rapidfire);
            }

            // TODO: add logic here to loop through the units and:
            // - remove destroyed units
            // - roll a dice for hull integrity < 70% of original if the unit is also destroyed
            // - apply shield regeneration
            $this->cleanupRound($round, $attackerUnits, $defenderUnits);

            // Subtract losses from the attacker and defender units.
            $attackerRemainingShips->subtractCollection($round->attackerLossesInThisRound, false);
            $defenderRemainingShips->subtractCollection($round->defenderLossesInThisRound, false);
            $round->attackerShips = clone $attackerRemainingShips;
            $round->defenderShips = clone $defenderRemainingShips;
            $rounds[] = $round;
            // ---- END ROUND
        }

        return $rounds;
    }

    /**
     * Let one unit attack another unit and apply the damage to the defender.
     *
     * @param bool $isAttacker True if the attacker is attacking, false if the defender is attacking. This is used
     * to determine which statistics to update.
     * @param BattleResultRound $round
     * @param BattleUnit $attacker
     * @param BattleUnit $defender
     * @return bool True if the attacker has rapidfire against the defender and can attack again, false otherwise.
     */
    private function attackUnit(bool $isAttacker, BattleResultRound $round, BattleUnit $attacker, BattleUnit $defender): bool
    {
        // Calculate the damage dealt by the attacker to the defender.
        $damage = $attacker->currentAttackPower;
        $shieldAbsorption = 0;

        if ($damage < (0.01 * $defender->currentShieldPoints)) {
            // If the damage is less than 1% of the shield points, the attack is bounced and no damage is dealt.
            return false;
        }

        if ($defender->currentShieldPoints > 0 && $damage <= $defender->currentShieldPoints) {
            // If the defender has a shield, first apply damage to the shield.
            $shieldAbsorption = $damage;
            $defender->currentShieldPoints -= $damage;
        }
        else if ($defender->currentShieldPoints > 0 && $damage > $defender->currentShieldPoints) {
            // If the shield is destroyed, apply the remaining damage to the hull plating.
            $shieldAbsorption = $defender->currentShieldPoints;
            $defender->currentHullPlating -= $damage - $defender->currentShieldPoints;
            $defender->currentShieldPoints = 0;
        }
        else {
            // No shield, apply damage directly to the hull plating.
            $defender->currentHullPlating -= $damage;
        }

        if ($isAttacker) {
            $round->hitsAttacker += 1;
            $round->fullStrengthAttacker += $damage;
            $round->absorbedDamageDefender += $shieldAbsorption;
        }
        else {
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
            }
            else if ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->attackerLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($attackerUnits[$key]);
            }
            else {
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
            }
            else if ($unit->damagedHullExplosion()) {
                // Hull was damaged and dice roll was successful, destroy the unit.
                $round->defenderLossesInThisRound->addUnit($unit->unitObject, 1);
                unset($defenderUnits[$key]);
            }
            else {
                // Apply shield regeneration.
                $unit->currentShieldPoints = $unit->originalShieldPoints;
            }
        }
    }
}