<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
use OGame\Models\Planet;
use OGame\Models\Planet\Coordinate;
use OGame\Services\FleetMissionService;
use OGame\Services\ObjectService;
use OGame\Services\SettingsService;
use Tests\AccountTestCase;

/**
 * Verify remaining admin server settings from issue #167.
 */
class AdminServerSettingsOptionsTest extends AccountTestCase
{
    /**
     * @return array<string, mixed>
     */
    private function baseServerSettingsPayload(array $overrides = []): array
    {
        return array_merge([
            '_token' => csrf_token(),
            'universe_name' => 'TestUniverse',
            'fleet_speed_war' => 1,
            'fleet_speed_holding' => 1,
            'fleet_speed_peaceful' => 1,
            'economy_speed' => 1,
            'research_speed' => 1,
            'basic_income_metal' => 30,
            'basic_income_crystal' => 15,
            'basic_income_deuterium' => 0,
            'basic_income_energy' => 0,
            'registration_planet_amount' => 1,
            'planet_fields_bonus' => 0,
            'dark_matter_bonus' => 12000,
            'espionage_probe_capacity_on' => 1,
            'deuterium_consumption' => '0.7',
            'alliance_combat_system_on' => 1,
            'alliance_cooldown_days' => 3,
            'debris_field_from_ships' => 30,
            'debris_field_from_defense' => 0,
            'maximum_moon_chance' => 20,
            'number_of_galaxies' => 4,
            'number_of_systems' => 100,
            'battle_engine' => 'php',
            'hamill_probability' => 1000,
        ], $overrides);
    }

    private function grantAdminRole(): void
    {
        $authUser = auth()->user();
        if ($authUser === null) {
            $this->fail('Not authenticated.');
        }
        $this->artisan('ogamex:admin:assign-role', ['username' => $authUser->username]);
    }

    /**
     * Admin can save new universe settings and SettingsService reflects them.
     */
    public function testAdminCanSaveNewServerSettings(): void
    {
        $this->grantAdminRole();

        $occupiedGalaxy = max(1, (int)(Planet::query()->max('galaxy') ?? 1));
        $occupiedSystem = max(1, (int)(Planet::query()->max('system') ?? 1));
        $numberOfGalaxies = max(4, $occupiedGalaxy);
        $numberOfSystems = max(100, $occupiedSystem);

        $response = $this->post('/admin/server-settings', $this->baseServerSettingsPayload([
            'number_of_galaxies' => $numberOfGalaxies,
            'number_of_systems' => $numberOfSystems,
        ]));

        $response->assertRedirect(route('admin.serversettings.index'));

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $this->assertSame('TestUniverse', $settings->universeName());
        $this->assertSame(12000, $settings->darkMatterBonus());
        $this->assertSame('12000', $settings->get('dark_matter_initial'));
        $this->assertTrue($settings->espionageProbeCapacityOn());
        $this->assertSame(0.7, $settings->deuteriumConsumption());
        $this->assertSame($numberOfGalaxies, $settings->numberOfGalaxies());
        $this->assertSame($numberOfSystems, $settings->numberOfSystems());
    }

    /**
     * Tampered deuterium consumption values are snapped back to the default.
     */
    public function testAdminRejectsInvalidDeuteriumConsumption(): void
    {
        $this->grantAdminRole();

        $this->post('/admin/server-settings', $this->baseServerSettingsPayload([
            'deuterium_consumption' => '0.01',
        ]))->assertRedirect(route('admin.serversettings.index'));

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $this->assertSame(1.0, $settings->deuteriumConsumption());
    }

    /**
     * Shrinking systems/galaxies below existing planets is rejected.
     */
    public function testAdminCannotShrinkUniverseBelowOccupiedCoordinates(): void
    {
        $this->grantAdminRole();

        $coords = $this->planetService->getPlanetCoordinates();
        Planet::query()->where('id', $this->planetService->getPlanetId())->update([
            'galaxy' => 5,
            'system' => 250,
            'planet' => $coords->position,
        ]);

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('number_of_galaxies', 9);
        $settings->set('number_of_systems', 499);

        $response = $this->post('/admin/server-settings', $this->baseServerSettingsPayload([
            'number_of_galaxies' => 4,
            'number_of_systems' => 100,
        ]));

        $response->assertRedirect(route('admin.serversettings.index'));
        $response->assertSessionHas('error');

        $this->assertSame(9, $settings->numberOfGalaxies());
        $this->assertSame(499, $settings->numberOfSystems());
    }

    /**
     * Espionage probes gain cargo capacity of 5 when the setting is enabled.
     */
    public function testEspionageProbeCapacitySettingAffectsCargo(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('espionage_probe_capacity_on', 0);

        $player = $this->planetService->getPlayer();
        if ($player === null) {
            $this->fail('Player not found.');
        }

        $probe = ObjectService::getShipObjectByMachineName('espionage_probe');
        $this->assertSame(0, $probe->properties->capacity->calculate($player)->totalValue);

        $settings->set('espionage_probe_capacity_on', 1);
        $this->assertSame(5, $probe->properties->capacity->calculate($player)->totalValue);
    }

    /**
     * Universe deuterium consumption multiplier reduces fleet fuel usage.
     */
    public function testDeuteriumConsumptionMultiplierAffectsFleetFuel(): void
    {
        $this->planetAddUnit('small_cargo', 10);
        $this->planetAddResources(new \OGame\Models\Resources(0, 0, 100000, 0));

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('deuterium_consumption', '1.0');

        /** @var FleetMissionService $fleetMissionService */
        $fleetMissionService = app(FleetMissionService::class);

        $units = new UnitCollection();
        $units->addUnit(ObjectService::getShipObjectByMachineName('small_cargo'), 10);
        $target = new Coordinate(1, 2, 3);

        $fullConsumption = $fleetMissionService->calculateConsumption(
            $this->planetService,
            $units,
            $target,
            0,
            10
        );

        $settings->set('deuterium_consumption', '0.5');
        $halfConsumption = $fleetMissionService->calculateConsumption(
            $this->planetService,
            $units,
            $target,
            0,
            10
        );

        $this->assertGreaterThan(0, $fullConsumption);
        $this->assertSame((int)round($fullConsumption * 0.5), $halfConsumption);
    }

    /**
     * Fleet check-target rejects systems outside the configured universe size.
     */
    public function testFleetRejectsCoordinatesOutsideConfiguredSystems(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('number_of_systems', 100);

        $this->planetSetObjectLevel('shipyard', 1);
        $this->playerSetResearchLevel('combustion_drive', 1);
        $this->planetAddUnit('small_cargo', 1);

        $response = $this->post('/ajax/fleet/dispatch/check-target', [
            'galaxy' => 1,
            'system' => 150,
            'position' => 5,
            'type' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertSame('failure', $response->json('status'));
        $errors = $response->json('errors');
        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('system', strtolower((string)($errors[0]['message'] ?? '')));
    }

    /**
     * Phalanx scan rejects systems outside the configured universe size.
     */
    public function testPhalanxRejectsCoordinatesOutsideConfiguredSystems(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('number_of_systems', 100);

        $response = $this->post('/ajax/phalanx/scan', [
            'galaxy' => 1,
            'system' => 250,
            'position' => 5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['system']);
    }

    /**
     * Login page shows the configured universe name.
     */
    public function testLoginPageShowsConfiguredUniverseName(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('universe_name', 'Alpha Centauri');

        $this->post('/logout');

        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Alpha Centauri');
    }

    /**
     * Activity logs and cron tasks admin pages are accessible to admins.
     */
    public function testAdminActivityLogsAndCronPagesAccessible(): void
    {
        $this->grantAdminRole();

        $this->get('/admin/activity-logs')->assertStatus(200)->assertSee('Activity logs');
        $this->get('/admin/activity-logs?tab=buildings')->assertStatus(200);
        $this->get('/admin/cron-tasks')
            ->assertStatus(200)
            ->assertSee('Cron tasks')
            ->assertSee('ogamex:scheduler:generate-highscores')
            ->assertDontSee('No scheduled tasks found');
    }

    /**
     * Cron run endpoint rejects commands outside the allowlist.
     */
    public function testCronRunRejectsCommandsOutsideAllowlist(): void
    {
        $this->grantAdminRole();

        $response = $this->post('/admin/cron-tasks/run', [
            '_token' => csrf_token(),
            'command' => 'migrate:fresh',
        ]);

        $response->assertRedirect(route('admin.crontasks.index'));
        $response->assertSessionHas('error');
    }
}
