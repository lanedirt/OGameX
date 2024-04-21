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
                <div id="technologydetails_content"></div>
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
@endsection
