@extends('ingame.layouts.main')

@section('content')

<style>
    /* AnythingSlider container sizing */
    .right_content .anythingSlider {
        width: 353px !important;
        height: 220px !important;
        margin: 0 auto;
    }

    .right_content .anythingWindow {
        width: 353px !important;
        height: 220px !important;
        overflow: hidden;
    }

    /* Ship/Defense list styling */
    #js_anythingSliderShips,
    #js_anythingSliderDefense {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    /* Panel sizing */
    #js_anythingSliderShips li,
    #js_anythingSliderDefense li {
        width: 353px !important;
        height: 220px !important;
        float: left;
    }

    /* Item grid layout */
    #js_anythingSliderShips .item,
    #js_anythingSliderDefense .item {
        display: inline-block;
        width: 80px;
        height: 100px;
        margin: 5px;
        vertical-align: top;
    }
</style>

<div id="eventboxContent" style="display: none">
    <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
</div>
<div id="traderOverviewcomponent" class="maincontent">
    <div id="traderOverview">
        <div id="inhalt">
            <div id="planet" style="background-position: 0px -220px; height: 250px;" class="detail">
                <div id="detail" class="detail_screen small">
                    <div id="techDetailLoading"></div>
                </div>
                <div id="loadingOverlay" class="" style="display: none;">
                    <img src="/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif" alt="load...">
                </div>
                <div id="header_text" style="display: block;">
                    <h2>@lang('Scrap Merchant')</h2>
                    <a class="back_to_overview js_backToOverview tooltip js_hideTipOnMobile right" href="{{ route('merchant.index') }}" data-tooltip-title="@lang('Back')" style="display: inline;"></a>
                    <a class="small_back_to_overview js_backToOverview tooltip js_hideTipOnMobile" href="{{ route('merchant.index') }}" data-tooltip-title="@lang('Back')"></a>
                </div>
            </div>
            <div class="c-left c-small"></div>
            <div class="c-right c-small"></div>

            <!-- Trader Scrap Start -->
            <div id="div_traderScrap" class="div_trader">
                <div class="header">
                    <h2>@lang('Scrap Merchant')</h2>
                </div>
                <div class="content">
                    <p class="stimulus clearfix">
                        <a href="javascript:void(0);" class="tooltipHTML help rules" data-tooltip-title="@lang('Rules|Usually the scrap merchant will pay back 35% of the construction costs of ships and defence systems. However you can only receive as many resources back as you have space for in your storage.<br /><br />With the help of Dark Matter you can renegotiate. In doing so, the percentage of the construction costs that the scrap merchant pays you will increase by 5 - 14%. Each round of negotiations are 2,000 Dark Matter more expensive than the last. The scrap merchant will pay out no more than 75% of the construction costs.')"></a>
                        @lang('The scrap merchant accepts used ships and defence systems.')
                    </p>

                    <div class="left_box">
                        <div class="left_header"><h2>@lang('Offer')</h2></div>
                        <div class="left_content">
                            <div class="scrap_trader_img">
                                <img src="/img/merchant/scrap_merchant.jpg" width="140" height="140" alt="@lang('Scrap Merchant')">
                            </div>
                            <div id="js_scrapOffer" class="offer tooltip js_hideTipOnMobile" data-tooltip-title="@lang('Offer')">
                                @php
                                    // Calculate initial bar height based on offer percentage (0-140px for 0-75%)
                                    $maxHeight = 140;
                                    $maxPercentage = 75;
                                    $initialHeight = ($offerPercentage / $maxPercentage) * $maxHeight;
                                @endphp
                                <div class="js_scrap_offer_amount scrap_offer_amount" style="height: {{ $initialHeight }}px;">{{ $offerPercentage }}%</div>
                            </div>
                            <br class="clearfloat">
                            <p class="scrap_trader_quote">@lang('You won`t get a better offer in any other galaxy.')</p>

                            <div class="resourceIcon metal resource_label odd tooltipLeft js_hideTipOnMobile" data-tooltip-title="@lang('Metal')"></div>
                            <div id="js_scrapAmountMetal" class="resource_amount odd">0</div><br class="clearfloat">

                            <div class="resourceIcon crystal resource_label even tooltipLeft js_hideTipOnMobile" data-tooltip-title="@lang('Crystal')"></div>
                            <div id="js_scrapAmountCrystal" class="resource_amount even">0</div><br class="clearfloat">

                            <div class="resourceIcon deuterium resource_label odd tooltipLeft js_hideTipOnMobile" data-tooltip-title="@lang('Deuterium')"></div>
                            <div id="js_scrapAmountDeuterium" class="resource_amount odd">0</div><br class="clearfloat">

                            <button id="js_scrapBargain" class="bargain">@lang('Bargain')</button>
                            <br class="clearfloat">
                            <span class="bargain_cost">@lang('Costs:'): <span class="js_bargainCost">{{ number_format($bargainCost) }}</span> @lang('Dark Matter')</span>
                        </div>
                        <div class="left_footer"></div>
                    </div>

                    <div class="right_box">
                        <div class="right_header">
                            <h2>@lang('Objects to be scrapped')</h2>
                        </div>
                        <div class="right_content">
                            <form id="breakerForm">
                                <div class="scrap_ships selected" id="js_tabShips">@lang('Ships')</div>
                                <div class="scrap_defense" id="js_tabDefense">@lang('Defensive structures')</div>

                                <!-- Ships Slider -->
                                <ul id="js_anythingSliderShips" class="ships">
                                        @php
                                            $shipsChunks = array_chunk($ships, 8, true);
                                            $tabindex = 0;
                                        @endphp
                                        @foreach($shipsChunks as $chunk)
                                            <li class="panel">
                                                @foreach($chunk as $shipId => $shipData)
                                                    @php $tabindex++; @endphp
                                                    <div class="item {{ $shipData['amount'] > 0 ? 'on' : 'off' }}" id="button{{ $shipId }}">
                                                        <div class="image">
                                                            <div class="sprite small ship ship{{ $shipId }}">
                                                                <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile js_maxShips" ref="#ship_{{ $shipId }}" data-tooltip-title="{{ $shipData['name'] }}">
                                                                    <span class="ecke">
                                                                        <span class="level amount">{{ number_format($shipData['amount']) }}</span>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <input class="ship_amount" tabindex="{{ $tabindex }}" name="am{{ $shipId }}" id="ship_{{ $shipId }}"
                                                               value="" type="text" pattern="[0-9,.]*" {{ $shipData['amount'] == 0 ? 'readonly="readonly"' : '' }}
                                                               data-item-id="{{ $shipId }}"
                                                               data-metal="{{ $shipData['cost']['metal'] }}" data-crystal="{{ $shipData['cost']['crystal'] }}" data-deuterium="{{ $shipData['cost']['deuterium'] }}">
                                                        <a href="javascript:void(0);" class="max tooltip js_maxShips" ref="#ship_{{ $shipId }}" data-tooltip-title="@lang('Select all')"></a>
                                                    </div>
                                                @endforeach
                                            </li>
                                        @endforeach
                                </ul>

                                <!-- Defense Slider -->
                                <ul id="js_anythingSliderDefense" class="defenders">
                                        @php
                                            $defenseChunks = array_chunk($defense, 8, true);
                                            $tabindex = 0;
                                        @endphp
                                        @if(count($defenseChunks) > 0)
                                            @foreach($defenseChunks as $chunk)
                                                <li class="panel">
                                                    @foreach($chunk as $defenseId => $defenseData)
                                                        @php $tabindex++; @endphp
                                                        <div class="item {{ $defenseData['amount'] > 0 ? 'on' : 'off' }}" id="button{{ $defenseId }}">
                                                            <div class="image">
                                                                <div class="sprite small defense defense{{ $defenseId }}">
                                                                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile js_maxShips" ref="#ship_{{ $defenseId }}" data-tooltip-title="{{ $defenseData['name'] }}">
                                                                        <span class="ecke">
                                                                            <span class="level amount">{{ number_format($defenseData['amount']) }}</span>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <input class="ship_amount" tabindex="{{ $tabindex }}" name="am{{ $defenseId }}" id="ship_{{ $defenseId }}"
                                                                   value="" type="text" pattern="[0-9,.]*" {{ $defenseData['amount'] == 0 ? 'readonly="readonly"' : '' }}
                                                                   data-item-id="{{ $defenseId }}"
                                                                   data-metal="{{ $defenseData['cost']['metal'] }}" data-crystal="{{ $defenseData['cost']['crystal'] }}" data-deuterium="{{ $defenseData['cost']['deuterium'] }}">
                                                            <a href="javascript:void(0);" class="max tooltip js_maxShips" ref="#ship_{{ $defenseId }}" data-tooltip-title="@lang('Select all')"></a>
                                                        </div>
                                                    @endforeach
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="panel">
                                                <div style="padding: 20px; text-align: center;">@lang('No defensive structures available')</div>
                                            </li>
                                        @endif
                                </ul>

                                <div class="allornonewrap fleft">
                                    <span class="send_all">
                                        <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile sendAll" data-tooltip-title="@lang('Select all')">
                                        </a>
                                    </span>
                                    <span class="send_none">
                                        <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile sendNone" data-tooltip-title="@lang('Reset choice')">
                                        </a>
                                    </span>
                                </div>
                                <input id="js_scrapScrapIT" class="scrap_it disabled" type="submit" value="@lang('Scrap')">
                            </form>
                        </div>
                        <div class="right_footer"></div>
                    </div>
                </div>
                <div class="footer"></div>
            </div>
            <!-- Trader Scrap End -->

        </div>
    </div>
</div>

<script type="text/javascript">
    var offerPercentage = {{ $offerPercentage }};
    var bargainCount = {{ $bargainCount }};
    var maxPercentage = 75;
    var darkMatter = {{ $darkMatter }};
    var storageCapacity = {
        metal: {{ $storageCapacity['metal'] }},
        crystal: {{ $storageCapacity['crystal'] }},
        deuterium: {{ $storageCapacity['deuterium'] }}
    };
    // Apply 1% buffer to match server-side validation and account for production
    var storageCapacityWithBuffer = {
        metal: Math.floor(storageCapacity.metal * 1.01),
        crystal: Math.floor(storageCapacity.crystal * 1.01),
        deuterium: Math.floor(storageCapacity.deuterium * 1.01)
    };
    var currentResources = {
        metal: {{ $currentResources['metal'] }},
        crystal: {{ $currentResources['crystal'] }},
        deuterium: {{ $currentResources['deuterium'] }}
    };

    // Map of item IDs to names for confirmation dialog
    var itemNames = {
        @foreach($ships as $shipId => $shipData)
            {{ $shipId }}: "{{ $shipData['name'] }}",
        @endforeach
        @foreach($defense as $defenseId => $defenseData)
            {{ $defenseId }}: "{{ $defenseData['name'] }}",
        @endforeach
    };

    $(document).ready(function() {
        // Function to update bargain button state (visual only, button remains clickable)
        function updateBargainButton() {
            var cost = {{ $bargainCost }};
            if (offerPercentage >= maxPercentage || darkMatter < cost) {
                $('#js_scrapBargain').addClass('disabled');
            } else {
                $('#js_scrapBargain').removeClass('disabled');
            }
        }

        // Initial button state check
        updateBargainButton();

        // Check if anythingSlider is available
        if (typeof $.fn.anythingSlider === 'undefined') {
            console.error('AnythingSlider plugin not loaded');
            return;
        }

        // Initialize AnythingSlider for ships
        var shipsSlider = $('#js_anythingSliderShips').anythingSlider({
            buildNavigation: true,
            buildStartStop: false,
            resizeContents: false,
            expand: false,
            hashTags: false,
            width: 353,
            height: 220
        });

        // Initialize AnythingSlider for defense (hidden initially)
        var defenseSlider = $('#js_anythingSliderDefense').anythingSlider({
            buildNavigation: true,
            buildStartStop: false,
            resizeContents: false,
            expand: false,
            hashTags: false,
            width: 353,
            height: 220
        });

        // Hide defense slider by default (show ships)
        $('#js_anythingSliderDefense').closest('.anythingSlider').hide();
        $('#js_anythingSliderShips').closest('.anythingSlider').show();

        // Tab switching
        $('#js_tabShips').click(function() {
            $(this).addClass('selected');
            $('#js_tabDefense').removeClass('selected');
            $('#js_anythingSliderShips').closest('.anythingSlider').show();
            $('#js_anythingSliderDefense').closest('.anythingSlider').hide();
        });

        $('#js_tabDefense').click(function() {
            $(this).addClass('selected');
            $('#js_tabShips').removeClass('selected');
            $('#js_anythingSliderDefense').closest('.anythingSlider').show();
            $('#js_anythingSliderShips').closest('.anythingSlider').hide();
        });

        // Max buttons
        $('.js_maxShips').click(function(e) {
            e.preventDefault();
            var ref = $(this).attr('ref');
            var $input = $(ref);
            var amount = parseInt($input.closest('.item').find('.amount').text().replace(/,/g, ''));
            if (amount > 0) {
                $input.val(amount.toLocaleString());
                updateScrapOffer();
                // Validate after a short delay so user can see the max amount first
                setTimeout(function() {
                    validateStorageCapacity($input);
                }, 100);
            }
        });

        // Select all / none
        $('.sendAll').click(function(e) {
            e.preventDefault();
            // Get the currently visible slider
            var $visibleSlider = $('.anythingSlider:visible');
            var $inputsToValidate = [];

            // Only select from active (non-cloned) panels in the visible slider
            $visibleSlider.find('.panel:not(.cloned) .item.on input.ship_amount').each(function() {
                var amount = parseInt($(this).closest('.item').find('.amount').text().replace(/,/g, ''));
                $(this).val(amount.toLocaleString());
                $inputsToValidate.push($(this));
            });
            updateScrapOffer();

            // Validate each input after a short delay so user can see the max amounts first
            setTimeout(function() {
                $inputsToValidate.forEach(function($input) {
                    validateStorageCapacity($input);
                });
            }, 100);
        });

        $('.sendNone').click(function(e) {
            e.preventDefault();
            // Get the currently visible slider
            var $visibleSlider = $('.anythingSlider:visible');
            // Only clear from active (non-cloned) panels in the visible slider
            $visibleSlider.find('.panel:not(.cloned) .ship_amount').val('');
            updateScrapOffer();
        });

        // Input change - only validate on 'change' (when user leaves field), not on every keystroke
        $('.ship_amount').on('input', function() {
            updateScrapOffer();
        });

        $('.ship_amount').on('change', function() {
            validateStorageCapacity($(this));
        });

        // Update scrap offer calculation
        function updateScrapOffer() {
            var totalMetal = 0, totalCrystal = 0, totalDeuterium = 0;
            var hasSelection = false;

            // Select only inputs that are NOT in cloned panels and have IDs
            var $validInputs = $('.ship_amount').filter(function() {
                var hasCloneClass = $(this).closest('.clone, .cloned').length > 0;
                var hasId = $(this).attr('id') !== undefined && $(this).attr('id') !== '';
                return hasId && !hasCloneClass;
            });

            $validInputs.each(function() {
                var val = parseInt($(this).val().replace(/,/g, '')) || 0;

                if (val > 0) {
                    hasSelection = true;
                    var costMetal = parseInt($(this).data('metal')) || 0;
                    var costCrystal = parseInt($(this).data('crystal')) || 0;
                    var costDeuterium = parseInt($(this).data('deuterium')) || 0;

                    totalMetal += val * costMetal;
                    totalCrystal += val * costCrystal;
                    totalDeuterium += val * costDeuterium;
                }
            });

            // Apply offer percentage
            var returnMetal = Math.floor(totalMetal * (offerPercentage / 100));
            var returnCrystal = Math.floor(totalCrystal * (offerPercentage / 100));
            var returnDeuterium = Math.floor(totalDeuterium * (offerPercentage / 100));

            // Apply storage limits (ensure free storage is never negative)
            var freeMetalStorage = Math.max(0, storageCapacityWithBuffer.metal - currentResources.metal);
            var freeCrystalStorage = Math.max(0, storageCapacityWithBuffer.crystal - currentResources.crystal);
            var freeDeuteriumStorage = Math.max(0, storageCapacityWithBuffer.deuterium - currentResources.deuterium);

            returnMetal = Math.min(returnMetal, freeMetalStorage);
            returnCrystal = Math.min(returnCrystal, freeCrystalStorage);
            returnDeuterium = Math.min(returnDeuterium, freeDeuteriumStorage);

            $('#js_scrapAmountMetal').text(returnMetal.toLocaleString());
            $('#js_scrapAmountCrystal').text(returnCrystal.toLocaleString());
            $('#js_scrapAmountDeuterium').text(returnDeuterium.toLocaleString());

            // Enable/disable scrap button
            if (hasSelection) {
                $('#js_scrapScrapIT').removeClass('disabled').prop('disabled', false);
            } else {
                $('#js_scrapScrapIT').addClass('disabled').prop('disabled', true);
            }
        }

        // Track the last modified input to know which one to validate
        var lastModifiedInput = null;

        // Validate storage capacity and adjust amounts if needed
        function validateStorageCapacity(targetInput) {
            var freeMetalStorage = Math.max(0, storageCapacityWithBuffer.metal - currentResources.metal);
            var freeCrystalStorage = Math.max(0, storageCapacityWithBuffer.crystal - currentResources.crystal);
            var freeDeuteriumStorage = Math.max(0, storageCapacityWithBuffer.deuterium - currentResources.deuterium);

            // First, calculate storage used by all OTHER inputs (not the target)
            $('.ship_amount[id]').each(function() {
                if (targetInput && this === targetInput[0]) {
                    return; // Skip the target input in this pass
                }

                var $input = $(this);
                var val = parseInt($input.val().replace(/,/g, '')) || 0;

                if (val <= 0) {
                    return; // Skip empty inputs
                }

                var metalCost = parseInt($input.data('metal')) || 0;
                var crystalCost = parseInt($input.data('crystal')) || 0;
                var deuteriumCost = parseInt($input.data('deuterium')) || 0;

                // Calculate resources per unit at current offer percentage
                var metalPerUnit = Math.floor(metalCost * (offerPercentage / 100));
                var crystalPerUnit = Math.floor(crystalCost * (offerPercentage / 100));
                var deuteriumPerUnit = Math.floor(deuteriumCost * (offerPercentage / 100));

                // Deduct storage used by this input
                freeMetalStorage -= metalPerUnit * val;
                freeCrystalStorage -= crystalPerUnit * val;
                freeDeuteriumStorage -= deuteriumPerUnit * val;
            });

            // Now validate ONLY the target input against remaining storage
            if (targetInput) {
                var val = parseInt(targetInput.val().replace(/,/g, '')) || 0;

                if (val > 0) {
                    var itemId = targetInput.attr('data-item-id');
                    var metalCost = parseInt(targetInput.data('metal')) || 0;
                    var crystalCost = parseInt(targetInput.data('crystal')) || 0;
                    var deuteriumCost = parseInt(targetInput.data('deuterium')) || 0;

                    // Calculate resources per unit at current offer percentage
                    var metalPerUnit = Math.floor(metalCost * (offerPercentage / 100));
                    var crystalPerUnit = Math.floor(crystalCost * (offerPercentage / 100));
                    var deuteriumPerUnit = Math.floor(deuteriumCost * (offerPercentage / 100));

                    // Calculate maximum amount that can be scrapped based on REMAINING storage
                    var maxByMetal = metalPerUnit > 0 ? Math.max(0, Math.floor(freeMetalStorage / metalPerUnit)) : Number.MAX_SAFE_INTEGER;
                    var maxByCrystal = crystalPerUnit > 0 ? Math.max(0, Math.floor(freeCrystalStorage / crystalPerUnit)) : Number.MAX_SAFE_INTEGER;
                    var maxByDeuterium = deuteriumPerUnit > 0 ? Math.max(0, Math.floor(freeDeuteriumStorage / deuteriumPerUnit)) : Number.MAX_SAFE_INTEGER;

                    var maxAmount = Math.max(0, Math.min(val, maxByMetal, maxByCrystal, maxByDeuterium));

                    if (maxAmount < val) {
                        // Need to reduce the amount
                        var itemName = itemNames[itemId] || 'Unknown Item';
                        targetInput.val(maxAmount.toLocaleString());

                        var message;
                        if (maxAmount === 0) {
                            message = 'The space in the storage was not large enough, so the number of ' + itemName + ' was reduced to 0';
                        } else {
                            message = 'The space in the storage was not large enough, so the number of ' + itemName + ' was reduced to ' + maxAmount.toLocaleString();
                        }
                        fadeBox(message, true);

                        // Refresh the offer calculation with new value
                        updateScrapOffer();
                    }
                }
            }
        }

        // Bargain button
        $('#js_scrapBargain').click(function(e) {
            e.preventDefault();

            if (offerPercentage >= maxPercentage) {
                errorBoxNotify(LocalizationStrings.error, '{{ __('t_merchant.offer_at_maximum') }}', 'OK');
                return;
            }

            var cost = {{ $bargainCost }};
            if (darkMatter < cost) {
                errorBoxDecision(
                    LocalizationStrings.error,
                    '{{ __('t_merchant.not_enough_dark_matter') }}',
                    '{{ __('t_merchant.yes') }}',
                    '{{ __('t_merchant.no') }}',
                    function() {
                        // User clicked yes - do nothing, just close
                    }
                );
                return;
            }

            $.post('{{ route('merchant.scrap.bargain') }}', {
                _token: '{{ csrf_token() }}'
            }, function(response) {
                if (response.success) {
                    offerPercentage = response.newPercentage;
                    bargainCount = response.bargainCount;
                    darkMatter = response.darkMatter;

                    // Update percentage text
                    $('.js_scrap_offer_amount').text(offerPercentage + '%');

                    // Update the visual height of the offer bar
                    // Height represents the percentage (0-140px for 0-75%)
                    var maxHeight = 140;
                    var barHeight = (offerPercentage / maxPercentage) * maxHeight;
                    $('.js_scrap_offer_amount').css('height', barHeight + 'px');

                    $('.js_bargainCost').text(response.newCost.toLocaleString());

                    updateScrapOffer();
                    updateBargainButton();
                    fadeBox('{{ __('t_merchant.negotiation_successful') }}', false);
                } else {
                    errorBoxNotify(LocalizationStrings.error, response.message);
                }
            }).fail(function(xhr) {
                var response = xhr.responseJSON;
                errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '{{ __('t_merchant.error_occurred') }}');
            });
        });

        // Scrap form submit
        $('#breakerForm').submit(function(e) {
            e.preventDefault();

            var items = {};
            var hasItems = false;

            // Only collect from inputs with IDs (cloned inputs don't have IDs)
            $('.ship_amount[id]').each(function() {
                var val = parseInt($(this).val().replace(/,/g, '')) || 0;
                if (val > 0) {
                    var id = $(this).attr('name').replace('am', '');
                    items[id] = val;
                    hasItems = true;
                }
            });

            if (!hasItems) {
                errorBoxNotify(LocalizationStrings.error, '{{ __('t_merchant.select_items_to_scrap') }}');
                return;
            }

            // Pre-validate storage capacity before showing confirmation
            $.post('{{ route('merchant.scrap.execute') }}', {
                _token: '{{ csrf_token() }}',
                items: items
            }, function(response) {
                // Success - show confirmation dialog
                showScrapConfirmation(items);
            }).fail(function(xhr) {
                var response = xhr.responseJSON;

                // Check if this is a storage capacity issue
                if (response && response.needsConfirmation && response.warnings) {
                    // Update input fields with adjusted amounts
                    if (response.adjustedItems) {
                        Object.keys(response.adjustedItems).forEach(function(itemId) {
                            var input = $('input[data-item-id="' + itemId + '"]');
                            if (input.length) {
                                input.val(response.adjustedItems[itemId]);
                            }
                        });
                    }

                    // Show warning messages
                    response.warnings.forEach(function(warning) {
                        fadeBox(warning.message, true);
                    });
                } else {
                    // Regular error
                    errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '{{ __('t_merchant.error_occurred') }}');
                }
            });
        });

        // Function to show scrap confirmation and execute
        function showScrapConfirmation(items) {
            // Build confirmation message with item list
            var itemListHtml = '<div style="text-align: left; margin-left: 30px">';
            $.each(items, function(id, amount) {
                var itemName = itemNames[id] || '{{ __('t_merchant.unknown_item') }}';
                itemListHtml += amount + 'x ' + itemName + '<br>';
            });
            itemListHtml += '</div>';

            var confirmMessage = '{{ __('t_merchant.scrap_confirmation') }}<br><br>' + itemListHtml;

            // Show confirmation dialog
            errorBoxDecision(
                '{{ __('t_merchant.scrap_merchant') }}',
                confirmMessage,
                '{{ __('t_merchant.yes') }}',
                '{{ __('t_merchant.no') }}',
                function() {
                    // User confirmed - execute the scrap
                    $.post('{{ route('merchant.scrap.execute') }}', {
                        _token: '{{ csrf_token() }}',
                        items: items,
                        confirmed: true
                    }, function(response) {
                        if (response.success) {
                            // Show the random merchant message from the server
                            fadeBox(response.message, false);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            errorBoxNotify(LocalizationStrings.error, response.message);
                        }
                    }).fail(function(xhr) {
                        var response = xhr.responseJSON;
                        errorBoxNotify(LocalizationStrings.error, response && response.message ? response.message : '{{ __('t_merchant.error_occurred') }}');
                    });
                }
            );
        }

        initTooltips();
    });
</script>

@endsection
