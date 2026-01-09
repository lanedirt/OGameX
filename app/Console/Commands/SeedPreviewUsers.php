<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use OGame\Enums\CharacterClass;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\PlanetService;

class SeedPreviewUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:seed-preview-users
                            {--password=pass : Password for all test accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with test users for preview environment testing. Cleans up and recreates users on each run.';

    /**
     * Prefix for test user emails to identify them.
     */
    private const EMAIL_PREFIX = 'previewtest';
    private const EMAIL_DOMAIN = 'ogamex.dev';

    /**
     * Test user configurations with various settings for different testing scenarios.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $userConfigs = [
        1 => [
            'description' => 'Admin user with Collector class, high resources',
            'role' => 'admin',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 100000,
            'tech' => ['energy_technology' => 10, 'computer_technology' => 10, 'espionage_technology' => 8],
            'planet' => ['metal' => 500000, 'crystal' => 300000, 'deuterium' => 100000, 'metal_mine' => 20, 'crystal_mine' => 18, 'deuterium_synthesizer' => 15],
        ],
        2 => [
            'description' => 'Regular user with General class, military focus',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 50000,
            'tech' => ['weapon_technology' => 12, 'shielding_technology' => 10, 'armor_technology' => 10, 'combustion_drive' => 8],
            'planet' => ['metal' => 200000, 'crystal' => 150000, 'deuterium' => 80000, 'shipyard' => 12, 'light_fighter' => 100, 'heavy_fighter' => 50, 'cruiser' => 20],
        ],
        3 => [
            'description' => 'Regular user with Discoverer class, exploration focus',
            'role' => 'player',
            'character_class' => CharacterClass::DISCOVERER,
            'dark_matter' => 75000,
            'tech' => ['astrophysics' => 8, 'espionage_technology' => 12, 'impulse_drive' => 6, 'hyperspace_drive' => 4],
            'planet' => ['metal' => 150000, 'crystal' => 100000, 'deuterium' => 120000, 'research_lab' => 10, 'espionage_probe' => 50, 'small_cargo' => 30],
        ],
        4 => [
            'description' => 'New player without character class selected',
            'role' => 'player',
            'character_class' => null,
            'dark_matter' => 10000,
            'tech' => [],
            'planet' => ['metal' => 50000, 'crystal' => 30000, 'deuterium' => 10000],
        ],
        5 => [
            'description' => 'Moderator with balanced setup',
            'role' => 'moderator',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 200000,
            'tech' => ['energy_technology' => 8, 'laser_technology' => 10, 'ion_technology' => 5, 'plasma_technology' => 3],
            'planet' => ['metal' => 300000, 'crystal' => 200000, 'deuterium' => 150000, 'solar_plant' => 20, 'fusion_plant' => 8],
        ],
        6 => [
            'description' => 'Player in vacation mode',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 25000,
            'vacation_mode' => true,
            'tech' => ['weapon_technology' => 5, 'shielding_technology' => 5],
            'planet' => ['metal' => 100000, 'crystal' => 80000, 'deuterium' => 40000],
        ],
        7 => [
            'description' => 'Advanced player with moon and defense',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 150000,
            'tech' => ['weapon_technology' => 15, 'shielding_technology' => 12, 'armor_technology' => 12, 'hyperspace_technology' => 8],
            'planet' => [
                'metal' => 400000, 'crystal' => 300000, 'deuterium' => 200000,
                'rocket_launcher' => 500, 'light_laser' => 200, 'heavy_laser' => 50,
                'gauss_cannon' => 20, 'ion_cannon' => 30, 'plasma_turret' => 5,
                'small_shield_dome' => 1, 'large_shield_dome' => 1,
            ],
        ],
        8 => [
            'description' => 'Fleet-focused player with many ships',
            'role' => 'player',
            'character_class' => CharacterClass::DISCOVERER,
            'dark_matter' => 80000,
            'tech' => ['combustion_drive' => 10, 'impulse_drive' => 8, 'hyperspace_drive' => 6, 'computer_technology' => 12],
            'planet' => [
                'metal' => 250000, 'crystal' => 180000, 'deuterium' => 150000,
                'small_cargo' => 100, 'large_cargo' => 50, 'colony_ship' => 2,
                'recycler' => 30, 'espionage_probe' => 100,
            ],
        ],
        9 => [
            'description' => 'Beginner player with minimal progress',
            'role' => 'player',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 5000,
            'tech' => ['energy_technology' => 1, 'combustion_drive' => 1],
            'planet' => ['metal' => 20000, 'crystal' => 10000, 'deuterium' => 5000, 'metal_mine' => 5, 'crystal_mine' => 3, 'deuterium_synthesizer' => 1],
        ],
        10 => [
            'description' => 'End-game player with high tech and resources',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 500000,
            'tech' => [
                'energy_technology' => 15, 'laser_technology' => 12, 'ion_technology' => 10,
                'hyperspace_technology' => 12, 'plasma_technology' => 8,
                'combustion_drive' => 15, 'impulse_drive' => 12, 'hyperspace_drive' => 10,
                'espionage_technology' => 10, 'computer_technology' => 15, 'astrophysics' => 12,
                'weapon_technology' => 15, 'shielding_technology' => 14, 'armor_technology' => 14,
            ],
            'planet' => [
                'metal' => 2000000, 'crystal' => 1500000, 'deuterium' => 800000,
                'metal_mine' => 25, 'crystal_mine' => 23, 'deuterium_synthesizer' => 20,
                'solar_plant' => 22, 'fusion_plant' => 12, 'robot_factory' => 10, 'nano_factory' => 5,
                'shipyard' => 15, 'research_lab' => 12,
                'battle_ship' => 50, 'battlecruiser' => 30, 'bomber' => 20, 'destroyer' => 10,
            ],
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $password = $this->option('password');

        $this->info('Seeding preview environment test users...');
        $this->newLine();

        // Always clean up existing test users first
        $this->cleanExistingUsers();

        // Create test users
        $createdUsers = [];
        foreach ($this->userConfigs as $num => $config) {
            $user = $this->createTestUser($num, $config, $password);
            $createdUsers[] = [
                'username' => $user->username,
                'email' => $user->email,
                'password' => $password,
                'role' => $config['role'],
                'class' => $config['character_class']?->getName() ?? 'None',
                'description' => $config['description'],
            ];
        }

        // Display summary
        $this->newLine();
        $this->info('Test users created successfully!');
        $this->newLine();

        $this->table(
            ['Username', 'Password', 'Role', 'Class', 'Description'],
            array_map(fn($u) => [
                $u['username'],
                $u['password'],
                $u['role'],
                $u['class'],
                $u['description'],
            ], $createdUsers)
        );

        $this->newLine();
        $this->info('All users can log in with their username and password: ' . $password);

        return Command::SUCCESS;
    }

    /**
     * Clean up existing preview test users.
     */
    private function cleanExistingUsers(): void
    {
        $this->warn('Cleaning up existing preview test users...');

        // Find users by email pattern
        $testEmails = [];
        for ($i = 1; $i <= 10; $i++) {
            $testEmails[] = $this->getEmail($i);
        }

        // Get user IDs before deleting
        $testUserIds = User::whereIn('email', $testEmails)->pluck('id')->toArray();

        if (empty($testUserIds)) {
            $this->info('No existing test users found.');
            return;
        }

        // Delete related records first (in correct order to avoid FK constraints)
        $deletedTech = UserTech::whereIn('user_id', $testUserIds)->delete();
        $this->line("  Deleted {$deletedTech} user tech records");

        $deletedPlanets = Planet::whereIn('user_id', $testUserIds)->delete();
        $this->line("  Deleted {$deletedPlanets} planets");

        $deletedUsers = User::whereIn('id', $testUserIds)->delete();
        $this->line("  Deleted {$deletedUsers} users");
    }

    /**
     * Create a test user with the specified configuration.
     *
     * @param int $num User number (1-10)
     * @param array<string, mixed> $config User configuration
     * @param string $password Password for the user
     */
    private function createTestUser(int $num, array $config, string $password): User
    {
        $username = $this->getUsername($num);
        $email = $this->getEmail($num);

        $this->info("Creating user {$num}: {$username} ({$config['description']})");

        // Create user directly without factory
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->lang = 'en';
        $user->dark_matter = $config['dark_matter'] ?? 0;
        $user->character_class = $config['character_class']?->value;
        $user->character_class_free_used = $config['character_class'] !== null;
        $user->character_class_changed_at = $config['character_class'] !== null ? now() : null;
        $user->first_login = $config['character_class'] === null; // Show class selection for users without class
        $user->vacation_mode = $config['vacation_mode'] ?? false;
        $user->vacation_mode_activated_at = ($config['vacation_mode'] ?? false) ? now()->subDays(2) : null;
        $user->save();

        // Assign role
        if ($config['role'] === 'admin') {
            $user->assignRole('admin');
        } elseif ($config['role'] === 'moderator') {
            $user->assignRole('moderator');
        }

        // Create user tech record
        $this->createUserTech($user, $config['tech'] ?? []);

        // Create planet
        $this->createPlanet($user, $num, $config['planet'] ?? []);

        return $user;
    }

    /**
     * Create user tech record with specified levels.
     *
     * @param User $user
     * @param array<string, int> $techLevels
     */
    private function createUserTech(User $user, array $techLevels): void
    {
        $techData = ['user_id' => $user->id];

        $userTech = UserTech::create($techData);

        // Update tech levels individually since they're not fillable
        foreach ($techLevels as $tech => $level) {
            $userTech->{$tech} = $level;
        }
        $userTech->save();
    }

    /**
     * Create a planet for the user using the built-in PlanetServiceFactory.
     *
     * @param User $user
     * @param int $num User number (determines coordinates)
     * @param array<string, mixed> $planetConfig
     */
    private function createPlanet(User $user, int $num, array $planetConfig): PlanetService
    {
        // Place users in a spread-out pattern in galaxy 1
        $galaxy = 1;
        $system = (int) ceil($num / 2); // 1, 1, 2, 2, 3, 3, 4, 4, 5, 5
        $position = ($num % 2 === 0) ? 8 : 4; // Alternate between position 4 and 8

        $coordinate = new Coordinate($galaxy, $system, $position);

        // Get services from container
        $playerServiceFactory = app(PlayerServiceFactory::class);
        $planetServiceFactory = app(PlanetServiceFactory::class);

        // Get player service for this user
        $playerService = $playerServiceFactory->make($user->id);

        // Create planet using the built-in factory (handles temperature, fields, etc.)
        $planetService = $planetServiceFactory->createPlanetAtPosition(
            $playerService,
            $coordinate,
            $user->username . "'s Planet"
        );

        // Apply custom config values (resources, buildings, ships, defense)
        // Get planet model directly from DB since PlanetService has it as private
        $planet = Planet::find($planetService->getPlanetId());
        foreach ($planetConfig as $key => $value) {
            $planet->{$key} = $value;
        }
        $planet->save();

        // Update user's current planet
        $user->planet_current = $planet->id;
        $user->save();

        return $planetService;
    }

    /**
     * Get username for test user number.
     */
    private function getUsername(int $num): string
    {
        return 'test' . $num;
    }

    /**
     * Get email for test user number.
     */
    private function getEmail(int $num): string
    {
        return self::EMAIL_PREFIX . $num . '@' . self::EMAIL_DOMAIN;
    }
}
