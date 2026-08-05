<?php

namespace Tests\Feature\FleetDispatch;

use Illuminate\Support\Facades\Date;
use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\FleetMission;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\FleetDispatchTestCase;

/**
 * Regression test for issue #922: when an inactive player is deleted while another player's fleet
 * is in flight toward their planet, deletion abandons the planet (bypassing the active-missions
 * guard via $force). The safety net is the `planet_id_to === null` check in GameMission::process(),
 * which lives outside this feature. This test pins that end-to-end behavior so a future change to
 * that external guard would break here.
 */
class DeleteInactivePlayersFleetReturnTest extends FleetDispatchTestCase
{
    /**
     * @var int The mission type for the test (attack).
     */
    protected int $missionType = 1;

    /**
     * @var string The mission name for the test, displayed in UI.
     */
    protected string $missionName = 'Attack';

    /**
     * Reset feature settings after each test to avoid state leaking between tests.
     */
    protected function tearDown(): void
    {
        $settingsService = resolve(SettingsService::class);
        $settingsService->set('inactive_player_deletion_days', 0);
        $settingsService->set('attack_block_until', 0);
        parent::tearDown();
    }

    /**
     * Prepare the attacker planet so it can dispatch an attack fleet.
     *
     * @return void
     */
    protected function basicSetup(): void
    {
        $this->planetAddUnit('light_fighter', 5);
        $this->playerSetResearchLevel('computer_technology', object_level: 1);

        $settingsService = resolve(SettingsService::class);
        $settingsService->set('economy_speed', 8);
        $settingsService->set('fleet_speed_war', 1);
        $settingsService->set('fleet_speed_holding', 1);
        $settingsService->set('fleet_speed_peaceful', 1);
        $settingsService->set('attack_block_until', 0);

        // Add deuterium so the dispatch is not blocked by fuel capacity restrictions.
        $this->planetAddResources(new Resources(0, 0, 1000000, 0));
    }

    /**
     * An enemy fleet already in flight toward an inactive player's planet must turn around and
     * return home when that player is deleted (and their planet abandoned) mid-flight.
     */
    public function testIncomingEnemyFleetReturnsHomeWhenTargetPlayerDeleted(): void
    {
        $this->basicSetup();

        // Attacker (current player) sends an attack fleet toward another player's planet.
        $unitCollection = new UnitCollection();
        $unitCollection->addUnit(ObjectService::getUnitObjectByMachineName('light_fighter'), 1);
        $targetPlanet = $this->sendMissionToOtherPlayerCleanPlanet($unitCollection, new Resources(0, 0, 0, 0));
        $targetPlayerId = $targetPlanet->getPlayer()->getId();
        $attackerPlanetId = $this->planetService->getPlanetId();

        // Capture the outgoing attack mission before it arrives.
        $fleetMissionService = resolve(FleetMissionService::class, ['player' => $this->planetService->getPlayer()]);
        $mission = $fleetMissionService->getActiveFleetMissionsForCurrentPlayer()->first();
        $this->assertNotNull($mission, 'Attack mission should exist');
        $missionId = $mission->id;
        $arrival = $mission->time_arrival;

        // Make the target player inactive and run the deletion command while the fleet is in flight.
        // This deletes the account and abandons the planet, which nulls the incoming mission's
        // planet_id_to (the active-missions guard is bypassed via $force during account deletion).
        resolve(SettingsService::class)->set('inactive_player_deletion_days', 35);
        $targetUser = User::findOrFail($targetPlayerId);
        $targetUser->time = (string) Date::now()->subDays(40)->timestamp;
        $targetUser->save();

        // @phpstan-ignore-next-line
        $this->artisan('ogamex:scheduler:delete-inactive-players')->assertSuccessful();

        $this->assertDatabaseMissing('users', ['id' => $targetPlayerId]);
        $this->assertDatabaseMissing('planets', ['id' => $targetPlanet->getPlanetId()]);

        // Advance to arrival and process the mission.
        $this->travelTo(Date::createFromTimestamp($arrival + 10));
        $this->reloadApplication();
        $this->get('/overview')->assertStatus(200);

        // The attack mission must be processed, and the fleet turned around into a return mission
        // heading back to the attacker's own planet.
        $originalMission = FleetMission::findOrFail($missionId);
        $this->assertEquals(1, $originalMission->processed, 'Original attack mission should be processed');

        $returnMission = FleetMission::where('parent_id', $missionId)->where('canceled', 0)->first();
        $this->assertNotNull($returnMission, 'A return mission should be created when the target planet no longer exists');
        $this->assertEquals($attackerPlanetId, $returnMission->planet_id_to, 'Return mission must head back to the attacker home planet');
    }
}
