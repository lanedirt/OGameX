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
                        <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('overview.index') }}" title="@lang('Back')"></a>
                        <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('overview.index') }}" title="@lang('Back')"></a>
                    </div>
                    <a href="{{ route('merchant.resource-market') }}" id="js_traderResources" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderResources" data-tooltip-title="@lang('You can exchange resources for other resources here.')">
                        <h2>@lang('Resource Market')</h2>
                    </a>
                    <a href="#" id="js_traderAuctioneer" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderAuctioneer" data-tooltip-title="@lang('Items are offered here daily and can be purchased using resources.')">
                        <h2>@lang('Auctioneer')</h2>
                    </a>
                    <br>
                    <a href="{{ route('merchant.scrap') }}" id="js_traderScrap" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderScrap" data-tooltip-title="@lang('The scrap merchant accepts used ships and defence systems.')">
                        <h2>@lang('Scrap Merchant')</h2>
                    </a>
                    <a href="#" id="js_traderImportExport" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderImportExport" data-tooltip-title="@lang('Containers with unknown contents are sold here for resources every day.')">
                        <h2>@lang('Import / Export')</h2>
                    </a>
                </div>
                <div class="c-left"></div>
                <div class="c-right"></div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // Add hover effects for merchant links
            $('#js_traderResources').hover(
                function() { $(this).addClass('resources_link_hover'); },
                function() { $(this).removeClass('resources_link_hover'); }
            );

            $('#js_traderAuctioneer').hover(
                function() { $(this).addClass('auctioneer_link_hover'); },
                function() { $(this).removeClass('auctioneer_link_hover'); }
            );

            $('#js_traderScrap').hover(
                function() { $(this).addClass('scrap_link_hover'); },
                function() { $(this).removeClass('scrap_link_hover'); }
            );

            $('#js_traderImportExport').hover(
                function() { $(this).addClass('importexport_link_hover'); },
                function() { $(this).removeClass('importexport_link_hover'); }
            );
        });
    </script>

@endsection
