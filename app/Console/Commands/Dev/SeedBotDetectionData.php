<?php

namespace OGame\Console\Commands\Dev;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OGame\Models\Planet;
use OGame\Models\User;

class SeedBotDetectionData extends Command
{
    protected $signature = 'ogamex:dev:seed-bot-detection
                            {--fresh : Clear existing fleet missions for test users before seeding}';

    protected $description = 'Seed fleet mission records to trigger all three bot-detection signals. Requires ogamex:dev:seed-users to have been run first.';

    private const EMAIL_DOMAIN = 'ogamex.dev';

    public function handle(): int
    {
        /** @var Collection<int, User> $users */
        $users = User::where('email', 'like', '%@' . self::EMAIL_DOMAIN)->get()->keyBy('username');

        if ($users->isEmpty()) {
            $this->error('No test users found. Run ogamex:dev:seed-users first.');
            return self::FAILURE;
        }

        foreach (['test2', 'test3', 'test4'] as $needed) {
            if (!$users->has($needed)) {
                $this->error("Required user {$needed} not found. Run ogamex:dev:seed-users first.");
                return self::FAILURE;
            }
        }

        /** @var User $test2 */
        $test2 = $users->get('test2');
        /** @var User $test3 */
        $test3 = $users->get('test3');
        /** @var User $test4 */
        $test4 = $users->get('test4');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing fleet missions for test users...');
            DB::table('fleet_missions')
                ->whereIn('user_id', $users->pluck('id')->toArray())
                ->delete();
        }

        $planet2 = Planet::where('user_id', $test2->id)->first();
        $planet3 = Planet::where('user_id', $test3->id)->first();
        $planet4 = Planet::where('user_id', $test4->id)->first();

        if (!$planet2 || !$planet3 || !$planet4) {
            $this->error('One or more test users have no planets. Run ogamex:dev:seed-users first.');
            return self::FAILURE;
        }

        $this->seedSharedIp($test2, $test3, $planet2, $planet3);
        $this->seedRoundTheClock($test4, $planet4);
        $this->seedInstantExpeditionRedispatch($test4, $planet4);
        $this->seedInstantFleetSaveAfterAttack($test2, $planet2, $test4, $planet4);

        Cache::forget('bot_detection_suspects');
        Cache::forget('bot_detection_ip_groups');

        $this->newLine();
        $this->info('Bot detection test data seeded. Visit /admin/server-administration to review.');
        $this->newLine();
        $this->table(
            ['Scenario', 'Who', 'Expected result'],
            [
                ['Shared registration IP + cross-account transports', 'test2 & test3', 'Appear in Shared IP Groups'],
                ['Round-the-clock (210 expeditions, all 24 hours, 7 days)', 'test4', 'Round-the-clock signal'],
                ['Instant expedition re-dispatch (3-second gap, 6 times)', 'test4', 'Instant re-dispatch signal'],
                ['Instant fleet-save after attack (2-second reaction, 3 attacks)', 'test4', 'Instant fleet-save signal'],
            ]
        );

        return self::SUCCESS;
    }

    /**
     * Set the same register_ip + last_ip on two users and insert cross-account transport missions.
     */
    private function seedSharedIp(User $user1, User $user2, Planet $planet1, Planet $planet2): void
    {
        $this->info('Seeding shared IP scenario (test2 + test3)...');

        $sharedIp = '192.168.66.100';
        $user1->last_ip     = $sharedIp;
        $user1->register_ip = $sharedIp;
        $user1->save();

        $user2->last_ip     = $sharedIp;
        $user2->register_ip = $sharedIp;
        $user2->save();

        $now = (int) now()->timestamp;

        // test2 → test3: large resource transfer
        DB::table('fleet_missions')->insert([
            'user_id'        => $user1->id,
            'planet_id_from' => $planet1->id,
            'planet_id_to'   => $planet2->id,
            'mission_type'   => 3,
            'time_departure' => $now - 3600,
            'time_arrival'   => $now - 1800,
            'metal'          => 500000,
            'crystal'        => 300000,
            'deuterium'      => 100000,
            'large_cargo'    => 50,
            'canceled'       => 0,
            'processed'      => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // test3 → test2: return transfer
        DB::table('fleet_missions')->insert([
            'user_id'        => $user2->id,
            'planet_id_from' => $planet2->id,
            'planet_id_to'   => $planet1->id,
            'mission_type'   => 3,
            'time_departure' => $now - 7200,
            'time_arrival'   => $now - 5400,
            'metal'          => 200000,
            'crystal'        => 150000,
            'small_cargo'    => 30,
            'canceled'       => 0,
            'processed'      => 1,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $this->info('  Shared IP set and 2 cross-account transport missions created.');
    }

    /**
     * Insert 210 expedition missions spread across all 24 hours over 7 days.
     * With computer_technology = 0 (1 fleet slot): 210 / (7 * 1) = 30 missions/slot/day > 18 threshold.
     */
    private function seedRoundTheClock(User $user, Planet $planet): void
    {
        $this->info('Seeding round-the-clock activity (test4, 210 expeditions over 7 days)...');

        $now     = (int) now()->timestamp;
        $batch   = [];

        for ($i = 0; $i < 210; $i++) {
            $daysAgo   = $i % 7;
            $hourSlot  = $i % 24;
            $departure = $now - ($daysAgo * 86400) - ($hourSlot * 3600) - rand(0, 59) * 60;

            $batch[] = [
                'user_id'        => $user->id,
                'planet_id_from' => $planet->id,
                'planet_id_to'   => null,
                'galaxy_to'      => 1,
                'system_to'      => rand(1, 499),
                'position_to'    => 16,
                'mission_type'   => 15,
                'time_departure' => $departure,
                'time_arrival'   => $departure + 3600,
                'canceled'       => 0,
                'processed'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        DB::table('fleet_missions')->insert($batch);
        $this->info('  210 expedition missions created across all 24 hours.');
    }

    /**
     * Insert 6 outbound/return/re-dispatch expedition triplets where the re-dispatch
     * follows the return within 3 seconds — well under the 10-second default threshold.
     */
    private function seedInstantExpeditionRedispatch(User $user, Planet $planet): void
    {
        $this->info('Seeding instant expedition re-dispatch (test4, 6 pairs, 3-second gap)...');

        $now = (int) now()->timestamp;

        for ($i = 0; $i < 6; $i++) {
            $base = $now - (($i + 1) * 7200);

            // Original outbound expedition
            $outboundId = DB::table('fleet_missions')->insertGetId([
                'user_id'        => $user->id,
                'planet_id_from' => $planet->id,
                'planet_id_to'   => null,
                'galaxy_to'      => 1,
                'system_to'      => rand(1, 499),
                'position_to'    => 16,
                'mission_type'   => 15,
                'time_departure' => $base,
                'time_arrival'   => $base + 3600,
                'canceled'       => 0,
                'processed'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $returnArrival = $base + 3600;

            // Return expedition — planet_id_to is the home planet (returned to)
            DB::table('fleet_missions')->insert([
                'user_id'        => $user->id,
                'planet_id_from' => null,
                'planet_id_to'   => $planet->id,
                'mission_type'   => 15,
                'time_departure' => $base,
                'time_arrival'   => $returnArrival,
                'canceled'       => 0,
                'processed'      => 1,
                'parent_id'      => $outboundId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Immediate re-dispatch — 3 seconds after return
            DB::table('fleet_missions')->insert([
                'user_id'        => $user->id,
                'planet_id_from' => $planet->id,
                'planet_id_to'   => null,
                'galaxy_to'      => 1,
                'system_to'      => rand(1, 499),
                'position_to'    => 16,
                'mission_type'   => 15,
                'time_departure' => $returnArrival + 3,
                'time_arrival'   => $returnArrival + 3 + 3600,
                'canceled'       => 0,
                'processed'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->info('  6 outbound/return/re-dispatch triplets created (3-second gap).');
    }

    /**
     * Insert 3 attack missions from the attacker to the defender's planet, each followed
     * within 2 seconds by the defender dispatching a fleet — instant fleet-save pattern.
     */
    private function seedInstantFleetSaveAfterAttack(User $attacker, Planet $attackerPlanet, User $defender, Planet $defenderPlanet): void
    {
        $this->info('Seeding instant fleet-save after attack (test2 attacks test4, 2-second reaction, 3 times)...');

        $now = (int) now()->timestamp;

        for ($i = 0; $i < 3; $i++) {
            $attackTime = $now - (($i + 1) * 3600);

            // Attack dispatched
            DB::table('fleet_missions')->insert([
                'user_id'        => $attacker->id,
                'planet_id_from' => $attackerPlanet->id,
                'planet_id_to'   => $defenderPlanet->id,
                'mission_type'   => 1,
                'time_departure' => $attackTime,
                'time_arrival'   => $attackTime + 600,
                'light_fighter'  => 100,
                'canceled'       => 0,
                'processed'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Defender reacts 2 seconds later by sending a fleet (expedition as fleet-save)
            DB::table('fleet_missions')->insert([
                'user_id'        => $defender->id,
                'planet_id_from' => $defenderPlanet->id,
                'planet_id_to'   => null,
                'galaxy_to'      => 1,
                'system_to'      => rand(1, 499),
                'position_to'    => 16,
                'mission_type'   => 15,
                'time_departure' => $attackTime + 2,
                'time_arrival'   => $attackTime + 2 + 7200,
                'espionage_probe' => 5,
                'canceled'       => 0,
                'processed'      => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->info('  3 attack + instant fleet-save pairs created (2-second reaction).');
    }
}
