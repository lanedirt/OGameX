<?php

namespace OGame\Console\Commands\Tests;

use GuzzleHttp\Promise\Utils;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\ObjectService;

/**
 * Class TestRaceConditionFleetDispatch.
 *
 * This test is created to verify that the fleet dispatch endpoint is protected against race conditions
 * when multiple parallel requests attempt to send the same ships simultaneously.
 *
 * The exploit scenario:
 * - User has 10 small cargo ships
 * - User sends 3 parallel requests to dispatch all 10 ships
 * - Without protection: All 3 requests pass validation (reading stale data) and 3 fleets are created
 * - With protection: Only 1 request succeeds, the others fail with "not enough units"
 */
class TestRaceConditionFleetDispatch extends TestCommand
{
    protected $signature = 'ogamex:test:race-condition-fleet-dispatch';
    protected $description = 'Issue parallel fleet dispatch requests to test race conditions for fleet sending.';

    /**
     * @var string The email of the test user.
     */
    protected string $email = 'raceconditionfleetdispatch@test.com';

    /**
     * @var int Number of parallel fleet dispatch requests to send.
     */
    protected int $numberOfRequests = 5;

    /**
     * @var int Number of ships to use in the test.
     */
    private int $shipCount = 10;

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

            // Run parallel fleet dispatch requests.
            $this->runParallelFleetDispatch();

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
        // Add enough resources for fuel
        $this->currentPlanetService->addResources(new Resources(0, 0, 1000000, 0));

        // Add small cargo ships to the planet
        $this->currentPlanetService->addUnit('small_cargo', $this->shipCount);

        $this->info("Added {$this->shipCount} small cargo ships to planet.");
    }

    /**
     * Run parallel fleet dispatch requests.
     *
     * @throws GuzzleException
     */
    private function runParallelFleetDispatch(): void
    {
        $this->info("Running {$this->numberOfRequests} parallel fleet dispatch requests...");

        $csrfToken = $this->getCsrfToken();
        $secondPlanetCoordinates = $this->secondPlanetService->getPlanetCoordinates();

        // Prepare fleet dispatch parameters
        $fleetData = [
            '_token' => $csrfToken,
            'galaxy' => $secondPlanetCoordinates->galaxy,
            'system' => $secondPlanetCoordinates->system,
            'position' => $secondPlanetCoordinates->position,
            'type' => 1, // Planet
            'mission' => 3, // Transport
            'speed' => 10, // 100% speed
            'holdingtime' => 0,
            'metal' => 0,
            'crystal' => 0,
            'deuterium' => 0,
            'am' . ObjectService::getUnitObjectByMachineName('small_cargo')->id => $this->shipCount,
        ];

        // Store timing logs
        $timeLogs = [];
        $promises = [];

        // Issue parallel POST requests
        for ($i = 0; $i < $this->numberOfRequests; $i++) {
            $timeLogs[$i]['start'] = new DateTime();

            $promises[$i] = $this->httpClient->postAsync('/ajax/fleet/dispatch/send-fleet', [
                'form_params' => $fleetData,
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

        $successCount = 0;
        $failCount = 0;

        foreach ($responses as $i => $result) {
            try {
                $response = $result['value'];
                if ($response) {
                    $body = json_decode($response->getBody()->getContents(), true);
                    $startTime = $timeLogs[$i]['start']->format('H:i:s.u');
                    $endTime = $timeLogs[$i]['end']->format('H:i:s.u');

                    if (isset($body['success']) && $body['success'] === true) {
                        $successCount++;
                        $this->info("Request $i: SUCCESS (Started: $startTime, Ended: $endTime)");
                    } else {
                        $failCount++;
                        $errorMsg = $body['errors'][0]['message'] ?? 'Unknown error';
                        $this->info("Request $i: FAILED - $errorMsg (Started: $startTime, Ended: $endTime)");
                    }
                } else {
                    $failCount++;
                    $this->error("Request $i: No response received");
                }
            } catch (Exception $e) {
                $failCount++;
                $this->error("Request $i: Exception - " . $e->getMessage());
            }
        }

        $this->info("Summary: $successCount successful, $failCount failed");
    }

    /**
     * Assert the database state after the test.
     */
    private function testAssert(): bool
    {
        $this->genericAssertSetup();

        // Count fleet missions created from this planet
        $fleetMissions = FleetMission::where('planet_id_from', $this->currentPlanetService->getPlanetId())
            ->where('mission_type', 3) // Transport
            ->whereNull('parent_id') // Only outgoing missions, not returns
            ->get();

        $missionCount = $fleetMissions->count();

        // Check ships remaining on planet
        $shipsRemaining = $this->currentPlanetService->getObjectAmount('small_cargo');

        // Calculate total ships in missions
        $shipsInMissions = 0;
        foreach ($fleetMissions as $mission) {
            $shipsInMissions += $mission->small_cargo ?? 0;
        }

        $this->info("Fleet missions created: $missionCount");
        $this->info("Ships remaining on planet: $shipsRemaining");
        $this->info("Ships in fleet missions: $shipsInMissions");
        $this->info("Total ships accounted for: " . ($shipsRemaining + $shipsInMissions) . " (expected: {$this->shipCount})");

        // Validation rules:
        // 1. Only 1 fleet mission should be created (since we're trying to send all ships)
        // 2. Ships remaining + ships in mission should equal original ship count (no duplication)
        if ($missionCount !== 1) {
            $this->error("[ERROR] Expected exactly 1 fleet mission, but found $missionCount. Race condition detected!");
            return false;
        }

        if ($shipsRemaining + $shipsInMissions !== $this->shipCount) {
            $this->error("[ERROR] Ship count mismatch! Remaining ($shipsRemaining) + In missions ($shipsInMissions) != Original ({$this->shipCount}). Duplication detected!");
            return false;
        }

        if ($shipsRemaining !== 0) {
            $this->error("[ERROR] Expected 0 ships remaining on planet after sending all, but found $shipsRemaining.");
            return false;
        }

        $this->info('[OK] Fleet dispatch race condition test passed. Only 1 fleet was created.');
        return true;
    }
}
