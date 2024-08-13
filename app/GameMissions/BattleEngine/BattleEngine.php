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

        // ---- BEGIN ROUND
        // Run battle round here until 6 rounds or until one of the players has no units left.
        $result->rounds = [];
        $round = new BattleResultRound();
        $round->defenderLossesInThisRound = new UnitCollection();
        $round->defenderLosses = new UnitCollection();
        $round->attackerLossesInThisRound = new UnitCollection();
        $round->attackerLosses = new UnitCollection();
        $round->attackerShips = clone $result->attackerUnitsStart;
        $round->defenderShips = clone $result->defenderUnitsStart;

        // Convert attacker fleet to individual unit array.
        // Key = unit id (int), value = structural integrity (int).
        // TODO: should actually include structural integrity, shield, and weapon damage.
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            for ($i = 0; $i < $unit->amount; $i++) {
                $attackerUnits[] = $unit->unitObject->id;
            }
        }

        $defenderUnits = [];
        foreach ($result->defenderUnitsStart->units as $unit) {
            for ($i = 0; $i < $unit->amount; $i++) {
                $defenderUnits[] = $unit->unitObject->id;
            }
        }

        // Let the attacker attack the defender.
        if (count($defenderUnits) > 0) {
            foreach ($attackerUnits as $key => $unitId) {
                // If all defender units are destroyed, break the loop.
                if (count($defenderUnits) === 0) {
                    break;
                }

                // Every single unit attacks a random unit from the defender's units.
                // For now we assume the attacker always kills the target.
                // Random target
                $targetUnitKey = array_rand($defenderUnits);
                $targetUnitId = $defenderUnits[$targetUnitKey];
                $targetUnitObject = $this->defenderPlanet->objects->getUnitObjectById($targetUnitId);

                // Add target defender unit to defender losses array.
                $round->defenderLosses->addUnit($targetUnitObject, 1);
                $round->defenderLossesInThisRound->addUnit($targetUnitObject, 1);

                $round->hitsAttacker += 1;

                // Unset the defender unit from the array.
                unset($defenderUnits[$targetUnitKey]);
            }
        }

        // Let the defender attack the attacker.
        if (count($attackerUnits) > 0) {
            foreach ($defenderUnits as $key => $unitId) {
                // If all attacker units are destroyed, break the loop.
                if (count($attackerUnits) === 0) {
                    break;
                }

                // Every single unit attacks a random unit from the attacker's units.
                // For now we assume the defender always kills the target.
                // Random target
                $targetUnitKey = array_rand($attackerUnits);
                $targetUnitId = $attackerUnits[$targetUnitKey];
                $targetUnitObject = $this->defenderPlanet->objects->getUnitObjectById($targetUnitId);
                // Add target attacker unit to attacker losses array.
                $round->attackerLosses->addUnit($targetUnitObject, 1);
                $round->attackerLossesInThisRound->addUnit($targetUnitObject, 1);

                $round->hitsDefender += 1;

                // Unset the attacker unit from the array.
                unset($attackerUnits[$targetUnitKey]);
            }
        }

        // Subtract losses from the attacker and defender units.
        $round->attackerShips->subtractCollection($round->attackerLossesInThisRound);
        $round->defenderShips->subtractCollection($round->defenderLossesInThisRound);
        $result->rounds[] = $round;
        // ---- END ROUND

        // Subtract losses from the attacker and defender units.
        // TODO: do this properly by taking results of last round.
        // $result->attackerUnitsResult->subtractCollection($round->attackerLossesInThisRound);
        // $result->defenderUnitsResult->subtractCollection($round->defenderLossesInThisRound);

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
}