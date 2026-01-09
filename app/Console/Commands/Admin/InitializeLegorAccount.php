<?php

namespace OGame\Console\Commands\Admin;

use OGame\Models\User;
use OGame\Models\Planet;
use OGame\Jobs\CreateLegorMoon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OGame\Factories\PlanetServiceFactory;
use OGame\Models\Planet\Coordinate;
use OGame\Models\UserTech;
use OGame\Services\PlayerService;

class InitializeLegorAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:admin:init-legor {--delay=90 : Delay in seconds before moon creation (default: 90)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the Legor admin account with Arakis planet at 1:1:2 (an ode to the original game)';

    /**
     * Execute the console command.
     */
    public function handle(PlanetServiceFactory $planetServiceFactory): int
    {
        // Check if Legor already exists (created by migration)
        $legor = User::where('username', 'Legor')->first();

        if ($legor) {
            // Legor exists, check if moon already exists
            $moonExists = Planet::where('user_id', $legor->id)
                ->where('planet_type', 3)
                ->exists();

            if ($moonExists) {
                $this->info('Legor account and moon already exist.');
                return Command::SUCCESS;
            }

            // Create moon for existing Legor
            $this->info('Legor account exists, creating moon...');

            $delay = (int) $this->option('delay');
            $minutes = round($delay / 60, 1);
            $this->info("Waiting {$delay} seconds ({$minutes} minutes) before creating moon...");

            sleep($delay);

            // Get Legor's planet and create moon
            $planet = Planet::where('user_id', $legor->id)
                ->where('planet_type', 1)
                ->first();

            if ($planet) {
                $moonCreationJob = new CreateLegorMoon($planet->id);
                $moonCreationJob->handle();
                $this->info("Moon creation complete!");
            }

            return Command::SUCCESS;
        }

        // Check if position 1:1:2 is available
        $coordinate = new Coordinate(1, 1, 2);
        if ($planetServiceFactory->planetExistsAtCoordinate($coordinate)) {
            $this->error('Position 1:1:2 is already occupied!');
            return Command::FAILURE;
        }

        // Prefer ID 1 for Legor (first account in universe), but allow next available if taken
        $userId = 1;
        if (User::where('id', 1)->exists()) {
            $this->warn('User ID 1 already exists. Legor will be created with next available ID.');
            // Let auto-increment handle the ID assignment
            $userId = null;
        }

        // Create User with ID 1 if available, otherwise use auto-increment
        $user = new User();
        if ($userId !== null) {
            $user->id = $userId;
        }
        $user->username = 'Legor';
        $user->email = 'legor@ogamex.local';
        $user->password = Hash::make(Str::random(32));
        $user->lang = 'en';
        $user->time = (string) now()->timestamp;
        $user->save();

        // Create UserTech record
        UserTech::create(['user_id' => $user->id]);

        // Assign admin role
        $user->assignRole('admin');

        // Create planet at 1:1:2
        $playerService = app()->make(PlayerService::class);
        $playerService->load($user->id);

        $planetService = $planetServiceFactory->createPlanetAtPosition(
            $playerService,
            $coordinate,
            'Arakis'
        );

        $this->info("Created Legor account with planet Arakis at 1:1:2");

        // Create moon after delay (1-2 minutes after planet creation)
        // Using synchronous sleep to avoid queue caching issues
        $delay = (int) $this->option('delay');
        $minutes = round($delay / 60, 1);
        $this->info("Waiting {$delay} seconds ({$minutes} minutes) before creating moon...");

        sleep($delay);

        $moonCreationJob = new CreateLegorMoon($planetService->getPlanetId());
        $moonCreationJob->handle();

        $this->info("Moon creation complete!");

        return Command::SUCCESS;
    }
}
