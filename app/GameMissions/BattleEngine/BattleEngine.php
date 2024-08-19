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

        // Convert attacker fleet to individual unit array.
        // Key = unit id (int), value = structural integrity (int).
        // TODO: should actually include structural integrity, shield, and weapon damage.
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            // Create new object for each unit in the fleet.
            $hullPlating = $unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue;
            $attackPower = $unit->unitObject->properties->attack->calculate($this->attackerPlayer)->totalValue;
            $unitObject = new BattleUnit($unit->unitObject, $hullPlating, $shieldPoints, $attackPower);

            for ($i = 0; $i < $unit->amount; $i++) {
                // Clone the unit object for each unit in the fleet and add it to the array.
                $attackerUnits[] = clone $unitObject;
            }
        }

        $defenderUnits = [];
        foreach ($result->defenderUnitsStart->units as $unit) {
            // Create new object for each unit in the fleet.
            $hullPlating = $unit->unitObject->properties->structural_integrity->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $shieldPoints = $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $attackPower = $unit->unitObject->properties->attack->calculate($this->defenderPlanet->getPlayer())->totalValue;
            $unitObject = new BattleUnit($unit->unitObject, $hullPlating, $shieldPoints, $attackPower);

            for ($i = 0; $i < $unit->amount; $i++) {
                $defenderUnits[] = clone $unitObject;
            }
        }

        $roundNumber = 0;
        while ($roundNumber < 6  && count($attackerUnits) > 0 && count($defenderUnits) > 0) {
            $roundNumber++;

            $round = new BattleResultRound();
            $round->defenderLossesInThisRound = new UnitCollection();
            $round->defenderLosses = new UnitCollection();
            $round->attackerLossesInThisRound = new UnitCollection();
            $round->attackerLosses = new UnitCollection();
            $round->attackerShips = clone $result->attackerUnitsStart;
            $round->defenderShips = clone $result->defenderUnitsStart;
            $round->absorbedDamageAttacker = 0;
            $round->absorbedDamageDefender = 0;

            // Let the attacker attack the defender.
            foreach ($attackerUnits as $unit) {
                // If all defender units are destroyed, break the loop.
                if (count($defenderUnits) === 0) {
                    break;
                }

                // Every single unit attacks a random unit from the defender's units.
                // For now, we assume the attacker always kills the target.
                // Random target
                $targetUnitKey = array_rand($defenderUnits);
                $targetUnit = $defenderUnits[$targetUnitKey];

                $this->attackUnit($round, $unit, $targetUnit);

                $round->hitsAttacker += 1;

                // Add target defender unit to defender losses array.
                /*$round->defenderLosses->addUnit($targetUnitObject, 1);
                $round->defenderLossesInThisRound->addUnit($targetUnitObject, 1);

                // Unset the defender unit from the array.
                unset($defenderUnits[$targetUnitKey]);*/
            }

            // Let the defender attack the attacker.
            foreach ($defenderUnits as $unit) {
                // If all attacker units are destroyed, break the loop.
                if (count($attackerUnits) === 0) {
                    break;
                }

                // Every single unit attacks a random unit from the attacker's units.
                // For now, we assume the defender always kills the target.
                // Random target
                $targetUnitKey = array_rand($attackerUnits);
                $targetUnit = $attackerUnits[$targetUnitKey];

                $this->attackUnit($round, $unit, $targetUnit);

                $round->hitsDefender += 1;

                // Add target attacker unit to attacker losses array.
                /*$round->attackerLosses->addUnit($targetUnitObject, 1);
                $round->attackerLossesInThisRound->addUnit($targetUnitObject, 1);

                // TODO: implement shield damage absorption for attacker units.
                $round->absorbedDamageAttacker += 1;
                // TODO: implement full strength defender.
                $round->fullStrengthDefender += 1;

                $round->hitsDefender += 1;

                // Unset the attacker unit from the array.
                unset($attackerUnits[$targetUnitKey]);*/
            }

            // TODO: add logic here to loop through the units and:
            // - remove destroyed units
            // - roll a dice for hull integrity < 70% of original if the unit is also destroyed
            // - apply shield regeneration

            // Subtract losses from the attacker and defender units.
            $round->attackerShips->subtractCollection($round->attackerLossesInThisRound, false);
            $round->defenderShips->subtractCollection($round->defenderLossesInThisRound, false);
            $rounds[] = $round;
            // ---- END ROUND
        }

        return $rounds;
    }

    /**
     * Let one unit attack another unit and apply the damage to the defender.
     *
     * @param BattleResultRound $round
     * @param BattleUnit $attacker
     * @param BattleUnit $defender
     * @return void
     */
    private function attackUnit(BattleResultRound $round, BattleUnit $attacker, BattleUnit $defender): void
    {
        // Calculate the damage dealt by the attacker to the defender.
        $damage = $attacker->currentAttackPower;

        // If the defender has a shield, first apply damage to the shield.
        if ($defender->currentShieldPoints > 0) {
            $defender->currentShieldPoints -= $damage;

            // If the shield is destroyed, apply the remaining damage to the hull plating.
            if ($defender->currentShieldPoints < 0) {
                $defender->currentHullPlating += $defender->currentShieldPoints;
                $defender->currentShieldPoints = 0;
            }
        } else {
            $defender->currentHullPlating -= $damage;
        }

        // TODO: implement shield damage absorption for defender units.
        $round->absorbedDamageDefender += 1;
        // TODO: implement full strength attacker.
        $round->fullStrengthAttacker += 1;
    }
}