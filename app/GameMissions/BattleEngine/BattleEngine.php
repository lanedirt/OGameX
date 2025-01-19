<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\Models\BattleResultRound;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

/**
 * Abstract class BattleEngine.
 *
 * This abstract class provides the base battle simulation functionality, while leaving
 * the core battle round logic to be implemented by specific battle engine implementations such as
 * PhpBattleEngine and RustBattleEngine.
 *
 * @package OGame\GameMissions\BattleEngine
 */
abstract class BattleEngine
{
    private UnitCollection $attackerFleet;
    protected PlayerService $attackerPlayer;
    protected PlanetService $defenderPlanet;

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

        // Sanitize the round array to make sure that the remaining attacker and defender units
        // for every round contain the starting unit types, even if there are no units of that type left.
        // This is important for the battle report to show all units that were part of the battle on
        // every round.
        $result->rounds = $this->sanitizeRoundArray($result->rounds);

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
     * Fight the battle rounds according to the specific battle engine implementation.
     *
     * @param BattleResult $result
     * @return array<BattleResultRound>
     */
    abstract protected function fightBattleRounds(BattleResult $result): array;

    /**
     * Calculate the debris field based on the units lost in the battle.
     *
     * @param UnitCollection $attackerUnitsLost
     * @param UnitCollection $defenderUnitsLost
     * @return Resources
     */
    protected function calculateDebris(UnitCollection $attackerUnitsLost, UnitCollection $defenderUnitsLost): Resources
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
     * Sanitizes the round array to make sure that the remaining attacker and defender units
     * for every round contain all the starting unit types.
     *
     * @param array<BattleResultRound> $rounds
     * @return array<BattleResultRound>
     */
    protected function sanitizeRoundArray(array $rounds): array
    {
        foreach ($rounds as $round) {
            // Ensure all attacker units are present in the round
            foreach ($this->attackerFleet->units as $unit) {
                if (!$round->attackerShips->hasUnit($unit->unitObject)) {
                    $round->attackerShips->addUnit($unit->unitObject, 0);
                }
            }

            // Ensure all defender units are present in the round
            foreach ($this->defenderPlanet->getShipUnits()->units as $unit) {
                if (!$round->defenderShips->hasUnit($unit->unitObject)) {
                    $round->defenderShips->addUnit($unit->unitObject, 0);
                }
            }
        }

        return $rounds;
    }

    /**
     * Calculate moon chance based on the debris field.
     *
     * @param Resources $debris
     * @return int
     */
    protected function calculateMoonChance(Resources $debris): int
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
    protected function rollMoonCreation($moonChance): bool
    {
        $dice = random_int(1, 100);
        return $dice <= $moonChance;
    }
}
