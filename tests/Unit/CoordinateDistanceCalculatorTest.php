<?php

namespace Tests\Unit;

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

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = app(SettingsService::class);
        $this->calculator = new CoordinateDistanceCalculator($this->settingsService);
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

        // Create planets in systems 1, 3, and 5 (leaving 2, 4, 6, 7, 8, 9, 10 empty)
        Planet::factory()->create(['user_id' => $user->id, 'galaxy' => 9, 'system' => 1, 'planet' => 1]);
        Planet::factory()->create(['user_id' => $user->id, 'galaxy' => 9, 'system' => 3, 'planet' => 1]);
        Planet::factory()->create(['user_id' => $user->id, 'galaxy' => 9, 'system' => 5, 'planet' => 1]);

        $from = new Coordinate(9, 1, 1);
        $to = new Coordinate(9, 10, 1);

        $result = $this->calculator->getNumEmptySystems($from, $to);

        // Systems 1-10: occupied are 1, 3, 5 = 3 occupied
        // Total systems = 10 - 1 + 1 = 10
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
