@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="facilitiescomponent" class="maincontent">
        <div id="facilities">
            <div class="c-left"></div>
            <div class="c-right"></div>
            <header data-anchor="technologyDetails" data-technologydetails-size="large" style="background-image:url({{ asset('img/headers/facilities/' . $header_filename) }}.jpg);">
                <h2>Facilities - {{ $planet_name }}</h2>
            </header>
            <div id="technologydetails_wrapper">
                <div id="technologydetails_content"></div>
            </div>
            <div id="technologies">
                <h3>
                    @lang('Facility buildings')
                </h3>
                <ul class="icons">
                    @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                    @foreach ($buildings[0] as $building)
                        <li class="technology {{ $building->object->class_name }} hasDetails tooltip hideTooltipOnMouseenter js_hideTipOnMobile ipiHintable tpd-hideOnClickOutside"
                            data-technology="{{ $building->object->id }}"
                            data-is-spaceprovider=""
                            aria-label="{{ $building->object->title }}"
                            data-ipi-hint="ipiTechnology{{ $building->object->class_name }}"
                            @if ($building->currently_building)
                                data-status="active"
                                data-is-spaceprovider=""
                                data-progress="26"
                                data-start="1713521207"
                                data-end="1713604880"
                                data-total="61608"
                                title="{{ $building->object->title }}<br/>@lang('Under construction')"
                            @elseif (!$building->requirements_met)
                                data-status="off"
                                title="{{ $building->object->title }}<br/>@lang('Requirements are not met!')"
                            @elseif (!$building->valid_planet_type)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('You can\'t construct that building on a moon!')"
                            @elseif ($building->ship_or_defense_in_progress && $building->object->machine_name == 'shipyard')
                                data-status="disabled"
                            title="{{ $building->object->title }}<br/>@lang('The shipyard is still busy')"
                            @elseif ($building->research_in_progress && $building->object->machine_name == 'research_lab')
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Research is currently being carried out!')"
                            @elseif (!$building->enough_resources)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Not enough resources!')"
                            @elseif ($build_queue_max)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Queue is full')"
                            @else
                                data-status="on"
                                title="{{ $building->object->title }}"
                                @endif
                        >

                        <span class="icon sprite sprite_medium medium {{ $building->object->class_name }}">
                            @if ($building->currently_building)
                            @elseif (!$building->requirements_met)
                            @elseif (!$building->valid_planet_type)
                            @elseif (!$building->enough_resources)
                            @elseif ($build_queue_max)
                            @elseif ($building->research_in_progress && $building->object->machine_name == 'research_lab')
                            @elseif ($building->ship_or_defense_in_progress  && $building->object->machine_name == 'shipyard')
                            @else
                                <button
                                        class="upgrade tooltip hideOthers js_hideTipOnMobile"
                                        aria-label="Expand {!! $building->object->title !!} on level {!! ($building->current_level + 1) !!}" title="Expand {!! $building->object->title !!} on level {!! ($building->current_level + 1) !!}"
                                        data-technology="{{ $building->object->id }}" data-is-spaceprovider="">
                                </button>
                            @endif
                            @if ($building->currently_building)
                                <span class="targetlevel" data-value="{{ $building->current_level + 1 }}" data-bonus="0">{{ $building->current_level + 1 }}</span>
                                <div class="cooldownBackground"></div>
                                <time-counter><time class="countdown buildingCountdown" id="countdownbuildingDetails" data-segments="2">...</time></time-counter>
                            @endif
                            <span class="level" data-value="{{ $building->current_level }}" data-bonus="0">
                            <span class="stockAmount">{{ $building->current_level }}</span>
                            <span class="bonus"></span>
                            </span>
                        </span>
                    @endforeach
                </ul>
            </div>
        </div>
        <div id="productionboxBottom">
            <div class="productionBoxBuildings boxColumn building">
                <div id="productionboxbuildingcomponent" class="productionboxbuilding injectedComponent parent facilities"><div class="content-box-s">
                        <div class="header">
                            <h3>@lang('Buildings')</h3>
                        </div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.building-active', ['build_active' => $build_active])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.building-queue', ['build_queue' => $build_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '{{ route('resources.addbuildrequest.post') }}';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODO_index.php?page=premium&showDarkMatter=1'
                    </script>
                </div>
            </div>
            <div class="productionBoxShips boxColumn ship">
            </div>
        </div>
        <script type="text/javascript">
            var planetMoveInProgress = false;
        </script>
        {{-- Last building slot warning --}}
        @include ('ingame.shared.buildings.last-building-slot-warning', ['planet' => $planet])
    </div>

    <div id="technologydetailscomponent" class="technologydetails injectedComponent parent facilities">
        <script type="text/javascript">
            var loca = {"LOCA_ALL_NOTICE":"Reference","LOCA_ALL_NETWORK_ATTENTION":"Caution","locaDemolishStructureQuestion":"Really downgrade TECHNOLOGY_NAME by one level?","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING":"One or more associated bonuses is already maxed out. Do you want to continue construction anyway?"};

            var technologyDetailsEndpoint = "{{ route('facilities.ajax') }}";
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
    {{-- openTech querystring parameter handling --}}
    @include ('ingame.shared.technology.open-tech', ['open_tech_id' => $open_tech_id])
@endsection
