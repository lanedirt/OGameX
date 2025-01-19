<?php

namespace OGame\GameMissions\BattleEngine;

use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\Models\BattleResultRound;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

/**
 * Class RustBattleEngine.
 *
 * This class is responsible for handling the battle logic in the game, used primarily
 * by the AttackMission class. This is the Rust version of the BattleEngine which calls
 * the Rust battle engine library for improved memory usage and performance.
 *
 * @package OGame\GameMissions\BattleEngine
 */
class RustBattleEngine extends BattleEngine
{
    /**
     * @var \FFI The FFI instance used to call the Rust battle engine.
     */
    private \FFI $ffi;

    /**
     * RustBattleEngine constructor.
     *
     * @param UnitCollection $attackerFleet The fleet of the attacker player.
     * @param PlayerService $attackerPlayer The attacker player.
     * @param PlanetService $defenderPlanet The planet of the defender player.
     * @param SettingsService $settings The settings service.
     */
    public function __construct(UnitCollection $attackerFleet, PlayerService $attackerPlayer, PlanetService $defenderPlanet, SettingsService $settings)
    {
        parent::__construct($attackerFleet, $attackerPlayer, $defenderPlanet, $settings);

        $this->ffi = \FFI::cdef(
            "char* fight_battle_rounds(const char* input_json);",
            base_path('storage/rust-libs/libbattle_engine_ffi.so')
        );
    }

    /**
     * Fight the battle in max 6 rounds.
     *
     * @param BattleResult $result
     * @return array<BattleResultRound>
     */
    protected function fightBattleRounds(BattleResult $result): array
    {
        // Convert PHP battle units to format expected by Rust
        $input = $this->prepareBattleInput($result);

        // Convert to JSON, forcing object notation
        $inputJson = json_encode($input, JSON_FORCE_OBJECT);

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
     * @return array{
     *     attacker_units: array<int, array{
     *         unit_id: int,
     *         amount: int,
     *         shield_points: int,
     *         attack_power: int,
     *         hull_plating: float,
     *         rapidfire: array<int, int>
     *     }>,
     *     defender_units: array<int, array{
     *         unit_id: int,
     *         amount: int,
     *         shield_points: int,
     *         attack_power: int,
     *         hull_plating: float,
     *         rapidfire: array<int, int>
     *     }>
     * }
     */
    private function prepareBattleInput(BattleResult $result): array
    {
        // Convert PHP battle units to Rust format
        $attackerUnits = [];
        foreach ($result->attackerUnitsStart->units as $unit) {
            $rapidfire = [];
            foreach ($unit->unitObject->rapidfire as $rapidfireObject) {
                $targetUnit = ObjectService::getUnitObjectByMachineName($rapidfireObject->object_machine_name);
                $rapidfire[$targetUnit->id] = $rapidfireObject->amount;
            }

            $attackerUnits[$unit->unitObject->id] = [
                'unit_id' => $unit->unitObject->id,
                'amount' => $unit->amount,
                'shield_points' => $unit->unitObject->properties->shield->calculate($this->attackerPlayer)->totalValue,
                'attack_power' => $unit->unitObject->properties->attack->calculate($this->attackerPlayer)->totalValue,
                'hull_plating' => floor($unit->unitObject->properties->structural_integrity->calculate($this->attackerPlayer)->totalValue / 10),
                'rapidfire' => $rapidfire,
            ];
        }

        // Convert defender units to Rust format
        $defenderUnits = [];
        foreach ($result->defenderUnitsStart->units as $unit) {
            $rapidfire = [];
            foreach ($unit->unitObject->rapidfire as $rapidfireObject) {
                $targetUnit = ObjectService::getUnitObjectByMachineName($rapidfireObject->object_machine_name);
                $rapidfire[$targetUnit->id] = $rapidfireObject->amount;
            }

            $defenderUnits[$unit->unitObject->id] = [
                'unit_id' => $unit->unitObject->id,
                'amount' => $unit->amount,
                'shield_points' => $unit->unitObject->properties->shield->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'attack_power' => $unit->unitObject->properties->attack->calculate($this->defenderPlanet->getPlayer())->totalValue,
                'hull_plating' => floor($unit->unitObject->properties->structural_integrity->calculate($this->defenderPlanet->getPlayer())->totalValue / 10),
                'rapidfire' => $rapidfire,
            ];
        }

        return [
            'attacker_units' => $attackerUnits,
            'defender_units' => $defenderUnits,
        ];
    }

    /**
     * Convert the battle output from Rust to PHP.
     *
     * @param array<string, list<array<string, float|int|string>>> $battleOutput
     * @return array<BattleResultRound>
     */
    private function convertBattleOutput(array $battleOutput): array
    {
        $rounds = [];
        foreach ($battleOutput['rounds'] as $roundData) {
            $round = new BattleResultRound();

            // Initialize collections.
            $round->attackerShips = new UnitCollection();
            $round->defenderShips = new UnitCollection();
            $round->attackerLosses = new UnitCollection();
            $round->attackerLossesInRound = new UnitCollection();
            $round->defenderLosses = new UnitCollection();
            $round->defenderLossesInRound = new UnitCollection();

            // Convert unit arrays to UnitCollections.
            if (isset($roundData['attacker_ships']) && is_array($roundData['attacker_ships'])) {
                $round->attackerShips = $this->convertUnitArrayToUnitCollection($roundData['attacker_ships']);
            }

            if (isset($roundData['defender_ships']) && is_array($roundData['defender_ships'])) {
                $round->defenderShips = $this->convertUnitArrayToUnitCollection($roundData['defender_ships']);
            }

            if (isset($roundData['attacker_losses']) && is_array($roundData['attacker_losses'])) {
                $round->attackerLosses = $this->convertUnitArrayToUnitCollection($roundData['attacker_losses']);
            }

            if (isset($roundData['attacker_losses_in_round']) && is_array($roundData['attacker_losses_in_round'])) {
                $round->attackerLossesInRound = $this->convertUnitArrayToUnitCollection($roundData['attacker_losses_in_round']);
            }

            if (isset($roundData['defender_losses']) && is_array($roundData['defender_losses'])) {
                $round->defenderLosses = $this->convertUnitArrayToUnitCollection($roundData['defender_losses']);
            }

            if (isset($roundData['defender_losses_in_round']) && is_array($roundData['defender_losses_in_round'])) {
                $round->defenderLossesInRound = $this->convertUnitArrayToUnitCollection($roundData['defender_losses_in_round']);
            }

            // Extract other properties.
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
     * Convert unit array received from the Rust FFI output to a UnitCollection.
     *
     * @param array<array<string, float|int|string>> $unitData
     * @return UnitCollection
     */
    private function convertUnitArrayToUnitCollection(array $unitData): UnitCollection
    {
        $unitCollection = new UnitCollection();
        foreach ($unitData as $unit) {
            $unitObject = ObjectService::getUnitObjectById((int)$unit['unit_id']);
            $unitCollection->addUnit($unitObject, (int)$unit['amount']);
        }
        return $unitCollection;
    }
}
