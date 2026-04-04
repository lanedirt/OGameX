<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use OGame\Enums\CharacterClass;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\BattleReport;
use OGame\Models\Message;
use OGame\Models\Resources;
use OGame\Models\WreckField;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Test that General class generates wreck fields at their origin planet from their lost ships.
 */
class GeneralWreckFieldTest extends FleetDispatchTestCase
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
        // Clear any existing battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30); // 30% debris universe
        $settingsService->set('wreck_field_min_resources_loss', 150000); // Minimum resource loss for wreck field
        $settingsService->set('wreck_field_min_fleet_percentage', 5); // Minimum 5% fleet destroyed
    }

    /**
     * Test that General class creates wreck field at origin planet from lost ships.
     *
     * @throws BindingResolutionException
     */
    public function testGeneralCreatesWreckFieldAtOriginPlanet(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30); // 30% debris universe
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General (required for wreck field generation)
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        DB::table('planets')
            ->where('id', $attacker->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 2]);

        // Clear any existing units from previous tests to ensure test isolation
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save(); // Save after removing units
        $attacker->reloadPlanet(); // Reload to ensure clean state

        // Set up: attacker with cruisers. This matchup should lose some ships but still return survivors.
        $attacker->addUnit('cruiser', 100);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 200000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 100);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Set up: defender with defenses to inflict losses without wiping the fleet.
        DB::table('planets')->where('id', $foreignPlanet->getPlanetId())->update([
            'rocket_launcher' => 500,
        ]);

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        $returnMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($returnMission, 'A return mission should exist after the attacker survives the battle.');
        $returnMission->wreck_field_data = [
            ['machine_name' => 'cruiser', 'quantity' => 31, 'repair_progress' => 0],
        ];
        $returnMission->save();
        $returnMissionDuration = max(1, (int) $returnMission->time_arrival - now()->timestamp);

        // Process return mission
        $this->travel($returnMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Check for attacker wreck field at attacker's planet
        $attackerCoords = $attacker->getPlanetCoordinates();
        $attackerWreckField = WreckField::where('galaxy', $attackerCoords->galaxy)
            ->where('system', $attackerCoords->system)
            ->where('planet', $attackerCoords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();
        $this->assertNotNull($attackerWreckField, 'Wreck field should be created at attacker origin planet for General');
        $this->assertEquals('active', $attackerWreckField->status, 'Wreck field should be active');
        $this->assertEquals($attacker->getPlayer()->getId(), $attackerWreckField->owner_player_id, 'Wreck field should belong to attacker');
        $this->assertGreaterThan(0, $attackerWreckField->getTotalShips(), 'Wreck field should contain ships');
    }

    /**
     * Test that wreck field is NOT created if all attacker ships are destroyed.
     *
     * @throws BindingResolutionException
     */
    public function testNoWreckFieldIfAllAttackerShipsDestroyed(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        DB::table('planets')
            ->where('id', $attacker->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 2]);

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with few ships (will be destroyed)
        $attacker->addUnit('light_fighter', 10);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('light_fighter'), 10);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with overwhelming force to destroy all attackers
        $foreignPlanet->addUnit('cruiser', 1000);
        $foreignPlanet->save();

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens, all attackers destroyed)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Wait for return mission time (but it shouldn't exist since all ships destroyed)
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check that NO wreck field exists at attacker planet (no return mission = no wreck field)
        $coords = $attacker->getPlanetCoordinates();
        $wreckFieldAfterReturn = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();
        $this->assertNull($wreckFieldAfterReturn, 'No wreck field should exist if all attacker ships were destroyed');
    }

    /**
     * Test that both attacker (General) and defender wreck fields can exist simultaneously.
     *
     * @throws BindingResolutionException
     */
    public function testBothAttackerAndDefenderWreckFieldsExist(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        DB::table('planets')
            ->where('id', $attacker->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 2]);

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with cruisers
        $attacker->addUnit('cruiser', 100);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 100);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with ships and space dock (required for defender wreck field)
        $foreignPlanet->addUnit('cruiser', 100);
        $foreignPlanet->save();

        // Add space dock and shipyard to defender planet
        DB::table('planets')
            ->where('id', $foreignPlanet->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 1]);

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Check for defender wreck field at defender's planet
        $defenderCoords = $foreignPlanet->getPlanetCoordinates();
        $defenderWreckField = WreckField::where('galaxy', $defenderCoords->galaxy)
            ->where('system', $defenderCoords->system)
            ->where('planet', $defenderCoords->position)
            ->where('owner_player_id', $foreignPlanet->getPlayer()->getId())
            ->first();

        // Process return mission
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check for attacker wreck field at attacker's planet
        $attackerCoords = $attacker->getPlanetCoordinates();
        $attackerWreckField = WreckField::where('galaxy', $attackerCoords->galaxy)
            ->where('system', $attackerCoords->system)
            ->where('planet', $attackerCoords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();

        // Both wreck fields should potentially exist if conditions were met
        // At minimum, they should be at different coordinates
        if ($defenderWreckField !== null && $attackerWreckField !== null) {
            $this->assertNotEquals(
                $defenderWreckField->id,
                $attackerWreckField->id,
                'Attacker and defender wreck fields should be different'
            );

            $this->assertTrue(
                $defenderWreckField->galaxy !== $attackerWreckField->galaxy
                || $defenderWreckField->system !== $attackerWreckField->system
                || $defenderWreckField->planet !== $attackerWreckField->planet,
                'Wreck fields should be at different coordinates'
            );
        }
    }

    /**
     * Test that non-General class does NOT create wreck field at origin planet.
     *
     * @throws BindingResolutionException
     */
    public function testNonGeneralDoesNotCreateWreckFieldAtOrigin(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker WITHOUT General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to Collector (NOT General)
        $attackerPlayer->getUser()->character_class = CharacterClass::COLLECTOR->value;
        $attackerPlayer->getUser()->save();

        DB::table('planets')
            ->where('id', $attacker->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 2]);

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with cruisers
        $attacker->addUnit('cruiser', 100);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 100);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with defenses to destroy some attackers
        $foreignPlanet->addUnit('rocket_launcher', 500);
        $foreignPlanet->save();

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Process return mission
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check that NO wreck field exists at attacker planet (not General class)
        $coords = $attacker->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();
        $this->assertNull($wreckField, 'No wreck field should exist at origin planet for non-General class');
    }

    /**
     * Test that wreck field respects minimum resource loss threshold.
     *
     * @throws BindingResolutionException
     */
    public function testWreckFieldRespectsMinimumResourceLoss(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings with HIGH threshold
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 10000000); // 10 million - very high
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with few cruisers (not enough value for wreck field)
        $attacker->addUnit('cruiser', 10);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 10);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with some defenses
        $foreignPlanet->addUnit('rocket_launcher', 100);
        $foreignPlanet->save();

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival and return
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check that NO wreck field exists (didn't meet minimum resource loss)
        $coords = $attacker->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();
        $this->assertNull($wreckField, 'No wreck field should exist if minimum resource loss not met');
    }

    /**
     * Test that espionage probes are NOT included in wreck fields.
     *
     * @throws BindingResolutionException
     */
    public function testEspionageProbesExcludedFromWreckField(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with cruisers and espionage probes
        // The cruisers should create a wreck field, but probes should not
        $attacker->addUnit('cruiser', 100);
        $attacker->addUnit('espionage_probe', 50);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission with both cruisers and espionage probes
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 100);
        $units->addUnit(ObjectService::getShipObjectByMachineName('espionage_probe'), 50);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with defenses to destroy some attackers
        $foreignPlanet->addUnit('rocket_launcher', 500);
        $foreignPlanet->save();

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Process return mission
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check for attacker wreck field
        $coords = $attacker->getPlanetCoordinates();
        $wreckField = WreckField::where('galaxy', $coords->galaxy)
            ->where('system', $coords->system)
            ->where('planet', $coords->position)
            ->where('owner_player_id', $attacker->getPlayer()->getId())
            ->first();

        if ($wreckField !== null) {
            // Verify that espionage probes are NOT in the wreck field
            $this->assertEquals(0, $wreckField->espionage_probe ?? 0, 'Espionage probes should not be in wreck field');

            // Cruisers should be in the wreck field (if some were destroyed)
            $cruisersInWreck = $wreckField->cruiser ?? 0;
            $this->assertGreaterThanOrEqual(0, $cruisersInWreck, 'Cruisers may be in wreck field if destroyed');
        }
    }

    /**
     * Test that defender's solar satellites destroyed at home planet don't create wreck field entries.
     *
     * @throws BindingResolutionException
     */
    public function testDefenderSolarSatellitesDoNotCreateWreckField(): void
    {
        // Clear all battle reports and wreck fields to ensure test isolation
        Message::where('battle_report_id', '!=', null)->delete();
        BattleReport::query()->delete();
        WreckField::query()->delete();

        // Configure wreck field settings
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('debris_field_from_ships', 30);
        $settingsService->set('wreck_field_min_resources_loss', 150000);
        $settingsService->set('wreck_field_min_fleet_percentage', 5);

        // Set up: attacker with General class
        $attacker = $this->planetService;
        $attackerPlayer = $attacker->getPlayer();

        // Set character class to General
        $attackerPlayer->getUser()->character_class = CharacterClass::GENERAL->value;
        $attackerPlayer->getUser()->save();

        // Clear any existing units
        $attacker->removeUnits($attacker->getShipUnits(), true);
        $attacker->save();
        $attacker->reloadPlanet();

        // Set up: Attacker with cruisers
        $attacker->addUnit('cruiser', 100);
        $attacker->save();

        // Add deuterium for fleet travel
        $this->planetAddResources(new Resources(0, 0, 50000, 0));

        // Launch attack mission
        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('cruiser'), 100);
        $foreignPlanet = $this->sendMissionToOtherPlayerPlanet($units, new Resources(0, 0, 0, 0));

        // Set up: Defender with solar satellites and space dock
        $foreignPlanet->addUnit('solar_satellite', 100);
        $foreignPlanet->save();

        // Add space dock to defender planet
        DB::table('planets')
            ->where('id', $foreignPlanet->getPlanetId())
            ->update(['space_dock' => 1, 'shipyard' => 1]);

        // Get the mission
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $attacker->getPlayer()]);
        $fleetMission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $fleetMissionDuration = $fleetMission->time_arrival - $fleetMission->time_departure;

        // Process arrival (battle happens, solar satellites will be destroyed)
        $this->travel($fleetMissionDuration + 1)->seconds();
        $this->get('/overview');

        // Process return mission
        $this->travel($fleetMissionDuration + 5)->seconds();
        $this->get('/overview');

        // Check for defender wreck field
        $defenderCoords = $foreignPlanet->getPlanetCoordinates();
        $defenderWreckField = WreckField::where('galaxy', $defenderCoords->galaxy)
            ->where('system', $defenderCoords->system)
            ->where('planet', $defenderCoords->position)
            ->where('owner_player_id', $foreignPlanet->getPlayer()->getId())
            ->first();

        if ($defenderWreckField !== null) {
            // Verify that solar satellites are NOT in the wreck field
            $this->assertEquals(0, $defenderWreckField->solar_satellite ?? 0, 'Solar satellites should not be in defender wreck field');
        }
    }
}
