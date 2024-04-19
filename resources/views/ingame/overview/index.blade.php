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
        textContent[0] = "@lang('Diameter'):";
        textContent[1] = "{!! $planet_diameter !!}km (<span>0<\/span>\/<span>188<\/span>)";
        textContent[2] = "@lang('Temperature'):";
        textContent[3] = "{!! $planet_temp_min !!}\u00b0C to {!! $planet_temp_max !!}\u00b0C";
        textContent[4] = "@lang('Position'):";
        textContent[5] = "<a  href=\"{{ route('galaxy.index', ['galaxy' => 4, 'system' => 4, 'position' => 4])  }}\" >[{!! $planet_coordinates !!}]<\/a>";
        textContent[6] = "@lang('Points'):";
        textContent[7] = "<a href='{{ route('highscore.index')  }}'>{{ $user_points }} (Place {!! $user_rank !!} of {!! $max_rank !!})<\/a>";
        textContent[8] = "@lang('Honour points'):";
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

        var cancelProduction_id;
        var production_listid;

        function cancelProduction(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("Caution", "" + question + "", "yes", "No", cancelProductionStart);
        }

        function cancelProductionStart() {
            $('<form id="cancelProductionStart" action="{{ route('resources.cancelbuildrequest') }}" method="POST" style="display: none;">{{ csrf_field() }}<input type="hidden" name="building_id" value="' + cancelProduction_id + '" /> <input type="hidden" name="building_queue_id" value="' + production_listid + '" /> <input type="hidden" name="redirect" value="overview" /></form>').appendTo('body').submit();
        }

        function cancelResearch(id, listid, question) {
            cancelProduction_id = id;
            production_listid = listid;
            errorBoxDecision("Caution", "" + question + "", "yes", "No", cancelResearchStart);
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
                <div id="header_text">
                    <h2>
                        <a href="javascript:void(0);" class="openPlanetRenameGiveupBox">

                            <p class="planetNameOverview">@lang('Overview') -</p>
                            <span id="planetNameHeader">
                            {{ $planet_name }}
                        </span>
                            <img class="hinted tooltip" title="Abandon/Rename Planet"
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
                            <a class="tooltipLeft dark_highlight_tablet fleft"
                               href='{{ route('galaxy.index') }}'
                               title="@lang('The relocation allows you to move your planets to another position in a distant system of your choosing.<br /><br />
The actual relocation first takes place 24 hours after activation. In this time, you can use your planets as normal. A countdown shows you how much time remains prior to the relocation.<br /><br />
Once the countdown has run down and the planet is to be moved, none of your fleets that are stationed there can be active. At this time, there should also be nothing in construction, nothing being repaired and nothing researched. If there is a construction task, a repair task or a fleet still active upon the countdown`s expiry, the relocation will be cancelled.<br /><br />
If the relocation is successful, you will be charged 240.000 Dark Matter. The planets, the buildings and the stored resources including moon will be moved immediately. Your fleets travel to the new coordinates automatically with the speed of the slowest ship. The jump gate to a relocated moon is deactivated for 24 hours.')"
                               data-tooltip-button="To galaxy">
                                <span class="planetMoveIcons settings planetMoveDefault icon fleft"></span>
                                <span class="planetMoveOverviewMoveLink">@lang('Relocate')</span>
                            </a>

                        </div>

                        <a class="dark_highlight_tablet float_right openPlanetRenameGiveupBox"
                           href="javascript:void(0);">
                            <span class="planetMoveOverviewGivUpLink">@lang('Abandon/Rename')</span>
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
                            <h3>@lang('Buildings')</h3>
                        </div>
                        <div class="content">
                            <table cellpadding="0" cellspacing="0" class="construction active">
                                <tbody>
                                {{-- Building is actively being built. --}}
                                @if (!empty($build_active))
                                    <tr>
                                        <th colspan="2">{!! $build_active->object->title !!}</th>
                                    </tr>
                                    <tr class="data">
                                        <td class="first" rowspan="3">
                                            <div>
                                                <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile"
                                                   style="display: block;"
                                                   onclick="cancelProduction({!! $build_active->object->id !!},{!! $build_active->id !!},&quot;Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?&quot;); return false;"
                                                   title="">
                                                    <img class="queuePic" width="40" height="40"
                                                         src="{!! asset('img/objects/buildings/' . $build_active->object->assets->imgSmall) !!}"
                                                         alt="{!! $build_active->object->title !!}">
                                                </a>
                                                <a href="javascript:void(0);" class="tooltip abortNow js_hideTipOnMobile"
                                                   onclick="cancelProduction({!! $build_active->object->id !!},{!! $build_active->id !!},&quot;Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?&quot;); return false;"
                                                   title="Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?">
                                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15"
                                                         width="15">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="desc ausbau">@lang('Improve to') <span
                                                    class="level">@lang('Level') {!! $build_active->level_target !!}</span>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td class="desc">@lang('Duration'):</td>
                                    </tr>
                                    <tr class="data">
                                        <td class="desc timer">
                                            <span id="Countdown">@lang('Loading...')</span>
                                            <!-- JAVASCRIPT -->
                                            <script type="module">
                                                $(document).ready(function () {
                                                    var timerHandler = new TimerHandler();
                                                    new baulisteCountdown(getElementByIdWithCache("Countdown"), {!! $build_active->time_countdown !!}, "{!! route('resources.index') !!}");
                                                });
                                            </script>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td colspan="2">
                                            <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building disabled"
                                               title="Reduces construction time by 50% of the total construction time (15s)."
                                               href="javascript:void(0);"
                                               rel="{{ route('shop.index') }}&amp;buyAndActivate=cb4fd53e61feced0d52cfc4c1ce383bad9c05f67">
                                                <div class="build-faster-img" alt="Halve time"></div>
                                                <span class="build-txt">@lang('Halve time')</span>
                                                <span class="dm_cost overmark">
                                Costs: 750 DM                            </span>
                                                <span class="order_dm">@lang('Purchase Dark Matter')</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endif

                                {{-- Building queue has items. --}}
                                @if (count($build_queue) > 0)
                                    <table class="queue">
                                        <tbody>
                                        <tr>
                                            @foreach ($build_queue as $item)
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet"
                                                       onclick="cancelProduction({!! $item->object->id !!},{!! $item->id !!},&quot;Cancel expansion of {!! $item->object->title !!} to level {!! $item->level_target !!}?&quot;); return false;"
                                                       title="">
                                                        <img class="queuePic"
                                                             src="{!! asset('img/objects/buildings/' . $item->object->assets->imgSmall) !!}"
                                                             height="28" width="28" alt="{!! $item->object->title !!}">
                                                        <span>{!! $item->level_target !!}</span>
                                                    </a>
                                                </td>
                                            @endforeach
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif

                                {{-- No buildings are being built. --}}
                                @if (empty($build_active))
                                    <tr>
                                        <td colspan="2" class="idle">
                                            <a class="tooltip js_hideTipOnMobile
                           "
                                               title="@lang('At the moment there is no building being built on this planet. Click here to get to resources.')"
                                               href="{{ route('resources.index') }}">
                                                @lang('No buildings in construction.')<br/>(@lang('To resources')) </a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="footer"></div>
                    </div>
                </div>
                <div id="productionboxlfbuildingcomponent" class="productionboxlfbuilding injectedComponent parent overview"><div class="content-box-s">
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

                        window.token = '69834f4d6fbeae853fa4fd22814d592c'
                    </script>
                </div>
            </div>
            <div class="productionBoxResearch boxColumn research">
                <div id="productionboxresearchcomponent" class="productionboxresearch injectedComponent parent overview">
                    <div class="content-box-s">
                        <div class="header"><h3>@lang('Research')</h3></div>
                        <div class="content">
                            <table cellspacing="0" cellpadding="0" class="construction active">
                                <tbody>
                                {{-- Building is actively being built. --}}
                                @php /** @var OGame\ViewModels\Queue\ResearchQueueViewModel $research_active */ @endphp
                                @if (!empty($research_active))
                                    <tr>
                                        <th colspan="2">{!! $research_active->object->title !!}</th>
                                    </tr>
                                    <tr class="data">
                                        <td class="first" rowspan="3">
                                            <div>
                                                <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile"
                                                   style="display: block;"
                                                   onclick="cancelResearch({!! $research_active->object->id !!},{!! $research_active->id !!},&quot;Cancel expansion of {!! $research_active->object->title !!} to level {!! $research_active->level_target !!}?&quot;); return false;"
                                                   title="">
                                                    <img class="queuePic" width="40" height="40"
                                                         src="{!! asset('img/objects/research/' . $research_active->object->assets->imgSmall) !!}"
                                                         alt="{!! $research_active->object->title !!}">
                                                </a>
                                                <a href="javascript:void(0);" class="tooltip abortNow js_hideTipOnMobile"
                                                   onclick="cancelResearch({!! $research_active->object->id !!},{!! $research_active->id !!},&quot;Cancel expansion of {!! $research_active->object->title !!} to level {!! $research_active->level_target !!}?&quot;); return false;"
                                                   title="Cancel expansion of {!! $research_active->object->title !!} to level {!! $research_active->level_target !!}?">
                                                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15"
                                                         width="15">
                                                </a>
                                            </div>
                                        </td>
                                        <td class="desc ausbau">@lang('Improve to') <span
                                                    class="level">Level {!! $research_active->level_target !!}</span>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td class="desc">@lang('Duration'):</td>
                                    </tr>
                                    <tr class="data">
                                        <td class="desc timer">
                                            <span id="researchCountdown">@lang('Loading...')</span>
                                            <!-- JAVASCRIPT -->
                                            <script type="module">
                                                $(document).ready(function () {
                                                    var timerHandler = new TimerHandler();
                                                    new baulisteCountdown(getElementByIdWithCache("researchCountdown"), {!! $research_active->time_countdown !!}, "{!! route('research.index') !!}");
                                                });
                                            </script>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td colspan="2">
                                            <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building disabled"
                                               title="Reduces construction time by 50% of the total construction time (15s)."
                                               href="javascript:void(0);"
                                               rel="{{ route('shop.index') }}&amp;buyAndActivate=cb4fd53e61feced0d52cfc4c1ce383bad9c05f67">
                                                <div class="build-faster-img" alt="Halve time"></div>
                                                <span class="build-txt">@lang('Halve time')</span>
                                                <span class="dm_cost overmark">
                                Costs: 750 DM                            </span>
                                                <span class="order_dm">Purchase Dark Matter</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endif

                                {{-- Building queue has items. --}}
                                @if (count($research_queue) > 0)
                                    <table class="queue">
                                        <tbody>
                                        <tr>
                                            @foreach ($research_queue as $item)
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet"
                                                       onclick="cancelResearch({!! $item->object->id !!},{!! $item->id !!},&quot;Cancel expansion of {!! $item->object->title !!} to level {!! $item->level_target !!}?&quot;); return false;"
                                                       title="">
                                                        <img class="queuePic"
                                                             src="{!! asset('img/objects/research/' . $item->object->assets->imgSmall) !!}"
                                                             height="28" width="28" alt="{!! $item->object->title !!}">
                                                        <span>{!! $item->level_target !!}</span>
                                                    </a>
                                                </td>
                                            @endforeach
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif

                                {{-- No buildings are being built. --}}
                                @if (empty($research_active))
                                    <tr>
                                        <td colspan="2" class="idle">
                                            <a class="tooltip js_hideTipOnMobile
                           " title="There is no research done at the moment. Click here to get to your research lab."
                                               href="{{ route('research.index') }}">
                                                @lang('There is no research in progress at the moment').<br/>(@lang('To research'))
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="footer"></div>
                    </div>
                </div>
                <div id="productionboxlfresearchcomponent" class="productionboxlfresearch injectedComponent parent overview"><div class="content-box-s">
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
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '#TODOpage=componentOnly&component=buildlistactions&action=scheduleEntry&asJson=1';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODOpage=premium&showDarkMatter=1'

                        window.token = '69834f4d6fbeae853fa4fd22814d592c'
                    </script>
                </div>
            </div>
            <div class="productionBoxShips boxColumn ship">

                <div id="productionboxshipyardcomponent" class="productionboxshipyard injectedComponent parent overview">
                    <div class="content-box-s">
                        <div class="header"><h3>@lang('Shipyard')</h3></div>
                        <div class="content">
                            <table cellspacing="0" cellpadding="0" class="construction active">
                                <tbody>
                                @php /** @var \OGame\ViewModels\Queue\UnitQueueViewModel $ship_active */ @endphp
                                {{-- Building is actively being built. --}}
                                @if (!empty($ship_active))
                                    <tr class="data">
                                        <th colspan="2">{{ $ship_active->object->title }}</th>
                                    </tr>
                                    <tr class="data">
                                        <td title="Production of {{ $ship_active->object_amount_remaining }} {{ $ship_active->object->title }} in progress"
                                            class="building tooltip" rowspan="2" valign="top">
                                            <a href="{{ route('shipyard.index', ['openTech' => $ship_active->object->id]) }}"
                                               onclick="$('.detail_button[ref=210]').click(); return false;">
                                                <img class="queuePic" width="40" height="40"
                                                     alt="{{ $ship_active->object->title }}"
                                                     src="{{ asset('img/objects/units/' . $ship_active->object->assets->imgSmall) }}"></a>
                                            <div class="shipSumCount"
                                                 id="shipSumCount">{{ $ship_active->object_amount_remaining }}</div>
                                        </td>
                                        <td class="desc timeProdShip">
                                            @lang('Building duration') <span class="shipCountdown"
                                                                             id="shipCountdown">{{ $ship_active->time_countdown }}</span>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td class="desc timeProdAll">
                                            @lang('Total time'): <br><span class="shipAllCountdown"
                                                                           id="shipAllCountdown">{{ $ship_queue_time_countdown }}</span>
                                        </td>
                                    </tr>
                                    <tr class="data">
                                        <td colspan="2">
                                            <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ships "
                                               title="Reduces construction time by 50% of the total construction time (9s)."
                                               href="javascript:void(0);"
                                               rel="{{ route('shop.index') }}&amp;buyAndActivate=75accaa0d1bc22b78d83b89cd437bdccd6a58887">
                                                <div class="build-faster-img" alt="Halve time"></div>
                                                <span class="build-txt">@lang('Halve time')</span>
                                                <span class="dm_cost ">
                                Costs: 750 DM                            </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if (empty($ship_active))
                                    <tr>
                                        <td colspan="2" class="idle">
                                            <a class="tooltip js_hideTipOnMobile
                           "
                                               title="At the moment there are no ships or defense being built on this planet. Click here to get to the shipyard."
                                               href="{{ route('shipyard.index') }}">
                                                @lang('No ships/defense in construction.')<br/>(@lang('To shipyard')) </a>

                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            @php /** @var \OGame\ViewModels\QueueQueue\UnitQueueListViewModel $ship_queue */ @endphp
                            @if ($ship_queue->count() > 0)
                                <table class="queue">
                                    <tbody>
                                    <tr>
                                        @php /** @var \OGame\ViewModels\QueueQueue\UnitQueueViewModel $item */ @endphp
                                        @foreach ($ship_queue->queue as $item)
                                            <td class="tooltip"
                                                title="{{ $item->object_amount }}x {{ $item->object->title }} in the building queue">
                                                <a class="queue_link dark_highlight_tablet"
                                                   href="{{ route('shipyard.index', ['openTech' => $item->object->id]) }}">
                                                    <img class="queuePic"
                                                         src="{{ asset('img/objects/units/' . $item->object->assets->imgSmall) }}"
                                                         height="28" width="28" alt="{{ $item->object->title }}">
                                                    {{ $item->object_amount }}
                                                </a>
                                            </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        <div class="footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
