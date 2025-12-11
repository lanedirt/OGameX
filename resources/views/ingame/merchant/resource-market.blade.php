@extends('ingame.layouts.main')

@section('content')

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>
    <div id="traderOverviewcomponent" class="maincontent">
        <div id="traderOverview">
            <div id="inhalt">
                <div id="planet" style="background-position: 0px 0px; height: 250px;" class="detail">
                    <div id="detail" class="detail_screen small">
                        <div id="techDetailLoading"></div>
                    </div>
                    <div id="loadingOverlay" class="" style="display: none;">
                        <img src="/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif" alt="load...">
                    </div>
                    <div id="header_text" style="display: block;">
                        <h2>@lang('Resource Market')</h2>
                        <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile right" href="{{ route('merchant.index') }}" data-tooltip-title="@lang('Back')" style="display: inline;"></a>
                        <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('merchant.index') }}" data-tooltip-title="@lang('Back')"></a>
                    </div>
                    @if($activeMerchant)
                        <div id="slot01" class="slot">
                            <a href="{{ route('merchant.market', ['type' => $activeMerchant['type']]) }}?overlay=1"
                               class="overlay tooltipHTML js_hideTipOnMobile"
                               data-overlay-class="traderlayer"
                               data-tooltip-title="@lang('Trade|Trade your resources at the agreed price')">
                                @lang('trade')
                            </a>
                        </div>
                    @endif
                    <div id="js_traderResources" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderResources" data-tooltip-title="@lang('You can exchange resources for other resources here.')" style="display: none;">
                        <h2>@lang('Resource Market')</h2>
                    </div>
                    <div id="js_traderAuctioneer" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderAuctioneer" data-tooltip-title="@lang('Items are offered here daily and can be purchased using resources.')" style="display: none;">
                        <h2>@lang('Auctioneer')</h2>
                    </div>
                    <br>
                    <div id="js_traderScrap" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderScrap" data-tooltip-title="@lang('The scrap merchant accepts used ships and defence systems.')" style="display: none;">
                        <h2>@lang('Scrap Merchant')</h2>
                    </div>
                    <div id="js_traderImportExport" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderImportExport" data-tooltip-title="@lang('Containers with unknown contents are sold here for resources every day.')" style="display: none;">
                        <h2>@lang('Import / Export')</h2>
                    </div>
                </div>
                <div class="c-left c-small" style=""></div>
                <div class="c-right c-small" style=""></div>

                <!-- Trader Resources Start -->
                <div id="div_traderResources" class="div_trader" style="">

                    <div id="boxHeader" class="header">
                        <h2>@lang('Resource Market')</h2>
                    </div>

                    <div class="big_tabs content ui-tabs ui-corner-all ui-widget ui-widget-content">
                        <ul role="tablist" class="ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header">
                            <li class="big_tab ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-changeResource" aria-labelledby="ui-id-8" aria-selected="true" aria-expanded="true">
                                <a href="#tabs-changeResource" class="ipiHintable ui-tabs-anchor" data-ipi-hint="ipiTraderResourcesExchange" role="presentation" tabindex="-1" id="ui-id-8">
                                    @lang('Exchange resources')
                                </a>
                            </li>
                        </ul>

                        <div id="tabs-changeResource" class="big_tab_content ui-tabs-panel ui-corner-bottom ui-widget-content" aria-labelledby="ui-id-8" role="tabpanel" aria-hidden="false" style="">

                            <div class="teaser_txt">
                                <h2>@lang('Exchange your resources.')</h2>
                            </div>

                            <div class="clearfix content_inner">
                                <div class="call_trader_step1">
                                    <p class="step_info">@lang('1. Exchange your resources.')</p>
                                    <ul class="resource_list">
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link metal_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'metal' ? 'active oldTraderActive' : '' !!}" data-resource-id="1" data-resource-type="metal" data-tooltip-title="@lang('Metal|Sell your Metal and get Crystal or Deuterium.<p>Costs: 3,500 Dark Matter</p>.')">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">@lang('Metal')</p>
                                            </a>
                                        </li>
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link crystal_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'crystal' ? 'active oldTraderActive' : '' !!}" data-resource-id="2" data-resource-type="crystal" data-tooltip-title="@lang('Crystal|Sell your Crystal and get Metal or Deuterium.<p>Costs: 3,500 Dark Matter</p>.')">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">@lang('Crystal')</p>
                                            </a>
                                        </li>
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link deuterium_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'deuterium' ? 'active oldTraderActive' : '' !!}" data-resource-id="3" data-resource-type="deuterium" data-tooltip-title="@lang('Deuterium|Sell your Deuterium and get Metal or Crystal.<p>Costs: 3,500 Dark Matter</p>.')">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">@lang('Deuterium')</p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="call_trader_step2">
                                    <p class="step_info">@lang('2. Call merchant')</p>
                                    <div class="step_arrow"></div>
                                    <div class="call_trader_box ipiHintable" data-ipi-hint="ipiTraderResourcesCall">
                                        <div id="js_alreadyPaidSection" class="{{ $activeMerchant ? '' : 'hidden' }}">
                                            <p class="cost_txt"><b>@lang('Costs:')</b> @lang('Already paid')</p>
                                            <a id="js_tradeBtn" class="overlay tooltipHTML js_hideTipOnMobile btn btn_confirm"
                                               data-overlay-class="traderlayer"
                                               href="{{ $activeMerchant ? route('merchant.market', ['type' => $activeMerchant['type']]) . '?overlay=1' : '#' }}"
                                               data-tooltip-title="@lang('Trade|Trade your resources at the agreed price')">
                                                @lang('trade')
                                            </a>
                                        </div>
                                        <div id="js_callMerchantSection" class="getNewTraderDiv {{ $activeMerchant ? 'hidden' : '' }}">
                                            <p class="cost_txt">@lang('Costs:') <span class="premium_txt">{{ number_format($merchantCost) }}</span> @lang('Dark Matter') (@lang('per call'))</p>
                                            <div class="btn_calltrader_wrap">
                                                <a class="btn_premium btn_calltrader" id="js_callMerchantBtn" disabled="disabled">@lang('Call merchant')</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- <div class="big_tabs content"> END -->
                    <div id="boxFooter" class="footer"></div>
                </div>  <!-- <div id="div_traderResources"> END -->

            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {
            var selectedResource = {!! $activeMerchant ? "'" . $activeMerchant['type'] . "'" : 'null' !!};
            var activeMerchantType = {!! $activeMerchant ? "'" . $activeMerchant['type'] . "'" : 'null' !!};

            // Handle resource selection
            $('.js_selectResource').click(function(e) {
                e.preventDefault();

                // Store selected resource type
                selectedResource = $(this).data('resource-type');

                // If clicking a different resource than the active merchant
                if (activeMerchantType && selectedResource !== activeMerchantType) {
                    // Remove active class from all resources
                    $('.js_selectResource').removeClass('active');

                    // Add oldTraderActive to the paid merchant
                    $('.js_selectResource[data-resource-type="' + activeMerchantType + '"]').addClass('oldTraderActive');

                    // Add active class to clicked resource
                    $(this).addClass('active');

                    // Hide "Already paid" section, show "Call merchant" section
                    $('#js_alreadyPaidSection').addClass('hidden');
                    $('#js_callMerchantSection').removeClass('hidden');

                    // Enable the call merchant button
                    var callBtn = $('#js_callMerchantBtn');
                    if ({!! $darkMatter !!} >= {!! $merchantCost !!}) {
                        callBtn.removeAttr('disabled');
                        callBtn.css('cursor', 'pointer');
                    } else {
                        callBtn.attr('disabled', 'disabled');
                        callBtn.css('cursor', 'not-allowed');
                    }
                } else if (activeMerchantType && selectedResource === activeMerchantType) {
                    // Clicking the active merchant resource - show "Trade" button
                    // Remove oldTraderActive class from all
                    $('.js_selectResource').removeClass('oldTraderActive');

                    // Remove active from all, add active to this
                    $('.js_selectResource').removeClass('active');
                    $(this).addClass('active');

                    // Show trade button
                    $('#js_callMerchantSection').addClass('hidden');
                    $('#js_alreadyPaidSection').removeClass('hidden');
                } else {
                    // No active merchant - clicking any resource
                    // Remove active class from all resources
                    $('.js_selectResource').removeClass('active');

                    // Add active class to clicked resource
                    $(this).addClass('active');

                    // Show "Call merchant" button
                    $('#js_callMerchantSection').removeClass('hidden');
                    $('#js_alreadyPaidSection').addClass('hidden');

                    // Enable the call merchant button
                    var callBtn = $('#js_callMerchantBtn');
                    if ({!! $darkMatter !!} >= {!! $merchantCost !!}) {
                        callBtn.removeAttr('disabled');
                        callBtn.css('cursor', 'pointer');
                    } else {
                        callBtn.attr('disabled', 'disabled');
                        callBtn.css('cursor', 'not-allowed');
                    }
                }
            });

            // Handle call merchant button click
            $('#js_callMerchantBtn').click(function(e) {
                e.preventDefault();

                if ($(this).attr('disabled') || !selectedResource) {
                    return false;
                }

                @if($darkMatter < $merchantCost)
                    errorBoxNotify(LocalizationStrings.error, '@lang("Insufficient dark matter. You need :cost dark matter to call a merchant.", ["cost" => number_format($merchantCost)])');
                    return false;
                @endif

                var button = $(this);
                var originalText = button.text();
                button.attr('disabled', 'disabled').text('@lang("Calling merchant...")');

                // Call the merchant via AJAX
                $.ajax({
                    url: '{{ route('merchant.call') }}',
                    type: 'POST',
                    data: {
                        type: selectedResource,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Merchant called successfully, update UI to show "Already paid" state
                            var tradeUrl = '{{ route('merchant.market', ['type' => '__TYPE__']) }}'.replace('__TYPE__', selectedResource) + '?overlay=1';

                            // Update the active merchant type
                            activeMerchantType = selectedResource;

                            // Remove oldTraderActive from all resources
                            $('.js_selectResource').removeClass('oldTraderActive');

                            // Remove active from all, add to selected resource
                            $('.js_selectResource').removeClass('active');
                            $('.js_selectResource[data-resource-type="' + selectedResource + '"]').addClass('active');

                            // Hide the "Call merchant" section
                            $('#js_callMerchantSection').addClass('hidden');

                            // Update and show the "Already paid" section
                            $('#js_tradeBtn').attr('href', tradeUrl);
                            $('#js_alreadyPaidSection').removeClass('hidden');

                            // Update the trade link in the planet div
                            if ($('#slot01').length === 0) {
                                var headerTradeLink = '<div id="slot01" class="slot"><a href="' + tradeUrl + '" class="overlay tooltipHTML js_hideTipOnMobile" data-overlay-class="traderlayer" data-tooltip-title="@lang('Trade|Trade your resources at the agreed price')">@lang('trade')</a></div>';
                                $('#planet').append(headerTradeLink);
                            } else {
                                // Update existing trade link
                                $('#slot01 a').attr('href', tradeUrl);
                            }

                            // Reset button text back to original
                            button.text(originalText);

                            // Automatically open the trade overlay
                            var $tempLink = $('<a class="overlay" data-overlay-class="traderlayer" href="' + tradeUrl + '"></a>');
                            $tempLink.appendTo('body');
                            $tempLink.click();
                            $tempLink.remove();
                        } else {
                            errorBoxNotify(LocalizationStrings.error, response.message || '@lang("Failed to call merchant.")');
                            button.removeAttr('disabled').text(originalText);
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '@lang("An error occurred. Please try again.")');
                        button.removeAttr('disabled').text(originalText);
                    }
                });
            });
        });
    </script>

@endsection
