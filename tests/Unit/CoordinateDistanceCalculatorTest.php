<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Models\User;
use OGame\Services\CoordinateDistanceCalculator;
use OGame\Services\SettingsService;
use Tests\TestCase;

class CoordinateDistanceCalculatorTest extends TestCase
{
    private CoordinateDistanceCalculator $calculator;
    private SettingsService $settingsService;

    /** @var array<int, int> Planet IDs created during tests, deleted in tearDown. */
    private array $createdPlanetIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = app(SettingsService::class);
        $this->calculator = new CoordinateDistanceCalculator($this->settingsService);
    }

    protected function tearDown(): void
    {
        if (!empty($this->createdPlanetIds)) {
            Planet::whereIn('id', $this->createdPlanetIds)->delete();
        }

        parent::tearDown();
    }

    /**
     * Test that empty systems calculation returns 0 when setting is disabled.
     */
    public function testEmptySystemsDisabled(): void
    {
        // Ensure setting is disabled
        $this->settingsService->set('ignore_empty_systems_on', 0);

        $from = new Coordinate(1, 1, 1);
        $to = new Coordinate(1, 10, 1);

        $result = $this->calculator->getNumEmptySystems($from, $to);

        $this->assertEquals(0, $result);
    }

    /**
     * Test that inactive systems calculation returns 0 when setting is disabled.
     */
    public function testInactiveSystemsDisabled(): void
    {
        // Ensure setting is disabled
        $this->settingsService->set('ignore_inactive_systems_on', 0);

        $from = new Coordinate(1, 1, 1);
        $to = new Coordinate(1, 10, 1);

        $result = $this->calculator->getNumInactiveSystems($from, $to);

        $this->assertEquals(0, $result);
    }

    /**
     * Test that empty systems are calculated correctly when enabled.
     */
    public function testEmptySystemsEnabled(): void
    {
        // Enable setting
        $this->settingsService->set('ignore_empty_systems_on', 1);

        // Create a user
        $user = User::factory()->create();

        // Find a starting system across all 9 galaxies where 10 consecutive systems are all empty,
        // to avoid unique constraint violations with planets created by other tests.
        $galaxy = null;
        $startSystem = null;
        for ($g = 1; $g <= 9 && $galaxy === null; $g++) {
            for ($start = 1; $start <= 490; $start++) {
                $existingCount = DB::table('planets')
                    ->where('galaxy', $g)
                    ->whereBetween('system', [$start, $start + 9])
                    ->count();
                if ($existingCount === 0) {
                    $galaxy = $g;
                    $startSystem = $start;
                    break;
                }
            }
        }

        if ($galaxy === null || $startSystem === null) {
            $this->fail('Could not find 10 consecutive empty systems in any galaxy for testing.');
        }

        // Create planets in systems startSystem, startSystem+2, startSystem+4 (leaving others empty)
        $p1 = Planet::factory()->create(['user_id' => $user->id, 'galaxy' => $galaxy, 'system' => $startSystem, 'planet' => 1]);
        $p2 = Planet::factory()->create(['user_id' => $user->id, 'galaxy' => $galaxy, 'system' => $startSystem + 2, 'planet' => 1]);
        $p3 = Planet::factory()->create(['user_id' => $user->id, 'galaxy' => $galaxy, 'system' => $startSystem + 4, 'planet' => 1]);
        $this->createdPlanetIds = [$p1->id, $p2->id, $p3->id];

        $from = new Coordinate($galaxy, $startSystem, 1);
        $to = new Coordinate($galaxy, $startSystem + 9, 1);

        $result = $this->calculator->getNumEmptySystems($from, $to);

        // Systems startSystem to startSystem+9: 3 occupied (startSystem, startSystem+2, startSystem+4), 10 total
        // Empty = 10 - 3 = 7
        $this->assertEquals(7, $result);
    }

    /**
     * Test that calculations return 0 for different galaxies.
     */
    public function testDifferentGalaxies(): void
    {
        $this->settingsService->set('ignore_empty_systems_on', 1);
        $this->settingsService->set('ignore_inactive_systems_on', 1);

        $from = new Coordinate(1, 1, 1);
        $to = new Coordinate(2, 1, 1);

        $emptyResult = $this->calculator->getNumEmptySystems($from, $to);
        $inactiveResult = $this->calculator->getNumInactiveSystems($from, $to);

        $this->assertEquals(0, $emptyResult);
        $this->assertEquals(0, $inactiveResult);
    }
}
