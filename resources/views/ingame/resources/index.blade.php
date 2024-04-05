@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- JAVASCRIPT -->
    <script type="text/javascript">
        function initResources() {
            var load_done = 1;
            gfSlider = new GFSlider(getElementByIdWithCache('planet'));
        }
        var action = 0;
        var id;
        var priceBuilding = 750;
        var priceShips = 750;
        var loca = loca || {};
        loca = $.extend({}, loca, {
            "error": "Error",
            "errorNotEnoughDM": "Not enough Dark Matter available! Do you want to buy some now?",
            "notice": "Reference"
        });
        var locaPremium = {
            "buildingHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
            "buildingFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?",
            "shipsHalfOverlay": "Do you want to reduce the construction time by 50% of the total construction time () for <b>750 Dark Matter<\/b>?",
            "shipsFullOverlay": "Do you want to immediately complete the construction order for <b>750 Dark Matter<\/b>?"
        };
        var demolish_id;
        var buildUrl;
        function loadDetails(type) {
            url = "{{ route('resources.index', ['ajax' => 1]) }}";
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
        $(document).ready(function () {
            $('#ranks tr').hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });
        });
        var timeDelta = 1514117983000 - (new Date()).getTime();
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
        var cancelProduction_id;
        var production_listid;
        function cancelProduction(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("Caution", "" + question + "", "yes", "No", cancelProductionStart);
        }
        function cancelProductionStart() {
            $('<form id="cancelProductionStart" action="{{ route('resources.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /></form>').appendTo('body').submit();

            //window.location.replace("{!! route('resources.cancelbuildrequest') !!}?_token=" + csrfToken + "&techid=" + cancelProduction_id + "&listid=" + production_listid);
        }
        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('resources.ajax') }}";

        $(document).ready(function () {
            initResources();
@if (!empty($build_active['id']))
            // Countdown for inline building element (pusher)
            var elem = getElementByIdWithCache("b_resources{{ $build_active['object']['id'] }}");
            if(elem) {
                new bauCountdown(elem, {{ $build_active['time_countdown'] }}, {{ $build_active['time_total'] }}, "{{ route('resources.index') }}");
            }
@endif
        });

    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">
        <div id="planet" style="background-image:url({{ asset('img/headers/resources/' . $header_filename) }}.jpg)">
            <div id="header_text">


                <h2>Resources - {{ $planet_name }}</h2>


            </div>
            <div id="slot01" class="slot">
                <a href="{{ route('resources.settings') }}">
                    Resource settings            </a>
            </div>

            <form method="POST" action="{!! route('resources.addbuildrequest') !!}" name="form" onkeyup="sendBuildRequest(null, event, false);" onsubmit="return false;">
                {{ csrf_field() }}
                <div id="detail" class="detail_screen">
                    <div id="techDetailLoading"></div>
                </div>
            </form>

        </div>
        <div class="c-left"></div>
        <div class="c-right"></div>




        <div id="buttonz">
            <div class="header">
                <h2>
                    Resource buildings	</h2>
            </div>
            <div class="content">
                <ul id="building">
                    @foreach ($buildings[0] as $building)
                        <li id="button{!! $building['count']!!}" class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="supply{!! $building['id'] !!}">
                                <div class="buildingimg">
                                    @if ($building['requirements_met'] && $building['enough_resources'] && $building['type'] != 'ship')
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Expand {!! $building['title'] !!} on level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('resources.addbuildrequest.get', ['modus' => 1, 'type' => $building['id'], 'planet_id' => $planet_id, '_token' => csrf_token()]) !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_resources{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $building['current_level'] }}</span>
            </span>
                                            </a>
                                        </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building['title'] !!}" ref="{!! $building['id'] !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title'] !!}
                               </span>
                                {!! $building['current_level'] !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <ul id="storage">
                    @foreach ($buildings[1] as $building)
                        <li id="button{!! $building['count']!!}" class="@if ($building['currently_building'])
                                on
                            @elseif (!$building['requirements_met'])
                                off
                            @elseif (!$building['enough_resources'])
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="@if ($building['currently_building']) item_box @endif supply{!! $building['id']!!}">
                                <div class="buildingimg @if ($building['currently_building'])
                                        stationlarge
                                        @endif">
                                    @if ($building['requirements_met'] && $building['enough_resources'])
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Expand {!! $building['title'] !!} on level {!! ($building['current_level'] + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{{ route('resources.addbuildrequest.get', ['modus' => 1, 'type' => $building['id'], 'planet_id' => $planet_id, '_token' => csrf_token()]) }}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14" alt="">
                                        </a>
                                    @endif
                                    @if ($building['currently_building'])
                                        <div class="construction">
                                            <div class="pusher" id="b_resources{{ $building['id'] }}" style="height:100px;">
                                            </div>
                                            <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building['id'] }}">
                                                <span class="time" id="test" name="zeit"></span>
                                            </a>

                                            <a class="detail_button slideIn"
                                               id="details{{ $building['id'] }}"
                                               ref="{{ $building['id'] }}"
                                               href="javascript:void(0);">
				<span class="eckeoben">
					<span style="font-size:11px;" class="undermark"> {{ $building['current_level'] + 1 }}</span>
				</span>
				<span class="ecke">
					<span class="level">{{ $building['current_level'] }}</span>
				</span>
                                            </a>
                                        </div>
                                    @else
                                            <a class="tooltip js_hideTipOnMobile slideIn" href="javascript:void(0);" title="{!! $building['title']!!}" ref="{!! $building['id'] !!}" id="details">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building['title']!!}	                               </span>
                               {!! $building['current_level'] !!}	                           </span>
                        </span>
                                            </a>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="footer"></div>
                <br class="clearfloat">
            </div>
        </div>

        <div class="content-box-s">
            <div class="header">
                <h3>Buildings</h3>
            </div>
            <div class="content">
                <table cellpadding="0" cellspacing="0" class="construction active">
                    <tbody>
                    {{-- Building is actively being built. --}}
                    @if (!empty($build_active['id']))
                        <tr>
                            <th colspan="2">{!! $build_active['object']['title'] !!}</th>
                        </tr>
                        <tr class="data">
                            <td class="first" rowspan="3">
                                <div>
                                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile" style="display: block;" onclick="cancelProduction({!! $build_active['object']['id'] !!},{!! $build_active['id'] !!},&quot;Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?&quot;); return false;" title="">
                                        <img class="queuePic" width="40" height="40" src="{!! asset('img/objects/buildings/' . $build_active['object']['assets']['img']['small']) !!}" alt="{!! $build_active['object']['title'] !!}">
                                    </a>
                                    <a href="javascript:void(0);" class="tooltip abortNow js_hideTipOnMobile" onclick="cancelProduction({!! $build_active['object']['id'] !!},{!! $build_active['id'] !!},&quot;Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?&quot;); return false;" title="Cancel expansion of {!! $build_active['object']['title'] !!} to level {!! $build_active['object']['level_target'] !!}?">
                                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15" width="15">
                                    </a>
                                </div>
                            </td>
                            <td class="desc ausbau">Improve to						<span class="level">Level {!! $build_active['object']['level_target'] !!}</span>
                            </td>
                        </tr>
                        <tr class="data">
                            <td class="desc">Duration:</td>
                        </tr>
                        <tr class="data">
                            <td class="desc timer">
                                <span id="Countdown">Loading...</span>
                                <!-- JAVASCRIPT -->
                                <script type="text/javascript">
                                var timerHandler=new TimerHandler();
                                new baulisteCountdown(getElementByIdWithCache("Countdown"), {!! $build_active['time_countdown'] !!}, "{!! route('resources.index') !!}");
                                </script>
                            </td>
                        </tr>
                        <tr class="data">
                            <td colspan="2">
                                <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building disabled" title="Reduces construction time by 50% of the total construction time (15s)." href="javascript:void(0);" rel="{{ route('shop.index', ['buyAndActivate' => 'cb4fd53e61feced0d52cfc4c1ce383bad9c05f67'])  }}">
                                    <div class="build-faster-img" alt="Halve time"></div>
                                    <span class="build-txt">Halve time</span>
                            <span class="dm_cost overmark">
                                Costs: 750 DM                            </span>
                                    <span class="order_dm">Purchase Dark Matter</span>
                                </a>
                            </td>
                        </tr>
                    @endif

                    {{-- Building queue has items. --}}
                    @if (count($build_queue) > 0)
                    <table class="queue">
                        <tbody><tr>
                            @foreach ($build_queue as $item)
                                <td>
                                    <a href="javascript:void(0);" class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet" onclick="cancelProduction({!! $item['object']['id'] !!},{!! $item['id'] !!},&quot;Cancel expansion of {!! $item['object']['title'] !!} to level {!! $item['object']['level_target'] !!}?&quot;); return false;" title="">
                                        <img class="queuePic" src="{!! asset('img/objects/buildings/' . $item['object']['assets']['img']['micro']) !!}" height="28" width="28" alt="{!! $item['object']['title'] !!}">
                                        <span>{!! $item['object']['level_target'] !!}</span>
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                        </tbody></table>
                    @endif

                    {{-- No buildings are being built. --}}
                    @if (empty($build_active))
                        <tr>
                            <td colspan="2" class="idle">
                                <a class="tooltip js_hideTipOnMobile
                           " title="At the moment there is no building being built on this planet. Click here to get to resources." href="{{ route('resources.index') }}">
                                    No buildings in construction.                            </a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="footer"></div>
        </div>

    </div>

@endsection
