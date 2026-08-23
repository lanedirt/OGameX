<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use OGame\Models\Planet\Coordinate;
use OGame\Services\SettingsService;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Remove any stale cache locks left behind by abnormally terminated test
        // runs (e.g. SIGKILL mid-lock). A leftover lock could otherwise make a
        // test hang or fail; cache_locks holds only transient lock state.
        DB::table('cache_locks')->delete();
    }

    /**
     * Find an empty coordinate near the given anchor using the shared collision-safe test logic.
     */
    protected function getSafeEmptyCoordinate(
        Coordinate $anchor,
        int $minPosition = 4,
        int $maxPosition = 12,
        int $minSystemDistance = 0
    ): Coordinate {
        $settingsService = resolve(SettingsService::class);
        $maxGalaxies = $settingsService->numberOfGalaxies();

        $galaxy = $anchor->galaxy <= $maxGalaxies ? $anchor->galaxy : 1;
        $coordinate = new Coordinate($galaxy, 0, 0);
        $tryCount = 0;

        while ($tryCount < 100) {
            $tryCount++;

            do {
                $offset = rand(-10, 10);
            } while ($minSystemDistance > 0 && abs($offset) < $minSystemDistance);

            $coordinate->system = max(1, min(499, $anchor->system + $offset));
            $coordinate->position = rand($minPosition, $maxPosition);

            $planetCount = DB::table('planets')
                ->where('galaxy', $coordinate->galaxy)
                ->where('system', $coordinate->system)
                ->where('planet', $coordinate->position)
                ->count();

            if ($planetCount === 0) {
                return $coordinate;
            }
        }

        $this->fail('Failed to find an empty coordinate for testing.');
    }
}
