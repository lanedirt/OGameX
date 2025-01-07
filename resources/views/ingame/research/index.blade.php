@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="researchcomponent" class="maincontent">
        <div id="research">
            <header id="planet" data-anchor="technologyDetails">
                <h2>@lang('Research') - {{ $planet_name }}</h2>
            </header>
            <div id="technologydetails_wrapper">
                <div id="technologydetails_content"></div>
            </div>

            <div id="technologies">
                <div id="technologies_basic">
                    <h3>@lang('Basic research')</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\ResearchViewModel $building */ @endphp
                        @foreach ($research[0] as $building)
                            @include('ingame.research.research-item', ['building' => $building, 'build_queue_max' => $build_queue_max])
                        @endforeach
                    </ul>
                </div>
                <div id="technologies_drive">
                    <h3>@lang('Drive research')</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\ResearchViewModel $building */ @endphp
                        @foreach ($research[1] as $building)
                            @include('ingame.research.research-item', ['building' => $building, 'build_queue_max' => $build_queue_max])
                        @endforeach
                    </ul>
                </div>
                <div id="technologies_advanced">
                    <h3>@lang('Advanced researches')</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\ResearchViewModel $building */ @endphp
                        @foreach ($research[2] as $building)
                            @include('ingame.research.research-item', ['building' => $building, 'build_queue_max' => $build_queue_max])
                        @endforeach
                    </ul>
                </div>
                <div id="technologies_combat">
                    <h3>@lang('Combat research')</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\ResearchViewModel $building */ @endphp
                        @foreach ($research[3] as $building)
                            @include('ingame.research.research-item', ['building' => $building, 'build_queue_max' => $build_queue_max])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div id="productionboxBottom">
            <div class="productionBoxResearch boxColumn research">
                <div id="productionboxresearchcomponent" class="productionboxresearch injectedComponent parent research"><div class="content-box-s">
                        <div class="header">
                            <h3>@lang('Research')</h3>
                        </div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.research-active', ['build_active' => $build_active])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.research-queue', ['build_queue' => $build_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '{{ route('research.addbuildrequest.post') }}';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODO_index.php?page=premium&showDarkMatter=1'
                    </script>
                </div>
            </div>
            <div class="productionBoxShips boxColumn ship">
            </div>
        </div>
        <script type="text/javascript">
            var LOCA_PLANETMOVE_BREAKUP_WARNING = 'Caution! This mission may still be running once the relocation period starts and if this is the case, the process will be canceled. Do you really want to continue with this job?';
            var LOCA_ALL_NETWORK_ATTENTION = 'Caution';
            var LOCA_ALL_YES = 'yes';
            var LOCA_ALL_NO = 'No';

            var planetMoveInProgress = false;
            var lastBuildingSlot = {"showWarning":false,"slotWarning":"This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item (e.g. <a href=\"#page&#61;shop#page&#61;shop&amp;category&#61;c18170d3125b9941ef3a86bd28dded7bf2066a6a&amp;item&#61;04e58444d6d0beb57b3e998edc34c60f8318825a\" class=\"tooltipHTML itemLink\">Gold Planet Fields<\/a>) to obtain more slots. Are you sure you want to build this building?"};
        </script>
    </div>

    <div id="technologydetailscomponent" class="technologydetails injectedComponent parent research">
        <script type="text/javascript">
            var loca = {"LOCA_ALL_NOTICE":"Reference","LOCA_ALL_NETWORK_ATTENTION":"Caution","locaDemolishStructureQuestion":"Really downgrade TECHNOLOGY_NAME by one level?","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING":"One or more associated bonuses is already maxed out. Do you want to continue construction anyway?"};

            var technologyDetailsEndpoint = "{{ route('research.ajax') }}";
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
