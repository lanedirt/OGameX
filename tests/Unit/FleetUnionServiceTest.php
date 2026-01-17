<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use OGame\Models\FleetUnion;
use OGame\Services\FleetUnionService;
use Tests\TestCase;

class FleetUnionServiceTest extends TestCase
{
    use RefreshDatabase;

    private FleetUnionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FleetUnionService::class);
    }

    /**
     * Test getMaxDelayTime returns 30% of remaining time.
     */
    public function testGetMaxDelayTimeCalculation(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() + 1000;

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(300, $maxDelay);
    }

    /**
     * Test getMaxDelayTime with zero remaining time.
     */
    public function testGetMaxDelayTimeZeroRemaining(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() - 100; // Already passed

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(0, $maxDelay);
    }

    /**
     * Test getMaxDelayTime with very short remaining time.
     */
    public function testGetMaxDelayTimeShortDuration(): void
    {
        $union = new FleetUnion();
        $union->time_arrival = time() + 10;

        $maxDelay = $this->service->getMaxDelayTime($union);

        $this->assertEquals(3, $maxDelay); // 30% of 10
    }

    /**
     * Test getMaxDelayTime with various remaining times.
     */
    public function testGetMaxDelayTimeVariousValues(): void
    {
        $testCases = [
            ['remaining' => 3600, 'expected' => 1080],  // 1 hour -> 18 minutes
            ['remaining' => 7200, 'expected' => 2160],  // 2 hours -> 36 minutes
            ['remaining' => 100, 'expected' => 30],     // 100 seconds -> 30 seconds
            ['remaining' => 1, 'expected' => 0],        // 1 second -> 0 seconds (floor)
        ];

        foreach ($testCases as $case) {
            $union = new FleetUnion();
            $union->time_arrival = time() + $case['remaining'];

            $maxDelay = $this->service->getMaxDelayTime($union);

            $this->assertEquals(
                $case['expected'],
                $maxDelay,
                "Failed for remaining time: {$case['remaining']}"
            );
        }
    }
}
