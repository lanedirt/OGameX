<?php

namespace OGame\GameMissions\BattleEngine;

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
    /**
     * Simulate a battle between two players.
     *
     * @param UnitCollection $attackerFleet The fleet of the attacker player.
     * @param PlayerService $attackerPlayer The attacker player.
     * @param PlanetService $defenderPlanet The planet of the defender player.
     * @return BattleResult Information about the battle result.
     */
    public function simulateBattle(UnitCollection $attackerFleet, PlayerService $attackerPlayer, PlanetService $defenderPlanet): BattleResult
    {
        // For now, we just return true if the attacker has more ships than the defender.

        // Simple implementation: if the attacker has more ships than the defender, the attacker wins.
        $total_attacker_ships = 0;
        foreach ($attackerFleet->units as $unit) {
            $total_attacker_ships += $unit->amount;
        }

        $result = new BattleResult();

        // Add all remaining attacker units to the result.
        // TODO: implement actual battle logic so only surviving attacker units are set here.
        $result->attackerUnits = $attackerFleet;

        // Determine loot: 50% of the resources are stolen.
        // TODO: make loot percentage configurable? For inactive players
        // loot percentage could be up to 75% for example.
        $result->lootPercentage = 50;
        $result->loot = new Resources(
            $defenderPlanet->getResources()->metal->get() * ($result->lootPercentage / 100),
            $defenderPlanet->getResources()->crystal->get() * ($result->lootPercentage / 100),
            $defenderPlanet->getResources()->deuterium->get() * ($result->lootPercentage / 100),
            0);

        return $result;
    }

}