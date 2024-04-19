@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="shipyardcomponent" class="maincontent">
        <div id="shipyard">
            <header id="planet" data-anchor="technologyDetails">
                <h2>@lang('Shipyard') - {{ $planet_name }}</h2>
            </header>
            <div id="technologydetails_wrapper">
                <div id="technologydetails_content" style="animation-duration: 0s !important"></div>
            </div>

            <div id="technologies">
                <div id="technologies_battle">
                    <h3>@lang('Battleships')</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                        @foreach ($units[0] as $building)
                            @include('ingame.shipyard.unit-item', ['building' => $building])
                        @endforeach
                    </ul>
                </div>
                <div id="technologies_civil">
                    <h3>Civil ships</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                        @foreach ($units[1] as $building)
                            @include('ingame.shipyard.unit-item', ['building' => $building])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div id="productionboxBottom">
            <div class="productionBoxShips boxColumn ship">

                <div id="productionboxshipyardcomponent" class="productionboxshipyard injectedComponent parent shipyard"><div class="content-box-s">
                        <div class="header">
                            <h3>@lang('Shipyard')</h3>
                        </div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.unit-active', ['build_active' => $build_active])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.unit-queue', ['build_queue' => $build_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '{{ route('shipyard.addbuildrequest') }}';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODO_index.php?page=premium&showDarkMatter=1'

                        window.token = '{{ csrf_token() }}';
                    </script>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var planetMoveInProgress = false;
        </script>
    </div>

    <div id="technologydetailscomponent" class="technologydetails injectedComponent parent shipyard">
        <script type="text/javascript">
            var loca = {"LOCA_ALL_NOTICE":"Reference","LOCA_ALL_NETWORK_ATTENTION":"Caution","locaDemolishStructureQuestion":"Really downgrade TECHNOLOGY_NAME by one level?","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING":"One or more associated bonuses is already maxed out. Do you want to continue construction anyway?"};

            var technologyDetailsEndpoint = "{{ route('shipyard.ajax') }}";
            var selectCharacterClassEndpoint = "#TODO_page=ingame&component=characterclassselection&characterClassId=CHARACTERCLASSID&action=selectClass&ajax=1&asJson=1";
            var deselectCharacterClassEndpoint = "#TODO_page=ingame&component=characterclassselection&characterClassId=CHARACTERCLASSID&action=deselectClass&ajax=1&asJson=1";

            var technologyDetails = new TechnologyDetails({
                technologyDetailsEndpoint: technologyDetailsEndpoint,
                selectCharacterClassEndpoint: selectCharacterClassEndpoint,
                deselectCharacterClassEndpoint: deselectCharacterClassEndpoint,
                loca: loca
            })
            technologyDetails.init()
        </script>
    </div>

    <!-- JAVASCRIPT -->
   <!-- <script type="text/javascript">
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
                    @php /** @var OGame\ViewModels\UnitViewModel $object */ @endphp
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
                                        @include ('ingame.shared.buildqueue.unit-currently-building-pusher', ['build_active' => $object])
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
                                        @include ('ingame.shared.buildqueue.unit-currently-building-pusher', ['build_active' => $object])
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

    </div>-->

@endsection
