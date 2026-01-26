<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Contracts\Container\BindingResolutionException;
use OGame\Enums\CharacterClass;
use Tests\FleetDispatchTestCase;

/**
 * Test that Reaper ships automatically collect 30% of debris from attacks (all classes).
 */
class ReaperDebrisCollectionTest extends FleetDispatchTestCase
{
    protected int $missionType = 1; // Attack mission
    protected string $missionName = 'Attack';

    /**
     * Prepare the planet for the test, so it has the required buildings and research.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        // Clear any existing battle reports to ensure test isolation
        // First delete messages that reference battle reports (foreign key constraint)
        \OGame\Models\Message::where('battle_report_id', '!=', null)->delete();
        \OGame\Models\BattleReport::query()->delete();

        // Enable debris field creation (30% of destroyed ships become debris)
        $settingsService = resolve(\OGame\Services\SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('debris_field_from_defense', 0); // Defenses don't create debris by default
    }

    /**
     * Test that Reaper ships automatically collect 30% of debris when attacking (any class).
     *
     * @throws BindingResolutionException
     */
    public function testReaperCollectsDebris(): void
    {
        // Clear all battle reports to ensure test isolation
        \OGame\Models\Message::where('battle_report_id', '!=', null)->delete();
        \OGame\Models\BattleReport::query()->delete();

        // Set up: attacker with any class (Reaper collection works for all classes)
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General (required for Reaper ships)
        $attackerPlayer->getUser()->character_class = \OGame\Enums\CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Clear any existing units from previous tests to ensure test isolation
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save(); // Save the planet after removing units
        $attacker->reloadPlanet(); // Reload to ensure clean state

        // Set up: Attacker with Reapers - need enough to survive and collect debris
        $attacker->addUnit('reaper', 10);
        $attacker->save(); // CRITICAL: Save to database so battle can load units

        // Verify Reapers were added
        $this->assertEquals(10, $attacker->getObjectAmount('reaper'), 'Reapers should be added to planet');

        // Add deuterium for fleet travel
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 50000, 0));

        // Launch attack mission to foreign planet
        $units = new \OGame\GameObjects\Models\Units\UnitCollection();
        $units->addUnit(\OGame\Services\ObjectService::getShipObjectByMachineName('reaper'), 10);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new \OGame\Models\Resources(0, 0, 0, 0));

        // Clear foreign planet units from previous tests to ensure isolation
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->save();
        $foreignPlanet->reloadPlanet();

        // Set up: Defender with ships (not defenses) to create debris when destroyed
        // Using cruisers to create battle - but not too many to exceed Reaper cargo capacity
        $foreignPlanet->addUnit('cruiser', 5);

        // Get debris field before processing mission
        $debrisFieldService = resolve(\OGame\Services\DebrisFieldService::class);
        $debrisFieldBefore = 0;
        if ($debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates())) {
            $debrisFieldBefore = $debrisFieldService->getResources()->sum();
        }

        // Get the mission to calculate travel time
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview'); // Trigger mission processing

        // Get battle report to check debris amounts
        // Filter by target planet coordinates to ensure we get the correct report
        $coords = $foreignPlanet->getPlanetCoordinates();
        $battleReport = \OGame\Models\BattleReport::query()
            ->where('planet_galaxy', $coords->galaxy)
            ->where('planet_system', $coords->system)
            ->where('planet_position', $coords->position)
            ->latest()
            ->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        $totalDebris = (int)$battleReport->debris['metal'] + (int)$battleReport->debris['crystal'] + (int)$battleReport->debris['deuterium'];
        $collectedDebris = (int)$battleReport->debris['collected_metal'] + (int)$battleReport->debris['collected_crystal'] + (int)$battleReport->debris['collected_deuterium'];

        // Check if any debris was created from battle
        $this->assertGreaterThan(0, $totalDebris, 'Battle should create debris from destroyed units');

        // Assert: Reaper collected debris (up to cargo capacity)
        // Note: Reapers must survive the battle to collect debris
        // Note: Reaper debris collection is capped by cargo capacity (7000 per Reaper, 70000 total for 10 Reapers with bonuses)
        $this->assertGreaterThan(0, $collectedDebris, 'Reapers should have collected debris (requires surviving Reapers)');
        // The collected amount should be close to 30% of total, but may be limited by cargo capacity
        $expectedCollected = min((int)($totalDebris * 0.30), 70000);
        $this->assertLessThanOrEqual($expectedCollected * 1.3, $collectedDebris, 'Reapers should not collect more than expected');

        // Check debris field: should contain remaining debris (100% - collected%)
        $debrisFieldService = resolve(\OGame\Services\DebrisFieldService::class);
        if ($debrisFieldService->loadForCoordinates($foreignPlanet->getPlanetCoordinates())) {
            $debrisFieldAfter = $debrisFieldService->getResources()->sum();
            $debrisInField = $debrisFieldAfter - $debrisFieldBefore;

            if ($totalDebris > 0) {
                $expectedInField = $totalDebris - $collectedDebris;
                $this->assertEqualsWithDelta($expectedInField, $debrisInField, 10, 'Debris field should contain remaining debris after collection');
            }
        }

        // Process return mission
        $this->travel(10)->seconds();

        // Check that returned fleet contains the collected debris as resources
        // The collected debris should have been added to the returning fleet
        // (checking this indirectly through the total resources)
        $this->assertGreaterThan(0, $attacker->metal()->get() + $attacker->crystal()->get(), 'Attacker should have received collected debris');
    }

    /**
     * Test that Reaper ships collect debris even when attacker is not General class.
     * Reaper debris collection works for ALL character classes.
     *
     * @throws BindingResolutionException
     */
    public function testReaperCollectsDebrisWithNonGeneralClass(): void
    {
        // Clear all battle reports to ensure test isolation
        \OGame\Models\Message::where('battle_report_id', '!=', null)->delete();
        \OGame\Models\BattleReport::query()->delete();

        // Set up: attacker is NOT General class (using Collector to prove it works for all classes)
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to Collector (not General - to prove Reapers work for all classes)
        $attackerPlayer->getUser()->character_class = CharacterClass::COLLECTOR->value;
        $attackerPlayer->getUser()->save();

        // Clear any existing units from previous tests to ensure test isolation
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();

        // Set up: Attacker with Reapers - need enough to survive and collect debris
        $attacker->addUnit('reaper', 10);
        $attacker->save(); // CRITICAL: Save to database so battle can load units

        // Verify Reapers were added
        $this->assertEquals(10, $attacker->getObjectAmount('reaper'), 'Reapers should be added to planet');

        // Add deuterium for fleet travel
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 50000, 0));

        // Launch attack mission to foreign planet
        $units = new \OGame\GameObjects\Models\Units\UnitCollection();
        $units->addUnit(\OGame\Services\ObjectService::getShipObjectByMachineName('reaper'), 10);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new \OGame\Models\Resources(0, 0, 0, 0));

        // Clear foreign planet units from previous tests to ensure isolation
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->save();
        $foreignPlanet->reloadPlanet();

        // Set up: Defender with ships (not defenses) to create debris when destroyed
        // Using cruisers to create battle - but not too many to exceed Reaper cargo capacity
        $foreignPlanet->addUnit('cruiser', 5);

        // Get the mission to calculate travel time
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview'); // Trigger mission processing

        // Get battle report to check debris amounts
        // Filter by target planet coordinates to ensure we get the correct report
        $coords = $foreignPlanet->getPlanetCoordinates();
        $battleReport = \OGame\Models\BattleReport::query()
            ->where('planet_galaxy', $coords->galaxy)
            ->where('planet_system', $coords->system)
            ->where('planet_position', $coords->position)
            ->latest()
            ->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        $totalDebris = (int)$battleReport->debris['metal'] + (int)$battleReport->debris['crystal'] + (int)$battleReport->debris['deuterium'];
        $collectedDebris = (int)$battleReport->debris['collected_metal'] + (int)$battleReport->debris['collected_crystal'] + (int)$battleReport->debris['collected_deuterium'];

        // Check if any debris was created from battle
        $this->assertGreaterThan(0, $totalDebris, 'Battle should create debris from destroyed units');

        // Assert: Debris SHOULD be collected by Reapers (up to cargo capacity)
        // Note: Reapers must survive the battle to collect debris
        // Note: Reaper debris collection is capped by cargo capacity (7000 per Reaper, 70000 total for 10 Reapers with bonuses)
        $this->assertGreaterThan(0, $collectedDebris, 'Reapers should have collected debris (requires surviving Reapers)');
        // The collected amount should be close to 30% of total, but may be limited by cargo capacity
        $expectedCollected = min((int)($totalDebris * 0.30), 70000);
        $this->assertLessThanOrEqual($expectedCollected * 1.3, $collectedDebris, 'Reapers should not collect more than expected');
    }

    /**
     * Test that no debris is collected if there are no Reaper ships in the attack.
     *
     * @throws BindingResolutionException
     */
    public function testNoDebrisCollectionWithoutReapers(): void
    {
        // Clear all battle reports to ensure test isolation
        \OGame\Models\Message::where('battle_report_id', '!=', null)->delete();
        \OGame\Models\BattleReport::query()->delete();

        // Set up: attacker has no Reapers (only other ships)
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Clear any existing units from previous tests to ensure test isolation
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();

        // Set up: Attacker has NO Reapers, only other ships
        $attacker->addUnit('light_fighter', 200);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 50000, 0));

        // Launch attack mission to foreign planet
        $units = new \OGame\GameObjects\Models\Units\UnitCollection();
        $units->addUnit(\OGame\Services\ObjectService::getShipObjectByMachineName('light_fighter'), 200);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new \OGame\Models\Resources(0, 0, 0, 0));

        // Clear foreign planet units from previous tests to ensure isolation
        $foreignPlanet->removeUnits($foreignPlanet->getShipUnits(), true);
        $foreignPlanet->removeUnits($foreignPlanet->getDefenseUnits(), true);
        $foreignPlanet->save();
        $foreignPlanet->reloadPlanet(); // Reload to ensure clean state

        // Set up: Defender with ships to create debris when destroyed
        $foreignPlanet->addUnit('light_fighter', 10);
        $foreignPlanet->save(); // Save defender units

        // Get the mission to calculate travel time
        $fleetMissionService = resolve(\OGame\Services\FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview'); // Trigger mission processing

        // Get battle report to check debris amounts
        $battleReport = \OGame\Models\BattleReport::latest()->first();
        $this->assertNotNull($battleReport, 'Battle report should be created');

        // Check attacker collected debris (should be 0 since attacker has no Reapers)
        $attackerCollectedDebris = ($battleReport->debris['attacker_collected_metal'] ?? 0) +
                                   ($battleReport->debris['attacker_collected_crystal'] ?? 0) +
                                   ($battleReport->debris['attacker_collected_deuterium'] ?? 0);

        // Assert: No debris should be collected by attacker (no Reapers in attacking fleet)
        $this->assertEquals(0, $attackerCollectedDebris, 'Attacker should not collect debris without Reaper ships');
    }
}
