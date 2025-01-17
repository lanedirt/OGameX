<?php

namespace OGame\Console\Commands\Tests;

use Illuminate\Support\Carbon;
use OGame\GameMissions\BattleEngine\Models\BattleResult;
use OGame\GameMissions\BattleEngine\BattleEngine;
use OGame\GameMissions\BattleEngine\PhpBattleEngine;
use OGame\GameMissions\BattleEngine\RustBattleEngine;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Services\ObjectService;
use OGame\Models\Resources;
use OGame\Services\SettingsService;
use InvalidArgumentException;
use Exception;

/**
 * This command is used to test the performance of the battle engine with specified fleets.
 * It can be used to test the performance of a single engine or to compare the performance of multiple engines.
 *
 * Use like this to test a single engine (PHP):
 * ---
 * php artisan test:battle-engine-performance php --fleet='{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}'
 * ---
 *
 * Use like this to test a single engine (Rust):
 * ---
 * php artisan test:battle-engine-performance rust --fleet='{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}'
 * ---
 *
 * Use like this to test all engines:
 * ---
 * php artisan test:battle-engine-performance --fleet='{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}'
 * ---
 */
class TestBattleEnginePerformance extends TestCommand
{
    protected $signature = 'test:battle-engine-performance
        {engine=all : The battle engine to test (php/rust). If no option is provided, all engines will be tested and compared.}
        {--fleet= : JSON string defining attacker and defender fleets}';
    protected $description = 'Test battle engine performance with specified fleets';

    protected string $email = 'battleengineperformance@test.com';
    private float $startTime;

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
     *
     * @param array<string, UnitCollection> $fleets The fleets to test.
     * @return int The exit code.
     */
    private function runComparisonTest(array $fleets): int
    {
        $engines = ['php', 'rust'];
        $results = [];

        foreach ($engines as $engine) {
            // Force garbage collection before each test
            gc_collect_cycles();

            // Create fleet based on input
            $this->setupBattle($fleets['defender']);
            $attackerFleet = $fleets['attacker'];

            // Start tracking metrics
            $this->startTime = microtime(true);

            // Run battle simulation
            $battleEngine = $this->createBattleEngine($engine, $attackerFleet);
            $battleResult = $battleEngine->simulateBattle();

            // Calculate metrics
            $endTime = microtime(true);

            // Get the appropriate peak memory value based on engine
            if ($engine === 'rust') {
                $peakMemoryDuringExecution = $battleResult->memoryUsagePeak * 1024; // Convert KB to bytes
            } else {
                $peakMemoryDuringExecution = memory_get_peak_usage(true);
            }

            $results[$engine] = [
                'time' => ($endTime - $this->startTime) * 1000,
                'peak_memory' => $peakMemoryDuringExecution,
                'rounds' => count($battleResult->rounds),
                'units' => [
                    'attacker_start' => $battleResult->attackerUnitsStart->getAmount(),
                    'defender_start' => $battleResult->defenderUnitsStart->getAmount(),
                    'attacker_end' => $battleResult->attackerUnitsResult->getAmount(),
                    'defender_end' => $battleResult->defenderUnitsResult->getAmount(),
                ]
            ];

            // Reset PHP's peak memory tracking
            gc_collect_cycles();
        }

        $this->displayComparisonResults($results);
        return 0;
    }

    /**
     * Display the comparison results between PHP and Rust battle engines.
     *
     * @param array<string, array<string, mixed>> $results The results to display.
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

        // Format peak memory
        $peakMemDiff = isset($phpMetrics['peak_memory'], $rustMetrics['peak_memory'])
            ? $rustMetrics['peak_memory'] - $phpMetrics['peak_memory']
            : 0;
        $this->printTableRow(
            "Peak Memory",
            number_format($phpMetrics['peak_memory'] / 1024 / 1024, 2) . "MB",
            number_format($rustMetrics['peak_memory'] / 1024 / 1024, 2) . "MB",
            $this->formatDifference($peakMemDiff / 1024 / 1024, "MB", true)
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
     *
     * @param string $engine The engine to test.
     * @param array<string, UnitCollection> $fleets The fleets to test.
     * @return int The exit code.
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

        // Remove all units from the planet
        $this->currentPlanetService->removeUnits($this->currentPlanetService->getShipUnits(), true);
        $this->currentPlanetService->removeUnits($this->currentPlanetService->getDefenseUnits(), true);

        // Set up defender planet with provided units
        foreach ($defenderFleet->units as $unit) {
            $this->currentPlanetService->addUnit($unit->unitObject->machine_name, $unit->amount);
        }
    }

    /**
     * Create a battle engine instance.
     *
     * @param string $engine The engine to test.
     * @param UnitCollection $attackerFleet The attacker fleet.
     * @return BattleEngine The battle engine instance.
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
        $peakMemoryUsage = $peakMemoryDuringExecution / 1024 / 1024; // Convert bytes to MB

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

        if ($battleResult->memoryUsagePeak > 0) {
            $this->info("Peak Rust (FFI) memory usage: " . number_format($battleResult->memoryUsagePeak / 1024, 2) . "MB");
        }

        $this->info("\n");
    }

    /**
     * Parse the fleet JSON string into an array of fleets.
     *
     * @param string $fleetJson The fleet JSON string.
     * @return array<string, UnitCollection>|null The fleets.
     */
    private function parseFleets(string $fleetJson): ?array
    {
        try {
            $fleets = json_decode($fleetJson, true, 512, JSON_THROW_ON_ERROR);

            if (!isset($fleets['attacker']) || !isset($fleets['defender'])) {
                throw new InvalidArgumentException('Fleet JSON must contain both "attacker" and "defender" arrays');
            }

            return [
                'attacker' => $this->createUnitCollection($fleets['attacker']),
                'defender' => $this->createUnitCollection($fleets['defender'])
            ];
        } catch (Exception $e) {
            $this->error('Invalid fleet JSON: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a unit collection from an array of units.
     *
     * @param array<string, int> $units The units to create the fleet from.
     * @return UnitCollection The created fleet.
     */
    private function createUnitCollection(array $units): UnitCollection
    {
        $fleet = new UnitCollection();

        foreach ($units as $unitType => $amount) {
            $unit = ObjectService::getUnitObjectByMachineName($unitType);
            $fleet->addUnit($unit, $amount);
        }

        return $fleet;
    }
}
