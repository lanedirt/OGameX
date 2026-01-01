<?php

namespace OGame\GameMissions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OGame\Enums\FleetMissionStatus;
use OGame\GameMissions\Abstracts\GameMission;
use OGame\GameMissions\Models\MissionPossibleStatus;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet\Coordinate;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

/**
 * Interplanetary Ballistic Missile (IBM/IPM) Attack Mission
 *
 * Missiles are one-way attacks that destroy enemy defenses.
 * - Range determined by Impulse Drive: (level × 5) - 1 systems
 * - Cannot cross galaxies
 * - Can be intercepted by Anti-Ballistic Missiles (ABMs) 1:1
 * - For moon attacks, parent planet ABMs defend first
 *
 * Damage Formula:
 * - Base damage: 12,000 per missile × (1 + 0.1 × Weapon Tech)
 * - Defense armor: Structure × (1 + 0.1 × Armor Tech) / 10
 * - Defense cost: Metal + Crystal ONLY (Deuterium excluded!)
 *
 * Flight time formula: (30 + 60 × distance_in_systems) / universe_speed seconds
 */
class MissileMission extends GameMission
{
    protected static string $name = 'Missile Attack';
    protected static int $typeId = 10;
    protected static bool $hasReturnMission = false;
    protected static FleetMissionStatus $friendlyStatus = FleetMissionStatus::Hostile;

    /**
     * @inheritdoc
     */
    public function isMissionPossible(PlanetService $planet, Coordinate $targetCoordinate, PlanetType $targetType, UnitCollection $units): MissionPossibleStatus
    {
        // Missile attack is only possible for planets and moons
        if (!in_array($targetType, [PlanetType::Planet, PlanetType::Moon])) {
            return new MissionPossibleStatus(false);
        }

        // Target planet must exist
        $targetPlanet = $this->planetServiceFactory->makeForCoordinate($targetCoordinate, true, $targetType);
        if ($targetPlanet === null) {
            return new MissionPossibleStatus(false);
        }

        // Cannot attack own planets
        if ($planet->getPlayer()->equals($targetPlanet->getPlayer())) {
            return new MissionPossibleStatus(false);
        }

        // Check if target is within missile range
        $attackerPlayer = $planet->getPlayer();
        $missileRange = $attackerPlayer->getMissileRange();

        // Calculate distance in systems
        $distance = $this->calculateSystemDistance($planet->getPlanetCoordinates(), $targetCoordinate);

        if ($distance > $missileRange) {
            return new MissionPossibleStatus(false, 'Target is out of missile range');
        }

        // Must have at least one missile
        $missileCount = $planet->getObjectAmount('interplanetary_missile');
        if ($missileCount <= 0) {
            return new MissionPossibleStatus(false, 'No missiles available');
        }

        return new MissionPossibleStatus(true);
    }

    /**
     * Calculate distance in systems between two coordinates.
     *
     * @param Coordinate $from
     * @param Coordinate $to
     * @return int Distance in systems (PHP_INT_MAX if different galaxy)
     */
    private function calculateSystemDistance(Coordinate $from, Coordinate $to): int
    {
        // Same galaxy and system = 0 distance
        if ($from->galaxy === $to->galaxy && $from->system === $to->system) {
            return 0;
        }

        // Different galaxy = not allowed (missiles can't cross galaxies)
        if ($from->galaxy !== $to->galaxy) {
            return PHP_INT_MAX; // Return very large number to make it out of range
        }

        // Same galaxy, different system
        return abs($from->system - $to->system);
    }

    /**
     * @inheritdoc
     */
    protected function processArrival(FleetMission $mission): void
    {
        try {
            $defenderTarget = $this->planetServiceFactory->make($mission->planet_id_to, true);
            $attackerPlanet = $this->planetServiceFactory->make($mission->planet_id_from, true);

            if (!$defenderTarget || !$attackerPlanet) {
                Log::error('Missile mission: Invalid planet IDs', [
                    'mission_id' => $mission->id,
                    'planet_id_from' => $mission->planet_id_from,
                    'planet_id_to' => $mission->planet_id_to,
                ]);
                $mission->processed = 1;
                $mission->save();
                return;
            }

            // Trigger defender target update
            $defenderTarget->update();

            // Get players for technology levels
            $attackerPlayer = $attackerPlanet->getPlayer();
            $defenderPlayer = $defenderTarget->getPlayer();

            // Get number of missiles sent (now stored in dedicated column!)
            $missileCount = (int)$mission->interplanetary_missile;

            // Get defender's ABM count - including parent planet if target is a moon
            $targetAbmCount = $defenderTarget->getObjectAmount('anti_ballistic_missile');
            $parentPlanetAbmCount = 0;
            $parentPlanet = null;

            // If target is a moon, also count ABMs from the parent planet
            if ($defenderTarget->isMoon()) {
                $coordinates = $defenderTarget->getPlanetCoordinates();
                $parentPlanet = $this->planetServiceFactory->makeForCoordinate($coordinates, true, PlanetType::Planet);
                if ($parentPlanet !== null) {
                    $parentPlanet->update();
                    $parentPlanetAbmCount = $parentPlanet->getObjectAmount('anti_ballistic_missile');
                }
            }

            $totalAbmCount = $targetAbmCount + $parentPlanetAbmCount;

            // Calculate how many missiles get through
            $interceptedMissiles = min($missileCount, $totalAbmCount);
            $effectiveMissiles = $missileCount - $interceptedMissiles;

            Log::info('Missile Attack: ABM Interception', [
                'mission_id' => $mission->id,
                'missiles_sent' => $missileCount,
                'target_abm_count' => $targetAbmCount,
                'parent_planet_abm_count' => $parentPlanetAbmCount,
                'total_abm_count' => $totalAbmCount,
                'intercepted_missiles' => $interceptedMissiles,
                'effective_missiles' => $effectiveMissiles,
                'target_coords' => $defenderTarget->getPlanetCoordinates()->asString(),
                'target_type' => $defenderTarget->isMoon() ? 'moon' : 'planet',
            ]);

            // Get defense counts BEFORE attack for reporting
            $defensesBeforeAttack = [];
            foreach (ObjectService::getDefenseObjects() as $defense) {
                $amount = $defenderTarget->getObjectAmount($defense->machine_name);
                if ($amount > 0) {
                    $defensesBeforeAttack[$defense->machine_name] = [
                        'name' => $defense->title,
                        'before' => $amount,
                    ];
                }
            }

            // Calculate defense destruction with proper formula
            $destroyedDefenses = $this->calculateDefenseDestruction(
                $defenderTarget,
                $defenderPlayer,
                $effectiveMissiles,
                $attackerPlayer,
                $mission
            );

            // TRANSACTION SAFETY: Wrap ABM removal and defense destruction in atomic transaction
            DB::transaction(function () use (
                $interceptedMissiles,
                $parentPlanet,
                $parentPlanetAbmCount,
                $defenderTarget,
                $targetAbmCount,
                $destroyedDefenses
            ) {
                // Remove intercepted ABMs - prioritize parent planet's ABMs first
                if ($interceptedMissiles > 0) {
                    // Use parent planet ABMs first
                    if ($parentPlanet !== null && $parentPlanetAbmCount > 0) {
                        $abmsUsedFromParent = min($interceptedMissiles, $parentPlanetAbmCount);
                        $parentPlanet->removeUnit('anti_ballistic_missile', $abmsUsedFromParent);
                        $parentPlanet->save();

                        $remainingInterceptions = $interceptedMissiles - $abmsUsedFromParent;

                        // Use moon ABMs if needed
                        if ($remainingInterceptions > 0 && $targetAbmCount > 0) {
                            $defenderTarget->removeUnit('anti_ballistic_missile', $remainingInterceptions);
                        }
                    } else {
                        // Target is a planet or moon with no parent, just use target's ABMs
                        $defenderTarget->removeUnit('anti_ballistic_missile', $interceptedMissiles);
                    }
                }

                // Remove destroyed defenses
                if ($destroyedDefenses->getAmount() > 0) {
                    $defenderTarget->removeUnits($destroyedDefenses, false);
                }

                $defenderTarget->save();
            });

            // Get defense counts AFTER attack for reporting
            foreach ($defensesBeforeAttack as $machineName => &$defenseData) {
                $amountAfter = $defenderTarget->getObjectAmount($machineName);
                $defenseData['after'] = $amountAfter;
                $defenseData['destroyed'] = $defenseData['before'] - $amountAfter;
            }

            Log::info('Missile Attack: Defenses Destroyed', [
                'mission_id' => $mission->id,
                'total_destroyed' => $destroyedDefenses->getAmount(),
                'destroyed_list' => $this->formatDestroyedDefenses($destroyedDefenses),
            ]);

            // Send battle reports to both players
            $this->sendMissileAttackMessages(
                $attackerPlanet,
                $attackerPlayer,
                $defenderTarget,
                $defenderPlayer,
                $missileCount,
                $interceptedMissiles,
                $effectiveMissiles,
                $destroyedDefenses,
                $defensesBeforeAttack,
                $parentPlanet,
                $parentPlanetAbmCount
            );

            // Mark mission as processed
            $mission->processed = 1;
            $mission->save();
        } catch (\Exception $e) {
            Log::error('Missile mission processing failed', [
                'mission_id' => $mission->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Mark as processed even on error to prevent retry loops
            $mission->processed = 1;
            $mission->save();
        }
    }

    /**
     * Calculate which defenses are destroyed by missiles using proper OGame formula.
     *
     * CRITICAL: Defense cost = Metal + Crystal ONLY (Deuterium excluded!)
     *
     * OGame Formula:
     * - Missile Damage = Number of IPMs × 12,000 × (1 + 0.1 × Attacker's Weapon Technology)
     * - Defense Armor = Defense Structure × (1 + 0.1 × Defender's Armor Technology) / 10
     * - Defenses Destroyed = floor(Missile Damage / Defense Armor)
     *
     * @param PlanetService $defenderPlanet
     * @param \OGame\Services\PlayerService $defenderPlayer
     * @param int $missileCount
     * @param \OGame\Services\PlayerService $attackerPlayer
     * @param FleetMission $mission
     * @return UnitCollection
     */
    private function calculateDefenseDestruction(
        PlanetService $defenderPlanet,
        $defenderPlayer,
        int $missileCount,
        $attackerPlayer,
        FleetMission $mission
    ): UnitCollection {
        $destroyedDefenses = new UnitCollection();

        if ($missileCount <= 0) {
            return $destroyedDefenses;
        }

        // Get technology levels
        $weaponTech = $attackerPlayer->getResearchLevel('weapon_technology');
        $armorTech = $defenderPlayer->getResearchLevel('armor_technology');

        // Calculate total missile damage with weapon technology bonus
        // Formula: Number of IPMs × 12,000 × (1 + 0.1 × Weapon Tech)
        $totalDestructionPower = $missileCount * 12000 * (1 + 0.1 * $weaponTech);

        Log::info('Missile Attack: Destruction Calculation', [
            'missiles' => $missileCount,
            'weapon_tech' => $weaponTech,
            'armor_tech' => $armorTech,
            'total_destruction_power' => $totalDestructionPower,
        ]);

        // Get target priority from mission (now stored in dedicated column!)
        $priorityCode = (int)($mission->target_priority ?? 0);
        $targetPriority = $this->decodePriority($priorityCode);

        // Get all defense objects and sort by priority
        $defenseObjects = ObjectService::getDefenseObjects();
        $this->sortDefensesByPriority($defenseObjects, $targetPriority);

        // Destroy defenses based on target priority
        foreach ($defenseObjects as $defense) {
            if ($totalDestructionPower <= 0) {
                break;
            }

            // Don't target missiles (shield domes can be targeted and destroyed by IPMs)
            if (in_array($defense->machine_name, [
                'interplanetary_missile',
                'anti_ballistic_missile',
            ])) {
                continue;
            }

            $defenseCount = $defenderPlanet->getObjectAmount($defense->machine_name);
            if ($defenseCount <= 0) {
                continue;
            }

            // Calculate defense armor with armor technology bonus
            // Formula: Defense Structure × (1 + 0.1 × Armor Tech) / 10
            $defenseStructure = $defense->properties->structural_integrity->rawValue;
            $defenseArmor = $defenseStructure * (1 + 0.1 * $armorTech) / 10;

            // Calculate how many of this defense can be destroyed
            $maxDestroyable = (int)floor($totalDestructionPower / $defenseArmor);
            $actualDestroyed = min($maxDestroyable, $defenseCount);

            if ($actualDestroyed > 0) {
                $destroyedDefenses->addUnit($defense, $actualDestroyed);
                $totalDestructionPower -= $actualDestroyed * $defenseArmor;
            }
        }

        return $destroyedDefenses;
    }

    /**
     * Send battle reports to both attacker and defender.
     */
    private function sendMissileAttackMessages(
        PlanetService $attackerPlanet,
        \OGame\Services\PlayerService $attackerPlayer,
        PlanetService $defenderPlanet,
        \OGame\Services\PlayerService $defenderPlayer,
        int $missileCount,
        int $interceptedMissiles,
        int $effectiveMissiles,
        UnitCollection $destroyedDefenses,
        array $defensesData,
        PlanetService|null $parentPlanet = null,
        int $parentPlanetAbmCount = 0
    ): void {
        // Format destroyed defenses list
        $defensesDestroyedText = $this->formatDestroyedDefenses($destroyedDefenses);

        // Get coordinates as strings
        $targetCoords = $defenderPlanet->getPlanetCoordinates()->asString();
        $targetType = $defenderPlanet->isMoon() ? 'moon' : 'planet';
        $attackerName = $attackerPlayer->getUsername();

        // Add note about parent planet ABMs if applicable
        $abmNote = '';
        if ($parentPlanet !== null && $parentPlanetAbmCount > 0) {
            $abmNote = " (including {$parentPlanetAbmCount} ABMs from parent planet)";
        }

        // Prepare message params
        $messageParams = [
            'origin_planet_id' => $attackerPlanet->getPlanetId(),
            'origin_planet_name' => $attackerPlanet->getPlanetName(),
            'origin_planet_coords' => $attackerPlanet->getPlanetCoordinates()->asString(),
            'target_planet_id' => $defenderPlanet->getPlanetId(),
            'target_planet_name' => $defenderPlanet->getPlanetName(),
            'target_coords' => $targetCoords,
            'target_type' => $targetType,
            'missiles_sent' => $missileCount,
            'missiles_intercepted' => $interceptedMissiles . $abmNote,
            'missiles_hit' => $effectiveMissiles,
            'defenses_destroyed' => $defensesDestroyedText,
            'defenses_data' => json_encode($defensesData), // JSON encode array for storage
        ];

        Log::info('Missile Attack: Sending attacker message', [
            'params' => $messageParams,
        ]);

        // Send message to attacker
        $messageService = resolve(\OGame\Services\MessageService::class, ['player' => $attackerPlayer]);
        $messageService->sendSystemMessageToPlayer(
            $attackerPlayer,
            \OGame\GameMessages\MissileAttackReport::class,
            $messageParams
        );

        // Send message to defender
        $messageService = resolve(\OGame\Services\MessageService::class, ['player' => $defenderPlayer]);
        $messageService->sendSystemMessageToPlayer(
            $defenderPlayer,
            \OGame\GameMessages\MissileDefenseReport::class,
            [
                'attacker_name' => $attackerName,
                'planet_id' => $defenderPlanet->getPlanetId(),
                'planet_name' => $defenderPlanet->getPlanetName(),
                'planet_coords' => $targetCoords,
                'missiles_incoming' => $missileCount,
                'missiles_intercepted' => $interceptedMissiles,
                'missiles_hit' => $effectiveMissiles,
                'defenses_destroyed' => $defensesDestroyedText,
                'defenses_data' => json_encode($defensesData), // JSON encode array for storage
            ]
        );
    }

    /**
     * Format destroyed defenses for display in messages.
     */
    private function formatDestroyedDefenses(UnitCollection $destroyedDefenses): string
    {
        if ($destroyedDefenses->getAmount() === 0) {
            return 'None';
        }

        $defensesList = [];
        foreach ($destroyedDefenses->units as $unit) {
            $defensesList[] = $unit->unitObject->title . ': ' . $unit->amount;
        }
        return implode(', ', $defensesList);
    }

    /**
     * Decode numeric priority code to string.
     */
    private function decodePriority(int $code): string
    {
        $priorityMap = [
            0 => 'cheapest',
            1 => 'expensive',
            2 => 'rocket_launcher',
            3 => 'light_laser',
            4 => 'heavy_laser',
            5 => 'gauss_cannon',
            6 => 'ion_cannon',
            7 => 'plasma_turret',
            8 => 'small_shield_dome',
            9 => 'large_shield_dome',
        ];

        return $priorityMap[$code] ?? 'cheapest';
    }

    /**
     * Sort defenses array by the given priority strategy.
     *
     * CRITICAL: Defense cost = Metal + Crystal ONLY (Deuterium excluded!)
     */
    private function sortDefensesByPriority(array &$defenseObjects, string $priority): void
    {
        if ($priority === 'cheapest') {
            // Sort by price ascending (cheapest first)
            // IMPORTANT: Metal + Crystal only, NO Deuterium!
            usort($defenseObjects, function ($a, $b) {
                $priceA = $a->price->resources->metal->get() + $a->price->resources->crystal->get();
                $priceB = $b->price->resources->metal->get() + $b->price->resources->crystal->get();
                return $priceA <=> $priceB;
            });
        } elseif ($priority === 'expensive') {
            // Sort by price descending (most expensive first)
            // IMPORTANT: Metal + Crystal only, NO Deuterium!
            usort($defenseObjects, function ($a, $b) {
                $priceA = $a->price->resources->metal->get() + $a->price->resources->crystal->get();
                $priceB = $b->price->resources->metal->get() + $b->price->resources->crystal->get();
                return $priceB <=> $priceA;
            });
        } else {
            // Specific defense type priority: put that type first, then sort rest by price ascending
            // IMPORTANT: Metal + Crystal only, NO Deuterium!
            usort($defenseObjects, function ($a, $b) use ($priority) {
                $aIsPriority = $a->machine_name === $priority;
                $bIsPriority = $b->machine_name === $priority;

                // Priority items come first
                if ($aIsPriority && !$bIsPriority) {
                    return -1;
                }
                if (!$aIsPriority && $bIsPriority) {
                    return 1;
                }

                // Both are priority or both are not, sort by price (Metal + Crystal only!)
                $priceA = $a->price->resources->metal->get() + $a->price->resources->crystal->get();
                $priceB = $b->price->resources->metal->get() + $b->price->resources->crystal->get();
                return $priceA <=> $priceB;
            });
        }
    }

    /**
     * @inheritdoc
     */
    protected function processReturn(FleetMission $mission): void
    {
        // Missile missions don't have a return trip
        $mission->processed = 1;
        $mission->save();
    }
}
