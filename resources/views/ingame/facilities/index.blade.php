@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- JAVASCRIPT -->
    <script type="text/javascript">
        function initStation() {
            var load_done = 1;
            gfSlider = new GFSlider(getElementByIdWithCache('planet'));
        }
        var action = 0;
        var id;
        var priceBuilding = 750;
        var priceShips = 750;
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
        $(document).ready(function () {
            $('#ranks tr').hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(this).removeClass('hover');
            });
        });

        var timeDelta = 1514117983000 - (new Date()).getTime();
        var cancelProduction_id;
        var production_listid;
        function cancelProduction(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("Caution", "" + question + "", "yes", "No", cancelProductionStart);
        }
        function cancelProductionStart() {
            $('<form id="cancelProductionStart" action="{{ route('facilities.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /></form>').appendTo('body').submit();
        }

        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('facilities.ajax') }}";

        $(document).ready(function () {
            initStation();
        });

    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">
        <div id="planet" style="background-image:url({{ asset('img/headers/facilities/' . $header_filename) }}.jpg)">
            <div id="header_text">
                <h2>Facilities - {{ $planet_name }}</h2>
            </div>

            <form method="POST" action="{!! route('facilities.addbuildrequest') !!}" name="form">
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
                <h2>Facility buildings        </h2>
            </div>
            <div class="content">
                <ul id="stationbuilding">
                @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                @foreach ($buildings[0] as $building)
                        <li id="button{!! $building->count!!}" class="@if ($building->currently_building)
                                on
                            @elseif (!$building->requirements_met)
                                off
                            @elseif (!$building->enough_resources)
                                disabled
                            @elseif ($build_queue_max)
                                disabled
                            @else
                                on
                            @endif
                                ">
                            <div class="station{!! $building->object->id !!}">
                                <div class="stationlarge buildingimg">
                                    @if ($building->requirements_met && $building->enough_resources)
                                        <a class="fastBuild tooltip js_hideTipOnMobile" title="Expand {!! $building->object->title !!} on level {!! ($building->current_level + 1) !!}" href="javascript:void(0);" onclick="sendBuildRequest('{!! route('facilities.addbuildrequest.get', ['modus' => 1, 'type' => $building->object->id, 'planet_id' => $planet_id, '_token' => csrf_token()]) !!}', null, 1);">
                                            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" width="22" height="14">
                                        </a>
                                    @endif
                                    @if ($building->currently_building)
                                            <div class="construction">
                                                <div class="pusher" id="b_resources{{ $building->object->id }}" style="height:100px;">
                                                </div>
                                                <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $building->object->id }}">
                                                    <span class="time" id="test" name="zeit"></span>
                                                </a>

                                                <a class="detail_button slideIn"
                                                   id="details{{ $building->object->id }}"
                                                   ref="{{ $building->object->id }}"
                                                   href="javascript:void(0);">
				<span class="eckeoben">
					<span style="font-size:11px;" class="undermark"> {{ $building->current_level + 1 }}</span>
				</span>
				<span class="ecke">
					<span class="level">{{ $building->current_level }}</span>
				</span>
                                                </a>
                                            </div>
                                    @endif
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{!! $building->object->title !!}" ref="{!! $building->object->id !!}" id="details" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                               <span class="textlabel">
                                   {!! $building->object->title !!}
                               </span>
                                {!! $building->current_level !!}	                           </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach
                </ul>
                <div class="footer"></div>
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
                    @include ('ingame.shared.buildqueue.building-active', ['build_active' => $build_active])
                    {{-- Building queue has items. --}}
                    @include ('ingame.shared.buildqueue.building-queue', ['build_queue' => $build_queue])
                    </tbody>
                </table>
            </div>
            <div class="footer"></div>
        </div>

    </div>

@endsection
