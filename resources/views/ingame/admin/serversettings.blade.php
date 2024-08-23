@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Server settings')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Server settings')</h2>
            </div>
            <form action="{{ route('admin.serversettings.update') }}" name="form" method="post">
                {{ csrf_field() }}
                <div class="content">
                    <div class="buddylistContent">
                        <p class="box_highlight textCenter no_buddies">@lang('Basic settings.')</p>
                        
                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Universe name:')</label>
                                <div class="thefield">
                                    <input class="textInput w200" type="text" maxlength="20" value="{{ $universe_name }}" size="30" name="universe_name">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">@lang('You can change the server settings below. Changes will be applied immediately.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Economy speed:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $economy_speed }}" size="2" maxlength="9" name="economy_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Research speed:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $research_speed }}" size="2" maxlength="9" name="research_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Fleet speed:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $fleet_speed }}" size="2" maxlength="9" name="fleet_speed">
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Planet fields bonus')</label>
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

                        <p class="box_highlight textCenter no_buddies">@lang('Note: basic income values below are multiplied by economy speed.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Basic metal income per hour:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_metal }}" size="6" name="basic_income_metal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_metal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Basic crystal income per hour:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_crystal }}" size="6" name="basic_income_crystal"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_crystal * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Basic deuterium income per hour:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_deuterium }}" size="6" name="basic_income_deuterium"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_deuterium * $economy_speed) }})
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Basic energy income per hour:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $basic_income_energy }}" size="6" name="basic_income_energy"> (= {{ \OGame\Facades\AppUtil::formatNumber($basic_income_energy * $economy_speed) }})
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">@lang('New player settings.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Amount of planets to give to player upon registration')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $registration_planet_amount }}" size="6" name="registration_planet_amount">
                                </div>
                            </div>

                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Dark Matter bonus:')</label>
                                <div class="thefield">
                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ $dark_matter_bonus }}" size="6" name="dark_matter_bonus">
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">@lang('Battle settings.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Alliance Combat System:')</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkAllianceCombatSystem" name="alliance_combat_system_on" value="1" {{ $alliance_combat_system_on ? 'checked' : '' }}>
                                        <label for="square-checkAllianceCombatSystem"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Destroyed ships in debris fields:')</label>
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
                                <label class="styled textBeefy">@lang('Defensive structures in debris fields:')</label>
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
                                <label class="styled textBeefy">@lang('Deuterium in debris fields:')</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkBoxDeuteriumInDebris" name="debris_field_deuterium_on" value="1" {{ $debris_field_deuterium_on ? 'checked' : '' }}>
                                        <label for="square-checkBoxDeuteriumInDebris"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                        </div>

                        <p class="box_highlight textCenter no_buddies">@lang('Galaxy settings.')</p>

                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Empty systems are ignored:')</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkIgnoreEmptySystems" name="ignore_empty_systems_on" value="1" {{ $ignore_empty_systems_on ? 'checked' : '' }}>
                                        <label for="square-checkIgnoreEmptySystems"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy">@lang('Inactive systems are ignored:')</label>
                                <div class="thefield">
                                    <square-checkbox class="square-checkbox">
                                        <input type="checkbox" id="square-checkIgnoreInactiveSystems" name="ignore_inactive_systems_on" value="1" {{ $ignore_inactive_systems_on ? 'checked' : '' }}>
                                        <label for="square-checkIgnoreInactiveSystems"></label>
                                    </square-checkbox>
                                </div>
                            </div>
                            <div class="fieldwrapper" style="margin-bottom: 50px;">
                                <label class="styled textBeefy">@lang('Number of galaxies:')</label>
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
                            <input type="submit" class="btn_blue" value="@lang('Save settings')">
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
