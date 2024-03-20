@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="movementcomponent" class="maincontent">
        <div id="movement">
            <div id="inhalt">
                <header id="planet" class="planet-header ">
                    <h2>Fleet movement - MyBaseYo</h2>
                    <a class="toggleHeader" data-name="movement">
                        <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                    </a>
                </header>
                <div class="c-left"></div>
                <div class="c-right"></div>
                <div class="fleetStatus">
            <span class="reload">
                <a class="dark_highlight_tablet" href="javascript:void(0);" onclick="reloadPage();">
                    <span class="icon icon_reload"></span>
                    <span>Reload</span>
                </a>
            </span>
                    <span class="fleetSlots">
                Fleets: <span class="current">3</span> / <span class="all">4</span>
            </span>
                    <span class="expSlots">
                Expeditions: <span class="current">0</span> / <span class="all">1</span>
            </span>
                    <span class="closeAll">
                <a href="javascript:void(0);" class="all_open">
                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                </a>
            </span>
                </div>
                <div id="fleet1827745" class="fleetDetails detailsOpened" data-mission-type="1" data-return-flight="1" data-arrival-time="1710806988">
                    <span class="timer tooltip tpd-hideOnClickOutside" title="" id="timer_1827745">54m 45s</span>
                    <span class="absTime">00:09:48  Clock</span>
                    <span class="mission hostile textBeefy">Attack (R)</span>
                    <span class="allianceName"></span>
                    <span class="originData">
                    <span class="originCoords tooltip" title="President Hati"><a href="{{ route('galaxy.index', ['galaxy' => 7, 'system' => 158])  }}">[7:158:10]</a></span>
                    <span class="originPlanet">
                                                    <figure class="planetIcon planet"></figure>MyBaseYo
                                            </span>
                </span>
                    <span class="marker01"></span>
                    <span class="marker02"></span>
                    <span class="fleetDetailButton">
                    <a href="#bl1827745" rel="bl1827745" title="Fleet details" class="tooltipRel tooltipClose fleet_icon_reverse">
                    </a>
                </span>
                    <span class="starStreak">
                    <div style="position: relative;">
                        <div class="origin fixed">
                            <!-- TODO: actual (mini) planet icon should be rendered here instead of static one like now. -->
                            <img class="tooltipHTML tpd-hideOnClickOutside" height="30" width="30" src="/img/icons/32926d2ee2884eab5015c14c73afa3.png" title="" alt="">
                        </div>

                        <div class="route fixed">

                            <a href="#bl1827745" rel="bl1827745" title="Fleet details" class="tooltipRel tooltipClose basic2 fleet_icon_reverse" id="route_1827745" style="margin-left: 220px;"></a>

                            <div style="display:none;" id="bl1827745">
                                <div class="htmlTooltip">
    <h1>Fleet details:</h1>
    <div class="splitLine"></div>
    <table cellpadding="0" cellspacing="0" class="fleetinfo">
        <tbody><tr>
            <th colspan="2">Ships:</th>
        </tr>
                <tr>
            <td>Small Cargo:</td>
            <td class="value">
                            4                        </td>
        </tr>
                <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2">Shipment:</th>
        </tr>
        <tr>
            <td>Metal:</td>
            <td class="value">
                5,000            </td>
        </tr>
        <tr>
            <td>Crystal:</td>
            <td class="value">
                5,000            </td>
        </tr>
        <tr>
            <td>Deuterium:</td>
            <td class="value">
                0            </td>
        </tr>
                    <tr>
                <td>Food:</td>
                <td class="value">
                    5                </td>
            </tr>
            </tbody></table>
</div>

                            </div>

                        </div>

                        <div class="destination fixed">
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/0a0346dd4999bd04761fc6b086e7a1.png" title="Start time:| 18.03.2024<br>23:01:36" alt="">
                        </div>
                    </div>
                </span><!-- Starstreak -->
                    <span class="destinationData">
                                            <span class="destinationPlanet status_abbr_inactive">

                            <span>
                                                                                                            <figure class="planetIcon planet"></figure>Homeworld
                                                                                                </span>
                        </span>

                                            <span class="destinationCoords tooltip tpd-hideOnClickOutside" title=""><a href="{{ route('galaxy.index', ['galaxy' => 7, 'system' => 158])  }}">[7:158:6]</a></span>
                                    </span>

                    <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113969" title="Send a message to Bandit Flame."><span class="icon icon_chat"></span></a>
                    <span class="openDetails">
                    <a href="javascript:void(0);" class="openCloseDetails" data-mission-id="1827745" data-end-time="1710806988">
                                                    <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
                                            </a>
                </span>
                </div>
                <div id="fleet1827772" class="fleetDetails detailsOpened" data-mission-type="1" data-return-flight="1" data-arrival-time="1710807022">
                    <span class="timer tooltip" title="19.03.2024 00:10:22" id="timer_1827772">55m 19s</span>
                    <span class="absTime">00:10:22  Clock</span>
                    <span class="mission hostile textBeefy">Attack (R)</span>
                    <span class="allianceName"></span>
                    <span class="originData">
                    <span class="originCoords tooltip" title="President Hati"><a href="{{ route('galaxy.index', ['galaxy' => 7, 'system' => 158])  }}">[7:158:10]</a></span>
                    <span class="originPlanet">
                                                    <figure class="planetIcon planet"></figure>MyBaseYo
                                            </span>
                </span>
                    <span class="marker01"></span>
                    <span class="marker02"></span>
                    <span class="fleetDetailButton">
                    <a href="#bl1827772" rel="bl1827772" title="Fleet details" class="tooltipRel tooltipClose fleet_icon_reverse">
                    </a>
                </span>
                    <span class="starStreak">
                    <div style="position: relative;">
                        <div class="origin fixed">
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/32926d2ee2884eab5015c14c73afa3.png" title="Time of arrival:| 19.03.2024<br>00:10:22" alt="">
                        </div>

                        <div class="route fixed">

                            <a href="#bl1827772" rel="bl1827772" title="Fleet details" class="tooltipRel tooltipClose basic2 fleet_icon_reverse" id="route_1827772" style="margin-left: 222px;"></a>

                            <div style="display:none;" id="bl1827772">
                                <div class="htmlTooltip">
    <h1>Fleet details:</h1>
    <div class="splitLine"></div>
    <table cellpadding="0" cellspacing="0" class="fleetinfo">
        <tbody><tr>
            <th colspan="2">Ships:</th>
        </tr>
                <tr>
            <td>Small Cargo:</td>
            <td class="value">
                            1                        </td>
        </tr>
                <tr>
            <td>Light Fighter:</td>
            <td class="value">
                            8                        </td>
        </tr>
                <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2">Shipment:</th>
        </tr>
        <tr>
            <td>Metal:</td>
            <td class="value">
                2,501            </td>
        </tr>
        <tr>
            <td>Crystal:</td>
            <td class="value">
                2,500            </td>
        </tr>
        <tr>
            <td>Deuterium:</td>
            <td class="value">
                0            </td>
        </tr>
                    <tr>
                <td>Food:</td>
                <td class="value">
                    0                </td>
            </tr>
            </tbody></table>
</div>

                            </div>

                        </div>

                        <div class="destination fixed">
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/0a0346dd4999bd04761fc6b086e7a1.png" title="Start time:| 18.03.2024<br>23:02:10" alt="">
                        </div>
                    </div>
                </span><!-- Starstreak -->
                    <span class="destinationData">
                                            <span class="destinationPlanet status_abbr_inactive">

                            <span>
                                                                                                            <figure class="planetIcon planet"></figure>Homeworld
                                                                                                </span>
                        </span>

                                            <span class="destinationCoords tooltip" title="Bandit Flame"><a href="{{ route('galaxy.index', ['galaxy' => 7, 'system' => 158])  }}">[7:158:6]</a></span>
                                    </span>

                    <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113969" title="Send a message to Bandit Flame."><span class="icon icon_chat"></span></a>
                    <span class="openDetails">
                    <a href="javascript:void(0);" class="openCloseDetails" data-mission-id="1827772" data-end-time="1710807022">
                                                    <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
                                            </a>
                </span>
                </div>
                <div id="fleet1829594" class="fleetDetails detailsOpened" data-mission-type="7" data-return-flight="" data-arrival-time="1710900719">
                    <span class="timer tooltip" title="19.03.2024 12:26:52" id="timer_1829594">13h 11m 49s</span>
                    <span class="absTime">12:26:52  Clock</span>
                    <span class="mission neutral textBeefy">Colonisation</span>
                    <span class="allianceName"></span>
                    <span class="originData">
                    <span class="originCoords tooltip" title="President Hati"><a href="{{ route('galaxy.index', ['galaxy' => 7, 'system' => 158])  }}">[7:158:10]</a></span>
                    <span class="originPlanet">
                                                    <figure class="planetIcon planet"></figure>MyBaseYo
                                            </span>
                </span>
                    <span class="marker01"></span>
                    <span class="marker02"></span>
                    <span class="fleetDetailButton">
                    <a href="#bl1829594" rel="bl1829594" title="Fleet details" class="tooltipRel tooltipClose fleet_icon_forward">
                    </a>
                </span>
                    <span class="reversal reversal_time ipiHintable" ref="1829594" data-ipi-hint="ipiFleetRecall">
                        <a class="icon_link tooltipHTML" href="{{ route('fleet.movement', ['return' => 1829594])  }}&amp;token=85cb33c35cf4c438287916336573a3b3" title="Recall:| 18.03.2024<br>23:48:01">
                            <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16">
                        </a>
                    </span>
                    <span class="starStreak">
                    <div style="position: relative;">
                        <div class="origin fixed">
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/32926d2ee2884eab5015c14c73afa3.png" title="Start time:| 18.03.2024<br>22:41:45" alt="">
                        </div>

                        <div class="route fixed">

                            <a href="#bl1829594" rel="bl1829594" title="Fleet details" class="tooltipRel tooltipClose basic2 fleet_icon_forward" id="route_1829594" style="margin-left: 11px;"></a>

                            <div style="display:none;" id="bl1829594">
                                <div class="htmlTooltip">
    <h1>Fleet details:</h1>
    <div class="splitLine"></div>
    <table cellpadding="0" cellspacing="0" class="fleetinfo">
        <tbody><tr>
            <th colspan="2">Ships:</th>
        </tr>
                <tr>
            <td>Colony Ship:</td>
            <td class="value">
                            1                        </td>
        </tr>
                <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2">Shipment:</th>
        </tr>
        <tr>
            <td>Metal:</td>
            <td class="value">
                4,000            </td>
        </tr>
        <tr>
            <td>Crystal:</td>
            <td class="value">
                2,000            </td>
        </tr>
        <tr>
            <td>Deuterium:</td>
            <td class="value">
                1,500            </td>
        </tr>
                    <tr>
                <td>Food:</td>
                <td class="value">
                    0                </td>
            </tr>
            </tbody></table>
</div>

                            </div>

                        </div>

                        <div class="destination fixed">
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/9eed39ad582f6ad20e2e202a160739.png" title="Time of arrival:| 19.03.2024<br>12:26:52" alt="">
                        </div>
                    </div>
                </span><!-- Starstreak -->
                    <span class="destinationData">
                                            <span class="destinationPlanet">
                            <span>
                                                                                                            <figure class="planetIcon planet"></figure>Deep space
                                                                                                </span>
                        </span>

                                            <span class="destinationCoords"><a href="{{ route('galaxy.index', ['galaxy' => 3, 'system' => 211])  }}">[3:211:8]</a></span>
                                    </span>
                    <span class="nextTimer tooltip" title="20.03.2024 02:11:59" id="timerNext_1829594">1d 2h 56m</span>
                    <span class="nextabsTime">02:11:59 Clock</span>
                    <span class="nextMission friendly textBeefy">Return</span>

                    <span class="openDetails">
                    <a href="javascript:void(0);" class="openCloseDetails" data-mission-id="1829594" data-end-time="1710851212">
                                                    <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
                                            </a>
                </span>
                </div>
                <div class="placeholder"></div>
            </div>
        </div>
        <script type="text/javascript">
            function unionEdit(response)
            {
                var data = $.parseJSON(response);
                errorBoxAsArray(data["errorbox"]);
                token = data.token;
                $("#federation_" + data["fleetID"]).children().attr("href", "#federationlayer&ajax=1&union=" + data["unionID"] + "&fleet=" + data["fleetID"] + "&target=" + data["targetID"]);
                $("#FederationLayer").parent('.overlayDiv').dialog('close');
                $("#FederationLayer").remove();
            }

            function reloadPage()
            {
                openParentLocation("{{ route('fleet.movement') }}");
            }

            var currentMovementTabExtensionStates = JSON.parse("{\"1827745\":[1,1710806988],\"1827772\":[1,1710807022],\"1829594\":[1,1710851212]}");
            var showInfos = 1;

            $(document).ready(function() {
                var movementLoca = "{\"callBack\":\"Recall\"}";

                if (showInfos == 0) {
                    showInfos = 1;
                    $(".closeAll").children().removeClass('all_open').addClass('all_closed');
                } else {
                    showInfos = 0;
                    $(".closeAll").children().removeClass('all_closed').addClass('all_open');
                }

                new reloadCountdown(
                    getElementByIdWithCache("timer_1827745"),
                    2275,
                    "{{ route('fleet.movement')  }}"
                );

                new movementImageCountdown(
                    getElementByIdWithCache("route_1827745"),
                    2275,
                    4092,
                    1,
                    0,
                    274
                );

                new reloadCountdown(
                    getElementByIdWithCache("timer_1827772"),
                    2309,
                    "{{ route('fleet.movement')  }}"
                );

                new movementImageCountdown(
                    getElementByIdWithCache("route_1827772"),
                    2309,
                    4092,
                    1,
                    0,
                    274
                );


                new reloadCountdown(
                    getElementByIdWithCache("timer_1829594"),
                    46499,
                    "{{ route('fleet.movement')  }}"
                );

                new movementImageCountdown(
                    getElementByIdWithCache("route_1829594"),
                    46499,
                    49507,
                    0,
                    0,
                    274
                );

                new simpleCountdown(
                    getElementByIdWithCache("timerNext_1829594"),
                    96006
                );

                new recallShipCountdown(
                    1829594,
                    1710807721
                )


                initMovement();
            });
        </script>
    </div>


@endsection
