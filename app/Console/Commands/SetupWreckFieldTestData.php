<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Models\WreckField;

class SetupWreckFieldTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wreckfield:setup-test-data
                            {--clean : Delete existing test users and data first}
                            {--password=password123 : Password for the test accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test users, planets, and a wreck field at 1:1:6';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $password = $this->option('password');
        $clean = $this->option('clean');

        if ($clean) {
            $this->cleanExistingData();
        }

        $this->info('Setting up wreck field test data...');

        // Create two test users
        $user1 = $this->createUser('testuser1@example.com', 'TestUser1', $password);
        $user2 = $this->createUser('testuser2@example.com', 'TestUser2', $password);

        // Create planets at 1:1:6 (main planet) with Space Dock level 5
        $planet1 = $this->createPlanet($user1->id, 1, 1, 6, 'Homeworld', 5);
        $planet2 = $this->createPlanet($user2->id, 1, 1, 7, 'Colony', 3);

        // Create a wreck field at 1:1:6 for user1
        $this->createWreckField($planet1, $user1->id);

        $this->newLine();
        $this->info('✓ Test data created successfully!');
        $this->newLine();
        $this->info('Test Accounts:');
        $this->table(
            ['Email', 'Username', 'Password', 'Planet', 'Coordinates'],
            [
                [$user1->email, 'TestUser1', $password, $planet1->name, '[1:1:6]'],
                [$user2->email, 'TestUser2', $password, $planet2->name, '[1:1:7]'],
            ]
        );
        $this->newLine();
        $this->info('✓ Wreck field created at [1:1:6] for TestUser1');
        $this->info('✓ Space Dock level 5 on [1:1:6]');
        $this->info('✓ Space Dock level 3 on [1:1:7]');

        return Command::SUCCESS;
    }

    /**
     * Create a test user.
     */
    private function createUser(string $email, string $username, string $password): User
    {
        $this->info("Creating user: {$username}");

        return User::factory()->create([
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'lang' => 'en',
        ]);
    }

    /**
     * Create a planet with Space Dock.
     */
    private function createPlanet(int $userId, int $galaxy, int $system, int $planet, string $name, int $spaceDockLevel): Planet
    {
        $this->info("Creating planet: [{$galaxy}:{$system}:{$planet}] with Space Dock level {$spaceDockLevel}");

        return Planet::factory()->create([
            'user_id' => $userId,
            'galaxy' => $galaxy,
            'system' => $system,
            'planet' => $planet,
            'name' => $name,
            'space_dock' => $spaceDockLevel,
        ]);
    }

    /**
     * Create a wreck field with a mix of ships.
     */
    private function createWreckField(Planet $planet, int $ownerId): void
    {
        $this->info("Creating wreck field at [{$planet->galaxy}:{$planet->system}:{$planet->planet}]");

        $wreckField = new WreckField();
        $wreckField->galaxy = $planet->galaxy;
        $wreckField->system = $planet->system;
        $wreckField->planet = $planet->planet;
        $wreckField->owner_player_id = $ownerId;
        $wreckField->status = 'active';
        $wreckField->created_at = now();
        $wreckField->expires_at = now()->addHours(72);
        $wreckField->ship_data = [
            ['machine_name' => 'light_fighter', 'quantity' => 500, 'repair_progress' => 0],
            ['machine_name' => 'heavy_fighter', 'quantity' => 200, 'repair_progress' => 0],
            ['machine_name' => 'cruiser', 'quantity' => 100, 'repair_progress' => 0],
            ['machine_name' => 'battle_ship', 'quantity' => 50, 'repair_progress' => 0],
            ['machine_name' => 'battlecruiser', 'quantity' => 30, 'repair_progress' => 0],
            ['machine_name' => 'bomber', 'quantity' => 20, 'repair_progress' => 0],
            ['machine_name' => 'destroyer', 'quantity' => 10, 'repair_progress' => 0],
            ['machine_name' => 'deathstar', 'quantity' => 2, 'repair_progress' => 0],
            ['machine_name' => 'small_cargo', 'quantity' => 300, 'repair_progress' => 0],
            ['machine_name' => 'large_cargo', 'quantity' => 200, 'repair_progress' => 0],
            ['machine_name' => 'colony_ship', 'quantity' => 5, 'repair_progress' => 0],
            ['machine_name' => 'recycler', 'quantity' => 100, 'repair_progress' => 0],
            ['machine_name' => 'espionage_probe', 'quantity' => 50, 'repair_progress' => 0],
        ];
        $wreckField->save();

        $this->info('  Ships in wreck field:');
        foreach ($wreckField->ship_data as $ship) {
            $this->info("    - {$ship['machine_name']}: {$ship['quantity']}");
        }
    }

    /**
     * Clean existing test data.
     */
    private function cleanExistingData(): void
    {
        $this->warn('Cleaning existing test data...');

        $testEmails = [
            'testuser1@example.com',
            'testuser2@example.com',
        ];

        // Get test user IDs before deleting
        $testUserIds = User::whereIn('email', $testEmails)->pluck('id')->toArray();

        $deleted = User::whereIn('email', $testEmails)->delete();
        $this->info("Deleted {$deleted} test users");

        // Clean up planets owned by test users at any coordinates
        if (!empty($testUserIds)) {
            $deletedPlanets = Planet::whereIn('user_id', $testUserIds)->delete();
            $this->info("Deleted {$deletedPlanets} planets owned by test users");

            // Clean up wreck fields owned by test users at any coordinates
            $deletedWreckFields = WreckField::whereIn('owner_player_id', $testUserIds)->delete();
            $this->info("Deleted {$deletedWreckFields} wreck fields owned by test users");
        }
    }
}
