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
use InvalidArgumentException;

/**
 * This command is used to test the performance of the battle engine with specified fleets.
 * It can be used to test the performance of a single engine or to compare the performance of multiple engines.
 */
class TestBattleEnginePerformance extends TestCommand
{
    protected $signature = 'test:battle-engine-performance
        {engine=all : The battle engine to test (php/rust). If no option is provided, all engines will be tested and compared.}
        {--fleet= : JSON string defining attacker and defender fleets}';
    protected $description = 'Test battle engine performance with specified fleets';

    protected string $email = 'battleengineperformance@test.com';
    private float $startTime;
    private int $startMemory;
    private int $peakMemory;

    /**
     * Main entry point for the command.
     */
    public function handle(): int
    {
        // Check for fleet option
        if (!$this->option('fleet') || !$this->parseFleets($this->option('fleet'))) {
            $this->error('Specify valid --fleet option in JSON format like this: --fleet=\'{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}\'');
            return 1;
        }

        // Set up the test environment
        parent::setup();

        $fleets = $this->parseFleets($this->option('fleet'));

        // Original single engine test logic
        $engine = $this->argument('engine');
        if ($engine === 'all') {
            return $this->runComparisonTest($fleets);
        }

        if (!in_array($engine, ['php', 'rust'])) {
            $this->error('Invalid engine specified. Use "php" or "rust"');
            return 1;
        }

        return $this->runSingleEngineTest($engine, $fleets);
    }

    /**
     * Run a comparison test between PHP and Rust battle engines.
     */
    private function runComparisonTest(array $units): int
    {
        $engines = ['php', 'rust'];
        $results = [];

        // Create fleet based on input
        $this->setupBattle($units['defender']);
        $attackerFleet = $units['attacker'];

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

            // Show Rust FFI memory as peak memory if available
            if ($engine === 'rust' && $battleResult->getMemoryUsagePeak() > 0) {
                $peakMemoryDuringExecution = ($battleResult->getMemoryUsagePeak() * 1024 * 1024); // Convert MB to bytes
            }

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

    /**
     * Display the comparison results between PHP and Rust battle engines.
     */
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

    /**
     * Print a table row for comparison results.
     */
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

    /**
     * Format the difference between two values.
     */
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

    /**
     * Run a single engine test with specified fleets.
     */
    private function runSingleEngineTest(string $engine, array $fleets): int
    {
        // Set static time
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 0, 0, 0));

        // Add resources and tech levels
        $this->currentPlanetService->addResources(new Resources(1000000, 1000000, 1000000, 0));
        $this->playerService->setResearchLevel('weapon_technology', 10);
        $this->playerService->setResearchLevel('shielding_technology', 10);
        $this->playerService->setResearchLevel('armor_technology', 10);

        // Set up defender planet with provided units
        foreach ($fleets['defender']->units as $unit) {
            $this->currentPlanetService->addUnit($unit->unitObject->machine_name, $unit->amount);
        }

        // Force garbage collection before starting measurements
        gc_collect_cycles();

        // Start tracking metrics
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        $this->peakMemory = $this->startMemory;

        // Create attacker fleet
        $attackerFleet = $fleets['attacker'];
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

    /**
     * Set up the battle environment.
     */
    private function setupBattle(UnitCollection $defenderFleet): void
    {
        // Set static time
        Carbon::setTestNow(Carbon::create(2024, 1, 1, 0, 0, 0));

        // Add resources and tech levels
        $this->currentPlanetService->addResources(new Resources(1000000, 1000000, 1000000, 0));
        $this->playerService->setResearchLevel('weapon_technology', 10);
        $this->playerService->setResearchLevel('shielding_technology', 10);
        $this->playerService->setResearchLevel('armor_technology', 10);

        // Set up defender planet with provided units
        foreach ($defenderFleet->units as $unit) {
            $this->currentPlanetService->addUnit($unit->unitObject->machine_name, $unit->amount);
        }
    }

    /**
     * Create a battle engine instance.
     */
    private function createBattleEngine(string $engine, UnitCollection $attackerFleet)
    {
        // Resolve settings service.
        $settingsService = resolve(SettingsService::class);

        return $engine === 'php'
            ? new PhpBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService)
            : new RustBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService);
    }

    /**
     * Display the battle metrics.
     */
    private function displayMetrics(BattleResult $battleResult): void
    {
        // Force garbage collection before final measurements
        gc_collect_cycles();

        $endTime = microtime(true);
        $peakMemoryDuringExecution = memory_get_peak_usage(true);

        $executionTime = ($endTime - $this->startTime) * 1000; // Convert to milliseconds
        $peakMemoryUsage = ($peakMemoryDuringExecution - $this->startMemory) / 1024 / 1024; // Convert to MB

        $this->info("\n========================================================");
        $this->info("Battle Statistics:");
        $this->info("========================================================");
        $this->info("Attacker initial fleet size: " . number_format($battleResult->attackerUnitsStart->getAmount()));
        $this->info("Defender initial fleet size: " . number_format($battleResult->defenderUnitsStart->getAmount()));
        $this->info("Number of rounds: " . number_format(count($battleResult->rounds)));
        $this->info("Attacker final fleet size: " . number_format($battleResult->attackerUnitsResult->getAmount()));
        $this->info("Defender final fleet size: " . number_format($battleResult->defenderUnitsResult->getAmount()));

        $this->info("\n========================================================");
        $this->info("Battle Engine Performance Metrics:");
        $this->info("========================================================");
        $this->info("Execution time: " . number_format($executionTime, 2) . "ms");
        $this->info("Peak PHP memory usage: " . number_format($peakMemoryUsage, 2) . "MB");

        if ($battleResult->getMemoryUsagePeak() > 0) {
            $this->info("Peak Rust (FFI) memory usage: " . number_format($battleResult->getMemoryUsagePeak(), 2) . "MB");
        }

        $this->info("\n");
    }

    /**
     * Parse the fleet JSON string into an array of fleets.
     */
    private function parseFleets(string $fleetJson): ?array
    {
        try {
            $fleets = json_decode($fleetJson, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($fleets['attacker']) || !isset($fleets['defender'])) {
                throw new InvalidArgumentException('Fleet JSON must contain both "attacker" and "defender" arrays');
            }

            return [
                'attacker' => $this->createPresetFleet($fleets['attacker']),
                'defender' => $this->createPresetFleet($fleets['defender'])
            ];
        } catch (Exception $e) {
            $this->error('Invalid fleet JSON: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a preset fleet from an array of units.
     */
    private function createPresetFleet(array $units): UnitCollection
    {
        $fleet = new UnitCollection();

        foreach ($units as $unitType => $amount) {
            $unit = ObjectService::getUnitObjectByMachineName($unitType);
            if ($unit) {
                $fleet->addUnit($unit, $amount);
            } else {
                $this->warn("Unknown unit type: $unitType");
            }
        }

        return $fleet;
    }
}
