@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif


    @if (session('error') && session('error') != __('t_ingame.options.msg_vacation_min_duration'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>
    <div id="preferencescomponent" class="maincontent">
        <div id="preferences">
            <div id="inhalt">
                <div id="planet">
                    <h2>{{ __('t_ingame.options.page_title') }} - {!! $username !!}</h2>
                </div>
                <div class="c-left"></div>
                <div class="c-right"></div>

                <div id="content" style="color:#fff;">
                    <div class="sectioncontent">
                        <div class="contentzs ui-tabs ui-widget ui-widget-content ui-corner-all" id="preferencesTabs">
                            <div class="tabwrapper">
                                <ul class="tabsbelow ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" id="tabs-pref" role="tablist">
                                    <li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="one" aria-labelledby="tabUserdata" aria-selected="true" aria-expanded="true">
                                        <a href="#one" id="tabUserdata" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>{{ __('t_ingame.options.tab_userdata') }}</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="two" aria-labelledby="tabGeneral" aria-selected="false" aria-expanded="false">
                                        <a href="#two" id="tabGeneral" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>{{ __('t_ingame.options.tab_general') }}</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="three" aria-labelledby="tabRepresentation" aria-selected="false" aria-expanded="false">
                                        <a href="#three" id="tabRepresentation" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>{{ __('t_ingame.options.tab_display') }}</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="four" aria-labelledby="tabExtended" aria-selected="false" aria-expanded="false">
                                        <a href="#four" id="tabExtended" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>{{ __('t_ingame.options.tab_extended') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <form autocomplete="off" method="post" name="prefs" id="prefs" class="formValidation" action="{{ route('options.save') }}">
                                {{ csrf_field() }}
                                <!--<input type="hidden" name="mode" value="save">
                            <input type="hidden" id="selectedTab" name="selectedTab" value="0">
                            <input type="hidden" name="token" value="1deca78fc6abd579d0444cd354eb4d8b">-->

                                <div class="content">

                                    <div id="one" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabUserdata" role="tabpanel" aria-hidden="false" style="display: block;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="playername">{{ __('t_ingame.options.section_playername') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.your_player_name') }}</label>
                                                <div class="thefield">{!! $username !!}</div>
                                            </div>
                                            @if ($canUpdateUsername)
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.new_player_name') }}</label>
                                                <div class="thefield">

                                                    <input class="textInput w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" type="text" maxlength="20" value="" size="30" id="db_character" name="new_username_username">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="fieldwrapper">
                                                <p>{{ __('t_ingame.options.username_change_once_week') }}
                                                {{ __('t_ingame.options.username_change_hint') }}</p>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="userpassword">
                                                {{ __('t_ingame.options.section_password') }}
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.old_password') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" value="" size="20" name="db_password" maxlength="20">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.new_password') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[pwMinSize],custom[pwMaxSize]]" type="password" maxlength="20" size="20" name="newpass1" id="newpass1">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.repeat_password') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" maxlength="20" size="20" name="newpass2">
                                                </div>
                                            </div>
                                            <div class="pw_check">
                                                <p>{{ __('t_ingame.options.password_check') }}</p>
                                                <div class="password-meter">
                                                    <span class="password">{{ __('t_ingame.options.password_strength_low') }}</span><span class="password">{{ __('t_ingame.options.password_strength_medium') }}</span><span class="password">{{ __('t_ingame.options.password_strength_high') }}</span>
                                                </div>
                                                <div id="password-meter">
                                                    <span class="password weak"></span>
                                                    <span class="password medium"></span>
                                                    <span class="password best"></span>
                                                </div>
                                                <div class="pw_arrow">
                                                    <span id="password-meter-rating-low" class="password arrow"></span>
                                                    <span id="password-meter-rating-medium" class="password"></span>
                                                    <span id="password-meter-rating-high" class="password"></span>
                                                </div>
                                            </div>
                                            <div class="password_prop">
                                                <p>{{ __('t_ingame.options.password_properties_title') }}</p>
                                                <ul>
                                                    <li id="password-meter-status-lengtbh">{{ __('t_ingame.options.password_min_max') }}
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-mixed-case">{{ __('t_ingame.options.password_mixed_case') }}
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-special-chars">{{ __('t_ingame.options.password_special_chars') }}
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-numbers">{{ __('t_ingame.options.password_numbers') }}
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                </ul>
                                            </div>

                                            <div class="fieldwrapper">
                                                <p>{!! __('t_ingame.options.password_length_hint') !!}</p>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="usermail">{{ __('t_ingame.options.section_email') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy" style="padding-top: 1px;">{{ __('t_ingame.options.current_email') }}</label>
                                                <div class="styled">{{ $current_email }}
                                                    <div class="buttonContainer">
                                                        <span class="validateButtonGift awesome-button shop">{{ __('t_ingame.options.send_validation_link') }}</span>
                                                    </div>
                                                    <span style="display: none;" class="validateDone errormsg good">{{ __('t_ingame.options.email_sent_success') }}</span>
                                                    <span style="display: none;" class="validateError errormsg bad">{{ __('t_ingame.options.email_sent_error') }}</span>
                                                    <span style="display: none;" class="validateErrorCounter errormsg bad">{{ __('t_ingame.options.email_too_many_requests') }}</span>
                                                </div>
                                            </div>

                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.new_email') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[email]]" type="email" value="" size="30" id="db_email" name="db_email">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.new_email_confirm') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[email]]" type="email" value="" size="30" id="db_email_confirm" name="db_email_confirm" onpaste="return false;">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.enter_password_confirm') }}</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" value="" size="30" name="db_email_password">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <p>{!! __('t_ingame.options.email_warning') !!}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="two" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabGeneral" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="espionageprobes">
                                                {{ __('t_ingame.options.section_spy_probes') }}
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.spy_probes_amount') }}</label>
                                                <div class="thefield">
                                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ old('espionage_probes_amount', $espionage_probes_amount ?? '') }}" size="4" maxlength="4" name="espionage_probes_amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="chat">
                                                {{ __('t_ingame.options.section_chat') }}
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.disable_chat_bar') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="disable_chat_bar" {{ old('disable_chat_bar') }}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="warnings">
                                                {{ __('t_ingame.options.section_warnings') }}
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.disable_outlaw_warning') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="disable_outlaw_warning" {{ old('disable_outlaw_warning') }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="three" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabRepresentation" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="general">{{ __('t_ingame.options.section_general_display') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.show_mobile_version') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="show_mobile_version" {{ old('show_mobile_version') }}>
                                                </div>
                                            </div>

                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.show_alt_dropdowns') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="show_old_dropdowns" {{ old('show_old_dropdowns') }}>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.activate_autofocus') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="activate_autofocus" {{ old('activate_autofocus') }}>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.always_show_events') }}</label>
                                                <div class="thefield">
                                                    <select name="eventsShow" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="1" selected="">
                                                            {{ __('t_ingame.options.events_hide') }}
                                                        </option>
                                                        <option value="2">
                                                            {{ __('t_ingame.options.events_above') }}
                                                        </option>
                                                        <option value="3">
                                                            {{ __('t_ingame.options.events_below') }}
                                                        </option></select><span class="dropdown currentlySelected w200" rel="dropdown57" style="width: 200px;"><a class="undefined" data-value="1" rel="dropdown57" href="javascript:void(0);">
                                                {{ __('t_ingame.options.events_hide') }}
                                            </a></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="planets">{{ __('t_ingame.options.section_planets') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.sort_planets_by') }}</label>
                                                <div class="thefield">
                                                    <select id="sortSetting" name="settings_sort" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="0" selected="">
                                                            {{ __('t_ingame.options.sort_emergence') }}
                                                        </option>
                                                        <option value="1">
                                                            {{ __('t_ingame.options.sort_coordinates') }}
                                                        </option>
                                                        <option value="2">
                                                            {{ __('t_ingame.options.sort_alphabet') }}
                                                        </option>
                                                        <option value="3">
                                                            {{ __('t_ingame.options.sort_size') }}
                                                        </option>
                                                        <option value="4">
                                                            {{ __('t_ingame.options.sort_used_fields') }}
                                                        </option>
                                                    </select><span class="dropdown currentlySelected w200" rel="dropdown382" style="width: 200px;"><a class="undefined" data-value="0" rel="dropdown382" href="javascript:void(0);">
                                                {{ __('t_ingame.options.sort_emergence') }}
                                            </a></span>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.sort_sequence') }}</label>
                                                <div class="thefield">
                                                    <input type="hidden" id="sortOrderHidden" name="" value="0">
                                                    <select id="sortOrder" name="settings_order" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="0" selected="">
                                                            {{ __('t_ingame.options.sort_order_up') }}
                                                        </option>
                                                        <option value="1">
                                                            {{ __('t_ingame.options.sort_order_down') }}
                                                        </option>
                                                    </select><span class="dropdown currentlySelected w200" rel="dropdown438" style="width: 200px;"><a class="undefined" data-value="0" rel="dropdown438" href="javascript:void(0);">
                                                {{ __('t_ingame.options.sort_order_up') }}
                                            </a></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="overview">{{ __('t_ingame.options.section_overview_display') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.highlight_planet_info') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="showDetailOverlay">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.animated_detail_display') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="animatedSliders">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.animated_overview') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="animatedOverview">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="overlays">{{ __('t_ingame.options.section_overlays') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <p>{{ __('t_ingame.options.overlays_hint') }}</p>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.popup_notes') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="popups[notices]">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.popup_combat_reports') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="popups[combatreport]">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="messages">{{ __('t_ingame.options.section_messages_display') }}</label>
                                        </div>
                                        <div class="group" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.hide_report_pictures') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="spioReportPictures">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.msgs_per_page') }}</label>
                                                <div class="thefield">
                                                    <select name="msgResultsPerPage" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="10" selected="">10
                                                        </option>
                                                        <option value="25">25
                                                        </option>
                                                        <option value="50">50
                                                        </option>
                                                    </select><span class="dropdown currentlySelected w200" rel="dropdown178" style="width: 200px;"><a class="undefined" data-value="10" rel="dropdown178" href="javascript:void(0);">10
                                            </a></span>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.auctioneer_notifications') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="auctioneerNotifications">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.economy_notifications') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="economyNotifications">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="galaxy">{{ __('t_ingame.options.section_galaxy_display') }}</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.detailed_activity') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="showActivityMinutes" value="1">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">{{ __('t_ingame.options.preserve_galaxy_system') }}</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="preserveSystemOnPlanetChange" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="four" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabExtended" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="vacationmode">{{ __('t_ingame.options.section_vacation') }}</label>
                                        </div>
                                        <div class="group bborder" style="">
                                            <div class="fieldwrapper" id="techinfo">
                                                @if($player->isInVacationMode())
                                                <p style="color: #ffcc00; font-weight: bold;">{{ __('t_ingame.options.vacation_active') }}</p>
                                                @if($player->getVacationModeUntil())
                                                    <p>{{ __('t_ingame.options.vacation_can_deactivate_after') }} {{ $player->getVacationModeUntil()->format('Y-m-d H:i:s') }}</p>
                                                @endif
                                                <br>
                                                @elseif(!$player->canActivateVacationMode())
                                                <p style="color: #ff0000;">{{ __('t_ingame.options.vacation_cannot_activate') }}</p>
                                                <br>
                                                @endif

                                                <p>{{ __('t_ingame.options.vacation_description_1') }}</p>
                                                <br>
                                                <p>{{ __('t_ingame.options.vacation_description_2') }}</p>
                                                <br>
                                                <p>{{ __('t_ingame.options.vacation_description_3') }}</p>
                                                <br>
                                            </div>
                                            <div class="fieldwrapper center">
                                                <div class="tooltip" style="cursor: default;" data-tooltip-title="{{ __('t_ingame.options.vacation_tooltip_min_days') }}">
                                                    <button id="vacation-mode-button" type="button" class="ui-button ui-corner-all ui-widget" {{ (!$player->isInVacationMode() && !$player->canActivateVacationMode()) ? 'disabled' : '' }}>
                                                        @if($player->isInVacationMode())
                                                            {{ __('t_ingame.options.vacation_deactivate_btn') }}
                                                        @else
                                                            {{ __('t_ingame.options.vacation_activate_btn') }}
                                                        @endif
                                                    </button>
                                                </div>
                                                <input type="checkbox" name="urlaubs_modus" id="urlaubs_modus" class="{{ $player->isInVacationMode() ? 'onVacation' : 'notOnVacation' }}" {{ $player->isInVacationMode() ? 'checked' : '' }} style="display: none;">
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="account">
                                                {{ __('t_ingame.options.section_account') }}
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">
                                                    {{ __('t_ingame.options.delete_account') }}
                                                </label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="db_deaktjava">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <p>{{ __('t_ingame.options.delete_account_hint') }}</p>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="textCenter">
                                        <input type="submit" class="btn_blue" value="{{ __('t_ingame.options.use_settings') }}">
                                    </div>
                                </div>
                                <div class="footer"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        (function($) {
            $.fn.validationEngineLanguage = function() {};
            $.validationEngineLanguage = {
                newLang: function() {
                    $.validationEngineLanguage.allRules = 	{
                        "minSize": {
                            "regex": "none",
                            "alertText": @json(__('t_ingame.options.validation_not_enough_chars'))},
                        "pwMinSize": {
                            "regex": /^.{ 4,}$/,
                            "alertText": @json(__('t_ingame.options.validation_pw_too_short'))},
                        "pwMaxSize": {
                            "regex": /^.{0, 20}$/,
                            "alertText": @json(__('t_ingame.options.validation_pw_too_long'))},
                        "email":{
                            "regex":/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                            "alertText": @json(__('t_ingame.options.validation_invalid_email'))},
                        "noSpecialCharacters":{
                            "regex":/^[a-zA-Z0-9\-_\s]+$/,
                            "alertText": @json(__('t_ingame.options.validation_special_chars'))},
                        "noBeginOrEndUnderscore":{
                            "regex":/^([^_]+(.*[^_])?)?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_begin_end_underscore'))},
                        "noBeginOrEndHyphen":{
                            "regex":/^([^\-]+(.*[^\-])?)?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_begin_end_hyphen'))},
                        "noBeginOrEndWhitespace":{
                            "regex":/^([^\s]+(.*[^\s])?)?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_begin_end_whitespace'))},
                        "notMoreThanThreeUnderscores":{
                            "regex":/^[^_]*(_[^_]*){0,3}$/,
                            "alertText": @json(__('t_ingame.options.validation_max_three_underscores'))},
                        "notMoreThanThreeHyphen":{
                            "regex":/^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText": @json(__('t_ingame.options.validation_max_three_hyphens'))},
                        "notMoreThanThreeWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText": @json(__('t_ingame.options.validation_max_three_spaces'))},
                        "noCollocateUnderscores":{
                            "regex":/^[^_]*(_[^_]+)*_?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_consecutive_underscores'))},
                        "noCollocateHyphen":{
                            "regex":/^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_consecutive_hyphens'))},
                        "noCollocateWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText": @json(__('t_ingame.options.validation_no_consecutive_spaces'))}

                    }
                }
            }
            $.validationEngineLanguage.newLang();
        })(jQuery);
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            passwordMinLength = 4;
            passwordMaxLength = 20;
            customSorting = 5;
            openGroup = 0;
            selectedTab = 0;
            tabsDisabled = false;
            moveInProgress = false;
            preferenceLoca = {!! json_encode([
                'changeNameTitle'    => __('t_ingame.options.js_change_name_title'),
                'changeNameQuestion' => __('t_ingame.options.js_change_name_question'),
                'planetMoveQuestion' => __('t_ingame.options.js_planet_move_question'),
                'tabDisabled'        => __('t_ingame.options.js_tab_disabled'),
                'vacationModeQuestion' => __('t_ingame.options.js_vacation_question'),
            ]) !!};
            initPreferences();

            // Show fadeBox for vacation mode success messages
            @if (session('success') == __('t_ingame.options.msg_vacation_activated'))
                fadeBox('{{ session('success') }}', false);
            @endif
            @if (session('success') == __('t_ingame.options.msg_vacation_deactivated'))
                fadeBox('{{ session('success') }}', false);
            @endif

            $(".validateButtonGift").click(function() {
                $(".validateButtonGift").hide();
                $.get(
                        '#emailvalidate.php?ajax=1&email=email@localdomain.local',
                        function (data) {
                            if (data == 1) {
                                $(".validateDone").show();
                            } else if(data == 0) {
                                $(".validateErrorCounter").show();
                            } else {
                                $(".validateError").show();
                            }
                        }
                );
            });
        });
    </script>
@endsection
