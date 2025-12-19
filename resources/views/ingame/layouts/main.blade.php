@php /** @var OGame\Services\PlayerService $currentPlayer */ @endphp
@php /** @var OGame\Services\SettingsService $settings */ @endphp
        <!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!--
     ===========================================
       ____   _____                     __   __
      / __ \ / ____|                    \ \ / /
     | |  | | |  __  __ _ _ __ ___   ___ \ V /
     | |  | | | |_ |/ _` | '_ ` _ \ / _ \ > <
     | |__| | |__| | (_| | | | | | |  __// . \
      \____/ \_____|\__,_|_| |_| |_|\___/_/ \_\
     ===========================================

     Powered by OGameX - Explore the universe! Conquer your enemies!
     GitHub: https://github.com/lanedirt/OGameX
     Version: {{ \OGame\Facades\GitInfoUtil::getAppVersionBranchCommit() }}

    This application is released under the MIT License. For more details, visit the GitHub repository.
    -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="/img/icons/20da7e6c416e6cd5f8544a73f588e5.png"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Language" content="en"/>
    <meta name="ogame-session" content="3c442273a6de4c8f79549e78f4c3ca50e7ea7580"/>
    <meta name="ogame-version" content="{{ \OGame\Facades\GitInfoUtil::getAppVersion() }}"/>
    <meta name="ogame-timestamp" content="1513426692"/>
    <meta name="ogame-universe" content="s1"/>
    <meta name="ogame-universe-name" content="Home"/>
    <meta name="ogame-universe-speed" content="{{ $settings->economySpeed() }}"/>
    <meta name="ogame-universe-speed-fleet" content="{{ $settings->fleetSpeed() }}"/>
    <meta name="ogame-language" content="en"/>
    <meta name="ogame-donut-galaxy" content="1"/>
    <meta name="ogame-donut-system" content="1"/>
    <meta name="ogame-player-id" content="{{ $currentPlayer->getId() }}"/>
    <meta name="ogame-player-name" content="{{ $currentPlayer->getUsername() }}"/>
    <meta name="ogame-alliance-id" content=""/>
    <meta name="ogame-alliance-name" content=""/>
    <meta name="ogame-alliance-tag" content=""/>
    <!-- TODO: update with current planet details -->
    <meta name="ogame-planet-id" content="{{ $currentPlanet->getPlanetId() }}"/>
    <meta name="ogame-planet-name" content="{{ $currentPlanet->getPlanetName() }}"/>
    <meta name="ogame-planet-coordinates" content="{{ $currentPlanet->getPlanetCoordinates()->asString() }}"/>
    <meta name="ogame-planet-type" content="planet"/>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ mix('css/ingame.css') }}">
    <script src="{{ mix('js/ingame.min.js') }}"></script>

    <script type="text/javascript">
        // Define timerHandler globally to prevent simpleCountdown errors
        if (!window.timerHandler) {
            window.timerHandler = {
                timers: [],
                callbacks: [],
                register: function(timer) {
                    this.timers.push(timer);
                },
                unregister: function(timer) {
                    var index = this.timers.indexOf(timer);
                    if (index > -1) {
                        this.timers.splice(index, 1);
                    }
                },
                appendCallback: function(callback) {
                    this.callbacks.push(callback);
                },
                init: function() {
                    // Initialize timer handler
                },
                stop: function() {
                    // Stop all timers
                }
            };
        }

        // Define LocalizationStrings for time formatting
        if (!window.LocalizationStrings) {
            window.LocalizationStrings = {
                timeunits: {
                    short: {
                        day: 'd',
                        hour: 'h',
                        minute: 'm',
                        second: 's'
                    },
                    long: {
                        day: 'day',
                        hour: 'hour',
                        minute: 'minute',
                        second: 'second'
                    }
                }
            };
        }

        window.token = "{{ csrf_token() }}";
        var inventoryObj;
        $.holdReady(true);

        var s = setInterval(function () {
            if (typeof initEmpireEquipment === "function") {
                $.holdReady(false);
                clearInterval(s);
            }
        }, 1);
    </script>

    <!-- Removed all custom close button CSS to restore normal jQuery UI behavior -->
</head>
<body id="{{ !empty($body_id) ? $body_id : 'ingamepage' }}" class="ogame lang-en default no-touch">
<div id="initial_welcome_dialog" title="Welcome to OGame!" style="display: none;">
    To help your game start get moving quickly, we've assigned you the name Commodore Nebula. You can change this at any
    time by clicking on the username.<br/>
    Fleet Command has left you information on your first steps in your inbox, to help you be well-equipped for your
    start.<br/>
    <br/>
    Have fun playing!
</div>
@if ($currentPlayer->isAdmin())
    @include ('ingame.layouts.admin-menu', ['currentPlayer' => $currentPlayer])
@endif
<div id="siteHeader"></div>
<div id="pageContent">
    <div id="top">
        <div id="pageReloader" onclick="javascript: redirectOverview();"></div>
        <div id="headerbarcomponent" class="">
            <div id="bar">
                <ul>
                    <li id="playerName">
                        @lang('Player'):
                        <selected-language-icon
                                style="background-image: url('/img/flags/a176fcd6f3e3de2bed6a73a8b1d5e7.png');"></selected-language-icon>

                        <span class="textBeefy">
                                <a href="{{ route('changenick.overlay') }}"
                                   class="overlay textBeefy"
                                   data-overlay-title="Change player name"
                                   data-overlay-popup-width="400"
                                   data-overlay-popup-height="200"
                                >
                                    {!! $currentPlayer->getUsername() !!}
                                </a>
                            </span>
                    </li>
                    <li>
                        <a href="{{ route('highscore.index') }}" accesskey="">@lang('Highscore')</a>
                        ({{ $highscoreRank }})
                    </li>
                    <li>
                        <a href="{{ route('notes.overlay') }}"
                           class="overlay" data-overlay-title="My notes"
                           data-overlay-class="notices"
                           data-overlay-popup-width="750"
                           data-overlay-popup-height="480"
                           accesskey="">
                            @lang('Notes')</a>
                    </li>
                    <li>
                        <a class=""
                           accesskey=""
                           href="{{ route('buddies.index') }}"
                        >
                            @lang('Buddies')@if($buddyRequestCount > 0) <span style="color: white;">({{ $buddyRequestCount }})</span>@endif</a>
                    </li>
                    <li><a class="overlay"
                           href="{{ route('search.overlay') }}"
                           data-overlay-title="Search Universe"
                           data-overlay-close="__default closeSearch"
                           data-overlay-class="search"
                           accesskey="">@lang('Search')</a>
                    </li>
                    <li><a href="{{ route('options.index') }}" accesskey="">@lang('Options')</a></li>
                    <li><a href="#">@lang('Support')</a></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">@lang('Log out')</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <li class="OGameClock">{{ \Carbon\Carbon::now()->format('d.m.Y H:i:s') }}</li>
                </ul>
            </div>
        </div>
        <div id="resourcesbarcomponent" class="">
            <div id="resources">

                <div class="resource_tile metal">
                    <div id="metal_box" class="metal tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="Metal|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['metal']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['metal']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['metal']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>"
                         data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1="
                         data-ipi-hint="ipiResourcemetal">
                        <div class="resourceIcon metal"></div>
                        <span class="value">
                        <span id="resources_metal"
                              class="{{ $resources['metal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['metal']['amount'] >= $resources['metal']['storage'] ? 'overmark' : '' }}"
                              data-raw="{!! $resources['metal']['amount'] !!}">{!! $resources['metal']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile crystal">
                    <div id="crystal_box" class="crystal tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="@lang('Crystal')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['crystal']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['crystal']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['crystal']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>"
                         data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1="
                         data-ipi-hint="ipiResourcecrystal">
                        <div class="resourceIcon crystal"></div>
                        <span class="value">
                        <span id="resources_crystal"
                              class="{{ $resources['crystal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['crystal']['amount'] >= $resources['crystal']['storage'] ? 'overmark' : '' }}"
                              data-raw="{!! $resources['crystal']['amount'] !!}">{!! $resources['crystal']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile deuterium">
                    <div id="deuterium_box" class="deuterium tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="@lang('Deuterium')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['deuterium']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['deuterium']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['deuterium']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>"
                         data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1="
                         data-ipi-hint="ipiResourcedeuterium">
                        <div class="resourceIcon deuterium"></div>
                        <span class="value">
                        <span id="resources_deuterium"
                              class="{{ $resources['deuterium']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['deuterium']['amount'] >= $resources['deuterium']['storage'] ? 'overmark' : '' }}"
                              data-raw="{!! $resources['deuterium']['amount'] !!}">{!! $resources['deuterium']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile energy">
                    <div id="energy_box" class="energy tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="@lang('Energy')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['energy']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Current production:')</th><td><span class=&quot;undermark&quot;>+{!! $resources['energy']['production_formatted'] !!}</span></td></tr><tr><th>@lang('Consumption')</th><td><span class=&quot;overmark&quot;>-{!! $resources['energy']['consumption_formatted'] !!}</span></td></tr></table>"
                         data-ipi-hint="ipiResourceenergy">
                        <div class="resourceIcon energy"></div>
                        <span class="value">
                        <span id="resources_energy" class="{{ $resources['energy']['amount'] < 0 ? 'overmark' : '' }}"
                              data-raw="{!! $resources['energy']['amount'] !!}">{!! $resources['energy']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile population">
                    <div id="population_box" class="population tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="Population|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;overmark&quot;>100</span></td></tr><tr><th>Living Space
</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Satisfied</th><td><span class=&quot;undermark&quot;>0</span></td></tr><tr><th>Hungry</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Growth rate</th><td><span class=&quot;&quot;>±0</span></td></tr><tr><th>Bunker Space
</th><td><span class=&quot;middlemark&quot;>100</span></td></tr></table>" data-ipi-hint="ipiResourcepopulation">
                        <div class="resourceIcon population"></div>
                        <span class="value">
                        <span id="resources_population" data-raw="100" class="overmark">100</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile food">
                    <div id="food_box" class="food tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="Food|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Storage capacity</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Overproduction</th><td><span class=&quot;undermark&quot;>0</span></td></tr><tr><th>Consumption</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Consumed in</th><td><span class=&quot;overmark timeTillFoodRunsOut&quot;>~</span></td></tr></table>"
                         data-ipi-hint="ipiResourcefood">
                        <div class="resourceIcon food"></div>
                        <span class="value">
                        <span id="resources_food" data-raw="0" class="overmark">0</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile darkmatter">
                    <div id="darkmatter_box" class="darkmatter tooltipHTML resource ipiHintable tpd-hideOnClickOutside"
                         title="@lang('Dark Matter')|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;&quot;>{!! $resources['darkmatter']['amount_formatted'] !!}</span></td></tr></table>"
                         data-tooltip-button="Purchase Dark Matter" data-ipi-hint="ipiResourcedarkmatter">
                        <a href="#TODO_page=payment" class="overlay">
                            <img src="/img/icons/401d1a91ff40dc7c8acfa4377d3d65.gif">
                            <div class="resourceIcon darkmatter"></div>
                        </a>
                        <span class="value">
                        <span id="resources_darkmatter" data-raw="{!! $resources['darkmatter']['amount'] !!}" class="overlay">{!! $resources['darkmatter']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="commandercomponent" class="">
            <div id="lifeform" class="fleft">
                <a href="#TODO_page=ingame&amp;component=lfsettings" class="tooltipHTML js_hideTipOnMobile ipiHintable"
                   title="Lifeform|No lifeforms
" data-ipi-hint="ipiLifeformSettings">
                    <div class="resourceIcon population"></div>
                </a>
            </div>
            <div id="characterclass" class="fleft">
                @php
                    $userClass = $currentPlayer->getUser()->getCharacterClassEnum();
                    $classTitle = 'No class selected';
                    $classIcon = 'none';
                    $classBonuses = [];

                    if ($userClass) {
                        $classTitle = 'Your class: ' . $userClass->getName();
                        $classIcon = $userClass->getMachineName();
                        $classBonuses = $userClass->getBonuses();
                    }
                @endphp
                <a href="{{ route('characterclass.index') }}"
                   class="tooltipHTML js_hideTipOnMobile ipiHintable"
                   title="{{ $classTitle }}|@if($classBonuses){{ implode('<br>', $classBonuses) }}@else@lang('Click to select a character class')@endif"
                   data-ipi-hint="ipiCharacterclassSettings">
                    <div class="sprite characterclass medium {{ $classIcon }}"></div>
                </a>
            </div>
            <div id="officers" class="  fright">
                <a href="#TODO_=premium&amp;openDetail=2" class="tooltipHTML   commander js_hideTipOnMobile "
                   title="Hire commander|&amp;#43;40 favorites, building queue, shortcuts, transport scanner, advertisement-free* <span style=&quot;font-size: 10px; line-height: 10px&quot;>(*excludes: game related references)</span>">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=3" class="tooltipHTML    admiral js_hideTipOnMobile " title="Hire admiral|Max. fleet slots +2,
Max. expeditions +1,
Improved fleet escape rate,
Combat simulation save slots +20">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=4" class="tooltipHTML    engineer js_hideTipOnMobile "
                   title="Hire engineer|Halves losses to defenses, +10% energy production">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=5" class="tooltipHTML    geologist js_hideTipOnMobile "
                   title="Hire geologist|+10% mine production">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=6" class="tooltipHTML    technocrat js_hideTipOnMobile "
                   title="Hire technocrat|+2 espionage levels, 25% less research time">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
            </div>
        </div>
        <div id="notificationbarcomponent" class="">
            <div id="message-wrapper">
                <a class=" comm_menu messages tooltip js_hideTipOnMobile"
                   href="{{ route('messages.index') }}"
                   title="{{ $unreadMessagesCount }} @lang('unread message(s)')">
                    @if ($unreadMessagesCount > 0)
                        <span class="new_msg_count totalMessages  news"
                              data-new-messages="{{ $unreadMessagesCount }}">
                                {{ $unreadMessagesCount }}
                            </span>
                    @endif
                </a>
                <!-- Neue Chatnachrichten-Zähler -->
                <a class=" comm_menu chat tooltip js_hideTipOnMobile tpd-hideOnClickOutside"
                   href="#"
                   title="0 unread conversation(s)">
                    <!-- js modification !-->
                    <span class="new_msg_count totalChatMessages noMessage" data-new-messages="0">
                    0                </span>
                </a>
                <div id="messages_collapsed">
                    <div id="eventboxFilled" class="eventToggle" style="display: none;">
                        <a id="js_eventDetailsClosed" class="tooltipRight js_hideTipOnMobile"
                           href="javascript:void(0);"
                           title="More details"></a>
                        <a id="js_eventDetailsOpen" class="tooltipRight open js_hideTipOnMobile"
                           href="javascript:void(0);"
                           title="Less detail"></a>


                    </div>
                    <div id="eventboxLoading" class="textCenter textBeefy" style="display: block;">
                        <img height="16" width="16"
                             src="/img/icons/3f9884806436537bdec305aa26fc60.gif"/>@lang('load...')
                    </div>
                    <div id="eventboxBlank" class="textCenter" style="display: none;">
                        @lang('No fleet movement')
                    </div>
                </div>
            @php
                    // Check for wreck fields on current player's planets
                    $playerWreckFields = [];

                    foreach ($currentPlayer->planets->allPlanets() as $planet) {
                        $wreckFieldService = new \OGame\Services\WreckFieldService($currentPlayer, app(\OGame\Services\SettingsService::class));
                        // Load only active or blocked wreck fields (skip repairing ones)
                        $wreckFieldLoaded = $wreckFieldService->loadActiveOrBlockedForCoordinates($planet->getPlanetCoordinates());

                        if ($wreckFieldLoaded) {
                            $wreckField = $wreckFieldService->getWreckField();
                            if ($wreckField && $wreckField->getTotalShips() > 0) {
                                // Check if THIS planet (where the wreck field is) has a Space Dock
                                $hasSpaceDock = $planet->getObjectLevel('space_dock') > 0;

                                $playerWreckFields[] = [
                                    'planet' => $planet,
                                    'wreckField' => $wreckField,
                                    'hasSpaceDock' => $hasSpaceDock
                                ];
                            }
                        }
                    }
                @endphp

                <div id="attack_alert" class="@if ($underAttack) soon @elseif (!empty($playerWreckFields) && !$underAttack) wreckField @else noAttack @endif"
                     @if ($underAttack) title="@lang('You are under attack!')" @endif>
                    @if ($underAttack)
                        <a href="#TODO_componentOnly&amp;component=eventList" class=" tooltipHTML js_hideTipOnMobile"></a>
                    @elseif (!empty($playerWreckFields))
                        @php
                            // Fix time calculation - use proper timezone
                            if (!empty($playerWreckFields[0])) {
                                $wreckFieldObj = $playerWreckFields[0]['wreckField'];
                                $now = now();

                                // For repairing wreck fields, use repair completion time
                                // For active/blocked wreck fields, use expiration time
                                if ($wreckFieldObj->status === 'repairing' && $wreckFieldObj->repair_completed_at) {
                                    $endTime = $wreckFieldObj->repair_completed_at;
                                } else {
                                    $endTime = $wreckFieldObj->expires_at;
                                }

                                // Use Carbon's proper diff calculation
                                $timeRemaining = max(0, $now->diffInSeconds($endTime, false));
                            } else {
                                $timeRemaining = 0;
                            }
                        @endphp
                        @if ($timeRemaining > 0)
                            @php
                                $days = floor($timeRemaining / 86400);
                                $hours = floor(($timeRemaining % 86400) / 3600);
                                $minutes = floor(($timeRemaining % 3600) / 60);

                                if ($days > 0) {
                                    $timeText = $days . 'd ' . $hours . 'h ' . $minutes . 'm';
                                } elseif ($hours > 0) {
                                    $timeText = $hours . 'h ' . $minutes . 'm';
                                } else {
                                    $timeText = $minutes . 'm';
                                }
                            @endphp
                            @php
                        // Build ship breakdown tooltip
                        $shipTooltipContent = "<span style='color: #00aaff;'>:</span> <br/>";
                        if (!empty($playerWreckFields[0])) {
                            // Try to get ship data from different possible locations
                            $shipData = null;
                            if (isset($playerWreckFields[0]['ship_data'])) {
                                $shipData = $playerWreckFields[0]['ship_data'];
                            } elseif (isset($playerWreckFields[0]['wreckField']) && method_exists($playerWreckFields[0]['wreckField'], 'getShipData')) {
                                $shipData = $playerWreckFields[0]['wreckField']->getShipData();
                            } elseif (isset($playerWreckFields[0]['wreckField']->ship_data)) {
                                $shipData = $playerWreckFields[0]['wreckField']->ship_data;
                            }

                            if (!empty($shipData) && is_array($shipData)) {
                                foreach ($shipData as $ship) {
                                    $machineName = $ship['machine_name'] ?? 'Unknown Ship';
                                    $quantity = $ship['quantity'] ?? 0;
                                    $shipName = ucfirst(str_replace('_', ' ', $machineName));
                                    $shipTooltipContent .= $shipName . ': ' . $quantity . '<br/>';
                                }
                            } else {
                                $shipTooltipContent .= 'No ships in wreck field';
                            }
                        } else {
                            $shipTooltipContent .= 'No wreck field available';
                        }
                    @endphp
                    @php
                    // Check if wreck field is active (not being repaired or burned)
                    $isWreckFieldActive = false;
                    $hasSpaceDockOnWreckFieldPlanet = false;
                    if ($timeRemaining > 0 && !empty($playerWreckFields[0])) {
                        $wreckField = $playerWreckFields[0]['wreckField'] ?? null;
                        $hasSpaceDockOnWreckFieldPlanet = $playerWreckFields[0]['hasSpaceDock'] ?? false;
                        if ($wreckField) {
                            // Show icon ONLY for active or blocked wreck fields (NOT repairing)
                            $isWreckFieldActive = in_array($wreckField->status, ['active', 'blocked']);
                        }
                    }
                    @endphp
                    @if ($isWreckFieldActive && $hasSpaceDockOnWreckFieldPlanet)
                    <a href="javascript:void(0);" class="wreckFieldIcon tooltip js_hideTipOnMobile" title="{{ $shipTooltipContent }}" style="cursor: pointer;" onclick="openWreckFieldDetailsPopup(); return false;"></a>
                            <span id="wreckFieldCountDown" class="wreckFieldCountDown" data-duration="{{ $timeRemaining }}" title="">{{ $timeText }}</span>
                    @endif
                                                        <script>
                            // Initialize wreck field countdown if not already done
                            if (typeof window.simpleCountdown !== 'undefined') {
                                var wreckfield = $("#wreckFieldCountDown");
                                if (wreckfield.length) {
                                    new simpleCountdown(wreckfield, wreckfield.data('duration'), null);
                                }
                            }
                            </script>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>

  
    <div id="left">
        <div id="ipimenucomponent" class="">
            <div id="ipiMenuWrapper" class="ipiMenuTrackedAction ipiHintable " title="" data-ipi-hint="ipiMenu">
                <div id="ipimenucontent"><a
                            href="#TODO_page=ajax&amp;component=ipioverview&amp;action=overviewLayer&amp;ajax=1"
                            class="overlay textBeefy" data-overlay-title="" id="ipiInnerMenuContentHolder">
                        <div class="ipiMenuHead">
                            Directives
                        </div>

                        <div class="ipiMenuBody hidden"></div>
                        <div class="ipiMenuFooter hidden"></div>
                    </a>
                </div>
            </div>
        </div>
        <div id="toolbarcomponent" class="">
            <div id="links">
                <ul id="menuTable" class="leftmenu">

                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('rewards.index') }}"
                               class="tooltipRight js_hideTipOnMobile "
                               target="_self"
                               title="Rewards">
                                <div class="menuImage overview {{(Request::is('rewards') || Request::is('overview') ? 'highlighted' : '') }}"></div>
                            </a>
                        </span>
                        <a class="menubutton {{(Request::is('overview') ? 'selected' : '') }}"
                           href="{{ route('overview.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Overview')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('resources.settings') }}"
                               class="tooltipRight js_hideTipOnMobile "
                               target="_self"
                               title="Resource settings">
                                <div class="menuImage resources {{(Request::is('resources*') ? 'highlighted' : '') }}"></div>
                            </a>
                        </span>
                        <a class="menubutton {{(Request::is('resources*') ? 'selected' : '') }}"
                           href="{{ route('resources.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Resources')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            @if ($currentPlanet->isMoon() && $currentPlanet->getObjectLevel('jump_gate') > 0)
                                <a href="{{ route('jumpgate.index') }}" class="overlay tooltipRight js_hideTipOnMobile" target="_self" data-overlay-title="@lang('Jump Gate')" title="@lang('Jump Gate')">
                                    <div class="menuImage station highlighted ipiHintable" data-ipi-hint="ipiToolbarJumpgate"></div>
                                </a>
                            @else
                                <div class="menuImage station"></div>
                            @endif
                        </span>
                        <a class="menubutton {{(Request::is('facilities') ? 'selected' : '') }}"
                           href="{{ route('facilities.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Facilities')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('merchant.resource-market') }}"
                               class="trader tooltipRight js_hideTipOnMobile "
                               target="_self"
                               title="Resource Market">
                                <div class="menuImage traderOverview {{(Request::is('merchant*') ? 'highlighted' : '') }}">
                                </div>
                            </a>
                        </span>
                        <a class="menubutton premiumHighligt {{(Request::is('merchant*') ? 'selected' : '') }}"
                           href="{{ route('merchant.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Merchant')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => 1, 'open' => 'all']) }}"
                               class="overlay tooltipRight js_hideTipOnMobile "
                               target="_blank"
                               title="Technology">
                                <div class="menuImage research {{(Request::is('research') ? 'highlighted' : '') }}">
                                </div>
                            </a>
                        </span>
                        <a class="menubutton {{(Request::is('research') ? 'selected' : '') }}"
                           href="{{ route('research.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Research')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <div class="menuImage shipyard {{(Request::is('shipyard') ? 'highlighted' : '') }}"></div>
                        </span>
                        <a class="menubutton {{(Request::is('shipyard') ? 'selected' : '') }}"
                           href="{{ route('shipyard.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Shipyard')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <div class="menuImage defense {{(Request::is('defense') ? 'highlighted' : '') }}"></div>
                        </span>
                        <a class="menubutton {{(Request::is('defense') ? 'selected' : '') }}"
                           href="{{ route('defense.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Defense')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('fleet.movement') }}"
                               class="tooltipRight js_hideTipOnMobile "
                               target="_self"
                               title="Fleet movement">
                                <div class="menuImage fleet1 {{(Request::is('fleet*') ? 'highlighted' : '') }}">
                                </div>
                            </a>
                        </span>
                        <a class="menubutton {{(Request::is('fleet*') ? 'selected' : '') }}"
                           href="{{ route('fleet.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Fleet')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <div class="menuImage galaxy {{(Request::is('galaxy') ? 'highlighted' : '') }}"></div>
                        </span>
                        <a class="menubutton {{(Request::is('galaxy') ? 'selected' : '') }}"
                           href="{{ route('galaxy.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Galaxy')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <div class="menuImage alliance {{(Request::is('alliance') ? 'highlighted' : '') }}"></div>
                        </span>
                        <a class="menubutton {{(Request::is('alliance') ? 'selected' : '') }}"
                           href="{{ route('alliance.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Alliance')</span>
                        </a>
                    </li>

                    <li>
                        <span class="menu_icon">
                            <div class="menuImage premium {{(Request::is('premium') ? 'highlighted' : '') }}"></div>
                        </span>
                        <a class="menubutton premiumHighligt officers {{(Request::is('premium') ? 'selected' : '') }}"
                           href="{{ route('premium.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Recruit Officers')</span>
                        </a>
                    </li>
                    <li>
                        <span class="menu_icon">
                            <a href="{{ route('shop.index') }}#page=inventory"
                               class="tooltipRight js_hideTipOnMobile "
                               target="_self"
                               title="Inventory">
                                <div class="menuImage shop {{(Request::is('shop') ? 'highlighted' : '') }}">
                                </div>
                            </a>
                        </span>
                        <a class="menubutton premiumHighligt {{(Request::is('shop') ? 'selected' : '') }}"
                           href="{{ route('shop.index') }}"
                           accesskey=""
                           target="_self"
                        >
                            <span class="textlabel">@lang('Shop')</span>
                        </a>
                    </li>
                </ul>

                <div id="toolLinksWrapper">
                    <ul id="menuTableTools" class="leftmenu"></ul>
                </div>
                <br class="clearfloat">
            </div>
        </div>
        <div id="advicebarcomponent" class="">
            <div class="adviceWrapper">

                <div id="advice-bar">
                </div>
            </div>

        </div>
    </div>
    <div id="middle">
        <div id="eventlistcomponent" class="">
            <div id="eventboxContent" style="display: none;">
            </div>

            <script type="text/javascript">
                var session = "3c442273a6de4c8f79549e78f4c3ca50e7ea7580";
                var vacation = 0;
                var timerHandler = new TimerHandler();

                function redirectPremium() {
                    location.href = "{{ route('premium.index', ['showDarkMatter' => 1]) }}#TODO_premium&showDarkMatter=1";
                }

                var playerId = "{{ $currentPlayer->getId() }}";
                var playerName = "{{ $currentPlayer->getUsername() }}";
                var player = {
                    "playerId": {{ $currentPlayer->getId() }},
                    "name": "{{ $currentPlayer->getUsername() }}",
                    "hasCommander": false,
                    "hasAPassword": true
                };
                var hasAPassword = true;
                var jsloca = {
                    "INTERNAL_ERROR": "A previously unknown error has occurred. Unfortunately your last action couldn`t be executed!",
                    "LOCA_ALL_YES": "yes",
                    "LOCA_ALL_NO": "No",
                    "LOCA_NOTIFY_ERROR": "Error",
                    "LOCA_NOTIFY_INFO": "Info",
                    "LOCA_NOTIFY_SUCCESS": "Success",
                    "LOCA_NOTIFY_WARNING": "Warning",
                    "COMBATSIM_PLANNING": "Planning",
                    "COMBATSIM_PENDING": "Simulation running...",
                    "COMBATSIM_DONE": "Complete",
                    "MSG_RESTORE": "restore",
                    "MSG_DELETE": "delete",
                    "COPIED_TO_CLIPBOARD": "Copied to clipboard",
                    "LOCA_ALL_NETWORK_ATTENTION": "Caution",
                    "LOCA_NETWORK_MSG_GAMEOPERATOR": "Report this message to a game operator?"
                };
                var session = "3c442273a6de4c8f79549e78f4c3ca50e7ea7580";
                var isMobile = false;
                var isMobileApp = false;
                var isMobileOnly = false;
                var isFacebookUser = false;
                var overlayWidth = 770;
                var overlayHeight = 600;
                var isRTLEnabled = 0;
                var activateToken = "e018389e3827e1499e41d35e3c811283";
                var miniFleetToken = "4002a42efaeb2808f6c232594fb09aa4";
                var currentPage = "overview";
                // BBCode preview is handled client-side with custom parser (buddyBBCodeParser)
                // Empty string prevents CORS errors while custom handlers override the preview
                var bbcodePreviewUrl = "";
                var popupWindows = [];
                var fleetDeutSaveFactor = 1;
                var honorScore = 0;
                var darkMatter = 0;
                var serverTime = new Date('{{ Carbon\Carbon::now() }}');
                var localTime = new Date();
                var timeDiff = serverTime - localTime;
                localTS = localTime.getTime();
                var startServerTime = localTime.getTime() - (0) - localTime.getTimezoneOffset() * 60 * 1000;
                var LocalizationStrings = {
                    "timeunits": {
                        "short": {
                            "year": "y",
                            "month": "m",
                            "week": "w",
                            "day": "d",
                            "hour": "h",
                            "minute": "m",
                            "second": "s"
                        }
                    },
                    "status": {
                        "ready": "done"
                    },
                    "decimalPoint": ".",
                    "thousandSeperator": ",",
                    "unitMega": "M",
                    "unitKilo": "K",
                    "unitMilliard": "B",
                    "question": "Question",
                    "error": "Error",
                    "loading": "load...",
                    "yes": "yes",
                    "no": "No",
                    "ok": "Ok",
                    "attention": "Caution",
                    "outlawWarning": "You are about to attack a stronger player. If you do this, your attack defenses will be shut down for 7 days and all players will be able to attack you without punishment. Are you sure you want to continue?",
                    "lastSlotWarningMoon": "This building will use the last available building slot. Expand your Lunar Base to receive more space. Are you sure you want to build this building?",
                    "lastSlotWarningPlanet": "This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item to obtain more slots. Are you sure you want to build this building?",
                    "forcedVacationWarning": "Some game features are unavailable until your account is validated.",
                    "moreDetails": "More details",
                    "lessDetails": "Less detail",
                    "planetOrder": {
                        "lock": "Lock arrangement",
                        "unlock": "Unlock arrangement"
                    },
                    "darkMatter": "Dark Matter",
                    "activateItem": {
                        "upgradeItemQuestion": "Would you like to replace the existing item? The old bonus will be lost in the process.",
                        "upgradeItemQuestionHeader": "Replace item?"
                    }
                };
                var constants = {
                    "espionage": 6,
                    "missleattack": 10,
                    "language": "en",
                    "name": "144"
                };
                var userData = {
                    "id": "108130"
                };
                var missleAttackLink = "{{ route('overview.index') }}#TODO_page=missileattacklayer&width=669&height=250";
                var changeNickLink = "{{ route('changenick.overlay') }}";
                var showOutlawWarning = true;
                var miniFleetLink = "{{ route('fleet.dispatch.sendminifleet') }}";
                var ogameUrl = "{{ str_replace('/', '\/', URL::to('/')) }}";
                var startpageUrl = "{{ str_replace('/', '\/', URL::to('/')) }}";
                var nodePort = 19603;
                // TODO: WebSocket/chat functionality not yet implemented. Disabled to prevent loading overview as a script.
                // var nodeUrl = "{{ route('overview.index') }}#TODO_19603\/socket.io\/socket.io.js";
                var nodeParams = {
                    "port": 19603,
                    "secure": "true"
                };
                var chatUrl = "/"; //#TODO_page=ajaxChat
                var chatUrlLoadMoreMessages = "{{ route('overview.index') }}#TODO_page=chatGetAdditionalMessages";
                var chatLoca = {
                    "TEXT_EMPTY": "Where is the message?",
                    "TEXT_TOO_LONG": "The message is too long.",
                    "SAME_USER": "You cannot write to yourself.",
                    "IGNORED_USER": "You have ignored this player.",
                    "NO_DATABASE_CONNECTION": "A previously unknown error has occurred. Unfortunately your last action couldn`t be executed!",
                    "INVALID_PARAMETERS": "A previously unknown error has occurred. Unfortunately your last action couldn`t be executed!",
                    "SEND_FAILED": "A previously unknown error has occurred. Unfortunately your last action couldn`t be executed!",
                    "LOCA_ALL_ERROR_NOTACTIVATED": "This function is only available after your accounts activation.",
                    "X_NEW_CHATS": "#+# unread conversation(s)",
                    "MORE_USERS": "show more"
                };
                var eventboxLoca = {
                    "mission": "Mission",
                    "missions": "Missions",
                    "next misson": "DUMMY_KEY_N\u00e4chster_fertig",
                    "type": "DUMMY_KEY_Art",
                    "friendly": "own",
                    "neutral": "friendly",
                    "hostile": "hostile",
                    "nextEvent": "Next",
                    "nextEventText": "Type"
                };

                var ajaxEventboxURI = "{{ route('fleet.eventbox.fetch') }}";
                var ajaxRecallFleetURI = "{{ route('fleet.dispatch.recallfleet') }}";
                var currentSpaceObjectId = 33624092;
                var ajaxReloadComponentURI = "#TODO_index.php?page=standalone&ajax=1";

                function redirectLogout() {
                    location.href = "{{ route('overview.index') }}";
                }

                function redirectOverview() {
                    location.href = "{{ route('overview.index') }}";
                }

                function redirectPlatformLogout() {
                    location.href = "{{ route('overview.index') }}";
                }

                function redirectSpaceDock() {
                    location.href = "{{ route('facilities.index', ['openTech' => 36]) }}";
                }

                // Global function to open facilities and trigger space dock click
                function openFacilitiesSpaceDock() {
                    // Store a flag to trigger space dock click after page loads
                    sessionStorage.setItem('triggerSpaceDock', 'true');

                    // Redirect to facilities page with parameter as backup
                    location.href = "{{ route('facilities.index') }}?openSpaceDock=1";
                }

                // Global function to open wreck field details popup
                function openWreckFieldDetailsPopup() {
                    // Make AJAX call to get wreck field details popup content
                    $.ajax({
                        url: "{{ route('facilities.wreckfieldstatus') }}",
                        method: 'GET',
                        success: function(response) {
                            if (response.success && response.wreckField) {
                                // Create popup content similar to the Details button
                                createWreckFieldPopup(response.wreckField);
                            } else {
                                // Fallback: go to facilities and trigger space dock
                                openFacilitiesSpaceDock();
                            }
                        },
                        error: function() {
                            // Fallback: go to facilities and trigger space dock
                            openFacilitiesSpaceDock();
                        }
                    });
                }

                // Function to create wreck field popup content
                function createWreckFieldPopup(wreckFieldData) {
                    const timeRemaining = wreckFieldData.time_remaining || 0;
                    const shipCount = wreckFieldData.ship_data ? wreckFieldData.ship_data.reduce((total, ship) => total + ship.quantity, 0) : 0;

                    let timeDisplay = '';
                    if (timeRemaining > 0) {
                        const days = Math.floor(timeRemaining / 86400);
                        const hours = Math.floor((timeRemaining % 86400) / 3600);
                        const minutes = Math.floor((timeRemaining % 3600) / 60);
                        const seconds = timeRemaining % 60;
                        timeDisplay = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    }

                    // Create proper popup content matching facilities page
                    let shipHtml = '';
                    if (wreckFieldData.ship_data && wreckFieldData.ship_data.length > 0) {
                        wreckFieldData.ship_data.forEach(ship => {
                            const shipName = ship.machine_name ? ship.machine_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown Ship';
                            const totalQuantity = ship.quantity || 0;
                            const repairProgress = wreckFieldData.repair_progress || 0;
                            const repairedCount = wreckFieldData.is_repairing ?
                                Math.floor(totalQuantity * (repairProgress / 100)) : 0;

                            // Calculate real-time repair progress if repairs are active
                            let currentRepairedCount = repairedCount;
                            if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time >= 0 && wreckFieldData.repair_completion_time && wreckFieldData.repair_started_at) {
                                const totalRepairTime = (new Date(wreckFieldData.repair_completion_time).getTime() - new Date(wreckFieldData.repair_started_at).getTime()) / 1000;
                                const elapsedTime = totalRepairTime - wreckFieldData.remaining_repair_time;
                                const currentProgress = Math.min(100, Math.max(0, (elapsedTime / totalRepairTime) * 100));
                                currentRepairedCount = Math.floor(totalQuantity * (currentProgress / 100));
                            }

                            // Map machine names to ship IDs for CSS background positioning
                            const shipIdMap = {
                                'fighter_light': '204',
                                'light_fighter': '204',
                                'fighter_heavy': '205',
                                'heavy_fighter': '205',
                                'cruiser': '206',
                                'battleship': '207',
                                'battle_ship': '207',
                                'battlecruiser': '215',
                                'interceptor': '215',
                                'bomber': '211',
                                'destroyer': '213',
                                'deathstar': '214',
                                'reaper': '218',
                                'explorer': '212',
                                'transporter_small': '202',
                                'small_cargo': '202',
                                'transporter_large': '203',
                                'large_cargo': '203',
                                'colony_ship': '208',
                                'recycler': '209',
                                'espionage_probe': '210',
                                'solar_satellite': '212'
                            };

                            const shipId = shipIdMap[ship.machine_name] || '204';

                            // Use different display format based on repair status
                            if (wreckFieldData.is_repairing) {
                                // During repairs: show progress like "102/451"
                                shipHtml += `
                                    <div class="tooltipHTML fleft ships" id="ship${shipId}" data-tooltip-title="${shipName}|${currentRepairedCount}/${totalQuantity}" title="${shipName}">
                                        <span class="ecke">
                                            <span class="level">${currentRepairedCount}/${totalQuantity}</span>
                                        </span>
                                    </div>
                                `;
                            } else {
                                // Before repairs: show single quantity like "350"
                                const repairTime = '32m 0s'; // TODO: Calculate based on ship count and dock level
                                shipHtml += `
                                    <div class="tooltip fleft ships" id="ship${shipId}" title="${shipName}">
                                        <span class="ecke">
                                            <span class="level">${totalQuantity}</span>
                                        </span>
                                        <div class="repairTime">
                                            <span style="color: whitesmoke">${repairTime}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                    }

                    const popupContent = `
                        <div id="repairlayer">
                            <div class="repairableShips">
                                ${wreckFieldData.is_repairing ?
                                    // During repairs: show minimal content
                                    `<span>There is no wreckage at this position.</span>` :
                                    // Before repairs: show full description
                                    `<div>
                                        <div class="descriptionText">Electronic charges flicker through defective drive units, atmosphere escapes from the wrecks of destroyed ships and is released into space. Huge gaping holes can be seen in the burned out hulls and empty escape capsules whirl around the room. So many ships have fallen victim to the great battle!

However, the Space Dock's engineers think that some of the remains can be salvaged, before the wreckage enters the atmosphere and ultimately burns up. The repair crews are ready.</div>
                                        <div class="rightArea">
                                            <div class="boxed">
                                                <p>Wreckage burns up in: </p>
                                                <p id="burnUpCountDownForRepairOverlay" data-duration="${timeRemaining}">${timeDisplay}</p>
                                            </div>
                                            <br>
                                            ${!wreckFieldData.is_repairing && !wreckFieldData.is_completed && wreckFieldData.can_repair ?
                                            `<div class="btn btn_dark fright burnUpButton">
                                                <input type="button" class="overmark burnUpButton" value="Leave to burn up" data-loca_box_text="Leave to burn up" data-loca_decision_text="The wreckage will descend into the planet's atmosphere and burn up. Once struck, a repair will no longer be possible. Are you sure you want to burn up the wreckage?" data-loca_yes="yes" data-loca_no="No" onclick="goToSpaceDockAndBurnUp();">
                                            </div>` : ''
                            }
                                        </div>
                                    </div>`
                                }
                                <div class="clearfix"></div>
                                <br>
                                <hr style="height: 2px; width: 644px; margin: 5.5px 0; background-color: #ffffff; border: none;">
                                <h3>${wreckFieldData.is_repairing ? 'Ships being repaired:' : 'Repairable ships:'}</h3>
                                <div class="ships_wrapper clearfix">
                                    ${shipHtml}
                                    <div class="clearfix"></div>
                                    <br>
                                    ${wreckFieldData.is_repairing ?
                                        // During repairs: show repair time remaining and collection button
                                        (() => {
                                            const remainingTime = wreckFieldData.remaining_repair_time || 0;

                                            // If repairs are complete (remainingTime === 0), show auto-return date/time
                                            let timeDisplay;
                                            let timeLabel = 'Repair time remaining: ';
                                            if (remainingTime === 0 && wreckFieldData.repair_started_at && wreckFieldData.total_repair_time > 0) {
                                                // Calculate auto-return time (72 hours after repair completion)
                                                const repairStartTime = new Date(wreckFieldData.repair_started_at);
                                                const totalRepairTime = wreckFieldData.total_repair_time;
                                                const repairCompletionTime = new Date(repairStartTime.getTime() + (totalRepairTime * 1000));
                                                const autoReturnTime = new Date(repairCompletionTime.getTime() + (72 * 60 * 60 * 1000)); // 72 hours later

                                                // Format the date/time
                                                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                                                timeDisplay = autoReturnTime.toLocaleDateString('en-US', options);
                                                timeLabel = 'Ships will be automatically put back into service on: ';
                                            } else {
                                                const hours = Math.floor(remainingTime / 3600);
                                                const minutes = Math.floor((remainingTime % 3600) / 60);
                                                const seconds = remainingTime % 60;
                                                timeDisplay = `${hours}h ${minutes}m ${seconds}s`;
                                            }

                                            // Check if at least 30 minutes have passed since repairs started
                                            let timeSinceRepairStart = 0;
                                            if (wreckFieldData.repair_started_at) {
                                                const repairStartTime = new Date(wreckFieldData.repair_started_at);
                                                timeSinceRepairStart = Math.floor((Date.now() - repairStartTime) / 1000);
                                            }
                                            const minRepairTime = 30 * 60; // 30 minutes
                                            const minTimePassed = timeSinceRepairStart >= minRepairTime;

                                            // Calculate repaired ships count
                                            const repairedShips = wreckFieldData.ship_data ? wreckFieldData.ship_data.reduce((sum, ship) => {
                                                return sum + Math.floor((ship.quantity * (wreckFieldData.repair_progress || 0)) / 100);
                                            }, 0) : 0;

                                            const canPartialCollect = minTimePassed && repairedShips > 0;
                                            const buttonDisabled = canPartialCollect ? '' : 'disabled="disabled"';
                                            const buttonClass = canPartialCollect ? 'middlemark' : 'disabled';

                                            return `<p>${timeLabel}<span id="repairTimeCountDownForRepairOverlay" data-duration="${remainingTime}">${timeDisplay}</span></p>
                                        <div class="btn btn_dark fright tooltip reCommissionButton" title="">
                                            <input type="button" class="${buttonClass} reCommissionButton tooltip partial-collect-btn" value="Put ships that are already repaired back into service" ${buttonDisabled}>
                                        </div>`;
                                        })() :
                                        wreckFieldData.is_completed ?
                                        `<div class="btn btn_dark fright">
                                            <input type="button" class="middlemark" value="Repairs completed - Collect ships" onclick="location.href='{{ route('facilities.index') }}';">
                                        </div>` :
                                        // Before repairs: show repair time and start button
                                        `<label>Repair time: </label><span id="repairTime">${wreckFieldData.remaining_repair_time > 0 ? Math.floor(wreckFieldData.remaining_repair_time / 60) + 'm ' + (wreckFieldData.remaining_repair_time % 60) + 's' : '32m 0s'}</span>
                                        <div class="btn btn_dark fright startRepairsButton">
                                            <input type="button" class="middlemark startRepairsButton" value="Start repairs" onclick="goToSpaceDockAndRepair();">
                                        </div>`
                                    }
                                </div>
                            </div>
                        </div>
                    `;

                    // Create jQuery UI dialog with proper background styling
                    const $dialog = $('<div>')
                        .addClass('overlayDiv repairlayer')
                        .html(popupContent)
                        .dialog({
                            modal: false,
                            resizable: false,
                            draggable: true,
                            width: 656,
                            title: 'Space Dock',
                            dialogClass: 'repairlayer',
                            closeOnEscape: true,
                            close: function() {
                                $(this).dialog('destroy').remove();
                            },
                            open: function() {
                                // Remove unwanted "Close" text from title bar
                                $(this).parent().find('.ui-dialog-titlebar-close').contents().filter(function() {
                                    return this.nodeType === 3 && $.trim(this.nodeValue) === 'Close';
                                }).remove();

                                // Also remove any span elements containing just "Close" text
                                $(this).parent().find('.ui-dialog-titlebar-close span').each(function() {
                                    const $this = $(this);
                                    if (!$this.hasClass('ui-icon') && !$this.hasClass('ui-button-icon-space') && $.trim($this.text()) === 'Close') {
                                        $this.remove();
                                    }
                                });

                                // Initialize repair time countdown timer
                                const $repairCountdown = $('#repairTimeCountDownForRepairOverlay');
                                if ($repairCountdown.length) {
                                    let duration = $repairCountdown.data('duration');

                                    // Only start countdown if there's time remaining (duration > 0)
                                    // If duration is 0, we're showing the auto-return date, not a countdown
                                    if (duration > 0) {
                                        const repairTimerInterval = setInterval(function() {
                                            if (duration <= 0) {
                                                $repairCountdown.text('0h 0m 0s');
                                                clearInterval(repairTimerInterval);
                                                return;
                                            }

                                            const hours = Math.floor(duration / 3600);
                                            const minutes = Math.floor((duration % 3600) / 60);
                                            const seconds = duration % 60;

                                            $repairCountdown.text(`${hours}h ${minutes}m ${seconds}s`);
                                            duration--;
                                        }, 1000);

                                        // Store interval on the element for cleanup
                                        $repairCountdown.data('repairTimerInterval', repairTimerInterval);
                                    }
                                }

                                // Bind click handler for partial collection button
                                $(this).find('.partial-collect-btn').on('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    collectPartialRepairedShips(e);
                                });
                            },
                            close: function() {
                                // Cleanup repair timer interval
                                const $repairCountdown = $('#repairTimeCountDownForRepairOverlay');
                                if ($repairCountdown.length) {
                                    const interval = $repairCountdown.data('repairTimerInterval');
                                    if (interval) {
                                        clearInterval(interval);
                                    }
                                }
                                $(this).dialog('destroy').remove();
                            }
                        });

                    // Initialize countdown timer if present
                    if (timeRemaining > 0 && typeof window.simpleCountdown !== 'undefined') {
                        const $countdown = $('#burnUpCountDownForRepairOverlay');
                        if ($countdown.length) {
                            new simpleCountdown($countdown, $countdown.data('duration'), null);
                        }
                    }
                }

                // Functions for popup actions
                function goToSpaceDockAndBurnUp() {
                    // Show confirmation dialog first
                    errorBoxDecision(
                        "Leave to burn up",
                        "The wreckage will descend into the planet's atmosphere and burn up. Once struck, a repair will no longer be possible. Are you sure you want to burn up the wreckage?",
                        "yes",
                        "No",
                        function() {
                            // User confirmed, proceed with burn up
                            $.ajax({
                                url: "{{ route('facilities.burnwreckfield') }}",
                                method: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.success) {
                                        fadeBox(response.message, false);
                                        // Close the dialog
                                        $('.ui-dialog:has(.repairlayer)').find('.ui-dialog-titlebar-close').click();
                                    } else {
                                        fadeBox(response.message || 'Error burning up wreck field', true);
                                    }
                                },
                                error: function() {
                                    fadeBox('Error burning up wreck field', true);
                                }
                            });
                        },
                        function() {
                            // User cancelled, do nothing
                        }
                    );
                }

                function goToSpaceDockAndRepair() {
                    // Start repairs directly via AJAX
                    $.ajax({
                        url: '{{ route("facilities.startrepairs") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                fadeBox('Repairs started successfully!');
                                // Close the dialog to prevent confusion
                                $('.ui-dialog:has(.repairlayer)').find('.ui-dialog-titlebar-close').click();

                                // Redirect to facilities page to show repair progress
                                setTimeout(() => {
                                    location.href = "{{ route('facilities.index') }}?openSpaceDock=1";
                                }, 500);
                            } else {
                                // Show error message
                                fadeBox(response.message || 'Error starting repairs', true);
                            }
                        },
                        error: function(xhr) {
                            // Show error message on AJAX failure
                            console.error('AJAX error:', xhr.status, xhr.statusText);
                            console.error('Response text:', xhr.responseText);

                            // Try to parse JSON if we get a 400 response with JSON body
                            let errorMsg = 'Error starting repairs: ' + xhr.status;
                            if (xhr.status === 400 && xhr.responseText) {
                                try {
                                    const jsonResponse = JSON.parse(xhr.responseText);
                                    if (jsonResponse.message) {
                                        errorMsg = jsonResponse.message;
                                    }
                                } catch (e) {
                                    // If parsing fails, fall back to default error
                                }
                            }

                            fadeBox(errorMsg, true);
                        }
                    });
                }

                function collectPartialRepairedShips(event) {
                    // Prevent any other event handlers from firing
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Collect partially repaired ships (after 30 minutes)
                    $.ajax({
                        url: '{{ route("facilities.completerepairs") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Close the dialog completely first
                                $('.ui-dialog:has(.repairlayer)').dialog('destroy').remove();

                                // Show success message
                                fadeBox(response.message || 'Ships collected successfully!');

                                // Redirect immediately to facilities page
                                setTimeout(function() {
                                    window.location.href = "{{ route('facilities.index') }}?openSpaceDock=1";
                                }, 100);
                            } else {
                                fadeBox(response.message || 'Error collecting ships', true);
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Error collecting ships';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            fadeBox(errorMsg, true);
                        }
                    });
                }

                reloadResources({
                    "resources": {
                        "population": {
                            "amount": 100,
                            "storage": 0,
                            "safeCapacity": 0,
                            "growthRate": 0,
                            "capableToFeed": 0,
                            "needFood": 0,
                            "singleFoodConsumption": 0,
                            "tooltip": "@lang('Population')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"overmark\">100<\/span><\/td><\/tr><tr><th>@lang('Living Space')\n<\/th><td><span class=\"overmark\">0<\/span><\/td><\/tr><tr><th>@lang('Satisfied')<\/th><td><span class=\"undermark\">0<\/span><\/td><\/tr><tr><th>@lang('Hungry')<\/th><td><span class=\"overmark\">0<\/span><\/td><\/tr><tr><th>@lang('Growth rate')<\/th><td><span class=\"\">\u00b10<\/span><\/td><\/tr><tr><th>@lang('Bunker Space')\n<\/th><td><span class=\"middlemark\">100<\/span><\/td><\/tr><\/table>",
                            "classesListItem": ""
                        },
                        "food": {
                            "amount": 0,
                            "storage": 0,
                            "capableToFeed": 0,
                            "production": 0,
                            "consumption": 0,
                            "timeTillFoodRunsOut": 0,
                            "vacationMode": "",
                            "tooltip": "@lang('Food')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"overmark\">0<\/span><\/td><\/tr><tr><th>@lang('Storage capacity')<\/th><td><span class=\"overmark\">0<\/span><\/td><\/tr><tr><th>@lang('Overproduction')<\/th><td><span class=\"undermark\">0<\/span><\/td><\/tr><tr><th>@lang('Consumption')<\/th><td><span class=\"overmark\">0<\/span><\/td><\/tr><tr><th>@lang('Consumed in')<\/th><td><span class=\"overmark timeTillFoodRunsOut\">~<\/span><\/td><\/tr><\/table>",
                            "classesListItem": ""
                        },
                        "metal": {
                            "amount": {!! $resources['metal']['amount'] !!},
                            "storage": {!! $resources['metal']['storage'] !!},
                            "baseProduction": 0, // TODO: add base production separately?
                            "production": {!! $resources['metal']['production_second'] !!},
                            "tooltip": "@lang('Metal')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"\">{!! $resources['metal']['amount_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Storage capacity')<\/th><td><span class=\"\">{!! $resources['metal']['storage_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Current production'):<\/th><td><span class=\"@if ($resources['metal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['metal']['production_hour'] > 0)+@endif{!! $resources['metal']['production_hour_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Den Capacity'):<\/th><td><span class=\"overermark\">0<\/span><\/td><\/tr><\/table>",
                            "classesListItem": "",
                            "shopUrl": "#TODO_category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&item=859d82d316b83848f7365d21949b3e1e63c7841f&page=shop&panel1-1="
                        },
                        "crystal": {
                            "amount": {!! $resources['crystal']['amount'] !!},
                            "storage": {!! $resources['crystal']['storage'] !!},
                            "baseProduction": 0, // TODO: add base production separately?
                            "production": {!! $resources['crystal']['production_second'] !!},
                            "tooltip": "@lang('Crystal')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"\">{!! $resources['crystal']['amount_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Storage capacity')<\/th><td><span class=\"\">{!! $resources['crystal']['storage_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Current production'):<\/th><td><span class=\"@if ($resources['crystal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['crystal']['production_hour'] > 0)+@endif{!! $resources['crystal']['production_hour_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Den Capacity'):<\/th><td><span class=\"overermark\">0<\/span><\/td><\/tr><\/table>",
                            "classesListItem": "",
                            "shopUrl": "#TODO_page=shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&item=bb2f6843226ef598f0b567b92c51b283de90aa48&page=shop&panel1-1="
                        },
                        "deuterium": {
                            "amount": {!! $resources['deuterium']['amount'] !!},
                            "storage": {!! $resources['deuterium']['storage'] !!},
                            "baseProduction": 0, // TODO: add base production separately?
                            "production": {!! $resources['deuterium']['production_second'] !!},
                            "tooltip": "@lang('Deuterium')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"\">{!! $resources['deuterium']['amount_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Storage capacity')<\/th><td><span class=\"\">{!! $resources['deuterium']['storage_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Current production'):<\/th><td><span class=\"@if ($resources['deuterium']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['deuterium']['production_hour'] > 0)+@endif{!! $resources['deuterium']['production_hour_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Den Capacity'):<\/th><td><span class=\"overermark\">0<\/span><\/td><\/tr><\/table>",
                            "classesListItem": "",
                            "shopUrl": "#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&item=cb72ed207dd871832a850ee29f1c1f83aa3f4f36&page=shop&panel1-1="
                        },
                        "energy": {
                            "amount": {!! $resources['energy']['amount'] !!},
                            "tooltip": "@lang('Energy')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"\">{!! $resources['energy']['amount_formatted'] !!}<\/span><\/td><\/tr><tr><th>Current production:<\/th><td><span class=\"{{ $resources['energy']['production'] > 0 ? 'undermark' : 'overmark' }}\">{{ $resources['energy']['production'] > 0 ? '+' : '' }}{!! $resources['energy']['production_formatted'] !!}<\/span><\/td><\/tr><tr><th>@lang('Consumption')<\/th><td><span class=\"{{ $resources['energy']['consumption'] > 0 ? 'overmark' : '' }}\">{{ $resources['energy']['consumption'] > 0 ? '-' : '' }}{!! $resources['energy']['consumption_formatted'] !!}<\/span><\/td><\/tr><\/table>",
                            "classesListItem": ""
                        },
                        "darkmatter": {
                            "amount": {!! $resources['darkmatter']['amount'] !!},
                            "tooltip": "@lang('Dark Matter')|<table class=\"resourceTooltip\"><tr><th>@lang('Available'):<\/th><td><span class=\"\">{!! $resources['darkmatter']['amount_formatted'] !!}<\/span><\/td><\/tr><\/table>",
                            "classesListItem": "",
                            "classes": "overlay",
                            "link": "#TODO_page=payment",
                            "img": "/img/icons/401d1a91ff40dc7c8acfa4377d3d65.gif"
                        }
                    },
                    "techs": {
                        // TODO: add tech levels as far as they are available
                    },
                    "honorScore": 11,
                });

                function updateAjaxResourcebox(data) {
                    reloadResources(data);
                }

                function getAjaxResourcebox(callback) {
                    $.get("{{ route('overview.index') }}#TODO_page=fetchResources&ajax=1", function (data) {
                        reloadResources(data, callback);
                    }, "text");
                }

                var changeSettingsLink = "#TODO_page=changeSettings";
                var changeSettingsToken = "ea77594feda8933a60595311a0f56512";
                var eventlistLink = "{{ route('fleet.eventlist.fetch') }}";

                function openAnnouncement() {
                    openOverlay("{{ route('overview.index') }}#TODO_page=announcement&ajax=1", {
                        'class': 'announcement',
                        zIndex: 4000
                    });
                }

                var planetMoveLoca = {
                    "askTitle": "Resettle Planet",
                    "askCancel": "Are you sure that you wish to cancel this planet relocation? The normal waiting time will thereby be maintained.",
                    "yes": "yes",
                    "no": "No",
                    "success": "The planet relocation was successfully cancelled.",
                    "error": "Error"
                };

                function openPlanetRenameGiveupBox() {
                    openOverlay("{{ route('planetabandon.overlay') }}", {
                        title: "Abandon\/Rename {{ $currentPlanet->getPlanetName() }}",
                        'class': "planetRenameOverlay"
                    });
                }

                var locaPremium = {
                    "buildingHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
                    "buildingFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?",
                    "shipsHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
                    "shipsFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?",
                    "researchHalfOverlay": "Do you want to reduce the research time by 50% of the total research time () for <b>750 Dark Matter<\/b>?",
                    "researchFullOverlay": "Do you want to immediately complete the research order for <b>750 Dark Matter<\/b>?"
                };
                var priceBuilding = 750;
                var priceResearch = 750;
                var priceShips = 750;
                var loca = loca || {};
                loca = $.extend({}, loca, {
                    "error": "Error",
                    "errorNotEnoughDM": "Not enough Dark Matter available! Do you want to buy some now?",
                    "notice": "Reference",
                    "planetGiveupQuestion": "Are you sure you want to abandon the planet %planetName% %planetCoordinates%?",
                    "moonGiveupQuestion": "Are you sure you want to abandon the moon %planetName% %planetCoordinates%?"
                });

                function type() {
                    var destination = document.getElementById(textDestination[currentIndex]);
                    if (destination) {
                        if (textContent[currentIndex].substr(currentChar, 1) == "<" && linetwo != 1) {
                            while (textContent[currentIndex].substr(currentChar, 1) != ">") {
                                currentChar++;
                            }
                        }
                        if (linetwo == 1) {
                            destination.innerHTML = textContent[currentIndex];
                            currentChar = destination.innerHTML = textContent[currentIndex].length + 1;
                        } else {
                            destination.innerHTML = textContent[currentIndex].substr(0, currentChar) + "_";
                            currentChar++;
                        }
                        if (currentChar > textContent[currentIndex].length) {
                            destination.innerHTML = textContent[currentIndex];
                            currentIndex++;
                            currentChar = 0;
                            if (currentIndex < textContent.length) {
                                type();
                            }
                        } else {
                            setTimeout("type()", 15);
                        }
                    }
                }

                function planetRenamed(data) {
                    if (data["status"]) {
                        $("#planetNameHeader").html(data["newName"]);
                        reloadPage();
                        $(".overlayDiv.planetRenameOverlay").dialog('close');
                    }
                    errorBoxAsArray(data["errorbox"]);
                }

                function reloadPage() {
                    location.href = "{{ url()->current() }}";
                }

                var demolish_id;
                var buildUrl;

                function loadDetails(type) {
                    url = "{{ route('overview.index', ['ajax' => 1]) }}";
                    if (typeof (detailUrl) != 'undefined') {
                        url = detailUrl;
                    }
                    $.get(url, {type: type}, function (data) {
                        $("#detail").html(data);
                        $("#techDetailLoading").hide();
                        $("input[type='text']:first", document.forms["form"]).focus();
                        $(document).trigger("ajaxShowElement", (typeof techID === 'undefined' ? 0 : techID));
                    });
                }

                function sendBuildRequest(url, ev, showSlotWarning) {
                    console.debug("sendBuildRequest");
                    if (ev != undefined) {
                        var keyCode;
                        if (window.event) {
                            keyCode = window.event.keyCode;
                        } else if (ev) {
                            keyCode = ev.which;
                        } else {
                            return true;
                        }
                        console.debug("KeyCode: " + keyCode);
                        if (keyCode != 13 || $('#premiumConfirmButton')) {
                            return true;
                        }
                    }

                    function build() {
                        if (url == null) {
                            sendForm();
                        } else {
                            fastBuild();
                        }
                    }

                    if (url == null) {
                        fallBackFunc = sendForm;
                    } else {
                        fallBackFunc = build;
                        buildUrl = url;
                    }
                    if (showSlotWarning) {
                        build();
                    } else {
                        build();
                    }
                    return false;
                }

                function fastBuild() {
                    location.href = buildUrl;
                    return false;
                }

                function sendForm() {
                    document.form.submit();
                    return false;
                }

                function demolishBuilding(id, question) {
                    demolish_id = id;
                    question += "<br/><br/>" + $("#demolish" + id).html();
                    errorBoxDecision("Caution", "" + question + "", "yes", "No", demolishStart);
                }

                function demolishStart() {
                    window.location.replace("{{ route('resources.index', ['modus' => 3]) }}&token=9c8a2a05984ebfd30e88ea2fd9da03df&type=" + demolish_id);
                }

                $('#planet').find('h2 a').hover(function () {
                    $('#planet').find('h2 a img').toggleClass('hinted');
                }, function () {
                    $('#planet').find('h2 a img').toggleClass('hinted');
                });
                var player = {hasCommander: false};
                var localizedBBCode = {
                    "bold": "Bold",
                    "italic": "Italic",
                    "underline": "Underline",
                    "stroke": "Strikethrough",
                    "sub": "Subscript",
                    "sup": "Superscript",
                    "fontColor": "Font colour",
                    "fontSize": "Font size",
                    "backgroundColor": "Background colour",
                    "backgroundImage": "Background image",
                    "tooltip": "Tool-tip",
                    "alignLeft": "Left align",
                    "alignCenter": "Centre align",
                    "alignRight": "Right align",
                    "alignJustify": "Justify",
                    "block": "Break",
                    "code": "Code",
                    "spoiler": "Spoiler",
                    "moreopts": "More Options",
                    "list": "List",
                    "hr": "Horizontal line",
                    "picture": "Image",
                    "link": "Link",
                    "email": "Email",
                    "player": "Player",
                    "item": "Item",
                    "coordinates": "Coordinates",
                    "preview": "Preview",
                    "textPlaceHolder": "Text...",
                    "playerPlaceHolder": "Player ID or name",
                    "itemPlaceHolder": "Item ID",
                    "coordinatePlaceHolder": "Galaxy:system:position",
                    "charsLeft": "Characters remaining",
                    "colorPicker": {
                        "ok": "Ok",
                        "cancel": "Cancel",
                        "rgbR": "R",
                        "rgbG": "G",
                        "rgbB": "B"
                    },
                    "backgroundImagePicker": {
                        "ok": "Ok",
                        "repeatX": "Repeat horizontally",
                        "repeatY": "Repeat vertically"
                    }
                }, itemNames = {
                    "090a969b05d1b5dc458a6b1080da7ba08b84ec7f": "Bronze Crystal Booster",
                    "e254352ac599de4dd1f20f0719df0a070c623ca8": "Bronze Deuterium Booster",
                    "b956c46faa8e4e5d8775701c69dbfbf53309b279": "Bronze Metal Booster",
                    "3c9f85221807b8d593fa5276cdf7af9913c4a35d": "Bronze Crystal Booster",
                    "422db99aac4ec594d483d8ef7faadc5d40d6f7d3": "Silver Crystal Booster",
                    "118d34e685b5d1472267696d1010a393a59aed03": "Gold Crystal Booster",
                    "d3d541ecc23e4daa0c698e44c32f04afd2037d84": "DETROID Bronze",
                    "0968999df2fe956aa4a07aea74921f860af7d97f": "DETROID Gold",
                    "27cbcd52f16693023cb966e5026d8a1efbbfc0f9": "DETROID Silver",
                    "d9fa5f359e80ff4f4c97545d07c66dbadab1d1be": "Bronze Deuterium Booster",
                    "e4b78acddfa6fd0234bcb814b676271898b0dbb3": "Silver Deuterium Booster",
                    "5560a1580a0330e8aadf05cb5bfe6bc3200406e2": "Gold Deuterium Booster",
                    "40f6c78e11be01ad3389b7dccd6ab8efa9347f3c": "KRAKEN Bronze",
                    "929d5e15709cc51a4500de4499e19763c879f7f7": "KRAKEN Gold",
                    "4a58d4978bbe24e3efb3b0248e21b3b4b1bfbd8a": "KRAKEN Silver",
                    "de922af379061263a56d7204d1c395cefcfb7d75": "Bronze Metal Booster",
                    "ba85cc2b8a5d986bbfba6954e2164ef71af95d4a": "Silver Metal Booster",
                    "05294270032e5dc968672425ab5611998c409166": "Gold Metal Booster",
                    "be67e009a5894f19bbf3b0c9d9b072d49040a2cc": "Bronze Moon Fields",
                    "05ee9654bd11a261f1ff0e5d0e49121b5e7e4401": "Gold Moon Fields",
                    "c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf": "Silver Moon Fields",
                    "485a6d5624d9de836d3eb52b181b13423f795770": "Bronze M.O.O.N.S.",
                    "45d6660308689c65d97f3c27327b0b31f880ae75": "Gold M.O.O.N.S.",
                    "fd895a5c9fd978b9c5c7b65158099773ba0eccef": "Silver M.O.O.N.S.",
                    "da4a2a1bb9afd410be07bc9736d87f1c8059e66d": "NEWTRON Bronze",
                    "8a4f9e8309e1078f7f5ced47d558d30ae15b4a1b": "NEWTRON Gold",
                    "d26f4dab76fdc5296e3ebec11a1e1d2558c713ea": "NEWTRON Silver",
                    "16768164989dffd819a373613b5e1a52e226a5b0": "Bronze Planet Fields",
                    "04e58444d6d0beb57b3e998edc34c60f8318825a": "Gold Planet Fields",
                    "0e41524dc46225dca21c9119f2fb735fd7ea5cb3": "Silver Planet Fields"
                };
                $(document).ready(function () {
                    initIndex();
                    initOverview();
                    initBuffBar();
                    tabletInitOverviewAdvice();

                    ogame.chat.showPlayerList('#chatBarPlayerList .cb_playerlist_box');
                    ogame.chat.showPlayerList('#sideBar');
                    var initChatAsyncInterval = window.setInterval(initChatAsync, 100);

                    function initChatAsync() {
                        if (ogame.chat.isLoadingPlayerList === false && ogame.chat.playerList !== null) {
                            clearInterval(initChatAsyncInterval);
                            ogame.chat.initChatBar(playerId);
                            ogame.chat.initChat(playerId, isMobile);
                            ogame.chat.updateCustomScrollbar($('.scrollContainer'));
                        }
                    }

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                });</script>            <!-- END JAVASCRIPT -->


            @yield('content')
        </div>
    </div>
    <div id="right">
        <div id="planetbarcomponent" class="">
            <div id="rechts">
                @php
                    // Get all current query parameters
                    $currentQueryParams = request()->query();
                    $totalPlanets = $currentPlayer->planets->planetCount();
                    $useCompactLayout = $totalPlanets >= 6;
                @endphp
                <div id="{{ $useCompactLayout ? 'cutty' : 'norm' }}">
                    <div id="{{ $useCompactLayout ? 'myPlanets' : 'myWorlds' }}">
                        <div id="countColonies">
                            <p class="textCenter">
                                <span>{{ $currentPlayer->planets->planetCount() }}/{{ $currentPlayer->getMaxPlanetAmount() }}</span> @lang('Planets')
                            </p>
                        </div>
                        <div id="planetList">
                            @foreach ($planets->allPlanets() as $key => $planet)
                                @php
                                    // Set or replace the 'cp' parameter
                                    $currentQueryParams['cp'] = $planet->getPlanetId();
                                    // Generate the URL to the current route with the updated query parameters
                                    $urlToPlanetWithUpdatedParam = request()->url() . '?' . http_build_query($currentQueryParams);
                                @endphp
                                <div class="smallplanet {{ $useCompactLayout ? 'smaller' : '' }} {{ ($planet->getPlanetId() === $currentPlanet->getPlanetId() && $currentPlayer->planets->allCount() > 1) ? 'hightlightPlanet' : '' }}"
                                     data-planet-id="{{ $planet->getPlanetId() }}" id="planet-{{ $key + 1 }}">
                                    <a href="{{ $urlToPlanetWithUpdatedParam }}"
                                       data-link="{{ $urlToPlanetWithUpdatedParam }}"
                                       title="<b>{{ $planet->getPlanetName() }} [{{ $planet->getPlanetCoordinates()->asString() }}]</b><br/>
                                        @lang('Lifeform'): Humans<br/>
                                        {{ OGame\Facades\AppUtil::formatNumber($planet->getPlanetDiameter()) }}km ({{ $planet->getBuildingCount() }}/{{ $planet->getPlanetFieldMax() }})<br>
                                        {{ $planet->getPlanetTempMin() }} to {{ $planet->getPlanetTempMax() }}°C<br/>
                                        <a href=&quot;{{ route('overview.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Overview')</a><br/>
                                        <a href=&quot;{{ route('resources.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Resources')</a><br/>
                                        <a href=&quot;{{ route('research.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Research')</a><br/>
                                        <a href=&quot;{{ route('facilities.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Facilities')</a><br/>
                                        <a href=&quot;{{ route('shipyard.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Shipyard')</a><br/>
                                        <a href=&quot;{{ route('defense.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Defense')</a><br/>
                                        <a href=&quot;{{ route('fleet.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Fleet')</a><br/>
                                        <a href=&quot;{{ route('galaxy.index') }}?cp={{ $planet->getPlanetId() }}&quot;>@lang('Galaxy')</a><br/>"
                                       class="planetlink {{ ($planet->getPlanetId() === $currentPlanet->getPlanetId() && $currentPlayer->planets->allCount() > 1) ? 'active' : '' }} tooltipRight tooltipClose js_hideTipOnMobile ipiHintable"
                                       data-ipi-hint="ipiPlanetHomeplanet">
                                        @if ($useCompactLayout)
                                            <div class="planetBarSpaceObjectContainer" style="height: 33px">
                                                <div class="planetBarSpaceObjectHighlightContainer" style="width: 25px; height: 25px;"></div>
                                                <img id="planetBarSpaceObjectImg_{{ $planet->getPlanetId() }}"
                                                     class="planetPic js_replace2x"
                                                     alt="{{ $planet->getPlanetName() }}"
                                                     src="{!! asset('img/planets/medium/' . $planet->getPlanetBiomeType() . '_' . $planet->getPlanetImageType() . '.png') !!}"
                                                     width="30" height="30">
                                            </div>
                                        @else
                                            <img class="planetPic js_replace2x"
                                                 alt="{{ $planet->getPlanetName() }}"
                                                 src="{!! asset('img/planets/medium/' . $planet->getPlanetBiomeType() . '_' . $planet->getPlanetImageType() . '.png') !!}"
                                                 width="48" height="48">
                                        @endif
                                        <span class="planet-name ">{!! $planet->getPlanetName() !!}</span>
                                        <span class="planet-koords ">[{!! $planet->getPlanetCoordinates()->asString() !!}]</span>
                                    </a>

                                    @if ($planet->isBuilding())
                                        <a class="constructionIcon tooltip js_hideTipOnMobile tpd-hideOnClickOutside"
                                           data-link="{{ $urlToPlanetWithUpdatedParam }}"
                                           href="{{ $urlToPlanetWithUpdatedParam }}"
                                           title="">
                                            @if ($planet->isDowngrading())
                                                <span class="icon12px icon_wrench_red"></span>
                                            @else
                                                <span class="icon12px icon_wrench"></span>
                                            @endif
                                        </a>
                                    @endif

                                    @if ($planet->hasMoon())
                                        @php
                                            $moon = $planet->moon();
                                            $currentQueryParams['cp'] = $moon->getPlanetId();
                                            $urlToMoonWithUpdatedParam = request()->url() . '?' . http_build_query($currentQueryParams);
                                        @endphp
                                        <a class="moonlink {{ ($moon->getPlanetId() === $currentPlanet->getPlanetId() && $currentPlayer->planets->allCount() > 1) ? 'active' : '' }} tooltipLeft tooltipClose js_hideTipOnMobile"
                                           title="<b>{{ $moon->getPlanetName() }} [{{ $moon->getPlanetCoordinates()->asString() }}]</b><br>
                                           {{ OGame\Facades\AppUtil::formatNumber($moon->getPlanetDiameter()) }}km ({{ $moon->getBuildingCount() }}/{{ $moon->getPlanetFieldMax() }})<br/>
                                           <a href=&quot;{{ route('overview.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Overview')</a><br/>
                                           <a href=&quot;{{ route('resources.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Resources')</a><br/>
                                           <a href=&quot;{{ route('facilities.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Facilities')</a><br/>
                                           <a href=&quot;{{ route('defense.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Defense')</a><br/>
                                           <a href=&quot;{{ route('fleet.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Fleet')</a><br/>
                                           <a href=&quot;{{ route('galaxy.index') }}?cp={{ $moon->getPlanetId() }}&quot;>@lang('Galaxy')</a><br/>"
                                           href="{{ $urlToMoonWithUpdatedParam }}"
                                           data-link="{{ $urlToMoonWithUpdatedParam }}"
                                           data-jumpgatelevel="0">
                                            @if ($useCompactLayout)
                                                <div class="planetBarSpaceObjectContainer" style="height: 20px">
                                                    <div class="planetBarSpaceObjectHighlightContainer" style="width: 16px; height: 16px;"></div>
                                                    <img id="planetBarSpaceObjectImg_{{ $moon->getPlanetId() }}"
                                                         src="/img/moons/small/{{ $moon->getPlanetImageType() }}.gif"
                                                         width="16" height="16"
                                                         alt="Moon"
                                                         class="icon-moon">
                                                </div>
                                            @else
                                                <img src="/img/moons/small/{{ $moon->getPlanetImageType() }}.gif"
                                                     width="16" height="16"
                                                     alt="Moon"
                                                     class="icon-moon">
                                            @endif
                                        </a>
                                    @endif

                                    @php
                                        // Check for wreck field at this planet's coordinates
                                        // Only show wreck field icon for planets, not moons
                                        // Load active or blocked wreck field (skip repairing ones)
                                        $wreckFieldService = new \OGame\Services\WreckFieldService($currentPlayer, app(\OGame\Services\SettingsService::class));
                                        $wreckFieldLoaded = $wreckFieldService->loadActiveOrBlockedForCoordinates($planet->getPlanetCoordinates());
                                        $wreckField = null;

                                        if ($wreckFieldLoaded) {
                                            $wreckFieldModel = $wreckFieldService->getWreckField();
                                            if ($wreckFieldModel && $wreckFieldModel->getTotalShips() > 0) {
                                                $wreckField = $wreckFieldModel;
                                            }
                                        }
                                    @endphp

                                    @if ($wreckField && in_array($wreckField->status, ['active', 'blocked']) && !$planet->isMoon())
                                        @php
                                            $hasSpaceDock = $currentPlayer->planets->current()->getObjectLevel('space_dock') > 0;
                                            $isOwner = $wreckField->owner_player_id === $currentPlayer->getId();
                                        @endphp
                                        @if ($isOwner && $hasSpaceDock)
                                        <a class="wreckFieldIcon tooltip js_hideTipOnMobile"
                                           title="Wreckage"
                                           href="javascript:void(0);" onclick="openFacilitiesSpaceDock();">
                                            <span class="icon icon_wreck_field"></span>
                                        </a>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="bannerSkyscrapercomponent" class="">
            <div id="banner_skyscraper" class="desktop" name="banner_skyscraper">
                <div style="position: relative;">
                    <a class="tooltipLeft " title="" href="#TODO=shop">
                        <img src="/img/banners/de0dadddb0285ba78b026ce18fc898.jpg" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Bar -->
<div id="chatBar">
    <ul class="chat_bar_list">
        <li id="chatBarPlayerList" class="chat_bar_pl_list_item">
            <div class="cb_playerlist_box"
                 style="display:none;">
            </div>
            <span class="onlineCount">@lang(':count Contact(s) online', ['count' => $onlineBuddiesCount])</span>
        </li>
    </ul><!-- END Chat Bar List -->
</div>
<!-- END Chat Bar -->

<button class="scroll_to_top">
    <span class="arrow"></span>@lang('Back to top')
</button>

<div id="siteFooter">
    <div class="content" style="font-size:10px">
        <div class="fleft textLeft">
            <a href="#TODO_changelog&ajax=1" class="tooltip js_hideTipOnMobile overlay" data-class="noXScrollbar"
               data-overlay-iframe="true" data-iframe-width="680" data-overlay-title="Patch notes">
                {{ \OGame\Facades\GitInfoUtil::getAppVersion() }}</a>
            <a class="homeLink" href="https://github.com/lanedirt/ogamex" target="_blank">©
                OGameX. @lang('All rights reserved.')</a>
        </div>
        <div class="fright textRight">
            <a href="{{ route('serversettings.overlay') }}" class="overlay"
               data-overlay-title="@lang('Server Settings')" data-overlay-class="serversettingsoverlay"
               data-overlay-popup-width="400" data-overlay-popup-height="510">@lang('Server Settings')</a>|
            <a href="http://wiki.ogame.org/" target="_blank">Help</a>|
            @switch ($locale)
                @case('en')
                    <a href="{{ route('language.switch', ['lang' => 'en']) }}" class="bold">English</a>|
                    <a href="{{ route('language.switch', ['lang' => 'nl']) }}">Dutch</a>|
                    @break
                @case('nl')
                    <a href="{{ route('language.switch', ['lang' => 'en']) }}">English</a>|
                    <a href="{{ route('language.switch', ['lang' => 'nl']) }}" class="bold">Dutch</a>|
                    @break
                @default
                    <a href="{{ route('language.switch', ['lang' => 'en']) }}" class="bold">English</a>|
                    <a href="{{ route('language.switch', ['lang' => 'nl']) }}">Dutch</a>|
            @endswitch
            <a href="#">Board</a>|
            <a class="overlay-temp" href="#" data-overlay-iframe="true" data-iframe-width="450"
               data-overlay-title="Rules">@lang('Rules')</a>|
            <a href="#">@lang('Legal')</a>
        </div>
    </div><!-- -->
</div>

<!-- #MMO:NETBAR# -->
<div id="pagefoldtarget"></div>

<!-- ogame/en ingame 16.12.2017 12:46 -->
<script type="text/javascript">
    //mmoInitSelect();
    //mmoTicker();    mmoToggleDisplay.init("mmoGamesOverviewPanel");

    @if (\Session::has('success'))
    $(document).ready(function () {
        fadeBox("{!! \Session::get('success') !!}", 0);
    });
    @endif
    @if (\Session::has('error'))
    $(document).ready(function () {
        fadeBox("{!! \Session::get('error') !!}", 1);
    });
    @endif
    @if (\Session::has('success_logout'))
    $(document).ready(function () {
        errorBoxNotify("Ok", "{!! \Session::get('success_logout') !!}", "Ok", redirectLogout);
    });
    @endif
</script>


<!-- #/MMO:NETBAR# -->
<div id="decisionTB" style="display:none;">
    <div id="errorBoxDecision" class="errorBox TBfixedPosition">
        <div class="head"><h4 id="errorBoxDecisionHead">-</h4></div>
        <div class="middle">
            <span id="errorBoxDecisionContent">-</span>
            <div class="response">
                <div style="float:left; width:180px;">
                    <a href="javascript:void(0);" class="yes"><span id="errorBoxDecisionYes">.</span></a>
                </div>
                <div style="float:left; width:180px;">
                    <a href="javascript:void(0);" class="no"><span id="errorBoxDecisionNo">.</span></a>
                </div>
                <br class="clearfloat"/>
            </div>
        </div>
        <div class="foot"></div>
    </div>
</div>

<div id="fadeBox" class="fadeBox fixedPostion" style="display:none;">
    <span id="fadeBoxStyle" class="success"></span>
    <p id="fadeBoxContent"></p>
</div>

<div id="notifyTB" style="display:none;">
    <div id="errorBoxNotify" class="errorBox TBfixedPosition">
        <div class="head"><h4 id="errorBoxNotifyHead">-</h4></div>
        <div class="middle">
            <span id="errorBoxNotifyContent">-</span>
            <div class="response">
                <div>
                    <a href="javascript:void(0);" class="ok">
                        <span id="errorBoxNotifyOk">.</span>
                    </a>
                </div>
                <br class="clearfloat"/>
            </div>
        </div>
        <div class="foot"></div>
    </div>
</div>
<script type="text/javascript">var visibleChats = {"players": [], "associations": []};
    var bigChatLink = "{{ route('overview.index') }}#TODO_page=chat";
    var locaKeys = {
        "bold": "@lang('Bold')",
        "italic": "@lang('Italic')",
        "underline": "@lang('Underline')",
        "stroke": "@lang('Strikethrough')",
        "sub": "@lang('Subscript')",
        "sup": "@lang('Superscript')",
        "fontColor": "@lang('Font colour')",
        "fontSize": "@lang('Font size')",
        "backgroundColor": "@lang('Background colour')",
        "backgroundImage": "@lang('Background image')",
        "tooltip": "@lang('Tool-tip')",
        "alignLeft": "@lang('Left align')",
        "alignCenter": "@lang('Centre align')",
        "alignRight": "@lang('Right align')",
        "alignJustify": "@lang('Justify')",
        "block": "@lang('Break')",
        "code": "@lang('Code')",
        "spoiler": "@lang('Spoiler')",
        "moreopts": "@lang('More Options')",
        "list": "@lang('List')",
        "hr": "@lang('Horizontal line')",
        "picture": "@lang('Image')",
        "link": "@lang('Link')",
        "email": "@lang('Email')",
        "player": "@lang('Player')",
        "item": "@lang('Item')",
        "coordinates": "@lang('Coordinates')",
        "preview": "@lang('Preview')",
        "textPlaceHolder": "@lang('Text...')",
        "playerPlaceHolder": "@lang('Player ID or name')",
        "itemPlaceHolder": "@lang('Item ID')",
        "coordinatePlaceHolder": "@lang('Galaxy:system:position')",
        "charsLeft": "@lang('Characters remaining')",
        "colorPicker": {"ok": "@lang('Ok')", "cancel": "@lang('Cancel')", "rgbR": "R", "rgbG": "G", "rgbB": "B"},
        "backgroundImagePicker": {
            "ok": "Ok",
            "repeatX": "@lang('Repeat horizontally')",
            "repeatY": "@lang('Repeat vertically')"
        }
    };</script>
</body>
</html>
