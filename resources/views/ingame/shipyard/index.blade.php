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
                <h2>{{ __('t_resources.shipyard.title') }} - {{ $planet_name }}</h2>
            </header>
            <div id="technologydetails_wrapper">
                <div id="technologydetails_content"></div>
            </div>

            <div id="technologies">
                <div id="technologies_battle">
                    <h3>{{ __('t_ingame.shipyard_page.battleships') }}</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                        @foreach ($units[0] as $building)
                            @include('ingame.shipyard.unit-item', ['building' => $building, 'shipyard_upgrading' => $shipyard_upgrading, 'nanite_upgrading' => $nanite_upgrading, 'is_in_vacation_mode' => $is_in_vacation_mode ?? false])
                        @endforeach
                    </ul>
                </div>
                <div id="technologies_civil">
                    <h3>{{ __('t_ingame.shipyard_page.civil_ships') }}</h3>
                    <ul class="icons">
                        @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                        @foreach ($units[1] as $building)
                            @include('ingame.shipyard.unit-item', ['building' => $building, 'shipyard_upgrading' => $shipyard_upgrading, 'nanite_upgrading' => $nanite_upgrading, 'is_in_vacation_mode' => $is_in_vacation_mode ?? false])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div id="productionboxBottom">
            <div class="productionBoxShips boxColumn ship">

                <div id="productionboxshipyardcomponent" class="productionboxshipyard injectedComponent parent shipyard"><div class="content-box-s">
                        <div class="header">
                            <h3>{{ __('t_resources.shipyard.title') }}</h3>
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
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = @json(__('t_ingame.buildings.last_inquiry_error'));
                        redirectPremiumLink = '#TODO_index.php?page=premium&showDarkMatter=1'
                    </script>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            var planetMoveInProgress = {{ $planet_move_in_progress ? 'true' : 'false' }};
        </script>
    </div>

    <div id="technologydetailscomponent" class="technologydetails injectedComponent parent shipyard">
        <script type="text/javascript">
            var loca = {!! json_encode([
                'LOCA_ALL_NOTICE' => __('t_ingame.buildings.loca_notice'),
                'LOCA_ALL_NETWORK_ATTENTION' => __('t_ingame.shared.caution'),
                'locaDemolishStructureQuestion' => __('t_ingame.buildings.loca_demolish'),
                'LOCA_ALL_YES' => __('t_ingame.shared.yes'),
                'LOCA_ALL_NO' => __('t_ingame.shared.no'),
                'LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING' => __('t_ingame.buildings.loca_lifeform_cap'),
            ]) !!};

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
    {{-- openTech querystring parameter handling --}}
    @include ('ingame.shared.technology.open-tech', ['open_tech_id' => $open_tech_id])
@endsection
