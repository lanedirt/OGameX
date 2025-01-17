<?php

namespace OGame\Console\Commands\Tests;

use Illuminate\Support\Carbon;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use OGame\Models\Resources;
use OGame\Services\SettingsService;

class TestBattleEnginePerformance extends TestCommand
{
    protected $signature = 'test:battle-engine-performance
        {units : Total number of units (e.g. 100000)}
        {engine=all : The battle engine to test (php/rust). If no option is provided, all engines will be tested and compared.}';
    protected $description = 'Test battle engine performance with specified number of units';

    protected string $email = 'battleengineperformance@test.com';
    private float $startTime;
    private int $startMemory;
    private int $peakMemory;

    /**
     * Main entry point for the command.
     */
    public function handle(): int
    {
        // Set up the test environment
        parent::setup();

        // Parse arguments
        $totalUnits = (int)$this->argument('units');

        // Original single engine test logic
        $engine = $this->argument('engine');
        if ($engine === 'all') {
            return $this->runComparisonTest($totalUnits);
        }

        if (!in_array($engine, ['php', 'rust'])) {
            $this->error('Invalid engine specified. Use "php" or "rust"');
            return 1;
        }

        return $this->runSingleEngineTest($engine, $totalUnits);
    }

    private function runComparisonTest(int $totalUnits): int
    {
        $engines = ['php', 'rust'];
        $results = [];

        // Create the same random fleet for all tests
        $this->setupBattle($totalUnits);
        $attackerFleet = $this->createRandomFleet($totalUnits / 2, false);

        foreach ($engines as $engine) {
            // Force garbage collection before starting
            gc_collect_cycles();

            // Start tracking metrics
            $this->startTime = microtime(true);
            $this->startMemory = memory_get_usage(true);
            $this->peakMemory = $this->startMemory;

            // Run battle simulation
            $battleEngine = $this->createBattleEngine($engine, $attackerFleet);
            $battleResult = $battleEngine->simulateBattle();

            // Calculate metrics
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            $peakMemoryDuringExecution = memory_get_peak_usage(true);

            $results[$engine] = [
                'time' => ($endTime - $this->startTime) * 1000,
                'memory' => ($endMemory - $this->startMemory) / 1024,
                'peak_memory' => ($peakMemoryDuringExecution - $this->startMemory) / 1024,
                'rounds' => count($battleResult->rounds),
                'units' => [
                    'attacker_start' => $battleResult->attackerUnitsStart->getAmount(),
                    'defender_start' => $battleResult->defenderUnitsStart->getAmount(),
                    'attacker_end' => $battleResult->attackerUnitsResult->getAmount(),
                    'defender_end' => $battleResult->defenderUnitsResult->getAmount(),
                ]
            ];
        }

        $this->displayComparisonResults($results);
        return 0;
    }

    private function displayComparisonResults(array $results): void
    {
        $this->info("\n╔════════════════════════════════════════════════════════════════════╗");
        $this->info("║                 Battle Engine Comparison Results                   ║");
        $this->info("╠══════════════════╦═══════════════╦═══════════════╦═════════════════╣");
        $this->info("║     Metric       ║  PHP Engine   ║  Rust Engine  ║  Difference     ║");
        $this->info("╠══════════════════╬═══════════════╬═══════════════╬═════════════════╣");

        // Get metrics for comparison
        $phpMetrics = $results['php'] ?? [];
        $rustMetrics = $results['rust'] ?? [];

        // Format execution time
        $timeDiff = isset($phpMetrics['time'], $rustMetrics['time'])
            ? $rustMetrics['time'] - $phpMetrics['time']
            : 0;
        $this->printTableRow(
            "Execution Time",
            number_format($phpMetrics['time'] ?? 0, 2) . "ms",
            number_format($rustMetrics['time'] ?? 0, 2) . "ms",
            $this->formatDifference($timeDiff, "ms", true)
        );

        // Format memory usage
        $memDiff = isset($phpMetrics['memory'], $rustMetrics['memory'])
            ? $rustMetrics['memory'] - $phpMetrics['memory']
            : 0;
        $this->printTableRow(
            "Memory Usage",
            number_format($phpMetrics['memory'] / 1024, 2) . "MB",
            number_format($rustMetrics['memory'] / 1024, 2) . "MB",
            $this->formatDifference($memDiff / 1024, "MB", true)
        );

        // Format peak memory
        $peakMemDiff = isset($phpMetrics['peak_memory'], $rustMetrics['peak_memory'])
            ? $rustMetrics['peak_memory'] - $phpMetrics['peak_memory']
            : 0;
        $this->printTableRow(
            "Peak Memory",
            number_format($phpMetrics['peak_memory'] / 1024, 2) . "MB",
            number_format($rustMetrics['peak_memory'] / 1024, 2) . "MB",
            $this->formatDifference($peakMemDiff / 1024, "MB", true)
        );

        // Format battle rounds
        $roundsDiff = isset($phpMetrics['rounds'], $rustMetrics['rounds'])
            ? $rustMetrics['rounds'] - $phpMetrics['rounds']
            : 0;
        $this->printTableRow(
            "Battle Rounds",
            number_format($phpMetrics['rounds'] ?? 0),
            number_format($rustMetrics['rounds'] ?? 0),
            $this->formatDifference($roundsDiff, "", false)
        );

        $this->info("╚══════════════════╩═══════════════╩═══════════════╩═════════════════╝\n");
    }

    private function printTableRow(string $label, string $php, string $rust, string $diff): void
    {
        $this->info(sprintf(
            "║ %-16s ║ %13s ║ %13s ║ %15s ║",
            $label,
            $php,
            $rust,
            $diff
        ));
    }

    private function formatDifference(float $diff, string $unit, bool $lowerIsBetter): string
    {
        if ($diff == 0) {
            return str_pad("0" . $unit, 15, " ", STR_PAD_LEFT);
        }

        $prefix = $diff > 0 ? "+" : "";
        $color = $diff > 0
            ? ($lowerIsBetter ? "red" : "green")
            : ($lowerIsBetter ? "green" : "red");

        $formattedDiff = $prefix . number_format($diff, 2) . $unit;
        $paddedDiff = str_pad($formattedDiff, 15, " ", STR_PAD_LEFT);

        return "<fg=$color>" . $paddedDiff . "</>";
    }

    private function runSingleEngineTest(string $engine, int $totalUnits): int
    {
        // Set static time
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 0, 0, 0));

        // Add resources and tech levels
        $this->currentPlanetService->addResources(new Resources(1000000, 1000000, 1000000, 0));
        $this->playerService->setResearchLevel('weapon_technology', 10);
        $this->playerService->setResearchLevel('shielding_technology', 10);
        $this->playerService->setResearchLevel('armor_technology', 10);

        // Set up defender planet with random units
        $defenderUnits = $this->createRandomFleet($totalUnits / 2, true);
        foreach ($defenderUnits->units as $unit) {
            $this->currentPlanetService->addUnit($unit->unitObject->machine_name, $unit->amount);
        }

        // Force garbage collection before starting measurements
        gc_collect_cycles();

        // Start tracking metrics
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        $this->peakMemory = $this->startMemory;

        // Create attacker fleet
        $attackerFleet = $this->createRandomFleet($totalUnits / 2, false);
        $this->info("\nAttacker (" . number_format($attackerFleet->getAmount()) . ") and defender (" . number_format($this->currentPlanetService->getShipUnits()->getAmount()) . " + " . number_format($this->currentPlanetService->getDefenseUnits()->getAmount()) . ") fleet created");

        // Run battle simulation
        $battleEngine = $this->createBattleEngine($engine, $attackerFleet);
        $this->info("\nBattle engine starting simulation...");

        $battleResult = $battleEngine->simulateBattle();
        $this->info("--> Battle engine finished simulation...");

        // Calculate and display metrics
        $this->displayMetrics($battleResult);

        return 0;
    }

    private function setupBattle(int $totalUnits): void
    {
        // Set static time
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 0, 0, 0));

        // Add resources and tech levels
        $this->currentPlanetService->addResources(new Resources(1000000, 1000000, 1000000, 0));
        $this->playerService->setResearchLevel('weapon_technology', 10);
        $this->playerService->setResearchLevel('shielding_technology', 10);
        $this->playerService->setResearchLevel('armor_technology', 10);

        // Set up defender planet with random units
        $defenderUnits = $this->createRandomFleet($totalUnits / 2, true);
        foreach ($defenderUnits->units as $unit) {
            $this->currentPlanetService->addUnit($unit->unitObject->machine_name, $unit->amount);
        }
    }

    private function createRandomFleet(int $targetUnits, bool $includeDefense): UnitCollection
    {
        $fleet = new UnitCollection();
        $availableUnits = [
            'light_fighter' => 1,
            'heavy_fighter' => 3,
            'cruiser' => 6,
            'battle_ship' => 20,
            'battlecruiser' => 30,
            'bomber' => 50,
            'destroyer' => 60,
            'deathstar' => 200,
            'small_cargo' => 1,
            'large_cargo' => 4,
        ];

        if ($includeDefense) {
            $availableUnits = array_merge($availableUnits, [
                // Defense structures
                'rocket_launcher' => 1,
                'light_laser' => 2,
                'heavy_laser' => 3,
                'gauss_cannon' => 6,
                'ion_cannon' => 5,
                'plasma_turret' => 7,
                'small_shield_dome' => 10,
                'large_shield_dome' => 20
            ]);
        }

        $totalUnitsCreated = 0;
        while ($totalUnitsCreated < $targetUnits) {
            $unitType = array_rand($availableUnits);

            // Calculate how many more actual units we can add
            $remainingUnits = $targetUnits - $totalUnitsCreated;
            // Calculate a random amount between 1 and remaining units, but no more than 20% of target
            $amount = min(
                rand(1, max(1, (int)($targetUnits * 0.2))),
                $remainingUnits
            );

            $fleet->addUnit(ObjectService::getUnitObjectByMachineName($unitType), $amount);
            $totalUnitsCreated += $amount;
        }

        return $fleet;
    }

    private function createBattleEngine(string $engine, UnitCollection $attackerFleet)
    {
        // Resolve settings service.
        $settingsService = resolve(SettingsService::class);

        return $engine === 'php'
            ? new PhpBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService)
            : new RustBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService);
    }

    private function displayMetrics(BattleResult $battleResult): void
    {
        // Force garbage collection before final measurements
        gc_collect_cycles();

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $peakMemoryDuringExecution = memory_get_peak_usage(true);

        $executionTime = ($endTime - $this->startTime) * 1000; // Convert to milliseconds
        $memoryUsage = ($endMemory - $this->startMemory) / 1024 / 1024; // Convert to MB
        $peakMemoryUsage = ($peakMemoryDuringExecution - $this->startMemory) / 1024 / 1024; // Convert to MB

        $this->info("\n--------------------------------");
        $this->info("Battle Statistics:");
        $this->info("--------------------------------");
        $this->info("Attacker initial fleet size: " . number_format($battleResult->attackerUnitsStart->getAmount()));
        $this->info("Defender initial fleet size: " . number_format($battleResult->defenderUnitsStart->getAmount()));
        $this->info("Number of rounds: " . number_format(count($battleResult->rounds)));
        $this->info("Attacker final fleet size: " . number_format($battleResult->attackerUnitsResult->getAmount()));
        $this->info("Defender final fleet size: " . number_format($battleResult->defenderUnitsResult->getAmount()));

        $this->info("\n--------------------------------");
        $this->info("Battle Engine Performance Metrics:");
        $this->info("--------------------------------");
        $this->info("Execution time: " . number_format($executionTime, 2) . "ms");
        $this->info("Memory usage: " . number_format($memoryUsage, 2) . "MB");
        $this->info("Peak memory usage: " . number_format($peakMemoryUsage, 2) . "MB");
        $this->info("\n");
    }
}
