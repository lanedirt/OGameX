<?php

namespace OGame\GameMissions\BattleEngine\Services;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

/**
 * Class LootService.
 *
 * This class is responsible for handling the loot logic used in battle engine. This covers
 * the calculation of the loot gained from a battle, constrained by the attacker's cargo capacity.
 *
 * @package OGame\GameMissions\BattleEngine
 */
class LootService
{
    private UnitCollection $attackerFleet;
    private PlayerService $attackerPlayer;
    private PlanetService $defenderPlanet;

    /**
     * @var int The percentage of loot that is gained from a battle.
     */
    private int $lootPercentage;

    /**
     * LootService constructor.
     *
     * @param UnitCollection $attackerFleet The fleet of the attacker player.
     * @param PlayerService $attackerPlayer The attacker player.
     * @param PlanetService $defenderPlanet The planet of the defender player.
     * @param int $lootPercentage The percentage of loot that is gained from a battle.
    */
    public function __construct(UnitCollection $attackerFleet, PlayerService $attackerPlayer, PlanetService $defenderPlanet, int $lootPercentage)
    {
        $this->attackerFleet = $attackerFleet;
        $this->attackerPlayer = $attackerPlayer;
        $this->defenderPlanet = $defenderPlanet;
        $this->lootPercentage = $lootPercentage;
    }

    /**
     * Calculate the loot gained from a battle, constrained by the attacker's cargo capacity.
     *
     * @return Resources The loot gained from the battle, constrained by the attacker's cargo capacity.
     */
    public function calculateLootCapacityConstrained(): Resources
    {
        // Determine loot: max 50% of the resources are stolen.
        // Add sanity check to prevent negative values.
        $resources = $this->defenderPlanet->getResources();
        $loot = new Resources(
            max(0, $resources->metal->get()) * ($this->lootPercentage / 100),
            max(0, $resources->crystal->get()) * ($this->lootPercentage / 100),
            max(0, $resources->deuterium->get()) * ($this->lootPercentage / 100),
            0
        );

        $total_cargo_capacity = $this->attackerFleet->getTotalCargoCapacity($this->attackerPlayer);

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
    public static function distributeLoot(Resources $loot, int $total_cargo_capacity): Resources
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
