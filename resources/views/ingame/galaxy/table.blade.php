
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
                    @foreach ($galaxy_rows as $number => $row)
                        <tr class="row empty_filter"
                            @if ($row['planet']) data-planet-id="{{ $row['planet']->getPlanetId() }}" @endif>
                            <td class="position js_no_action">{{ $number }}</td>
                            <td colspan="1"
                                class="microplanet @if ($row['planet']) colonized @else planetEmpty js_planetEmpty1 @endif js_planet1">
                                @if ($row['planet'])
                                    <div class="ListImage">
                                        <a href="javascript: void(0);" onclick="sendShips(
                                                6,
                                                {{ $row['planet']->getPlanetCoordinates()['galaxy'] }},
                                                {{ $row['planet']->getPlanetCoordinates()['system'] }},
                                                {{ $row['planet']->getPlanetCoordinates()['planet'] }},
                                                1,
                                                1
                                                        ); return false;">
                                            <img class="planetTooltip {{ $row['planet']->getPlanetType() }}_{{ $row['planet']->getPlanetImageType() }}"
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
                                @if ($row['planet'])
                                    {{ $row['planet']->getPlanetName()  }}
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
                                @if ($row['planet'])
                                    <span class="status_abbr_active">{{ $row['planet']->getPlayer()->getUsername() }}</span>
                                @endif
                                <span class="status">
                                                        </span>
                            </td>
                            <td class="allytag
                               js_allyTag1
                               js_no_action                                                               ">
                            </td>
                            <td class="action" colspan="2">
                                @if ($row['planet'])
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
                </table>