<?php

namespace OGame\Console\Commands\Dev;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\User;

class SeedStuckFleetMissions extends Command
{
    protected $signature = 'ogamex:dev:seed-stuck-fleet-missions
                            {--fresh : Clear all unprocessed fleet missions for test users before seeding}';

    protected $description = 'Seed stuck (overdue, unprocessed) fleet missions for admin tool testing. Requires ogamex:dev:seed-users to have been run first.';

    private const EMAIL_DOMAIN = 'ogamex.dev';

    public function handle(): int
    {
        $users = User::where('email', 'like', '%@' . self::EMAIL_DOMAIN)
            ->whereIn('username', ['test1', 'test2', 'test3'])
            ->get()
            ->keyBy('username');

        foreach (['test1', 'test2', 'test3'] as $needed) {
            if (!$users->has($needed)) {
                $this->error("Required user {$needed} not found. Run ogamex:dev:seed-users first.");
                return self::FAILURE;
            }
        }

        /** @var User $user1 */
        $user1 = $users->get('test1');
        /** @var User $user2 */
        $user2 = $users->get('test2');
        /** @var User $user3 */
        $user3 = $users->get('test3');

        $planet1 = Planet::where('user_id', $user1->id)->first();
        $planet2 = Planet::where('user_id', $user2->id)->first();
        $planet3 = Planet::where('user_id', $user3->id)->first();

        if (!$planet1 || !$planet2 || !$planet3) {
            $this->error('One or more test users have no planets. Run ogamex:dev:seed-users first.');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Clearing unprocessed fleet missions for test users...');
            DB::table('fleet_missions')
                ->whereIn('user_id', [$user1->id, $user2->id, $user3->id])
                ->where('processed', 0)
                ->where('canceled', 0)
                ->delete();
        }

        $now = (int) now()->timestamp;

        $base = [
            'processed'             => 0,
            'processed_hold'        => 0,
            'canceled'              => 0,
            'type_from'             => PlanetType::Planet->value,
            'type_to'               => PlanetType::Planet->value,
            'deuterium_consumption' => 0,
            'created_at'            => now(),
            'updated_at'            => now(),
        ];

        // 1 – healthy transport test1 → test2
        $this->seed('Transport test1 → test2', array_merge($base, [
            'user_id'        => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to'   => $planet2->id,
            'galaxy_from'    => $planet1->galaxy, 'system_from' => $planet1->system, 'position_from' => $planet1->planet,
            'galaxy_to'      => $planet2->galaxy, 'system_to' => $planet2->system, 'position_to' => $planet2->planet,
            'mission_type'   => 3,
            'time_departure' => $now - 3600, 'time_arrival' => $now - 1800,
            'small_cargo'    => 10, 'metal' => 50000, 'crystal' => 30000,
        ]));

        // 2 – healthy transport test2 → test3
        $this->seed('Transport test2 → test3', array_merge($base, [
            'user_id'        => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to'   => $planet3->id,
            'galaxy_from'    => $planet2->galaxy, 'system_from' => $planet2->system, 'position_from' => $planet2->planet,
            'galaxy_to'      => $planet3->galaxy, 'system_to' => $planet3->system, 'position_to' => $planet3->planet,
            'mission_type'   => 3,
            'time_departure' => $now - 7200, 'time_arrival' => $now - 5400,
            'large_cargo'    => 5, 'deuterium' => 20000,
        ]));

        // 3 – healthy transport test3 → test1
        $this->seed('Transport test3 → test1', array_merge($base, [
            'user_id'        => $user3->id,
            'planet_id_from' => $planet3->id,
            'planet_id_to'   => $planet1->id,
            'galaxy_from'    => $planet3->galaxy, 'system_from' => $planet3->system, 'position_from' => $planet3->planet,
            'galaxy_to'      => $planet1->galaxy, 'system_to' => $planet1->system, 'position_to' => $planet1->planet,
            'mission_type'   => 3,
            'time_departure' => $now - 900, 'time_arrival' => $now - 300,
            'small_cargo'    => 3, 'crystal' => 15000,
        ]));

        // 4 – attack test2 → test3
        $this->seed('Attack test2 → test3', array_merge($base, [
            'user_id'        => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to'   => $planet3->id,
            'galaxy_from'    => $planet2->galaxy, 'system_from' => $planet2->system, 'position_from' => $planet2->planet,
            'galaxy_to'      => $planet3->galaxy, 'system_to' => $planet3->system, 'position_to' => $planet3->planet,
            'mission_type'   => 1,
            'time_departure' => $now - 1800, 'time_arrival' => $now - 600,
            'light_fighter'  => 50, 'heavy_fighter' => 20,
        ]));

        // 5 – espionage test1 → test2
        $this->seed('Espionage test1 → test2', array_merge($base, [
            'user_id'         => $user1->id,
            'planet_id_from'  => $planet1->id,
            'planet_id_to'    => $planet2->id,
            'galaxy_from'     => $planet1->galaxy, 'system_from' => $planet1->system, 'position_from' => $planet1->planet,
            'galaxy_to'       => $planet2->galaxy, 'system_to' => $planet2->system, 'position_to' => $planet2->planet,
            'mission_type'    => 6,
            'time_departure'  => $now - 2700, 'time_arrival' => $now - 2400,
            'espionage_probe' => 3,
        ]));

        // 6 – expedition test1 → deep space
        $this->seed('Expedition test1 → deep space', array_merge($base, [
            'user_id'        => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to'   => null,
            'galaxy_from'    => $planet1->galaxy, 'system_from' => $planet1->system, 'position_from' => $planet1->planet,
            'galaxy_to'      => 1, 'system_to' => 250, 'position_to' => 16,
            'type_to'        => PlanetType::DeepSpace->value,
            'mission_type'   => 15,
            'time_departure' => $now - 4500, 'time_arrival' => $now - 900,
            'large_cargo'    => 2,
        ]));

        // 7 – recycle test2 → debris field
        $this->seed('Recycle test2 → debris', array_merge($base, [
            'user_id'        => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to'   => null,
            'galaxy_from'    => $planet2->galaxy, 'system_from' => $planet2->system, 'position_from' => $planet2->planet,
            'galaxy_to'      => $planet3->galaxy, 'system_to' => $planet3->system, 'position_to' => $planet3->planet,
            'type_to'        => PlanetType::DebrisField->value,
            'mission_type'   => 8,
            'time_departure' => $now - 3000, 'time_arrival' => $now - 1200,
            'recycler'       => 5,
        ]));

        // 8 – colonisation test3 → empty slot
        $this->seed('Colonisation test3 → empty slot', array_merge($base, [
            'user_id'        => $user3->id,
            'planet_id_from' => $planet3->id,
            'planet_id_to'   => null,
            'galaxy_from'    => $planet3->galaxy, 'system_from' => $planet3->system, 'position_from' => $planet3->planet,
            'galaxy_to'      => 2, 'system_to' => 100, 'position_to' => 7,
            'mission_type'   => 7,
            'time_departure' => $now - 6000, 'time_arrival' => $now - 3600,
            'colony_ship'    => 1,
        ]));

        // 9 – broken return: insert a processed parent first, then an unprocessed return with no linked planets
        $parentId = DB::table('fleet_missions')->insertGetId(array_merge($base, [
            'user_id'        => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to'   => $planet2->id,
            'galaxy_from'    => $planet1->galaxy, 'system_from' => $planet1->system, 'position_from' => $planet1->planet,
            'galaxy_to'      => $planet2->galaxy, 'system_to' => $planet2->system, 'position_to' => $planet2->planet,
            'mission_type'   => 3,
            'time_departure' => $now - 7200, 'time_arrival' => $now - 5400,
            'processed'      => 1,
            'small_cargo'    => 5, 'metal' => 10000,
        ]));

        $this->seed('Broken return (no linked planets)', array_merge($base, [
            'user_id'        => $user1->id,
            'parent_id'      => $parentId,
            'planet_id_from' => null,
            'planet_id_to'   => null,
            'galaxy_from'    => $planet2->galaxy, 'system_from' => $planet2->system, 'position_from' => $planet2->planet,
            'galaxy_to'      => $planet1->galaxy, 'system_to' => $planet1->system, 'position_to' => $planet1->planet,
            'type_from'      => PlanetType::Moon->value,
            'type_to'        => PlanetType::Planet->value,
            'mission_type'   => 3,
            'time_departure' => $now - 4800, 'time_arrival' => $now - 3000,
            'small_cargo'    => 5, 'metal' => 10000,
        ]));

        // 10 – missing origin: planet_id_from=null, type_from=Planet → "Missing origin"
        $this->seed('Missing origin (no planet_id_from)', array_merge($base, [
            'user_id'        => $user2->id,
            'planet_id_from' => null,
            'planet_id_to'   => $planet1->id,
            'galaxy_from'    => $planet3->galaxy, 'system_from' => $planet3->system, 'position_from' => $planet3->planet,
            'galaxy_to'      => $planet1->galaxy, 'system_to' => $planet1->system, 'position_to' => $planet1->planet,
            'mission_type'   => 3,
            'time_departure' => $now - 2400, 'time_arrival' => $now - 1500,
            'small_cargo'    => 8, 'metal' => 25000,
        ]));

        $this->newLine();
        $this->info('Stuck fleet missions seeded. Visit /admin/server-administration to review.');
        $this->newLine();
        $this->table(
            ['#', 'User', 'Mission type', 'Expected status'],
            [
                ['1', 'test1', 'Transport (3)',    'Ready to process'],
                ['2', 'test2', 'Transport (3)',    'Ready to process'],
                ['3', 'test3', 'Transport (3)',    'Ready to process'],
                ['4', 'test2', 'Attack (1)',       'Ready to process'],
                ['5', 'test1', 'Espionage (6)',    'Ready to process'],
                ['6', 'test1', 'Expedition (15)',  'Ready to process'],
                ['7', 'test2', 'Recycle (8)',      'Ready to process'],
                ['8', 'test3', 'Colonisation (7)', 'Ready to process'],
                ['9', 'test1', 'Transport (3)',    'Broken return target'],
                ['10', 'test2', 'Transport (3)',   'Missing origin'],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function seed(string $label, array $data): void
    {
        $this->info("  Inserting: {$label}...");
        DB::table('fleet_missions')->insert($data);
    }
}
