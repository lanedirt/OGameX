@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="facilitiescomponent" class="maincontent">
        <div id="facilities">
            <div class="c-left"></div>
            <div class="c-right"></div>
            <header data-anchor="technologyDetails" data-technologydetails-size="large" style="background-image:url('{{ asset('img/headers/facilities/' . $header_filename . '.jpg') }}');">
                <h2>Facilities - {{ $planet_name }}</h2>
                @if (isset($jump_gate_level) && $jump_gate_level > 0)
                    <div id="slot01" class="slot">
                        <a href="{{ route('jumpgate.index') }}" class="overlay" data-overlay-title="@lang('Use Jump Gate')">@lang('Jump Gate')</a>
                    </div>
                @endif
                @if (isset($alliance_depot_level) && $alliance_depot_level > 0)
                    <div id="slot01" class="slot">
                        <a href="{{ route('alliance-depot.index') }}" class="overlay" data-overlay-class="allydepot" data-overlay-title="@lang('Alliance Depot')">@lang('Alliance Depot')</a>
                    </div>
                @endif
            </header>
            <div id="technologydetails_wrapper">
                <div id="technologydetails_content"></div>
            </div>
            <div id="technologies">
                <h3>
                    @lang('Facility buildings')
                </h3>
                <ul class="icons">
                    @php /** @var OGame\ViewModels\BuildingViewModel $building */ @endphp
                    @foreach ($buildings[0] as $building)
                        <li class="technology {{ $building->object->class_name }} hasDetails tooltip hideTooltipOnMouseenter js_hideTipOnMobile ipiHintable tpd-hideOnClickOutside"
                            data-technology="{{ $building->object->id }}"
                            data-is-spaceprovider=""
                            aria-label="{{ $building->object->title }}"
                            data-ipi-hint="ipiTechnology{{ $building->object->class_name }}"
                            @if ($building->currently_building)
                                data-status="active"
                                data-is-spaceprovider=""
                                data-progress="26"
                                data-start="1713521207"
                                data-end="1713604880"
                                data-total="61608"
                                title="{{ $building->object->title }}<br/>@lang('Under construction')"
                            @elseif ($is_in_vacation_mode)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Error, player is in vacation mode')"
                            @elseif (!$building->requirements_met)
                                data-status="off"
                                title="{{ $building->object->title }}<br/>@lang('Requirements are not met!')"
                            @elseif (!$building->valid_planet_type)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('You can\'t construct that building on a moon!')"
                            @elseif ($building->ship_or_defense_in_progress && ($building->object->machine_name === 'shipyard' || $building->object->machine_name === 'nano_factory'))
                                data-status="disabled"
                            title="{{ $building->object->title }}<br/>@lang('The shipyard is still busy')"
                            @elseif ($building->research_in_progress && $building->object->machine_name == 'research_lab')
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Research is currently being carried out!')"
                            @elseif (!$building->enough_resources)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Not enough resources!')"
                            @elseif ($build_queue_max)
                                data-status="disabled"
                                title="{{ $building->object->title }}<br/>@lang('Queue is full')"
                            @else
                                data-status="on"
                                title="{{ $building->object->title }}"
                                @endif
                        >

                        <span class="icon sprite sprite_medium medium {{ $building->object->class_name }}">
                            @if ($building->currently_building)
                            @elseif (!$building->requirements_met)
                            @elseif (!$building->valid_planet_type)
                            @elseif (!$building->enough_resources)
                            @elseif ($build_queue_max)
                            @elseif ($is_in_vacation_mode)
                            @elseif ($building->research_in_progress && $building->object->machine_name == 'research_lab')
                            @elseif ($building->ship_or_defense_in_progress  && ($building->object->machine_name === 'shipyard' || $building->object->machine_name === 'nano_factory'))
                            @else
                                <button
                                        class="upgrade tooltip hideOthers js_hideTipOnMobile"
                                        aria-label="Expand {!! $building->object->title !!} on level {!! ($building->current_level + 1) !!}" title="Expand {!! $building->object->title !!} on level {!! ($building->current_level + 1) !!}"
                                        data-technology="{{ $building->object->id }}" data-is-spaceprovider="">
                                </button>
                            @endif
                            @if ($building->currently_building)
                                <span class="targetlevel" data-value="{{ $building->target_level ?? ($building->current_level + 1) }}" data-bonus="0">{{ $building->target_level ?? ($building->current_level + 1) }}</span>
                                <div class="cooldownBackground"></div>
                                <time-counter><time class="countdown buildingCountdown" id="countdownbuildingDetails" data-segments="2">...</time></time-counter>
                            @endif
                            <span class="level" data-value="{{ $building->current_level }}" data-bonus="0">
                            <span class="stockAmount">{{ $building->current_level }}</span>
                            <span class="bonus"></span>
                            </span>
                        </span>
                    @endforeach
                </ul>
            </div>
        </div>

        <div id="productionboxBottom">
            <div class="productionBoxBuildings boxColumn building">
                <div id="productionboxbuildingcomponent" class="productionboxbuilding injectedComponent parent facilities"><div class="content-box-s">
                        <div class="header">
                            <h3>@lang('Buildings')</h3>
                        </div>
                        <div class="content">
                            {{-- Building is actively being built. --}}
                            @include ('ingame.shared.buildqueue.building-active', ['build_active' => $build_active])
                            {{-- Building queue has items. --}}
                            @include ('ingame.shared.buildqueue.building-queue', ['build_queue' => $build_queue])
                        </div>
                        <div class="footer"></div>
                    </div>
                    <script type="text/javascript">
                        var scheduleBuildListEntryUrl = '{{ route("facilities.addbuildrequest.post") }}';
                        var LOCA_ERROR_INQUIRY_NOT_WORKED_TRYAGAIN = 'Your last action could not be processed. Please try again.';
                        redirectPremiumLink = '#TODO_index.php?page=premium&showDarkMatter=1'
                    </script>
                </div>
            </div>
            <div class="productionBoxShips boxColumn ship">
            </div>
        </div>


        <script type="text/javascript">
            var planetMoveInProgress = false;
            var wreckFieldUpdateInterval;
            var burnUpCountdownInterval;

      // Helper function to format datetime for countdown
            function formatDateTime(seconds) {
                const days = Math.floor(seconds / 86400);
                const hours = Math.floor((seconds % 86400) / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                return `P${days}DT${hours}H${minutes}M${seconds % 60}S`;
            }

      // Wreck field functionality
            $(document).ready(function() {

                // Check if we need to trigger space dock click from sessionStorage or URL parameter
                const shouldTriggerSpaceDock = sessionStorage.getItem('triggerSpaceDock') === 'true' ||
                                               new URLSearchParams(window.location.search).get('openSpaceDock') === '1';

                if (shouldTriggerSpaceDock) {
                    sessionStorage.removeItem('triggerSpaceDock');

                    // Use a more direct approach - click the space dock building itself
                    setTimeout(() => {
                        const $spaceDock = $('.technology[data-technology="36"]');
                        if ($spaceDock.length > 0) {
                            // Click the space dock building element itself (not the details button)
                            const $buildingContent = $spaceDock.find('span, div').not('.details');
                            if ($buildingContent.length > 0) {
                                $buildingContent.first().trigger('click');
                                setTimeout(checkAndLoadWreckField, 1000);
                            } else {
                                $spaceDock.first().trigger('click');
                                setTimeout(checkAndLoadWreckField, 1000);
                            }
                        } else {
                            console.error('Space dock element not found');
                        }
                    }, 1000);
                }

                // Handle clicks on space dock technology directly
                $(document).on('click', '.technology.space_dock, .technology[data-technology="36"]', function(e) {

                    // Don't trigger if clicking the upgrade button or any button
                    if ($(e.target).closest('button').length === 0 && !$(e.target).hasClass('upgrade')) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Use multiple approaches to detect when space dock details are loaded
                        checkForSpaceDockDetails();
                    }
                });

                // Debug: Check if technologydetails_content exists
            });

            function checkForSpaceDockDetails() {

                // Check immediately
                checkAndLoadWreckField();

                // Check again after a short delay (since AJAX might be loading)
                setTimeout(checkAndLoadWreckField, 500);

                // Check one more time after a longer delay
                setTimeout(checkAndLoadWreckField, 1500);
            }

            function checkAndLoadWreckField() {

                // Look for space dock details in the content
                var $spaceDockDetails = $('#technologydetails_content').find('#technologydetails[data-technology-id="36"]');

                if ($spaceDockDetails.length === 0) {
                    // Try alternative approach - look for any space dock related content
                    $spaceDockDetails = $('#technologydetails_content').find('*').filter(function() {
                        var $this = $(this);
                        return $this.find('.repairDock, .space_dock').length > 0 ||
                               $this.hasClass('repairDock') ||
                               $this.hasClass('space_dock') ||
                               $this.text().toLowerCase().indexOf('space dock') !== -1 ||
                               $this.attr('data-technology-id') === '36';
                    }).closest('#technologydetails');
                }


                if ($spaceDockDetails.length > 0) {

                    // Find the description div within the technology details
                    var $description = $spaceDockDetails.find('.description');

                    if ($description.length > 0) {
                        loadWreckFieldDataIntoDescription($description);
                    } else {
                    }
                } else {
                }
            }

            // Global functions for wreck field actions (called from onclick attributes)
            window.startWreckFieldRepairs = function() {
                $.post('{{ route("facilities.startrepairs") }}', {
                    _token: "{{ csrf_token() }}"
                })
                .done(function(response) {
                    if (response.success) {
                        // Show success fadeBox
                        if (window.fadeBox) {
                            fadeBox('Repairs started successfully!');
                        }
                        // Reload wreck field data to show updated status
                        // Find the current space dock description and reload data
                        $('.technology.space_dock .description, .technology[data-technology="36"] .description').each(function() {
                            loadWreckFieldDataIntoDescription($(this));
                        });
                    } else {
                        if (window.errorBox) {
                            errorBoxDecision('Error', response.message || 'Error starting repairs', 'OK', null, null);
                        } else {
                            alert(response.message || 'Error starting repairs');
                        }
                    }
                })
                .fail(function() {
                    if (window.errorBox) {
                        errorBoxDecision('Error', 'Network error starting repairs', 'OK', null, null);
                    } else {
                        alert('Network error starting repairs');
                    }
                });
            };

            window.completeWreckFieldRepairs = function() {
                $.post('{{ route("facilities.completerepairs") }}', {
                    _token: "{{ csrf_token() }}"
                })
                .done(function(response) {
                    if (response.success) {
                        // Show success fadeBox
                        if (window.fadeBox) {
                            fadeBox('Repairs completed and ships collected successfully!');
                        }
                        // Reload wreck field data - should hide section since wreck field is gone
                        $('.technology.space_dock .description, .technology[data-technology="36"] .description').each(function() {
                            loadWreckFieldDataIntoDescription($(this));
                        });
                    } else {
                        if (window.errorBox) {
                            errorBoxDecision('Error', response.message || 'Error completing repairs', 'OK', null, null);
                        } else {
                            alert(response.message || 'Error completing repairs');
                        }
                    }
                })
                .fail(function() {
                    if (window.errorBox) {
                        errorBoxDecision('Error', 'Network error completing repairs', 'OK', null, null);
                    } else {
                        alert('Network error completing repairs');
                    }
                });
            };

            window.collectRepairedShips = function() {

                // Get fresh CSRF token from meta tag
                const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token
                    }
                });

                $.ajax({
                    url: '{{ route("facilities.completerepairs") }}',
                    method: 'POST',
                    data: {
                        _token: token
                    },
                    dataType: 'json',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }
                })
                .done(function(response) {
                    if (response.success) {
                        // Show success fadeBox
                        if (window.fadeBox) {
                            fadeBox('All ships have been put back into service');
                        }
                        // Auto-refresh page with space dock open
                        sessionStorage.setItem('triggerSpaceDock', 'true');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000); // 2 second delay to let user see the message
                    } else {
                        if (window.errorBox) {
                            errorBoxDecision('Error', response.message || 'Error collecting ships', 'OK', null, null);
                        } else {
                            alert(response.message || 'Error collecting ships');
                        }
                    }
                })
                .fail(function() {
                    if (window.errorBox) {
                        errorBoxDecision('Error', 'Network error collecting ships', 'OK', null, null);
                    } else {
                        alert('Network error collecting ships');
                    }
                });
            };

            window.burnWreckField = function() {
                if (confirm('{{ __("Are you sure you want to burn up this wreck field? This action cannot be undone.") }}')) {
                    $.post('{{ route("facilities.burnwreckfield") }}', {
                        _token: "{{ csrf_token() }}"
                    })
                    .done(function(response) {
                        if (response.success) {
                            // Show success fadeBox
                            if (window.fadeBox) {
                                fadeBox('Wreck field burned successfully!');
                            }
                            // Reload wreck field data - should hide section since wreck field is gone
                            $('.technology.space_dock .description, .technology[data-technology="36"] .description').each(function() {
                                loadWreckFieldDataIntoDescription($(this));
                            });
                        } else {
                            if (window.errorBox) {
                                errorBoxDecision('Error', response.message || 'Error burning wreck field', 'OK', null, null);
                            } else {
                                alert(response.message || 'Error burning wreck field');
                            }
                        }
                    })
                    .fail(function() {
                        if (window.errorBox) {
                            errorBoxDecision('Error', 'Network error burning wreck field', 'OK', null, null);
                        } else {
                            alert('Network error burning wreck field');
                        }
                    });
                }
            };


            function createRepairLayerOverlay(wreckFieldData) {

                // Create the overlay HTML structure
                var overlayHtml = `
                    <div class="overlayDiv repairlayer" style="width: 656px; background: url('{{ asset('img/facilities/e9f54b10dc4e1140ce090106d2f528.jpg') }}') 100% 0% rgb(0, 0, 0);">
                          <div id="repairlayer" style="">
                            <div class="repairableShips">
                                <span>${wreckFieldData.is_repairing ? 'There is no wreckage at this position.' : 'Wreckages can be repaired in the Space Dock.'}</span>
                                <div class="clearfix"></div>
                                <br>
                                <hr>
                `;

                if (wreckFieldData.is_repairing) {
                    overlayHtml += `
                        <h3>Ships being repaired:</h3>
                        <div class="ships_wrapper clearfix">
                    `;

                    // Calculate real-time repair progress
                    let totalRepairProgress = 0;
                    if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time >= 0) {
                        // Use remaining repair time to calculate progress
                        const totalRepairTime = wreckFieldData.repair_completion_time && wreckFieldData.repair_started_at ?
                            (new Date(wreckFieldData.repair_completion_time).getTime() - new Date(wreckFieldData.repair_started_at).getTime()) / 1000 :
                            wreckFieldData.remaining_repair_time * 2; // Fallback estimate

                        const elapsedTime = totalRepairTime - wreckFieldData.remaining_repair_time;
                        totalRepairProgress = Math.min(100, Math.max(0, (elapsedTime / totalRepairTime) * 100));

                    }

                    // Map machine names to OGame shipyard CSS classes
                    const shipClassMap = {
                        'light_fighter': 'fighterLight',
                        'heavy_fighter': 'fighterHeavy',
                        'cruiser': 'cruiser',
                        'battleship': 'battleship',
                        'colony_ship': 'colonyShip',
                        'recycler': 'recycler',
                        'espionage_probe': 'espionageProbe',
                        'bomber': 'bomber',
                        'destroyer': 'destroyer',
                        'deathstar': 'deathstar',
                        'battlecruiser': 'battlecruiser',
                        'small_cargo': 'cargoSmall',
                        'large_cargo': 'cargoLarge',
                        'solar_satellite': 'solarSatellite'
                    };

                    // Add ship icons for each ship type with correct shipyard images
                    if (wreckFieldData.ship_data && Array.isArray(wreckFieldData.ship_data)) {
                        for (const ship of wreckFieldData.ship_data) {
                            if (ship.quantity > 0) {
                                // Calculate real-time repaired count with NaN check
                                let repairedCount;
                                if (wreckFieldData.is_repairing) {
                                    const progress = isNaN(totalRepairProgress) ? 0 : totalRepairProgress;
                                    repairedCount = Math.floor((ship.quantity * progress) / 100);
                                } else {
                                    repairedCount = Math.floor((ship.quantity * (ship.repair_progress || 0)) / 100);
                                }

                                const shipName = ship.machine_name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                const shipClass = shipClassMap[ship.machine_name] || '';


                                // Copy exact shipyard icon structure with space dock overlay
                                overlayHtml += `
                                    <div class="tooltipHTML fleft ships" title="${shipName}|${repairedCount} / ${ship.quantity}">
                                        <span class="icon sprite sprite_small ${shipClass}">
                                            <span class="ecke">
                                                <span class="level">${repairedCount}/${ship.quantity}</span>
                                            </span>
                                        </span>
                                    </div>
                                `;
                            }
                        }
                    } else {
                        overlayHtml += '<p>No ship data available</p>';
                    }

                    overlayHtml += `
                                    <div class="clearfix"></div>
                                    <br>
                    `;

                    // Add repair time countdown if repairs are in progress
                    if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time > 0) {
                        // Format time to only show hours, minutes, seconds (no days)
                        const remainingTime = wreckFieldData.remaining_repair_time;
                        const hours = Math.floor(remainingTime / 3600);
                        const minutes = Math.floor((remainingTime % 3600) / 60);
                        const seconds = remainingTime % 60;
                        const timeDisplay = `${hours}h ${minutes}m ${seconds}s`;

                        overlayHtml += `
                            <p>Repair time remaining: <span id="repairTimeCountDownForRepairOverlay" data-duration="${remainingTime}">${timeDisplay}</span></p>
                        `;
                    }
                    // Add auto-return message if repairs are completed but not collected
                    else if (wreckFieldData.is_completed) {
                        const repairCompletionTime = new Date(wreckFieldData.repair_completion_time);
                        const autoReturnTime = new Date(repairCompletionTime.getTime() + (3 * 24 * 60 * 60 * 1000)); // 3 days from repair completion
                        const formattedDate = autoReturnTime.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        }).replace(/\//g, '.');
                        const formattedTime = autoReturnTime.toLocaleTimeString('en-GB', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        overlayHtml += `
                            <p>Your last ships will be automatically returned to service on ${formattedDate} ${formattedTime}.</p>
                        `;
                    }

                    // Add collect button if ships are ready (allow partial collection after 30 minutes)
                    const repairProgress = wreckFieldData.repair_progress || 0;
                    const minRepairTime = 30 * 60; // 30 minutes
                    var timeSinceRepairStart = 0;
                    if (wreckFieldData.repair_started_at) {
                        const repairStartTime = new Date(wreckFieldData.repair_started_at);
                        timeSinceRepairStart = Math.floor((Date.now() - repairStartTime) / 1000);
                    }
                    const minTimePassed = timeSinceRepairStart >= minRepairTime;

                    // Check if there are any late-added ships (ships added after repairs started)
                    // If yes, collection is completely disabled
                    const hasLateAddedShips = (wreckFieldData.ship_data || []).some(ship => ship.late_added === true);

                    const totalRepaired = wreckFieldData.ship_data.reduce((sum, ship) => {
                        return sum + Math.floor((ship.quantity * repairProgress) / 100);
                    }, 0);

                    // Enable collection after 30 minutes if any ships are repaired AND no late-added ships
                    if (totalRepaired > 0 && minTimePassed && !hasLateAddedShips) {
                        overlayHtml += `
                            <div class="btn btn_dark fright wreckfield-collect-btn-overlay">
                                <input type="button" class="middlemark wreckfield-collect-btn-overlay-input" value="Put ships that are already repaired back into service" onclick="collectRepairedShips(); closeOverlay();">
                            </div>
                        `;
                    }
                }

                overlayHtml += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Create and show the overlay dialog (using fallback method to avoid jQuery UI dialog issues)
                if (false) {
                    // Use jQuery UI dialog if available
                    $('<div>' + overlayHtml + '</div>').dialog({
                        title: 'Space Dock',
                        width: 656,
                        modal: true,
                        resizable: false,
                        dialogClass: 'repairlayer-dialog',
                        open: function() {
                            // Initialize custom countdown timer for repair time
                            var $countdown = $('#repairTimeCountDownForRepairOverlay');
                            if ($countdown.length) {
                                var duration = $countdown.data('duration');

                                // Clear any existing interval
                                var existingInterval = $countdown.data('countdownInterval');
                                if (existingInterval) {
                                    clearInterval(existingInterval);
                                }

                                // Create new countdown interval
                                var interval = setInterval(function() {
                                    if (duration <= 0) {
                                        $countdown.text('0h 0m 0s');
                                        clearInterval(interval);
                                        return;
                                    }

                                    // Calculate hours, minutes, seconds
                                    var hours = Math.floor(duration / 3600);
                                    var minutes = Math.floor((duration % 3600) / 60);
                                    var seconds = duration % 60;

                                    $countdown.text(hours + 'h ' + minutes + 'm ' + seconds + 's');
                                    duration--;

                                    // Update the data-duration attribute
                                    $countdown.data('duration', duration);
                                }, 1000);

                                // Store the interval reference
                                $countdown.data('countdownInterval', interval);
                            }
                        },
                        close: function() {
                            $(this).dialog('destroy').remove();
                        }
                    });
                } else {
                    // Fallback: create a simple modal overlay
                    var overlay = $('<div id="wreckFieldDetailsOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">' +
                        '<div style="width: 656px; background: #000; border: 1px solid #333; padding: 20px;">' +
                        '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">' +
                        '<h2 style="color: #fff; margin: 0;">Space Dock</h2>' +
                        '<button onclick="closeOverlay()" style="background: #333; color: #fff; border: 1px solid #555; padding: 5px 10px; cursor: pointer;">✕</button>' +
                        '</div>' +
                        overlayHtml +
                        '</div>' +
                        '</div>');

                    $('body').append(overlay);
                }
            }

            window.closeOverlay = function() {
                $('#wreckFieldDetailsOverlay').remove();
                $('.ui-dialog').remove();
            };

            function loadWreckFieldDataIntoDescription($description) {

                // Remove any existing wreck field section first
                $description.find('#wreckFieldSection').remove();

                $.get('{{ route("facilities.wreckfieldstatus") }}', {
                    _token: "{{ csrf_token() }}"
                })
                .done(function(response) {
                    if (response.debug_info) {
                    }
                    if (response.success) {
                        updateWreckFieldDisplayInDescription($description, response.wreckField);
                    } else {
                        // Don't show alert for normal "no wreck field" case
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                });
            }

            function updateWreckFieldDisplayInDescription($description, wreckFieldData) {

                // Clear existing timers
                clearWreckFieldTimers();

                // Remove any existing wreck field elements first to ensure clean state
                $description.find('.complex_action').remove();
                $description.find('#wreckFieldSection').remove();

                if (!wreckFieldData) {
                    // No wreck field available - don't add anything to description
                    return;
                }

                // Calculate totals for display (use real-time progress like overlay)
                const totalShips = wreckFieldData.ship_data.reduce((sum, ship) => sum + ship.quantity, 0);
                const repairProgress = wreckFieldData.repair_progress || 0;
                const repairedShips = wreckFieldData.ship_data.reduce((sum, ship) => {
                    return sum + Math.floor((ship.quantity * repairProgress) / 100);
                }, 0);

                // Create the exact HTML structure from OGame
                // Only show "repairing" interface if status is actually 'repairing', not just is_repairing flag
                var isActiveWreckField = wreckFieldData.can_repair && !wreckFieldData.is_completed && wreckFieldData.status !== 'repairing';
                var $complexAction = $('<div class="complex_action ' + (isActiveWreckField ? 'wreckfield_norepairorder' : 'nowreckfield_repairorder') + '"></div>');

                var $innerDescription = $('<div id="description"></div>');

                if (!isActiveWreckField) {
                    // When repairing: create the exact OGame structure
                    var $wreckFieldSpan = $('<span class="wreck_field" style="font-size: 7px !important;">There is no wreckage at this position.</span>');
                    var $separator = $('<hr>');
                    var $repairOrder = $('<span class="repair_order"></span>');

                    // Create ship details for tooltip (matching popup calculation)
                  var shipDetails = '';
                  if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time >= 0 && wreckFieldData.repair_completion_time && wreckFieldData.repair_started_at) {
                      // Calculate real-time progress for during repairs
                      const totalRepairTime = (new Date(wreckFieldData.repair_completion_time).getTime() - new Date(wreckFieldData.repair_started_at).getTime()) / 1000;
                      const elapsedTime = totalRepairTime - wreckFieldData.remaining_repair_time;
                      const currentProgress = Math.min(100, Math.max(0, (elapsedTime / totalRepairTime) * 100));

                      for (const ship of wreckFieldData.ship_data) {
                          if (ship.quantity > 0) {
                              const repairedCount = Math.floor(ship.quantity * (currentProgress / 100));
                              // Format machine name to readable name (replace underscores with spaces and capitalize)
                              let shipName = ship.machine_name.replace(/_/g, ' ');
                              shipName = shipName.replace(/\b\w/g, l => l.toUpperCase());
                              shipDetails += `${shipName}: ${repairedCount} / ${ship.quantity}<br>`;
                          }
                      }
                  } else {
                      // Before repairs: use individual ship progress or 0
                      for (const ship of wreckFieldData.ship_data) {
                          if (ship.quantity > 0) {
                              const repairedCount = wreckFieldData.is_repairing ? Math.floor((ship.quantity * (ship.repair_progress || 0)) / 100) : 0;
                              // Format machine name to readable name (replace underscores with spaces and capitalize)
                              let shipName = ship.machine_name.replace(/_/g, ' ');
                              shipName = shipName.replace(/\b\w/g, l => l.toUpperCase());
                              shipDetails += `${shipName}: ${repairedCount} / ${ship.quantity}<br>`;
                          }
                      }
                  }

                  // Create repair time timer element
                  let repairTimerElement = '';
                  if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time > 0) {
                      const remainingTime = wreckFieldData.remaining_repair_time;
                      const hours = Math.floor(remainingTime / 3600);
                      const minutes = Math.floor((remainingTime % 3600) / 60);
                      const seconds = remainingTime % 60;
                      repairTimerElement = `<span id="complexActionRepairTimer" data-duration="${remainingTime}" style="font-size: 7px; font-weight: bold; color: white;">${hours}h ${minutes}m ${seconds}s</span>`;
                  }

                  var $shipsSpan = $(`
                        <span class="ships" style="font-size: 7px;">
                            ${repairTimerElement ? `Repair time remaining: ${repairTimerElement} ` : ''}
                            Repaired Ships: <a href="javascript:void(0);" class="value tooltip" onclick="openWreckFieldDetailsPopup(); return false;" style="font-size: 7px; font-weight: bold;">${repairedShips} / ${totalShips}</a>
                        </span>
                    `);

                  // Set the tooltip content with ship details
                  $shipsSpan.find('a').attr('title', shipDetails);

                    $repairOrder.append($shipsSpan);

                    var $wreckfieldBtns = $('<div id="wreckfield-btns"></div>');

                    // Check collection constraints (same as backend)
                    const minRepairTime = 30 * 60; // 30 minutes in seconds
                    const repairProgress = wreckFieldData.repair_progress || 0;

                    // Check if at least 30 minutes have passed since repairs started
                    var timeSinceRepairStart = 0;
                    if (wreckFieldData.repair_started_at) {
                        const repairStartTime = new Date(wreckFieldData.repair_started_at);
                        timeSinceRepairStart = Math.floor((Date.now() - repairStartTime) / 1000);
                    }

                    const minTimePassed = timeSinceRepairStart >= minRepairTime;
                    const hasRepairedShips = repairedShips > 0;

                    // Check if there are any late-added ships (ships added after repairs started)
                    // If yes, collection is completely disabled - players must wait for auto-return
                    const hasLateAddedShips = (wreckFieldData.ship_data || []).some(ship => ship.late_added === true);

                    // Complex action: Only enable when repairs are 100% complete AND no late-added ships
                    const repairsComplete = wreckFieldData.is_completed || repairProgress >= 100;
                    const collectEnabled = repairsComplete && hasRepairedShips && !hasLateAddedShips;

                    // Create tooltip explaining why button is disabled
                    var collectButtonTooltip = '';
                    if (!collectEnabled) {
                        if (hasLateAddedShips) {
                            collectButtonTooltip = 'Ships added during ongoing repairs cannot be collected manually. You must wait until all repairs are automatically completed.';
                        } else if (wreckFieldData.is_repairing) {
                            collectButtonTooltip = 'Repairs are still in progress. Use the Details window for partial collection.';
                        } else if (!hasRepairedShips) {
                            collectButtonTooltip = 'No ships repaired yet';
                        } else {
                            collectButtonTooltip = 'Repairs must be completed to collect ships from here.';
                        }
                    }

                    const collectButtonStyle = collectEnabled ? '' : 'opacity: 0.5; cursor: not-allowed;';
                    const collectButtonOnclick = collectEnabled ? 'onclick="collectRepairedShips()"' : 'disabled=""';

                    var $collectBtn = $(`
                        <button class="wreckfield-collect-btn" ${collectButtonOnclick}>
                            <span class="btn btn_dark tooltip middlemark" title="${collectButtonTooltip}" style="${collectButtonStyle}">Collect</span>
                        </button>
                    `);
                    var $detailsBtn = $('<a class="btn btn_dark undermark fright" href="javascript:void(0);" onclick="openWreckFieldDetailsPopup(); return false;">Details</a>');

                    $wreckfieldBtns.append($collectBtn);
                    $wreckfieldBtns.append($detailsBtn);

                    // Aggressive CSS override to force text clickability
                    setTimeout(function() {
                        // Add very aggressive CSS rules
                        $('<style>')
                            .prop('type', 'text/css')
                            .html('\
                                .btn.btn_dark.undermark.fright, \
                                button.recomission { \
                                    pointer-events: auto !important; \
                                    position: relative !important; \
                                    z-index: 1000 !important; \
                                } \
                                .btn.btn_dark.undermark.fright:before, \
                                .btn.btn_dark.undermark.fright:after, \
                                button.recomission:before, \
                                button.recomission:after { \
                                    pointer-events: none !important; \
                                } \
                                .btn.btn_dark.undermark.fright *, \
                                button.recomission * { \
                                    pointer-events: auto !important; \
                                    position: relative !important; \
                                    z-index: 1001 !important; \
                                }')
                            .appendTo('head');

                        // Force inline styles on the button
                        $detailsBtn.css({
                            'pointer-events': 'auto !important',
                            'position': 'relative !important',
                            'z-index': '1000 !important'
                        });

                        // Force inline styles on all children
                        $detailsBtn.find('*').css({
                            'pointer-events': 'auto !important',
                            'position': 'relative !important',
                            'z-index': '1001 !important'
                        });

                        // Apply the same fix to the Collect button
                        $collectBtn.css({
                            'pointer-events': 'auto !important',
                            'position': 'relative !important',
                            'z-index': '1000 !important'
                        });

                        $collectBtn.find('*').css({
                            'pointer-events': 'auto !important',
                            'position': 'relative !important',
                            'z-index': '1001 !important'
                        });
                    }, 50);

                    $innerDescription.append($wreckFieldSpan);
                    $innerDescription.append($separator);
                    $innerDescription.append($repairOrder);
                    $innerDescription.append($wreckfieldBtns);

                } else {
                    // When active wreck field (not being repaired): create the proper active wreck field interface
                    var timeRemaining = wreckFieldData.time_remaining || 0;

                    var timeDisplay = '3d 0h 0m'; // Default to 72 hours if time is 0
                    if (timeRemaining > 0) {
                        var days = Math.floor(timeRemaining / 86400);
                        var hours = Math.floor((timeRemaining % 86400) / 3600);
                        var minutes = Math.floor((timeRemaining % 3600) / 60);
                        timeDisplay = days + 'd ' + hours + 'h ' + minutes + 'm';
                    } else {
                    }

                    // Create the wreck field span with proper structure
                    var $wreckFieldSpan = $('<span class="wreck_field" style="font-size: 7px !important;"></span>');
                    $wreckFieldSpan.text('Wreckage burns up in: ');
                    var $timeElement = $('<time id="burnUpCountDownForStationScreen" class="value countdown" datetime="P' + timeDisplay.replace(/\s/g, '') + '" style="font-size: 11px !important; font-weight: bold;">' + timeDisplay + '</time>');
                    $wreckFieldSpan.append($timeElement);

                    // Add Details link
                    var $detailsLink = $('<a href="javascript:void(0);" class="fright tooltip" onclick="openWreckFieldDetailsPopup(); return false;">Details</a>');

                    var $separator = $('<hr>');

                    // Create tooltip content for hover
                    var tooltipContent = '';
                    if (wreckFieldData.ship_data && wreckFieldData.ship_data.length > 0) {
                        tooltipContent = wreckFieldData.ship_data.map(function(ship) {
                            var shipName = ship.machine_name.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                            return shipName + ': ' + ship.quantity;
                        }).join('<br>');
                    } else {
                        tooltipContent = 'No ships available for repair';
                    }

                    // Use the same tooltip system as shipyard - simple title attribute with <br/> tags
                    var $shipLink = $('<a href="javascript:void(0);" class="value tooltip hideTooltipOnMouseenter js_hideTipOnMobile" title="' + tooltipContent.replace(/<br>/g, '<br/>') + '" style="font-size: 8px !important; font-weight: bold;">' + totalShips + ' Ships</a>');

                    var $repairOrder = $('<span class="repair_order" style="font-size: 7px !important;">Repairable Ships: <i></i> in <time class="value" datetime="PT32M" style="font-size: 8px !important; font-weight: bold;">32m</time></span>');
                    $repairOrder.find('i').append($shipLink);

                    var $wreckfieldBtns = $('<div id="wreckfield-btns"></div>');
                    var $burnUpBtn = $('<a href="javascript:void(0);" class="btn btn_dark overmark burn_up" onclick="confirmBurnUpWreckField();">Leave to burn up</a>');
                    var $repairBtn = $('<a href="javascript:void(0);" class="btn btn_dark undermark repair" onclick="startWreckFieldRepairs();">Start repairs</a>');

                    $wreckfieldBtns.append($burnUpBtn);
                    $wreckfieldBtns.append($repairBtn);

                    // Add elements to inner description in correct order
                    $innerDescription.append($wreckFieldSpan);
                    $innerDescription.append($detailsLink);
                    $innerDescription.append($separator);
                    $innerDescription.append($repairOrder);
                    $innerDescription.append($wreckfieldBtns);

                    // Start countdown timer if time remaining
                    if (timeRemaining > 0) {
                        startBurnUpCountdown(timeRemaining);
                    }
                }

                $complexAction.append($innerDescription);

                // Insert the complex_action BEFORE the txt_box (this is the key fix!)
                var $txtBox = $description.find('.txt_box');
                $txtBox.before($complexAction);

                // Start countdown timers
                if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time > 0) {
                    // Add repair timer for complex action
                    const $repairTimer = $('#complexActionRepairTimer');
                    if ($repairTimer.length) {
                        let duration = $repairTimer.data('duration');
                        const repairTimerInterval = setInterval(function() {
                            if (duration <= 0) {
                                $repairTimer.text('0h 0m 0s');
                                clearInterval(repairTimerInterval);
                                return;
                            }

                            const hours = Math.floor(duration / 3600);
                            const minutes = Math.floor((duration % 3600) / 60);
                            const seconds = duration % 60;

                            $repairTimer.text(`${hours}h ${minutes}m ${seconds}s`);
                            duration--;
                        }, 1000);
                    }
                }
            }

            function clearWreckFieldTimers() {
                if (wreckFieldUpdateInterval) {
                    clearInterval(wreckFieldUpdateInterval);
                    wreckFieldUpdateInterval = null;
                }
                if (burnUpCountdownInterval) {
                    clearInterval(burnUpCountdownInterval);
                    burnUpCountdownInterval = null;
                }
            }

            // Function to start burn up countdown
            function startBurnUpCountdown(timeRemaining) {
                if (burnUpCountdownInterval) {
                    clearInterval(burnUpCountdownInterval);
                }

                burnUpCountdownInterval = setInterval(function() {
                    var $countdownElement = $('#burnUpCountDownForStationScreen');
                    if ($countdownElement.length > 0 && timeRemaining > 0) {
                        timeRemaining--;
                        var days = Math.floor(timeRemaining / 86400);
                        var hours = Math.floor((timeRemaining % 86400) / 3600);
                        var minutes = Math.floor((timeRemaining % 3600) / 60);
                        var timeDisplay = days + 'd ' + hours + 'h ' + minutes + 'm';
                        $countdownElement.text(timeDisplay);
                    } else {
                        clearInterval(burnUpCountdownInterval);
                        // Reload wreck field data when time expires
                        loadWreckFieldDataForSpaceDock();
                    }
                }, 1000);
            }

            // Function to confirm burn up
            function confirmBurnUpWreckField() {
                errorBoxDecision(
                    "Leave to burn up",
                    "The wreckage will descend into the planet's atmosphere and burn up. Once struck, a repair will no longer be possible. Are you sure you want to burn up the wreckage?",
                    "yes",
                    "No",
                    function() {
                        burnUpWreckField();
                    },
                    function() {}
                );
            }

            // Function to start repairs directly from space dock
            function startWreckFieldRepairs() {
                startRepairs();
            }

            // Function to burn up wreck field
            function burnUpWreckField() {
                $.ajax({
                    url: "{{ route('facilities.burnwreckfield') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            fadeBox(response.message, false);
                            // Clean up wreck field interface immediately and reload page
                            setTimeout(function() {
                                // Clear timers and remove wreck field elements directly
                                clearWreckFieldTimers();
                                $('.technology_description').find('.complex_action').remove();
                                $('.technology_description').find('#wreckFieldSection').remove();
                                // Reload the page to refresh everything
                                location.reload();
                            }, 500);
                        } else {
                            fadeBox(response.message, true);
                        }
                    },
                    error: function(xhr, status, error) {
                        fadeBox('Error burning up wreck field', true);
                    }
                });
            }


            function startCountdownTimer(elementId, seconds, callback) {
                var $element = $('#' + elementId);
                if (!$element.length) return;

                // Clear any existing interval for this element
                var existingInterval = $element.data('countdownInterval');
                if (existingInterval) {
                    clearInterval(existingInterval);
                }

                // Create our own simple countdown that doesn't rely on the existing system
                var timeLeft = seconds;
                $element.text(formatTime(timeLeft));

                var interval = setInterval(function() {
                    timeLeft--;
                    if (timeLeft <= 0) {
                        $element.text('done');
                        clearInterval(interval);
                        $element.removeData('countdownInterval');
                        if (callback) {
                            callback();
                        }
                    } else {
                        $element.text(formatTime(timeLeft));
                    }
                }, 1000);

                // Store the interval so we can clear it later
                $element.data('countdownInterval', interval);
            }

            // Helper function to format time like the main countdown
            function formatTime(seconds) {
                if (seconds <= 0) return 'done';

                var days = Math.floor(seconds / 86400);
                var hours = Math.floor((seconds % 86400) / 3600);
                var minutes = Math.floor((seconds % 3600) / 60);
                var secs = seconds % 60;

                return days + 'd ' + hours + 'h ' + minutes + 'm ' + secs + 's';
            }
        </script>
        {{-- Last building slot warning --}}
        @include ('ingame.shared.buildings.last-building-slot-warning', ['planet' => $planet])
    </div>

    <div id="technologydetailscomponent" class="technologydetails injectedComponent parent facilities">
        <script type="text/javascript">
            var loca = {"LOCA_ALL_NOTICE":"Reference","LOCA_ALL_NETWORK_ATTENTION":"Caution","locaDemolishStructureQuestion":"Really downgrade TECHNOLOGY_NAME by one level?","LOCA_ALL_YES":"yes","LOCA_ALL_NO":"No","LOCA_LIFEFORM_BONUS_CAP_REACHED_WARNING":"One or more associated bonuses is already maxed out. Do you want to continue construction anyway?"};

            var technologyDetailsEndpoint = "{{ route('facilities.ajax') }}";
            var selectCharacterClassEndpoint = "#TODO_page=ingame&component=characterclassselection&characterClassId=CHARACTERCLASSID&action=selectClass&ajax=1&asJson=1";
            var deselectCharacterClassEndpoint = "#TODO_page=ingame&component=characterclassselection&characterClassId=CHARACTERCLASSID&action=deselectClass&ajax=1&asJson=1";

            var technologyDetails = new TechnologyDetails({
                technologyDetailsEndpoint: technologyDetailsEndpoint,
                selectCharacterClassEndpoint: selectCharacterClassEndpoint,
                deselectCharacterClassEndpoint: deselectCharacterClassEndpoint,
                loca: loca
            })
            technologyDetails.init()
        </script>

        <style>
        /* Custom styling for wreck field collect button */
        #technologydetails > .complex_action.nowreckfield_repairorder > button.wreckfield-collect-btn,
        #technologydetails > .complex_action.wreckfield_repairorder > button.wreckfield-collect-btn {
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        #technologydetails > .complex_action.nowreckfield_repairorder > button.wreckfield-collect-btn:hover,
        #technologydetails > .complex_action.wreckfield_repairorder > button.wreckfield-collect-btn:hover {
            background: transparent;
            border: none;
        }

        #technologydetails > .complex_action.nowreckfield_repairorder > button.wreckfield-collect-btn:disabled,
        #technologydetails > .complex_action.wreckfield_repairorder > button.wreckfield-collect-btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        #technologydetails > .complex_action.nowreckfield_repairorder > button.wreckfield-collect-btn:disabled span,
        #technologydetails > .complex_action.wreckfield_repairorder > button.wreckfield-collect-btn[disabled] span {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Also style overlay buttons */
        .wreckfield-collect-btn-overlay input:disabled,
        .wreckfield-collect-btn-overlay-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        </style>
    </div>
    {{-- openTech querystring parameter handling --}}
    @include ('ingame.shared.technology.open-tech', ['open_tech_id' => $open_tech_id])
@endsection
