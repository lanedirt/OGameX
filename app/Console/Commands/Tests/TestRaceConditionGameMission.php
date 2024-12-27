<?php

namespace OGame\Console\Commands\Tests;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Validation\ValidationException;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Services\ObjectService;

/**
 * Class TestRaceConditionGameMission.
 *
 * This test is created because of the following issue:
 *
 * Fleet mission processing is not atomic and can cause issues when multiple requests are processed at the same time.
 * This test will issue parallel requests to the overview page which should start the return mission for the transport
 * mission that was dispatched in the previous request. The test will assert that only 1 returning mission is created
 * for 1 transport mission.
 */
class TestRaceConditionGameMission extends TestCommand
{
    protected $signature = 'test:race-condition-game-mission';
    protected $description = 'Issue parallel requests to test race conditions for game mission processing.';

    /**
     * @var string The email of the test user.
     */
    protected string $email = 'raceconditiongamemission@test.com';

    /**
     * Main entry point for the command.
     *
     * @throws ValidationException
     * @throws Exception
     * @throws GuzzleException
     */
    public function handle(): int
    {
        for ($i = 0; $i < $this->numberOfIterations; $i++) {
            $this->info("Running test iteration... $i");

            // Set up the test environment.
            parent::setup();

            // Set up parameters for this specific test.
            $this->testSetup();

            // Dispatch the fleet mission to the second planet.
            $this->dispatchFleetMissionTransport();

            // Mutate the database and set the time_arrival to 1 second in the past so the next
            // request will be able to process the mission and start the return mission.
            FleetMission::where('planet_id_from', $this->currentPlanetService->getPlanetId())
                ->update(['time_departure' => time() - 901,
                    'time_arrival' => time() - 1]);

            // Run the parallel requests against the overview page which should start the return mission.
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
     * @throws Exception
     */
    private function testSetup(): void
    {
        $this->currentPlanetService->addUnit('small_cargo', 10);
    }

    /**
     * Assert the database state after the test.
     */
    private function testAssert(): bool
    {
        $this->genericAssertSetup();

        // Test if there is only 1 retuning mission in the database.
        $returningMissions = FleetMission::where('planet_id_to', $this->currentPlanetService->getPlanetId())
            ->where('mission_type', 3)
            ->get();
        if ($returningMissions->count() === 1) {
            $this->info('[OK] Exactly 1 returning mission created for 1 transport mission.');
            return true;
        } else {
            $this->error('[ERROR] There are ' . $returningMissions->count() . ' returning missions in the database while only 1 should be created. Check for race conditions!');
            return false;
        }
    }

    /**
     * @throws GuzzleException
     */
    private function dispatchFleetMissionTransport(): void
    {
        $missionTypeTransport = 3;
        $secondPlanet = $this->playerService->planets->all()[1];
        $this->playerService->planets->first()->addResources(new Resources(0, 0, 1000000, 0));
        $secondPlanetCoordinates = $secondPlanet->getPlanetCoordinates();

        $csrfToken = $this->getCsrfToken();
        $this->httpClient->request('POST', '/ajax/fleet/dispatch/send-fleet', [
            'timeout' => 30,
            'form_params' => [
                '_token' => $csrfToken,
                'galaxy' => $secondPlanetCoordinates->galaxy,
                'system' => $secondPlanetCoordinates->system,
                'position' => $secondPlanetCoordinates->position,
                'type' => 1,
                'mission' => $missionTypeTransport,
                'metal' => 0,
                'speed' => 10,
                'crystal' => 0,
                'deuterium' => 0,
                'am' . ObjectService::getUnitObjectByMachineName('small_cargo')->id  => '10',
            ]
        ]);

        $this->info("Dispatched 10 small cargos to users second planet: {$secondPlanetCoordinates->asString()}");
    }
}
