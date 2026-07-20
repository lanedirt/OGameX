<?php

namespace Tests\Feature;

use OGame\GameObjects\Models\Units\UnitCollection;
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
     * Admin can save new universe settings and SettingsService reflects them.
     */
    public function testAdminCanSaveNewServerSettings(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $response = $this->post('/admin/server-settings', [
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
        ]);

        $response->assertRedirect(route('admin.serversettings.index'));

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $this->assertSame('TestUniverse', $settings->universeName());
        $this->assertSame(12000, $settings->darkMatterBonus());
        $this->assertSame('12000', $settings->get('dark_matter_initial'));
        $this->assertTrue($settings->espionageProbeCapacityOn());
        $this->assertSame(0.7, $settings->deuteriumConsumption());
        $this->assertSame(4, $settings->numberOfGalaxies());
        $this->assertSame(100, $settings->numberOfSystems());
    }

    /**
     * Espionage probes gain cargo capacity of 5 when the setting is enabled.
     */
    public function testEspionageProbeCapacitySettingAffectsCargo(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->set('espionage_probe_capacity_on', 0);

        $probe = ObjectService::getShipObjectByMachineName('espionage_probe');
        $this->assertSame(0, $probe->properties->capacity->calculate($this->planetService->getPlayer())->totalValue);

        $settings->set('espionage_probe_capacity_on', 1);
        $this->assertSame(5, $probe->properties->capacity->calculate($this->planetService->getPlayer())->totalValue);
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
     * Activity logs and cron tasks admin pages are accessible to admins.
     */
    public function testAdminActivityLogsAndCronPagesAccessible(): void
    {
        $this->artisan('ogamex:admin:assign-role', ['username' => auth()->user()->username]);

        $this->get('/admin/activity-logs')->assertStatus(200)->assertSee('Activity logs');
        $this->get('/admin/activity-logs?tab=buildings')->assertStatus(200);
        $this->get('/admin/cron-tasks')
            ->assertStatus(200)
            ->assertSee('Cron tasks')
            ->assertSee('ogamex:scheduler:generate-highscores')
            ->assertDontSee('No scheduled tasks found');
    }
}
