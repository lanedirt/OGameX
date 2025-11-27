<?php

namespace Tests\Unit;

use OGame\Services\JumpGateService;
use OGame\Services\SettingsService;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\UnitTestCase;

/**
 * Unit tests for Jump Gate cooldown calculation formula.
 *
 * Formula: base_time = 60 / fleet_speed (in minutes)
 *          reduction = (level - 1) * 10%
 *          cooldown = base_time - (base_time * reduction)
 *          minimum = 1 minute (60 seconds)
 */
class JumpGateCooldownTest extends UnitTestCase
{
    /**
     * Test cooldown at fleet speed 1 (standard speed).
     * Base time = 60 minutes = 3600 seconds
     */
    public function testCooldownAtFleetSpeed1(): void
    {
        $service = $this->createServiceWithFleetSpeed(1);

        // Level 1: 0% reduction = 3600 seconds
        $this->assertEquals(3600, $service->calculateCooldown(1));

        // Level 2: 10% reduction = 3240 seconds
        $this->assertEquals(3240, $service->calculateCooldown(2));

        // Level 3: 20% reduction = 2880 seconds
        $this->assertEquals(2880, $service->calculateCooldown(3));

        // Level 5: 40% reduction = 2160 seconds
        $this->assertEquals(2160, $service->calculateCooldown(5));

        // Level 10: 90% reduction = 360 seconds
        $this->assertEquals(360, $service->calculateCooldown(10));
    }

    /**
     * Test cooldown at fleet speed 2 (2x speed).
     * Base time = 30 minutes = 1800 seconds
     */
    public function testCooldownAtFleetSpeed2(): void
    {
        $service = $this->createServiceWithFleetSpeed(2);

        // Level 1: 0% reduction = 1800 seconds
        $this->assertEquals(1800, $service->calculateCooldown(1));

        // Level 2: 10% reduction = 1620 seconds
        $this->assertEquals(1620, $service->calculateCooldown(2));

        // Level 5: 40% reduction = 1080 seconds
        $this->assertEquals(1080, $service->calculateCooldown(5));
    }

    /**
     * Test cooldown at fleet speed 10.
     * Base time = 6 minutes = 360 seconds
     * Note: Due to floating point precision, some values may be off by 1 second.
     */
    public function testCooldownAtFleetSpeed10(): void
    {
        $service = $this->createServiceWithFleetSpeed(10);

        // Level 1: 0% reduction = 360 seconds
        $this->assertEquals(360, $service->calculateCooldown(1));

        // Level 2: 10% reduction = 324 seconds
        $this->assertEquals(324, $service->calculateCooldown(2));

        // Level 5: 40% reduction = ~216 seconds (215-216 due to float precision)
        $cooldownLevel5 = $service->calculateCooldown(5);
        $this->assertGreaterThanOrEqual(215, $cooldownLevel5);
        $this->assertLessThanOrEqual(216, $cooldownLevel5);

        // Level 10: 90% reduction = 36 seconds, but minimum is 60
        $this->assertEquals(60, $service->calculateCooldown(10));
    }

    /**
     * Test cooldown at very high fleet speed (500x).
     * Base time = 0.12 minutes = 7.2 seconds, below minimum
     */
    public function testCooldownAtHighFleetSpeed(): void
    {
        $service = $this->createServiceWithFleetSpeed(500);

        // All levels should return minimum (60 seconds)
        $this->assertEquals(60, $service->calculateCooldown(1));
        $this->assertEquals(60, $service->calculateCooldown(5));
        $this->assertEquals(60, $service->calculateCooldown(10));
    }

    /**
     * Test minimum cooldown is enforced (1 minute = 60 seconds).
     */
    public function testMinimumCooldownEnforced(): void
    {
        // At fleet speed 100, base time = 36 seconds (below minimum)
        $service = $this->createServiceWithFleetSpeed(100);

        // Level 1: should be minimum 60 seconds
        $this->assertEquals(60, $service->calculateCooldown(1));

        // Level 10: with 90% reduction would be 3.6 seconds, but minimum is 60
        $this->assertEquals(60, $service->calculateCooldown(10));
    }

    /**
     * Test that higher levels always result in same or lower cooldown.
     */
    public function testHigherLevelReducesOrMaintainsCooldown(): void
    {
        $service = $this->createServiceWithFleetSpeed(1);

        $previousCooldown = PHP_INT_MAX;
        for ($level = 1; $level <= 15; $level++) {
            $cooldown = $service->calculateCooldown($level);
            $this->assertLessThanOrEqual($previousCooldown, $cooldown, "Level $level should have cooldown <= level " . ($level - 1));
            $previousCooldown = $cooldown;
        }
    }

    /**
     * Test reduction percentage calculation.
     * Level 1 = 0%, Level 2 = 10%, Level 11 = 100% (capped by minimum)
     */
    public function testReductionPercentages(): void
    {
        $service = $this->createServiceWithFleetSpeed(1);
        $baseCooldown = 3600; // 60 minutes at speed 1

        // Level 1: 0% reduction
        $this->assertEquals($baseCooldown, $service->calculateCooldown(1));

        // Level 6: 50% reduction
        $this->assertEquals($baseCooldown * 0.5, $service->calculateCooldown(6));

        // Level 11: 100% reduction would be 0, but minimum is 60
        $this->assertEquals(60, $service->calculateCooldown(11));
    }

    /**
     * Test edge case: fleet speed less than 1 should be treated as 1.
     */
    public function testFleetSpeedBelowOne(): void
    {
        $service = $this->createServiceWithFleetSpeed(0);

        // Should use fleet speed 1 as minimum
        $this->assertEquals(3600, $service->calculateCooldown(1));
    }

    /**
     * Create JumpGateService with mocked SettingsService for specific fleet speed.
     *
     * @param int $fleetSpeed
     * @return JumpGateService
     */
    private function createServiceWithFleetSpeed(int $fleetSpeed): JumpGateService
    {
        /** @var SettingsService&MockObject $settingsService */
        $settingsService = $this->createMock(SettingsService::class);
        $settingsService->method('fleetSpeedWar')->willReturn($fleetSpeed);

        return new JumpGateService($settingsService);
    }
}
