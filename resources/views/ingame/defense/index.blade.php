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
        var production_listid;
        $(document).ready(function () {
            initEventTable();
        });
        var player = {hasCommander: false};
        var detailUrl = "{{ route('defense.ajax') }}";

        $(document).ready(function () {
            initResources();
        });

    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt">
        <div id="planet" style="background-image:url(img/headers/defense/defense.jpg)">
            <div id="header_text">


                <h2>Defense - {{ $planet_name }}</h2>

            </div>

            <form method="POST" action="{!! route('defense.addbuildrequest') !!}" name="form" onkeyup="sendBuildRequest(null, event, false);" onsubmit="return false;">
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
                <h2>Defensive structures</h2>
            </div>
            <div class="content">
                <ul id="defensebuilding">
                @php /** @var OGame\ViewModels\UnitViewModel $object */ @endphp
                @foreach ($units[0] as $object)
                        <li id="defense{{ $object->count }}" class="@if ($object->currently_building)
                                on
                            @elseif (!$object->requirements_met)
                                off
                            @elseif (!$object->enough_resources)
                                disabled
                            @else
                                on
                            @endif">
                            <div class="item_box defense{{ $object->object->id }}">
                                <div class="buildingimg">
                                    @include ('ingame.shared.buildqueue.unit-currently-building-pusher', ['build_active' => $object])
                                    <a class="detail_button tooltip js_hideTipOnMobile slideIn" title="{{ $object->object->title }} (0)@if (!$object->requirements_met)
                                            <br/>Requirements are not met
                                            @endif" ref="{{ $object->object->id }}" id="details{{ $object->object->id }}" href="javascript:void(0);">
                        <span class="ecke">
                            <span class="level">
                                <span class="textlabel">
                                    {{ $object->object->title }}	                                </span>
                                {{ $object->amount }}	                            </span>
                        </span>
                                    </a>
                                </div>
                            </div>
                        </li>
                @endforeach
                </ul>
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
