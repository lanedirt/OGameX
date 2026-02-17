<?php

namespace OGame\Http\Controllers\Admin;

use Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use OGame\Enums\HighscoreTypeEnum;
use OGame\Http\Controllers\OGameController;
use OGame\Services\PlayerService;
use OGame\Services\SettingsService;

class ServerSettingsController extends OGameController
{
    /**
     * Shows the server settings page.
     *
     * @param PlayerService $player
     * @param SettingsService $settingsService
     * @return View
     */
    public function index(PlayerService $player, SettingsService $settingsService): View
    {
        return view('ingame.admin.serversettings')->with([
            'fleet_speed_war' => $settingsService->fleetSpeedWar(),
            'fleet_speed_holding' => $settingsService->fleetSpeedHolding(),
            'fleet_speed_peaceful' => $settingsService->fleetSpeedPeaceful(),
            'economy_speed' => $settingsService->economySpeed(),
            'research_speed' => $settingsService->researchSpeed(),
            'basic_income_metal' => $settingsService->basicIncomeMetal(),
            'basic_income_crystal' => $settingsService->basicIncomeCrystal(),
            'basic_income_deuterium' => $settingsService->basicIncomeDeuterium(),
            'basic_income_energy' => $settingsService->basicIncomeEnergy(),
            'registration_planet_amount' => $settingsService->registrationPlanetAmount(),
            'universe_name' => $settingsService->universeName(),
            'planet_fields_bonus' => $settingsService->planetFieldsBonus(),
            'dark_matter_bonus' => $settingsService->darkMatterBonus(),
            'alliance_combat_system_on' => $settingsService->allianceCombatSystemOn(),
            'alliance_cooldown_days' => $settingsService->allianceCooldownDays(),
            'debris_field_from_ships' => $settingsService->debrisFieldFromShips(),
            'debris_field_from_defense' => $settingsService->debrisFieldFromDefense(),
            'debris_field_deuterium_on' => $settingsService->debrisFieldDeuteriumOn(),
            'wreck_field_min_resources_loss' => $settingsService->wreckFieldMinResourcesLoss(),
            'wreck_field_min_fleet_percentage' => $settingsService->wreckFieldMinFleetPercentage(),
            'wreck_field_lifetime_hours' => $settingsService->wreckFieldLifetimeHours(),
            'wreck_field_repair_max_hours' => $settingsService->wreckFieldRepairMaxHours(),
            'wreck_field_repair_min_minutes' => $settingsService->wreckFieldRepairMinMinutes(),
            'maximum_moon_chance' => $settingsService->maximumMoonChance(),
            'ignore_empty_systems_on' => $settingsService->ignoreEmptySystemsOn(),
            'ignore_inactive_systems_on' => $settingsService->ignoreInactiveSystemsOn(),
            'number_of_galaxies' => $settingsService->numberOfGalaxies(),
            'battle_engine' => $settingsService->battleEngine(),
            'dark_matter_regen_enabled' => (bool)$settingsService->get('dark_matter_regen_enabled', 0),
            'dark_matter_regen_amount' => (int)$settingsService->get('dark_matter_regen_amount', 150000),
            'dark_matter_regen_period' => (int)$settingsService->get('dark_matter_regen_period', 604800),
            'planet_relocation_cost' => (int)$settingsService->get('planet_relocation_cost', 240000),
            'planet_relocation_duration' => (int)$settingsService->get('planet_relocation_duration', 86400),
            'bonus_expedition_slots' => $settingsService->bonusExpeditionSlots(),
            'expedition_reward_multiplier_resources' => $settingsService->expeditionRewardMultiplierResources(),
            'expedition_reward_multiplier_ships' => $settingsService->expeditionRewardMultiplierShips(),
            'expedition_reward_multiplier_dark_matter' => $settingsService->expeditionRewardMultiplierDarkMatter(),
            'expedition_reward_multiplier_items' => $settingsService->expeditionRewardMultiplierItems(),
            'expedition_weight_ships' => $settingsService->expeditionWeightShips(),
            'expedition_weight_resources' => $settingsService->expeditionWeightResources(),
            'expedition_weight_delay' => $settingsService->expeditionWeightDelay(),
            'expedition_weight_speedup' => $settingsService->expeditionWeightSpeedup(),
            'expedition_weight_nothing' => $settingsService->expeditionWeightNothing(),
            'expedition_weight_black_hole' => $settingsService->expeditionWeightBlackHole(),
            'expedition_weight_pirates' => $settingsService->expeditionWeightPirates(),
            'expedition_weight_aliens' => $settingsService->expeditionWeightAliens(),
            'expedition_weight_dark_matter' => $settingsService->expeditionWeightDarkMatter(),
            'expedition_weight_merchant' => $settingsService->expeditionWeightMerchant(),
            'expedition_weight_items' => $settingsService->expeditionWeightItems(),
            'hamill_probability' => $settingsService->hamillManoeuvreChance(),
            'highscore_admin_visible' => $settingsService->highscoreAdminVisible(),
        ]);
    }

    /**
     * Updates the server settings.
     *
     * @param SettingsService $settingsService
     * @return RedirectResponse
     */
    public function update(SettingsService $settingsService): RedirectResponse
    {
        $settingsService->set('fleet_speed_war', request('fleet_speed_war'));
        $settingsService->set('fleet_speed_holding', request('fleet_speed_holding'));
        $settingsService->set('fleet_speed_peaceful', request('fleet_speed_peaceful'));
        $settingsService->set('economy_speed', request('economy_speed'));
        $settingsService->set('research_speed', request('research_speed'));

        $settingsService->set('basic_income_metal', request('basic_income_metal'));
        $settingsService->set('basic_income_crystal', request('basic_income_crystal'));
        $settingsService->set('basic_income_deuterium', request('basic_income_deuterium'));
        $settingsService->set('basic_income_energy', request('basic_income_energy'));

        $settingsService->set('registration_planet_amount', request('registration_planet_amount'));

        $settingsService->set('planet_fields_bonus', request('planet_fields_bonus'));
        $settingsService->set('dark_matter_bonus', request('dark_matter_bonus'));
        $settingsService->set('alliance_combat_system_on', request('alliance_combat_system_on', 0));
        $settingsService->set('alliance_cooldown_days', request('alliance_cooldown_days', 3));
        $settingsService->set('debris_field_from_ships', request('debris_field_from_ships'));
        $settingsService->set('debris_field_from_defense', request('debris_field_from_defense'));
        $settingsService->set('debris_field_deuterium_on', request('debris_field_deuterium_on', 0));
        $settingsService->set('wreck_field_min_resources_loss', request('wreck_field_min_resources_loss', 150000));
        $settingsService->set('wreck_field_min_fleet_percentage', request('wreck_field_min_fleet_percentage', 5));
        $settingsService->set('wreck_field_lifetime_hours', request('wreck_field_lifetime_hours', 72));
        $settingsService->set('wreck_field_repair_max_hours', request('wreck_field_repair_max_hours', 12));
        $settingsService->set('wreck_field_repair_min_minutes', request('wreck_field_repair_min_minutes', 30));
        $settingsService->set('maximum_moon_chance', request('maximum_moon_chance'));

        $settingsService->set('ignore_empty_systems_on', request('ignore_empty_systems_on', 0));
        $settingsService->set('ignore_inactive_systems_on', request('ignore_inactive_systems_on', 0));
        $settingsService->set('number_of_galaxies', request('number_of_galaxies'));

        $settingsService->set('battle_engine', request('battle_engine'));

        $settingsService->set('dark_matter_regen_enabled', request('dark_matter_regen_enabled', 0));
        $settingsService->set('dark_matter_regen_amount', request('dark_matter_regen_amount', 150000));
        $settingsService->set('dark_matter_regen_period', request('dark_matter_regen_period', 604800));
        $settingsService->set('planet_relocation_cost', request('planet_relocation_cost', 240000));
        $settingsService->set('planet_relocation_duration', request('planet_relocation_duration', 86400));

        $settingsService->set('bonus_expedition_slots', request('bonus_expedition_slots', 0));
        $settingsService->set('expedition_reward_multiplier_resources', request('expedition_reward_multiplier_resources', 1.0));
        $settingsService->set('expedition_reward_multiplier_ships', request('expedition_reward_multiplier_ships', 1.0));
        $settingsService->set('expedition_reward_multiplier_dark_matter', request('expedition_reward_multiplier_dark_matter', 1.0));
        $settingsService->set('expedition_reward_multiplier_items', request('expedition_reward_multiplier_items', 1.0));
        $settingsService->set('expedition_weight_ships', request('expedition_weight_ships', 22));
        $settingsService->set('expedition_weight_resources', request('expedition_weight_resources', 32.5));
        $settingsService->set('expedition_weight_delay', request('expedition_weight_delay', 7));
        $settingsService->set('expedition_weight_speedup', request('expedition_weight_speedup', 2));
        $settingsService->set('expedition_weight_nothing', request('expedition_weight_nothing', 26.5));
        $settingsService->set('expedition_weight_black_hole', request('expedition_weight_black_hole', 0.3));
        $settingsService->set('expedition_weight_pirates', request('expedition_weight_pirates', 0));
        $settingsService->set('expedition_weight_aliens', request('expedition_weight_aliens', 0));
        $settingsService->set('expedition_weight_dark_matter', request('expedition_weight_dark_matter', 9));
        $settingsService->set('expedition_weight_merchant', request('expedition_weight_merchant', 0.7));
        $settingsService->set('expedition_weight_items', request('expedition_weight_items', 0));

        $settingsService->set('hamill_manoeuvre_chance', max(1, (int)request('hamill_probability', 1000)));

        $settingsService->set('highscore_admin_visible', request('highscore_admin_visible', 0));

        // Clear highscore cache when admin visibility setting changes
        $this->clearHighscoreCache();

        return redirect()->route('admin.serversettings.index')->with('success', __('Changes saved!'));
    }

    /**
     * Clear all highscore-related cache entries.
     */
    private function clearHighscoreCache(): void
    {
        // Clear player count cache for both admin visible states
        Cache::forget('highscore-player-count-0');
        Cache::forget('highscore-player-count-1');

        // Clear highscore list cache for all types and pages (up to 100 pages should cover most cases)
        foreach (HighscoreTypeEnum::cases() as $type) {
            for ($page = 1; $page <= 100; $page++) {
                Cache::forget(sprintf('highscores-%s-%d-0', $type->name, $page));
                Cache::forget(sprintf('highscores-%s-%d-1', $type->name, $page));
            }
        }
    }
}
