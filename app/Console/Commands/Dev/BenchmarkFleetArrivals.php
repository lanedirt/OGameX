<?php

namespace OGame\Console\Commands\Dev;

use Exception;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Enums\PlanetType;
use OGame\Models\FleetMission;
use OGame\Models\Planet;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;
use RuntimeException;

/**
 * Local benchmark that measures head-of-line blocking in the fleet-arrival queue.
 *
 * It seeds a set of ATTACK destinations (each triggering an inline battle = slow to process)
 * and a set of TRANSPORT destinations (no battle = fast to process), then forces every
 * dispatched mission to become due at the same instant. The real queue-worker container(s)
 * process the resulting delayed ProcessFleetArrival jobs; this command only OBSERVES the
 * database and reports how long each mission waited between its scheduled arrival and the
 * moment it was actually processed.
 *
 * With a single worker, a slow battle at planet A blocks the (unrelated) transport at planet B
 * until the battle finishes. Scaling the worker container to N replicas lets B be processed
 * concurrently. Comparing the transport latency between the two configurations demonstrates
 * whether head-of-line blocking is real and whether scaling workers fixes it.
 */
#[Description('Benchmark fleet-arrival queue head-of-line blocking (dev only). Observes the real worker; does not process missions itself.')]
#[Signature('ogamex:dev:benchmark-fleet-arrivals
                            {--heavy=4 : Number of heavy (battle) attack destinations}
                            {--light=4 : Number of light (transport) destinations}
                            {--lead-ms=3000 : Milliseconds in the future at which all missions become due}
                            {--cleanup : Delete all benchmark data and exit}')]
class BenchmarkFleetArrivals extends Command
{
    /**
     * Email domain that namespaces every benchmark user (used for safe cleanup).
     */
    private const EMAIL_DOMAIN = 'benchmark.local';

    /**
     * Username prefix for every benchmark user.
     */
    private const USERNAME_PREFIX = 'bench_';

    /**
     * Number of light fighters in each dispatched attack fleet.
     * Balanced against the defender fleet so the battle runs multiple rounds (~0.5-2s).
     */
    private const ATTACKER_FLEET_SIZE = 1000000;

    /**
     * Number of light fighters garrisoned on each heavy (attack) destination planet.
     */
    private const DEFENDER_FLEET_SIZE = 1000000;

    /**
     * Number of small cargo ships in each transport fleet (kept small; transport is cheap).
     */
    private const TRANSPORT_SHIPS = 10;

    /**
     * Safety cap on the observation loop, in milliseconds.
     */
    private const OBSERVE_TIMEOUT_MS = 120000;

    /**
     * Observation poll interval, in microseconds (50ms).
     */
    private const POLL_INTERVAL_US = 50000;

    public function handle(): int
    {
        if ($this->option('cleanup')) {
            $deleted = $this->cleanup();
            $this->info("Cleanup complete. Removed {$deleted} benchmark user(s) and their data.");

            return self::SUCCESS;
        }

        $heavy = max(0, (int) $this->option('heavy'));
        $light = max(0, (int) $this->option('light'));
        $leadMs = max(500, (int) $this->option('lead-ms'));

        if ($heavy + $light === 0) {
            $this->error('Nothing to benchmark: both --heavy and --light are zero.');

            return self::FAILURE;
        }

        // Always start from a clean slate so coordinates and fleet slots never collide.
        $this->info('Cleaning up any previous benchmark data...');
        $this->cleanup();

        $this->info("Seeding benchmark: {$heavy} heavy (battle) destination(s), {$light} light (transport) destination(s)...");

        $attackerPlanet = $this->createAttackerPlanet($heavy, $light);
        $heavyPlanets = $this->createDefenderPlanets($heavy);
        $lightPlanets = $this->createTransportTargetPlanets($light);

        $fleetMissionService = resolve(FleetMissionService::class);

        // One shared "due" instant so every mission's arrival job becomes runnable at once.
        $dueMs = (int) Date::now()->valueOf() + $leadMs;
        $dueSeconds = intdiv($dueMs, 1000);

        /** @var array<int, array{type: string, planet_id: int}> $dispatched */
        $dispatched = [];

        foreach ($heavyPlanets as $target) {
            $mission = $this->dispatchAttack($fleetMissionService, $attackerPlanet, $target);
            $this->forceDue($fleetMissionService, $mission, $dueSeconds, $dueMs);
            $dispatched[$mission->id] = ['type' => 'heavy', 'planet_id' => $target->getPlanetId()];
        }

        foreach ($lightPlanets as $target) {
            $mission = $this->dispatchTransport($fleetMissionService, $attackerPlanet, $target);
            $this->forceDue($fleetMissionService, $mission, $dueSeconds, $dueMs);
            $dispatched[$mission->id] = ['type' => 'light', 'planet_id' => $target->getPlanetId()];
        }

        $this->info('Dispatched ' . count($dispatched) . ' mission(s), all due at ' . Date::createFromTimestamp($dueSeconds)->toDateTimeString() . " (+{$leadMs}ms). Observing...");

        $observed = $this->observe(array_keys($dispatched), $dueMs);

        $this->report($dispatched, $observed, $dueMs);

        return self::SUCCESS;
    }

    /**
     * Poll fleet_missions until every dispatched mission is processed or the safety timeout hits.
     *
     * @param array<int, int> $missionIds
     * @return array<int, int> mission_id => observed processing time in ms (wall clock)
     */
    private function observe(array $missionIds, int $dueMs): array
    {
        /** @var array<int, int> $observed */
        $observed = [];
        $total = count($missionIds);
        $startMs = (int) Date::now()->valueOf();

        while (count($observed) < $total) {
            $nowMs = (int) Date::now()->valueOf();
            if ($nowMs - $startMs > self::OBSERVE_TIMEOUT_MS) {
                $this->warn('Observation timed out before all missions were processed.');
                break;
            }

            /** @var array<int, int> $rows mission_id => processed */
            $rows = DB::table('fleet_missions')
                ->whereIn('id', $missionIds)
                ->pluck('processed', 'id')
                ->map(fn ($value): int => (int) $value)
                ->all();

            $observeMs = (int) Date::now()->valueOf();
            foreach ($rows as $id => $processed) {
                if ($processed === 1 && !isset($observed[$id])) {
                    $observed[$id] = $observeMs;
                }
            }

            if (count($observed) < $total) {
                usleep(self::POLL_INTERVAL_US);
            }
        }

        return $observed;
    }

    /**
     * Print the per-mission latency table and the summary block.
     *
     * @param array<int, array{type: string, planet_id: int}> $dispatched
     * @param array<int, int> $observed
     */
    private function report(array $dispatched, array $observed, int $dueMs): void
    {
        $this->newLine();

        /** @var array<int, int> $heavyLatencies */
        $heavyLatencies = [];
        /** @var array<int, int> $lightLatencies */
        $lightLatencies = [];
        $rows = [];
        $lastObservedMs = $dueMs;

        foreach ($dispatched as $id => $meta) {
            if (isset($observed[$id])) {
                $latency = $observed[$id] - $dueMs;
                $lastObservedMs = max($lastObservedMs, $observed[$id]);
                $latencyText = $latency . ' ms';
                if ($meta['type'] === 'heavy') {
                    $heavyLatencies[] = $latency;
                } else {
                    $lightLatencies[] = $latency;
                }
            } else {
                $latencyText = 'NOT PROCESSED';
            }

            $rows[] = [$id, $meta['type'], $meta['planet_id'], $latencyText];
        }

        $this->table(['Mission ID', 'Type', 'Dest Planet', 'Latency (observed - arrival)'], $rows);

        $this->newLine();
        $this->info('Summary');
        $summaryRows = [];
        $summaryRows[] = ['Heavy (battle)', ...$this->percentiles($heavyLatencies)];
        $summaryRows[] = ['Light (transport)', ...$this->percentiles($lightLatencies)];
        $this->table(['Group', 'count', 'p50 (ms)', 'p95 (ms)', 'max (ms)'], $summaryRows);

        $drainMs = $lastObservedMs - $dueMs;
        $this->line("Total drain time (last processed - arrival): {$drainMs} ms");

        $this->newLine();
        $allProcessed = count($observed) === count($dispatched);
        // processed is a 0/1 flag guarded by updateMission (returns early when already processed),
        // so reaching 1 for every dispatched mission proves each was processed exactly once.
        if ($allProcessed) {
            $this->info('PASS: all ' . count($dispatched) . ' missions processed exactly once (processed=1).');
        } else {
            $missing = count($dispatched) - count($observed);
            $this->error("FAIL: {$missing} mission(s) were never processed within the timeout.");
        }
    }

    /**
     * Compute [count, p50, p95, max] for a list of latencies.
     *
     * @param array<int, int> $values
     * @return array<int, int|string>
     */
    private function percentiles(array $values): array
    {
        if ($values === []) {
            return [0, '-', '-', '-'];
        }

        sort($values);
        $count = count($values);

        return [
            $count,
            $this->percentile($values, 50),
            $this->percentile($values, 95),
            $values[$count - 1],
        ];
    }

    /**
     * Nearest-rank percentile of a pre-sorted list.
     *
     * @param array<int, int> $sorted
     */
    private function percentile(array $sorted, int $p): int
    {
        $count = count($sorted);
        if ($count === 0) {
            return 0;
        }

        $rank = (int) ceil($p / 100 * $count);
        $rank = max(1, min($count, $rank));

        return $sorted[$rank - 1];
    }

    /**
     * Dispatch an attack mission (mission type 1) that will trigger an inline battle.
     */
    private function dispatchAttack(FleetMissionService $service, PlanetService $attacker, PlanetService $target): FleetMission
    {
        $fleet = new UnitCollection();
        $fleet->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), self::ATTACKER_FLEET_SIZE);

        return $service->createNewFromPlanet(
            $attacker,
            $target->getPlanetCoordinates(),
            PlanetType::Planet,
            1,
            $fleet,
            new Resources(0, 0, 0, 0),
            10,
        );
    }

    /**
     * Dispatch a transport mission (mission type 3) with a tiny cargo and no battle.
     */
    private function dispatchTransport(FleetMissionService $service, PlanetService $attacker, PlanetService $target): FleetMission
    {
        $fleet = new UnitCollection();
        $fleet->addUnit(ObjectService::getUnitObjectByMachineName('small_cargo'), self::TRANSPORT_SHIPS);

        return $service->createNewFromPlanet(
            $attacker,
            $target->getPlanetCoordinates(),
            PlanetType::Planet,
            3,
            $fleet,
            new Resources(1, 0, 0, 0),
            10,
        );
    }

    /**
     * Override a mission's arrival timestamps so it becomes due at the shared instant, then
     * re-sync its delayed queue job to the new time.
     */
    private function forceDue(FleetMissionService $service, FleetMission $mission, int $dueSeconds, int $dueMs): void
    {
        $mission->time_arrival = $dueSeconds;
        $mission->time_arrival_ms = $dueMs;
        $mission->saveQuietly();

        // saveQuietly() skips the observer, so reschedule the ProcessFleetArrival job explicitly.
        $service->syncMissionArrivalJobs($mission);
    }

    /**
     * Create the single attacker player and its planet, loaded with enough ships/resources
     * to launch every heavy and light mission from one planet.
     */
    private function createAttackerPlanet(int $heavy, int $light): PlanetService
    {
        $tech = [
            // Plenty of fleet slots for all simultaneous missions.
            'computer_technology' => 50,
            'combustion_drive' => 10,
            'impulse_drive' => 10,
            'hyperspace_drive' => 10,
            'weapon_technology' => 10,
            'shielding_technology' => 10,
            'armor_technology' => 10,
        ];

        $planet = $this->createPlayerWithPlanet('attacker', $tech);

        $model = $this->planetModel($planet);
        $model->light_fighter = max(1, $heavy) * self::ATTACKER_FLEET_SIZE;
        $model->small_cargo = max(1, $light) * self::TRANSPORT_SHIPS;
        $model->metal = 1000000000;
        $model->crystal = 1000000000;
        $model->deuterium = 10000000000;
        $model->save();

        return $this->reloadPlanet($planet);
    }

    /**
     * Create the heavy (attack) destination planets, each a separate player with a large
     * garrison so the resulting battle takes measurable engine time.
     *
     * @return array<int, PlanetService>
     */
    private function createDefenderPlanets(int $heavy): array
    {
        $tech = [
            'weapon_technology' => 10,
            'shielding_technology' => 10,
            'armor_technology' => 10,
        ];

        $planets = [];
        for ($i = 1; $i <= $heavy; $i++) {
            $planet = $this->createPlayerWithPlanet('defender_' . $i, $tech);

            $model = $this->planetModel($planet);
            $model->light_fighter = self::DEFENDER_FLEET_SIZE;
            $model->deuterium = 1000000;
            $model->save();

            $planets[] = $this->reloadPlanet($planet);
        }

        return $planets;
    }

    /**
     * Create the light (transport) destination planets. They all belong to one extra benchmark
     * player and carry no defenses (transport does not trigger a battle).
     *
     * @return array<int, PlanetService>
     */
    private function createTransportTargetPlanets(int $light): array
    {
        if ($light === 0) {
            return [];
        }

        $user = $this->createUser('targets', []);
        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);

        $planets = [];
        for ($i = 0; $i < $light; $i++) {
            $playerService = $playerServiceFactory->make($user->id, true);

            if ($playerService->planets->planetCount() === 0) {
                $planetService = $planetServiceFactory->createInitialPlanetForPlayer($playerService, 'Target');
                $user->planet_current = $planetService->getPlanetId();
                $user->save();
            } else {
                $coordinate = $planetServiceFactory->determineNewPlanetPosition();
                $planetService = $planetServiceFactory->createAdditionalPlanetForPlayer($playerService, $coordinate);
            }

            $planets[] = $planetService;
        }

        return $planets;
    }

    /**
     * Create a benchmark user with the given tech levels and one initial (Homeworld) planet.
     *
     * @param array<string, int> $tech
     */
    private function createPlayerWithPlanet(string $slug, array $tech): PlanetService
    {
        $user = $this->createUser($slug, $tech);

        $playerServiceFactory = resolve(PlayerServiceFactory::class);
        $planetServiceFactory = resolve(PlanetServiceFactory::class);

        $playerService = $playerServiceFactory->make($user->id, true);
        $planetService = $planetServiceFactory->createInitialPlanetForPlayer($playerService, 'Homeworld');

        $user->planet_current = $planetService->getPlanetId();
        $user->save();

        return $planetService;
    }

    /**
     * Create a namespaced benchmark user and its tech record.
     *
     * @param array<string, int> $tech
     */
    private function createUser(string $slug, array $tech): User
    {
        $user = new User();
        $user->username = self::USERNAME_PREFIX . $slug;
        $user->email = self::USERNAME_PREFIX . $slug . '@' . self::EMAIL_DOMAIN;
        $user->password = Hash::make('benchmark');
        $user->lang = 'en';
        $user->time = (string) Date::now()->timestamp;
        $user->save();

        $userTech = UserTech::create(['user_id' => $user->id]);
        foreach ($tech as $name => $level) {
            $userTech->{$name} = $level;
        }
        $userTech->save();

        return $user;
    }

    /**
     * Fetch the underlying Planet model for direct column writes.
     */
    private function planetModel(PlanetService $planet): Planet
    {
        $model = Planet::find($planet->getPlanetId());
        if ($model === null) {
            throw new RuntimeException('Planet model not found for id ' . $planet->getPlanetId());
        }

        return $model;
    }

    /**
     * Reload a planet service so it reflects columns written directly to the model.
     */
    private function reloadPlanet(PlanetService $planet): PlanetService
    {
        $planetServiceFactory = resolve(PlanetServiceFactory::class);
        $reloaded = $planetServiceFactory->make($planet->getPlanetId(), true);
        if ($reloaded === null) {
            throw new RuntimeException('Failed to reload planet id ' . $planet->getPlanetId());
        }

        return $reloaded;
    }

    /**
     * Delete every benchmark user together with their planets, fleet missions and queued jobs.
     *
     * @return int Number of benchmark users removed.
     */
    private function cleanup(): int
    {
        /** @var array<int, int> $userIds */
        $userIds = User::where('email', 'like', '%@' . self::EMAIL_DOMAIN)->pluck('id')->all();
        if ($userIds === []) {
            return 0;
        }

        /** @var array<int, FleetMission> $missions */
        $missions = FleetMission::whereIn('user_id', $userIds)->get(['id', 'arrival_job_id', 'hold_job_id'])->all();

        /** @var array<int, int> $jobIds */
        $jobIds = [];
        foreach ($missions as $mission) {
            if ($mission->arrival_job_id !== null) {
                $jobIds[] = $mission->arrival_job_id;
            }
            if ($mission->hold_job_id !== null) {
                $jobIds[] = $mission->hold_job_id;
            }
        }

        if ($jobIds !== []) {
            try {
                DB::table('jobs')->whereIn('id', $jobIds)->whereNull('reserved_at')->delete();
            } catch (Exception) {
                // Queue table may differ or job already gone; safe to ignore in cleanup.
            }
        }

        // Battles spawn dependent rows (return missions, battle reports, debris/wreck fields)
        // across many tables that reference these planets/missions. Rather than chase every FK
        // in dependency order, disable FK checks for this dev-only teardown of isolated
        // benchmark entities. Delete children-first anyway to avoid leaving stray rows.
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        try {
            $planetIds = Planet::whereIn('user_id', $userIds)->pluck('id')->all();

            FleetMission::whereIn('user_id', $userIds)->delete();
            if ($planetIds !== []) {
                // Also clear missions launched by other players AT these benchmark planets.
                FleetMission::whereIn('planet_id_to', $planetIds)
                    ->orWhereIn('planet_id_from', $planetIds)
                    ->delete();
            }
            Planet::whereIn('user_id', $userIds)->delete();
            UserTech::whereIn('user_id', $userIds)->delete();
            User::whereIn('id', $userIds)->delete();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return count($userIds);
    }
}
