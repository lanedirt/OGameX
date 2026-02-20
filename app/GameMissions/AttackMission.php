<?php

namespace OGame\GameMissions;

use OGame\Enums\FleetMissionStatus;
use OGame\Enums\FleetSpeedType;
use OGame\GameMessages\DebrisFieldHarvest;
use OGame\GameMessages\FleetLostContact;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\BattleEngine\Models\AttackerFleet;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameMissions\BattleEngine\Services\LootService;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Enums\GameObjectType;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\CharacterClassService;
use OGame\Services\DebrisFieldService;
use OGame\Services\ObjectService;
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
        $parentCheck = parent::isMissionPossible($planet, $targetCoordinate, $targetType, $units);
        if (!$parentCheck->possible) {
            return $parentCheck;
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
        if ($ownPlanetCheck = $this->checkOwnPlanet($planet, $targetPlanet)) {
            return $ownPlanetCheck;
        }

        // If target player is in vacation mode, the mission is not possible.
        if ($vacationCheck = $this->checkTargetVacationMode($targetPlanet)) {
            return $vacationCheck;
        }

        // Legor's planet (Arakis at 1:1:2) cannot be attacked
        if ($adminCheck = $this->checkAdminProtection($targetPlanet, __('This planet belongs to an administrator and cannot be attacked.'))) {
            return $adminCheck;
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

        // Create AttackerFleet for the mission
        $attackerFleet = AttackerFleet::fromFleetMission($mission, $this->fleetMissionService, $this->playerServiceFactory, true);

        // Collect all defending fleets (planet owner + ACS defend fleets)
        $defenders = $this->collectDefendingFleets($defenderPlanet);

        // Execute the battle logic using configured battle engine
        switch ($this->settings->battleEngine()) {
            case 'php':
                $battleEngine = new PhpBattleEngine([$attackerFleet], $defenderPlanet, $defenders, $this->settings);
                break;
            case 'rust':
            default:
                // Default to RustBattleEngine if no specific engine is configured
                $battleEngine = new RustBattleEngine([$attackerFleet], $defenderPlanet, $defenders, $this->settings);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Set the attacker's origin planet ID on the battle result for the battle report.
        $battleResult->attackerPlanetId = $mission->planet_id_from;

        // Update military statistics for both players
        $this->updateMilitaryStatistics($attackerPlayer, $defenderPlanet->getPlayer(), $battleResult);

        // Deduct loot from the target planet.
        $defenderPlanet->deductResources($battleResult->loot);

        // Process defender fleet results (planet owner + ACS defend fleets)
        foreach ($battleResult->defenderFleetResults as $fleetResult) {
            if ($fleetResult->fleetMissionId === 0) {
                // Planet owner's stationary forces - remove permanently lost units
                // Calculate permanently lost: lost units minus repaired defenses
                $permanentlyLostUnits = clone $fleetResult->unitsLost;

                // Safely subtract repaired defenses - only subtract units that actually exist in the lost units
                if ($battleResult->repairedDefenses->getAmount() > 0) {
                    foreach ($battleResult->repairedDefenses->units as $repairedUnit) {
                        // Only subtract if this unit type exists in our lost units
                        if ($permanentlyLostUnits->hasUnit($repairedUnit->unitObject)) {
                            $permanentlyLostUnits->removeUnit($repairedUnit->unitObject, $repairedUnit->amount, true);
                        }
                    }
                }

                // Only remove units if there are any to remove
                if ($permanentlyLostUnits->getAmount() > 0) {
                    $defenderPlanet->removeUnits($permanentlyLostUnits, false);
                }

                $defenderPlanet->save();
            } else {
                // ACS Defend fleet - handle return or destruction
                $defendMission = FleetMission::find($fleetResult->fleetMissionId);
                if ($defendMission) {
                    if ($fleetResult->completelyDestroyed) {
                        // Fleet was completely destroyed - no return mission
                        $defendMission->processed = 1;
                        $defendMission->save();

                        // Send fleet lost contact message to the fleet owner
                        $fleetOwner = $this->playerServiceFactory->make($fleetResult->ownerId);
                        $coordinates = '[coordinates]' . $defenderPlanet->getPlanetCoordinates()->asString() . '[/coordinates]';
                        $this->messageService->sendSystemMessageToPlayer($fleetOwner, FleetLostContact::class, [
                            'coordinates' => $coordinates,
                        ]);
                    } else {
                        // Fleet survived - create return mission with surviving units
                        // Calculate resource survival rate based on cargo capacity
                        $fleetOwner = $this->playerServiceFactory->make($fleetResult->ownerId);
                        $originalUnits = $this->fleetMissionService->getFleetUnits($defendMission);
                        $originalCargoCapacity = $originalUnits->getTotalCargoCapacity($fleetOwner);
                        $remainingCargoCapacity = $fleetResult->unitsResult->getTotalCargoCapacity($fleetOwner);

                        // Handle edge case: if original capacity is 0, survival rate is 0
                        $survivalRate = $originalCargoCapacity > 0
                            ? $remainingCargoCapacity / $originalCargoCapacity
                            : 0;

                        // Calculate resources remaining on surviving ships
                        $remainingResources = new Resources(
                            max(0, (int)($defendMission->metal * $survivalRate)),
                            max(0, (int)($defendMission->crystal * $survivalRate)),
                            max(0, (int)($defendMission->deuterium * $survivalRate)),
                            0
                        );

                        $this->startReturn($defendMission, $remainingResources, $fleetResult->unitsResult);
                    }
                }
            }
        }

        // Create or append debris field.
        // TODO: we could change this debris field append logic to do everything in a single query to
        // prevent race conditions. Check this later when looking into reducing chance of race conditions occurring.
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($defenderPlanet->getPlanetCoordinates());

        // Check if attacker has Reaper ships for automatic debris collection (General class only)
        $attackerCollectedDebris = new Resources(0, 0, 0, 0);
        $defenderCollectedDebris = new Resources(0, 0, 0, 0);
        $characterClassService = app(CharacterClassService::class);

        // Attacker Reaper collection
        $attackerDebrisCollectionPercentage = $characterClassService->getReaperDebrisCollectionPercentage($attackerPlayer->getUser());
        if ($attackerDebrisCollectionPercentage > 0 && $battleResult->attackerUnitsResult->getAmountByMachineName('reaper') > 0) {
            // Calculate 30% of the debris to be collected automatically
            $collectionAmount = new Resources(
                (int)($battleResult->debris->metal->get() * $attackerDebrisCollectionPercentage),
                (int)($battleResult->debris->crystal->get() * $attackerDebrisCollectionPercentage),
                (int)($battleResult->debris->deuterium->get() * $attackerDebrisCollectionPercentage),
                0
            );

            // Calculate Reaper cargo capacity
            $reaperObject = ObjectService::getShipObjectByMachineName('reaper');
            $reaperCount = $battleResult->attackerUnitsResult->getAmountByMachineName('reaper');
            $reaperCargoCapacity = $reaperObject->properties->capacity->calculate($attackerPlayer)->totalValue * $reaperCount;

            // Limit collected debris to Reaper cargo capacity
            // (Can collect maximum 30% of debris OR Reaper capacity, whichever is lower)
            if ($collectionAmount->sum() <= $reaperCargoCapacity) {
                $attackerCollectedDebris = $collectionAmount;
            } else {
                // Distribute the 30% debris amount across Reaper capacity
                $attackerCollectedDebris = LootService::distributeLoot($collectionAmount, $reaperCargoCapacity);
            }
        }

        // Calculate remaining debris after attacker collection
        $debrisAfterAttackerCollection = new Resources(
            $battleResult->debris->metal->get() - $attackerCollectedDebris->metal->get(),
            $battleResult->debris->crystal->get() - $attackerCollectedDebris->crystal->get(),
            $battleResult->debris->deuterium->get() - $attackerCollectedDebris->deuterium->get(),
            0
        );

        // Defender Reaper collection (from remaining debris after attacker collection)
        $defenderDebrisCollectionPercentage = $characterClassService->getReaperDebrisCollectionPercentage($defenderPlanet->getPlayer()->getUser());
        if ($defenderDebrisCollectionPercentage > 0 && $battleResult->defenderUnitsResult->getAmountByMachineName('reaper') > 0) {
            // Calculate 30% of the remaining debris
            $collectionAmount = new Resources(
                (int)($debrisAfterAttackerCollection->metal->get() * $defenderDebrisCollectionPercentage),
                (int)($debrisAfterAttackerCollection->crystal->get() * $defenderDebrisCollectionPercentage),
                (int)($debrisAfterAttackerCollection->deuterium->get() * $defenderDebrisCollectionPercentage),
                0
            );

            // Calculate defender Reaper cargo capacity
            $reaperObject = ObjectService::getShipObjectByMachineName('reaper');
            $defenderReaperCount = $battleResult->defenderUnitsResult->getAmountByMachineName('reaper');
            $defenderReaperCargoCapacity = $reaperObject->properties->capacity->calculate($defenderPlanet->getPlayer())->totalValue * $defenderReaperCount;

            // Limit collected debris to Reaper cargo capacity
            if ($collectionAmount->sum() <= $defenderReaperCargoCapacity) {
                $defenderCollectedDebris = $collectionAmount;
            } else {
                // Distribute the 30% debris amount across Reaper capacity
                $defenderCollectedDebris = LootService::distributeLoot($collectionAmount, $defenderReaperCargoCapacity);
            }

            // Add collected debris to defender planet's resources
            $defenderPlanet->addResources($defenderCollectedDebris);
            $defenderPlanet->save();
        }

        // Total collected debris for battle report
        $collectedDebris = new Resources(
            $attackerCollectedDebris->metal->get() + $defenderCollectedDebris->metal->get(),
            $attackerCollectedDebris->crystal->get() + $defenderCollectedDebris->crystal->get(),
            $attackerCollectedDebris->deuterium->get() + $defenderCollectedDebris->deuterium->get(),
            0
        );

        // Add debris to the field (minus what was collected by both attacker and defender Reapers)
        $remainingDebris = new Resources(
            $debrisAfterAttackerCollection->metal->get() - $defenderCollectedDebris->metal->get(),
            $debrisAfterAttackerCollection->crystal->get() - $defenderCollectedDebris->crystal->get(),
            $debrisAfterAttackerCollection->deuterium->get() - $defenderCollectedDebris->deuterium->get(),
            0
        );
        $debrisFieldService->appendResources($remainingDebris);

        // Save the debris field
        $debrisFieldService->save();

        // Create or extend wreck field at defender's location if conditions are met
        // Note: If attacker is General class, a separate wreck field will be created at the attacker's
        // origin planet when the return mission arrives (see processReturn method).
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
            $this->messageService->sendSystemMessageToPlayer($attackerPlayer, FleetLostContact::class, [
                'coordinates' => $coordinates,
            ]);

            // Send full battle report to defender
            $reportId = $this->createBattleReport($attackerPlayer, $defenderPlanet, $battleResult, $collectedDebris, $attackerCollectedDebris, $defenderCollectedDebris);
            $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);
        } else {
            // Normal behavior: send battle report to both attacker and defender
            $reportId = $this->createBattleReport($attackerPlayer, $defenderPlanet, $battleResult, $collectedDebris, $attackerCollectedDebris, $defenderCollectedDebris);
            // Send to attacker.
            $this->messageService->sendBattleReportMessageToPlayer($attackerPlayer, $reportId);
            // Send to defender.
            $this->messageService->sendBattleReportMessageToPlayer($defenderPlanet->getPlayer(), $reportId);
        }

        // Send Reaper auto-collection message to attacker if debris was collected
        if ($attackerCollectedDebris->sum() > 0) {
            $reaperObject = ObjectService::getShipObjectByMachineName('reaper');
            $reaperCount = $battleResult->attackerUnitsResult->getAmountByMachineName('reaper');
            $reaperCargoCapacity = $reaperObject->properties->capacity->calculate($attackerPlayer)->totalValue * $reaperCount;

            $this->messageService->sendSystemMessageToPlayer($attackerPlayer, DebrisFieldHarvest::class, [
                'from' => '[planet]' . $mission->planet_id_from . '[/planet]',
                'to' => '[debrisfield]' . $defenderPlanet->getPlanetCoordinates()->asString(). '[/debrisfield]',
                'coordinates' => '[coordinates]' . $defenderPlanet->getPlanetCoordinates()->asString() . '[/coordinates]',
                'ship_name' => $reaperObject->title,
                'ship_amount' => $reaperCount,
                'storage_capacity' => $reaperCargoCapacity,
                'metal' => (int)($battleResult->debris->metal->get() * $attackerDebrisCollectionPercentage),
                'crystal' => (int)($battleResult->debris->crystal->get() * $attackerDebrisCollectionPercentage),
                'deuterium' => (int)($battleResult->debris->deuterium->get() * $attackerDebrisCollectionPercentage),
                'harvested_metal' => $attackerCollectedDebris->metal->get(),
                'harvested_crystal' => $attackerCollectedDebris->crystal->get(),
                'harvested_deuterium' => $attackerCollectedDebris->deuterium->get(),
            ]);
        }

        // Send Reaper auto-collection message to defender if debris was collected
        if ($defenderCollectedDebris->sum() > 0) {
            $reaperObject = ObjectService::getShipObjectByMachineName('reaper');
            $defenderReaperCount = $battleResult->defenderUnitsResult->getAmountByMachineName('reaper');
            $defenderReaperCargoCapacity = $reaperObject->properties->capacity->calculate($defenderPlanet->getPlayer())->totalValue * $defenderReaperCount;

            $this->messageService->sendSystemMessageToPlayer($defenderPlanet->getPlayer(), DebrisFieldHarvest::class, [
                'from' => '[planet]' . $defenderPlanet->getPlanetId() . '[/planet]',
                'to' => '[debrisfield]' . $defenderPlanet->getPlanetCoordinates()->asString(). '[/debrisfield]',
                'coordinates' => '[coordinates]' . $defenderPlanet->getPlanetCoordinates()->asString() . '[/coordinates]',
                'ship_name' => $reaperObject->title,
                'ship_amount' => $defenderReaperCount,
                'storage_capacity' => $defenderReaperCargoCapacity,
                'metal' => (int)($debrisAfterAttackerCollection->metal->get() * $defenderDebrisCollectionPercentage),
                'crystal' => (int)($debrisAfterAttackerCollection->crystal->get() * $defenderDebrisCollectionPercentage),
                'deuterium' => (int)($debrisAfterAttackerCollection->deuterium->get() * $defenderDebrisCollectionPercentage),
                'harvested_metal' => $defenderCollectedDebris->metal->get(),
                'harvested_crystal' => $defenderCollectedDebris->crystal->get(),
                'harvested_deuterium' => $defenderCollectedDebris->deuterium->get(),
            ]);
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

        // Total resources = remaining mission resources + remaining loot + collected debris (from attacker Reapers)
        $totalResources = new Resources(
            $remainingResources->metal->get() + $remainingLoot->metal->get() + $attackerCollectedDebris->metal->get(),
            $remainingResources->crystal->get() + $remainingLoot->crystal->get() + $attackerCollectedDebris->crystal->get(),
            $remainingResources->deuterium->get() + $remainingLoot->deuterium->get() + $attackerCollectedDebris->deuterium->get(),
            0
        );

        // Ensure total doesn't exceed remaining capacity
        if ($totalResources->sum() > $remainingCargoCapacity) {
            $totalResources = LootService::distributeLoot($totalResources, $remainingCargoCapacity);
        }

        // Calculate wreck field for General class attacker
        // General perk: wreck field from attacker's lost ships is transported back with the return mission
        $attackerWreckFieldData = null;
        $characterClassService = resolve(CharacterClassService::class);
        if ($characterClassService->isGeneral($attackerPlayer->getUser())) {
            // Calculate attacker's lost units (start - result = lost)
            $attackerUnitsLost = clone $battleResult->attackerUnitsStart;
            $attackerUnitsLost->subtractCollection($battleResult->attackerUnitsResult);

            // Calculate wreck field data if conditions are met
            $attackerWreckFieldData = $this->calculateAttackerWreckField($attackerUnitsLost, $battleResult->attackerUnitsStart);
        }

        $this->startReturn($mission, $totalResources, $battleResult->attackerUnitsResult, 0, $attackerWreckFieldData);
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

        // Create wreck field at origin planet if data exists (General class perk)
        // The wreck field is created from the attacker's lost ships and appears at the origin planet
        if (!empty($mission->wreck_field_data) && is_array($mission->wreck_field_data)) {
            $wreckFieldService = new WreckFieldService($target_planet->getPlayer(), $this->settings);

            // Determine coordinates for wreck field
            // If returning to a moon, create wreck field at the planet's coordinates
            $wreckFieldCoordinates = $target_planet->isMoon()
                ? $target_planet->planet()->getPlanetCoordinates()
                : $target_planet->getPlanetCoordinates();

            // Create wreck field at origin planet
            $wreckFieldService->createWreckField(
                $wreckFieldCoordinates,
                $mission->wreck_field_data,
                $target_planet->getPlayer()->getId()
            );
        }

        // Send message to player that the return mission has arrived.
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }

    /**
     * Calculate the wreck field for attacker's lost ships (General class perk).
     * Similar logic to defender wreck field but for attacker's ships.
     *
     * @param UnitCollection $attackerUnitsLost Units lost by the attacker.
     * @param UnitCollection $attackerUnitsStart Starting units of the attacker.
     * @return array<array{machine_name: string, quantity: int, repair_progress: int}>|null Wreck field data with ships array, or null if conditions not met.
     */
    private function calculateAttackerWreckField(UnitCollection $attackerUnitsLost, UnitCollection $attackerUnitsStart): array|null
    {
        $wreckFieldData = [];
        $wreckFieldPercentage = (100.0 - $this->settings->debrisFieldFromShips()) / 100;

        // Only ships (not defenses) can go into wreck fields
        foreach ($attackerUnitsLost->units as $unit) {
            if ($unit->amount > 0 && $unit->unitObject->type === GameObjectType::Ship) {
                $wreckFieldCount = (int) floor($unit->amount * $wreckFieldPercentage);
                if ($wreckFieldCount > 0) {
                    $wreckFieldData[] = [
                        'machine_name' => $unit->unitObject->machine_name,
                        'quantity' => $wreckFieldCount,
                        'repair_progress' => 0,
                    ];
                }
            }
        }

        // Check if wreck field conditions are met
        $totalLostValue = $attackerUnitsLost->toResources()->metal->get() +
                         $attackerUnitsLost->toResources()->crystal->get() +
                         $attackerUnitsLost->toResources()->deuterium->get();
        $totalFleetValue = $attackerUnitsStart->toResources()->metal->get() +
                          $attackerUnitsStart->toResources()->crystal->get() +
                          $attackerUnitsStart->toResources()->deuterium->get();

        if ($totalFleetValue > 0) {
            $destroyedPercentage = ($totalLostValue / $totalFleetValue) * 100;
            $minResourcesRequired = $this->settings->wreckFieldMinResourcesLoss();
            $minFleetPercentageRequired = $this->settings->wreckFieldMinFleetPercentage();

            // Only return wreck field data if conditions are met and there are ships
            if ($totalLostValue >= $minResourcesRequired
                && $destroyedPercentage >= $minFleetPercentageRequired
                && !empty($wreckFieldData)) {
                return $wreckFieldData;
            }
        }

        return null;
    }

    /**
     * Creates a battle report for the given battle result.
     *
     * @param PlayerService $attackPlayer The player who initiated the attack.
     * @param PlanetService $defenderPlanet The planet that was attacked.
     * @param BattleResult $battleResult The result of the battle.
     * @param Resources $collectedDebris Total debris collected automatically by Reaper ships (attacker + defender).
     * @param Resources $attackerCollectedDebris Debris collected by attacker's Reaper ships.
     * @param Resources $defenderCollectedDebris Debris collected by defender's Reaper ships.
     * @return int
     */
    private function createBattleReport(PlayerService $attackPlayer, PlanetService $defenderPlanet, BattleResult $battleResult, Resources $collectedDebris, Resources $attackerCollectedDebris, Resources $defenderCollectedDebris): int
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
            'hamill_manoeuvre_triggered' => $battleResult->hamillManoeuvreTriggered,
        ];

        $characterClassService = app(CharacterClassService::class);
        $attackerCharacterClass = $characterClassService->getCharacterClass($attackPlayer->getUser());
        $defenderCharacterClass = $characterClassService->getCharacterClass($defenderPlanet->getPlayer()->getUser());

        $report->attacker = [
            'player_id' => $attackPlayer->getId(),
            'resource_loss' => $battleResult->attackerResourceLoss->sum(),
            'units' => $battleResult->attackerUnitsStart->toArray(),
            'weapon_technology' => $battleResult->attackerWeaponLevel,
            'shielding_technology' => $battleResult->attackerShieldLevel,
            'armor_technology' => $battleResult->attackerArmorLevel,
            'planet_id' => $battleResult->attackerPlanetId,
            'character_class' => $attackerCharacterClass?->getName(),
        ];

        // TODO: Enhance battle reports to show individual participating fleets/defenders
        // Currently shows aggregated defender data (combined units, planet owner's tech, single player_id)
        // Should show:
        // - Combined fleet totals (current behavior)
        // - Dropdown/expandable sections for each participating fleet:
        //   - Planet owner's stationary forces (ships + defenses with their tech levels)
        //   - Each ACS Defend fleet (units, owner, tech levels)
        // - Per-fleet losses and survivors
        // Data available in: $battleResult->defenderFleetResults
        $report->defender = [
            'player_id' => $defenderPlanet->getPlayer()->getId(),
            'resource_loss' => $battleResult->defenderResourceLoss->sum(),
            'units' => $battleResult->defenderUnitsStart->toArray(),
            'weapon_technology' => $battleResult->defenderWeaponLevel,
            'shielding_technology' => $battleResult->defenderShieldLevel,
            'armor_technology' => $battleResult->defenderArmorLevel,
            'character_class' => $defenderCharacterClass?->getName(),
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
            'collected_metal' => $collectedDebris->metal->get(),
            'collected_crystal' => $collectedDebris->crystal->get(),
            'collected_deuterium' => $collectedDebris->deuterium->get(),
            'attacker_collected_metal' => $attackerCollectedDebris->metal->get(),
            'attacker_collected_crystal' => $attackerCollectedDebris->crystal->get(),
            'attacker_collected_deuterium' => $attackerCollectedDebris->deuterium->get(),
            'defender_collected_metal' => $defenderCollectedDebris->metal->get(),
            'defender_collected_crystal' => $defenderCollectedDebris->crystal->get(),
            'defender_collected_deuterium' => $defenderCollectedDebris->deuterium->get(),
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

    /**
     * Update military statistics for both attacker and defender after battle.
     * Tracks destroyed and lost military units for highscore purposes.
     *
     * @param PlayerService $attackerPlayer The attacking player
     * @param PlayerService $defenderPlayer The defending player
     * @param BattleResult $battleResult The battle result containing unit losses
     * @return void
     */
    private function updateMilitaryStatistics(PlayerService $attackerPlayer, PlayerService $defenderPlayer, BattleResult $battleResult): void
    {
        // Calculate military points from units (both civil and military ships count)
        // Military ships count 100%, civil ships count 50%
        $attackerLostPoints = $this->calculateMilitaryPoints($battleResult->attackerUnitsLost, $attackerPlayer);
        $defenderLostPoints = $this->calculateMilitaryPoints($battleResult->defenderUnitsLost, $defenderPlayer);

        // Update attacker statistics
        // Attacker destroyed enemy units (defender's losses)
        // Attacker lost their own units
        $attackerUser = $attackerPlayer->getUser();
        $attackerUser->military_units_destroyed_points += $defenderLostPoints;
        $attackerUser->military_units_lost_points += $attackerLostPoints;
        $attackerUser->save();

        // Update defender statistics
        // Defender destroyed enemy units (attacker's losses)
        // Defender lost their own units
        $defenderUser = $defenderPlayer->getUser();
        $defenderUser->military_units_destroyed_points += $attackerLostPoints;
        $defenderUser->military_units_lost_points += $defenderLostPoints;
        $defenderUser->save();
    }

    /**
     * Calculate military points from lost units.
     * Military ships count 100%, civil ships count 50%, defenses count 100%.
     *
     * @param UnitCollection $unitsLost The units that were lost
     * @param PlayerService $player The player who lost the units (for tech bonus calculation)
     * @return int The military points value
     */
    private function calculateMilitaryPoints(UnitCollection $unitsLost, PlayerService $player): int
    {
        $points = 0;

        foreach ($unitsLost->units as $unit) {
            if ($unit->amount > 0) {
                $unitValue = $unit->unitObject->price->resources->sum();

                // Check unit type and apply appropriate multiplier
                if ($unit->unitObject->type === GameObjectType::Ship) {
                    // Check if it's a military or civil ship
                    $militaryShips = ObjectService::getMilitaryShipObjects();
                    $isMilitaryShip = false;
                    foreach ($militaryShips as $militaryShip) {
                        if ($militaryShip->machine_name === $unit->unitObject->machine_name) {
                            $isMilitaryShip = true;
                            break;
                        }
                    }

                    if ($isMilitaryShip) {
                        // Military ships: 100%
                        $points += ($unitValue * $unit->amount);
                    } else {
                        // Civil ships: 50%
                        $points += ($unitValue * $unit->amount * 0.5);
                    }
                } elseif ($unit->unitObject->type === GameObjectType::Defense) {
                    // Defense units: 100%
                    $points += ($unitValue * $unit->amount);
                }
            }
        }

        // Convert to points (divide by 1000, same as regular highscore calculation)
        return (int)floor($points / 1000);
    }
}
