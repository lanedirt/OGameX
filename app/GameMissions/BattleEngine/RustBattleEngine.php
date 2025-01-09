<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Resources;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;
use OGame\Services\ObjectService;

/**
 * Class RustBattleEngine.
 *
 * This class is responsible for handling the battle logic in the game, used primarily
 * by the AttackMission class. This is the Rust version of the BattleEngine which calls
 * the Rust battle engine library for improved memory usage and performance.
 *
 * @package OGame\GameMissions\BattleEngine
 */
class RustBattleEngine
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
     * @var \FFI The FFI instance used to call the Rust battle engine.
     */
    private \FFI $ffi;

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

        $this->ffi = \FFI::cdef(
            "char* fight_battle_rounds(const char* input_json);",
            base_path('storage/ffi-libs/libbattle_engine_ffi.so')
        );
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
        // Convert PHP battle units to format expected by Rust
        $input = $this->prepareBattleInput($result);

        // Convert to JSON
        $inputJson = json_encode($input);

        // Call Rust function
        // @phpstan-ignore-next-line
        $outputPtr = $this->ffi->fight_battle_rounds($inputJson);
        $output = \FFI::string($outputPtr);

        // Parse JSON response
        $battleOutput = json_decode($output, true);

        // Convert Rust output back to PHP battle rounds
        return $this->convertBattleOutput($battleOutput);
    }

    /**
     * Prepare the battle input for the Rust battle engine.
     *
     * @param BattleResult $result
     * @return array
     */
    private function prepareBattleInput(BattleResult $result): array
    {
        // Convert PHP battle units to Rust format
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            $attackerUnits[] = [
                'unit_id' => $unit->unitObject->machine_name,
                'structural_integrity' => $unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue,
                'shield_points' => $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue,
                'attack_power' => $unit->unitObject->properties->attack->calculate($this->attackerPlayer)->totalValue,
                'original_shield_points' => $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue,
                'current_shield_points' => $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue,
                'current_hull_plating' => floor($unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue / 10),
            ];
        }

        // Convert defender units to Rust format
        $defenderUnits = [];
        foreach ($result->defenderUnitsStart->units as $unit) {
            $defenderUnits[] = [
                'unit_id' => $unit->unitObject->machine_name,
                'structural_integrity' => $unit->unitObject->properties->structural_integrity->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'shield_points' => $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'attack_power' => $unit->unitObject->properties->attack->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'original_shield_points' => $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'current_shield_points' => $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'current_hull_plating' => floor($unit->unitObject->properties->structural_integrity->calculate($this->defenderPlanet->getPlayer())->totalValue / 10),
            ];
        }

        return [
            'attacker_units' => $attackerUnits,
            'defender_units' => $defenderUnits,
        ];
    }

    private function convertBattleOutput(array $battleOutput): array
    {
        $rounds = [];
        foreach ($battleOutput['rounds'] as $roundData) {
            $round = new BattleResultRound();

            // Initialize collections
            $round->attackerShips = new UnitCollection();
            $round->defenderShips = new UnitCollection();

            // Convert attacker ships
            if (isset($roundData['attacker_ships']) && is_array($roundData['attacker_ships'])) {
                foreach ($roundData['attacker_ships'] as $unitData) {
                    if (is_array($unitData) && isset($unitData['unit_id'])) {
                        $unit = ObjectService::getUnitObjectByMachineName($unitData['unit_id']);
                        $round->attackerShips->addUnit($unit, 1);
                    }
                }
            }

            // Convert defender ships
            if (isset($roundData['defender_ships']) && is_array($roundData['defender_ships'])) {
                foreach ($roundData['defender_ships'] as $unitData) {
                    if (is_array($unitData) && isset($unitData['unit_id'])) {
                        $unit = ObjectService::getUnitObjectByMachineName($unitData['unit_id']);
                        $round->defenderShips->addUnit($unit, 1);
                    }
                }
            }

            // Set round statistics with safe defaults
            $round->hitsAttacker = (int)($roundData['hits_attacker'] ?? 0);
            $round->hitsDefender = (int)($roundData['hits_defender'] ?? 0);
            $round->absorbedDamageAttacker = (int)($roundData['absorbed_damage_attacker'] ?? 0);
            $round->absorbedDamageDefender = (int)($roundData['absorbed_damage_defender'] ?? 0);
            $round->fullStrengthAttacker = (int)($roundData['full_strength_attacker'] ?? 0);
            $round->fullStrengthDefender = (int)($roundData['full_strength_defender'] ?? 0);

            $rounds[] = $round;
        }
        return $rounds;
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
        $dice = random_int(1, 100);
        return $dice <= $moonChance;
    }
}
