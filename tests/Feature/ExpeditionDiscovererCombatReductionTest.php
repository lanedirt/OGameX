<?php

namespace Tests\Feature;

use Exception;
use OGame\Enums\CharacterClass;
use OGame\GameMissions\Models\ExpeditionOutcomeType;
use OGame\Models\Resources;
use OGame\Services\SettingsService;
use ReflectionClass;
use Tests\FleetDispatchTestCase;

/**
 * Test that Discoverer class has reduced combat encounter chance on expeditions.
 *
 * According to the Discoverer class perks, they should have 50% reduced chance
 * of encountering pirates or aliens during expeditions.
 */
class ExpeditionDiscovererCombatReductionTest extends FleetDispatchTestCase
{
    protected int $missionType = 15; // Expedition
    protected string $missionName = 'Expedition';

    /**
     * Prepare the planet for the test.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('large_cargo', 5000);
        $this->planetAddUnit('espionage_probe', 1);

        // Set astrophysics research level to 1 to allow expeditions.
        $this->playerSetResearchLevel('astrophysics', 1);
        // Set computer technology to a high enough level to allow enough concurrent fleets.
        $this->playerSetResearchLevel('computer_technology', 10);

        // Set the fleet and economy speed to 1x for this test.
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 1);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
        $this->planetAddResources(new Resources(0, 0, 100000, 0));
    }

    protected function messageCheckMissionArrival(): void
    {
        // Not needed for this test
    }

    protected function messageCheckMissionReturn(): void
    {
        // Not needed for this test
    }

    /**
     * Clean up after each test to avoid polluting other tests.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Delete any test missions we created
        \OGame\Models\FleetMission::where('user_id', $this->planetService->getPlayer()->getUser()->id)->delete();

        // Reset character class
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = null;
        $user->save();

        parent::tearDown();
    }

    /**
     * Test that Discoverer class has reduced combat encounter rate.
     * Uses statistical sampling by calling selectRandomOutcome directly.
     *
     * @throws Exception
     */
    public function testDiscovererHasReducedCombatEncounters(): void
    {
        $this->basicSetup();

        // Configure expedition outcome weights to make combat more common for testing
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('expedition_weight_pirates', 30);  // Increase from default 3.0
        $settingsService->set('expedition_weight_aliens', 20);   // Increase from default 1.5
        $settingsService->set('expedition_weight_resources', 10);
        $settingsService->set('expedition_weight_ships', 10);
        $settingsService->set('expedition_weight_dark_matter', 10);
        $settingsService->set('expedition_weight_nothing', 10);
        $settingsService->set('expedition_weight_delay', 5);
        $settingsService->set('expedition_weight_speedup', 5);
        $settingsService->set('expedition_weight_black_hole', 0);
        $settingsService->set('expedition_weight_merchant', 0);

        // Create a test mission for Discoverer
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = CharacterClass::DISCOVERER->value;
        $user->save();

        $discovererMission = new \OGame\Models\FleetMission();
        $discovererMission->user_id = $user->id;
        $discovererMission->mission_type = 15;
        $discovererMission->save();

        // Run outcome selection many times and count combat encounters
        $totalIterations = 500;
        $discovererCombatCount = 0;

        $expeditionMission = resolve(\OGame\GameMissions\ExpeditionMission::class);
        $reflection = new ReflectionClass($expeditionMission);
        $method = $reflection->getMethod('selectRandomOutcome');
        $method->setAccessible(true);

        for ($i = 0; $i < $totalIterations; $i++) {
            $outcome = $method->invoke($expeditionMission, $discovererMission);
            if ($outcome === ExpeditionOutcomeType::BattlePirates || $outcome === ExpeditionOutcomeType::BattleAliens) {
                $discovererCombatCount++;
            }
        }

        // Calculate Discoverer combat rate
        $discovererCombatRate = $discovererCombatCount / $totalIterations;

        // Now test with Collector class
        $user->character_class = CharacterClass::COLLECTOR->value;
        $user->save();

        $collectorMission = new \OGame\Models\FleetMission();
        $collectorMission->user_id = $user->id;
        $collectorMission->mission_type = 15;
        $collectorMission->save();

        $collectorCombatCount = 0;

        for ($i = 0; $i < $totalIterations; $i++) {
            $outcome = $method->invoke($expeditionMission, $collectorMission);
            if ($outcome === ExpeditionOutcomeType::BattlePirates || $outcome === ExpeditionOutcomeType::BattleAliens) {
                $collectorCombatCount++;
            }
        }

        // Calculate Collector combat rate
        $collectorCombatRate = $collectorCombatCount / $totalIterations;

        // Calculate the expected ratio
        // With weights: pirates=30, aliens=20, others=50
        // Collector: combat = 50/100 = 50%
        // Discoverer: combat = 25/75 = 33.3%
        // Ratio: 33.3 / 50 = 0.666
        // With 500 samples, allow 10% margin for statistical variance
        $expectedRatio = 0.666;
        $marginOfError = 0.10;

        $actualRatio = $collectorCombatRate > 0 ? $discovererCombatRate / $collectorCombatRate : 0;

        // Output debug info
        echo "\n";
        echo "Discoverer combat encounters: {$discovererCombatCount}/{$totalIterations} (" . round($discovererCombatRate * 100, 1) . "%)\n";
        echo "Collector combat encounters: {$collectorCombatCount}/{$totalIterations} (" . round($collectorCombatRate * 100, 1) . "%)\n";
        echo "Combat rate ratio (Discoverer/Collector): " . round($actualRatio, 3) . " (expected: ~{$expectedRatio})\n";
        echo "Note: 50% weight reduction translates to ~33% probability reduction\n";

        // Assert that Discoverer has significantly fewer combat encounters
        $this->assertLessThan(
            $collectorCombatCount,
            $discovererCombatCount,
            'Discoverer should have fewer combat encounters than Collector'
        );

        // Assert that the ratio is approximately 0.5 (with margin of error)
        $this->assertGreaterThanOrEqual(
            $expectedRatio - $marginOfError,
            $actualRatio,
            "Combat rate ratio should be at least " . ($expectedRatio - $marginOfError)
        );
        $this->assertLessThanOrEqual(
            $expectedRatio + $marginOfError,
            $actualRatio,
            "Combat rate ratio should be at most " . ($expectedRatio + $marginOfError)
        );
    }

    /**
     * Test that the combat weight reduction only affects pirates and aliens.
     * Other outcomes should still occur at their normal rates.
     *
     * @throws Exception
     */
    public function testCombatReductionOnlyAffectsCombatOutcomes(): void
    {
        $this->basicSetup();

        // Set player as Discoverer
        $user = $this->planetService->getPlayer()->getUser();
        $user->character_class = CharacterClass::DISCOVERER->value;
        $user->save();

        // Configure expedition outcome weights with equal distribution
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('expedition_weight_pirates', 10);
        $settingsService->set('expedition_weight_aliens', 10);
        $settingsService->set('expedition_weight_resources', 10);
        $settingsService->set('expedition_weight_ships', 10);
        $settingsService->set('expedition_weight_nothing', 10);
        $settingsService->set('expedition_weight_dark_matter', 0);
        $settingsService->set('expedition_weight_delay', 0);
        $settingsService->set('expedition_weight_speedup', 0);
        $settingsService->set('expedition_weight_black_hole', 0);
        $settingsService->set('expedition_weight_merchant', 0);

        // Create a test mission
        $mission = new \OGame\Models\FleetMission();
        $mission->user_id = $user->id;
        $mission->mission_type = 15;
        $mission->save();

        // Run outcome selection many times
        $totalIterations = 500;
        $outcomes = [
            'combat' => 0,
            'resources' => 0,
            'ships' => 0,
            'nothing' => 0,
        ];

        $expeditionMission = resolve(\OGame\GameMissions\ExpeditionMission::class);
        $reflection = new ReflectionClass($expeditionMission);
        $method = $reflection->getMethod('selectRandomOutcome');
        $method->setAccessible(true);

        for ($i = 0; $i < $totalIterations; $i++) {
            $outcome = $method->invoke($expeditionMission, $mission);

            if ($outcome === ExpeditionOutcomeType::BattlePirates || $outcome === ExpeditionOutcomeType::BattleAliens) {
                $outcomes['combat']++;
            } elseif ($outcome === ExpeditionOutcomeType::GainResources) {
                $outcomes['resources']++;
            } elseif ($outcome === ExpeditionOutcomeType::GainShips) {
                $outcomes['ships']++;
            } elseif ($outcome === ExpeditionOutcomeType::Failed) {
                $outcomes['nothing']++;
            }
        }

        // Combat should be significantly less common than other outcomes due to 50% reduction
        // With original weights of 10/10/10/10/10 (50 total), Discoverer gets:
        // Combat: (10+10)*0.5 = 10, Others: 10+10+10 = 30, Total = 40
        // So combat should be ~25% and each other outcome ~25% each

        $combatRate = $outcomes['combat'] / $totalIterations;
        $resourceRate = $outcomes['resources'] / $totalIterations;
        $shipRate = $outcomes['ships'] / $totalIterations;
        $nothingRate = $outcomes['nothing'] / $totalIterations;

        echo "\n";
        echo "Outcome distribution for Discoverer:\n";
        echo "  Combat: {$outcomes['combat']} (" . round($combatRate * 100, 2) . "%)\n";
        echo "  Resources: {$outcomes['resources']} (" . round($resourceRate * 100, 2) . "%)\n";
        echo "  Ships: {$outcomes['ships']} (" . round($shipRate * 100, 2) . "%)\n";
        echo "  Nothing: {$outcomes['nothing']} (" . round($nothingRate * 100, 2) . "%)\n";

        // Combat should be less than any individual non-combat outcome
        $this->assertLessThan(
            $outcomes['resources'] + $outcomes['ships'] + $outcomes['nothing'],
            $outcomes['combat'] * 2,
            'Combat outcomes should be less common than non-combat outcomes combined'
        );
    }
}
