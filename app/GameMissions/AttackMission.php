<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\DebrisFieldService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\WreckFieldService;
use Throwable;

class AttackMission extends GameMission
{
    protected static string $name = 'Attack';
    protected static int $typeId = 1;
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

        // Attack mission is only possible for planets and moons.
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

        // If target player is in vacation mode, the mission is not possible.
        $targetPlayer = $targetPlanet->getPlayer();
        if ($targetPlayer->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'This player is in vacation mode!');
        }

        // Legor's planet (Arakis at 1:1:2) cannot be attacked
        if ($targetPlayer->getUsername(false) === 'Legor') {
            return new MissionPossibleStatus(false, __('t_messages.This planet belongs to an administrator and cannot be attacked.'));
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
                // Default to RustBattleEngine if no specific engine is configured
                $battleEngine = new RustBattleEngine($attackerUnits, $attackerPlayer, $defenderPlanet, $this->settings);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Set the attacker's origin planet ID on the battle result for the battle report.
        $battleResult->attackerPlanetId = $mission->planet_id_from;

        // Deduct loot from the target planet.
        $defenderPlanet->deductResources($battleResult->loot);

        // Deduct defender's permanently lost units from the defenders planet.
        // Repaired defenses are not removed (destroyed - repaired = permanently lost).
        $defenderUnitsLost = clone $battleResult->defenderUnitsStart;
        $defenderUnitsLost->subtractCollection($battleResult->defenderUnitsResult);

        // Calculate permanently lost units (destroyed - repaired)
        $permanentlyLostUnits = clone $defenderUnitsLost;
        $permanentlyLostUnits->subtractCollection($battleResult->repairedDefenses);
        $defenderPlanet->removeUnits($permanentlyLostUnits, false);

        // Save defenders planet
        $defenderPlanet->save();

        // Create or append debris field.
        // TODO: we could change this debris field append logic to do everything in a single query to
        // prevent race conditions. Check this later when looking into reducing chance of race conditions occurring.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($defenderPlanet->getPlanetCoordinates());

        // Add debris to the field
        $debrisFieldService->appendResources($battleResult->debris);

        // Save the debris field
        $debrisFieldService->save();

        // Create or extend wreck field if conditions are met
        //
        // TODO: When the General class is implemented, wreck fields generated during attacks with a General
        // should behave differently: the wreck field should only be spawned at the General's origin planet
        // once the attacking fleet has returned from the mission. This means the wreck field data needs to
        // be stored with the fleet mission and created at the origin planet coordinates upon mission return.
        //
        // Current behavior: wreck field is created immediately at the battle location.
        // General behavior (future): wreck field data stored with mission, created at origin planet on return.
        //
        // IMPORTANT: If the battle is on a moon, the wreck field is created at the planet's coordinates
        // (not the moon's), and can only be interacted with from the planet.
        if (!empty($battleResult->wreckField) && $battleResult->wreckField['formed']) {
            $wreckFieldService = new WreckFieldService($defenderPlanet->getPlayer(), $this->settings);

            // Determine the coordinates for the wreck field
            // If battle is on a moon, use the planet's coordinates. If on a planet, use its own coordinates.
            $wreckFieldCoordinates = $defenderPlanet->isMoon()
                ? $defenderPlanet->planet()->getPlanetCoordinates()
                : $defenderPlanet->getPlanetCoordinates();

            $wreckField = $wreckFieldService->createWreckField(
                $wreckFieldCoordinates,
                $battleResult->wreckField['ships'],
                $defenderPlanet->getPlayer()->getId()
            );
        }

        // Create a moon for defender if result of battle indicates so and defender planet does not already have a moon.
        // Only create moon if defender is a planet (not already a moon).
        if ($defenderPlanet->isPlanet() && !$defenderPlanet->hasMoon() && $battleResult->moonCreated) {
            $debrisAmount = (int)$battleResult->debris->sum();
            $this->planetServiceFactory->createMoonForPlanet($defenderPlanet, $debrisAmount, $battleResult->moonChance);
        }

        // Check if attacker fleet was destroyed in first round
        $attackerDestroyedFirstRound = false;
        if (count($battleResult->rounds) > 0) {
            $firstRound = $battleResult->rounds[0];
            if ($firstRound->attackerShips->getAmount() === 0) {
                $attackerDestroyedFirstRound = true;
            }
        }

        if ($attackerDestroyedFirstRound) {
            // Send simplified "fleet lost contact" message to attacker (no fleet or tech info)
            $coordinates = '[coordinates]' . $defenderPlanet->getPlanetCoordinates()->asString() . '[/coordinates]';
            $this->messageService->sendSystemMessageToPlayer($attackerPlayer, \OGame\GameMessages\FleetLostContact::class, [
                'coordinates' => $coordinates,
            ]);

            // Send full battle report to defender
            $reportId = $this->createBattleReport($attackerPlayer, $defenderPlanet, $battleResult);
            $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);
        } else {
            // Normal behavior: send battle report to both attacker and defender
            $reportId = $this->createBattleReport($attackerPlayer, $defenderPlanet, $battleResult);
            // Send to attacker.
            $this->messageService->sendBattleReportMessageToPlayer($attackerPlayer, $reportId);
            // Send to defender.
            $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);
        }

        // Mark the arrival mission as processed
        $mission->processed = 1;
        $mission->save();

        // Create and start the return mission (if attacker has remaining units).
        // Calculate survival rate based on cargo capacity before and after battle
        $originalCargoCapacity = $battleResult->attackerUnitsStart->getTotalCargoCapacity($attackerPlayer);
        $remainingCargoCapacity = $battleResult->attackerUnitsResult->getTotalCargoCapacity($attackerPlayer);

        // Handle edge case: if original capacity is 0, survival rate is 0
        $survivalRate = $originalCargoCapacity > 0
            ? $remainingCargoCapacity / $originalCargoCapacity
            : 0;

        // Calculate resources remaining on surviving ships
        $remainingResources = new Resources(
            max(0, (int)($mission->metal * $survivalRate)),
            max(0, (int)($mission->crystal * $survivalRate)),
            max(0, (int)($mission->deuterium * $survivalRate)),
            0
        );

        // Calculate loot remaining on surviving ships
        // Loot is also subject to the survival rate (if cargo ships carrying loot are destroyed, loot is lost)
        $remainingLoot = new Resources(
            max(0, (int)($battleResult->loot->metal->get() * $survivalRate)),
            max(0, (int)($battleResult->loot->crystal->get() * $survivalRate)),
            max(0, (int)($battleResult->loot->deuterium->get() * $survivalRate)),
            0
        );

        // Total resources = remaining mission resources + remaining loot
        $totalResources = new Resources(
            $remainingResources->metal->get() + $remainingLoot->metal->get(),
            $remainingResources->crystal->get() + $remainingLoot->crystal->get(),
            $remainingResources->deuterium->get() + $remainingLoot->deuterium->get(),
            0
        );

        // Ensure total doesn't exceed remaining capacity
        if ($totalResources->sum() > $remainingCargoCapacity) {
            $totalResources = LootService::distributeLoot($totalResources, $remainingCargoCapacity);
        }

        $this->startReturn($mission, $totalResources, $battleResult->attackerUnitsResult);
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Attack return trip: add back the units to the source planet. Then we're done.
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
     * @return int
     */
    private function createBattleReport(PlayerService $attackPlayer, PlanetService $defenderPlanet, BattleResult $battleResult): int
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
        ];

        $report->attacker = [
            'player_id' => $attackPlayer->getId(),
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

        $report->repaired_defenses = $battleResult->repairedDefenses->toArray();

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
}
