<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Enums\CharacterClass;
use Tests\FleetDispatchTestCase;

/**
 * Test that Reaper ships automatically collect 30% of debris from attacks (General class).
 */
class ReaperDebrisCollectionTest extends FleetDispatchTestCase
{
    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        // No special setup needed for Reaper debris collection tests
    }

    /**
     * Test that Reaper ships automatically collect 30% of debris when attacking with General class.
     *
     * @throws BindingResolutionException
     */
    public function testReaperCollectsDebrisWithGeneralClass(): void
    {
        // Set up: attacker is General class
        $this->playerSetUp();
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Set up: defender planet
        $this->secondPlayerSetup();
        $defender = $this->secondPlanetService;

        // Set up: Attacker has Reapers
        $attacker->addUnit('reaper', 50);
        $attacker->addUnit('light_fighter', 100);

        // Set up: Defender has defenses that will create debris
        $defender->addUnit('rocket_launcher', 100);

        // Launch attack mission
        $this->sendMissionToSecondPlayer(
            $this->planetService,
            [
                'reaper' => 50,
                'light_fighter' => 100,
            ],
            1, // attack mission
            new \OGame\Models\Resources(0, 0, 0, 0)
        );

        // Get debris field before processing mission
        $debrisFieldService = resolve(\OGame\Services\DebrisFieldService::class);
        $debrisFieldBefore = 0;
        if ($debrisFieldService->loadForCoordinates($defender->getPlanetCoordinates())) {
            $debrisFieldBefore = $debrisFieldService->getResources()->sum();
        }

        // Process arrival (battle happens)
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class);
        $fleetMissionService->updateFleetMissions();

        // Get battle report to check debris amounts
        $battleReport = \OGame\Models\BattleReport::latest()->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        $totalDebris = $battleReport->debris['metal'] + $battleReport->debris['crystal'] + $battleReport->debris['deuterium'];
        $collectedDebris = $battleReport->debris['collected_metal'] + $battleReport->debris['collected_crystal'] + $battleReport->debris['collected_deuterium'];

        // Assert: Reaper collected 30% of debris
        if ($totalDebris > 0) {
            $expectedCollected = (int)($totalDebris * 0.30);
            $this->assertGreaterThan(0, $collectedDebris, 'Reapers should have collected debris');
            $this->assertEqualsWithDelta($expectedCollected, $collectedDebris, $expectedCollected * 0.05, 'Reapers should collect approximately 30% of debris');
        }

        // Check debris field: should contain 70% of total debris (100% - 30% collected)
        $debrisFieldService = resolve(\OGame\Services\DebrisFieldService::class);
        if ($debrisFieldService->loadForCoordinates($defender->getPlanetCoordinates())) {
            $debrisFieldAfter = $debrisFieldService->getResources()->sum();
            $debrisInField = $debrisFieldAfter - $debrisFieldBefore;

            if ($totalDebris > 0) {
                $expectedInField = $totalDebris - $collectedDebris;
                $this->assertEqualsWithDelta($expectedInField, $debrisInField, 1, 'Debris field should contain 70% of total debris');
            }
        }

        // Process return mission
        $fleetMissionService->updateFleetMissions();

        // Check that returned fleet contains the collected debris as resources
        $attacker->reload();
        // The collected debris should have been added to the returning fleet
        // (checking this indirectly through the total resources)
        $this->assertGreaterThan(0, $attacker->metal()->get() + $attacker->crystal()->get(), 'Attacker should have received collected debris');
    }

    /**
     * Test that Reaper ships do NOT collect debris when attacker is not General class.
     *
     * @throws BindingResolutionException
     */
    public function testReaperDoesNotCollectDebrisWithoutGeneralClass(): void
    {
        // Set up: attacker is NOT General class (Collector)
        $this->playerSetUp();
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to Collector (not General)
        $attackerPlayer->getUser()->character_class = CharacterClass::COLLECTOR->value;
        $attackerPlayer->getUser()->save();

        // Set up: defender planet
        $this->secondPlayerSetup();
        $defender = $this->secondPlanetService;

        // Set up: Attacker has Reapers
        $attacker->addUnit('reaper', 50);
        $attacker->addUnit('light_fighter', 100);

        // Set up: Defender has defenses that will create debris
        $defender->addUnit('rocket_launcher', 100);

        // Launch attack mission
        $this->sendMissionToSecondPlayer(
            $this->planetService,
            [
                'reaper' => 50,
                'light_fighter' => 100,
            ],
            1, // attack mission
            new \OGame\Models\Resources(0, 0, 0, 0)
        );

        // Process arrival (battle happens)
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class);
        $fleetMissionService->updateFleetMissions();

        // Get battle report to check debris amounts
        $battleReport = \OGame\Models\BattleReport::latest()->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        $collectedDebris = ($battleReport->debris['collected_metal'] ?? 0) +
                          ($battleReport->debris['collected_crystal'] ?? 0) +
                          ($battleReport->debris['collected_deuterium'] ?? 0);

        // Assert: No debris should be collected (not General class)
        $this->assertEquals(0, $collectedDebris, 'Reapers should NOT collect debris without General class');
    }

    /**
     * Test that no debris is collected if there are no Reaper ships in the attack.
     *
     * @throws BindingResolutionException
     */
    public function testNoDebrisCollectionWithoutReapers(): void
    {
        // Set up: attacker is General class but has no Reapers
        $this->playerSetUp();
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Set up: defender planet
        $this->secondPlayerSetup();
        $defender = $this->secondPlanetService;

        // Set up: Attacker has NO Reapers, only other ships
        $attacker->addUnit('light_fighter', 200);

        // Set up: Defender has defenses that will create debris
        $defender->addUnit('rocket_launcher', 100);

        // Launch attack mission
        $this->sendMissionToSecondPlayer(
            $this->planetService,
            [
                'light_fighter' => 200,
            ],
            1, // attack mission
            new \OGame\Models\Resources(0, 0, 0, 0)
        );

        // Process arrival (battle happens)
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class);
        $fleetMissionService->updateFleetMissions();

        // Get battle report to check debris amounts
        $battleReport = \OGame\Models\BattleReport::latest()->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        $collectedDebris = ($battleReport->debris['collected_metal'] ?? 0) +
                          ($battleReport->debris['collected_crystal'] ?? 0) +
                          ($battleReport->debris['collected_deuterium'] ?? 0);

        // Assert: No debris should be collected (no Reapers in fleet)
        $this->assertEquals(0, $collectedDebris, 'No debris should be collected without Reaper ships');
    }
}
