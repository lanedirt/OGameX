<?php

namespace OGame\Console\Commands\Test;

use GuzzleHttp\Promise\Utils;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use OGame\Models\Resources;
use OGame\Models\UnitQueue;
use OGame\Services\ObjectService;

/**
 * Class TestRaceConditionUnitQueueCreation.
 *
 * This test is created to verify that the unit/defense queue creation endpoint is protected against
 * race conditions when multiple parallel requests attempt to add items to the queue simultaneously.
 *
 * The exploit scenario:
 * - User has exactly enough resources for 10 rocket launchers
 * - User sends 5 parallel requests to build 10 rocket launchers each
 * - Without protection: All 5 requests pass validation (reading stale resources) and 5 queue items are created
 * - With protection: Only 1 request succeeds, the others fail with "not enough resources"
 */
class TestRaceConditionUnitQueueCreation extends TestCommand
{
    protected $signature = 'ogamex:test:race-condition-unitqueue-creation';
    protected $description = 'Issue parallel unit queue creation requests to test race conditions.';

    /**
     * @var string The email of the test user.
     */
    protected string $email = 'raceconditionunitqueuecreation@test.com';

    /**
     * @var int Number of parallel queue creation requests to send.
     */
    protected int $numberOfRequests = 10;

    /**
     * @var int Number of units to build in each request.
     */
    private int $unitCount = 50;

    /**
     * Main entry point for the command.
     *
     * @throws ValidationException
     * @throws Exception|GuzzleException
     */
    public function handle(): int
    {
        for ($i = 0; $i < $this->numberOfIterations; $i++) {
            $this->info("Running test iteration... $i");

            // Set up the test environment.
            parent::setup();

            // Set up parameters for this specific test.
            $this->testSetup();

            // Run parallel unit queue creation requests.
            $this->runParallelQueueCreation();

            // Assert the database state after the test.
            if (!$this->testAssert()) {
                $this->error('Test failed. Exiting...');
                return 1;
            }
        }

        $this->info('All test iterations passed!');
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
        // Set up shipyard requirement
        $this->currentPlanetService->setObjectLevel(ObjectService::getObjectByMachineName('shipyard')->id, 1);

        // Get the price of a rocket launcher
        $rocketLauncherPrice = ObjectService::getObjectPrice('rocket_launcher', $this->currentPlanetService);

        // Add exactly enough resources for the requested number of units
        // This ensures only 1 queue creation should succeed
        $totalPrice = $rocketLauncherPrice->multiply($this->unitCount);

        // Set resources to exactly match what we need (clear first, then add)
        $this->currentPlanetService->deductResources($this->currentPlanetService->getResources());
        $this->currentPlanetService->addResources($totalPrice);

        $this->info("Set up planet with exactly enough resources for {$this->unitCount} rocket launchers.");
        $this->info("Resources: Metal={$totalPrice->metal->get()}, Crystal={$totalPrice->crystal->get()}, Deuterium={$totalPrice->deuterium->get()}");
    }

    /**
     * Run parallel unit queue creation requests.
     *
     * @throws GuzzleException
     */
    private function runParallelQueueCreation(): void
    {
        $this->info("Running {$this->numberOfRequests} parallel unit queue creation requests...");

        $csrfToken = $this->getCsrfToken();

        // Get rocket launcher ID
        $rocketLauncherId = ObjectService::getUnitObjectByMachineName('rocket_launcher')->id;

        // Prepare queue creation parameters
        $queueData = [
            '_token' => $csrfToken,
            'technologyId' => $rocketLauncherId,
            'amount' => $this->unitCount,
            'mode' => 1, // Add to queue
        ];

        // Store timing logs
        $timeLogs = [];
        $promises = [];

        // Issue parallel POST requests
        for ($i = 0; $i < $this->numberOfRequests; $i++) {
            $timeLogs[$i]['start'] = new DateTime();

            $promises[$i] = $this->httpClient->postAsync('/defense/add-buildrequest', [
                'form_params' => $queueData,
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
            ])->then(
                function ($response) use (&$timeLogs, $i) {
                    $timeLogs[$i]['end'] = new DateTime();
                    return $response;
                },
                function ($exception) use (&$timeLogs, $i) {
                    $timeLogs[$i]['end'] = new DateTime();
                    return $exception->getResponse();
                }
            );
        }

        // Wait for all requests to complete
        $responses = Utils::settle($promises)->wait();

        foreach ($responses as $i => $result) {
            try {
                $response = $result['value'];
                if ($response) {
                    $statusCode = $response->getStatusCode();
                    $startTime = $timeLogs[$i]['start']->format('H:i:s.u');
                    $endTime = $timeLogs[$i]['end']->format('H:i:s.u');

                    $this->info("Request $i: Status $statusCode (Started: $startTime, Ended: $endTime)");
                } else {
                    $this->error("Request $i: No response received");
                }
            } catch (Exception $e) {
                $this->error("Request $i: Exception - " . $e->getMessage());
            }
        }
    }

    /**
     * Assert the database state after the test.
     */
    private function testAssert(): bool
    {
        $this->genericAssertSetup();

        // Get rocket launcher ID
        $rocketLauncherId = ObjectService::getUnitObjectByMachineName('rocket_launcher')->id;

        // Count queue items created for this planet
        $queueItems = UnitQueue::where('planet_id', $this->currentPlanetService->getPlanetId())
            ->where('object_id', $rocketLauncherId)
            ->where('processed', 0)
            ->get();

        $queueCount = $queueItems->count();

        // Calculate total units in queue
        $totalUnitsInQueue = 0;
        foreach ($queueItems as $item) {
            $totalUnitsInQueue += $item->object_amount ?? 0;
        }

        // Check resources remaining on planet
        $resourcesRemaining = $this->currentPlanetService->getResources();

        $this->info("Queue items created: $queueCount");
        $this->info("Total units in queue: $totalUnitsInQueue");
        $this->info("Resources remaining - Metal: {$resourcesRemaining->metal->get()}, Crystal: {$resourcesRemaining->crystal->get()}, Deuterium: {$resourcesRemaining->deuterium->get()}");

        // Validation rules:
        // 1. Only 1 queue item should be created (since we only have resources for 1 batch)
        // 2. Resources should be fully consumed (close to 0)
        if ($queueCount !== 1) {
            $this->error("[ERROR] Expected exactly 1 queue item, but found $queueCount. Race condition detected!");
            return false;
        }

        if ($totalUnitsInQueue !== $this->unitCount) {
            $this->error("[ERROR] Expected {$this->unitCount} units in queue, but found $totalUnitsInQueue.");
            return false;
        }

        // Check that resources are depleted (should be 0 or very close)
        if ($resourcesRemaining->metal->get() > 100 || $resourcesRemaining->crystal->get() > 100) {
            $this->error("[ERROR] Resources not properly deducted. Metal: {$resourcesRemaining->metal->get()}, Crystal: {$resourcesRemaining->crystal->get()}");
            return false;
        }

        $this->info('[OK] Unit queue creation race condition test passed. Only 1 queue item was created.');
        return true;
    }
}
