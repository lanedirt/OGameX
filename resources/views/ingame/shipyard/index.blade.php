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
        var production_listid;
        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('shipyard.ajax') }}";

        $(document).ready(function () {
            initResources();
        });

    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">
        <div id="planet" style="background-image:url(img/headers/shipyard/shipyard.jpg)">
            <div id="header_text">
                <h2>Shipyard - {{ $planet_name }}</h2>
            </div>

            <form method="POST" action="{!! route('shipyard.addbuildrequest') !!}" name="form" onkeyup="sendBuildRequest(null, event, false);" onsubmit="return false;">
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
                    <span class="h_battleships">Combat ships</span>
                    <span class="h_civilships">Civil ships</span>
                </h2>
            </div>
            <div class="content">
                <div id="battleships">
                    <ul id="military">
                    @php /** @var OGame\ViewModels\QueueUnitViewModel $object */ @endphp
                    @foreach ($units[0] as $object)
                            <li id="button{{ $object->count }}" class="@if ($object->currently_building)
                                    on
                                @elseif (!$object->requirements_met)
                                    off
                                @elseif (!$object->enough_resources)
                                    disabled
                                @else
                                    on
                                @endif">
                                <div class="item_box military{{ $object->object->id }}">
                                    <div class="buildingimg">
                                        <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{{ $object->object->title }} (0)@if (!$object->requirements_met)
                                                <br/>Requirements are not met
                                                @endif" ref="{{ $object->object->id }}" id="details{{ $object->object->id }}" href="javascript:void(0);">
                                            <span class="ecke">
                                                <span class="level">
                                                    <span class="textlabel">{{ $object->object->title }}</span>
                                                    {{ \OGame\Facades\AppUtil::formatNumberShort($object->amount) }}
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div id="civilships">
                    <ul id="civil">
                        @foreach ($units[1] as $object)
                            <li id="button{{ $object->count }}" class="@if ($object->currently_building)
                                    on
                                @elseif (!$object->requirements_met)
                                    off
                                @elseif (!$object->enough_resources)
                                    disabled
                                @else
                                    on
                                @endif">
                                <div class="item_box civil{{ $object->object->id }}">
                                    <div class="buildingimg">
                                        <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{{ $object->object->title }} (0)@if (!$object->requirements_met)
                                                <br/>Requirements are not met
                                                @endif" ref="{{ $object->object->id }}" id="details{{ $object->object->id }}" href="javascript:void(0);">
                                        <span class="ecke">
                                            <span class="level">
                                                <span class="textlabel">{{ $object->object->title }}</span>
                                                {{ $object->amount }}
                                            </span>
                                        </span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <br class="clearfloat">
                <div class="footer"></div>
            </div>
        </div>

        <div id="line">
            {{-- Unit which is actively being built. --}}
            @include ('ingame.shared.buildqueue.unit-active', ['build_active' => $build_active])
            {{-- Unit queue --}}
            @include ('ingame.shared.buildqueue.unit-queue', ['build_queue' => $build_queue])
            <div class="clearfloat"></div>
        </div>

    </div>

@endsection
