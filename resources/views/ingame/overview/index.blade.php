
@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <!-- JAVASCRIPT -->
    <script type="text/javascript">
        var textContent = [];
        textContent[0] = "{{ __('t_ingame.overview.diameter') }}:";
        textContent[1] = "{!! $planet_diameter !!}km (<span>{{ $building_count }}<\/span>\/<span>{{ $max_building_count }}<\/span>)";
        textContent[2] = "{{ __('t_ingame.overview.temperature') }}:";
        textContent[3] = "{!! $planet_temp_min !!}\u00b0C to {!! $planet_temp_max !!}\u00b0C";
        textContent[4] = "{{ __('t_ingame.overview.position') }}:";
        textContent[5] = "<a  href=\"{{ route('galaxy.index', ['galaxy' => 4, 'system' => 4, 'position' => 4])  }}\" >[{!! $planet_coordinates !!}]<\/a>";
        textContent[6] = "{{ __('t_ingame.overview.points') }}:";
        textContent[7] = "<a href='{{ route('highscore.index')  }}'>{{ $user_points }} ({{ __('t_ingame.overview.score_place') }} {!! $user_rank !!} {{ __('t_ingame.overview.score_of') }} {!! $max_rank !!})<\/a>";
        textContent[8] = "{{ __('t_ingame.overview.honour_points') }}:";
        textContent[9] = "0";

        var textDestination = [];
        textDestination[0] = "diameterField";
        textDestination[1] = "diameterContentField";
        textDestination[2] = "temperatureField";
        textDestination[3] = "temperatureContentField";
        textDestination[4] = "positionField";
        textDestination[5] = "positionContentField";
        textDestination[6] = "scoreField";
        textDestination[7] = "scoreContentField";
        textDestination[8] = "honorField";
        textDestination[9] = "honorContentField";
        var currentIndex = 0;
        var currentChar = 0;
        var linetwo = 0;

        @if($planet_move)
        var planetMoveLoca = {
            "askTitle": "{{ __('t_ingame.planet_move.resettle_title') }}",
            "askCancel": "{{ __('t_ingame.planet_move.cancel_confirm') }}",
            "yes": "{{ __('t_ingame.shared.yes') }}",
            "no": "{{ __('t_ingame.shared.no') }}",
            "success": "{{ __('t_ingame.planet_move.cancel_success') }}",
            "error": "{{ __('t_ingame.shared.error') }}"
        };
        var planetMoveCooldown = {{ $planet_move_countdown }};
        new SimpleCountdownTimer('#moveCountdown', {{ $planet_move_countdown }}, '{{ route('overview.index') }}');
        @elseif($planet_move_cooldown > 0)
        new SimpleCountdownTimer('#moveCountdown', {{ $planet_move_cooldown }}, '{{ route('overview.index') }}');
        @endif

        var cancelProduction_id;
        var production_listid;

        function cancelProduction(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("{{ __('t_ingame.shared.caution') }}", "" + question + "", "{{ __('t_ingame.shared.yes') }}", "{{ __('t_ingame.shared.no') }}", cancelProductionStart);
        }

        function cancelProductionStart() {
            $('<form id="cancelProductionStart" action="{{ route('resources.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /> <input type="hidden" name="redirect" value="overview" /></form>').appendTo('body').submit();
        }

        function cancelResearch(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("{{ __('t_ingame.shared.caution') }}", "" + question + "", "{{ __('t_ingame.shared.yes') }}", "{{ __('t_ingame.shared.no') }}", cancelResearchStart);
        }

        function cancelResearchStart() {
            $('<form id="cancelProductionStart" action="{{ route('research.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /> <input type="hidden" name="redirect" value="overview" /></form>').appendTo('body').submit();
        }

        function initType() {
            type();
        }

        $(document).ready(function () {
            gfSlider = new GFSlider(getElementByIdWithCache('detailWrapper'));
            initType();
            @if (!empty($ship_active))
            // Countdown for inline ship element (pusher)
            new shipCountdown(getElementByIdWithCache('shipAllCountdown'), getElementByIdWithCache('shipCountdown'), getElementByIdWithCache('shipSumCount'), {{ $ship_active->time_countdown }}, {{ $ship_active->time_countdown_object_next }}, {{ $ship_queue_time_countdown }}, {{ $ship_active->object_amount_remaining }}, "{{ route('shipyard.index') }}");
            @endif
        });
    </script>

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif"/>
    </div>

  
    <div id="inhalt">
        <div id="planet" style="background-image:url({{ asset('img/headers/overview/' . $header_filename) }}.jpg);">

            <div id="detailWrapper">
                @if ($has_moon)
                    <div id="moon">
                        <a href="{{ request()->url() }}?{{ http_build_query([...request()->query(), 'cp' => $other_planet->getPlanetId()]) }}"
                           class="tooltipBottom js_hideTipOnMobile"
                           title="{{ __('t_ingame.overview.switch_to_moon') }} {{ $other_planet->getPlanetName() }}">
                            <img alt="{{ $other_planet->getPlanetName() }}" src="{!! asset('img/moons/big/' . $other_planet->getPlanetImageType() . '.gif') !!}">
                        </a>
                    </div>
                @elseif ($has_planet)
                    <div id="planet_as_moon">
                        <a href="{{ request()->url() }}?{{ http_build_query([...request()->query(), 'cp' => $other_planet->getPlanetId()]) }}"
                           class="tooltipBottom js_hideTipOnMobile"
                           title="{{ __('t_ingame.overview.switch_to_planet') }} {{ $other_planet->getPlanetName() }}">
                            <img alt="{{ $other_planet->getPlanetName() }}" src="{!! asset('img/planets/' . $other_planet->getPlanetBiomeType() . '_moon_view.jpg') !!}">
                        </a>
                    </div>
                @endif

                <div id="header_text">
                    <h2>
                        <a href="javascript:void(0);" class="openPlanetRenameGiveupBox">

                            <p class="planetNameOverview">{{ __('t_ingame.overview.page_title') }} -</p>
                            <span id="planetNameHeader">
                            {{ $planet_name }}
                        </span>
                            <img class="hinted tooltip" title="{{ __('t_ingame.overview.abandon_rename_title') }}"
                                 src="/img/icons/1f57d944fff38ee51d49c027f574ef.gif" width="16" height="16"/>
                        </a>
                    </h2>
                </div>
                <div id="detail" class="detail_screen">
                    <div id="techDetailLoading"></div>
                </div>
                <div id="planetdata">
                    <div class="overlay"></div>
                    <div id="planetDetails">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td class="desc">
                                    <span id="diameterField"></span>
                                </td>
                                <td class="data">
                                    <span id="diameterContentField"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="desc">
                                    <span id="temperatureField"></span>
                                </td>
                                <td class="data">
                                    <span id="temperatureContentField"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="desc">
                                    <span id="positionField"></span>
                                </td>
                                <td class="data">
                                    <span id="positionContentField"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="desc">
                                    <span id="scoreField"></span></td>
                                <td class="data">
                                    <span id="scoreContentField"></span>
                                </td>
                            </tr>

                            <tr>
                                <td class="desc">
                                    <span id="honorField"></span></td>
                                <td class="data ">
                                    <span id="honorContentField"></span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div id="planetOptions">

                        <div class="planetMoveStart fleft" style="display: inline;">
                            @if($planet_move)
                                @php
                                    $t = $planet_move_countdown;
                                    $countdownParts = [];
                                    foreach (['d' => 86400, 'h' => 3600, 'm' => 60, 's' => 1] as $u => $v) {
                                        $n = intdiv($t, $v);
                                        if ($n > 0) {
                                            $t -= $n * $v;
                                            $countdownParts[] = $n . $u;
                                        }
                                        if (count($countdownParts) >= 2) break;
                                    }
                                    $countdownFormatted = implode(' ', $countdownParts);
                                @endphp
                                <span class="planetMoveProgress fleft">
                                    @if(count($planet_move_blockers) > 0)
                                        <span class="tooltip planetMoveIcons planetMoveBreakup icon"
                                              title="{{ __('t_ingame.planet_move.blockers_title') }} {{ implode(', ', $planet_move_blockers) }}"></span>
                                    @else
                                        <span class="tooltip planetMoveIcons planetMoveOk icon"
                                              title="{{ __('t_ingame.planet_move.no_blockers') }}"></span>
                                    @endif
                                    <span id="moveProgress">
                                        <a id="moveCountdown" class="tooltip js_hideTipOnMobile undermark"
                                           href="{{ route('galaxy.index', ['galaxy' => $planet_move->target_galaxy, 'system' => $planet_move->target_system]) }}"
                                           title="[{{ $planet_move_target }}]">{{ $countdownFormatted }}
                                        </a>
                                        <a class="tooltip js_hideTipOnMobile cancelMove icon_link"
                                           href="javascript:void(0);"
                                           rel="{{ route('planetMove.cancel') }}"
                                           title="{{ __('t_ingame.planet_move.cancel') }}">
                                            <span class="icon icon_quit"></span>
                                        </a>
                                    </span>
                                </span>
                            @elseif($planet_move_cooldown > 0)
                                @php
                                    $t = $planet_move_cooldown;
                                    $cooldownParts = [];
                                    foreach (['d' => 86400, 'h' => 3600, 'm' => 60, 's' => 1] as $u => $v) {
                                        $n = intdiv($t, $v);
                                        if ($n > 0) {
                                            $t -= $n * $v;
                                            $cooldownParts[] = $n . $u;
                                        }
                                        if (count($cooldownParts) >= 2) break;
                                    }
                                    $cooldownFormatted = implode(' ', $cooldownParts);
                                @endphp
                                <span class="tooltip planetMoveIcons planetMoveInactive icon"
                                      title="{{ __('t_ingame.planet_move.cooldown_title') }}"></span>
                                <span id="moveCountdown" class="status_abbr_longinactive tooltip fleft"
                                      title="{{ __('t_ingame.planet_move.cooldown_title') }}">{{ $cooldownFormatted }}</span>
                            @else
                                <a class="tooltipLeft dark_highlight_tablet fleft"
                                   href='{{ route('galaxy.index') }}'
                                   title="{{ __('t_ingame.planet_move.explanation') }}"
                                   data-tooltip-button="{{ __('t_ingame.planet_move.to_galaxy') }}">
                                    <span class="planetMoveIcons settings planetMoveDefault icon fleft"></span>
                                    <span class="planetMoveOverviewMoveLink">{{ __('t_ingame.planet_move.relocate') }}</span>
                                </a>
                            @endif
                        </div>

                        <a class="dark_highlight_tablet float_right openPlanetRenameGiveupBox"
                           href="javascript:void(0);">
                            <span class="planetMoveOverviewGivUpLink">{{ __('t_ingame.overview.abandon_rename') }}</span>
                            <span class="planetMoveIcons settings planetMoveGiveUp icon"></span>
                        </a>
                    </div>
                </div>
            </div>

            <div id="buffBar" class="sliderWrapper">
                <div data-uuid="" data-id="" class="add_item">
                    <a class="activate_item border3px" href="javascript:void(0);" ref="1"></a>
                </div>

                <ul class="active_items hidden">
                    <li>
                    </li>
                </ul>
            </div>


        </div>
        <div class="c-left"></div>
        <div class="c-right"></div>

        <div id="productionboxBottom">
            <div class="productionBoxBuildings boxColumn building">
                <div id="productionboxbuildingcomponent" class="productionboxbuilding injectedComponent parent overview">
                    <div class="content-box-s">
                        <div class="header">
                            <h3>{{ __('t_ingame.overview.buildings') }}</h3>
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
                <!--<div id="productionboxlfbuildingcomponent" class="productionboxlfbuilding injectedComponent parent overview"><div class="content-box-s">
                        <div class="header">
                            <h3>Lifeform Buildings
                            </h3>
                        </div>
                        <div class="content">
                            <table cellspacing="0" cellpadding="0" class="construction active">
                                <tbody>
                                <tr>
                                    <td colspan="2" class="idle">
                                        <a class="tooltip js_hideTipOnMobile " title="The lifeforms are not currently constructing any buildings. Click here to view buildings.
" href="#TODO_=ingame&amp;component=lfbuildings">
                                            No buildings in construction.
                                            <br>
                                            (View Buildings
                                            )
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="footer"></div>
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '#TODOpage=componentOnly&component=buildlistactions&action=scheduleEntry&asJson=1';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODOpage=premium&showDarkMatter=1'
                    </script>
                </div>
            </div>-->
            </div>
            <div class="productionBoxResearch boxColumn research">
                <div id="productionboxresearchcomponent" class="productionboxresearch injectedComponent parent overview">
                    <div class="content-box-s">
                        <div class="header"><h3>{{ __('t_ingame.overview.research') }}</h3></div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.research-active', ['build_active' => $research_active])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.research-queue', ['build_queue' => $research_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                </div>
                <!--<div id="productionboxlfresearchcomponent" class="productionboxlfresearch injectedComponent parent overview"><div class="content-box-s">
                        <div class="header">
                            <h3>Lifeform Research
                            </h3>
                        </div>
                        <div class="content">
                            <table cellspacing="0" cellpadding="0" class="construction active">
                                <tbody>
                                <tr>
                                    <td colspan="2" class="idle">
                                        <a class="tooltip js_hideTipOnMobile " title="There is currently no research in progress. Click here to view lifeform techs.
" href="#TODOpage=ingame&amp;component=lfresearch">
                                            There is no research in progress at the moment.
                                            <br>
                                            (View Lifeform Development
                                            )
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="footer"></div>
                    </div>-->
                <script type="text/javascript">
                    var scheduleBuildListEntryUrl = '#TODOpage=componentOnly&component=buildlistactions&action=scheduleEntry&asJson=1';
                    var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                    redirectPremiumLink = '#TODOpage=premium&showDarkMatter=1'
                </script>
            </div>

            <div class="productionBoxShips boxColumn ship">

                <div id="productionboxshipyardcomponent" class="productionboxshipyard injectedComponent parent overview">
                    <div class="content-box-s">
                        <div class="header"><h3>{{ __('t_resources.shipyard.title') }}</h3></div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.unit-active', ['build_active' => $ship_active, 'build_queue_countdown' => $ship_queue_time_countdown])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.unit-queue', ['build_queue' => $ship_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
