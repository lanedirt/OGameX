<?php

namespace OGame\Console\Commands;

use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\Enums\PlanetType;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\SettingsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateUniverse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ogamex:populate-universe
                            {--galaxies=* : Specific galaxies to populate (e.g., 1 2 3). Default: all galaxies}
                            {--density=medium : Population density - low (20-30%), medium (40-60%), high (70-85%)}
                            {--dry-run : Show what would be created without actually creating anything}
                            {--force : Bypass confirmation prompts}
                            {--min-planets=1 : Minimum planets per bot player}
                            {--max-planets=3 : Maximum planets per bot player}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the OGame universe with bot players and planets';

    private SettingsService $settingsService;
    private PlanetServiceFactory $planetServiceFactory;
    private PlayerServiceFactory $playerServiceFactory;

    /**
     * Execute the console command.
     */
    public function handle(
        SettingsService $settingsService,
        PlanetServiceFactory $planetServiceFactory,
        PlayerServiceFactory $playerServiceFactory
    ): int {
        $this->settingsService = $settingsService;
        $this->planetServiceFactory = $planetServiceFactory;
        $this->playerServiceFactory = $playerServiceFactory;

        // Get configuration
        $totalGalaxies = $settingsService->numberOfGalaxies();
        $density = $this->option('density');
        $isDryRun = $this->option('dry-run');
        $galaxiesToPopulate = $this->getGalaxiesToPopulate($totalGalaxies);
        $minPlanets = max(1, (int)$this->option('min-planets'));
        $maxPlanets = min(9, max($minPlanets, (int)$this->option('max-planets')));

        // Validate density option
        if (!in_array($density, ['low', 'medium', 'high'])) {
            $this->error("Invalid density option. Must be one of: low, medium, high");
            return Command::FAILURE;
        }

        // Calculate population statistics
        $densityRange = $this->getDensityRange($density);
        $totalPositions = count($galaxiesToPopulate) * 499 * 15; // galaxies * systems * positions
        $targetPlanets = (int)($totalPositions * $densityRange['target']);
        $estimatedPlayers = (int)ceil($targetPlanets / (($minPlanets + $maxPlanets) / 2));

        // Show summary
        $this->info("OGameX Universe Population");
        $this->info("==========================");
        $this->table(
            ['Setting', 'Value'],
            [
                ['Total Galaxies', $totalGalaxies],
                ['Galaxies to Populate', implode(', ', $galaxiesToPopulate)],
                ['Systems per Galaxy', 499],
                ['Positions per System', 15],
                ['Density', ucfirst($density) . ' (' . ($densityRange['target'] * 100) . '%)'],
                ['Total Available Positions', number_format($totalPositions)],
                ['Target Planets', number_format($targetPlanets)],
                ['Estimated Bot Players', number_format($estimatedPlayers)],
                ['Planets per Player', $minPlanets . '-' . $maxPlanets],
                ['Mode', $isDryRun ? 'DRY RUN' : 'LIVE'],
            ]
        );

        // Check existing population
        $existingPlanets = Planet::whereIn('galaxy', $galaxiesToPopulate)
            ->where('planet_type', PlanetType::Planet)
            ->count();

        if ($existingPlanets > 0) {
            $this->warn("Warning: Found {$existingPlanets} existing planets in the selected galaxies.");
            if (!$this->option('force') && !$isDryRun) {
                if (!$this->confirm('Do you want to continue and add more planets?')) {
                    $this->info('Operation cancelled.');
                    return Command::SUCCESS;
                }
            }
        }

        if (!$isDryRun && !$this->option('force')) {
            if (!$this->confirm("Are you ready to populate the universe?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Start population
        $this->newLine();
        if ($isDryRun) {
            $this->info("DRY RUN MODE - No changes will be made");
        } else {
            $this->info("Starting universe population...");
        }
        $this->newLine();

        // Generate available positions pool
        $availablePositions = $this->generateAvailablePositions($galaxiesToPopulate, $existingPlanets);
        shuffle($availablePositions);

        // Calculate how many positions to fill
        $positionsToFill = min($targetPlanets - $existingPlanets, count($availablePositions));

        if ($positionsToFill <= 0) {
            $this->warn("No positions available to populate.");
            return Command::SUCCESS;
        }

        $selectedPositions = array_slice($availablePositions, 0, $positionsToFill);

        // Group positions by player
        $playerPositions = $this->groupPositionsByPlayers($selectedPositions, $minPlanets, $maxPlanets);

        $playersCreated = 0;
        $planetsCreated = 0;

        $progressBar = $this->output->createProgressBar(count($playerPositions));
        $progressBar->setFormat('verbose');

        foreach ($playerPositions as $positions) {
            if (!$isDryRun) {
                DB::beginTransaction();
                try {
                    // Create bot player
                    $botPlayer = $this->createBotPlayer();
                    $playersCreated++;

                    // Create planets for this player
                    foreach ($positions as $position) {
                        $this->createPlanetForPlayer($botPlayer, $position);
                        $planetsCreated++;
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("\nError creating player/planets: " . $e->getMessage());
                    continue;
                }
            } else {
                $playersCreated++;
                $planetsCreated += count($positions);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Show results
        $this->info("Universe Population Complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Bot Players Created', number_format($playersCreated)],
                ['Planets Created', number_format($planetsCreated)],
                ['Total Planets (including existing)', number_format($existingPlanets + $planetsCreated)],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Get the list of galaxies to populate
     */
    private function getGalaxiesToPopulate(int $totalGalaxies): array
    {
        $galaxiesOption = $this->option('galaxies');

        if (empty($galaxiesOption)) {
            // Populate all galaxies
            return range(1, $totalGalaxies);
        }

        $galaxies = [];
        foreach ($galaxiesOption as $galaxy) {
            $galaxy = (int)$galaxy;
            if ($galaxy < 1 || $galaxy > $totalGalaxies) {
                $this->warn("Skipping invalid galaxy number: {$galaxy}");
                continue;
            }
            $galaxies[] = $galaxy;
        }

        return array_unique($galaxies);
    }

    /**
     * Get density range based on density option
     */
    private function getDensityRange(string $density): array
    {
        return match ($density) {
            'low' => ['min' => 0.20, 'max' => 0.30, 'target' => 0.25],
            'medium' => ['min' => 0.40, 'max' => 0.60, 'target' => 0.50],
            'high' => ['min' => 0.70, 'max' => 0.85, 'target' => 0.77],
            default => ['min' => 0.40, 'max' => 0.60, 'target' => 0.50],
        };
    }

    /**
     * Generate pool of available positions
     */
    private function generateAvailablePositions(array $galaxies, int $existingPlanets): array
    {
        $positions = [];

        // Get existing planet positions to exclude
        $existingPositions = Planet::whereIn('galaxy', $galaxies)
            ->where('planet_type', PlanetType::Planet)
            ->get(['galaxy', 'system', 'planet'])
            ->map(fn($p) => "{$p->galaxy}:{$p->system}:{$p->planet}")
            ->toArray();

        $existingSet = array_flip($existingPositions);

        // Generate all possible positions, focusing on habitable zone (4-12)
        $preferredPositions = range(4, 12); // Most habitable
        $otherPositions = array_merge(range(1, 3), range(13, 15));

        foreach ($galaxies as $galaxy) {
            for ($system = 1; $system <= 499; $system++) {
                // Add preferred positions first
                foreach ($preferredPositions as $position) {
                    $key = "{$galaxy}:{$system}:{$position}";
                    if (!isset($existingSet[$key])) {
                        $positions[] = compact('galaxy', 'system', 'position');
                    }
                }

                // Add other positions
                foreach ($otherPositions as $position) {
                    $key = "{$galaxy}:{$system}:{$position}";
                    if (!isset($existingSet[$key])) {
                        $positions[] = compact('galaxy', 'system', 'position');
                    }
                }
            }
        }

        return $positions;
    }

    /**
     * Group positions into player assignments
     */
    private function groupPositionsByPlayers(array $positions, int $minPlanets, int $maxPlanets): array
    {
        $playerGroups = [];
        $currentGroup = [];

        foreach ($positions as $position) {
            $currentGroup[] = $position;

            // Randomly decide when to create a new player
            if (count($currentGroup) >= $minPlanets) {
                $shouldCreatePlayer = count($currentGroup) >= $maxPlanets ||
                                     (count($currentGroup) < $maxPlanets && rand(1, 100) > 60);

                if ($shouldCreatePlayer) {
                    $playerGroups[] = $currentGroup;
                    $currentGroup = [];
                }
            }
        }

        // Add remaining positions as a player if any
        if (!empty($currentGroup) && count($currentGroup) >= $minPlanets) {
            $playerGroups[] = $currentGroup;
        }

        return $playerGroups;
    }

    /**
     * Create a bot player
     */
    private function createBotPlayer(): User
    {
        $username = $this->generateBotUsername();
        $email = strtolower($username) . '@bot.ogamex.local';

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->password = bcrypt(bin2hex(random_bytes(32))); // Random secure password
        $user->lang = 'en'; // Default language
        $user->save();

        // Create initial player tech record
        $tech = new UserTech();
        $tech->user_id = $user->id;
        $tech->save();

        return $user;
    }

    /**
     * Generate a bot username
     */
    private function generateBotUsername(): string
    {
        $prefixes = ['Commander', 'Admiral', 'Captain', 'Lord', 'Baron', 'Emperor', 'Duke', 'General'];
        $suffixes = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Omega', 'Prime', 'Nova', 'Stellar'];
        $numbers = rand(100, 9999);

        return $prefixes[array_rand($prefixes)] . $suffixes[array_rand($suffixes)] . $numbers;
    }

    /**
     * Create a planet for a player at a specific position
     */
    private function createPlanetForPlayer(User $user, array $position): void
    {
        $playerService = $this->playerServiceFactory->make($user->id);
        $coordinate = new Coordinate($position['galaxy'], $position['system'], $position['position']);

        // Create planet at specific coordinates
        // Note: Using createAdditionalPlanetForPlayer for all planets since we want to control exact coordinates
        // The method createInitialPlanetForPlayer auto-determines position which we don't want here
        $this->planetServiceFactory->createAdditionalPlanetForPlayer($playerService, $coordinate);
    }
}
