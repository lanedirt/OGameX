@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>{{ __('t_ingame.admin.title') }}</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>{{ __('t_ingame.admin.title') }}</h2>
            </div>
            <form action="{{ route('admin.serversettings.update') }}" name="form" method="post">
                {{ csrf_field() }}
                <div class="content">
                    <div class="buddylistContent">
                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_basic') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.universe_name') }}</label>
                                <div class="thefield">
                                    <input class="textInput w200" type="text" maxlength="20" value="{{ $universe_name }}" size="30" name="universe_name">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_changes_note') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.economy_speed') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $economy_speed }}" size="2" maxlength="9" name="economy_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.research_speed') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $research_speed }}" size="2" maxlength="9" name="research_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.fleet_speed_war') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $fleet_speed_war }}" size="2" maxlength="9" name="fleet_speed_war">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.fleet_speed_holding') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $fleet_speed_holding }}" size="2" maxlength="9" name="fleet_speed_holding">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.fleet_speed_peaceful') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $fleet_speed_peaceful }}" size="2" maxlength="9" name="fleet_speed_peaceful">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.planet_fields_bonus') }}</label>
                                <div class="thefield">
                                    <select name="planet_fields_bonus" class="w130" data-value="{{ $planet_fields_bonus }}">
                                        <option value="0"{{ $planet_fields_bonus == 0 ? ' selected' : '' }}>+0</option>
                                        <option value="10"{{ $planet_fields_bonus == 10 ? ' selected' : '' }}>+10</option>
                                        <option value="25"{{ $planet_fields_bonus == 25 ? ' selected' : '' }}>+25</option>
                                        <option value="30"{{ $planet_fields_bonus == 30 ? ' selected' : '' }}>+30</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_income_note') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.income_metal') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_metal }}" size="6" name="basic_income_metal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_metal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.income_crystal') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_crystal }}" size="6" name="basic_income_crystal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_crystal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.income_deuterium') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_deuterium }}" size="6" name="basic_income_deuterium"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_deuterium * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.income_energy') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_energy }}" size="6" name="basic_income_energy"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_energy * $economy_speed) }})
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_new_player') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.registration_planet_amount') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $registration_planet_amount }}" size="6" name="registration_planet_amount">
                                </div>
                            </div>

                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.dm_bonus') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $dark_matter_bonus }}" size="6" name="dark_matter_bonus">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_dm_regen') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <div class="smallFont" style="margin-bottom: 15px; padding: 10px; background-color: #1e2328; border: 1px solid #4a5568; border-radius: 4px;">
                                    {{ __('t_ingame.admin.dm_regen_description') }}
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.dm_regen_enabled') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkDarkMatterRegenEnabled" name="dark_matter_regen_enabled" value="1" {{ $dark_matter_regen_enabled ? 'checked' : '' }}>
                                        <label for="square-checkDarkMatterRegenEnabled"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.dm_regen_amount') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w100 textCenter textBeefy" value="{{ $dark_matter_regen_amount }}" size="10" name="dark_matter_regen_amount">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.dm_regen_period') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w100 textCenter textBeefy" value="{{ $dark_matter_regen_period }}" size="10" name="dark_matter_regen_period">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_relocation') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.relocation_cost') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w100 textCenter textBeefy" value="{{ $planet_relocation_cost }}" size="10" name="planet_relocation_cost">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.relocation_duration') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w100 textCenter textBeefy" value="{{ $planet_relocation_duration }}" size="10" name="planet_relocation_duration">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_alliance') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.alliance_cooldown') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $alliance_cooldown_days }}" size="2" maxlength="3" name="alliance_cooldown_days">
                                    <div class="smallFont" style="margin-top: 5px;">{{ __('t_ingame.admin.alliance_cooldown_desc') }}</div>
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_battle') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.battle_engine') }}</label>
                                <div class="thefield">
                                <select name="battle_engine" class="w130" data-value="{{ $battle_engine }}">
                                    <option value="rust"{{ $battle_engine == 'rust' ? ' selected' : '' }}>Rust</option>
                                        <option value="php"{{ $battle_engine == 'php' ? ' selected' : '' }}>PHP</option>
                                    </select>
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.battle_engine_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.acs') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkAllianceCombatSystem" name="alliance_combat_system_on" value="1" {{ $alliance_combat_system_on ? 'checked' : '' }}>
                                        <label for="square-checkAllianceCombatSystem"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.debris_ships') }}</label>
                                <div class="thefield">
                                    <select name="debris_field_from_ships" class="w130" data-value="{{ $debris_field_from_ships }}">
                                        <option value="0"{{ $debris_field_from_ships == 0 ? ' selected' : '' }}>0%</option>
                                        <option value="30"{{ $debris_field_from_ships == 30 ? ' selected' : '' }}>30%</option>
                                        <option value="40"{{ $debris_field_from_ships == 40 ? ' selected' : '' }}>40%</option>
                                        <option value="50"{{ $debris_field_from_ships == 50 ? ' selected' : '' }}>50%</option>
                                        <option value="60"{{ $debris_field_from_ships == 60 ? ' selected' : '' }}>60%</option>
                                        <option value="70"{{ $debris_field_from_ships == 70 ? ' selected' : '' }}>70%</option>
                                        <option value="80"{{ $debris_field_from_ships == 80 ? ' selected' : '' }}>80%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.debris_defense') }}</label>
                                <div class="thefield">
                                    <select name="debris_field_from_defense" class="w130" data-value="{{ $debris_field_from_defense }}">
                                        <option value="0"{{ $debris_field_from_defense == 0 ? ' selected' : '' }}>0%</option>
                                        <option value="30"{{ $debris_field_from_defense == 30 ? ' selected' : '' }}>30%</option>
                                        <option value="40"{{ $debris_field_from_defense == 40 ? ' selected' : '' }}>40%</option>
                                        <option value="50"{{ $debris_field_from_defense == 50 ? ' selected' : '' }}>50%</option>
                                        <option value="60"{{ $debris_field_from_defense == 60 ? ' selected' : '' }}>60%</option>
                                        <option value="70"{{ $debris_field_from_defense == 70 ? ' selected' : '' }}>70%</option>
                                        <option value="80"{{ $debris_field_from_defense == 80 ? ' selected' : '' }}>80%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.debris_deuterium') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkBoxDeuteriumInDebris" name="debris_field_deuterium_on" value="1" {{ $debris_field_deuterium_on ? 'checked' : '' }}>
                                        <label for="square-checkBoxDeuteriumInDebris"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.wreck_min_resources') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w100 textCenter textBeefy" value="{{ $wreck_field_min_resources_loss }}" size="10" name="wreck_field_min_resources_loss">
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.wreck_min_resources_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.wreck_min_fleet_pct') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $wreck_field_min_fleet_percentage }}" size="3" maxlength="3" name="wreck_field_min_fleet_percentage">
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.wreck_min_fleet_pct_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.wreck_lifetime') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $wreck_field_lifetime_hours }}" size="3" maxlength="3" name="wreck_field_lifetime_hours">
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.wreck_lifetime_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.wreck_repair_max') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $wreck_field_repair_max_hours }}" size="3" maxlength="3" name="wreck_field_repair_max_hours">
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.wreck_repair_max_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.wreck_repair_min') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $wreck_field_repair_min_minutes }}" size="3" maxlength="3" name="wreck_field_repair_min_minutes">
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.wreck_repair_min_desc') }}</div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.moon_chance') }}</label>
                                <div class="thefield">
                                    <select name="maximum_moon_chance" class="w130" data-value="{{ $maximum_moon_chance }}">
                                        <option value="0"{{ $maximum_moon_chance == 0 ? ' selected' : '' }}>0%</option>
                                        <option value="10"{{ $maximum_moon_chance == 10 ? ' selected' : '' }}>10%</option>
                                        <option value="20"{{ $maximum_moon_chance == 20 ? ' selected' : '' }}>20%</option>
                                        <option value="30"{{ $maximum_moon_chance == 30 ? ' selected' : '' }}>30%</option>
                                        <option value="40"{{ $maximum_moon_chance == 40 ? ' selected' : '' }}>40%</option>
                                        <option value="50"{{ $maximum_moon_chance == 50 ? ' selected' : '' }}>50%</option>
                                        <option value="60"{{ $maximum_moon_chance == 60 ? ' selected' : '' }}>60%</option>
                                        <option value="70"{{ $maximum_moon_chance == 70 ? ' selected' : '' }}>70%</option>
                                        <option value="80"{{ $maximum_moon_chance == 80 ? ' selected' : '' }}>80%</option>
                                        <option value="90"{{ $maximum_moon_chance == 90 ? ' selected' : '' }}>90%</option>
                                        <option value="100"{{ $maximum_moon_chance == 100 ? ' selected' : '' }}>100%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.hamill_probability') }}</label>
                                <div class="thefield" style="display: flex; gap: 10px; align-items: center;">
                                    <input type="text" pattern="^[0-9]+$" placeholder="1000" class="textInput w100 textCenter textBeefy" size="6" name="hamill_probability" value="{{ $hamill_probability }}">
                                    <span style="color: #999; font-size: 0.9em;">
                                        (Default: 1000 = 0.1% chance | Testing: 10 = 10% chance)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_expedition') }}</p>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_expedition_slots') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <div class="smallFont" style="margin-bottom: 15px; padding: 10px; background-color: #1e2328; border: 1px solid #4a5568; border-radius: 4px;">
                                    {{ __('t_ingame.admin.expedition_slots_desc') }}
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.expedition_bonus_slots') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $bonus_expedition_slots }}" size="2" maxlength="9" name="bonus_expedition_slots">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.expedition_multiplier_res') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_reward_multiplier_resources }}" size="6" name="expedition_reward_multiplier_resources">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.expedition_multiplier_ships') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_reward_multiplier_ships }}" size="6" name="expedition_reward_multiplier_ships">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.expedition_multiplier_dm') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_reward_multiplier_dark_matter }}" size="6" name="expedition_reward_multiplier_dark_matter">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.expedition_multiplier_items') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_reward_multiplier_items }}" size="6" name="expedition_reward_multiplier_items">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_expedition_weights') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <div class="smallFont" style="margin-bottom: 15px; padding: 10px; background-color: #1e2328; border: 1px solid #4a5568; border-radius: 4px;">
                                    {{ __('t_ingame.admin.expedition_weights_desc') }}
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <div class="smallFont" style="margin-bottom: 15px; padding: 10px; background-color: #2c3e50; border: 1px solid #3498db; border-radius: 4px;">
                                    <strong>{{ __('t_ingame.admin.expedition_weights_defaults') }}</strong><br>
                                    {{ __('t_ingame.admin.expedition_weights_values') }}
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_ships') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_ships }}" size="6" name="expedition_weight_ships">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_resources') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_resources }}" size="6" name="expedition_weight_resources">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_delay') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_delay }}" size="6" name="expedition_weight_delay">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_speedup') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_speedup }}" size="6" name="expedition_weight_speedup">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_nothing') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_nothing }}" size="6" name="expedition_weight_nothing">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_black_hole') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_black_hole }}" size="6" name="expedition_weight_black_hole">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_pirates') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_pirates }}" size="6" name="expedition_weight_pirates">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_aliens') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_aliens }}" size="6" name="expedition_weight_aliens">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_dm') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_dark_matter }}" size="6" name="expedition_weight_dark_matter">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_merchant') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_merchant }}" size="6" name="expedition_weight_merchant">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.weight_items') }}</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*\.?[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $expedition_weight_items }}" size="6" name="expedition_weight_items">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_highscore') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.highscore_admin_visible') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkHighscoreAdminVisible" name="highscore_admin_visible" value="1" {{ $highscore_admin_visible ? 'checked' : '' }}>
                                        <label for="square-checkHighscoreAdminVisible"></label>
                                    </square-checkbox>
                                </div>
                                <div class="smallFont">{{ __('t_ingame.admin.highscore_admin_visible_desc') }}</div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">{{ __('t_ingame.admin.section_galaxy') }}</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.galaxy_ignore_empty') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkIgnoreEmptySystems" name="ignore_empty_systems_on" value="1" {{ $ignore_empty_systems_on ? 'checked' : '' }}>
                                        <label for="square-checkIgnoreEmptySystems"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.galaxy_ignore_inactive') }}</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkIgnoreInactiveSystems" name="ignore_inactive_systems_on" value="1" {{ $ignore_inactive_systems_on ? 'checked' : '' }}>
                                        <label for="square-checkIgnoreInactiveSystems"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper" style="margin-bottom: 50px;">
                                <label class="styled textBeefy">{{ __('t_ingame.admin.galaxy_count') }}</label>
                                <div class="thefield">
                                    <select name="number_of_galaxies" class="w130" data-value="{{ $number_of_galaxies }}">
                                        <option value="5"{{ $number_of_galaxies == 5 ? ' selected' : '' }}>5</option>
                                        <option value="6"{{ $number_of_galaxies == 6 ? ' selected' : '' }}>6</option>
                                        <option value="7"{{ $number_of_galaxies == 7 ? ' selected' : '' }}>7</option>
                                        <option value="8"{{ $number_of_galaxies == 8 ? ' selected' : '' }}>8</option>
                                        <option value="9"{{ $number_of_galaxies == 9 ? ' selected' : '' }}>9</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="footer">
                        <div class="textCenter">
                            <input type="submit" class="btn_blue" value="{{ __('t_ingame.admin.save') }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <script language="javascript">
            initBBCodes();
            initOverlays();
        </script>
    </div>

@endsection
