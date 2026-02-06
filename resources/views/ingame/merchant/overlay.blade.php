<div id="merchant">
    <div style="margin:0px auto;">
        <form id="TraderForm" action="javascript:void(0);" onsubmit="trySubmit();">
            <table id="merchanttable" cellpadding="0" cellspacing="0">
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="freeStorage">@lang('Free storage capacity')</td>
                    <td class="tradingRate">@lang('Exchange rate')</td>
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
                        <td class="noCenter">@lang($resourceNames[$resourceKey])</td>

                        @if($isSelling)
                            <td id="toSell">
                                <span id="{{ $resourceId }}_value_label">{{ number_format($currentAmount, 0, '.', ',') }}</span>
                            </td>
                            <td>&nbsp;</td>
                            <td>@lang('Being sold')</td>
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
                                <span class="tooltipHTML tooltipRight" data-tooltip-title="@lang('Get new exchange rate!')">
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
                                   class="tooltip js_hideTipOnMobile setMaxValue" data-tooltip-title="@lang('Exchange maximum amount')">
                                </a>
                            </td>
                            <td><span id="{{ $resourceId }}_storage">{{ number_format($freeStorageAmount, 0, '.', ',') }}</span></td>
                            <td class="rate tooltipHTML tooltipRight" data-tooltip-title="@lang('Get new exchange rate!')">
                                <span class="undermark">{{ number_format($activeMerchant['trade_rates']['receive'][$resourceKey]['rate'], 2, '.', '') }}</span>
                            </td>
                        @endif
                    </tr>
                @endforeach

                <tr>
                    <td colspan="6" style="padding:10px">
                        <span>@lang('A trader only delivers as much resources as there is free storage capacity.')</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" rowspan="2">
                        <input type="button" tabindex="3" name="tradebutton" class="btn_blue"
                               value="@lang('Trade resources!')" onclick="trySubmit();">
                    </td>
                    <td colspan="3" class="newRate">
                        <a href="javascript:void(0);" tabindex="4" name="tradebuttonRate"
                           class="buttonTraderNewRate" data-merchant-type="{{ $merchantType }}">
                            @lang('New exchange rate')
                        </a>
                        @lang('Costs:')
                        3,500 @lang('Dark Matter')
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
        var merchantTitle = '@lang("There is a trader here buying")' + ' ' + '{{ ucfirst($merchantType) }}' + '.';
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
        var formData = {
            _token: token,
            give_resource: merchantType,
            receive_resource: null,
            give_amount: 0,
            exchange_rate: 0
        };

        // Find which resource is being received
        @foreach(['metal' => 1, 'crystal' => 2, 'deuterium' => 3] as $resourceKey => $resourceId)
            @if($resourceKey !== $merchantType)
                var value{{ $resourceId }} = parseInt($('#{{ $resourceId }}_value').val().replace(/[,\.]/g, '')) || 0;
                if (value{{ $resourceId }} > 0) {
                    formData.receive_resource = '{{ $resourceKey }}';
                    var giveRate = baseFactor[{{ $resources[$merchantType] }}];
                    var receiveRate = tradeFactor[{{ $resourceId }}];
                    formData.give_amount = Math.ceil(value{{ $resourceId }} * (giveRate / receiveRate));
                    formData.exchange_rate = receiveRate / giveRate;
                }
            @endif
        @endforeach

        if (!formData.receive_resource || formData.give_amount === 0) {
            errorBoxNotify(LocalizationStrings.error, '@lang("Please select a resource to receive.")');
            return false;
        }

        // Use a small tolerance (1 unit) for floating point comparison
        if (formData.give_amount > offer_amount + 1) {
            errorBoxNotify(LocalizationStrings.error, '@lang("You don\'t have enough resources to trade.")');
            return false;
        }

        // Ensure we don't try to give more than we have
        if (formData.give_amount > offer_amount) {
            formData.give_amount = offer_amount;
        }

        // Submit the trade
        $.post('{{ route('merchant.trade') }}', formData, function(response) {
            if (response.success) {
                fadeBox(response.message || '@lang("Trade completed successfully!")', false);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                errorBoxNotify(LocalizationStrings.error, response.message || '@lang("Trade failed.")');
            }
        }).fail(function(xhr) {
            var response = xhr.responseJSON;
            errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '@lang("An error occurred. Please try again.")');
        });
    }

    function callTrader() {
        errorBoxDecision(
            '@lang("Caution")',
            '@lang("Do you want to get a new exchange rate for 3,500 Dark Matter? This will replace your current merchant.")',
            LocalizationStrings.yes,
            LocalizationStrings.no,
            function() {
                $.post('{{ route('merchant.call') }}', {
                    _token: token,
                    type: merchantType
                }, function(response) {
                    if (response.success) {
                        fadeBox('@lang("New merchant called successfully!")', false);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        errorBoxNotify(LocalizationStrings.error, response.message || '@lang("Failed to call merchant.")');
                    }
                }).fail(function(xhr) {
                    var response = xhr.responseJSON;
                    errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '@lang("An error occurred. Please try again.")');
                });
            }
        );
    }
</script>
