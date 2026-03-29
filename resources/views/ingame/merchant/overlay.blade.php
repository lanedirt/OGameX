<div id="merchant">
    <div style="margin:0px auto;">
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
                                    // Base trade rates: metal=3.00, crystal=2.00, deuterium=1.00
                                    // The resource being sold always has its maximum value
                                    $baseRate = match($merchantType) {
                                        'metal' => 3.00,
                                        'crystal' => 2.00,
                                        'deuterium' => 1.00,
                                        default => 1.00
                                    };
                                @endphp
                                <span class="tooltipHTML tooltipRight" data-tooltip-title="{{ __('t_merchant.get_new_exchange_rate') }}">
                                    {{ number_format($baseRate, 2, '.', '') }}
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
                                <span class="undermark">{{ number_format($activeMerchant['trade_rates']['receive'][$resourceKey]['rate'], 2, '.', '') }}</span>
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
</div>

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
                $freeStorageAmount = max(0, floor($storageCapacity - $currentAmount));
            @endphp
            "{{ $resourceId }}": {{ (int)$freeStorageAmount }},
        @endforeach
    };

    var baseFactor = {
        1: 3.00, // Metal
        2: 2.00, // Crystal
        3: 1.00 // Deuterium
    };

    var tradeFactor = {
        @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
            @if($resourceKey === $merchantType)
                "{{ $resourceId }}": 1.0,
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
    var offer_amount = Math.floor({{ $offerAmount }});
    var token = "{{ csrf_token() }}";
    var merchantType = "{{ $merchantType }}";

    // Store current planet ID to detect switches
    var currentPlanetId = {{ $planet->getPlanetId() }};

    $(function() {
        $('.buttonTraderNewRate').on('click', callTrader);
        initTooltips();

        // Set dialog title
        var merchantTitle = @json(__('t_merchant.trader_buying') . ' ' . ucfirst($merchantType) . '.');
        if (typeof $('.overlayDiv.traderlayer').dialog === 'function') {
            $('.overlayDiv.traderlayer').dialog('option', 'title', merchantTitle);
        }

        // Detect planet switches and reload overlay
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
    });

    function checkValue(resourceId) {
        var input = document.getElementById(resourceId + '_value');

        // Format input with thousand separators as user types
        formatNumber(input, $(input).val());

        // Get the numeric value for calculations
        var value = parseInt($(input).val().replace(/[,\.]/g, '')) || 0;

        // Calculate how much of the selling resource is needed
        var giveResourceId = {{ $resources[$merchantType] }};
        var giveRate = baseFactor[giveResourceId];
        var receiveRate = tradeFactor[resourceId];

        var neededAmount = Math.ceil(value * (giveRate / receiveRate));

        // Check if we have enough of the selling resource
        if (neededAmount > offer_amount) {
            value = Math.floor(offer_amount * (receiveRate / giveRate));
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
                var val{{ $resourceId }} = parseInt($('#{{ $resourceId }}_value').val().replace(/[,\.]/g, '')) || 0;
                if (val{{ $resourceId }} > 0) {
                    var rate{{ $resourceId }} = tradeFactor[{{ $resourceId }}];
                    totalNeeded += Math.ceil(val{{ $resourceId }} * (giveRate / rate{{ $resourceId }}));
                }
            @endif
        @endforeach

        // Update the selling resource display
        $('#' + giveResourceId + '_value_label').text(number_format(totalNeeded, 0));
    }

    function setMaxValue(resourceId) {
        var giveResourceId = {{ $resources[$merchantType] }};
        var giveRate = baseFactor[giveResourceId];
        var receiveRate = tradeFactor[resourceId];

        // Calculate max based on storage capacity first (ensure integer)
        var maxFromStorage = Math.floor(freeStorage[resourceId]);

        // Calculate max based on available selling resource
        // We need to ensure that Math.ceil(maxReceive * (giveRate / receiveRate)) <= offer_amount
        // So we calculate: maxReceive = Math.floor(offer_amount * (receiveRate / giveRate))
        // But then verify it doesn't exceed what we have when converted back
        var maxFromAvailable = Math.floor(offer_amount * (receiveRate / giveRate));

        // Double-check by calculating how much we'd actually need
        var wouldNeed = Math.ceil(maxFromAvailable * (giveRate / receiveRate));
        while (wouldNeed > offer_amount && maxFromAvailable > 0) {
            maxFromAvailable--;
            wouldNeed = Math.ceil(maxFromAvailable * (giveRate / receiveRate));
        }

        // Use the smaller of the two (both are integers now)
        var maxValue = Math.floor(Math.min(maxFromAvailable, maxFromStorage));

        $('#' + resourceId + '_value').val(maxValue);
        checkValue(resourceId);
    }

    function trySubmit() {
        var giveResourceId = {{ $resources[$merchantType] }};
        var giveRate = baseFactor[giveResourceId];
        var receiveResources = {};
        var totalGiveAmount = 0;

        // Collect all receive resources and calculate total give cost
        @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
            @if($resourceKey !== $merchantType)
                var value{{ $resourceId }} = parseInt($('#{{ $resourceId }}_value').val().replace(/[,\.]/g, '')) || 0;
                if (value{{ $resourceId }} > 0) {
                    var receiveRate{{ $resourceId }} = tradeFactor[{{ $resourceId }}];
                    receiveResources['{{ $resourceKey }}'] = value{{ $resourceId }};
                    totalGiveAmount += Math.ceil(value{{ $resourceId }} * (giveRate / receiveRate{{ $resourceId }}));
                }
            @endif
        @endforeach

        if (Object.keys(receiveResources).length === 0 || totalGiveAmount === 0) {
            errorBoxNotify(LocalizationStrings.error, @json(__('t_merchant.please_select_resource')));
            return false;
        }

        // Use a small tolerance (1 unit) for floating point comparison
        if (totalGiveAmount > offer_amount + 1) {
            errorBoxNotify(LocalizationStrings.error, @json(__('t_merchant.not_enough_resources')));
            return false;
        }

        // Ensure we don't try to give more than we have
        if (totalGiveAmount > offer_amount) {
            totalGiveAmount = offer_amount;
        }

        // Submit the trade with all receive resources
        $.post('{{ route('merchant.trade') }}', {
            _token: token,
            give_resource: merchantType,
            receive_resources: receiveResources,
            give_amount: totalGiveAmount,
        }, function(response) {
            if (response.success) {
                fadeBox(response.message || @json(__('t_merchant.trade_completed_success')), false);
                setTimeout(function() {
                    location.reload();
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
