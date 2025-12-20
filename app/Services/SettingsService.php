<?php

namespace OGame\Services;

use OGame\Models\Setting;

/**
 * Class SettingsService.
 *
 * SettingsService object.
 *
 * @package OGame\Services
 */
class SettingsService
{
    /**
     * Array of setting objects.
     *
     * @var array<string, Setting>
     */
    private array $settings = [];

    /**
     * SettingsService constructor.
     */
    public function __construct()
    {
    }

    /**
     * Load all settings from database and cache locally.
     *
     * @return void
     */
    private function loadFromDatabase(): void
    {
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting;
        }
    }

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param string|int $default
     * @return string
     */
    public function get(string $key, string|int $default = ''): string
    {
        // When a setting is accessed, load everything from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // If it doesn't exist, return default.
        if (empty($this->settings[$key])) {
            return (string)$default;
        }

        return $this->settings[$key]->value;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param string|int $value
     * @return void
     */
    public function set(string $key, string|int $value): void
    {
        // When a setting is accessed, load everything from database.
        // We do it here instead of in constructor so call to database
        // is only made when something on the page actually accesses
        // a settings value.
        if (empty($this->settings)) {
            $this->loadFromDatabase();
        }

        // Check if to be saved value is actually different from current one.
        $currentValue = $this->get($key, '');
        if (!empty($currentValue) && $currentValue === $value) {
            // To be saved value is same as current value, skip update to prevent unnecessary db call.
            return;
        }

        $updated_setting = Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        $this->settings[$key] = $updated_setting;
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function fleetSpeed(): int
    {
        return (int)$this->get('fleet_speed', 1);
    }

    /**
     * Returns the war fleet speed setting.
     *
     * @return int
     */
    public function fleetSpeedWar(): int
    {
        return (int)$this->get('fleet_speed_war', 1);
    }

    /**
     * Returns the holding fleet speed setting.
     *
     * @return int
     */
    public function fleetSpeedHolding(): int
    {
        return (int)$this->get('fleet_speed_holding', 1);
    }

    /**
     * Returns the peaceful fleet speed setting.
     *
     * @return int
     */
    public function fleetSpeedPeaceful(): int
    {
        return (int)$this->get('fleet_speed_peaceful', 1);
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function economySpeed(): int
    {
        return (int)$this->get('economy_speed', 1);
    }

    /**
     * Returns the fleet speed setting.
     *
     * @return int
     */
    public function researchSpeed(): int
    {
        return (int)$this->get('research_speed', 1);
    }

    /**
     * Returns the basic income metal setting.
     *
     * @return int
     */
    public function basicIncomeMetal(): int
    {
        return (int)$this->get('basic_income_metal', 30);
    }

    /**
     * Returns the basic income crystal setting.
     *
     * @return int
     */
    public function basicIncomeCrystal(): int
    {
        return (int)$this->get('basic_income_crystal', 15);
    }

    /**
     * Returns the basic income deuterium setting.
     *
     * @return int
     */
    public function basicIncomeDeuterium(): int
    {
        return (int)$this->get('basic_income_deuterium', 0);
    }

    /**
     * Returns the basic income energy setting.
     *
     * @return int
     */
    public function basicIncomeEnergy(): int
    {
        return (int)$this->get('basic_income_energy', 0);
    }

    /**
     * Returns the amount of planets that should be created for a new player
     * upon registration. Defaults to 1.
     *
     * @return int
     */
    public function registrationPlanetAmount(): int
    {
        return (int)$this->get('registration_planet_amount', 1);
    }

    /**
     * Returns the amount of planet fields bonus given upon planet creation.
     *
     * @return int
     */
    public function planetFieldsBonus(): int
    {
        return (int)$this->get('planet_fields_bonus', 0);
    }

    /**
     * Returns the amount of dark matter given for a new player.
     *
     * @return int
     */
    public function darkMatterBonus(): int
    {
        return (int)$this->get('dark_matter_bonus', 8000);
    }

    /**
     * Returns the status of the Alliance Combat System.
     *
     * @return int
     */
    public function allianceCombatSystemOn(): int
    {
        return (int)$this->get('alliance_combat_system_on', 1);
    }

    /**
     * Returns the percentage of debris field generated from destroyed ships.
     *
     * @return int
     */
    public function debrisFieldFromShips(): int
    {
        return (int)$this->get('debris_field_from_ships', 30);
    }

    /**
     * Returns the percentage of debris field generated from destroyed defensive structures.
     *
     * @return int
     */
    public function debrisFieldFromDefense(): int
    {
        return (int)$this->get('debris_field_from_defense', 0);
    }

    /**
     * Returns the percentage of destroyed ships that become wreck fields.
     *
     * @return int
     */
    public function wreckFieldFromShips(): int
    {
        return (int)$this->get('wreck_field_from_ships', 70);
    }

    /**
     * Returns the minimum resource loss required for wreck field formation.
     *
     * @return int
     */
    public function wreckFieldMinResourcesLoss(): int
    {
        return (int)$this->get('wreck_field_min_resources_loss', 150000);
    }

    /**
     * Returns the minimum fleet percentage that must be destroyed for wreck field formation.
     *
     * @return int
     */
    public function wreckFieldMinFleetPercentage(): int
    {
        return (int)$this->get('wreck_field_min_fleet_percentage', 5);
    }

    /**
     * Returns the lifetime of wreck fields in hours.
     *
     * @return int
     */
    public function wreckFieldLifetimeHours(): int
    {
        return (int)$this->get('wreck_field_lifetime_hours', 72);
    }

    /**
     * Returns the maximum repair time in hours for wreck fields.
     *
     * @return int
     */
    public function wreckFieldRepairMaxHours(): int
    {
        return (int)$this->get('wreck_field_repair_max_hours', 12);
    }

    /**
     * Returns the minimum repair time in minutes for wreck fields.
     *
     * @return int
     */
    public function wreckFieldRepairMinMinutes(): int
    {
        return (int)$this->get('wreck_field_repair_min_minutes', 30);
    }

    /**
     * Returns the status of Deuterium in debris fields.
     *
     * @return int
     */
    public function debrisFieldDeuteriumOn(): int
    {
        return (int)$this->get('debris_field_deuterium_on', 0);
    }

    /**
     * Returns the maximum percentage chance of a moon forming after battle.
     *
     * @return int
     */
    public function maximumMoonChance(): int
    {
        return (int)$this->get('maximum_moon_chance', 20);
    }

    /**
     * Returns the status of ignoring empty systems.
     *
     * @return int
     */
    public function ignoreEmptySystemsOn(): int
    {
        return (int)$this->get('ignore_empty_systems_on', 0);
    }

    /**
     * Returns the status of ignoring inactive systems.
     *
     * @return int
     */
    public function ignoreInactiveSystemsOn(): int
    {
        return (int)$this->get('ignore_inactive_systems_on', 0);
    }

    /**
     * Returns the number of galaxies in the universe.
     *
     * @return int
     */
    public function numberOfGalaxies(): int
    {
        return (int)$this->get('number_of_galaxies', 9);
    }

    /**
     * Returns the name of the universe.
     *
     * @return string
     */
    public function universeName(): string
    {
        return $this->get('universe_name', "Universe");
    }

    /**
     * Returns the battle engine setting.
     *
     * @return string
     */
    public function battleEngine(): string
    {
        return $this->get('battle_engine', 'rust');
    }

    /**
     * Returns if expedition failed outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedEnabled(): bool
    {
        return (bool)$this->get('expedition_failed', 1);
    }

    /**
     * Returns if expedition failed and delay outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedAndDelayEnabled(): bool
    {
        return (bool)$this->get('expedition_failed_and_delay', 1);
    }

    /**
     * Returns if expedition failed and speedup outcome is enabled.
     *
     * @return bool
     */
    public function expeditionFailedAndSpeedupEnabled(): bool
    {
        return (bool)$this->get('expedition_failed_and_speedup', 1);
    }

    /**
     * Returns if expedition gain ships outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainShipsEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_ships', 1);
    }

    /**
     * Returns if expedition gain dark matter outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainDarkMatterEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_dark_matter', 1);
    }

    /**
     * Returns if expedition gain resources outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainResourcesEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_resources', 1);
    }

    /**
     * Returns if expedition gain merchant trade outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainMerchantTradeEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_merchant_trade', 1);
    }

    /**
     * Returns if expedition gain item outcome is enabled.
     *
     * @return bool
     */
    public function expeditionGainItemEnabled(): bool
    {
        return (bool)$this->get('expedition_gain_item', 1);
    }

    /**
     * Returns if expedition loss of fleet outcome is enabled.
     *
     * @return bool
     */
    public function expeditionLossOfFleetEnabled(): bool
    {
        return (bool)$this->get('expedition_loss_of_fleet', 1);
    }

    /**
     * Returns if expedition battle outcome is enabled.
     *
     * @return bool
     */
    public function expeditionBattleEnabled(): bool
    {
        return (bool)$this->get('expedition_battle', 1);
    }

    /**
     * Returns the defense repair rate percentage (0-100).
     * After a battle, destroyed defenses have this percentage chance of being repaired.
     * Default is 70% as per official game rules.
     *
     * @return int
     */
    public function defenseRepairRate(): int
    {
        return (int)$this->get('defense_repair_rate', 70);
    }

    /**
     * Returns the bonus expedition slots setting.
     *
     * @return int
     */
    public function bonusExpeditionSlots(): int
    {
        return (int)$this->get('bonus_expedition_slots', 0);
    }

    /**
     * Returns the expedition rewards multiplier.
     *
     * @return float
     */
    public function expeditionRewardsMultiplier(): float
    {
        return (float)$this->get('expedition_rewards_multiplier', '1.0');
    }

    /**
     * Returns the expedition resource rewards multiplier.
     *
     * @return float
     */
    public function expeditionRewardMultiplierResources(): float
    {
        return (float)$this->get('expedition_reward_multiplier_resources', '1.0');
    }

    /**
     * Returns the expedition ship rewards multiplier.
     *
     * @return float
     */
    public function expeditionRewardMultiplierShips(): float
    {
        return (float)$this->get('expedition_reward_multiplier_ships', '1.0');
    }

    /**
     * Returns the expedition dark matter rewards multiplier.
     *
     * @return float
     */
    public function expeditionRewardMultiplierDarkMatter(): float
    {
        return (float)$this->get('expedition_reward_multiplier_dark_matter', '1.0');
    }

    /**
     * Returns the expedition item rewards multiplier.
     *
     * @return float
     */
    public function expeditionRewardMultiplierItems(): float
    {
        return (float)$this->get('expedition_reward_multiplier_items', '1.0');
    }

    /**
     * Returns the expedition outcome weight for ships (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightShips(): float
    {
        return (float)$this->get('expedition_weight_ships', '22');
    }

    /**
     * Returns the expedition outcome weight for resources (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightResources(): float
    {
        return (float)$this->get('expedition_weight_resources', '32.5');
    }

    /**
     * Returns the expedition outcome weight for delay (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightDelay(): float
    {
        return (float)$this->get('expedition_weight_delay', '7');
    }

    /**
     * Returns the expedition outcome weight for speedup (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightSpeedup(): float
    {
        return (float)$this->get('expedition_weight_speedup', '2');
    }

    /**
     * Returns the expedition outcome weight for nothing/failed (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightNothing(): float
    {
        return (float)$this->get('expedition_weight_nothing', '26.5');
    }

    /**
     * Returns the expedition outcome weight for black hole/fleet loss (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightBlackHole(): float
    {
        return (float)$this->get('expedition_weight_black_hole', '0.3');
    }

    /**
     * Returns the expedition outcome weight for pirates (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightPirates(): float
    {
        return (float)$this->get('expedition_weight_pirates', '0');
    }

    /**
     * Returns the expedition outcome weight for aliens (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightAliens(): float
    {
        return (float)$this->get('expedition_weight_aliens', '0');
    }

    /**
     * Returns the expedition outcome weight for dark matter (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightDarkMatter(): float
    {
        return (float)$this->get('expedition_weight_dark_matter', '9');
    }

    /**
     * Returns the expedition outcome weight for merchant (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightMerchant(): float
    {
        return (float)$this->get('expedition_weight_merchant', '0.7');
    }

    /**
     * Returns the expedition outcome weight for items (0-100 scale, relative).
     *
     * @return float
     */
    public function expeditionWeightItems(): float
    {
        return (float)$this->get('expedition_weight_items', '0');
    }
}
