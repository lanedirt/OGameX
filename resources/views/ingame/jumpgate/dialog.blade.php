<div id="jumpgate">
    <form id="jumpgateForm" name="jumpgateForm">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div id="selecttarget">
            <h4>@lang('Select jump target')</h4>
            <div class="fleft">
                <span class="textBeefy">
                    @lang('Origin coordinates')
                </span>
                <a class="dark_highlight_tablet" target="_parent" href="{{ route('galaxy.index', ['galaxy' => $current_moon->getPlanetCoordinates()->galaxy, 'system' => $current_moon->getPlanetCoordinates()->system]) }}">
                    [{{ $current_moon->getPlanetCoordinates()->asString() }}]
                </a>
            </div>
            @if (!$is_on_cooldown)
                @if ($default_target)
                    <div class="homeIcon openStandardMoonMenu js_openStandardMoonMenu tooltip js_hideTipOnMobile" data-tooltip-title="@lang('Standard Jump Gate Target')"></div>
                @endif
                <div class="fright">
                    <span class="textBeefy">
                        @lang('Target coordinates'):
                    </span>
                    <select name="targetMoonId" id="targetMoonId" class="dropdown">
                        @if (count($eligible_targets) === 0)
                            <option value="0">--</option>
                        @else
                            @foreach ($eligible_targets as $target_moon)
                                <option value="{{ $target_moon->getPlanetId() }}"
                                    @if ($default_target && $default_target->getPlanetId() === $target_moon->getPlanetId())
                                        selected
                                    @endif
                                >
                                    [{{ $target_moon->getPlanetCoordinates()->asString() }}]
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            @endif
            <br class="clearfloat">
        </div>

        @if ($is_on_cooldown)
            {{-- Cooldown state --}}
            <div id="jumpgateNotReady">
                <p>@lang('Jump Gate is not ready!')</p>
                <p>@lang('Time until next jump'):</p>
                <p class="countdown" id="cooldown">{{ $cooldown_formatted }}</p>
            </div>
        @else
            {{-- Normal state - show ships --}}
            <h4>@lang('Select ships')</h4>
            <table cellspacing="0" cellpadding="0" class="list ship_selection_table">
                <tbody>
                @php
                    $shipChunks = array_chunk($available_ships, 2);
                    $rowIndex = 0;
                @endphp
                @foreach ($shipChunks as $shipPair)
                    <tr class="{{ $rowIndex % 2 == 0 ? 'alt' : '' }}">
                        @foreach ($shipPair as $ship)
                            @if ($ship['amount'] > 0)
                                <td class="ship_txt_row">
                                    <a class="dark_highlight_tablet" href="javascript:void(0);" onclick="toggleMaxShips('#jumpgateForm', {{ $ship['id'] }}, {{ $ship['amount'] }})">
                                        <div class="shipImage">
                                            <img class="tech{{ $ship['id'] }}" width="28" height="28" alt="{{ $ship['title'] }}" src="{{ asset('img/moons/small/3e567d6f16d040326c7a0ea29a4f41.gif') }}">
                                        </div>
                                    </a>
                                    <p>
                                        {{ $ship['title'] }}
                                        <span class="quantity" style="cursor: pointer;" onclick="toggleMaxShips('#jumpgateForm', {{ $ship['id'] }}, {{ $ship['amount'] }})">({{ number_format($ship['amount'], 0, ',', '.') }})</span>
                                    </p>
                                </td>
                                <td class="ship_input_row">
                                    <input name="ship_{{ $ship['id'] }}" type="text" pattern="[0-9,.]*" id="ship_{{ $ship['id'] }}" autocomplete="off" rel="{{ $ship['amount'] }}" class="textRight textinput">
                                </td>
                            @else
                                <td class="ship_txt_row tdInactive">
                                    <div class="shipImage">
                                        <img class="off tech{{ $ship['id'] }}" width="28" height="28" alt="{{ $ship['title'] }}" src="{{ asset('img/moons/small/3e567d6f16d040326c7a0ea29a4f41.gif') }}">
                                    </div>
                                    <p>
                                        {{ $ship['title'] }}
                                        <span class="quantity">(0)</span>
                                    </p>
                                </td>
                                <td class="ship_input_row">
                                    <input name="{{ $ship['id'] }}" type="text" pattern="[0-9,.]*" id="{{ $ship['id'] }}" class="textRight disabled" disabled>
                                </td>
                            @endif
                        @endforeach
                        @if (count($shipPair) === 1)
                            <td colspan="2"></td>
                        @endif
                    </tr>
                    @php $rowIndex++; @endphp
                @endforeach
                </tbody>
            </table>
            <div class="secondcol">
                <span class="float_left send_all">
                    <a id="sendall" href="javascript:void(0);" class="tooltip js_hideTipOnMobile" onclick="setMaxIntInputJumpgate()" data-tooltip-title="@lang('Select all available ships')">
                    </a>
                </span>
                <span class="float_left send_none">
                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile" onclick="document.jumpgateForm.reset();" data-tooltip-title="@lang('Reset selection')">
                    </a>
                </span>

                <input type="button" class="btn_blue float_right js_executeJumpButton" value="@lang('Jump')" onclick="executeJump()">
            </div>

            {{-- Default target selection form --}}
            <div class="showmessage">
                <div class="answerHeadline">
                    @lang('Standard Jump Gate Target')
                    <a class="openCloseForm" href="javascript:void(0);"></a>
                </div>
            </div>
            <div class="thirdCol hidden">
                <form id="jumpgateDefaultTargetSelectionForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <span class="float_left standard_moon_selection">
                        <select name="defaultJumpgateTarget" id="defaultJumpgateTarget" class="dropdown">
                            @foreach ($target_moons as $targetData)
                                @php $targetMoon = $targetData['moon']; @endphp
                                <option value="{{ $targetMoon->getPlanetId() }}"
                                    @if ($default_target && $default_target->getPlanetId() === $targetMoon->getPlanetId())
                                        selected
                                    @endif
                                >
                                    [{{ $targetMoon->getPlanetCoordinates()->asString() }}]
                                </option>
                            @endforeach
                        </select>
                    </span>
                    <input type="button" onclick="setDefaultTarget()" class="btn_blue float_left" value="@lang('OK')">
                </form>
            </div>
        @endif
    </form>
</div>

<script type="text/javascript">
    var jumpgateTranslation = {
        "validTargetNeeded": "@lang('You must select a valid target.')",
        "noShipsWereSelected": "@lang('No ships were selected!')",
        "jumpSuccess": "@lang('Ships have been transferred successfully.')",
        "jumpError": "@lang('An error occurred during the jump.')"
    };

    @php
        $shipMaxAmounts = [];
        foreach ($available_ships as $ship) {
            if ($ship['amount'] > 0) {
                $shipMaxAmounts[$ship['id']] = $ship['amount'];
            }
        }
    @endphp

    var shipMaxAmounts = @json($shipMaxAmounts);

    function toggleMaxShips(form, techID, amountOnPlanet) {
        var shipAmount = $(form).find('#ship_' + techID);
        if (parseInt(shipAmount.val()) !== amountOnPlanet) {
            shipAmount.val(amountOnPlanet);
        } else {
            shipAmount.val('');
        }
    }

    function setMaxIntInputJumpgate() {
        for (var techID in shipMaxAmounts) {
            $('#ship_' + techID).val(shipMaxAmounts[techID]);
        }
    }

    function executeJump() {
        var targetMoonId = $('#targetMoonId').val();
        if (!targetMoonId) {
            errorBoxDecision('Error', jumpgateTranslation.validTargetNeeded, 'OK', null, null);
            return;
        }

        // Collect ship data
        var formData = {
            '_token': '{{ csrf_token() }}',
            'targetMoonId': targetMoonId
        };

        var hasShips = false;
        for (var techID in shipMaxAmounts) {
            var amount = parseInt($('#ship_' + techID).val()) || 0;
            if (amount > 0) {
                formData['ship_' + techID] = amount;
                hasShips = true;
            }
        }

        if (!hasShips) {
            errorBoxDecision('Error', jumpgateTranslation.noShipsWereSelected, 'OK', null, null);
            return;
        }

        $.ajax({
            url: '{{ route('jumpgate.execute') }}',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Show success message with errorBoxNotify and redirect on OK
                errorBoxNotify('@lang('OK')', response.message || jumpgateTranslation.jumpSuccess, '@lang('OK')', function() {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        location.reload();
                    }
                });
            },
            error: function(xhr) {
                var message = jumpgateTranslation.jumpError;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                fadeBox(message, true);
            }
        });
    }

    function setDefaultTarget() {
        var targetMoonId = $('#defaultJumpgateTarget').val();

        $.ajax({
            url: '{{ route('jumpgate.setdefaulttarget') }}',
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'targetMoonId': targetMoonId
            },
            dataType: 'json',
            success: function(response) {
                fadeBox(response.message, false);
            },
            error: function(xhr) {
                var message = '@lang("An error occurred.")';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                fadeBox(message, true);
            }
        });
    }

    @if ($is_on_cooldown && $cooldown_remaining > 0)
    // Initialize countdown timer
    (function($) {
        simpleCountdown($("#cooldown"), {{ $cooldown_remaining }}, function() {
            location.reload();
        });
    })(jQuery);
    @endif

    // Initialize dropdowns immediately (overlay already loaded)
    (function() {
        if (typeof $.fn.ogameDropDown === 'function') {
            $('#jumpgate select.dropdown').ogameDropDown();
        }
    })();

    // Initialize input validation for ship amounts
    // Validates on keyup, change, and input events - clamps value between 0 and max (rel attribute)
    $('#jumpgateForm .ship_input_row .textinput').on('keyup change input', function() {
        checkIntInput(this, 0, $(this).attr('rel'));
    }).on('focus', function() {
        if ($.isNumeric($(this).val()) === false) {
            $(this).val('');
        } else {
            $(this).select();
        }
    });

    // Toggle default target form (Standard Jump Gate Target)
    $('#jumpgate .answerHeadline, .js_openStandardMoonMenu').click(function() {
        $('#jumpgate').find('.answerHeadline').toggleClass('open');
        $('.thirdCol').toggleClass('hidden');
    });
</script>
