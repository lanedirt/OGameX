<div id="rocketattack" data-title="@lang('Missile Attack')">
    @if(!empty($error))
        <div class="error-box">
            <p class="error">{{ $error }}</p>
        </div>
    @else
        <form method="post" action="{{ route('galaxy.missile-attack') }}" id="rocketForm">
            @csrf
            <input type="hidden" name="galaxy" value="{{ $galaxy }}">
            <input type="hidden" name="system" value="{{ $system }}">
            <input type="hidden" name="position" value="{{ $position }}">
            <input type="hidden" name="type" value="{{ $type }}">

            <div id="target">@lang('Target'): @if($type == 3)<figure class="planetIcon moon tooltip js_hideTipOnMobile" data-tooltip-title="@lang('Moon')"></figure>@endif[{{ $target_coords }}]</div>
            <div id="flightDuration">@lang('Flight duration'): {{ $flight_duration_formatted }}</div>
            <div id="arrivalTime">@lang('Arrival'): <span id="arrivalTimer" data-duration="{{ $flight_duration }}">{{ $arrival_time }}</span> @lang('Clock')</div>

            <div id="infos">
                <div id="numberrockets">
                    <ul>
                        <li class="defense503">
                            <div class="buildingimg sprite defense small defense503">
                                <a id="number" href="javascript:void(0);" class="tooltip js_hideTipOnMobile" data-tooltip-title="@lang('Interplanetary Missiles')">
                                    <span class="ecke">
                                        <span class="level">{{ $available_missiles }}</span>
                                    </span>
                                </a>
                            </div>
                        </li>
                    </ul>
                    <input type="text" pattern="[0-9]*" name="missile_count" id="missileCount" data-max="{{ $available_missiles }}" class="textinput" value="1">
                </div>

                <div id="priority">
                    @lang('Primary target'):
                    <ul>
                        <li class="defense401">
                            <div class="buildingimg sprite defense small defense401">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="401" data-priority="2" data-tooltip-title="@lang('Rocket Launcher')"></a>
                            </div>
                        </li>
                        <li class="defense402">
                            <div class="buildingimg sprite defense small defense402">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="402" data-priority="3" data-tooltip-title="@lang('Light Laser')"></a>
                            </div>
                        </li>
                        <li class="defense403">
                            <div class="buildingimg sprite defense small defense403">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="403" data-priority="4" data-tooltip-title="@lang('Heavy Laser')"></a>
                            </div>
                        </li>
                        <li class="defense404">
                            <div class="buildingimg sprite defense small defense404">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="404" data-priority="5" data-tooltip-title="@lang('Gauss Cannon')"></a>
                            </div>
                        </li>
                        <li class="defense405">
                            <div class="buildingimg sprite defense small defense405">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="405" data-priority="6" data-tooltip-title="@lang('Ion Cannon')"></a>
                            </div>
                        </li>
                        <li class="defense406">
                            <div class="buildingimg sprite defense small defense406">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="406" data-priority="7" data-tooltip-title="@lang('Plasma Turret')"></a>
                            </div>
                        </li>
                        <li class="defense407">
                            <div class="buildingimg sprite defense small defense407">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="407" data-priority="8" data-tooltip-title="@lang('Small Shield Dome')"></a>
                            </div>
                        </li>
                        <li class="defense408">
                            <div class="buildingimg sprite defense small defense408">
                                <a href="javascript:void(0)" class="tooltip js_hideTipOnMobile defense-target" data-ref="408" data-priority="9" data-tooltip-title="@lang('Large Shield Dome')"></a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div id="noPriorityInfo" style="display: block;">@lang('No primary target selected: random target')</div>
                <input type="hidden" name="target_priority" id="primaryTarget" value="0">

                @if($target_abm_count > 0)
                    <div id="abmWarning" style="color: #ff6b6b; margin: 10px 0; font-weight: bold;">
                        <img src="/img/galaxy/activity.gif" alt="Warning">
                        @lang('Target has') <strong>{{ $target_abm_count }}</strong> @lang('Anti-Ballistic Missiles')
                    </div>
                @endif

                <input type="submit" class="btn_blue" value="@lang('Fire')">
            </div>
        </form>
    @endif
</div>

<script type="text/javascript">
(function($) {
    function initMissleAttackLayer() {
        // Missile count quick select - clicking the missile image sets max amount
        $('#number').on('click', function(e) {
            e.preventDefault();
            var maxMissiles = $('#missileCount').data('max');
            $('#missileCount').val(maxMissiles);
        });

        // Defense target selection
        $('.defense-target').on('click', function(e) {
            e.preventDefault();

            var $this = $(this);
            var priority = $this.data('priority');
            var ref = $this.data('ref');

            // Remove active state from all
            $('.defense-target').parent().parent().removeClass('active');

            // If clicking the same target, deselect it
            if ($('#primaryTarget').val() == priority) {
                $('#primaryTarget').val('0');
                $('#noPriorityInfo').show();
            } else {
                // Select this target
                $this.parent().parent().addClass('active');
                $('#primaryTarget').val(priority);
                $('#noPriorityInfo').hide();
            }
        });

        // Form submission
        $('#rocketForm').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('input[type="submit"]');

            // Validate missile count
            var missileCount = parseInt($('#missileCount').val());
            var maxMissiles = parseInt($('#missileCount').data('max'));

            if (isNaN(missileCount) || missileCount < 1) {
                fadeBox('@lang('Please enter a valid number of missiles')', 1);
                return;
            }

            if (missileCount > maxMissiles) {
                fadeBox('@lang('You do not have enough missiles')', 1);
                return;
            }

            // Disable submit button
            $submitBtn.prop('disabled', true);

            // Submit via AJAX
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        fadeBox(response.message || '@lang('Missiles launched successfully!')', 0);

                        // Refresh fleet widget immediately - same pattern as espionage missions
                        // Method 1: Reload the event box (notification bar)
                        if (typeof getAjaxEventbox === 'function') {
                            getAjaxEventbox();
                        }

                        // Method 2: Refresh fleet events (fleet widget)
                        if (typeof refreshFleetEvents === 'function') {
                            refreshFleetEvents(true);
                        }

                        // Close overlay after brief delay
                        setTimeout(function() {
                            $('#rocketattack').closest('.overlayDiv').dialog('close');
                            // Refresh galaxy view if needed
                            if (typeof reloadGalaxy === 'function') {
                                reloadGalaxy();
                            }
                        }, 1500);
                    } else {
                        fadeBox(response.error || '@lang('Failed to launch missiles')', 1);
                        $submitBtn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    var errorMessage = '@lang('An error occurred. Please try again.')';

                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    fadeBox(errorMessage, 1);
                    $submitBtn.prop('disabled', false);
                }
            });
        });
    }

    // Update arrival time every second (current time + fixed flight duration)
    function updateArrivalTime() {
        var $timer = $('#arrivalTimer');
        if ($timer.length === 0) return;

        var flightDuration = parseInt($timer.data('duration'));
        if (isNaN(flightDuration) || flightDuration <= 0) return;

        setInterval(function() {
            // Calculate arrival time as current time + flight duration
            var arrivalDate = new Date(Date.now() + (flightDuration * 1000));
            var day = String(arrivalDate.getDate()).padStart(2, '0');
            var month = String(arrivalDate.getMonth() + 1).padStart(2, '0');
            var year = String(arrivalDate.getFullYear()).slice(-2);
            var hours = String(arrivalDate.getHours()).padStart(2, '0');
            var minutes = String(arrivalDate.getMinutes()).padStart(2, '0');
            var seconds = String(arrivalDate.getSeconds()).padStart(2, '0');

            var formattedTime = day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + ':' + seconds;
            $timer.text(formattedTime);
        }, 1000);
    }

    // Execute immediately - this script loads with the overlay content
    initMissleAttackLayer();
    updateArrivalTime();

    // Set dialog title
    setTimeout(function() {
        var $dialog = $('#rocketattack').closest('.ui-dialog');
        if ($dialog.length > 0) {
            $dialog.find('.ui-dialog-title').text('Missile Attack');
        }
    }, 100);
})(jQuery);
</script>

<style>
#rocketattack {
    padding: 10px;
    text-align: center;
    font-size: 11px;
}

#rocketattack #target {
    width: 420px;
    height: 14px;
    color: #848484;
    font-size: 13.2px;
    margin: 0 auto 0px auto;
    line-height: 14px;
}

#rocketattack #target .planetIcon {
    display: inline-block;
    vertical-align: middle;
    margin: 0 2px;
    padding: 0;
    width: 16px;
    height: 16px;
}

#rocketattack #flightDuration,
#rocketattack #arrivalTime {
    margin-bottom: 0px;
    line-height: 14px;
}

#rocketattack #infos {
    margin-top: 10px;
}

#rocketattack #numberrockets {
    margin-bottom: 10px;
    text-align: center;
}

#rocketattack #numberrockets ul {
    list-style: none;
    padding: 0;
    margin: 0 auto 5px auto;
    display: block;
}

#rocketattack #numberrockets li {
    display: inline-block;
    margin: 0;
}

#rocketattack #numberrockets .textinput {
    width: 70px;
    padding: 3px 5px;
    text-align: center;
    display: block;
    margin: 0 auto;
}

#rocketattack #priority {
    margin-bottom: 5px;
    text-align: center;
}

#rocketattack #priority ul {
    list-style: none;
    padding: 0;
    margin: 5px auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    width: 420px;
}

#rocketattack #priority li {
    width: 80px;
    margin: 5px 10px;
    opacity: 0.5;
    transition: opacity 0.2s;
    flex: 0 0 80px;
}

#rocketattack #priority li .buildingimg {
    width: 80px;
    height: 80px;
}

#rocketattack #priority li:hover {
    opacity: 0.8;
    cursor: pointer;
}

#rocketattack #priority li.active {
    opacity: 1;
    outline: 2px solid #6f9fc8;
    outline-offset: 2px;
}

#rocketattack #noPriorityInfo {
    color: #848484;
    font-style: italic;
    margin-bottom: 10px;
    text-align: center;
}

#rocketattack .btn_blue {
    margin-top: 5px;
}

#rocketattack .error-box {
    background: #3d1a1a;
    border: 1px solid #ff6b6b;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
}

#rocketattack .error-box p.error {
    color: #ff6b6b;
    margin: 0;
}
</style>
