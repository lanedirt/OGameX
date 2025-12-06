@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') && session('error') != __('You can only deactivate vacation mode after the minimum duration of 48 hours has passed.'))
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
                    <h2>Options - {!! $username !!}</h2>
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
                                            <span>User data</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="two" aria-labelledby="tabGeneral" aria-selected="false" aria-expanded="false">
                                        <a href="#two" id="tabGeneral" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>General</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="three" aria-labelledby="tabRepresentation" aria-selected="false" aria-expanded="false">
                                        <a href="#three" id="tabRepresentation" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>Display</span>
                                        </a>
                                    </li>
                                    <li class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="four" aria-labelledby="tabExtended" aria-selected="false" aria-expanded="false">
                                        <a href="#four" id="tabExtended" class="ui-tabs-anchor" role="presentation" tabindex="-1">
                                            <span>Extended</span>
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
                                            <label class="styled textBeefy" data-element="playername">Players Name</label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">@lang('Your player name:')</label>
                                                <div class="thefield">{!! $username !!}</div>
                                            </div>
                                            @if ($canUpdateUsername)
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">@lang('New player name:')</label>
                                                <div class="thefield">

                                                    <input class="textInput w200 validate[optional,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],minSize[3]]" type="text" maxlength="20" value="" size="30" id="db_character" name="new_username_username">
                                                </div>
                                            </div>
                                            @endif
                                            <div class="fieldwrapper">
                                                <p>@lang('You can change your username once per week.')
                                                @lang('To do so, click on your name or the settings at the top of the screen.')</p>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="userpassword">
                                                Change password
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Enter old password
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" value="" size="20" name="db_password" maxlength="20">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">New password (at least 4 characters)
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[pwMinSize],custom[pwMaxSize]]" type="password" maxlength="20" size="20" name="newpass1" id="newpass1">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Repeat the new password
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" maxlength="20" size="20" name="newpass2">
                                                </div>
                                            </div>
                                            <div class="pw_check">
                                                <p>Password check:</p>
                                                <div class="password-meter">
                                                    <span class="password">Low</span><span class="password">Medium</span><span class="password">High</span>
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
                                                <p>The password should contain the following properties</p>
                                                <ul>
                                                    <li id="password-meter-status-lengtbh">min. 4 characters, max. 20 characters
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-mixed-case">Upper and lower case
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-special-chars">Special characters (e.g. !?:_., )
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                    <li id="password-meter-status-numbers">Numbers
                                                        <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" class="status-checked" style="visibility: hidden;"></li>
                                                </ul>
                                            </div>

                                            <div class="fieldwrapper">
                                                <p>Your password needs to have at least <strong>4 characters</strong> and may not be longer than <strong>20 characters</strong>.</p>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="usermail">Email address</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy" style="padding-top: 1px;">Current email address:</label>
                                                <div class="styled">{{ $current_email }}
                                                    <div class="buttonContainer">
                                                        <span class="validateButtonGift awesome-button shop">Send validation link</span>
                                                    </div>
                                                    <span style="display: none;" class="validateDone errormsg good">Email has been sent successfully!</span>
                                                    <span style="display: none;" class="validateError errormsg bad">Error! Account is already validated or the email could not be sent!</span>
                                                    <span style="display: none;" class="validateErrorCounter errormsg bad">You`ve already requested too many emails!</span>
                                                </div>
                                            </div>

                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">New email address
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[email]]" type="email" value="" size="30" id="db_email" name="db_email">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">New email address <em>(to confirmation)</em>
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200 validate[optional,custom[email]]" type="email" value="" size="30" id="db_email_confirm" name="db_email_confirm" onpaste="return false;">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Enter password <em>(as confirmation)</em>
                                                    :</label>
                                                <div class="thefield">
                                                    <input class="textInput w200" type="password" value="" size="30" name="db_email_password">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <p><b>Warning!</b> After a successful account validation, a renewed change of email address is only possible after a period of <b>7 days</b>.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="two" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabGeneral" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="espionageprobes">
                                                Spy probes
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Number of espionage probes:</label>
                                                <div class="thefield">
                                                    <input type="text" pattern="[0-9]*" class="textInput w50 textCenter textBeefy" value="{{ old('espionage_probes_amount') }}" size="2" maxlength="2" name="espionage_probes_amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="chat">
                                                Chat
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Deactivate chat bar:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="disable_chat_bar" {{ old('disable_chat_bar') }}>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="warnings">
                                                Warnings
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Deactivate Outlaw-Warning on attacks on opponents 5-times stronger:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="disable_outlaw_warning" {{ old('disable_outlaw_warning') }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="three" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabRepresentation" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="general">General</label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Show mobile version
                                                    :</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="show_mobile_version" {{ old('show_mobile_version') }}>
                                                </div>
                                            </div>

                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Show alternative drop downs
                                                    :</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="show_old_dropdowns" {{ old('show_old_dropdowns') }}>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Activate autofocus in the highscores:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="activate_autofocus" {{ old('activate_autofocus') }}>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Always show events:</label>
                                                <div class="thefield">
                                                    <select name="eventsShow" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="1" selected="">
                                                            Hide
                                                        </option>
                                                        <option value="2">
                                                            Above the content
                                                        </option>
                                                        <option value="3">
                                                            Below the content
                                                        </option></select><span class="dropdown currentlySelected w200" rel="dropdown57" style="width: 200px;"><a class="undefined" data-value="1" rel="dropdown57" href="javascript:void(0);">
                                                Hide
                                            </a></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="planets">Your planets</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Sort planets by:</label>
                                                <div class="thefield">
                                                    <select id="sortSetting" name="settings_sort" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="0" selected="">
                                                            Order of emergence
                                                        </option>
                                                        <option value="1">
                                                            Coordinates
                                                        </option>
                                                        <option value="2">
                                                            Alphabet
                                                        </option>
                                                        <option value="3">
                                                            Size
                                                        </option>
                                                        <option value="4">
                                                            Used fields
                                                        </option>
                                                    </select><span class="dropdown currentlySelected w200" rel="dropdown382" style="width: 200px;"><a class="undefined" data-value="0" rel="dropdown382" href="javascript:void(0);">
                                                Order of emergence
                                            </a></span>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Sorting sequence:</label>
                                                <div class="thefield">
                                                    <input type="hidden" id="sortOrderHidden" name="" value="0">
                                                    <select id="sortOrder" name="settings_order" class="w200 dropdownInitialized" style="display: none;">
                                                        <option value="0" selected="">
                                                            up
                                                        </option>
                                                        <option value="1">
                                                            down
                                                        </option>
                                                    </select><span class="dropdown currentlySelected w200" rel="dropdown438" style="width: 200px;"><a class="undefined" data-value="0" rel="dropdown438" href="javascript:void(0);">
                                                up
                                            </a></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="overview">Overview</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Highlight planet information:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="showDetailOverlay">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Animated detail display:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="animatedSliders">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Animated overview:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="animatedOverview">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="overlays">Overlays</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <p>The following settings allow the corresponding overlays to open as an additional browser window instead of within the game.</p>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Notes in an extra window:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="popups[notices]">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Combat reports in an extra window
                                                    :</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="popups[combatreport]">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="messages">Messages</label>
                                        </div>
                                        <div class="group" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Hide pictures in reports:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="spioReportPictures">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Amount of displayed message per page
                                                    :</label>
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
                                                <label class="styled textBeefy">Auctioneer notification
                                                    :</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="auctioneerNotifications">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Create economy messages
                                                    :</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="economyNotifications">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="galaxy">Galaxy</label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Detailed activity display:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" checked="" name="showActivityMinutes" value="1">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">Preserve galaxy/system with planet change:</label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="preserveSystemOnPlanetChange" value="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="four" class="wrap ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="tabExtended" role="tabpanel" aria-hidden="true" style="display: none;">
                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="vacationmode">Vacation Mode</label>
                                        </div>
                                        <div class="group bborder" style="display: block;">
                                            @if($player->isInVacationMode())
                                            <div class="fieldwrapper">
                                                <p style="color: #ffcc00; font-weight: bold;">You are currently in vacation mode.</p>
                                                @if($player->getVacationModeUntil())
                                                    <p>You can deactivate it after: {{ $player->getVacationModeUntil()->format('Y-m-d H:i:s') }}</p>
                                                @endif
                                            </div>
                                            @endif
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">
                                                    @if($player->isInVacationMode())
                                                        Deactivate vacation mode
                                                    @else
                                                        Activate vacation mode
                                                    @endif
                                                </label>
                                                <div class="thefield">
                                                    <input type="checkbox"
                                                           name="urlaubs_modus"
                                                           id="urlaubs_modus"
                                                           class="{{ $player->isInVacationMode() ? 'onVacation' : 'notOnVacation' }}"
                                                           {{ $player->isInVacationMode() ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <p>Vacation mode is designed to protect you during long absences from the game. You can only activate it when none of your fleets are in transit. Building and research orders will be put on hold.</p>
                                                <br>
                                                <p>Once vacation mode is activated, it will protect you from new attacks. Attacks that have already started will, however, continue and your production will be set to zero.</p>
                                                <br>
                                                <p>Vacation mode lasts a minimum of 48 hours. Only after this time expires will you be able to deactivate it.</p>
                                            </div>
                                        </div>

                                        <div class="category fieldwrapper alt bar">
                                            <label class="styled textBeefy" data-element="account">
                                                Your Account
                                            </label>
                                        </div>
                                        <div class="group bborder" style="display: none;">
                                            <div class="fieldwrapper">
                                                <label class="styled textBeefy">
                                                    Delete account
                                                </label>
                                                <div class="thefield">
                                                    <input type="checkbox" name="db_deaktjava">
                                                </div>
                                            </div>
                                            <div class="fieldwrapper">
                                                <p>Check here to have your account marked for automatic deletion after 7 days.</p>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="textCenter">
                                        <input type="submit" class="btn_blue" value="Use settings">
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
                            "alertText": "Not enough characters"},
                        "pwMinSize": {
                            "regex": /^.{ 4,}$/,
                            "alertText": "The entered password is to short (min. 4 characters)"},
                        "pwMaxSize": {
                            "regex": /^.{0, 20}$/,
                            "alertText": "The entered password is to long (max. 20 characters)"},
                        "email":{
                            "regex":/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/,
                            "alertText":"You need to enter a valid email address!"},
                        "noSpecialCharacters":{
                            "regex":/^[a-zA-Z0-9\-_\s]+$/,
                            "alertText": "Contains invalid characters."},
                        "noBeginOrEndUnderscore":{
                            "regex":/^([^_]+(.*[^_])?)?$/,
                            "alertText": "Your name may not start or end with an underscore."},
                        "noBeginOrEndHyphen":{
                            "regex":/^([^\-]+(.*[^\-])?)?$/,
                            "alertText": "Your name may not start or finish with a hyphen."},
                        "noBeginOrEndWhitespace":{
                            "regex":/^([^\s]+(.*[^\s])?)?$/,
                            "alertText": "Your name may not start or end with a space."},
                        "notMoreThanThreeUnderscores":{
                            "regex":/^[^_]*(_[^_]*){0,3}$/,
                            "alertText": "Your name may not contain more than 3 underscores in total."},
                        "notMoreThanThreeHyphen":{
                            "regex":/^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText": "Your name may not contain more than 3 hyphens."},
                        "notMoreThanThreeWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText": "Your name may not include more than 3 spaces in total."},
                        "noCollocateUnderscores":{
                            "regex":/^[^_]*(_[^_]+)*_?$/,
                            "alertText": "You may not use two or more underscores one after the other."},
                        "noCollocateHyphen":{
                            "regex":/^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText": "You may not use two or more hyphens consecutively."},
                        "noCollocateWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText": "You may not use two or more spaces one after the other."}

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
            preferenceLoca = {"changeNameTitle":"New player name","changeNameQuestion":"Are you sure you want to change your player name to %newName%?","planetMoveQuestion":"Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be cancelled. Do you really want to continue with this job?","tabDisabled":"To use this option you have to validated and cannot be in vacation mode!","vacationModeQuestion":"Do you want to activate vacation mode? You can only end your vacation after 2 days."};
            initPreferences();

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
