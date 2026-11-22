<?php

namespace Tests\Unit;

use OGame\Facades\AppUtil;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AppUtilTest extends TestCase
{
    /**
     * Test that selectWeightedRandom() selects keys roughly proportionally to their weights.
     */
    public function testSelectWeightedRandomDistribution(): void
    {
        $weights = ['common' => 90, 'uncommon' => 10];
        $counts = ['common' => 0, 'uncommon' => 0];
        $iterations = 10000;

        for ($i = 0; $i < $iterations; $i++) {
            $key = AppUtil::selectWeightedRandom($weights);
            $this->assertArrayHasKey($key, $counts, 'Selected key must be one of the input keys');
            $counts[$key]++;
        }

        // Expected: common=9000 (90%), uncommon=1000 (10%). Tolerance ±300 is 10 standard
        // deviations for N=10000, so the test cannot flake but still catches broken weighting.
        $this->assertGreaterThanOrEqual(8700, $counts['common'], 'Key with 90% weight should be selected roughly 90% of the time');
        $this->assertLessThanOrEqual(9300, $counts['common'], 'Key with 90% weight should be selected roughly 90% of the time');
        $this->assertGreaterThanOrEqual(700, $counts['uncommon'], 'Key with 10% weight should be selected roughly 10% of the time');
        $this->assertLessThanOrEqual(1300, $counts['uncommon'], 'Key with 10% weight should be selected roughly 10% of the time');
    }

    /**
     * Test that selectWeightedRandom() never selects a key with zero weight when another
     * key has a positive weight.
     */
    public function testSelectWeightedRandomSkipsZeroWeightKeys(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $this->assertSame('winner', AppUtil::selectWeightedRandom(['loser' => 0, 'winner' => 1]));
        }
    }

    /**
     * Test that selectWeightedRandom() with a single key always returns that key.
     */
    public function testSelectWeightedRandomSingleKey(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame(42, AppUtil::selectWeightedRandom([42 => 5]));
        }
    }

    /**
     * Test that selectWeightedRandom() throws on an empty array.
     */
    public function testSelectWeightedRandomEmptyArrayThrows(): void
    {
        $this->expectException(RuntimeException::class);
        AppUtil::selectWeightedRandom([]);
    }

    /**
     * Test that selectWeightedRandom() falls back to the first key when all weights are zero.
     */
    public function testSelectWeightedRandomAllZeroWeightsReturnsFirstKey(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->assertSame('first', AppUtil::selectWeightedRandom(['first' => 0, 'second' => 0, 'third' => 0]));
        }
    }
}
