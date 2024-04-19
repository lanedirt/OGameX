@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>
    <div id="traderOverviewcomponent" class="maincontent">
        <div id="traderOverview">
            <div id="inhalt">
                <div id="planet">
                    <div id="detail" class="detail_screen small">
                        <div id="techDetailLoading"></div>
                    </div>
                    <div id="loadingOverlay">
                        <img src="/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif" alt="load...">
                    </div>
                    <div id="header_text">
                        <h2></h2>
                        <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="javascript:void(0)" title="Back"></a>
                        <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="javascript:void(0)" title="Back"></a>
                    </div>

                    <div id="js_traderResources" class="js_trader trader_link tooltipLeft js_hideTipOnMobile" title="">
                        <h2>Resource Market</h2>
                    </div>
                    <div id="js_traderAuctioneer" class="js_trader trader_link tooltipRight js_hideTipOnMobile" title="Items are offered here daily and can be purchased using resources.">
                        <h2>Auctioneer</h2>
                    </div>
                    <br>
                    <div id="js_traderScrap" class="js_trader trader_link tooltipLeft js_hideTipOnMobile" title="">
                        <h2>Scrap Merchant</h2>
                    </div>
                    <div id="js_traderImportExport" class="js_trader trader_link tooltipRight js_hideTipOnMobile" title="Containers with unknown contents are sold here for resources every day.">
                        <h2>Import / Export</h2>
                    </div>
                </div>
                <div class="c-left"></div>
                <div class="c-right"></div>
         </div>
        </div>
    </div>


@endsection
