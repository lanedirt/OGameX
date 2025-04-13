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
 * This command is used to test the performance of a specific battle engine with specified fleets.
 *
 * Use like this to test PHP
 * ---
 * php artisan test:battle-engine-performance php --fleet='{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}'
 * ---
 *
 * Use like this to test Rust
 * ---
 * php artisan test:battle-engine-performance rust --fleet='{"attacker": {"light_fighter": 1667}, "defender": {"rocket_launcher": 1667}}'
 * ---
 */
class TestBattleEnginePerformance extends TestCommand
{
    protected $signature = 'test:battle-engine-performance
        {engine : The battle engine to test (php/rust)}
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
        $engine = $this->argument('engine');

        if (!in_array($engine, ['php', 'rust'])) {
            $this->error('Invalid engine specified. Use "php" or "rust"');
            return 1;
        }

        return $this->runSingleEngineTest($engine, $fleets);
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
        $this->displayMetrics($engine, $battleResult);

        return 0;
    }

    /**
     * Create a battle engine instance.
     *
     * @param string $engine The engine to test.
     * @param UnitCollection $attackerFleet The attacker fleet.
     * @return BattleEngine The battle engine instance.
     */
    private function createBattleEngine(string $engine, UnitCollection $attackerFleet): BattleEngine
    {
        // Resolve settings service.
        $settingsService = resolve(SettingsService::class);

        return $engine === 'php'
            ? new PhpBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService)
            : new RustBattleEngine($attackerFleet, $this->playerService, $this->currentPlanetService, $settingsService);
    }

    /**
     * Display the battle metrics.
     *
     * @param string $engine The engine used for the battle.
     * @param BattleResult $battleResult The battle result.
     */
    private function displayMetrics(string $engine, BattleResult $battleResult): void
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

        $this->info(string: "Peak PHP memory usage: " . number_format($peakMemoryUsage, 2) . "MB");

        if ($engine === 'rust') {
            $this->info("Note: Rust memory usage can't be measured reliably from PHP. Debug Rust app manually to get indication of Rust memory usage.");
        }

        $this->info("\n");
    }

    /**
     * Parse the fleet JSON string into an array of fleets.
     *
     * @param string $fleetJson The fleet JSON string.
     * @return array<string, UnitCollection>|null The fleets.
     */
    private function parseFleets(string $fleetJson): array|null
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
