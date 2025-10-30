<?php

namespace OGame\GameMissions;

use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\ACSService;
use OGame\Services\DebrisFieldService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use Throwable;

class ACSAttackMission extends GameMission
{
    protected static string $name = 'ACS Attack';
    protected static int $typeId = 2;
    protected static bool $hasReturnMission = true;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // ACS Attack mission is only possible for planets and moons.
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // If target planet does not exist, the mission is not possible.
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // If planet belongs to current player, the mission is not possible.
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // If all checks pass, the mission is possible.
        return new MissionPossibleStatus(true);
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    protected function processArrival(FleetMission $mission): void
    {
        // For ACS missions, we need to find the ACS group this fleet belongs to
        $acsFleetMember = \OGame\Models\AcsFleetMember::where('fleet_mission_id', $mission->id)->first();

        if (!$acsFleetMember) {
            // This shouldn't happen, but fall back to regular attack if no ACS group found
            $this->processSingleFleetAttack($mission);
            return;
        }

        $acsGroup = ACSService::findGroup($acsFleetMember->acs_group_id);

        if (!$acsGroup) {
            // Group not found, fall back to single fleet attack
            $this->processSingleFleetAttack($mission);
            return;
        }

        // Check if group has already been processed
        if ($acsGroup->status === 'completed' || $acsGroup->status === 'cancelled') {
            // Group already processed, mark this mission as processed too
            $mission->processed = 1;
            $mission->save();
            return;
        }

        // Check if all fleets in the group have arrived
        $allArrived = ACSService::allFleetsArrived($acsGroup);
        \Log::debug('ACS Group arrival check', [
            'acs_group_id' => $acsGroup->id,
            'mission_id' => $mission->id,
            'all_fleets_arrived' => $allArrived,
            'current_time' => time(),
            'group_arrival_time' => $acsGroup->arrival_time,
        ]);

        if (!$allArrived) {
            // Not all fleets have arrived yet, don't process yet
            // The mission will be processed when the update cycle runs again
            \Log::debug('Not all fleets arrived yet, waiting...', [
                'acs_group_id' => $acsGroup->id,
                'fleet_count' => ACSService::getGroupFleets($acsGroup)->count(),
            ]);
            return;
        }

        \Log::debug('All fleets arrived! Processing combined attack', [
            'acs_group_id' => $acsGroup->id,
            'status' => $acsGroup->status,
        ]);

        // All fleets have arrived, process the combined attack
        // Only process if group is still pending/active
        if ($acsGroup->status === 'pending' || $acsGroup->status === 'active') {
            $this->processACSGroupAttack($acsGroup, $mission);
        }
    }

    /**
     * Process a combined ACS attack with all fleets in the group.
     *
     * @param \OGame\Models\AcsGroup $acsGroup
     * @param FleetMission $triggeringMission The mission that triggered this processing
     * @return void
     * @throws Throwable
     */
    private function processACSGroupAttack(\OGame\Models\AcsGroup $acsGroup, FleetMission $triggeringMission): void
    {
        $defenderPlanet = $this->planetServiceFactory->make($triggeringMission->planet_id_to, true);

        // Trigger defender planet update to make sure the battle uses up-to-date info.
        $defenderPlanet->update();

        // Get all fleet missions in this ACS group
        $fleetMembers = ACSService::getGroupFleets($acsGroup);

        // Combine all attacker units
        $combinedAttackerUnits = new UnitCollection();
        $attackerFleets = [];

        foreach ($fleetMembers as $member) {
            $fleetMission = $this->fleetMissionService->getFleetMissionById($member->fleet_mission_id);
            $fleetUnits = $this->fleetMissionService->getFleetUnits($fleetMission);

            // Add this fleet's units to the combined force
            $combinedAttackerUnits->addCollection($fleetUnits);

            // Store fleet info for later processing
            $originPlanet = $this->planetServiceFactory->make($fleetMission->planet_id_from, true);
            $fleetPlayer = $originPlanet->getPlayer();
            $attackerFleets[] = [
                'mission' => $fleetMission,
                'player' => $fleetPlayer,
                'units' => $fleetUnits,
                'cargo_capacity' => $fleetUnits->getTotalCargoCapacity($fleetPlayer),
            ];
        }

        // Use the first attacker's player for battle engine (all attackers fight together)
        $primaryAttacker = $attackerFleets[0]['player'];

        // Execute the battle logic using configured battle engine
        switch ($this->settings->battleEngine()) {
            case 'php':
                $battleEngine = new PhpBattleEngine($combinedAttackerUnits, $primaryAttacker, $defenderPlanet, $this->settings);
                break;
            case 'rust':
            default:
                $battleEngine = new RustBattleEngine($combinedAttackerUnits, $primaryAttacker, $defenderPlanet, $this->settings);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Deduct loot from the target planet.
        $defenderPlanet->deductResources($battleResult->loot);

        // Deduct defender's lost units from the defenders planet.
        $defenderUnitsLost = clone $battleResult->defenderUnitsStart;
        $defenderUnitsLost->subtractCollection($battleResult->defenderUnitsResult);
        $defenderPlanet->removeUnits($defenderUnitsLost, false);

        // Calculate repaired defenses (70% chance for each destroyed defense structure)
        $repairedDefenses = $this->calculateRepairedDefenses($defenderUnitsLost);

        // Add repaired defenses back to the planet
        if ($repairedDefenses->getAmount() > 0) {
            $defenderPlanet->addUnits($repairedDefenses, false);
        }

        // Save defenders planet
        $defenderPlanet->save();

        // Create or append debris field.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($defenderPlanet->getPlanetCoordinates());
        $debrisFieldService->appendResources($battleResult->debris);
        $debrisFieldService->save();

        // Create a moon for defender if result of battle indicates so and defender planet does not already have a moon.
        if (!$defenderPlanet->hasMoon() && $battleResult->moonCreated) {
            $this->planetServiceFactory->createMoonForPlanet($defenderPlanet);
        }

        // Distribute loot and losses among all attackers
        $this->distributeLootAndLosses($attackerFleets, $battleResult, $defenderPlanet, $repairedDefenses);

        // Mark the ACS group as completed
        $acsGroup->status = 'completed';
        $acsGroup->save();
    }

    /**
     * Distribute loot and losses among all participating attackers.
     *
     * @param array $attackerFleets Array of attacker fleet information
     * @param BattleResult $battleResult The battle result
     * @param PlanetService $defenderPlanet The defender's planet
     * @param UnitCollection $repairedDefenses Repaired defense units
     * @return void
     */
    private function distributeLootAndLosses(array $attackerFleets, BattleResult $battleResult, PlanetService $defenderPlanet, UnitCollection $repairedDefenses): void
    {
        // Calculate total cargo capacity of all surviving ships
        $totalCargoCapacity = 0;
        foreach ($attackerFleets as $fleet) {
            // We'll calculate surviving cargo capacity later
            $totalCargoCapacity += $fleet['cargo_capacity'];
        }

        // Calculate how much loot each attacker gets based on their cargo capacity
        $totalLoot = $battleResult->loot;

        foreach ($attackerFleets as $fleet) {
            $mission = $fleet['mission'];
            $player = $fleet['player'];

            // Calculate this fleet's share of losses
            $initialUnits = $fleet['units'];
            $fleetLossPercentage = $this->calculateFleetLossPercentage($initialUnits, $battleResult);
            $survivingUnits = $this->calculateSurvivingUnits($initialUnits, $fleetLossPercentage);

            // Calculate this fleet's share of loot based on cargo capacity
            $cargoShare = $totalCargoCapacity > 0 ? ($fleet['cargo_capacity'] / $totalCargoCapacity) : 0;
            $fleetLoot = new Resources(
                (int)($totalLoot->metal->get() * $cargoShare),
                (int)($totalLoot->crystal->get() * $cargoShare),
                (int)($totalLoot->deuterium->get() * $cargoShare),
                0
            );

            // Create battle report for this attacker
            $reportId = $this->createBattleReport($player, $defenderPlanet, $battleResult, $repairedDefenses, true);
            $this->messageService->sendBattleReportMessageToPlayer($player, $reportId);

            // Mark mission as processed
            $mission->processed = 1;
            $mission->save();

            // Create return mission with loot and surviving units
            $this->startReturn($mission, $fleetLoot, $survivingUnits);
        }

        // Send battle report to defender
        $reportId = $this->createBattleReport($attackerFleets[0]['player'], $defenderPlanet, $battleResult, $repairedDefenses, false);
        $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);
    }

    /**
     * Calculate the percentage of losses for a specific fleet.
     *
     * @param UnitCollection $fleetUnits
     * @param BattleResult $battleResult
     * @return float Loss percentage (0-1)
     */
    private function calculateFleetLossPercentage(UnitCollection $fleetUnits, BattleResult $battleResult): float
    {
        // Calculate what percentage of total attacking force this fleet represented
        $fleetValue = 0;
        $totalStartValue = 0;

        foreach ($fleetUnits->units as $unit) {
            $unitValue = $unit->amount * $unit->unitObject->price->resources->metal->get();
            $fleetValue += $unitValue;
        }

        foreach ($battleResult->attackerUnitsStart->units as $unit) {
            $unitValue = $unit->amount * $unit->unitObject->price->resources->metal->get();
            $totalStartValue += $unitValue;
        }

        $fleetPercentage = $totalStartValue > 0 ? ($fleetValue / $totalStartValue) : 0;

        // Calculate total attacker losses
        $totalLosses = clone $battleResult->attackerUnitsStart;
        $totalLosses->subtractCollection($battleResult->attackerUnitsResult);

        $lossValue = 0;
        foreach ($totalLosses->units as $unit) {
            $unitValue = $unit->amount * $unit->unitObject->price->resources->metal->get();
            $lossValue += $unitValue;
        }

        // Return the percentage of losses relative to fleet size
        return $fleetValue > 0 ? ($lossValue * $fleetPercentage / $fleetValue) : 0;
    }

    /**
     * Calculate surviving units for a fleet based on loss percentage.
     *
     * @param UnitCollection $originalUnits
     * @param float $lossPercentage
     * @return UnitCollection
     */
    private function calculateSurvivingUnits(UnitCollection $originalUnits, float $lossPercentage): UnitCollection
    {
        $survivingUnits = new UnitCollection();

        foreach ($originalUnits->units as $unit) {
            $surviving = (int)($unit->amount * (1 - $lossPercentage));
            if ($surviving > 0) {
                $survivingUnits->addUnit($unit->unitObject, $surviving);
            }
        }

        return $survivingUnits;
    }

    /**
     * Process a single fleet attack (fallback when no ACS group is found).
     *
     * @param FleetMission $mission
     * @return void
     * @throws Throwable
     */
    private function processSingleFleetAttack(FleetMission $mission): void
    {
        $defenderPlanet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Trigger defender planet update to make sure the battle uses up-to-date info.
        $defenderPlanet->update();

        $attackerPlayer = $origin_planet->getPlayer();
        $attackerUnits = $this->fleetMissionService->getFleetUnits($mission);

        // Execute the battle logic using configured battle engine
        switch ($this->settings->battleEngine()) {
            case 'php':
                $battleEngine = new PhpBattleEngine($attackerUnits, $attackerPlayer, $defenderPlanet, $this->settings);
                break;
            case 'rust':
            default:
                $battleEngine = new RustBattleEngine($attackerUnits, $attackerPlayer, $defenderPlanet, $this->settings);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Deduct loot from the target planet.
        $defenderPlanet->deductResources($battleResult->loot);

        // Deduct defender's lost units from the defenders planet.
        $defenderUnitsLost = clone $battleResult->defenderUnitsStart;
        $defenderUnitsLost->subtractCollection($battleResult->defenderUnitsResult);
        $defenderPlanet->removeUnits($defenderUnitsLost, false);

        // Calculate repaired defenses (70% chance for each destroyed defense structure)
        $repairedDefenses = $this->calculateRepairedDefenses($defenderUnitsLost);

        // Add repaired defenses back to the planet
        if ($repairedDefenses->getAmount() > 0) {
            $defenderPlanet->addUnits($repairedDefenses, false);
        }

        // Save defenders planet
        $defenderPlanet->save();

        // Create or append debris field.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($defenderPlanet->getPlanetCoordinates());
        $debrisFieldService->appendResources($battleResult->debris);
        $debrisFieldService->save();

        // Create a moon for defender if result of battle indicates so and defender planet does not already have a moon.
        if (!$defenderPlanet->hasMoon() && $battleResult->moonCreated) {
            $this->planetServiceFactory->createMoonForPlanet($defenderPlanet);
        }

        // Send a message to both attacker and defender with a reference to the same battle report.
        $reportId = $this->createBattleReport($attackerPlayer, $defenderPlanet, $battleResult, $repairedDefenses, false);
        $this->messageService->sendBattleReportMessageToPlayer($attackerPlayer, $reportId);
        $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission (if attacker has remaining units).
        $this->startReturn($mission, $battleResult->loot, $battleResult->attackerUnitsResult);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // ACS Attack return trip: add back the units to the source planet.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Creates a battle report for the given battle result.
     *
     * @param PlayerService $attackPlayer The player who initiated the attack.
     * @param PlanetService $defenderPlanet The planet that was attacked.
     * @param BattleResult $battleResult The result of the battle.
     * @param UnitCollection $repairedDefenses The defensive structures that were repaired after the battle.
     * @param bool $isACSReport Whether this is an ACS battle report.
     * @return int
     */
    private function createBattleReport(PlayerService $attackPlayer, PlanetService $defenderPlanet, BattleResult $battleResult, UnitCollection $repairedDefenses, bool $isACSReport = false): int
    {
        // Create new battle report record.
        $report = new BattleReport();
        $report->planet_galaxy = $defenderPlanet->getPlanetCoordinates()->galaxy;
        $report->planet_system = $defenderPlanet->getPlanetCoordinates()->system;
        $report->planet_position = $defenderPlanet->getPlanetCoordinates()->position;
        $report->planet_type = $defenderPlanet->getPlanetType()->value;

        $report->planet_user_id = $defenderPlanet->getPlayer()->getId();

        $report->general = [
            'moon_existed' => $battleResult->moonExisted,
            'moon_chance' => $battleResult->moonChance,
            'moon_created' => $battleResult->moonCreated,
            'is_acs' => $isACSReport,
        ];

        $report->attacker = [
            'player_id' => $attackPlayer->getId(),
            'resource_loss' => $battleResult->attackerResourceLoss->sum(),
            'units' => $battleResult->attackerUnitsStart->toArray(),
            'weapon_technology' => $battleResult->attackerWeaponLevel,
            'shielding_technology' => $battleResult->attackerShieldLevel,
            'armor_technology' => $battleResult->attackerArmorLevel,
        ];

        $report->defender = [
            'player_id' => $defenderPlanet->getPlayer()->getId(),
            'resource_loss' => $battleResult->defenderResourceLoss->sum(),
            'units' => $battleResult->defenderUnitsStart->toArray(),
            'weapon_technology' => $battleResult->defenderWeaponLevel,
            'shielding_technology' => $battleResult->defenderShieldLevel,
            'armor_technology' => $battleResult->defenderArmorLevel,
        ];

        $report->loot = [
            'percentage' => $battleResult->lootPercentage,
            'metal' => (int)$battleResult->loot->metal->get(),
            'crystal' => (int)$battleResult->loot->crystal->get(),
            'deuterium' => (int)$battleResult->loot->deuterium->get(),
        ];

        $report->debris = [
            'metal' => $battleResult->debris->metal->get(),
            'crystal' => $battleResult->debris->crystal->get(),
            'deuterium' => $battleResult->debris->deuterium->get(),
        ];

        $report->repaired_defenses = $repairedDefenses->toArray();

        $rounds = [];
        foreach ($battleResult->rounds as $round) {
            $rounds[] = [
                'attacker_ships' => $round->attackerShips->toArray(),
                'defender_ships' => $round->defenderShips->toArray(),
                'attacker_losses' => $round->attackerLosses->toArray(),
                'defender_losses' => $round->defenderLosses->toArray(),
                'attacker_losses_in_this_round' => $round->attackerLossesInRound->toArray(),
                'defender_losses_in_this_round' => $round->defenderLossesInRound->toArray(),
                'absorbed_damage_attacker' => $round->absorbedDamageAttacker,
                'absorbed_damage_defender' => $round->absorbedDamageDefender,
                'full_strength_attacker' => $round->fullStrengthAttacker,
                'full_strength_defender' => $round->fullStrengthDefender,
                'hits_attacker' => $round->hitsAttacker,
                'hits_defender' => $round->hitsDefender,
            ];
        }

        $report->rounds = $rounds;
        $report->save();

        return $report->id;
    }

    /**
     * Calculate which defensive structures are repaired after battle.
     * In OGame, each destroyed defensive structure has a 70% chance to be rebuilt.
     *
     * @param UnitCollection $defenderUnitsLost The units lost by the defender during battle.
     * @return UnitCollection Collection of repaired defensive structures.
     * @throws \Exception
     */
    private function calculateRepairedDefenses(UnitCollection $defenderUnitsLost): UnitCollection
    {
        $repairedDefenses = new UnitCollection();

        // Get all defense objects to identify which lost units are defensive structures
        $defenseObjects = ObjectService::getDefenseObjects();
        $defenseObjectMachineNames = array_column($defenseObjects, 'machine_name');

        // Process each lost unit
        foreach ($defenderUnitsLost->units as $unit) {
            // Check if this unit is a defensive structure (ships are not repaired)
            if (in_array($unit->unitObject->machine_name, $defenseObjectMachineNames)) {
                // Roll 70% chance for each individual defensive structure
                $repairedCount = 0;
                for ($i = 0; $i < $unit->amount; $i++) {
                    // Generate random number 1-100, if <= 70 then repair this unit (70% chance)
                    if (random_int(1, 100) <= 70) {
                        $repairedCount++;
                    }
                }

                // Add repaired defenses to the collection
                if ($repairedCount > 0) {
                    $repairedDefenses->addUnit($unit->unitObject, $repairedCount);
                }
            }
        }

        return $repairedDefenses;
    }
}
