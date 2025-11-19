<?php

namespace Tests\Unit;

use OGame\Models\Planet\Coordinate;
use OGame\Services\PhalanxService;
use Tests\UnitTestCase;

class PhalanxServiceTest extends UnitTestCase
{
    private PhalanxService $phalanxService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phalanxService = resolve(PhalanxService::class);
    }

    /**
     * Test phalanx range calculation formula.
     */
    public function testCalculatePhalanxRange(): void
    {
        // Level 0: should return -1 (formula: level^2 - 1)
        $this->assertEquals(-1, $this->phalanxService->calculatePhalanxRange(0));

        // Level 1: range 0 (can only scan same system)
        $this->assertEquals(0, $this->phalanxService->calculatePhalanxRange(1));

        // Level 2: range 3
        $this->assertEquals(3, $this->phalanxService->calculatePhalanxRange(2));

        // Level 3: range 8
        $this->assertEquals(8, $this->phalanxService->calculatePhalanxRange(3));

        // Level 4: range 15
        $this->assertEquals(15, $this->phalanxService->calculatePhalanxRange(4));

        // Level 5: range 24
        $this->assertEquals(24, $this->phalanxService->calculatePhalanxRange(5));

        // Level 10: range 99
        $this->assertEquals(99, $this->phalanxService->calculatePhalanxRange(10));
    }

    /**
     * Test that canScanTarget returns false when phalanx level is 0.
     */
    public function testCannotScanWithoutPhalanx(): void
    {
        $targetCoords = new Coordinate(1, 100, 5);
        $this->assertFalse($this->phalanxService->canScanTarget(1, 100, 0, $targetCoords));
    }

    /**
     * Test that canScanTarget returns false when target is in different galaxy.
     */
    public function testCannotScanDifferentGalaxy(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 100;
        $phalanxLevel = 5;

        $targetCoords = new Coordinate(2, 100, 5); // Different galaxy

        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords));
    }

    /**
     * Test that canScanTarget returns true when target is in same system.
     */
    public function testCanScanSameSystem(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 100;
        $phalanxLevel = 1; // Range 0, can only scan same system

        $targetCoords = new Coordinate(1, 100, 5); // Same galaxy and system

        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords));
    }

    /**
     * Test that canScanTarget returns false when target is out of range.
     */
    public function testCannotScanOutOfRange(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 100;
        $phalanxLevel = 1; // Range 0, can only scan same system

        $targetCoords = new Coordinate(1, 101, 5); // 1 system away, out of range

        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords));
    }

    /**
     * Test that canScanTarget returns true when target is within range.
     */
    public function testCanScanWithinRange(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 100;
        $phalanxLevel = 2; // Range 3

        // Test exactly at range
        $targetCoords1 = new Coordinate(1, 103, 5); // 3 systems away
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords1));

        // Test within range
        $targetCoords2 = new Coordinate(1, 102, 5); // 2 systems away
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords2));

        // Test on the other side
        $targetCoords3 = new Coordinate(1, 97, 5); // 3 systems away (other direction)
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords3));

        // Test out of range
        $targetCoords4 = new Coordinate(1, 104, 5); // 4 systems away, out of range
        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords4));
    }

    /**
     * Test that hasEnoughDeuterium returns correct result.
     */
    public function testHasEnoughDeuterium(): void
    {
        // Test with not enough deuterium
        $this->assertFalse($this->phalanxService->hasEnoughDeuterium(4999));

        // Test with exactly enough deuterium
        $this->assertTrue($this->phalanxService->hasEnoughDeuterium(5000));

        // Test with more than enough deuterium
        $this->assertTrue($this->phalanxService->hasEnoughDeuterium(10000));

        // Test with 0 deuterium
        $this->assertFalse($this->phalanxService->hasEnoughDeuterium(0));
    }

    /**
     * Test that getScanCost returns correct value.
     */
    public function testGetScanCost(): void
    {
        $this->assertEquals(5000, $this->phalanxService->getScanCost());
    }

    /**
     * Test edge case: very high phalanx level.
     */
    public function testVeryHighPhalanxLevel(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 250;
        $phalanxLevel = 20; // Range 399

        // Should be able to scan very far
        $targetCoords = new Coordinate(1, 1, 1); // 249 systems away
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords));

        // But still can't scan different galaxy
        $targetCoordsDiffGalaxy = new Coordinate(2, 250, 1);
        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoordsDiffGalaxy));
    }

    /**
     * Test system distance calculation works in both directions.
     */
    public function testSystemDistanceBidirectional(): void
    {
        $moonGalaxy = 1;
        $moonSystem = 100;
        $phalanxLevel = 3; // Range 8

        // Test scanning backwards
        $targetCoords1 = new Coordinate(1, 92, 5); // 8 systems backwards
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords1));

        // Test scanning forwards
        $targetCoords2 = new Coordinate(1, 108, 5); // 8 systems forwards
        $this->assertTrue($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords2));

        // Test out of range backwards
        $targetCoords3 = new Coordinate(1, 91, 5); // 9 systems backwards
        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords3));

        // Test out of range forwards
        $targetCoords4 = new Coordinate(1, 109, 5); // 9 systems forwards
        $this->assertFalse($this->phalanxService->canScanTarget($moonGalaxy, $moonSystem, $phalanxLevel, $targetCoords4));
    }
}
