<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\EspionageReport;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\CounterEspionageService;
use OGame\Services\DebrisFieldService;
use OGame\Services\PlanetService;
use Throwable;

class EspionageMission extends GameMission
{
    protected static string $name = 'Espionage';
    protected static int $typeId = 6;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::war;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Hostile;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Cannot send missions while in vacation mode
        if ($planet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'You cannot send missions while in vacation mode!');
        }

        // Espionage mission is only possible for planets and moons.
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

        // If no espionage probes are present in the fleet, the mission is not possible.
        if ($units->getAmountByMachineName('espionage_probe') === 0) {
            return new MissionPossibleStatus(false);
        }

        // If mission from and to coordinates and types are the same, the mission is not possible.
        if ($planet->getPlanetCoordinates()->equals($targetCoordinate) && $planet->getPlanetType() === $targetType) {
            return new MissionPossibleStatus(false);
        }

        // If target player is in vacation mode, the mission is not possible.
        $targetPlayer = $targetPlanet->getPlayer();
        if ($targetPlayer->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'This player is in vacation mode!');
        }

        // Legor's planet (Arakis at 1:1:2) cannot be probed
        if ($targetPlayer->getUsername(false) === 'Legor') {
            return new MissionPossibleStatus(false, 'This planet belongs to an administrator and cannot be probed.');
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
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);
        $origin_planet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Trigger target planet update to make sure the espionage report is accurate.
        $target_planet->update();

        // Calculate counter-espionage chance
        $counterEspionageService = resolve(CounterEspionageService::class);
        $attackerProbeCount = $mission->espionage_probe;
        $attackerEspionageLevel = $origin_planet->getPlayer()->getResearchLevel('espionage_technology');
        $defenderEspionageLevel = $target_planet->getPlayer()->getResearchLevel('espionage_technology');

        // TODO: Include ACS Defend fleets in counter-espionage chance calculation
        // Currently only counts planet owner's ships via getDefenderShipCount()
        // Should also count ships from ACS Defend fleets present at the target planet
        // This creates inconsistency: ACS fleets participate in battle but don't affect detection chance
        $defenderShipCount = $counterEspionageService->getDefenderShipCount($target_planet);

        $counterEspionageChance = $counterEspionageService->calculateChance(
            $attackerProbeCount,
            $attackerEspionageLevel,
            $defenderEspionageLevel,
            $defenderShipCount
        );

        // Get fleet units for potential battle and return
        $units = $this->fleetMissionService->getFleetUnits($mission);
        $survivingUnits = clone $units;

        // Check if counter-espionage is triggered
        $counterEspionageTriggered = false;
        if ($counterEspionageChance > 0) {
            $counterEspionageTriggered = $counterEspionageService->rollCounterEspionage($counterEspionageChance);
        }

        if ($counterEspionageTriggered) {
            // Execute counter-espionage battle
            $battleResult = $this->executeCounterEspionageBattle($origin_planet, $target_planet, $units, $counterEspionageService, $mission->id, $mission->user_id);

            // Set the attacker's origin planet ID on the battle result for the battle report.
            $battleResult->attackerPlanetId = $mission->planet_id_from;

            // Create or append debris field.
            // TODO: we could change this debris field append logic to do everything in a single query to
            // prevent race conditions. Check this later when looking into reducing chance of race conditions occurring.
            // (This TODO is copied from AttackMission.php for consistency)
            $debrisFieldService = resolve(DebrisFieldService::class);
            $debrisFieldService->loadOrCreateForCoordinates($target_planet->getPlanetCoordinates());

            // Add debris to the field
            $debrisFieldService->appendResources($battleResult->debris);

            // Save the debris field
            $debrisFieldService->save();

            // Create battle report for defender
            $battleReportId = $this->createCounterEspionageBattleReport($origin_planet->getPlayer(), $target_planet, $battleResult);
            $this->messageService->sendBattleReportMessageToPlayer($target_planet->getPlayer(), $battleReportId);

            // Update surviving units based on battle result
            $survivingUnits = $battleResult->attackerUnitsResult;

            // If all probes destroyed, send "fleet lost contact" message to attacker
            if ($survivingUnits->getAmount() === 0) {
                $this->messageService->sendFleetLostContactMessageToPlayer(
                    $origin_planet->getPlayer(),
                    '[' . $target_planet->getPlanetCoordinates()->asString() . ']'
                );
            }
        }

        // Always create espionage report (even if all probes destroyed)
        $reportId = $this->createEspionageReport($mission, $origin_planet, $target_planet, $counterEspionageChance);

        // --- Defender notification (mirror official OGame "you were spied" message)
        // Defender is the owner of the target planet
        $defenderUserId = $target_planet->getPlayer()->getId();

        if ($defenderUserId) {
            // Params expected by t_messages.espionage_detected.*
            $attackerName = $origin_planet->getPlayer()->getUsername();

            $params = [
                // IMPORTANT: pass the raw mission planet id inside [planet]...[/planet]
                'planet'        => '[planet]' . $mission->planet_id_from . '[/planet]',
                'defender'      => '[planet]' . $mission->planet_id_to . '[/planet]',   // defender planet
                'attacker_name' => $attackerName,
                'chance'        => $counterEspionageChance,
            ];

            $playerServiceFactory = resolve(\OGame\Factories\PlayerServiceFactory::class);
            $defenderService      = $playerServiceFactory->make($defenderUserId);

            $this->messageService->sendSystemMessageToPlayer(
                $defenderService,
                \OGame\GameMessages\DefenderEspionageDetected::class,
                $params
            );
        }

        // Send a message to the player with a reference to the espionage report.
        $this->messageService->sendEspionageReportMessageToPlayer(
            $origin_planet->getPlayer(),
            $reportId,
        );

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create return mission only if probes survived
        if ($survivingUnits->getAmount() > 0) {
            $this->startReturn($mission, $this->fleetMissionService->getResources($mission), $survivingUnits);
        }
    }

    /**
     * Execute counter-espionage battle between probes and defender ships.
     *
     * In counter-espionage, only the defender's ships participate in the battle,
     * not defense structures. This is per OGame mechanics.
     *
     * @param PlanetService $originPlanet
     * @param PlanetService $targetPlanet
     * @param UnitCollection $attackerUnits
     * @param CounterEspionageService $counterEspionageService
     * @param int $fleetMissionId
     * @param int $ownerId
     * @return \OGame\GameMissions\BattleEngine\Models\BattleResult
     */
    private function executeCounterEspionageBattle(
        PlanetService $originPlanet,
        PlanetService $targetPlanet,
        UnitCollection $attackerUnits,
        CounterEspionageService $counterEspionageService,
        int $fleetMissionId,
        int $ownerId
    ): \OGame\GameMissions\BattleEngine\Models\BattleResult {
        $attackerPlayer = $originPlanet->getPlayer();

        // Get only ships for counter-espionage battle (no defense)
        $defenderShips = $counterEspionageService->getDefenderShipsForBattle($targetPlanet);

        // Temporarily remove defense from planet for the battle
        $originalDefense = $targetPlanet->getDefenseUnits();
        foreach ($originalDefense->units as $unit) {
            $targetPlanet->removeUnit($unit->unitObject->machine_name, $unit->amount, false);
        }

        // Collect all defending fleets (planet owner + ACS defend fleets)
        $defenders = $this->collectDefendingFleets($targetPlanet);

        // Execute battle using configured battle engine
        switch ($this->settings->battleEngine()) {
            case 'php':
                $battleEngine = new PhpBattleEngine($attackerUnits, $attackerPlayer, $targetPlanet, $defenders, $this->settings, $fleetMissionId, $ownerId);
                break;
            case 'rust':
            default:
                $battleEngine = new RustBattleEngine($attackerUnits, $attackerPlayer, $targetPlanet, $defenders, $this->settings, $fleetMissionId, $ownerId);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Restore defense to planet (they were not part of the battle)
        foreach ($originalDefense->units as $unit) {
            $targetPlanet->addUnit($unit->unitObject->machine_name, $unit->amount, false);
        }

        // Process defender fleet results (planet owner + ACS defend fleets)
        foreach ($battleResult->defenderFleetResults as $fleetResult) {
            if ($fleetResult->fleetMissionId === 0) {
                // Planet owner's ships - remove permanently lost ships (no defense repair in counter-espionage)
                if ($fleetResult->unitsLost->getAmount() > 0) {
                    $targetPlanet->removeUnits($fleetResult->unitsLost, false);
                }
                $targetPlanet->save();
            } else {
                // ACS Defend fleet - handle return or destruction
                $defendMission = \OGame\Models\FleetMission::find($fleetResult->fleetMissionId);
                if ($defendMission) {
                    if ($fleetResult->completelyDestroyed) {
                        // Fleet was completely destroyed - no return mission
                        $defendMission->processed = 1;
                        $defendMission->save();

                        // Send fleet lost contact message to the fleet owner
                        $fleetOwner = $this->playerServiceFactory->make($fleetResult->ownerId);
                        $coordinates = '[coordinates]' . $targetPlanet->getPlanetCoordinates()->asString() . '[/coordinates]';
                        $this->messageService->sendSystemMessageToPlayer($fleetOwner, \OGame\GameMessages\FleetLostContact::class, [
                            'coordinates' => $coordinates,
                        ]);
                    } else {
                        // Fleet survived - create return mission with surviving units
                        $this->startReturn($defendMission, new \OGame\Models\Resources(0, 0, 0, 0), $fleetResult->unitsResult);
                    }
                }
            }
        }

        return $battleResult;
    }

    /**
     * Create a battle report for counter-espionage battle.
     *
     * @param \OGame\Services\PlayerService $attackerPlayer
     * @param PlanetService $defenderPlanet
     * @param \OGame\GameMissions\BattleEngine\Models\BattleResult $battleResult
     * @return int
     */
    private function createCounterEspionageBattleReport(
        \OGame\Services\PlayerService $attackerPlayer,
        PlanetService $defenderPlanet,
        \OGame\GameMissions\BattleEngine\Models\BattleResult $battleResult
    ): int {
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
        ];

        $report->attacker = [
            'player_id' => $attackerPlayer->getId(),
            'resource_loss' => $battleResult->attackerResourceLoss->sum(),
            'units' => $battleResult->attackerUnitsStart->toArray(),
            'weapon_technology' => $battleResult->attackerWeaponLevel,
            'shielding_technology' => $battleResult->attackerShieldLevel,
            'armor_technology' => $battleResult->attackerArmorLevel,
            'planet_id' => $battleResult->attackerPlanetId,
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
            'percentage' => 0,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
        ];

        $report->debris = [
            'metal' => $battleResult->debris->metal->get(),
            'crystal' => $battleResult->debris->crystal->get(),
            'deuterium' => $battleResult->debris->deuterium->get(),
        ];

        $report->repaired_defenses = [];

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
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Espionage return trip: add back the units to the source planet. Then we're done.
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any).
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Espionage return mission does not send a return confirmation message to the user.

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Creates an espionage report for the target planet.
     *
     * @param FleetMission $mission
     * @param PlanetService $originPlanet
     * @param PlanetService $targetPlanet
     * @param int $counterEspionageChance
     * @return int
     */
    private function createEspionageReport(FleetMission $mission, PlanetService $originPlanet, PlanetService $targetPlanet, int $counterEspionageChance = 0): int
    {
        // Create new espionage report record.
        $report = new EspionageReport();
        $report->planet_galaxy = $targetPlanet->getPlanetCoordinates()->galaxy;
        $report->planet_system = $targetPlanet->getPlanetCoordinates()->system;
        $report->planet_position = $targetPlanet->getPlanetCoordinates()->position;
        $report->planet_type = $targetPlanet->getPlanetType()->value;

        $report->planet_user_id = $targetPlanet->getPlayer()->getId();

        $report->player_info = [
            'player_id' => (string)$targetPlanet->getPlayer()->getId(),
            'player_name' => $targetPlanet->getPlayer()->getUsername(),
        ];

        // Resources
        $report->resources = [
            'metal' => (int)$targetPlanet->metal()->get(),
            'crystal' => (int)$targetPlanet->crystal()->get(),
            'deuterium' => (int)$targetPlanet->deuterium()->get(),
            'energy' => (int)$targetPlanet->energy()->get()
        ];

        // Debris field (if any).
        $debrisField = resolve(DebrisFieldService::class);
        $debrisField->loadOrCreateForCoordinates($targetPlanet->getPlanetCoordinates());
        $debrisFieldResources = $debrisField->getResources();
        if ($debrisFieldResources->any()) {
            $report->debris = [
                'metal' => (int)$debrisFieldResources->metal->get(),
                'crystal' => (int)$debrisFieldResources->crystal->get(),
                'deuterium' => (int)$debrisFieldResources->deuterium->get(),
            ];
        }

        // TODO: Validate this does not cause issues when probing slot 16
        $attackerEspionageLevel = $originPlanet->getPlayer()->getResearchLevel('espionage_technology');
        $defenderEspionageLevel = $targetPlanet->getPlayer()->getResearchLevel('espionage_technology');
        $techDifference = $defenderEspionageLevel - $attackerEspionageLevel;
        $levelDifference = max(0, $techDifference);
        $extraProbesRequired = pow($levelDifference, 2);
        $remainingProbes = max(0, $mission->espionage_probe - $extraProbesRequired);

        // Fleets
        // TODO: Include ACS Defend fleets in espionage report
        // Currently only shows planet owner's ships via getShipUnits()
        // Should also show ACS Defend fleet units present at the target planet
        // This creates inconsistency: ACS fleets are invisible to spy but participate in counter-espionage battles
        // Consider showing them in a separate section or with owner labels
        if ($this->canRevealData($remainingProbes, $attackerEspionageLevel, $defenderEspionageLevel, 2, 1)) {
            $report->ships = $targetPlanet->getShipUnits()->toArray();
        }

        // Defense
        if ($this->canRevealData($remainingProbes, $attackerEspionageLevel, $defenderEspionageLevel, 3, 2)) {
            $report->defense = $targetPlanet->getDefenseUnits()->toArray();
        }

        // Buildings
        if ($this->canRevealData($remainingProbes, $attackerEspionageLevel, $defenderEspionageLevel, 5, 3)) {
            $report->buildings = $targetPlanet->getBuildingArray();
        }

        // Research
        if ($this->canRevealData($remainingProbes, $attackerEspionageLevel, $defenderEspionageLevel, 7, 4)) {
            $report->research = $targetPlanet->getPlayer()->getResearchArray();
        }

        // Store counter-espionage chance
        $report->counter_espionage_chance = $counterEspionageChance;

        $report->save();

        return $report->id;
    }

    /**
     * Determine if specific data can be revealed in the report based on remaining probes and espionage levels.
     *
     * @param int $remainingProbes
     * @param int $attackerLevel
     * @param int $defenderLevel
     * @param int $probeThreshold
     * @param int $levelThreshold
     * @return bool
     */
    private function canRevealData(int $remainingProbes, int $attackerLevel, int $defenderLevel, int $probeThreshold, int $levelThreshold): bool
    {
        return $remainingProbes >= $probeThreshold || $attackerLevel - $levelThreshold >= $defenderLevel;
    }
}
