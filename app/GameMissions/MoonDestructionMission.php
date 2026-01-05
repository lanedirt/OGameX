<?php

namespace OGame\GameMissions;

use Illuminate\Support\Facades\DB;
use OGame\Enums\FleetSpeedType;
use OGame\GameMessages\MoonDestroyed;
use OGame\GameMessages\MoonDestructionCatastrophic;
use OGame\GameMessages\MoonDestructionFailure;
use OGame\GameMessages\MoonDestructionMissionFailed;
use OGame\GameMessages\MoonDestructionSuccess;
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
use OGame\Services\DebrisFieldService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;

class MoonDestructionMission extends GameMission
{
    protected static string $name = 'Destroy';
    protected static int $typeId = 9;
    protected static bool $hasReturnMission = true;
    protected static FleetSpeedType $fleetSpeedType = FleetSpeedType::war;

    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Cannot send missions while in vacation mode
        if ($planet->getPlayer()->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'You cannot send missions while in vacation mode!');
        }

        // Moon destruction mission is only possible for moons
        if ($targetType !== PlanetType::Moon) {
            return new MissionPossibleStatus(false, __('Destroy mission can only target moons.'));
        }

        // Check if target moon exists
        $targetMoon = $this->planetServiceFactory->makeMoonForCoordinate($targetCoordinate);
        if ($targetMoon === null) {
            return new MissionPossibleStatus(false, __('No moon exists at the target coordinates.'));
        }

        // Cannot attack own moon
        if ($planet->getPlayer()->equals($targetMoon->getPlayer())) {
            return new MissionPossibleStatus(false, __('You cannot destroy your own moon.'));
        }

        // Fleet must contain at least one Deathstar
        $deathstarCount = 0;
        foreach ($units->units as $unit) {
            if ($unit->unitObject->machine_name === 'deathstar') {
                $deathstarCount = $unit->amount;
                break;
            }
        }

        if ($deathstarCount === 0) {
            return new MissionPossibleStatus(false, __('Destroy mission requires at least one Deathstar.'));
        }

        // If target player is in vacation mode, the mission is not possible.
        $targetPlayer = $targetMoon->getPlayer();
        if ($targetPlayer->isInVacationMode()) {
            return new MissionPossibleStatus(false, 'This player is in vacation mode!');
        }

        // If all checks pass, the mission is possible
        return new MissionPossibleStatus(true);
    }

    private function calculateMoonDestructionChance(int $moonDiameter, int $deathstarCount): float
    {
        $moonDiameter = max(1, $moonDiameter);
        $deathstarCount = max(0, $deathstarCount);
        $destructionChance = (100 - sqrt($moonDiameter)) * sqrt($deathstarCount);
        return max(0, min(100, $destructionChance));
    }

    private function calculateDeathstarLossChance(int $moonDiameter): float
    {
        $moonDiameter = max(1, $moonDiameter);
        $lossChance = sqrt($moonDiameter) / 2;
        return max(0, min(100, $lossChance));
    }

    protected function processArrival(FleetMission $mission): void
    {
        // Check if target moon still exists
        $targetMoon = $this->planetServiceFactory->make($mission->planet_id_to, true);
        if ($targetMoon === null || !$targetMoon->isMoon()) {
            // Moon doesn't exist - redirect to planet or cancel
            $this->handleMissingMoon($mission);
            return;
        }

        $originPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);

        // Trigger defender moon update to make sure the battle uses up-to-date info
        $targetMoon->update();

        $attackerPlayer = $originPlanet->getPlayer();
        $attackerUnits = $this->fleetMissionService->getFleetUnits($mission);

        // Collect all defending fleets (planet owner + ACS defend fleets)
        $defenders = $this->collectDefendingFleets($targetMoon);

        // Execute the battle logic using configured battle engine
        switch ($this->settings->battleEngine()) {
            case 'php':
                $battleEngine = new PhpBattleEngine($attackerUnits, $attackerPlayer, $targetMoon, $defenders, $this->settings, $mission->id, $mission->user_id);
                break;
            case 'rust':
            default:
                $battleEngine = new RustBattleEngine($attackerUnits, $attackerPlayer, $targetMoon, $defenders, $this->settings, $mission->id, $mission->user_id);
                break;
        }

        $battleResult = $battleEngine->simulateBattle();

        // Set the attacker's origin planet ID on the battle result for the battle report.
        $battleResult->attackerPlanetId = $mission->planet_id_from;

        // Deduct loot from the target moon
        $targetMoon->deductResources($battleResult->loot);

        // Process defender fleet results (moon owner + ACS defend fleets)
        foreach ($battleResult->defenderFleetResults as $fleetResult) {
            if ($fleetResult->fleetMissionId === 0) {
                // Moon owner's stationary forces - remove lost units (no defense repair on moons)
                if ($fleetResult->unitsLost->getAmount() > 0) {
                    $targetMoon->removeUnits($fleetResult->unitsLost, false);
                }
                $targetMoon->save();
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
                        $coordinates = '[coordinates]' . $targetMoon->getPlanetCoordinates()->asString() . '[/coordinates]';
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

        // Create or append debris field from combat
        $debrisFieldService = resolve(DebrisFieldService::class);
        $debrisFieldService->loadOrCreateForCoordinates($targetMoon->getPlanetCoordinates());
        $debrisFieldService->appendResources($battleResult->debris);
        $debrisFieldService->save();

        // Create battle report
        $reportId = $this->createBattleReport($attackerPlayer, $targetMoon, $battleResult);

        // Check if attacker won the battle
        if ($battleResult->attackerUnitsResult->getAmount() === 0) {
            // Attacker lost - send battle report and end mission
            $this->messageService->sendBattleReportMessageToPlayer($attackerPlayer, $reportId);
            $this->messageService->sendBattleReportMessageToPlayer($targetMoon->getPlayer(), $reportId);

            // Mark mission as processed - no return mission
            $mission->processed = 1;
            $mission->save();
            return;
        }

        // Attacker won - send battle reports
        $this->messageService->sendBattleReportMessageToPlayer($attackerPlayer, $reportId);
        $this->messageService->sendBattleReportMessageToPlayer($targetMoon->getPlayer(), $reportId);

        // Proceed to destruction attempt
        $this->executeDestructionAttempt($mission, $targetMoon, $battleResult->attackerUnitsResult);
    }

    private function handleMissingMoon(FleetMission $mission): void
    {
        $attackerPlayer = $this->playerServiceFactory->make($mission->user_id, true);
        $coordinates = $mission->galaxy_to . ':' . $mission->system_to . ':' . $mission->position_to;

        // Send failure message to attacker
        $this->messageService->sendSystemMessageToPlayer($attackerPlayer, MoonDestructionMissionFailed::class, [
            'coordinates' => '[coordinates]' . $coordinates . '[/coordinates]',
        ]);

        $mission->processed = 1;
        $mission->save();

        // Start return mission with original units and resources
        $this->startReturn(
            $mission,
            $this->fleetMissionService->getResources($mission),
            $this->fleetMissionService->getFleetUnits($mission)
        );
    }

    private function executeDestructionAttempt(FleetMission $mission, PlanetService $targetMoon, UnitCollection $survivingUnits): void
    {
        $moonDiameter = $targetMoon->getPlanetDiameter();
        $deathstarCount = 0;
        foreach ($survivingUnits->units as $unit) {
            if ($unit->unitObject->machine_name === 'deathstar') {
                $deathstarCount = $unit->amount;
                break;
            }
        }

        $destructionChance = $this->calculateMoonDestructionChance($moonDiameter, $deathstarCount);
        $lossChance = $this->calculateDeathstarLossChance($moonDiameter);

        // Roll for moon destruction (1-100 for precise percentages)
        $destructionRoll = random_int(1, 100);
        $moonDestroyed = $destructionRoll <= $destructionChance;

        // Roll for Deathstar loss - single roll for entire fleet
        $lossRoll = random_int(1, 100);
        $allDeathstarsLost = $lossRoll <= $lossChance;

        // Update surviving units if all Deathstars are lost
        if ($allDeathstarsLost) {
            foreach ($survivingUnits->units as $unit) {
                if ($unit->unitObject->machine_name === 'deathstar') {
                    $unit->amount = 0;
                    break;
                }
            }
        }

        $mission->processed = 1;
        $mission->save();

        if ($moonDestroyed) {
            $this->handleMoonDestructionSuccess($mission, $targetMoon, $survivingUnits, $destructionChance, $lossChance);
        } elseif ($allDeathstarsLost) {
            $this->handleCatastrophicFailure($mission, $targetMoon, $destructionChance, $lossChance);
        } else {
            $this->handleMoonDestructionFailure($mission, $targetMoon, $survivingUnits, $destructionChance, $lossChance);
        }
    }

    private function handleMoonDestructionSuccess(FleetMission $mission, PlanetService $targetMoon, UnitCollection $survivingUnits, float $destructionChance, float $lossChance): void
    {
        $attackerPlayer = $this->playerServiceFactory->make($mission->user_id, true);
        $defenderPlayer = $targetMoon->getPlayer();
        $moonName = $targetMoon->getPlanetName();
        $moonCoords = $targetMoon->getPlanetCoordinates()->asString();

        // Destroy the moon within a transaction
        DB::transaction(function () use ($targetMoon) {
            // Redirect all fleets targeting this moon to the parent planet
            $this->redirectFleetsFromMoon($targetMoon);

            // Delete the moon (this will cascade to buildings, units, queues)
            $targetMoon->abandonPlanet();
        });

        // Send success message to attacker
        $this->messageService->sendSystemMessageToPlayer($attackerPlayer, MoonDestructionSuccess::class, [
            'moon_name' => $moonName,
            'moon_coords' => '[coordinates]' . $moonCoords . '[/coordinates]',
            'destruction_chance' => number_format($destructionChance, 2) . '%',
            'loss_chance' => number_format($lossChance, 2) . '%',
        ]);

        // Send notification to moon owner
        $this->messageService->sendSystemMessageToPlayer($defenderPlayer, MoonDestroyed::class, [
            'moon_name' => $moonName,
            'moon_coords' => '[coordinates]' . $moonCoords . '[/coordinates]',
            'attacker_name' => $attackerPlayer->getUsername(),
        ]);

        // Create return mission with surviving units and resources
        $totalResources = new Resources($mission->metal, $mission->crystal, $mission->deuterium, 0);
        $this->startReturn($mission, $totalResources, $survivingUnits);
    }

    private function redirectFleetsFromMoon(PlanetService $moon): void
    {
        $moonCoordinates = $moon->getPlanetCoordinates();

        // Find all active missions targeting this moon
        $activeMissions = FleetMission::where('galaxy_to', $moonCoordinates->galaxy)
            ->where('system_to', $moonCoordinates->system)
            ->where('position_to', $moonCoordinates->position)
            ->where('type_to', PlanetType::Moon->value)
            ->where('processed', 0)
            ->get();

        // Redirect each mission to the planet
        foreach ($activeMissions as $activeMission) {
            $activeMission->type_to = PlanetType::Planet->value;

            // Try to find the parent planet ID
            $parentPlanet = $this->planetServiceFactory->makePlanetForCoordinate($moonCoordinates);
            if ($parentPlanet !== null) {
                $activeMission->planet_id_to = $parentPlanet->getPlanetId();
            } else {
                $activeMission->planet_id_to = null;
            }

            $activeMission->save();
        }
    }

    private function handleMoonDestructionFailure(FleetMission $mission, PlanetService $targetMoon, UnitCollection $survivingUnits, float $destructionChance, float $lossChance): void
    {
        $attackerPlayer = $this->playerServiceFactory->make($mission->user_id, true);
        $moonName = $targetMoon->getPlanetName();
        $moonCoords = $targetMoon->getPlanetCoordinates()->asString();

        // Send failure message to attacker
        $this->messageService->sendSystemMessageToPlayer($attackerPlayer, MoonDestructionFailure::class, [
            'moon_name' => $moonName,
            'moon_coords' => '[coordinates]' . $moonCoords . '[/coordinates]',
            'destruction_chance' => number_format($destructionChance, 2) . '%',
            'loss_chance' => number_format($lossChance, 2) . '%',
        ]);

        // Create return mission with surviving units and resources
        $totalResources = new Resources($mission->metal, $mission->crystal, $mission->deuterium, 0);
        $this->startReturn($mission, $totalResources, $survivingUnits);
    }

    private function handleCatastrophicFailure(FleetMission $mission, PlanetService $targetMoon, float $destructionChance, float $lossChance): void
    {
        $attackerPlayer = $this->playerServiceFactory->make($mission->user_id, true);
        $moonName = $targetMoon->getPlanetName();
        $moonCoords = $targetMoon->getPlanetCoordinates()->asString();

        $this->messageService->sendSystemMessageToPlayer($attackerPlayer, MoonDestructionCatastrophic::class, [
            'moon_name' => $moonName,
            'moon_coords' => '[coordinates]' . $moonCoords . '[/coordinates]',
            'destruction_chance' => number_format($destructionChance, 2) . '%',
            'loss_chance' => number_format($lossChance, 2) . '%',
        ]);
    }

    private function createBattleReport(PlayerService $attackPlayer, PlanetService $defenderMoon, BattleResult $battleResult): int
    {
        $report = new BattleReport();
        $report->planet_galaxy = $defenderMoon->getPlanetCoordinates()->galaxy;
        $report->planet_system = $defenderMoon->getPlanetCoordinates()->system;
        $report->planet_position = $defenderMoon->getPlanetCoordinates()->position;
        $report->planet_type = $defenderMoon->getPlanetType()->value;
        $report->planet_user_id = $defenderMoon->getPlayer()->getId();

        $report->general = [
            'moon_existed' => true,
            'moon_chance' => 0,
            'moon_created' => false,
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
            'player_id' => $defenderMoon->getPlayer()->getId(),
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

    protected function processReturn(FleetMission $mission): void
    {
        // Load the target planet
        $target_planet = $this->planetServiceFactory->make($mission->planet_id_to, true);

        // Add back the units to the source planet
        $target_planet->addUnits($this->fleetMissionService->getFleetUnits($mission));

        // Add resources to the origin planet (if any)
        $return_resources = $this->fleetMissionService->getResources($mission);
        if ($return_resources->any()) {
            $target_planet->addResources($return_resources);
        }

        // Send message to player that the return mission has arrived
        $this->sendFleetReturnMessage($mission, $target_planet->getPlayer());

        // Mark the return mission as processed
        $mission->processed = 1;
        $mission->save();
    }
}
