<?php

namespace OGame\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use OGame\Enums\CharacterClass;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Planet;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\ObjectService;
use OGame\Services\PlanetService;

class PreviewSeedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:preview-seed-users
                            {--password=test : Password for all test accounts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with test users for preview environments. Recreates users on each run.';

    /**
     * The email domain for the preview users.
     *
     * @var string
     */
    private const EMAIL_DOMAIN = 'ogamex.dev';

    /**
     * The user configurations.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $userConfigs = [
        1 => [
            'description' => 'Admin, Collector, 2 planets, high resources',
            'role' => 'admin',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 100000,
            'tech' => ['energy_technology' => 10, 'computer_technology' => 10, 'espionage_technology' => 8, 'astrophysics' => 4],
            'planet' => ['metal' => 500000, 'crystal' => 300000, 'deuterium' => 100000, 'metal_mine' => 20, 'crystal_mine' => 18, 'deuterium_synthesizer' => 15],
            'additional_planets' => [
                ['name' => 'Colony', 'config' => ['metal' => 100000, 'crystal' => 80000, 'deuterium' => 50000, 'metal_mine' => 15, 'crystal_mine' => 12]],
            ],
        ],
        2 => [
            'description' => 'General, military focus, ships & fleet',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 50000,
            'tech' => ['weapon_technology' => 12, 'shielding_technology' => 10, 'armor_technology' => 10, 'combustion_drive' => 8, 'impulse_drive' => 4, 'ion_technology' => 2],
            'planet' => ['metal' => 200000, 'crystal' => 150000, 'deuterium' => 80000, 'robot_factory' => 10, 'shipyard' => 12, 'light_fighter' => 100, 'heavy_fighter' => 50, 'cruiser' => 20],
        ],
        3 => [
            'description' => 'Discoverer, exploration, probes & cargo',
            'role' => 'player',
            'character_class' => CharacterClass::DISCOVERER,
            'dark_matter' => 75000,
            'tech' => ['astrophysics' => 8, 'espionage_technology' => 12, 'impulse_drive' => 6, 'hyperspace_drive' => 4, 'combustion_drive' => 3],
            'planet' => ['metal' => 150000, 'crystal' => 100000, 'deuterium' => 120000, 'robot_factory' => 4, 'shipyard' => 4, 'research_lab' => 10, 'espionage_probe' => 50, 'small_cargo' => 30],
        ],
        4 => [
            'description' => 'New player, no class selected yet',
            'role' => 'player',
            'character_class' => null,
            'dark_matter' => 10000,
            'tech' => [],
            'planet' => ['metal' => 50000, 'crystal' => 30000, 'deuterium' => 10000],
        ],
        5 => [
            'description' => 'Moderator, Collector, balanced setup',
            'role' => 'moderator',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 200000,
            'tech' => ['energy_technology' => 8, 'laser_technology' => 10, 'ion_technology' => 5, 'plasma_technology' => 3],
            'planet' => ['metal' => 300000, 'crystal' => 200000, 'deuterium' => 150000, 'solar_plant' => 20, 'deuterium_synthesizer' => 5, 'fusion_plant' => 8],
        ],
        6 => [
            'description' => 'General, vacation mode enabled',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 25000,
            'vacation_mode' => true,
            'tech' => ['weapon_technology' => 5, 'shielding_technology' => 5],
            'planet' => ['metal' => 100000, 'crystal' => 80000, 'deuterium' => 40000],
        ],
        7 => [
            'description' => 'General, 3 planets, heavy defense',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 150000,
            'tech' => ['weapon_technology' => 15, 'shielding_technology' => 12, 'armor_technology' => 12, 'hyperspace_technology' => 8, 'astrophysics' => 6, 'plasma_technology' => 7, 'laser_technology' => 6, 'energy_technology' => 3, 'ion_technology' => 4],
            'planet' => [
                'metal' => 400000, 'crystal' => 300000, 'deuterium' => 200000,
                'robot_factory' => 8, 'shipyard' => 8,
                'rocket_launcher' => 500, 'light_laser' => 200, 'heavy_laser' => 50,
                'gauss_cannon' => 20, 'ion_cannon' => 30, 'plasma_turret' => 5,
                'small_shield_dome' => 1, 'large_shield_dome' => 1,
            ],
            'additional_planets' => [
                ['name' => 'Fortress', 'config' => ['metal' => 200000, 'crystal' => 150000, 'deuterium' => 100000, 'robot_factory' => 4, 'shipyard' => 4, 'rocket_launcher' => 1000, 'light_laser' => 500]],
                ['name' => 'Outpost', 'config' => ['metal' => 80000, 'crystal' => 60000, 'deuterium' => 40000, 'metal_mine' => 18, 'crystal_mine' => 15]],
            ],
        ],
        8 => [
            'description' => 'Discoverer, fleet focus, inactive (i)',
            'role' => 'player',
            'character_class' => CharacterClass::DISCOVERER,
            'dark_matter' => 80000,
            'inactive_days' => 8, // Shows as (i) in galaxy - 7+ days inactive
            'tech' => ['combustion_drive' => 10, 'impulse_drive' => 8, 'hyperspace_drive' => 6, 'computer_technology' => 12, 'shielding_technology' => 2],
            'planet' => [
                'metal' => 250000, 'crystal' => 180000, 'deuterium' => 150000,
                'robot_factory' => 6, 'shipyard' => 6,
                'small_cargo' => 100, 'large_cargo' => 50, 'colony_ship' => 2,
                'recycler' => 30, 'espionage_probe' => 100,
            ],
        ],
        9 => [
            'description' => 'Collector, beginner, long inactive (I)',
            'role' => 'player',
            'character_class' => CharacterClass::COLLECTOR,
            'dark_matter' => 5000,
            'inactive_days' => 40, // Shows as (I) in galaxy - 28+ days inactive
            'tech' => ['energy_technology' => 1, 'combustion_drive' => 1],
            'planet' => ['metal' => 20000, 'crystal' => 10000, 'deuterium' => 5000, 'metal_mine' => 5, 'crystal_mine' => 3, 'deuterium_synthesizer' => 1],
        ],
        10 => [
            'description' => 'General, endgame, 5 planets, max tech',
            'role' => 'player',
            'character_class' => CharacterClass::GENERAL,
            'dark_matter' => 500000,
            'tech' => [
                'energy_technology' => 15, 'laser_technology' => 12, 'ion_technology' => 10,
                'hyperspace_technology' => 12, 'plasma_technology' => 8,
                'combustion_drive' => 15, 'impulse_drive' => 12, 'hyperspace_drive' => 10,
                'espionage_technology' => 10, 'computer_technology' => 15, 'astrophysics' => 15,
                'weapon_technology' => 15, 'shielding_technology' => 14, 'armor_technology' => 14,
            ],
            'planet' => [
                'metal' => 2000000, 'crystal' => 1500000, 'deuterium' => 800000,
                'metal_mine' => 25, 'crystal_mine' => 23, 'deuterium_synthesizer' => 20,
                'solar_plant' => 22, 'fusion_plant' => 12, 'robot_factory' => 10, 'nano_factory' => 5,
                'shipyard' => 15, 'research_lab' => 12,
                'battle_ship' => 50, 'battlecruiser' => 30, 'bomber' => 20, 'destroyer' => 10,
            ],
            'additional_planets' => [
                ['name' => 'Mining Hub', 'config' => ['metal' => 500000, 'crystal' => 400000, 'deuterium' => 200000, 'metal_mine' => 22, 'crystal_mine' => 20, 'deuterium_synthesizer' => 18]],
                ['name' => 'Fleet Base', 'config' => ['metal' => 300000, 'crystal' => 200000, 'deuterium' => 300000, 'robot_factory' => 10, 'shipyard' => 12, 'battle_ship' => 100, 'cruiser' => 200]],
                ['name' => 'Research Station', 'config' => ['metal' => 200000, 'crystal' => 300000, 'deuterium' => 150000, 'research_lab' => 10]],
                ['name' => 'Defense Platform', 'config' => ['metal' => 150000, 'crystal' => 100000, 'deuterium' => 80000, 'robot_factory' => 8, 'shipyard' => 8, 'rocket_launcher' => 2000, 'plasma_turret' => 20, 'small_shield_dome' => 1, 'large_shield_dome' => 1]],
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

        $this->warn('Cleaning up existing preview test users...');
        DeletePreviewUsers::deleteTestUsers($this);

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

        $this->newLine();
        $this->info('Test users created successfully!');
        $this->newLine();

        $this->table(
            ['Email (login)', 'Password', 'Role', 'Class', 'Description'],
            array_map(fn($u) => [
                $u['email'],
                $u['password'],
                $u['role'],
                $u['class'],
                $u['description'],
            ], $createdUsers)
        );

        $this->newLine();
        $this->info('All users can log in with their email and password: ' . $password);

        return Command::SUCCESS;
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

        // Set last activity time (used for inactive status in galaxy view)
        // inactive_days: 0 or not set = active (now), 7+ = inactive (i), 28+ = long inactive (I)
        $inactiveDays = $config['inactive_days'] ?? 0;
        $user->time = (string) now()->subDays($inactiveDays)->timestamp;

        $user->save();

        // Assign role
        if ($config['role'] === 'admin') {
            $user->assignRole('admin');
        } elseif ($config['role'] === 'moderator') {
            $user->assignRole('moderator');
        }

        // Get building requirements needed for tech (e.g., research_lab level for technologies)
        $techBuildingRequirements = $this->getBuildingRequirementsForTech($config['tech'] ?? []);

        // Create main planet with merged building requirements from both planet config and tech requirements
        $planetConfig = array_merge($techBuildingRequirements, $config['planet'] ?? []);
        $this->createPlanet($user, null, $planetConfig);

        // Create user tech record (after planet so research_lab exists)
        $this->createUserTech($user, $config['tech'] ?? []);

        // Create additional planets if configured
        if (!empty($config['additional_planets'])) {
            foreach ($config['additional_planets'] as $additionalPlanet) {
                $this->createPlanet($user, $additionalPlanet['name'] ?? null, $additionalPlanet['config'] ?? []);
            }
        }

        return $user;
    }

    /**
     * Create user tech record with specified levels.
     * Automatically expands to include all recursive prerequisites.
     *
     * @param User $user
     * @param array<string, int> $techLevels
     */
    private function createUserTech(User $user, array $techLevels): void
    {
        $techData = ['user_id' => $user->id];

        $userTech = UserTech::create($techData);

        // Expand tech levels to include all recursive prerequisites
        $expandedTechLevels = $this->expandTechRequirements($techLevels);

        // Update tech levels individually since they're not fillable
        foreach ($expandedTechLevels as $tech => $level) {
            $userTech->{$tech} = $level;
        }
        $userTech->save();
    }

    /**
     * Create a planet for the user using the built-in PlanetServiceFactory.
     *
     * @param User $user
     * @param string|null $planetName Optional custom name (null = use factory default)
     * @param array<string, mixed> $planetConfig
     */
    private function createPlanet(User $user, ?string $planetName, array $planetConfig): PlanetService
    {
        // Get services from container
        $playerServiceFactory = app(PlayerServiceFactory::class);
        $planetServiceFactory = app(PlanetServiceFactory::class);

        // Get player service for this user (reload to get fresh planet list)
        $playerService = $playerServiceFactory->make($user->id, true);

        // Check if this is the first planet for the user
        $isFirstPlanet = $playerService->planets->planetCount() === 0;

        if ($isFirstPlanet) {
            // Create initial planet using the built-in factory (default name: "Homeworld")
            $planetService = $planetServiceFactory->createInitialPlanetForPlayer(
                $playerService,
                $planetName ?? 'Homeworld'
            );
        } else {
            // Create additional planet (colony) - factory will find available position
            $coordinate = $planetServiceFactory->determineNewPlanetPosition();
            $planetService = $planetServiceFactory->createAdditionalPlanetForPlayer(
                $playerService,
                $coordinate
            );
        }

        // Apply custom config values (resources, buildings, ships, defense)
        // Get planet model directly from DB since PlanetService has it as private
        $planet = Planet::find($planetService->getPlanetId());

        // Only set custom name if provided
        if ($planetName !== null) {
            $planet->name = $planetName;
        }

        // Expand building requirements to include all recursive prerequisites
        $expandedConfig = $this->expandBuildingRequirements($planetConfig);

        foreach ($expandedConfig as $key => $value) {
            $planet->{$key} = $value;
        }
        $planet->save();

        // Update user's current planet to first planet only
        if ($isFirstPlanet) {
            $user->planet_current = $planet->id;
            $user->save();
        }

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
        return $this->getUsername($num) . '@' . self::EMAIL_DOMAIN;
    }

    /**
     * Get building requirements needed for the given tech levels.
     * Technologies require buildings like research_lab at certain levels.
     *
     * @param array<string, int> $techLevels
     * @return array<string, int> Building requirements [machine_name => level]
     */
    private function getBuildingRequirementsForTech(array $techLevels): array
    {
        $buildingRequirements = [];

        foreach ($techLevels as $techName => $level) {
            $requirements = ObjectService::getRecursiveRequirements($techName);
            foreach ($requirements as $reqName => $reqLevel) {
                try {
                    $obj = ObjectService::getObjectByMachineName($reqName);
                    if ($obj->type === \OGame\GameObjects\Models\Enums\GameObjectType::Building ||
                        $obj->type === \OGame\GameObjects\Models\Enums\GameObjectType::Station) {
                        if (!isset($buildingRequirements[$reqName]) || $buildingRequirements[$reqName] < $reqLevel) {
                            $buildingRequirements[$reqName] = $reqLevel;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $buildingRequirements;
    }

    /**
     * Expand tech requirements to include all recursive research prerequisites.
     *
     * @param array<string, int> $techLevels Original tech levels
     * @return array<string, int> Expanded tech levels with all prerequisites
     */
    private function expandTechRequirements(array $techLevels): array
    {
        $expanded = $techLevels;

        foreach ($techLevels as $techName => $level) {
            $requirements = ObjectService::getRecursiveRequirements($techName);
            foreach ($requirements as $reqName => $reqLevel) {
                try {
                    $obj = ObjectService::getObjectByMachineName($reqName);
                    if ($obj->type === \OGame\GameObjects\Models\Enums\GameObjectType::Research) {
                        if (!isset($expanded[$reqName]) || $expanded[$reqName] < $reqLevel) {
                            $expanded[$reqName] = $reqLevel;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $expanded;
    }

    /**
     * Expand planet config to include all recursive building prerequisites.
     *
     * @param array<string, mixed> $planetConfig Original planet config
     * @return array<string, mixed> Expanded config with all building prerequisites
     */
    private function expandBuildingRequirements(array $planetConfig): array
    {
        $expanded = $planetConfig;

        foreach ($planetConfig as $key => $value) {
            if (in_array($key, ['metal', 'crystal', 'deuterium', 'name'])) {
                continue;
            }

            $requirements = ObjectService::getRecursiveRequirements($key);
            foreach ($requirements as $reqName => $reqLevel) {
                try {
                    $obj = ObjectService::getObjectByMachineName($reqName);
                    if ($obj->type === \OGame\GameObjects\Models\Enums\GameObjectType::Building ||
                        $obj->type === \OGame\GameObjects\Models\Enums\GameObjectType::Station) {
                        if (!isset($expanded[$reqName]) || $expanded[$reqName] < $reqLevel) {
                            $expanded[$reqName] = $reqLevel;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $expanded;
    }
}
