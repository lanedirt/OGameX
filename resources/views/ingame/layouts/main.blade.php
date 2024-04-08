<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <meta name="ogame-planet-coordinates" content="{{ $currentPlanet->getPlanetCoordinatesAsString() }}"/>
    <meta name="ogame-planet-type" content="planet"/>

    <!--[if (gt IE 9)|!(IE)]><!-->
    <link rel="stylesheet" type="text/css" href="/css/43b7408a18e671621274f53b5cee56.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/fleets.css" media="screen" />
    <!--<![endif]-->
    <!--[if lt IE 10]>
    <link rel="stylesheet" type="text/css" href="/css/ltie10/a38a903f82bb4d2e7021f22bb14496.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/79452e4c157dbce5929452f810159e.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/9a111b0209f44edde46ca7dd9d303c.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/e81fd1c9005e83f852dd9a0560e3f7.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/da5d31cd0a99fc43fbbd9a06b52899.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/c5f033061127f4dbd170574705c787.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/a4ab94ec319cd1ed340abd608174e7.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/8d0216e7f6955a8cffbcc3184cd64d.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/c4669ba58ee728ac1bc7610f1f6a30.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/f468a2aecceb0bb7086230beedea62.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/ba2216eb24ccc0e410cfbe4fb135f1.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/2d769ad9c91469fa1fea767618fa1b.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/4633a3895446133a62a64ef54bc986.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/42f730c1520474b22c5dabbb840b43.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/4b46399f46a1b56773041f1f1abfdb.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/32b054a20d0f9aa3578a28af00096d.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/0b04dfcfe3acd740d8df12a3cc7758.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/ltie10/e7bb4d0526a879ed6cad775bfda300.css" media="screen" />
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/css/22b955f43c237ad23d644e8e52272a.css" media="screen" />
    <!--[if IE 8]>
    <link rel="stylesheet" type="text/css" href="/css/ie8/0db2335aa04275bf3d33e76a35aafd.css" media="screen" />
    <![endif]-->
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script type='text/javascript' src='/js/095a3a537441223e34647ad44e30ec.js'></script>
    <script type="text/javascript">
        var inventoryObj;
    </script>
    <script type="text/javascript">
        $.holdReady(true);

        var s = setInterval(function() {
            if (typeof initEmpireEquipment === "function") {
                $.holdReady(false);
                clearInterval(s);
            }
        }, 1);
    </script>
    <script src="{{ asset('/js/jquery.js') }}"></script>
    <script src="{{ asset('/js/chat.js') }}"></script>
    <script src="{{ asset('/js/inventory.js') }}"></script>
    <script src="{{ asset('/js/jquery-spinners.js') }}"></script>
    <script src="{{ asset('/js/jquery-tipped.js') }}"></script>
    <script src="{{ asset('/js/messages.js') }}"></script>
    <script src="{{ asset('/js/tooltips.js') }}"></script>
    <script src="{{ asset('/js/trader.js') }}"></script>
    <script src="{{ asset('/js/percentagebar.js') }}"></script>
    <script src="{{ asset('/js/fleetdispatcher.js') }}"></script>
    <script src="{{ asset('/js/logic571d.js') }}"></script>
</head>
<body @isset($body_id)
      id="{!! $body_id !!}"
    @endisset
      class="ogame lang-en default no-touch"
>
<div id="initial_welcome_dialog" title="Welcome to OGame!" style="display: none;">
    To help your game start get moving quickly, we’ve assigned you the name Commodore Nebula. You can change this at any time by clicking on the username.<br />
    Fleet Command has left you information on your first steps in your inbox, to help you be well-equipped for your start.<br />
    <br />
    Have fun playing!        </div>
<div class="contentBoxBody">
    <noscript>
        <div id="messagecenter">
            <div id="javamessagebox">
                        <span class="overmark">
                            <strong>Please activate JavaScript to continue with the game.</strong>
                        </span>
            </div>
        </div>
    </noscript>
    <div id="ie_message">
        <p><img src="/img/icons/e621aa80dbd4746a9f4f114c8d3853.gif" height="16" width="16" />Your currently used browser is outdated and may cause display errors on this website. Please update your browser to a newer version: <a href="http://www.microsoft.com/upgrade/">Internet Explorer</a> or <a href="http://www.mozilla-europe.org/de/firefox/">Mozilla Firefox</a></p>
    </div>
    <script type="text/javascript">isIE = false;</script>
    <!--[IF IE]>
    <script type="text/javascript">
        isIE = true;
    </script>
    <![endif]-->

    <!-- HEADER -->
    <!-- ONET 4 POLAND -->

    <div id="boxBG">
        <div id="box">
            <a name="anchor"></a>
            <div id="info" class="header normal">
                <a href="{{ route('overview.index') }}"><img src="/img/layout/pixel.gif" id="logoLink" /></a>
                <div id="star"></div>
                <div id="star1"></div>
                <div id="star2"></div>
                <div id="clearAdvice"></div>
                <div id="bar">
                    <ul>
                        <li id="playerName">
                            Player:
                                                                <span class="textBeefy">
                            <a href="{{ route('changenick.overlay') }}"
                               class="overlay textBeefy"
                               data-overlay-title="Change player name"
                               data-overlay-popup-width="400"
                               data-overlay-popup-height="200"
                            >{!! $currentPlayer->getUsername() !!}</a>
                        </span>
                        </li>
                        <li>
                            <a href="{{ route('highscore.index') }}" accesskey="">Highscore</a>
                            (0)                </li>
                        <li>
                            <a href="{{ route('notes.overlay') }}"
                               class="overlay" data-overlay-title="My notes"
                               data-overlay-class="notices"
                               data-overlay-popup-width="750"
                               data-overlay-popup-height="480"
                               accesskey="">
                                Notes</a>
                        </li>
                        <li>
                            <a class=""
                               accesskey=""
                               href="{{ route('buddies.index') }}"
                            >
                                Buddies</a>
                        </li>
                        <li><a class="overlay"
                               href="{{ route('search.overlay') }}"
                               data-overlay-title="Search Universe"
                               data-overlay-close="__default closeSearch"
                               data-overlay-class="search"
                               accesskey="">Search</a>
                        </li>
                        <li><a href="{{ route('options.index') }}" accesskey="">Options</a></li>
                        <li><a href="#">Support</a></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Log out</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                        <li class="OGameClock">16.12.2017 <span>12:18:12</span></li>
                    </ul>

                </div>    	<ul id="resources">
                    <li id="metal_box" class="metal tooltipHTML"
                        title="Metal:| &lt;table class=&quot;resourceTooltip&quot;&gt;
            &lt;tr&gt;
                &lt;th&gt;Available:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;{!! $resources['metal']['amount_formatted'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Storage capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;10.000&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Current production:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;undermark&quot;&gt;+{!! $resources['metal']['production_hour'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Den Capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overermark&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;">
                        <div class="resourceIcon metal"></div>
                <span class="value">
                    <span id="resources_metal" class="{{ $resources['metal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['metal']['amount'] >= $resources['metal']['storage'] ? 'overmark' : '' }}">
                        {!! $resources['metal']['amount_formatted'] !!}                    </span>
                </span>
                    </li>
                    <li id="crystal_box" class="crystal tooltipHTML"
                        title="Crystal:| &lt;table class=&quot;resourceTooltip&quot;&gt;
            &lt;tr&gt;
                &lt;th&gt;Available:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;{!! $resources['crystal']['amount_formatted'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Storage capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;10.000&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Current production:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;undermark&quot;&gt;+{!! $resources['crystal']['production_hour'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Den Capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overermark&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;">
                        <div class="resourceIcon crystal"></div>
                <span class="value">
                    <span id="resources_crystal" class="{{ $resources['crystal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['crystal']['amount'] >= $resources['crystal']['storage'] ? 'overmark' : '' }}">
                        {!! $resources['crystal']['amount_formatted'] !!}                    </span>
                </span>
                    </li>
                    <li id="deuterium_box" class="deuterium tooltipHTML"
                        title="Deuterium:| &lt;table class=&quot;resourceTooltip&quot;&gt;
            &lt;tr&gt;
                &lt;th&gt;Available:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;{!! $resources['deuterium']['amount_formatted'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Storage capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;10.000&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Current production:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overmark&quot;&gt;{!! $resources['deuterium']['production_hour'] !!}&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Den Capacity:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overermark&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;">
                        <div class="resourceIcon deuterium"></div>
                <span class="value">
                    <span id="resources_deuterium" class="{{ $resources['deuterium']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['deuterium']['amount'] >= $resources['deuterium']['storage'] ? 'overmark' : '' }}">
                        {!! $resources['deuterium']['amount_formatted'] !!}                    </span>
               	</span>
                    </li>
                    <li id="energy_box" class="energy tooltipHTML"
                        title="Energy:| &lt;table class=&quot;resourceTooltip&quot;&gt;
            &lt;tr&gt;
                &lt;th&gt;Available:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;&quot;&gt;{!! $resources['energy']['amount_formatted'] !!} &lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Current production:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overmark&quot;&gt;{!! $resources['energy']['production'] !!} &lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;th&gt;Consumption:&lt;/th&gt;
                &lt;td&gt;&lt;span class=&quot;overmark&quot;&gt;{!! $resources['energy']['consumption'] !!} &lt;/span&gt;&lt;/td&gt;
            &lt;/tr&gt;
        &lt;/table&gt;">
                        <div class="resourceIcon energy"></div>
                <span class="value">
                    <span id="resources_energy" class="{{ $resources['energy']['amount'] < 0 ? 'overmark' : '' }}">
                            {!! $resources['energy']['amount_formatted'] !!}			            </span>
                    </span>
                    </li>
                    <li id="darkmatter_box" class="darkmatter dark_highlight_tablet tooltipHTML"
                        title="Dark Matter:| &lt;table class=&quot;resourceTooltip&quot;&gt;
                &lt;tr&gt;
                    &lt;th&gt;Available:&lt;/th&gt;
                    &lt;td&gt;&lt;span class=&quot;&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;th&gt;Purchased:&lt;/th&gt;
                    &lt;td&gt;&lt;span class=&quot;&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;th&gt;Found:&lt;/th&gt;
                    &lt;td&gt;&lt;span class=&quot;&quot;&gt;0&lt;/span&gt;&lt;/td&gt;
                &lt;/tr&gt;
            &lt;/table&gt;"
                        data-tooltip-button="Purchase Dark Matter">
                        <a class="overlay" href="{{ route('payment.overlay') }}">
                            <img src="/img/icons/401d1a91ff40dc7c8acfa4377d3d65.gif" />
                    <span class="value">
                        <span id="resources_darkmatter">
                            0                        </span>
                    </span>
                        </a>
                    </li>
                </ul>



                <div id="officers" class="">

                    <a href="{{ route('premium.index', ['openDetail' => 2]) }}"
                       class="tooltipHTML   commander js_hideTipOnMobile" title="Hire Commander|+40 favourites, building queue, empire view, shortcuts, transport scanner, advertisement free* &lt;span style=&quot;font-size:10px;line-height:10px;&quot;&gt;(*excludes: game related references)&lt;/span&gt;">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="30" height="30"/>
                    </a>

                    <a href="{{ route('premium.index', ['openDetail' => 3]) }}"
                       class="tooltipHTML   admiral js_hideTipOnMobile" title="Hire Admiral|Max. fleet slots +2,
Max. expeditions +1,
Improved fleet escape rate">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="30" height="30"/>
                    </a>
                    <a href="{{ route('premium.index', ['openDetail' => 4]) }}"
                       class="tooltipHTML   engineer js_hideTipOnMobile" title="Hire Engineer|Halves losses to defenses, +10% energy production">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="30" height="30"/>
                    </a>
                    <a href="{{ route('premium.index', ['openDetail' => 5]) }}"
                       class="tooltipHTML   geologist js_hideTipOnMobile" title="Hire Geologist|+10% mine production">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="30" height="30"/>
                    </a>
                    <a href="{{ route('premium.index', ['openDetail' => 6]) }}"
                       class="tooltipHTML   technocrat js_hideTipOnMobile" title="Hire Technocrat|+2 espionage levels, 25% less research time">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="30" height="30"/>
                    </a>
                </div>
                <div id="message-wrapper">
                    <!-- Neue Nachrichten-Zähler -->
                    <a class=" comm_menu messages tooltip js_hideTipOnMobile"
                       href="{{ route('messages.index') }}"
                       title="{{ $unreadMessagesCount }} unread message(s)">
                        @if ($unreadMessagesCount > 0)
                            <span class="new_msg_count totalMessages  news" data-new-messages="{{ $unreadMessagesCount }}">
                                {{ $unreadMessagesCount }}
                            </span>
                        @endif
                    </a>
                    <!-- Neue Chatnachrichten-Zähler -->
                    <a class=" comm_menu chat tooltip js_hideTipOnMobile"
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
                            <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif" />load...</div>
                        <div id="eventboxBlank" class="textCenter" style="display: none;">
                            No fleet movement</div>            </div>
                    <div id="attack_alert" class="tooltip eventToggle noAttack" title="">
                        <a href="{{ route('overview.index') }}#TODO_page=eventList" ></a>
                    </div>
                    <br class="clearfloat" />
                </div><!-- #message-wrapper -->

                <div id="helper">
                    <a class="tooltip tooltipClose"
                       href="#"
                       title="Tutorial overview<br/><a href='#'>Rewards</a>"
                    >?</a>

                </div>

                <div id="selectedPlanetName" class="textCenter">{!! $planets->first()->getPlanetName() !!}</div>
            </div><!-- Info -->
            <!-- END HEADER -->

            <!-- LEFTMENU -->
            <link rel="stylesheet" type="text/css" href="/css/22b955f43c237ad23d644e8e52272a.css" media="screen" />
            <div id='toolbarcomponent'><div id="links">
                    <ul id="menuTable" class="leftmenu">

                        <li>
                <span class="menu_icon">
                                            <a href="{{ route('rewards.index') }}"
                                               class="tooltipRight js_hideTipOnMobile "
                                               target="_self"
                                               title="Rewards">
                            <div class="menuImage overview {{(Request::is('rewards') || Request::is('overview') ? 'highlighted' : '') }}
                                ">
                            </div>
                        </a>
                                    </span>
                            <a class="menubutton {{(Request::is('overview') ? 'selected' : '') }}"
                               href="{{ route('overview.index') }}"
                               accesskey=""
                               target="_self"
                            >
                                <span class="textlabel">Overview</span>
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
                                <span class="textlabel">Resources</span>
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
                                <span class="textlabel">Facilities</span>
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
                                <span class="textlabel">Merchant</span>
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
                                <span class="textlabel">Research</span>
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
                                <span class="textlabel">Shipyard</span>
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
                                <span class="textlabel">Defense</span>
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
                                <span class="textlabel">Fleet</span>
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
                                <span class="textlabel">Galaxy</span>
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
                                <span class="textlabel">Alliance</span>
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
                                <span class="textlabel">Recruit Officers</span>
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
                                <span class="textlabel">Shop</span>
                            </a>
                        </li>
                    </ul>

                    <div class="adviceWrapper">
                        <div id='advicebarcomponent' class='advicebar injectedComponent parent toolbar'><div class="adviceWrapper">

                                <div id="advice-bar">


                                    <!--
                                    class="overlay"
                                       data-overlay-modal="true"
                                       data-overlay-title="Confirm Email Address"
                                    -->
                                    <a href="#"
                                       class="tooltipHTML tooltipRight advice"
                                       title="Not validated|Your account has not been validated yet. Click here to receive a new validation link."
                                    >
                                        <img src="/img/icons/09980161fadf11b18189770e1d78d2.gif" height="32" width="32" />
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div id="toolLinksWrapper">
                        <ul id="menuTableTools" class="leftmenu"></ul>
                    </div>
                    <br class="clearfloat"/>
                </div>
            </div>            <!-- END LEFTMENU -->

            <!-- CONTENT AREA -->
            <div id="contentWrapper"
                 class="with_chat_bar">

                @yield('content')

            </div>
            <!-- END CONTENT AREA -->

            <!-- RIGHTMENU -->
            <div id="rechts">
                <div id="norm">
                    <div id="myWorlds">
                        <div id="countColonies">
                            <p class="textCenter">
                                <span>{{ $planets->count() }}/{{ $planets->count() }}</span> Planets
                            </p>
                        </div>
                        <div id="planetList"
                        >
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

                                <div class="smallplanet {{ $planet->getPlanetId() == $currentPlanet->getPlanetId() ? 'hightlightPlanet' : '' }} " id="planet-{{ $planet->getPlanetId() }}">
                                    <a href="{{ $urlToCurrentWithUpdatedParam }}"
                                       title="&lt;b&gt;{{ $planet->getPlanetName() }} [{{ $planet->getPlanetCoordinatesAsString() }}]&lt;/b&gt;&lt;br/&gt;12.800km (0/188)&lt;br&gt;47°C to 87°C&lt;br/&gt;&lt;a href=&quot;#TODO_overview&amp;cp=33734581&quot;&gt;Overview&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_resources&amp;cp=33734581&quot;&gt;Resources&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=research&amp;cp=33734581&quot;&gt;Research&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=station&amp;cp=33734581&quot;&gt;Facilities&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=shipyard&amp;cp=33734581&quot;&gt;Shipyard&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=defense&amp;cp=33734581&quot;&gt;Defense&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=fleet1&amp;cp=33734581&quot;&gt;Fleet&lt;/a&gt;&lt;br/&gt;&lt;a href=&quot;#TODO_page=galaxy&amp;cp=33734581&amp;galaxy=4&amp;system=358&amp;position=4&quot;&gt;Galaxy&lt;/a&gt;"
                                       class="planetlink {{ $planet->getPlanetId() == $currentPlanet->getPlanetId() ? 'active' : '' }}  tooltipRight tooltipClose js_hideTipOnMobile"
                                    >
                                        <img class="planetPic js_replace2x"
                                             alt="{{ $planet->getPlanetName() }}"
                                             src="/img/icons/a8821a3ef84e0acd053aef2e98972a.png"
                                             width="48"
                                             height ="48"
                                        />
                                        <span class="planet-name ">{!! $planet->getPlanetName() !!}</span>
                                        <span class="planet-koords ">[{!! $planet->getPlanetCoordinatesAsString() !!}]</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div id="banner_skyscraper" name="banner_skyscraper">

            </div>

            <!-- END RIGHTMENU -->

            <!-- JAVASCRIPT -->
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
                    "thousandSeperator": ".",
                    "unitMega": "Mn",
                    "unitKilo": "K",
                    "unitMilliard": "Bn",
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
                            "tooltip": "Metal|<table class=\"resourceTooltip\">\n            <tr>\n                <th>Available:<\/th>\n                <td><span class=\"\">{!! $resources['metal']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['metal']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['metal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['metal']['production_hour'] > 0)+@endif{!! $resources['metal']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['metal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['metal']['amount'] >= $resources['metal']['storage'] ? 'overmark' : '' }}"
                        },
                        "crystal": {
                            "resources": {
                                "actualFormat": "{!! $resources['crystal']['amount_formatted'] !!}",
                                "actual": {!! $resources['crystal']['amount'] !!},
                                "max": {!! $resources['crystal']['storage'] !!},
                                "production": {!! $resources['crystal']['production_second'] !!}
                            },
                            "tooltip": "Crystal|<table class=\"resourceTooltip\">\n            <tr>\n                <th>Available:<\/th>\n                <td><span class=\"\">{!! $resources['crystal']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['crystal']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['crystal']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['crystal']['production_hour'] > 0)+@endif{!! $resources['crystal']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['crystal']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['crystal']['amount'] >= $resources['crystal']['storage'] ? 'overmark' : '' }}"
                        },
                        "deuterium": {
                            "resources": {
                                "actualFormat": "{!! $resources['deuterium']['amount_formatted'] !!}",
                                "actual": {!! $resources['deuterium']['amount'] !!},
                                "max": {!! $resources['deuterium']['storage'] !!},
                                "production": {!! $resources['deuterium']['production_second'] !!}
                            },
                            "tooltip": "Deuterium|<table class=\"resourceTooltip\">\n            <tr>\n                <th>Available:<\/th>\n                <td><span class=\"\">{!! $resources['deuterium']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Storage capacity:<\/th>\n                <td><span class=\"\">{!! $resources['deuterium']['storage_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"@if ($resources['deuterium']['production_hour'] <= 0) overmark @else undermark @endif\">@if ($resources['deuterium']['production_hour'] > 0)+@endif{!! $resources['deuterium']['production_hour_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Den Capacity:<\/th>\n                <td><span class=\"overermark\">0<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['deuterium']['storage_almost_full'] ? 'middlemark' : '' }}{{ $resources['deuterium']['amount'] >= $resources['deuterium']['storage'] ? 'overmark' : '' }}"
                        },
                        "energy": {
                            "resources": {
                                "actual": {!! $resources['energy']['amount'] !!},
                                "actualFormat": "{!! $resources['energy']['amount_formatted'] !!}"
                            },
                            "tooltip": "Energy|<table class=\"resourceTooltip\">\n            <tr>\n                <th>Available:<\/th>\n                <td><span class=\"\">{!! $resources['energy']['amount_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Current production:<\/th>\n                <td><span class=\"{{ $resources['energy']['production'] > 0 ? 'undermark' : 'overmark' }}\">{{ $resources['energy']['production'] > 0 ? '+' : '' }}{!! $resources['energy']['production_formatted'] !!}<\/span><\/td>\n            <\/tr>\n            <tr>\n                <th>Consumption:<\/th>\n                <td><span class=\"{{ $resources['energy']['consumption'] > 0 ? 'overmark' : '' }}\">{{ $resources['energy']['consumption'] > 0 ? '-' : '' }}{!! $resources['energy']['consumption_formatted'] !!}<\/span><\/td>\n            <\/tr>\n        <\/table>",
                            "class": "{{ $resources['energy']['amount'] < 0 ? 'overmark' : '' }}"
                        },
                        "darkmatter": {
                            "resources": {
                                "actual": 0,
                                "actualFormat": "0"
                            },
                            "string": "0 Dark Matter",
                            "tooltip": "Dark Matter|<table class=\"resourceTooltip\">\n                <tr>\n                    <th>Available:<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n                <tr>\n                    <th>Purchased:<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n                <tr>\n                    <th>Found:<\/th>\n                    <td><span class=\"\">0<\/span><\/td>\n                <\/tr>\n            <\/table>",
                            "class": ""
                        },
                        "honorScore": 0
                    });
                }
                function getAjaxEventbox() {
                    $.get("{{ route('overview.index') }}#TODO_page=fetchEventbox&ajax=1", reloadEventbox, "text");
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
                function planetRenamed(data)
                {var data=$.parseJSON(data);if(data["status"]){$("#planetNameHeader").html(data["newName"]);reloadRightmenu("{{ route('overview.index') }}#TODO_page=rightmenu&renamed=1&pageToLink=overview");$(".overlayDiv.planetRenameOverlay").dialog('close');}
                    errorBoxAsArray(data["errorbox"]);}
                function reloadPage() {
                    location.href = "{{ route('overview.index') }}";
                }
                var demolish_id;
                var buildUrl;
                function loadDetails(type) {
                    url = "{{ route('overview.index', ['ajax' => 1]) }}";
                    if (typeof(detailUrl) != 'undefined') {
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

        </div><!-- box -->

    </div><!-- boxBG -->

</div><!-- contentBoxBody -->

<!-- Chat Bar -->
<div id="chatBar">
    <ul class="chat_bar_list">
        <li id="chatBarPlayerList" class="chat_bar_pl_list_item">
            <div class="cb_playerlist_box"
                 style="display:none;"            >
            </div>
            <span class="onlineCount">0 Contact(s) online</span>
        </li>
    </ul><!-- END Chat Bar List -->
</div>
<!-- END Chat Bar -->

<button class="scroll_to_top">
    <span class="arrow"></span>Back to top        </button>

<div id="siteFooter">
    <div class="content" style="font-size:10px">
        <div class="fleft textLeft">
            <a href="#TODO_changelog&ajax=1" class="tooltip js_hideTipOnMobile overlay" data-class="noXScrollbar" data-overlay-iframe="true" data-iframe-width="680" data-overlay-title="Patch notes">
                {{ \OGame\Utils\GitInfoUtil::getAppVersion() }}</a>
            <a class="homeLink" href="https://github.com/lanedirt/ogamex" target="_blank">© OGameX. All rights reserved.</a>
        </div>
        <div class="fright textRight">
            <a href="http://wiki.ogame.org/" target="_blank">Help</a>|
            <a href="#">Board</a>|
            <a class="overlay-temp" href="#" data-overlay-iframe="true" data-iframe-width="450" data-overlay-title="Rules">Rules</a>|
            <a href="#">Legal</a>
        </div>
    </div><!-- -->
</div>

<!-- #MMO:NETBAR# -->
<div id="pagefoldtarget"></div>
<noscript>
    <style type="text/css">

        body {margin:0; padding:0;} #mmonetbar { background:transparent url('/img/bg/ab65c4951f415dff50d74738c953b5.jpg') repeat-x; font:normal 11px Tahoma, Arial, Helvetica, sans-serif; height:32px; left:0; padding:0; position:absolute; text-align:center; top:0; width:100%; z-index:3000; } #mmonetbar #mmoContent { height:32px; margin:0 auto; width:1024px; position: relative; } #mmonetbar #mmoLogo { float:left; display:block; height:32px; width:108px; text-indent: -9999px; } #mmonetbar #mmoNews, #mmonetbar #mmoGame, #mmonetbar #mmoFocus, #pagefoldtarget { display:none !important; }
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
                <br class="clearfloat" />
            </div>
        </div>
        <div class="foot"></div>
    </div>
</div>

<div id="fadeBox" class="fadeBox fixedPostion" style="display:none;">
    <div>
        <span id="fadeBoxStyle" class="success"></span>
        <p id="fadeBoxContent"></p>
    </div>
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
                <br class="clearfloat" />
            </div>
        </div>
        <div class="foot"></div>
    </div>
</div>
<script type="text/javascript">var visibleChats = {"players":[],"associations":[]};var bigChatLink = "{{ route('overview.index') }}#TODO_page=chat";var locaKeys = {"bold":"Bold","italic":"Italic","underline":"Underline","stroke":"Strikethrough","sub":"Subscript","sup":"Superscript","fontColor":"Font colour","fontSize":"Font size","backgroundColor":"Background colour","backgroundImage":"Background image","tooltip":"Tool-tip","alignLeft":"Left align","alignCenter":"Centre align","alignRight":"Right align","alignJustify":"Justify","block":"Break","code":"Code","spoiler":"Spoiler","moreopts":"More Options","list":"List","hr":"Horizontal line","picture":"Image","link":"Link","email":"Email","player":"Player","item":"Item","coordinates":"Coordinates","preview":"Preview","textPlaceHolder":"Text...","playerPlaceHolder":"Player ID or name","itemPlaceHolder":"Item ID","coordinatePlaceHolder":"Galaxy:system:position","charsLeft":"Characters remaining","colorPicker":{"ok":"Ok","cancel":"Cancel","rgbR":"R","rgbG":"G","rgbB":"B"},"backgroundImagePicker":{"ok":"Ok","repeatX":"Repeat horizontally","repeatY":"Repeat vertically"}};</script>    </body>
</html>