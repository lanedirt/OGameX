<div class="galaxyTable">
    <div class="galaxyRow ctGalaxyHead">
        <div class="galaxyCell span11">
            <div id="probes">
                Esp.Probe:<span id="probeValue">0</span>
            </div>
            <div id="recycler">
                Recy.:<span id="recyclerValue">0</span>
            </div>
            <div id="rockets">
                IPM.:<span id="missileValue">0</span>
            </div>
            <div id="slots">
                Used slots:<span id="slotUsed">0</span>/<span id="slotValue">11</span>
            </div>
            <div id="galaxyHeaderDiscoveryCount">
                Discoveries: 0/1800
            </div>
        </div>

        <div id="filterCell" class="filterCell">
            <div id="filter_empty" class="filter" onclick="filterToggle(event);">E</div>
            <div id="filter_inactive" class="filter" onclick="filterToggle(event);">I</div>
            <div id="filter_newbie" class="filter" onclick="filterToggle(event);">N</div>
            <div id="filter_strong" class="filter" onclick="filterToggle(event);">S</div>
            <div id="filter_vacation" class="filter" onclick="filterToggle(event);">V</div>
        </div>
    </div>
    <div class="galaxyRow ctGalaxyHead headBold">
        <div class="galaxyCell span1-2">Planet</div>
        <div class="galaxyCell cellPlanetName">Name</div>
        <div class="galaxyCell cellMoon">Moon</div>
        <div class="galaxyCell cellDebris">DF</div>
        <div class="galaxyCell cellPlayerName">Player (Status)</div>
        <div class="galaxyCell cellAlliance">Alliance</div>
        <div class="galaxyCell cellAction">Action</div>
    </div>

    @php /** @var OGame\ViewModels\GalaxyRowViewModel $row */ @endphp
    @foreach ($galaxy_rows as $number => $row)
        <div class="galaxyRow ctContentRow {{ !$row->planet ? 'empty_filter' : '' }}" id="galaxyRow{{ $row->position }}">
            <!-- Todo: extra filters: newbie_filter, inactive_filter, strong_filter, vacation_filter -->
            <div class="galaxyCell cellPosition">{{ $row->position }}</div>
            <div class="galaxyCell cellPlanet">
                @if ($row->planet)
                <a href="javascript: void(0);" onclick=""><div class="microplanet jungle_10 planetTooltip tooltipRel tooltipPersistent tooltipClose tooltipRight js_hideTipOnMobile" data-planet-id="33624092" rel="planet6"><div class="activity minute15 tooltip js_hideTipOnMobile hideTooltipOnMouseenter" title="Activity">
                        </div><div id="ownFleetStatus_6_1" class="fleetAction js_hideTipOnMobile hideTooltipOnMouseenter" title="">
                        </div>
                        <div id="planet6" style="display: none;" class="htmlTooltip galaxyTooltip">
                            <h1>Planet: <span class="textNormal">BirbTown</span></h1>
                            <div class="splitLine"></div>
                            <ul class="ListImage">
                                <li><span>[{{ $row->planet->getPlanetCoordinates()->asString() }}]</span></li>
                                <li><div class="planetTooltip microplanet jungle_10"></div></li>
                            </ul>
                            <ul class="ListLinks">
                                <li>Activity:<div class="alert_triangle"><img src="/img/icons/b4c8503dd1f37dc9924909d28f3b26.gif"></div></li>No actions available.
                            </ul>
                        </div>
                    </div></a>
                @endif
            </div>
            <div class="galaxyCell cellPlanetName">
                @if ($row->planet)
                    <span class="">{{ $row->planet->getPlanetName() }}</span>
                @endif
            </div>
            <div class="galaxyCell cellMoon">
                <!-- TODO: add moon support -->
                <!--<a href="javascript: void(0);" onclick="">
                    <div class="micromoon moon_a tooltipRel tooltipClose tooltipRight js_hideTipOnMobile" data-moon-id="33644212" rel="moon6"><div id="ownFleetStatus_6_3" class="fleetAction js_hideTipOnMobile hideTooltipOnMouseenter" title="">
                        </div>
                        <div id="moon6" style="display: none;" class="htmlTooltip galaxyTooltip">
                            <h1><span class="textNormal">Moon</span></h1>
                            <div class="splitLine"></div>
                            <ul class="ListImage">
                                <li><span id="pos-moon">[2:3:6]</span></li>
                                <li><div class="moonTooltip micromoon moon_a"></div></li>
                                <li><span id="moonsize" title="Diameter of moon in km">8888 km</span></li>
                            </ul>
                            <ul class="ListLinks">
                                <li><a href="#page=ingame&amp;component=fleetdispatch&amp;galaxy=2&amp;system=3&amp;position=6&amp;type=3&amp;mission=3">Transport</a></li><li><a href="#page=ingame&amp;component=fleetdispatch&amp;galaxy=2&amp;system=3&amp;position=6&amp;type=3&amp;mission=4">Deployment</a></li><li><a href="#page=messages&amp;messageId=2776115&amp;tabid=20&amp;ajax=1" class="overlay">Espionage report</a></li>
                            </ul>
                        </div>
                    </div></a>-->
            </div>
            <div class="galaxyCell cellDebris"></div>
            <div class="galaxyCell cellPlayerName">
                @if ($row->planet)
                    <span class="status_abbr_active ownPlayerRow">{{ $row->planet->getPlayer()->getUsername() }}</span>
                @endif
            </div>
            <div class="galaxyCell cellAlliance">
                <!-- TODO: add alliance support -->
                <!--
                <span class="status_abbr_ally_own tooltipRel tooltipClose tooltipRight js_hideTipOnMobile" rel="alliance500635">
                    VOC
                    <div id="alliance500635" style="display: none;" class="htmlTooltip galaxyTooltip">
                        <h1>
                             <selected-language-icon style="background-image: url('/img/icons/fa7e0cc8f939afa3eb116f75a077dd.png');"></selected-language-icon>
                            VOC
                        </h1>
                        <div class="splitLine"></div>
                        <ul class="ListLinks">
                            <li class="rank">Rank: <a href="#page=highscore&amp;site=1&amp;category=2&amp;searchRelId=500635">26</a></li>
                            <li class="members">Member: 11</li>
                            <li>Alliance Class: <span class="alliance_class small none">No alliance class selected</span></li>
                            <li><a href="#page=ingame&amp;component=alliance">Alliance Page</a></li>

                        </ul>
                    </div>
                </span>
                -->
            </div>
            <div class="galaxyCell cellAction">
                @if ($row->planet)
                    @if ($row->planet->getPlayer()->getId() === $player->getId())
                        <div class="emptyAction"></div>
                        <div class="emptyAction"></div>
                        <div class="emptyAction"></div>
                        <div class="emptyAction"></div>
                        <div class="emptyAction"></div>
                    @else
                        <div class="emptyAction"></div>
                        <a class="tooltip js_hideTipOnMobile espionage" title="Espionage not possible" href="javascript: void(0);">
                            <span class="icon icon_eye grayscale"></span>
                        </a>
                        <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114357" title="Write message"><span class="icon icon_chat"></span></a>

                        <a class="tooltip overlay buddyrequest ipiHintable" title="Buddy request to player" href="#page=ingame&amp;component=buddies&amp;action=7&amp;id=114357&amp;ajax=1" data-overlay-title="Buddy request to player" data-ipi-hint="ipiGalaxySendBuddyRequest">
                            <span class="icon icon_user"></span>
                        </a>
                        <a class="tooltip js_hideTipOnMobile missleattack" title="Missile Attack" href="javascript: void(0);">
                            <span class="icon icon_missile grayscale"></span>
                        </a>
                    @endif
                @else
                    <div class="emptyAction"></div>
                    <div class="tooltip planetMoveIcons colonize-inactive icon tpd-hideOnClickOutside" title="Colonization<br>Due to the proximity to sun, collection of solar energy is highly efficient. However, planets in this position tend to be small and provide only small amounts of deuterium.<br><div style='display: flex;align-items: center;'><img src='/cdn/img/galaxy/activity.gif' style=''/>It is not possible to colonize a planet without a colony ship.</div>"></div>
                    <a class="planetMoveIcons planetMoveDefault tooltip icon js_hideTipOnMobile" href="javascript: void(0);" onclick="movePlanet(
                       '#page=ingame&amp;component=overview&amp;action=planetMove&amp;moveAction=prepareMove&amp;ajax=1&amp;asJson=1&amp;token=a678d85b233445174d433cff10087702',
                       {'position':1,
                       'galaxy': 2,
                       'system': 3},
                       '#page=ingame&amp;component=galaxy&amp;galaxy=2&amp;system=3'
                       ); return false;" title="Relocate"><div class="planetMoveIcons planetMoveDefault tooltip icon js_hideTipOnMobile" title="Relocate"></div></a>
                    <div class="emptyAction"></div>
                    <div class="emptyAction"></div>
                @endif
            </div>
        </div>

    @endforeach
    <div class="expeditionDebrisSlotBoxRow">
        <div class="expeditionDebrisSlotBoxCell cellPosition">16</div>
        <div class="expeditionDebrisSlotBox" id="galaxyRow16">
            <div>
                <h3 class="title float_left">Deep space:</h3>
            </div>
            <div id="expeditionDebrisSlotDebrisContainer">

            </div>
            <div id="expeditionDebrisSlotActions">
                <div id="galaxyExpeditionFleetTemplateContainer" class="tooltip hideTooltipOnMouseenter disabled" title="You need an Admiral to use this feature.">
                    <a id="expeditionFleetTemplateBtn" class="dark_highlight_tablet">
                        <span class="icon icon_combatunits"></span>
                        <span class="expedtionFleetTemplateBtnTitle">Expedition Fleet</span>
                    </a>
                    <select class="expeditionFleetTemplateSelect dropdownInitialized" size="1" title="test" id="expeditionFleetTemplateSelect" disabled="" style="display: none;">
                        <option value="0">-</option>
                    </select><span class="dropdown currentlySelected expeditionFleetTemplateSelect disabled" rel="dropdown39" style="width: 79.2969px;"><a class="undefined" data-value="0" rel="dropdown39" href="javascript:void(0);">-</a></span>
                </div>

                <div id="expeditionbutton" class="btn_blue float_right btn_system_action" onclick="doExpedition();">
                    Expedition
                </div>
                <div id="sendExpeditionFleetTemplateFleet" class="btn_blue float_right btn_system_action" style="display: none" onclick="sendExpedtionFleetFromTemplate()" disabled="">
                    send
                </div>
            </div>
        </div>
    </div>
    <div class="galaxyRow ctGalaxyFleetInfo" id="fleetstatusrow"></div>
    <div class="galaxyRow ctGalaxyFooter">
        <div id="colonized"><span id="amountColonized">8</span>&nbsp;Planets colonized</div>
        <div id="legend" class="filterCell">
            <a href="javascript: void(0);" class="tooltipRel tooltipClose" rel="legendTT" data-tippy-placement="top">
                <span class="icon icon_info"></span>
            </a>
        </div>
    </div>
</div>


<!--
<table cellpadding="0" cellspacing="0" id="galaxytable" border="0" data-galaxy="{{ $current_galaxy }}"
                       data-system="{{ $current_system }}">
                    <thead>
                    <tr class="info info_header ct_head_row">
                        <th colspan="11">
                    <span id="probes">
                        Esp.Probe:
                        <span id="probeValue">{{ $espionage_probe_count }}</span>
                    </span>
                            <span id="recycler">
                        Recy.:
                        <span id="recyclerValue">{{ $recycler_count }}</span>
                    </span>
                            <span id="rockets">
                        IPM.:
                        <span id="missileValue">{{ $interplanetary_missiles_count }}</span>
                    </span>
                            <span id="slots">
                        Used slots:
                        <span id="slotValue">
                            <span id="slotUsed">{{ $used_slots }}</span>/{{ $max_slots }}
                        </span>
                    </span>

                            <span class="fright">
                        <span id="filter_empty" class="filter " onclick="filterToggle(event);">E</span>
                        <span id="filter_inactive" class="filter " onclick="filterToggle(event);">I</span>
                        <span id="filter_newbie" class="filter " onclick="filterToggle(event);">N</span>
                        <span id="filter_strong" class="filter " onclick="filterToggle(event);">A</span>
                        <span id="filter_vacation" class="filter " onclick="filterToggle(event);">V</span>
                    </span>
                        </th>
                    </tr>
                    <tr id="galaxyheadbg2" class="ct_head_row">
                        <th class="first" style="width: 70px; overflow: hidden;">Planet</th>
                        <th style="width: 129px; padding-right: 5px;">Name</th>
                        <th class="text_moon" style="width: 38px; padding-right: 5px;">Moon</th>
                        <th style="width: 38px; padding-right: 5px;">DF</th>
                        <th style="width: 130px; padding-right: 5px;">Player (status)</th>
                        <th style="width: 108px; padding-right: 5px;">Alliance</th>
                        <th class="last" style="width: 75px;">Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr class="footer ct_foot_row" id="fleetstatus">
                        <td class="ct_foot_row" colspan="11" id="fleetstatusrow">
                        </td>
                    </tr>
                    <tr class="info ct_foot_row">
                        <td colspan="11">
                    <span id="legend">
                        <a href="javascript: void(0);" class="tooltipRel tooltipClose" rel="legendTT">
                            <span class="icon icon_info"></span>
                        </a>
                    </span>
                            <span id="colonized">3 Planets colonised</span>
                            <br class="clearfloat">
                        </td>
                    </tr>
                    </tfoot>
                    <tbody>
                    @php /** @var OGame\ViewModels\GalaxyRowViewModel $row */ @endphp
                    @foreach ($galaxy_rows as $number => $row)
                        <tr class="row empty_filter"
                            @if ($row->planet) data-planet-id="{{ $row->planet->getPlanetId() }}" @endif>
                            <td class="position js_no_action">{{ $row->position }}</td>
                            <td colspan="1"
                                class="microplanet @if ($row->planet) colonized @else planetEmpty js_planetEmpty1 @endif js_planet1">
                                @if ($row->planet)
                                    <div class="ListImage">
                                        <a href="javascript: void(0);" onclick="sendShips(
                                                6,
                                                {{ $row->planet->getPlanetCoordinates()['galaxy'] }},
                                                {{ $row->planet->getPlanetCoordinates()['system'] }},
                                                {{ $row->planet->getPlanetCoordinates()['planet'] }},
                                                1,
                                                1
                                                        ); return false;">
                                            <img class="planetTooltip {{ $row->planet->getPlanetType() }}_{{ $row->planet->getPlanetImageType() }}"
                                                 src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" alt="" height="33"
                                                 width="38">
                                        </a>

                                    </div>
                                @endif
                                <div id="ownFleetStatus_1_1" class="fleetAction">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="12" height="12"
                                         alt="">
                                </div>
                            </td>
                            <td class="planetname1 planetEmpty js_planetNameEmpty1" align="center">
                                @if ($row->planet)
                                    {{ $row->planet->getPlanetName()  }}
                                @else
                                    <span class="tooltip planetMoveIcons colonize-inactive icon"
                                          title="It is not possible to colonise a planet without a colony ship."></span>
                                    <a class="planetMoveIcons planetMoveDefault tooltip icon js_hideTipOnMobile"
                                       href="javascript: void(0);" onclick="movePlanet(
                                       '{{ route('planetMove.index', ['action' => 'prepareMove', 'galaxy' => $current_galaxy, 'system' => $current_system, 'ajax' => 1, 'position' => 1]) }}',
                                       '{{ route('galaxy.index', ['galaxy' => $current_galaxy, 'system' => $current_system]) }}'
                                   ); return false;" title="Relocate"></a>
                                @endif
                            </td>

                            <td class="moon js_moon1 js_no_action">
                                <div id="ownFleetStatus_1_3" class="fleetAction">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="12" height="12"
                                         alt="">
                                </div>
                            </td>
                            <td class="debris js_debris1 ">
                                <div id="ownFleetStatus_1_2" class="fleetAction">
                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="12" height="12"
                                         alt="">
                                </div>
                            </td>
                            <td class="playername
                               js_playerName1
                               js_no_action                                                               ">
                                @if ($row->planet)
                                    <span class="status_abbr_active">{{ $row->planet->getPlayer()->getUsername() }}</span>
                                @endif
                                <span class="status">
                                                        </span>
                            </td>
                            <td class="allytag
                               js_allyTag1
                               js_no_action                                                               ">
                            </td>
                            <td class="action" colspan="2">
                                @if ($row->planet)
                                    <span>
                                                                                                                                                                                                                                                            <a class="tooltip js_hideTipOnMobile espionage"
                                                                                                                                                                                                                                                               title=""
                                                                                                                                                                                                                                                               href="javascript: void(0);"
                                                                                                                                                                                                                                                               onclick="sendShips(
                                                           6,
                                                           4,
                                                           198,
                                                           4,
                                                           1,
                                                           2
                                                       ); return false;">
                                                        <span class="icon icon_eye"></span>
                                                    </a>
                                                                                                                                                                                                                                                                                                <a href="javascript:void(0)"
                                                                                                                                                                                                                                                                                                   class="sendMail js_openChat tooltip"
                                                                                                                                                                                                                                                                                                   data-playerid="109997"
                                                                                                                                                                                                                                                                                                   title="Write message"><span
                                                                                                                                                                                                                                                                                                            class="icon icon_chat"></span></a>
                                                                                                                                                                                                                                                    <a class="tooltip overlay buddyrequest"
                                                                                                                                                                                                                                                       title="Buddy request"
                                                                                                                                                                                                                                                       href="{{ route('buddies.index', ['action' => 7, 'id' => 109997, 'ajax' => 1]) }}"
                                                                                                                                                                                                                                                       data-overlay-title="Buddy request to player">
                                                    <span class="icon icon_user"></span>
                                                </a>
                                                                                                                                                                                                                                                                            <span class="tooltip js_hideTipOnMobile overlay missleattack"
                                                                                                                                                                                                                                                                                  title="Missile Attack"
                                                                                                                                                                                                                                                                                  data-overlay-modal="true">
                                                        <span class="icon icon_missile grayscale"></span>
                                                    </span>


                                </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>-->