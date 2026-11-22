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
                        <h2>{{ __('t_merchant.resource_market') }}</h2>
                        <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile right" href="{{ route('merchant.index') }}" data-tooltip-title="{{ __('t_merchant.back') }}" style="display: inline;"></a>
                        <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('merchant.index') }}" data-tooltip-title="{{ __('t_merchant.back') }}"></a>
                    </div>
                    @if($activeMerchant)
                        <div id="slot01" class="slot">
                            <a href="{{ route('merchant.market', ['type' => $activeMerchant['type']]) }}?overlay=1"
                               class="overlay tooltipHTML js_hideTipOnMobile"
                               data-overlay-class="traderlayer"
                               data-tooltip-title="{{ __('t_merchant.trade_tooltip') }}">
                                {{ __('t_merchant.trade') }}
                            </a>
                        </div>
                    @endif
                    <div id="js_traderResources" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderResources" data-tooltip-title="{{ __('t_merchant.exchange_resources_desc') }}" style="display: none;">
                        <h2>{{ __('t_merchant.resource_market') }}</h2>
                    </div>
                    <div id="js_traderAuctioneer" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderAuctioneer" data-tooltip-title="{{ __('t_merchant.auctioneer_desc') }}" style="display: none;">
                        <h2>{{ __('t_merchant.auctioneer') }}</h2>
                    </div>
                    <br>
                    <div id="js_traderScrap" class="js_trader trader_link tooltipLeft js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderScrap" data-tooltip-title="{{ __('t_merchant.scrap_merchant_desc') }}" style="display: none;">
                        <h2>{{ __('t_merchant.scrap_merchant') }}</h2>
                    </div>
                    <div id="js_traderImportExport" class="js_trader trader_link tooltipRight js_hideTipOnMobile ipiHintable" data-ipi-hint="ipiTraderImportExport" data-tooltip-title="{{ __('t_merchant.import_export_desc') }}" style="display: none;">
                        <h2>{{ __('t_merchant.import_export') }}</h2>
                    </div>
                </div>
                <div class="c-left c-small" style=""></div>
                <div class="c-right c-small" style=""></div>

                <!-- Trader Resources Start -->
                <div id="div_traderResources" class="div_trader" style="">

                    <div id="boxHeader" class="header">
                        <h2>{{ __('t_merchant.resource_market') }}</h2>
                    </div>

                    <div class="big_tabs content ui-tabs ui-corner-all ui-widget ui-widget-content">
                        <ul role="tablist" class="ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header">
                            {{-- TODO: Implement "Get more resources" tab - allows purchasing daily production with dark matter --}}
                            {{--
                            <li class="big_tab ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-buyResource" aria-labelledby="ui-id-7" aria-selected="true" aria-expanded="true">
                                <a href="#tabs-buyResource" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-7">
                                    @lang('Get more resources')
                                </a>
                            </li>
                            --}}
                            <li class="big_tab ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" role="tab" tabindex="0" aria-controls="tabs-changeResource" aria-labelledby="ui-id-8" aria-selected="true" aria-expanded="true">
                                <a href="#tabs-changeResource" class="ipiHintable ui-tabs-anchor" data-ipi-hint="ipiTraderResourcesExchange" role="presentation" tabindex="-1" id="ui-id-8">
                                    {{ __('t_merchant.exchange_resources') }}
                                </a>
                            </li>
                        </ul>

                        {{-- TODO: Implement "Get more resources" tab content --}}
                        {{-- Allows players to purchase up to one daily production of resources with dark matter --}}
                        {{-- Features: --}}
                        {{--   - Individual resource purchase (metal, crystal, deuterium) --}}
                        {{--   - All resources bundle purchase --}}
                        {{--   - Adjustable amounts (up to daily production) --}}
                        {{--   - Storage capacity validation --}}
                        {{--   - Dynamic dark matter cost calculation --}}
                        {{--   - Minimum 10,000 resources if daily production is lower --}}
                        {{-- Tab switching handled by JavaScript (lines 181-194) --}}
                        {{--
                        <div id="tabs-buyResource" class="big_tab_content ui-tabs-panel ui-corner-bottom ui-widget-content" aria-labelledby="ui-id-7" role="tabpanel" aria-hidden="false" style="">
                            <div class="teaser_txt">
                                <h2>@lang('Buy a daily production directly from the merchant')</h2>
                                <p>@lang('Here you can have the resource storage of your planets directly refilled by up to one daily production.')</p>
                            </div>

                            <div class="content_inner buy_resources productionBasedPackages" data-dark-matter="{!! $darkMatter !!}">
                                <div class="fill_resource">
                                    <p style="text-align: center; padding: 40px; color: #6f9fc8;">
                                        <strong>TODO: Implementation pending</strong><br><br>
                                        This feature will allow you to purchase resources with dark matter.<br>
                                        Features to implement:<br>
                                        - Individual resource purchase (metal, crystal, deuterium)<br>
                                        - All resources bundle purchase<br>
                                        - Adjustable amounts (up to daily production)<br>
                                        - Storage capacity validation<br>
                                        - Dynamic dark matter cost calculation<br>
                                        - Minimum 10,000 resources if daily production is lower
                                    </p>
                                </div>
                                <div class="clearfloat"></div>
                                <div class="roundBox hints">
                                    <h2>@lang('Notices:')</h2>
                                    <ul class="ListLinks">
                                        <li>@lang('You are offered a maximum of one complete daily production equal to the total production of all your planets by default.')</li>
                                        <li>@lang('If your daily production of a resource is less than 10000, you will be offered at least this amount.')</li>
                                        <li>@lang('You must have enough free storage capacity on the active planet or moon for the purchased resources. Otherwise the surplus resources are lost.')</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        --}}

                        <div id="tabs-changeResource" class="big_tab_content ui-tabs-panel ui-corner-bottom ui-widget-content" aria-labelledby="ui-id-8" role="tabpanel" aria-hidden="false" style="">

                            <div class="teaser_txt">
                                <h2>{{ __('t_merchant.exchange_your_resources') }}</h2>
                            </div>

                            <div class="clearfix content_inner">
                                <div class="call_trader_step1">
                                    <p class="step_info">{{ __('t_merchant.step_one_exchange') }}</p>
                                    <ul class="resource_list">
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link metal_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'metal' ? 'active oldTraderActive' : '' !!}" data-resource-id="1" data-resource-type="metal" data-tooltip-title="{{ __('t_merchant.sell_metal_tooltip') }}">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">{{ __('t_merchant.metal') }}</p>
                                            </a>
                                        </li>
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link crystal_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'crystal' ? 'active oldTraderActive' : '' !!}" data-resource-id="2" data-resource-type="crystal" data-tooltip-title="{{ __('t_merchant.sell_crystal_tooltip') }}">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">{{ __('t_merchant.crystal') }}</p>
                                            </a>
                                        </li>
                                        <li class="resource_elem">
                                            <a role="button" class="tooltipHTML resource_link deuterium_img js_selectResource {!! $activeMerchant && $activeMerchant['type'] === 'deuterium' ? 'active oldTraderActive' : '' !!}" data-resource-id="3" data-resource-type="deuterium" data-tooltip-title="{{ __('t_merchant.sell_deuterium_tooltip') }}">
                                                <div class="selected_premium"></div>
                                                <p class="res_txt">{{ __('t_merchant.deuterium') }}</p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="call_trader_step2">
                                    <p class="step_info">{{ __('t_merchant.step_two_call') }}</p>
                                    <div class="step_arrow"></div>
                                    <div class="call_trader_box ipiHintable" data-ipi-hint="ipiTraderResourcesCall">
                                        <div id="js_alreadyPaidSection" class="{{ $activeMerchant ? '' : 'hidden' }}">
                                            <p class="cost_txt"><b>{{ __('t_merchant.costs') }}</b> {{ __('t_merchant.already_paid') }}</p>
                                            <a id="js_tradeBtn" class="overlay tooltipHTML js_hideTipOnMobile btn btn_confirm"
                                               data-overlay-class="traderlayer"
                                               href="{{ $activeMerchant ? route('merchant.market', ['type' => $activeMerchant['type']]) . '?overlay=1' : '#' }}"
                                               data-tooltip-title="{{ __('t_merchant.trade_tooltip') }}">
                                                {{ __('t_merchant.trade') }}
                                            </a>
                                        </div>
                                        <div id="js_callMerchantSection" class="getNewTraderDiv {{ $activeMerchant ? 'hidden' : '' }}">
                                            <p class="cost_txt">{{ __('t_merchant.costs') }} <span class="premium_txt">{{ number_format($merchantCost) }}</span> {{ __('t_merchant.dark_matter') }} ({{ __('t_merchant.per_call') }})</p>
                                            <div class="btn_calltrader_wrap">
                                                <a class="btn_premium btn_calltrader" id="js_callMerchantBtn" disabled="disabled">{{ __('t_merchant.call_merchant') }}</a>
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

            // When clicking the trade button, immediately change highlight from blue to green
            $(document).on('click', '#js_tradeBtn', function() {
                if (activeMerchantType) {
                    setTimeout(function() {
                        $('.js_selectResource').removeClass('active');
                        $('.js_selectResource[data-resource-type="' + activeMerchantType + '"]').addClass('oldTraderActive');
                    }, 50);
                }
            });

            // Handle tab switching
            $('.big_tabs .ui-tabs-nav a').click(function(e) {
                e.preventDefault();

                var targetTab = $(this).attr('href');

                // Update tab navigation
                $('.big_tabs .ui-tabs-nav li').removeClass('ui-tabs-active ui-state-active').addClass('ui-state-default');
                $(this).closest('li').addClass('ui-tabs-active ui-state-active').removeClass('ui-state-default');

                // Update tab panels
                $('.big_tab_content').hide().attr('aria-hidden', 'true');
                $(targetTab).show().attr('aria-hidden', 'false');
            });

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
                    errorBoxNotify(LocalizationStrings.error, @json(__('t_merchant.insufficient_dm_call', ['cost' => number_format($merchantCost)])));
                    return false;
                @endif

                var button = $(this);
                var originalText = button.text();
                button.attr('disabled', 'disabled').text(@json(__('t_merchant.calling_merchant')));

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
                                var headerTradeLink = '<div id="slot01" class="slot"><a href="' + tradeUrl + '" class="overlay tooltipHTML js_hideTipOnMobile" data-overlay-class="traderlayer" data-tooltip-title="{{ __('t_merchant.trade_tooltip') }}">{{ __('t_merchant.trade') }}</a></div>';
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

                            // Immediately change the resource highlight from blue (active) to green (oldTraderActive)
                            setTimeout(function() {
                                $('.js_selectResource').removeClass('active');
                                $('.js_selectResource[data-resource-type="' + selectedResource + '"]').addClass('oldTraderActive');
                            }, 50);
                        } else {
                            errorBoxNotify(LocalizationStrings.error, response.message || @json(__('t_merchant.failed_to_call')));
                            button.removeAttr('disabled').text(originalText);
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : @json(__('t_merchant.error_retry')));
                        button.removeAttr('disabled').text(originalText);
                    }
                });
            });
        });
    </script>

@endsection
