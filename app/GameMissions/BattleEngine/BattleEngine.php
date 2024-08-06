<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resource;
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
    }

    /**
     * Simulate a battle between two players.
     *
     * @return BattleResult Information about the battle result.
     */
    public function simulateBattle(): BattleResult
    {
        $total_attacker_ships = 0;
        foreach ($this->attackerFleet->units as $unit) {
            $total_attacker_ships += $unit->amount;
        }

        $result = new BattleResult();

        // Add all remaining attacker units to the result.
        // TODO: implement actual battle logic so only surviving attacker units are set here.
        $result->attackerUnits = $this->attackerFleet;
        $result->lootPercentage = $this->lootPercentage;

        // Check if the attacker has enough cargo capacity to carry the loot.
        // If not, reduce the loot to the cargo capacity.
        $result->loot = $this->calculateLootCapacityConstrained();

        return $result;
    }

    /**
     * Calculate the loot gained from a battle, constrained by the attacker's cargo capacity.
     *
     * @return Resources The loot gained from the battle, constrained by the attacker's cargo capacity.
     */
    private function calculateLootCapacityConstrained(): Resources
    {
        // Determine loot: 50% of the resources are stolen.
        $resources = $this->defenderPlanet->getResources();
        $loot = new Resources(
            $resources->metal->get() * ($this->lootPercentage / 100),
            $resources->crystal->get() * ($this->lootPercentage / 100),
            $resources->deuterium->get() * ($this->lootPercentage / 100),
            0);

        $total_cargo_capacity = 0;
        foreach ($this->attackerFleet->units as $unit) {
            $total_cargo_capacity += $unit->unitObject->properties->capacity->calculate($this->attackerPlayer)->totalValue * $unit->amount;
        }

        // If the loot is greater than the cargo capacity, reduce the loot to the cargo capacity
        // in even parts so that the attacker gets a fair share of the available loot.
        return $this->distributeLoot($loot, $total_cargo_capacity);
    }

    /**
     * Distribute the loot evenly based on the total cargo capacity.
     *
     * @param Resources $loot
     * @param int $total_cargo_capacity
     * @return Resources
     */
    private function distributeLoot(Resources $loot, int $total_cargo_capacity): Resources
    {
        $total_loot = $loot->sum();

        if ($total_cargo_capacity >= $total_loot) {
            // No need to adjust if there is enough capacity to take all loot.
            return $loot;
        }

        $distributed_loot = new Resources(0, 0, 0, 0);
        $resources = ['metal', 'crystal', 'deuterium'];

        // Calculate max loot per resource type.
        $max_loot_per_resource = floor($total_cargo_capacity / count($resources));

        // Set all resources to the max loot per resource type if they exceed it.
        foreach ($resources as $resource_name) {
            $distributed_loot->$resource_name->set(min($loot->$resource_name->get(), $max_loot_per_resource));
        }

        // Calculate diff between even distributed loot and total cargo capacity.
        $remaining_capacity = $total_cargo_capacity - $distributed_loot->sum();

        // If there is remaining capacity, fill up resources evenly as long as
        // the distributed loot does not exceed the original loot.
        while ($remaining_capacity > 0) {
            $unfilled_resources = 0;

            // Count the number of resources that have not reached their max loot yet.
            // This is used to distribute the remaining capacity evenly among them in one
            // or more passes.
            foreach ($resources as $resource_name) {
                if ($loot->$resource_name->get() > $distributed_loot->$resource_name->get()) {
                    $unfilled_resources++;
                }
            }

            if ($unfilled_resources == 0) {
                break;
            }

            // Distribute the remaining capacity evenly among the resources.
            foreach ($resources as $resource_name) {
                if ($loot->$resource_name->get() > $distributed_loot->$resource_name->get()) {
                    $distributed_loot->$resource_name->set(min($loot->$resource_name->get(), $distributed_loot->$resource_name->get() + ($remaining_capacity / $unfilled_resources)));
                }
            }

            $remaining_capacity = $total_cargo_capacity - $distributed_loot->sum();
            if ($remaining_capacity <= 0) {
               break;
            }
        }

        return $distributed_loot;
    }
}