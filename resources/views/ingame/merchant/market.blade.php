@php
    $isOverlay = request()->has('overlay') || request()->ajax();
@endphp

@if(!$isOverlay)
@extends('ingame.layouts.main')

@section('content')
@endif

@if($isOverlay)
    @if($activeMerchant)
        <form id="TraderForm" action="javascript:void(0);" onsubmit="trySubmit();">
            <table id="merchanttable" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="freeStorage">{{ __('t_merchant.free_storage_capacity') }}</td>
                                    <td class="tradingRate">{{ __('t_merchant.exchange_rate') }}</td>
                                </tr>

                                @php
                                    $resources = ['metal' => 1, 'crystal' => 2, 'deuterium' => 3];
                                    $resourceNames = ['metal' => 'Metal', 'crystal' => 'Crystal', 'deuterium' => 'Deuterium'];
                                    $rowIndex = 0;
                                @endphp

                                @foreach($resources as $resourceKey => $resourceId)
                                    @php
                                        $isSelling = ($resourceKey === $merchantType);
                                        $rowClass = ($rowIndex % 2 === 0) ? 'alt' : '';
                                        $rowIndex++;

                                        // Base rates: Metal=3, Crystal=2, Deuterium=1
                                        $baseRate = match($merchantType) {
                                            'metal' => 3,
                                            'crystal' => 2,
                                            'deuterium' => 1,
                                            default => 1
                                        };

                                        // Get current planet resources and storage
                                        $currentAmount = match($resourceKey) {
                                            'metal' => $planet->metal()->get(),
                                            'crystal' => $planet->crystal()->get(),
                                            'deuterium' => $planet->deuterium()->get(),
                                            default => 0
                                        };
                                        $storageCapacity = match($resourceKey) {
                                            'metal' => $planet->metalStorage()->get(),
                                            'crystal' => $planet->crystalStorage()->get(),
                                            'deuterium' => $planet->deuteriumStorage()->get(),
                                            default => 0
                                        };
                                        $freeStorageAmount = max(0, $storageCapacity - $currentAmount);
                                    @endphp

                                    <tr class="{{ $rowClass }} {{ $isSelling ? 'toSell' : '' }}">
                                        <td class="resIcon noCenter">
                                            <div class="resourceIcon {{ $resourceKey }}"></div>
                                        </td>
                                        <td class="noCenter">{{ __('t_merchant.' . $resourceKey) }}</td>

                                        @if($isSelling)
                                            <td id="toSell">
                                                <span id="{{ $resourceId }}_value_label">{{ number_format($currentAmount, 0, '.', ',') }}</span>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>{{ __('t_merchant.being_sold') }}</td>
                                            <td class="rate">
                                                @php
                                                    // Base trade rates: metal=3, crystal=2, deuterium=1
                                                    $baseRate = match($merchantType) {
                                                        'metal' => 3,
                                                        'crystal' => 2,
                                                        'deuterium' => 1,
                                                        default => 1
                                                    };
                                                @endphp
                                                <span class="tooltipHTML tooltipRight" data-tooltip-title="{{ __('t_merchant.get_new_exchange_rate') }}">
                                                    {{ $baseRate }}
                                                </span>
                                            </td>
                                            <input type="hidden" name="{{ $resourceId }}_value" id="{{ $resourceId }}_value" value="0">
                                        @else
                                            <td>
                                                <input type="text" pattern="[0-9,.]*" tabindex="{{ $resourceId }}" class="textinput" size="11"
                                                       name="{{ $resourceId }}_value" id="{{ $resourceId }}_value" value="0"
                                                       onkeyup="checkValue({{ $resourceId }})" onchange="checkValue({{ $resourceId }})">
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="setMaxValue({{ $resourceId }}); return false;"
                                                   class="tooltip js_hideTipOnMobile setMaxValue" data-tooltip-title="{{ __('t_merchant.exchange_maximum_amount') }}">
                                                </a>
                                            </td>
                                            <td><span id="{{ $resourceId }}_storage">{{ number_format($freeStorageAmount, 0, '.', ',') }}</span></td>
                                            <td class="rate tooltipHTML tooltipRight" data-tooltip-title="{{ __('t_merchant.get_new_exchange_rate') }}">
                                                <span class="undermark">{{ $activeMerchant['trade_rates']['receive'][$resourceKey]['rate'] }}</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="6" style="padding:10px">
                                        <span>{{ __('t_merchant.trader_delivery_notice') }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" rowspan="2">
                                        <input type="button" tabindex="3" name="tradebutton" class="btn_blue"
                                               value="{{ __('t_merchant.trade_resources') }}" onclick="trySubmit();">
                                    </td>
                                    <td colspan="3" class="newRate">
                                        <a href="javascript:void(0);" tabindex="4" name="tradebuttonRate"
                                           class="buttonTraderNewRate" data-merchant-type="{{ $merchantType }}">
                                            {{ __('t_merchant.new_exchange_rate') }}
                                        </a>
                                        {{ __('t_merchant.costs') }}
                                        3,500 {{ __('t_merchant.dark_matter') }}
                                    </td>
                                </tr>
                </table>
            </form>
    @else
        <div style="text-align: center; padding: 30px;">
            <p style="color: #6f9fc0;">{{ __('t_merchant.no_merchant_available') }}</p>
            <p style="color: #999;">{{ __('t_merchant.please_call_merchant') }}</p>
        </div>
    @endif

@else
    <!-- Full Page View -->
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
                        <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile right" href="{{ route('merchant.resource-market') }}" data-tooltip-title="{{ __('t_merchant.back') }}" style="display: inline;"></a>
                        <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('merchant.resource-market') }}" data-tooltip-title="{{ __('t_merchant.back') }}"></a>
                    </div>
                </div>
                <div class="c-left c-small" style=""></div>
                <div class="c-right c-small" style=""></div>

                @if($activeMerchant)
                    <!-- Active Merchant Trade Interface -->
                    <div id="merchant" style="margin: 20px auto; max-width: 800px;">
                        <form id="TraderForm" action="javascript:void(0);" onsubmit="trySubmit();">
                            <table id="merchanttable" cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="freeStorage">{{ __('t_merchant.free_storage_capacity') }}</td>
                                    <td class="tradingRate">{{ __('t_merchant.exchange_rate') }}</td>
                                </tr>

                                @php
                                    $resources = ['metal' => 1, 'crystal' => 2, 'deuterium' => 3];
                                    $resourceNames = ['metal' => 'Metal', 'crystal' => 'Crystal', 'deuterium' => 'Deuterium'];
                                    $rowIndex = 0;
                                @endphp

                                @foreach($resources as $resourceKey => $resourceId)
                                    @php
                                        $isSelling = ($resourceKey === $merchantType);
                                        $rowClass = ($rowIndex % 2 === 0) ? 'alt' : '';
                                        $rowIndex++;

                                        // Base rates: Metal=3, Crystal=2, Deuterium=1 (defined once, used in both branches)
                                        $baseRate = match($merchantType) {
                                            'metal' => 3,
                                            'crystal' => 2,
                                            'deuterium' => 1,
                                            default => 1
                                        };

                                        // Get current planet resources and storage
                                        $currentAmount = match($resourceKey) {
                                            'metal' => $planet->metal()->get(),
                                            'crystal' => $planet->crystal()->get(),
                                            'deuterium' => $planet->deuterium()->get(),
                                            default => 0
                                        };
                                        $storageCapacity = match($resourceKey) {
                                            'metal' => $planet->metalStorage()->get(),
                                            'crystal' => $planet->crystalStorage()->get(),
                                            'deuterium' => $planet->deuteriumStorage()->get(),
                                            default => 0
                                        };
                                        $freeStorageAmount = max(0, $storageCapacity - $currentAmount);
                                    @endphp

                                    <tr class="{{ $rowClass }} {{ $isSelling ? 'toSell' : '' }}">
                                        <td class="resIcon noCenter">
                                            <div class="resourceIcon {{ $resourceKey }}"></div>
                                        </td>
                                        <td class="noCenter">{{ __('t_merchant.' . $resourceKey) }}</td>

                                        @if($isSelling)
                                            <td id="toSell">
                                                <span id="{{ $resourceId }}_value_label">{{ number_format($currentAmount, 0, '.', ',') }}</span>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>{{ __('t_merchant.being_sold') }}</td>
                                            <td class="rate">
                                                @php
                                                    // Base trade rates: metal=3, crystal=2, deuterium=1
                                                    $baseRate = match($merchantType) {
                                                        'metal' => 3,
                                                        'crystal' => 2,
                                                        'deuterium' => 1,
                                                        default => 1
                                                    };
                                                @endphp
                                                <span class="tooltipHTML tooltipRight" data-tooltip-title="{{ __('t_merchant.get_new_exchange_rate') }}">
                                                    {{ $baseRate }}
                                                </span>
                                            </td>
                                            <input type="hidden" name="{{ $resourceId }}_value" id="{{ $resourceId }}_value" value="0">
                                        @else
                                            <td>
                                                <input type="text" pattern="[0-9,.]*" tabindex="{{ $resourceId }}" class="textinput" size="11"
                                                       name="{{ $resourceId }}_value" id="{{ $resourceId }}_value" value="0"
                                                       onkeyup="checkValue({{ $resourceId }})" onchange="checkValue({{ $resourceId }})">
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" onclick="setMaxValue({{ $resourceId }}); return false;"
                                                   class="tooltip js_hideTipOnMobile setMaxValue" data-tooltip-title="{{ __('t_merchant.exchange_maximum_amount') }}">
                                                </a>
                                            </td>
                                            <td><span id="{{ $resourceId }}_storage">{{ number_format($freeStorageAmount, 0, '.', ',') }}</span></td>
                                            <td class="rate tooltipHTML tooltipRight" data-tooltip-title="{{ __('t_merchant.get_new_exchange_rate') }}">
                                                <span class="undermark">{{ $activeMerchant['trade_rates']['receive'][$resourceKey]['rate'] }}</span>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach

                                <tr>
                                    <td colspan="6" style="padding:10px">
                                        <span>{{ __('t_merchant.trader_delivery_notice') }}</span>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" rowspan="2">
                                        <input type="button" tabindex="3" name="tradebutton" class="btn_blue"
                                               value="{{ __('t_merchant.trade_resources') }}" onclick="trySubmit();">
                                    </td>
                                    <td colspan="3" class="newRate">
                                        <a href="javascript:void(0);" tabindex="4" name="tradebuttonRate"
                                           class="buttonTraderNewRate" data-merchant-type="{{ $merchantType }}">
                                            {{ __('t_merchant.new_exchange_rate') }}
                                        </a>
                                        {{ __('t_merchant.costs') }}
                                        3,500 {{ __('t_merchant.dark_matter') }}
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                @else
                    <!-- No Active Merchant -->
                    <div style="text-align: center; padding: 50px;">
                        <h2>{{ __('t_merchant.no_merchant_available_h2') }}</h2>
                        <p>{{ __('t_merchant.please_call_merchant') }}</p>
                        <a href="{{ route('merchant.resource-market') }}" class="btn_blue">
                            {{ __('t_merchant.back_to_resource_market') }}
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endif

    @if($activeMerchant)
    <script type="text/javascript">
        var freeStorage = {
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @php
                    $currentAmount = match($resourceKey) {
                        'metal' => $planet->metal()->get(),
                        'crystal' => $planet->crystal()->get(),
                        'deuterium' => $planet->deuterium()->get(),
                        default => 0
                    };
                    $storageCapacity = match($resourceKey) {
                        'metal' => $planet->metalStorage()->get(),
                        'crystal' => $planet->crystalStorage()->get(),
                        'deuterium' => $planet->deuteriumStorage()->get(),
                        default => 0
                    };
                    $freeStorageAmount = max(0, $storageCapacity - $currentAmount);
                @endphp
                "{{ $resourceId }}": {{ $freeStorageAmount }},
            @endforeach
        };

        var factor = {
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @if($resourceKey === $merchantType)
                    {{-- Base rates: Metal=3, Crystal=2, Deuterium=1 --}}
                    "{{ $resourceId }}": {{ match($merchantType) { 'metal' => '3.00', 'crystal' => '2.00', 'deuterium' => '1.00', default => '1.00' } }},
                @else
                    "{{ $resourceId }}": {{ $activeMerchant['trade_rates']['receive'][$resourceKey]['rate'] }},
                @endif
            @endforeach
        };

        var offer_id = "{{ $merchantType }}";
        @php
            $offerAmount = match($merchantType) {
                'metal' => $planet->metal()->get(),
                'crystal' => $planet->crystal()->get(),
                'deuterium' => $planet->deuterium()->get(),
                default => 0
            };
        @endphp
        var offer_amount = {{ $offerAmount }};
        var currentResources = {
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @php
                    $currentAmount = match($resourceKey) {
                        'metal' => $planet->metal()->get(),
                        'crystal' => $planet->crystal()->get(),
                        'deuterium' => $planet->deuterium()->get(),
                        default => 0
                    };
                @endphp
                "{{ $resourceId }}": {{ $currentAmount }},
            @endforeach
        };
        var token = "{{ csrf_token() }}";
        var merchantType = "{{ $merchantType }}";

        // Store current planet ID to detect switches
        var currentPlanetId = {{ $planet->getPlanetId() }};

        $(function() {
            $('.buttonTraderNewRate').on('click', callTrader);
            initTooltips();

            // Initialize resource input fields
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @if($resourceKey !== $merchantType)
                    $('#{{ $resourceId }}_value').val('0');
                @endif
            @endforeach

            // Initialize selling resource display
            var giveResourceId = {{ $resources[$merchantType] }};
            $('#' + giveResourceId + '_value_label').text('0');


            // Set dialog title and configuration for overlay
            @if($isOverlay)
            var merchantTitle = @json(__('t_merchant.trader_buying') . ' ' . ucfirst($merchantType) . '.');
            if (typeof $('.overlayDiv.traderlayer').dialog === 'function') {
                $('.overlayDiv.traderlayer').dialog('option', 'title', merchantTitle);
                $('.ui-widget-overlay').css({
                    'opacity': '1',
                    'background': '#000'
                });
            }
            @endif

            // Detect planet switches and reload overlay
            @if($isOverlay)
            setInterval(function() {
                // Check if current planet selector exists and has changed
                var newPlanetId = null;
                if (typeof window.parent !== 'undefined' && window.parent.document) {
                    var planetLink = window.parent.document.querySelector('.smallplanet.active a');
                    if (planetLink && planetLink.href) {
                        var match = planetLink.href.match(/[?&]cp=(\d+)/);
                        if (match) {
                            newPlanetId = parseInt(match[1]);
                        }
                    }
                }

                // If planet changed, reload the overlay with current planet's data
                if (newPlanetId && newPlanetId !== currentPlanetId) {
                    var merchantUrl = '{{ route('merchant.market', ['type' => $merchantType]) }}?overlay=1';
                    if (window.parent && window.parent.location) {
                        window.parent.location.href = merchantUrl;
                    } else {
                        window.location.reload();
                    }
                }
            }, 1000); // Check every second
            @endif
        });

        function checkValue(resourceId) {
            var input = document.getElementById(resourceId + '_value');

            // Format input with thousand separators first
            formatNumber(input, $(input).val());

            // Get the numeric value for calculations
            var value = parseInt($(input).val().replace(/,/g, '')) || 0;

            // Calculate how much of the selling resource is needed
            var giveResourceId = {{ $resources[$merchantType] }};
            var giveRate = factor[giveResourceId];
            var receiveRate = factor[resourceId];

            var neededAmount = Math.ceil(value * (giveRate / receiveRate));

            // Check if we have enough of the selling resource (use current amount, not stale page load amount)
            var currentOfferAmount = currentResources[giveResourceId];
            if (neededAmount > currentOfferAmount) {
                value = Math.floor(currentOfferAmount * (receiveRate / giveRate));
                neededAmount = Math.ceil(value * (giveRate / receiveRate));
                formatNumber(input, value);
            }

            // Check if it fits in storage
            var maxStorage = freeStorage[resourceId];
            if (value > maxStorage) {
                value = maxStorage;
                neededAmount = Math.ceil(value * (giveRate / receiveRate));
                formatNumber(input, value);
            }

            // Calculate total amount needed from selling resource
            var totalNeeded = 0;
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @if($resourceKey !== $merchantType)
                    var val{{ $resourceId }} = parseInt($('#{{ $resourceId }}_value').val().replace(/,/g, '')) || 0;
                    if (val{{ $resourceId }} > 0) {
                        var rate{{ $resourceId }} = factor[{{ $resourceId }}];
                        totalNeeded += Math.ceil(val{{ $resourceId }} * (giveRate / rate{{ $resourceId }}));
                    }
                @endif
            @endforeach

            // Update the selling resource display
            $('#' + giveResourceId + '_value_label').text(number_format(totalNeeded, 0));
        }

        function setMaxValue(resourceId) {
            var giveResourceId = {{ $resources[$merchantType] }};
            var giveRate = factor[giveResourceId];
            var receiveRate = factor[resourceId];

            // Use current resource amount (available at page load)
            var currentOfferAmount = currentResources[giveResourceId];

            // Calculate max based on available selling resource
            var maxFromAvailable = Math.floor(currentOfferAmount * (receiveRate / giveRate));

            // Calculate max based on storage capacity
            var maxFromStorage = freeStorage[resourceId];

            // Use the smaller of the two
            var maxValue = Math.min(maxFromAvailable, maxFromStorage);

            $('#' + resourceId + '_value').val(maxValue);
            checkValue(resourceId);
        }

        function trySubmit() {
            var formData = {
                _token: token,
                give_resource: merchantType,
                receive_resource: null,
                give_amount: 0
            };

            // Find which resource is being received
            // Note: exchange_rate is calculated server-side from cached merchant data to prevent spoofing
            @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
                @if($resourceKey !== $merchantType)
                    var value{{ $resourceId }} = parseInt($('#{{ $resourceId }}_value').val().replace(/,/g, '')) || 0;
                    if (value{{ $resourceId }} > 0) {
                        formData.receive_resource = '{{ $resourceKey }}';
                        var giveRate = factor[{{ $resources[$merchantType] }}];
                        var receiveRate = factor[{{ $resourceId }}];
                        formData.give_amount = Math.ceil(value{{ $resourceId }} * (giveRate / receiveRate));
                    }
                @endif
            @endforeach

            if (!formData.receive_resource || formData.give_amount === 0) {
                errorBoxNotify(LocalizationStrings.error, @json(__('t_merchant.please_select_resource')));
                return false;
            }

            if (formData.give_amount > offer_amount) {
                errorBoxNotify(LocalizationStrings.error, @json(__('t_merchant.not_enough_resources')));
                return false;
            }

            // Submit the trade
            $.post('{{ route('merchant.trade') }}', formData, function(response) {
                if (response.success) {
                    fadeBox(response.message || @json(__('t_merchant.trade_completed_success')), false);
                    setTimeout(function() {
                        // Reload the page to show updated resources
                        window.location.reload();
                    }, 1000);
                } else {
                    errorBoxNotify(LocalizationStrings.error, response.message || @json(__('t_merchant.trade_failed')));
                }
            }).fail(function(xhr) {
                var response = xhr.responseJSON;
                errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : @json(__('t_merchant.error_retry')));
            });
        }

        function callTrader() {
            errorBoxDecision(
                @json(__('t_ingame.shared.caution')),
                @json(__('t_merchant.new_rate_confirmation')),
                LocalizationStrings.yes,
                LocalizationStrings.no,
                function() {
                    $.post('{{ route('merchant.call') }}', {
                        _token: token,
                        type: merchantType
                    }, function(response) {
                        if (response.success) {
                            fadeBox(@json(__('t_merchant.merchant_called_success')), false);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            errorBoxNotify(LocalizationStrings.error, response.message || @json(__('t_merchant.failed_to_call')));
                        }
                    }).fail(function(xhr) {
                        var response = xhr.responseJSON;
                        errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : @json(__('t_merchant.error_retry')));
                    });
                }
            );
        }
    </script>
    @endif

@if(!$isOverlay)
@endsection
@endif
