<?php

namespace OGame\Console\Commands\Tests;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use OGame\Models\Resources;
use OGame\Services\ObjectService;
use OGame\Services\UnitQueueService;

/**
 * Class TestRaceConditionUnitQueue.
 *
 * This test was created as an example for running parallel requests. There has not been an actual
 * bug report that the unit queue has race conditions. This test is just an example of how to run
 * tests that cover cases using parallel requests.
 */
class TestRaceConditionUnitQueue extends TestCommand
{
    protected $signature = 'test:race-condition-unitqueue';
    protected $description = 'Issue parallel requests to test race conditions for unit queue planet updates.';

    /**
     * @var string The email of the test user.
     */
    protected string $email = 'raceconditionunitqueue@test.com';

    /**
     * Main entry point for the command.
     *
     * @throws ValidationException
     * @throws Exception|GuzzleException
     */
    public function handle(): int
    {
        for ($i = 0; $i < $this->numberOfIterations; $i++) {
            // Set up the test environment.
            parent::setup();

            // Set up parameters for this specific test.
            $this->testSetup();

            // Run the parallel requests against the overview page.
            $this->runParallelRequests('overview');

            // Assert the database state after the test.
            if (!$this->testAssert()) {
                $this->error('Test failed. Exiting...');
                return 1;
            }
        }

        return 0;
    }

    /**
     * Set up the test environment.
     *
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function testSetup(): void
    {
        // Prepare user for testing the race conditions by adding some entries to the build queues.
        $this->currentPlanetService->addResources(new Resources(1000000, 1000000, 1000000, 0));
        $this->currentPlanetService->setObjectLevel(ObjectService::getObjectByMachineName('robot_factory')->id, 2);
        $this->currentPlanetService->setObjectLevel(ObjectService::getObjectByMachineName('shipyard')->id, 1);
        $this->currentPlanetService->setObjectLevel(ObjectService::getObjectByMachineName('research_lab')->id, 1);
        $this->playerService->setResearchLevel('energy_technology', 1);
        $this->playerService->setResearchLevel('combustion_drive', 1);

        // Set time to static time 2024-01-01
        $startTime = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($startTime);

        // Add light fighter build job.
        $unitQueueService = resolve(UnitQueueService::class);
        $unitQueueService->add($this->playerService->planets->current(), ObjectService::getUnitObjectByMachineName('light_fighter')->id, 10);
    }

    /**
     * Assert the database state after the test.
     */
    private function testAssert(): bool
    {
        $this->genericAssertSetup();

        // Test if the user has the expected units.
        $light_fighter_count = $this->playerService->planets->current()->getObjectAmount('light_fighter');
        if ($light_fighter_count === 10) {
            $this->info('[OK] User has built 10 light fighters as expected.');
            return true;
        } else {
            $this->error('[ERROR] User does not have the expected 10 light fighter units. Amount of light fighters on planet: ' . $light_fighter_count . '. Check for race conditions!');
            return false;
        }
    }
}
