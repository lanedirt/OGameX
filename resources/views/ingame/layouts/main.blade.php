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
     Version: {{ \OGame\Utils\GitInfoUtil::getAppVersionBranchCommit() }}

    This application is released under the MIT License. For more details, visit the GitHub repository.
    -->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="/img/icons/20da7e6c416e6cd5f8544a73f588e5.png"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Language" content="en"/>
    <meta name="ogame-session" content="3c442273a6de4c8f79549e78f4c3ca50e7ea7580"/>
    <meta name="ogame-version" content="{{ \OGame\Utils\GitInfoUtil::getAppVersion() }}"/>
    <meta name="ogame-timestamp" content="1513426692"/>
    <meta name="ogame-universe" content="s1"/>
    <meta name="ogame-universe-name" content="Home"/>
    <meta name="ogame-universe-speed" content="5"/>
    <meta name="ogame-universe-speed-fleet" content="5"/>
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
        var inventoryObj;
        $.holdReady(true);

        var s = setInterval(function () {
            if (typeof initEmpireEquipment === "function") {
                $.holdReady(false);
                clearInterval(s);
            }
        }, 1);
    </script>
</head>
<body id="ingamepage" class="ogame lang-en default no-touch">
<div id="initial_welcome_dialog" title="Welcome to OGame!" style="display: none;">
    To help your game start get moving quickly, we’ve assigned you the name Commodore Nebula. You can change this at any
    time by clicking on the username.<br/>
    Fleet Command has left you information on your first steps in your inbox, to help you be well-equipped for your
    start.<br/>
    <br/>
    Have fun playing!
</div>

<div id="pageContent">
    <div id="top">
        <div id="pageReloader" onclick="javascript: redirectOverview();"></div>
        <div id="headerbarcomponent" class="">
            <div id="bar">
                <ul>
                    <li id="playerName">
                        @lang('Player'):
                        <selected-language-icon style="background-image: url('/img/flags/a176fcd6f3e3de2bed6a73a8b1d5e7.png');"></selected-language-icon>

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
                        (0)
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
                            @lang('Buddies')</a>
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
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">@lang('Log out')</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <li class="OGameClock">16.12.2017 <span>12:18:12</span></li>
                </ul>
            </div>
        </div>
        <div id="resourcesbarcomponent" class="">
            <div id="resources">

                <div class="resource_tile metal">
                    <div id="metal_box" class="metal tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="Metal|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['metal']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['metal']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['metal']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>" data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1=" data-ipi-hint="ipiResourcemetal">
                        <div class="resourceIcon metal"></div>
                        <span class="value">
                        <span id="resources_metal" class="{{ $resources['metal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['metal']['amount'] >= $resources['metal']['storage'] ? 'overmark' : '' }}" data-raw="{!! $resources['metal']['amount'] !!}">{!! $resources['metal']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile crystal">
                    <div id="crystal_box" class="crystal tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="@lang('Crystal')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['crystal']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['crystal']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['crystal']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>" data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1=" data-ipi-hint="ipiResourcecrystal">
                        <div class="resourceIcon crystal"></div>
                        <span class="value">
                        <span id="resources_crystal" class="{{ $resources['crystal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['crystal']['amount'] >= $resources['crystal']['storage'] ? 'overmark' : '' }}" data-raw="{!! $resources['crystal']['amount'] !!}">{!! $resources['crystal']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile deuterium">
                    <div id="deuterium_box" class="deuterium tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="@lang('Deuterium')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['deuterium']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Storage capacity')</th><td><span class=&quot;&quot;>{!! $resources['deuterium']['storage_formatted'] !!}</span></td></tr><tr><th>@lang('Current production'):</th><td><span class=&quot;undermark&quot;>+{!! $resources['deuterium']['production_hour'] !!}</span></td></tr><tr><th>@lang('Den Capacity'):</th><td><span class=&quot;middlemark&quot;>0</span></td></tr></table>" data-shop-url="#TODO_shop#category=d8d49c315fa620d9c7f1f19963970dea59a0e3be&amp;item=859d82d316b83848f7365d21949b3e1e63c7841f&amp;page=shop&amp;panel1-1=" data-ipi-hint="ipiResourcedeuterium">
                        <div class="resourceIcon deuterium"></div>
                        <span class="value">
                        <span id="resources_deuterium" class="{{ $resources['deuterium']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['deuterium']['amount'] >= $resources['deuterium']['storage'] ? 'overmark' : '' }}" data-raw="{!! $resources['deuterium']['amount'] !!}">{!! $resources['deuterium']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile energy">
                    <div id="energy_box" class="energy tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="@lang('Energy')|<table class=&quot;resourceTooltip&quot;><tr><th>@lang('Available'):</th><td><span class=&quot;&quot;>{!! $resources['energy']['amount_formatted'] !!}</span></td></tr><tr><th>@lang('Current production:')</th><td><span class=&quot;undermark&quot;>+{!! $resources['energy']['production_formatted'] !!}</span></td></tr><tr><th>@lang('Consumption')</th><td><span class=&quot;overmark&quot;>-{!! $resources['energy']['consumption_formatted'] !!}</span></td></tr></table>" data-ipi-hint="ipiResourceenergy">
                        <div class="resourceIcon energy"></div>
                        <span class="value">
                        <span id="resources_energy" class="{{ $resources['energy']['amount'] < 0 ? 'overmark' : '' }}" data-raw="{!! $resources['energy']['amount'] !!}">{!! $resources['energy']['amount_formatted'] !!}</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile population">
                    <div id="population_box" class="population tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="Population|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;overmark&quot;>100</span></td></tr><tr><th>Living Space
</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Satisfied</th><td><span class=&quot;undermark&quot;>0</span></td></tr><tr><th>Hungry</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Growth rate</th><td><span class=&quot;&quot;>±0</span></td></tr><tr><th>Bunker Space
</th><td><span class=&quot;middlemark&quot;>100</span></td></tr></table>" data-ipi-hint="ipiResourcepopulation">
                        <div class="resourceIcon population"></div>
                        <span class="value">
                        <span id="resources_population" data-raw="100" class="overmark">100</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile food">
                    <div id="food_box" class="food tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="Food|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Storage capacity</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Overproduction</th><td><span class=&quot;undermark&quot;>0</span></td></tr><tr><th>Consumption</th><td><span class=&quot;overmark&quot;>0</span></td></tr><tr><th>Consumed in</th><td><span class=&quot;overmark timeTillFoodRunsOut&quot;>~</span></td></tr></table>" data-ipi-hint="ipiResourcefood">
                        <div class="resourceIcon food"></div>
                        <span class="value">
                        <span id="resources_food" data-raw="0" class="overmark">0</span>
                    </span>
                    </div>
                </div>
                <div class="resource_tile darkmatter">
                    <div id="darkmatter_box" class="darkmatter tooltipHTML resource ipiHintable tpd-hideOnClickOutside" title="@lang('Dark Matter')|<table class=&quot;resourceTooltip&quot;><tr><th>Available:</th><td><span class=&quot;&quot;>19,890</span></td></tr><tr><th>Purchased</th><td><span class=&quot;&quot;>225</span></td></tr><tr><th>Found</th><td><span class=&quot;&quot;>19,665</span></td></tr></table>" data-tooltip-button="Purchase Dark Matter" data-ipi-hint="ipiResourcedarkmatter">
                        <a href="#TODO_page=payment" class="overlay">
                            <img src="/img/icons/401d1a91ff40dc7c8acfa4377d3d65.gif">
                            <div class="resourceIcon darkmatter"></div>
                        </a>
                        <span class="value">
                        <span id="resources_darkmatter" data-raw="19890" class="overlay">19,890</span>
                    </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="commandercomponent" class="">
            <div id="lifeform" class="fleft">
                <a href="#TODO_page=ingame&amp;component=lfsettings" class="tooltipHTML js_hideTipOnMobile ipiHintable" title="Lifeform|No lifeforms
" data-ipi-hint="ipiLifeformSettings">
                    <div class="resourceIcon population"></div>
                </a>
            </div>
            <div id="characterclass" class="fleft">
                <a href="#TODO_=ingame&amp;component=characterclassselection" class="tooltipHTML js_hideTipOnMobile ipiHintable" title="Your class: Collector|+25% mine production<br>+10% energy production<br>+100% speed for Transporters<br>+25% cargo bay for Transporters<br>+50% Crawler bonus<br>+10% more usable Crawlers with Geologist<br>Overload the Crawlers up to 150%<br>+10% discount on acceleration (building)" data-ipi-hint="ipiCharacterclassSettings">
                    <div class="sprite characterclass medium miner"></div>
                </a>
            </div>
            <div id="officers" class="  fright">
                <a href="#TODO_=premium&amp;openDetail=2" class="tooltipHTML   commander js_hideTipOnMobile " title="Hire commander|&amp;#43;40 favorites, building queue, shortcuts, transport scanner, advertisement-free* <span style=&quot;font-size: 10px; line-height: 10px&quot;>(*excludes: game related references)</span>">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=3" class="tooltipHTML    admiral js_hideTipOnMobile " title="Hire admiral|Max. fleet slots +2,
Max. expeditions +1,
Improved fleet escape rate,
Combat simulation save slots +20">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=4" class="tooltipHTML    engineer js_hideTipOnMobile " title="Hire engineer|Halves losses to defenses, +10% energy production">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=5" class="tooltipHTML    geologist js_hideTipOnMobile " title="Hire geologist|+10% mine production">
                    <img src="/img/layout/pixel.gif" width="30" height="30">
                </a>
                <a href="#TODO_page=premium&amp;openDetail=6" class="tooltipHTML    technocrat js_hideTipOnMobile " title="Hire technocrat|+2 espionage levels, 25% less research time">
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
                        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif"/>@lang('load...')
                    </div>
                    <div id="eventboxBlank" class="textCenter" style="display: none;">
                        @lang('No fleet movement')
                    </div>
                </div>
                <div id="attack_alert" class="tooltip noAttack" title="">
                    <a href="#TODO_componentOnly&amp;component=eventList" class=" tooltipHTML js_hideTipOnMobile"></a>
                </div>
            </div>
        </div>

    </div>
    <div id="left">
        <div id="ipimenucomponent" class="">
            <div id="ipiMenuWrapper" class="ipiMenuTrackedAction ipiHintable " title="" data-ipi-hint="ipiMenu"><div id="ipimenucontent"><a href="#TODO_page=ajax&amp;component=ipioverview&amp;action=overviewLayer&amp;ajax=1" class="overlay textBeefy" data-overlay-title="" id="ipiInnerMenuContentHolder">
                        <div class="ipiMenuHead">
                            Directives
                        </div>

                        <div class="ipiMenuBody hidden"></div>
                        <div class="ipiMenuFooter hidden"></div>
                    </a>


                </div></div>
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
                            <div class="menuImage resources {{(Request::is('resources*') ? 'highlighted' : '') }}
                                ">
                            </div>
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
                                            <div class="menuImage station {{(Request::is('facilities') ? 'highlighted' : '') }}"></div>
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
                                            <a href="{{ route('merchant.index') }}#page=traderResources&amp;animation=false"
                                               class="trader tooltipRight js_hideTipOnMobile "
                                               target="_self"
                                               title="Resource Market">
                            <div class="menuImage traderOverview {{(Request::is('merchant') ? 'highlighted' : '') }}">
                            </div>
                        </a>
                                    </span>
                        <a class="menubutton premiumHighligt {{(Request::is('merchant') ? 'selected' : '') }}"
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
            </div>    </div>
        <div id="advicebarcomponent" class="">
            <div class="adviceWrapper">

                <div id="advice-bar">
                </div>
            </div>

        </div>
    </div>
    <div id="middle">
        <div id="eventlistcomponent" class="">
            <div id="eventboxContent" style="display: none;">    <div id="eventListWrap">
                    <div id="eventHeader">
                        <a class="close_details eventToggle" href="javascript:toggleEvents();">
                        </a>
                        <h2>Events</h2>
                    </div>
                    <table id="eventContent">
                        <tbody>
                        <tr class="eventFleet" id="eventRow-3717429" data-mission-type="15" data-return-flight="false" data-arrival-time="1713536448">
                            <td class="countDown">
                                <span id="counter-eventlist-3717429" class="friendly textBeefy">10m 5s</span>
                            </td>
                            <td class="arrivalTime">15:20:48 Clock</td>
                            <td class="missionFleet">
                                <img src="/img/fleet/892b08269e0e0bbde60b090099f547.gif" class="tooltipHTML" title="Own fleet | Expedition" alt="">
                            </td>

                            <td class="originFleet">
                                <figure class="planetIcon planet"></figure>Farm1
                            </td>
                            <td class="coordsOrigin">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=5&amp;system=38" target="_top">
                                    [5:38:8]
                                </a>
                            </td>

                            <td class="detailsFleet">
                                <span>58</span>
                            </td>
                            <td class="icon_movement">
            <span class="tooltip tooltipRight tooltipClose" title="<div class=&quot;htmlTooltip&quot;>
    <h1>Fleet details:</h1>
    <div class=&quot;splitLine&quot;></div>
            <table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;>
            <tr>
                <th colspan=&quot;3&quot;>Ships:</th>
            </tr>
                                                <tr>
                        <td colspan=&quot;2&quot;>Small Cargo:</td>
                        <td class=&quot;value&quot;>11</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Large Cargo:</td>
                        <td class=&quot;value&quot;>46</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Cruiser:</td>
                        <td class=&quot;value&quot;>1</td>
                    </tr>
                                                        <tr>
                    <td colspan=&quot;3&quot;>&amp;nbsp;</td>
                </tr>
                <tr>
                    <th colspan=&quot;3&quot;>Shipment:</th>
                </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Metal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Crystal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Deuterium:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Food:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    </table>
    </div>
">
                &nbsp;
            </span>
                            </td>

                            <td class="destFleet">
                                <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Deep space
                            </td>
                            <td class="destCoords">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=5&amp;system=38" target="_top">
                                    [5:38:16]
                                </a>
                            </td>

                            <td class="sendMail">
                            </td>    <td class="sendProbe">
                            </td>
                            <td class="sendMail">
                            </td>
                        </tr>

                        <tr class="eventFleet" id="eventRow-3717430" data-mission-type="15" data-return-flight="true" data-arrival-time="1713537495">
                            <td class="countDown">
                                <span id="counter-eventlist-3717430" class="friendly textBeefy">27m 32s</span>
                            </td>
                            <td class="arrivalTime">15:38:15 Clock</td>
                            <td class="missionFleet">
                                <img src="/img/fleet/892b08269e0e0bbde60b090099f547.gif" class="tooltipHTML" title="Own fleet | Expedition (R)" alt="">
                            </td>

                            <td class="originFleet">
                                <figure class="planetIcon planet"></figure>Farm1
                            </td>
                            <td class="coordsOrigin">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=5&amp;system=38" target="_top">
                                    [5:38:8]
                                </a>
                            </td>

                            <td class="detailsFleet">
                                <span>58</span>
                            </td>
                            <td class="icon_movement_reserve">
            <span class="tooltip tooltipRight tooltipClose" title="<div class=&quot;htmlTooltip&quot;>
    <h1>Fleet details:</h1>
    <div class=&quot;splitLine&quot;></div>
            <table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;>
            <tr>
                <th colspan=&quot;3&quot;>Ships:</th>
            </tr>
                                                <tr>
                        <td colspan=&quot;2&quot;>Small Cargo:</td>
                        <td class=&quot;value&quot;>11</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Large Cargo:</td>
                        <td class=&quot;value&quot;>46</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Cruiser:</td>
                        <td class=&quot;value&quot;>1</td>
                    </tr>
                                                        <tr>
                    <td colspan=&quot;3&quot;>&amp;nbsp;</td>
                </tr>
                <tr>
                    <th colspan=&quot;3&quot;>Shipment:</th>
                </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Metal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Crystal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Deuterium:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Food:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    </table>
    </div>
">
                &nbsp;
            </span>
                            </td>

                            <td class="destFleet">
                                <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Deep space
                            </td>
                            <td class="destCoords">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=5&amp;system=38" target="_top">
                                    [5:38:16]
                                </a>
                            </td>

                            <td class="sendMail">
                            </td>    <td class="sendProbe">
                            </td>
                            <td class="sendMail">
                            </td>
                        </tr>

                        <tr class="eventFleet" id="eventRow-3717244" data-mission-type="3" data-return-flight="true" data-arrival-time="1713539113">
                            <td class="countDown">
                                <span id="counter-eventlist-3717244" class="friendly textBeefy">54m 30s</span>
                            </td>
                            <td class="arrivalTime">16:05:13 Clock</td>
                            <td class="missionFleet">
                                <img src="/img/fleet/2af2939219d8227a11a50ff4df7b51.gif" class="tooltipHTML" title="Own fleet | Transport (R)" alt="">
                            </td>

                            <td class="originFleet">
                                <figure class="planetIcon planet"></figure>Farm1
                            </td>
                            <td class="coordsOrigin">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=1&amp;system=361" target="_top">
                                    [1:361:7]
                                </a>
                            </td>

                            <td class="detailsFleet">
                                <span>1</span>
                            </td>
                            <td class="icon_movement_reserve">
            <span class="tooltip tooltipRight tooltipClose" title="<div class=&quot;htmlTooltip&quot;>
    <h1>Fleet details:</h1>
    <div class=&quot;splitLine&quot;></div>
            <table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;>
            <tr>
                <th colspan=&quot;3&quot;>Ships:</th>
            </tr>
                                                <tr>
                        <td colspan=&quot;2&quot;>Large Cargo:</td>
                        <td class=&quot;value&quot;>1</td>
                    </tr>
                                                        <tr>
                    <td colspan=&quot;3&quot;>&amp;nbsp;</td>
                </tr>
                <tr>
                    <th colspan=&quot;3&quot;>Shipment:</th>
                </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Metal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Crystal:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Deuterium:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    <tr>
                        <td colspan=&quot;2&quot;>Food:</td>
                        <td class=&quot;value&quot;>0</td>
                    </tr>
                                    </table>
    </div>
">
                &nbsp;
            </span>
                            </td>

                            <td class="destFleet">
                                <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>BirbTown
                            </td>
                            <td class="destCoords">
                                <a href="#TODO=ingame&amp;component=galaxy&amp;galaxy=2&amp;system=3" target="_top">
                                    [2:3:6]
                                </a>
                            </td>

                            <td class="sendMail">
                            </td>    <td class="sendProbe">
                            </td>
                            <td class="sendMail">
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <div id="eventFooter"></div>
                </div>
        </div>

            <script type="text/javascript">
                var session = "3c442273a6de4c8f79549e78f4c3ca50e7ea7580";
                var vacation = 0;
                var timerHandler = new TimerHandler();

                function redirectPremium() {
                    location.href = "{{ route('premium.index', ['showDarkMatter' => 1]) }}#TODO_premium&showDarkMatter=1";
                }

                var playerId = "1";
                var playerName = "Admin";
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
                var bbcodePreviewUrl = "{{ route('overview.index') }}#TODO_page=bbcodePreview";
                var popupWindows = [];
                var fleetDeutSaveFactor = 1;
                var honorScore = 0;
                var darkMatter = 0;
                var serverTime = new Date(2017, 11, 16, 12, 18, 12);
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
                    "status": {"ready": "done"},
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
                var userData = {"id": "108130"};
                var missleAttackLink = "{{ route('overview.index') }}#TODO_page=missileattacklayer&width=669&height=250";
                var changeNickLink = "{{ route('changenick.overlay') }}";
                var showOutlawWarning = true;
                var miniFleetLink = "{{ route('overview.index') }}#TODO_page=minifleet&ajax=1";
                var ogameUrl = "{{ str_replace('/', '\/', URL::to('/')) }}";
                var startpageUrl = "{{ str_replace('/', '\/', URL::to('/')) }}";
                var nodePort = 19603;
                var nodeUrl = "{{ route('overview.index') }}#TODO_19603\/socket.io\/socket.io.js";
                var nodeParams = {"port": 19603, "secure": "true"};
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

                function initAjaxEventbox() {
                    reloadEventbox({"hostile": 0, "neutral": 0, "friendly": 0});
                }

                function initAjaxResourcebox() {
                    reloadResources({
                        "metal": {
                            "resources": {
                                "actualFormat": "{!! $resources['metal']['amount_formatted'] !!}",
                                "actual": {!! $resources['metal']['amount'] !!},
                                "max": {!! $resources['metal']['storage'] !!},
                                "production": {!! $resources['metal']['production_second'] !!}
                            },
                            "tooltip": "Metal|<table class=\"resourceTooltip\">\n            <tr>\n                <th>@lang('Available'):<\/th>\n                <td><span class=\"\">{!! $resources['metal']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['metal']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['metal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['metal']['production_hour'] > 0)+@endif{!! $resources['metal']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['metal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['metal']['amount'] >= $resources['metal']['storage'] ? 'overmark' : '' }}"
                        },
                        "crystal": {
                            "resources": {
                                "actualFormat": "{!! $resources['crystal']['amount_formatted'] !!}",
                                "actual": {!! $resources['crystal']['amount'] !!},
                                "max": {!! $resources['crystal']['storage'] !!},
                                "production": {!! $resources['crystal']['production_second'] !!}
                            },
                            "tooltip": "Crystal|<table class=\"resourceTooltip\">\n            <tr>\n                <th>@lang('Available'):<\/th>\n                <td><span class=\"\">{!! $resources['crystal']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['crystal']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['crystal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['crystal']['production_hour'] > 0)+@endif{!! $resources['crystal']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['crystal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['crystal']['amount'] >= $resources['crystal']['storage'] ? 'overmark' : '' }}"
                        },
                        "deuterium": {
                            "resources": {
                                "actualFormat": "{!! $resources['deuterium']['amount_formatted'] !!}",
                                "actual": {!! $resources['deuterium']['amount'] !!},
                                "max": {!! $resources['deuterium']['storage'] !!},
                                "production": {!! $resources['deuterium']['production_second'] !!}
                            },
                            "tooltip": "Deuterium|<table class=\"resourceTooltip\">\n            <tr>\n                <th>@lang('Available'):<\/th>\n                <td><span class=\"\">{!! $resources['deuterium']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['deuterium']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['deuterium']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['deuterium']['production_hour'] > 0)+@endif{!! $resources['deuterium']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['deuterium']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['deuterium']['amount'] >= $resources['deuterium']['storage'] ? 'overmark' : '' }}"
                        },
                        "energy": {
                            "resources": {
                                "actual": {!! $resources['energy']['amount'] !!},
                                "actualFormat": "{!! $resources['energy']['amount_formatted'] !!}"
                            },
                            "tooltip": "Energy|<table class=\"resourceTooltip\">\n            <tr>\n                <th>@lang('Available'):<\/th>\n                <td><span class=\"\">{!! $resources['energy']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"{{ $resources['energy']['production'] > 0 ? 'undermark' : 'overmark' }}\">{{ $resources['energy']['production'] > 0 ? '+' : '' }}{!! $resources['energy']['production_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Consumption:<\/th>\n                <td><span class=\"{{ $resources['energy']['consumption'] > 0 ? 'overmark' : '' }}\">{{ $resources['energy']['consumption'] > 0 ? '-' : '' }}{!! $resources['energy']['consumption_formatted'] !!}<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['energy']['amount'] < 0 ? 'overmark' : '' }}"
                        },
                        "darkmatter": {
                            "resources": {
                                "actual": 0,
                                "actualFormat": "0"
                            },
                            "string": "0 Dark Matter",
                            "tooltip": "Dark Matter|<table class=\"resourceTooltip\">\n                <tr>\n                    <th>@lang('Available'):<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n                <tr>\n                    <th>Purchased:<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n                <tr>\n                    <th>Found:<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n            <\/table>",
                            "class": ""
                        },
                        "honorScore": 0
                    });
                }

                function getAjaxEventbox() {
                    // TODO: re-enable
                    //$.get("{{ route('overview.index') }}#TODO_page=fetchEventbox&ajax=1", reloadEventbox, "text");
                }

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
                var eventlistLink = "{{ route('overview.index') }}#TODO_page=eventList&ajax=1";

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
                    openOverlay("{{ route('overview.index') }}#TODO_page=planetlayer", {
                        title: "Abandon\/Rename Homeworld",
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
                    var data = $.parseJSON(data);
                    if (data["status"]) {
                        $("#planetNameHeader").html(data["newName"]);
                        reloadRightmenu("{{ route('overview.index') }}#TODO_page=rightmenu&renamed=1&pageToLink=overview");
                        $(".overlayDiv.planetRenameOverlay").dialog('close');
                    }
                    errorBoxAsArray(data["errorbox"]);
                }

                function reloadPage() {
                    location.href = "{{ route('overview.index') }}";
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
                    initAjaxEventbox();
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
                <div id="norm">
                    <div id="myWorlds">
                        <div id="countColonies">
                            <p class="textCenter">
                                <span>2/2</span> Planets
                            </p>
                        </div>
                        <div id="planetList">
                            <!-- @var PlanetService $planet -->
                            @php
                                // Get all current query parameters
                                $currentQueryParams = request()->query();
                            @endphp

                            @foreach ($planets->all() as $planet)
                                @php
                                    // Set or replace the 'cp' parameter
                                   $currentQueryParams['cp'] = $planet->getPlanetId();
                                   // Generate the URL to the current route with the updated query parameters
                                   $urlToCurrentWithUpdatedParam = request()->url() . '?' . http_build_query($currentQueryParams);
                                @endphp

                                <div class="smallplanet {{ $planet->getPlanetId() == $currentPlanet->getPlanetId() ? 'hightlightPlanet' : '' }}" id="planet-{{ $planet->getPlanetId() }}">
                                    <a href="{{ $urlToCurrentWithUpdatedParam }}" data-link="{{ $urlToCurrentWithUpdatedParam }}" title="<b>{{ $planet->getPlanetName() }} [{{ $planet->getPlanetCoordinates()->asString() }}]</b><br/>@lang('Lifeform'): Humans
<br/>12,800km (152/193)<br>20°C to 60°C<br/><a href=&quot;#TODO=ingame&amp;component=overview&amp;cp=33624092&quot;>@lang('Overview')</a><br/><a href=&quot;#TODO=ingame&amp;component=supplies&amp;cp=33624092&quot;>@lang('Resources')</a><br/><a href=&quot;#TODO_page=ingame&amp;component=lfbuildings&amp;cp=33624092&quot;>@lang('Lifeform')</a><br/><a href=&quot;#TODOpage=ingame&amp;component=research&amp;cp=33624092&quot;>@lang('Research')</a><br/><a href=&quot;#TODO_page=ingame&amp;component=facilities&amp;cp=33624092&quot;>@lang('Facilities')</a><br/><a href=&quot;#TODO_page=ingame&amp;component=shipyard&amp;cp=33624092&quot;>@lang('Shipyard')</a><br/><a href=&quot;#TODO_component=defenses&amp;cp=33624092&quot;>@lang('Defense')</a><br/><a href=&quot;#TODO_page=ingame&amp;component=fleetdispatch&amp;cp=33624092&quot;>@lang('Fleet')</a><br/><a href=&quot;#TODO_component=galaxy&amp;cp=33624092&amp;galaxy=2&amp;system=3&amp;position=6&quot;>@lang('Galaxy')</a>" class="planetlink {{ $planet->getPlanetId() == $currentPlanet->getPlanetId() ? 'active' : '' }} tooltipRight tooltipClose js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiPlanetHomeplanet">
                                        <img class="planetPic js_replace2x" alt="{{ $planet->getPlanetName() }}" src="/img/icons/a8821a3ef84e0acd053aef2e98972a.png" width="48" height="48">
                                        <span class="planet-name ">{!! $planet->getPlanetName() !!}</span>
                                        <span class="planet-koords ">[{!! $planet->getPlanetCoordinates()->asString() !!}]</span>
                                    </a>
                                    <!--
                                    <a class="moonlink  tooltipLeft tooltipClose js_hideTipOnMobile" title="<b>Moon [2:3:6]</b><br>8,888km (0/1)<br/><a href=&quot;#TODO_ingame&amp;component=overview&amp;cp=33644212&quot;>Overview</a><br/><a href=&quot;#TODO_page=ingame&amp;component=supplies&amp;cp=33644212&quot;>Resources</a><br/><a href=&quot;#TODO=ingame&amp;component=facilities&amp;cp=33644212&quot;>Facilities</a><br/><a href=&quot;#TODO=ingame&amp;component=defenses&amp;cp=33644212&quot;>Defense</a><br/><a href=&quot;#TODO=ingame&amp;component=fleetdispatch&amp;cp=33644212&quot;>Fleet</a><br/><a href=&quot;#TODO=ingame&amp;component=galaxy&amp;cp=33644212&amp;galaxy=2&amp;system=3&amp;position=6&quot;>Galaxy</a>" href="#TODO=ingame&amp;component=shipyard&amp;cp=33644212" data-link="#TODO=ingame&amp;component=shipyard&amp;cp=33644212" data-jumpgatelevel="0">
                                        <img src="/img/icons/9c9f0a78e85bcf40c2ccfc08db5cb4.gif" width="16" height="16" alt="Moon" class="icon-moon">
                                    </a>
                                    -->
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>    </div>
        <div id="bannerSkyscrapercomponent" class="">
            <div id="banner_skyscraper" class="desktop" name="banner_skyscraper">
                <div style="position: relative;">
                    <a class="tooltipLeft " title="" href="#TODO=shop">
                        <img src="/img/banners/de0dadddb0285ba78b026ce18fc898.jpg" alt="">
                    </a>
                </div>
            </div>    </div>
    </div>
</div>

<!-- Chat Bar -->
<div id="chatBar">
    <ul class="chat_bar_list">
        <li id="chatBarPlayerList" class="chat_bar_pl_list_item">
            <div class="cb_playerlist_box"
                 style="display:none;">
            </div>
            <span class="onlineCount">@lang(':count Contact(s) online', ['count' => 0])</span>
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
                {{ \OGame\Utils\GitInfoUtil::getAppVersion() }}</a>
            <a class="homeLink" href="https://github.com/lanedirt/ogamex" target="_blank">© OGameX. @lang('All rights reserved.')</a>
        </div>
        <div class="fright textRight">
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
<noscript>
    <style type="text/css">

        body {
            margin: 0;
            padding: 0;
        }

        #mmonetbar {
            background: transparent url('/img/bg/ab65c4951f415dff50d74738c953b5.jpg') repeat-x;
            font: normal 11px Tahoma, Arial, Helvetica, sans-serif;
            height: 32px;
            left: 0;
            padding: 0;
            position: absolute;
            text-align: center;
            top: 0;
            width: 100%;
            z-index: 3000;
        }

        #mmonetbar #mmoContent {
            height: 32px;
            margin: 0 auto;
            width: 1024px;
            position: relative;
        }

        #mmonetbar #mmoLogo {
            float: left;
            display: block;
            height: 32px;
            width: 108px;
            text-indent: -9999px;
        }

        #mmonetbar #mmoNews, #mmonetbar #mmoGame, #mmonetbar #mmoFocus, #pagefoldtarget {
            display: none !important;
        }
    </style>
</noscript>

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
        "backgroundImagePicker": {"ok": "Ok", "repeatX": "@lang('Repeat horizontally')", "repeatY": "@lang('Repeat vertically')"}
    };</script>
</body>
</html>