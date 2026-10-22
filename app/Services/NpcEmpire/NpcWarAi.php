<?php

namespace OGame\Services\NpcEmpire;

use Exception;
use Illuminate\Support\Facades\Date;
use OGame\GameConstants\UniverseConstants;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\EspionageReport;
use OGame\Models\NpcEmpire;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\Resources;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

/**
 * Class NpcWarAi.
 *
 * Active competition for NPC empires: discovers nearby player planets,
 * gathers intelligence with espionage probes and raids targets whose
 * resources outweigh their defenses. Behavior scales with the empire's
 * randomized aggression trait. Uses the real fleet mission pipeline, so
 * all OGame rules (counter-espionage, vacation mode, server attack
 * blocks, admin protection) apply identically.
 *
 * @package OGame\Services\NpcEmpire
 */
class NpcWarAi
{
    /**
     * Ships the AI is willing to send on raids. Civilian support ships
     * (probes, colony, recycler, solar sat) stay home.
     *
     * @var array<string, float>
     */
    private const MILITARY_WEIGHT = [
        'light_fighter'  => 1.0,
        'heavy_fighter'  => 2.0,
        'cruiser'        => 4.0,
        'battle_ship'    => 6.0,
        'battlecruiser'  => 5.0,
        'bomber'         => 4.0,
        'destroyer'      => 8.0,
        'deathstar'      => 50.0,
    ];

    /**
     * Defense unit rough strength weights.
     *
     * @var array<string, float>
     */
    private const DEFENSE_WEIGHT = [
        'rocket_launcher'         => 1.0,
        'light_laser'             => 1.5,
        'heavy_laser'             => 2.0,
        'gauss_cannon'            => 5.0,
        'ion_cannon'              => 3.0,
        'plasma_turret'           => 15.0,
        'small_shield_dome'       => 2.0,
        'large_shield_dome'       => 6.0,
        'anti_ballistic_missile'  => 1.0,
        'interplanetary_missile'  => 2.0,
    ];

    /**
     * Amount of probes sent per espionage run.
     */
    private const PROBE_COUNT = 5;

    /**
     * Probes dispatched less than this many seconds ago are considered
     * still in flight; the AI waits for their report instead of re-probing.
     */
    private const PROBE_IN_FLIGHT_WINDOW = 600;

    /**
     * Intelligence older than this is considered stale and re-probed.
     */
    private const INTEL_FRESH_HOURS = 8;

    /**
     * Minimum estimated loot (in resource units) for an attack to be worth
     * the fuel and risk.
     */
    private const MIN_LOOT = 5000;

    /**
     * Share of the target's resources the attacker can realistically loot.
     */
    private const LOOT_SHARE = 0.5;

    /**
     * Share of the expected loot the attack fleet reserves cargo space for.
     */
    private const CARGO_RESERVE_FACTOR = 0.75;

    /**
     * After a rejection, a target is not re-evaluated for this long (seconds).
     */
    private const REJECTED_RECHECK = 43200;

    /**
     * After attacking a target, leave it alone for this long (seconds).
     */
    private const ATTACKED_COOLDOWN = 172800;

    /**
     * Max war memory entries kept per empire.
     */
    private const MAX_TARGETS = 20;

    /**
     * Max candidate targets evaluated per decision cycle.
     */
    private const CANDIDATE_LIMIT = 8;

    public function __construct(
        private FleetMissionService $fleetMissionService,
        private SettingsService $settingsService,
    ) {
    }

    /**
     * Run one war decision cycle: probe a new target, evaluate fresh
     * intelligence or dispatch a raid. At most one action per cycle.
     *
     * @param PlayerService $player
     * @param NpcEmpire $empire
     */
    public function decide(PlayerService $player, NpcEmpire $empire): void
    {
        if (!$this->settingsService->npcAttackPlayersEnabled()) {
            return;
        }

        $state = $empire->ai_state ?? [];
        $war = $state['war'] ?? [];
        $this->pruneWarState($war);

        $staging = $this->findStagingPlanet($player);
        if ($staging === null) {
            return;
        }

        foreach ($this->discoverCandidates($player, $staging, $empire) as $candidate) {
            $key = $candidate->galaxy . ':' . $candidate->system . ':' . $candidate->planet;
            $memory = $war[$key] ?? [];

            $now = (int)Date::now()->timestamp;

            // Recently attacked or rejected: leave this target alone.
            if ($now < (int)($memory['recheck_at'] ?? 0)) {
                continue;
            }

            $report = $this->getFreshIntel($candidate, $memory);
            if ($report !== null) {
                if ($this->tryAttack($player, $staging, $empire, $candidate, $report, $memory)) {
                    $war[$key] = $memory;
                    break;
                }
                // Not worth it: back off for a while, try the next candidate.
                $memory['recheck_at'] = $now + self::REJECTED_RECHECK;
                $war[$key] = $memory;
                continue;
            }

            // No usable intel: probe (unless a probe flight is already out).
            if ($now - (int)($memory['probed_at'] ?? 0) < self::PROBE_IN_FLIGHT_WINDOW) {
                continue;
            }

            $memory['probed_at'] = $now;
            $war[$key] = $memory;

            if ($this->sendProbes($staging, $candidate)) {
                break;
            }

            // No probes available: remove memory so we retry next cycle.
            unset($war[$key]);
        }

        $state['war'] = $war;
        $empire->ai_state = $state;
    }

    /**
     * Find the planet to stage raids from: the one with the most military
     * presence, falling back to the first planet.
     *
     * @param PlayerService $player
     * @return PlanetService|null
     */
    private function findStagingPlanet(PlayerService $player): PlanetService|null
    {
        $best = null;
        $bestFighters = -1;
        foreach ($player->planets->allPlanets() as $planet) {
            $fighters = $planet->getObjectAmount('light_fighter');
            if ($fighters > $bestFighters) {
                $bestFighters = $fighters;
                $best = $planet;
            }
        }

        return $best;
    }

    /**
     * Discover player-owned planets near the staging planet. Higher
     * aggression widens the search radius. NPC planets and vacation mode
     * players are excluded.
     *
     * @param PlayerService $player
     * @param PlanetService $staging
     * @param NpcEmpire $empire
     * @return array<int, Planet>
     */
    private function discoverCandidates(PlayerService $player, PlanetService $staging, NpcEmpire $empire): array
    {
        $origin = $staging->getPlanetCoordinates();
        $radius = 15 + (int)($empire->aggression / 5);
        $minSystem = max(UniverseConstants::MIN_SYSTEM, $origin->system - $radius);
        $maxSystem = min(UniverseConstants::MAX_SYSTEM_COUNT, $origin->system + $radius);

        return Planet::query()
            ->join('users', 'planets.user_id', '=', 'users.id')
            ->where('users.is_npc', false)
            ->where('users.vacation_mode', 0)
            ->where('planets.user_id', '!=', $player->getId())
            ->where('planets.galaxy', $origin->galaxy)
            ->whereBetween('planets.system', [$minSystem, $maxSystem])
            ->where('planets.planet_type', 1)
            ->where('planets.destroyed', 0)
            ->inRandomOrder()
            ->limit(self::CANDIDATE_LIMIT)
            ->select(['planets.*'])
            ->get()
            ->all();
    }

    /**
     * Get freshest usable intelligence for a target coordinate, or null
     * when no report exists within the freshness window.
     *
     * @param Planet $candidate
     * @param array<string, int> $memory
     * @return EspionageReport|null
     */
    private function getFreshIntel(Planet $candidate, array $memory): EspionageReport|null
    {
        $now = (int)Date::now()->timestamp;

        // Reports are trusted when they were created after the AI's own
        // probe dispatch (its own probe result), or at least recently
        // enough to reflect reality.
        $query = EspionageReport::query()
            ->where('planet_galaxy', $candidate->galaxy)
            ->where('planet_system', $candidate->system)
            ->where('planet_position', $candidate->planet)
            ->where('planet_type', 1)->latest();

        $report = $query->first();
        if ($report === null || $report->created_at === null) {
            return null;
        }

        $reportTs = (int)$report->created_at->timestamp;
        $probedAt = (int)($memory['probed_at'] ?? 0);

        // Own probe result: always usable. Otherwise only if recent.
        if ($reportTs >= $probedAt && $probedAt > 0) {
            return $report;
        }

        if ($now - $reportTs <= self::INTEL_FRESH_HOURS * 3600) {
            return $report;
        }

        return null;
    }

    /**
     * Try to dispatch a raid against the target based on the given intel.
     * Mutates the per-target memory on success.
     *
     * @param PlayerService $player
     * @param PlanetService $staging
     * @param NpcEmpire $empire
     * @param Planet $candidate
     * @param EspionageReport $report
     * @param array<string, int> $memory
     * @return bool True when an attack was dispatched.
     */
    private function tryAttack(
        PlayerService $player,
        PlanetService $staging,
        NpcEmpire $empire,
        Planet $candidate,
        EspionageReport $report,
        array &$memory
    ): bool {
        $resources = $report->resources ?? [];
        $lootTarget = (int)(max(0, ($resources['metal'] ?? 0) + ($resources['crystal'] ?? 0) + ($resources['deuterium'] ?? 0)) * self::LOOT_SHARE);

        if ($lootTarget < self::MIN_LOOT) {
            return false;
        }

        $fleet = $this->composeAttackFleet($staging, $player, $lootTarget);
        if ($fleet === null) {
            return false;
        }

        // Strength heuristic: only attack when the raid pays off. Higher
        // aggression lowers the required superiority over the defender.
        $attackStrength = 0.0;
        foreach ($fleet->units as $entry) {
            $attackStrength += (self::MILITARY_WEIGHT[$entry->unitObject->machine_name] ?? 0.5) * $entry->amount;
        }

        $defenseStrength = 0.0;
        foreach ($report->defense ?? [] as $machineName => $amount) {
            $defenseStrength += (self::DEFENSE_WEIGHT[$machineName] ?? 1.0) * $amount;
        }
        foreach ($report->ships ?? [] as $machineName => $amount) {
            $defenseStrength += (self::MILITARY_WEIGHT[$machineName] ?? 0.5) * $amount;
        }

        $requiredRatio = max(1.0, 3 - $empire->aggression / 40);
        if ($defenseStrength > 0 && $attackStrength < $defenseStrength * $requiredRatio) {
            return false;
        }

        $coordinate = new Coordinate($candidate->galaxy, $candidate->system, $candidate->planet);

        try {
            $this->fleetMissionService->createNewFromPlanet(
                $staging,
                $coordinate,
                PlanetType::Planet,
                1,
                $fleet,
                new Resources(),
                10
            );
        } catch (Exception $e) {
            // Server attack block, target switched to vacation mode, not
            // enough fuel etc. Back off for a while.
            logger()->debug('NPC raid dispatch failed', [
                'npc_user_id' => $player->getId(),
                'target'      => $coordinate->galaxy . ':' . $coordinate->system . ':' . $coordinate->position,
                'error'       => $e->getMessage(),
            ]);
            $memory['recheck_at'] = (int)Date::now()->timestamp + self::REJECTED_RECHECK;

            return false;
        }

        $memory['attacked_at'] = (int)Date::now()->timestamp;
        $memory['recheck_at'] = $memory['attacked_at'] + self::ATTACKED_COOLDOWN;

        return true;
    }

    /**
     * Compose the raid fleet: all available military ships plus enough
     * cargo capacity for the expected loot.
     *
     * @param PlanetService $staging
     * @param PlayerService $player
     * @param int $lootTarget
     * @return UnitCollection|null Null when there is no fleet worth sending.
     */
    private function composeAttackFleet(PlanetService $staging, PlayerService $player, int $lootTarget): UnitCollection|null
    {
        $ships = $staging->getShipUnits();

        $fleet = new UnitCollection();
        $militaryWeightTotal = 0.0;
        foreach ($ships->units as $entry) {
            $machineName = $entry->unitObject->machine_name;
            if (isset(self::MILITARY_WEIGHT[$machineName])) {
                $fleet->addUnit($entry->unitObject, $entry->amount);
                $militaryWeightTotal += self::MILITARY_WEIGHT[$machineName] * $entry->amount;
            }
        }

        if ($militaryWeightTotal < 1) {
            return null;
        }

        // Cargo: prioritize large cargo, then small, until the loot target
        // fits (capped at what the planet has available).
        if ($lootTarget > 0) {
            $neededCapacity = (int)($lootTarget * self::CARGO_RESERVE_FACTOR);
            $smallCapacity = (int)ObjectService::getShipObjectByMachineName('small_cargo')
                ->properties->capacity->calculate($player)->totalValue;
            $largeAvailable = $staging->getObjectAmount('large_cargo');
            $smallAvailable = $staging->getObjectAmount('small_cargo');
            $largeCapacity = (int)$this->largeCargoCapacity($player);

            $largeUsed = min($largeAvailable, (int)ceil($neededCapacity / max(1, $largeCapacity)));
            $remainingCapacity = $neededCapacity - ($largeUsed * $largeCapacity);
            $smallUsed = min($smallAvailable, (int)ceil(max(0, $remainingCapacity) / max(1, $smallCapacity)));

            if ($largeUsed > 0) {
                $fleet->addUnit(ObjectService::getShipObjectByMachineName('large_cargo'), $largeUsed);
            }
            if ($smallUsed > 0) {
                $fleet->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), $smallUsed);
            }

            // Raids without any cargo haul no loot: skip.
            if ($largeUsed === 0 && $smallUsed === 0) {
                return null;
            }
        }

        return $fleet;
    }

    /**
     * Per-unit large cargo capacity (tech-independent base for planning,
     * exact capacity is validated by the mission itself).
     *
     * @param PlayerService $player
     * @return float
     */
    private function largeCargoCapacity(PlayerService $player): float
    {
        return (float)ObjectService::getShipObjectByMachineName('large_cargo')
            ->properties->capacity->calculate($player)->totalValue;
    }

    /**
     * Dispatch an espionage run against the target. Returns false when no
     * probes are available on the staging planet.
     *
     * @param PlanetService $staging
     * @param Planet $candidate
     * @return bool
     */
    private function sendProbes(PlanetService $staging, Planet $candidate): bool
    {
        $probeAmount = $staging->getObjectAmount('espionage_probe');
        if ($probeAmount < 1) {
            return false;
        }

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('espionage_probe'), min($probeAmount, self::PROBE_COUNT));

        $coordinate = new Coordinate($candidate->galaxy, $candidate->system, $candidate->planet);

        try {
            $this->fleetMissionService->createNewFromPlanet(
                $staging,
                $coordinate,
                PlanetType::Planet,
                6,
                $units,
                new Resources(),
                10
            );
        } catch (Exception $e) {
            // Vacation mode switch, attack block, insufficient fuel etc.
            // The memory entry keeps the probed timestamp so the AI waits
            // out the in-flight window before retrying.
            logger()->debug('NPC espionage dispatch failed', [
                'npc_user_id' => $staging->getPlayer()?->getId(),
                'target'      => $coordinate->galaxy . ':' . $coordinate->system . ':' . $coordinate->position,
                'error'       => $e->getMessage(),
            ]);

            return true;
        }

        return true;
    }

    /**
     * Prune stale war memory entries and cap the state size.
     *
     * @param array<string, array<string, int>> $war
     */
    private function pruneWarState(array &$war): void
    {
        $cutoff = (int)Date::now()->timestamp - 14 * 86400;

        foreach ($war as $key => $memory) {
            $lastActivity = max(
                (int)($memory['attacked_at'] ?? 0),
                (int)($memory['probed_at'] ?? 0),
                (int)($memory['recheck_at'] ?? 0)
            );
            if ($lastActivity < $cutoff) {
                unset($war[$key]);
            }
        }

        // Hard cap so the JSON state can never grow unbounded.
        if (count($war) > self::MAX_TARGETS) {
            uasort($war, static fn (array $a, array $b): int => max($b ?: [0]) <=> max($a ?: [0]));
            $war = array_slice($war, 0, self::MAX_TARGETS, true);
        }
    }
}
