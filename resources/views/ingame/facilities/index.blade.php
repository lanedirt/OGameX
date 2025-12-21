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
            <header data-anchor="technologyDetails" data-technologydetails-size="large" style="background-image:url({{ asset('img/headers/facilities/' . $header_filename) }}.jpg);">
                <h2>Facilities - {{ $planet_name }}</h2>
                @if (isset($jump_gate_level) && $jump_gate_level > 0)
                    <div id="slot01" class="slot">
                        <a href="{{ route('jumpgate.index') }}" class="overlay" data-overlay-title="@lang('Use Jump Gate')">@lang('Jump Gate')</a>
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
                            @elseif ($building->ship_or_defense_in_progress && ( $building->object->machine_name == 'shipyard' || $building->object->machine_name == 'nano_factory' ) )
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
                            @elseif ($building->ship_or_defense_in_progress  && ( $building->object->machine_name == 'shipyard' || $building->object->machine_name == 'nano_factory' ) )
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
                        var scheduleBuildListEntryUrl = '{{ route('facilities.addbuildrequest.post') }}';
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

      // Helper function to format datetime for countdown
            function formatDateTime(seconds) {
                const days = Math.floor(seconds / 86400);
                const hours = Math.floor((seconds % 86400) / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                return `P${days}DT${hours}H${minutes}M${seconds % 60}S`;
            }

      // Wreck field functionality
            $(document).ready(function() {
                console.log('Wreck field functionality initialized');

                // Handle clicks on space dock technology directly
                $(document).on('click', '.technology.space_dock, .technology[data-technology="36"]', function(e) {
                    console.log('Click detected on space dock technology');
                    console.log('Target:', $(e.target));
                    console.log('Current element:', $(this));
                    console.log('Is upgrade button?', $(e.target).closest('button').length > 0);
                    console.log('Has upgrade class?', $(e.target).hasClass('upgrade'));

                    // Don't trigger if clicking the upgrade button or any button
                    if ($(e.target).closest('button').length === 0 && !$(e.target).hasClass('upgrade')) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Space dock clicked directly!');

                        // Use multiple approaches to detect when space dock details are loaded
                        checkForSpaceDockDetails();
                    }
                });

                // Debug: Check if technologydetails_content exists
                console.log('technologydetails_content exists:', $('#technologydetails_content').length);
                console.log('Space dock technologies exist:', $('.technology.space_dock, .technology[data-technology="36"]').length);
            });

            function checkForSpaceDockDetails() {
                console.log('Checking for space dock details...');

                // Check immediately
                checkAndLoadWreckField();

                // Check again after a short delay (since AJAX might be loading)
                setTimeout(checkAndLoadWreckField, 500);

                // Check one more time after a longer delay
                setTimeout(checkAndLoadWreckField, 1500);
            }

            function checkAndLoadWreckField() {
                console.log('Checking for space dock details in technology details content...');

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

                console.log('Found space dock details:', $spaceDockDetails.length);

                if ($spaceDockDetails.length > 0) {
                    console.log('Space dock details found! Looking for description...');

                    // Find the description div within the technology details
                    var $description = $spaceDockDetails.find('.description');
                    console.log('Found description elements:', $description.length);

                    if ($description.length > 0) {
                        console.log('Found Space dock description, loading wreck field data...');
                        loadWreckFieldDataIntoDescription($description);
                    } else {
                        console.log('Space dock description not found in technology details');
                        console.log('Available elements in space dock details:', $spaceDockDetails.find('*').map(function() { return this.tagName + '.' + this.className; }).get());
                        console.log('Space dock details HTML:', $spaceDockDetails.html());
                    }
                } else {
                    console.log('Space dock details not found yet');
                }
            }

            // Global functions for wreck field actions (called from onclick attributes)
            window.startWreckFieldRepairs = function() {
                $.post('{{ route('facilities.startrepairs') }}', {
                    _token: '{{ csrf_token() }}'
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
                $.post('{{ route('facilities.completerepairs') }}', {
                    _token: '{{ csrf_token() }}'
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
                $.post('{{ route('facilities.completerepairs') }}', {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success) {
                        // Show success fadeBox
                        if (window.fadeBox) {
                            fadeBox('Ships collected successfully!');
                        }
                        // Reload wreck field data - should hide section since wreck field is gone
                        $('.technology.space_dock .description, .technology[data-technology="36"] .description').each(function() {
                            loadWreckFieldDataIntoDescription($(this));
                        });
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
                    $.post('{{ route('facilities.burnwreckfield') }}', {
                        _token: '{{ csrf_token() }}'
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

            window.showWreckFieldDetails = function() {
                // Get current wreck field data
                $.get('{{ route('facilities.wreckfieldstatus') }}', {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    if (response.success && response.wreckField) {
                        createRepairLayerOverlay(response.wreckField);
                    } else {
                        if (window.errorBox) {
                            errorBoxDecision('Error', 'No wreck field data available', 'OK', null, null);
                        } else {
                            alert('No wreck field data available');
                        }
                    }
                })
                .fail(function() {
                    if (window.errorBox) {
                        errorBoxDecision('Error', 'Network error loading wreck field details', 'OK', null, null);
                    } else {
                        alert('Network error loading wreck field details');
                    }
                });
                return false;
            };

            function createRepairLayerOverlay(wreckFieldData) {
                console.log('Creating repair layer overlay with data:', wreckFieldData);
                console.log('Ship data:', wreckFieldData.ship_data);

                // Create the overlay HTML structure
                var overlayHtml = `
                    <div class="overlayDiv repairlayer" style="width: 656px; background: url('https://gf2.geo.gfsrv.net/cdn13/e9f54b10dc4e1140ce090106d2f528.jpg') 100% 0% rgb(0, 0, 0);">
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

                        console.log(`Repair progress: ${totalRepairProgress}%, Total time: ${totalRepairTime}s, Remaining: ${wreckFieldData.remaining_repair_time}s`);
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

                                console.log(`Ship: ${ship.machine_name}, Quantity: ${ship.quantity}, Repaired: ${repairedCount}, Class: ${shipClass}`);

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
                        console.log('No valid ship data found');
                        overlayHtml += '<p>No ship data available</p>';
                    }

                    overlayHtml += `
                                    <div class="clearfix"></div>
                                    <br>
                    `;

                    // Add completion time if repairs are in progress
                    if (wreckFieldData.remaining_repair_time > 0) {
                        const completionTime = new Date(Date.now() + wreckFieldData.remaining_repair_time * 1000);
                        const formattedDate = completionTime.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: '2-digit'
                        }).replace(/\//g, '.');
                        const formattedTime = completionTime.toLocaleTimeString('en-GB', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        overlayHtml += `
                            <p>Your last ships will be automatically returned to service on ${formattedDate} ${formattedTime}.</p>
                        `;
                    }

                    // Add the "Put ships that are already repaired back into service" button
                    overlayHtml += `
                        <div class="btn btn_dark fright reCommissionButton">
                            <input type="button" class="middlemark reCommissionButton" value="Put ships that are already repaired back into service" onclick="collectRepairedShips(); closeOverlay();">
                        </div>
                    `;

                    // Add collect button if ships are ready
                    const totalRepaired = wreckFieldData.ship_data.reduce((sum, ship) => {
                        return sum + Math.floor((ship.quantity * (ship.repair_progress || 0)) / 100);
                    }, 0);

                    if (totalRepaired > 0) {
                        overlayHtml += `
                            <div class="btn btn_dark fright reCommissionButton">
                                <input type="button" class="middlemark reCommissionButton" value="Put ships that are already repaired back into service" onclick="collectRepairedShips(); closeOverlay();">
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

                // Create and show the overlay dialog
                if (typeof $ !== 'undefined' && $.fn.dialog) {
                    // Use jQuery UI dialog if available
                    $('<div>' + overlayHtml + '</div>').dialog({
                        title: 'Space Dock',
                        width: 656,
                        modal: true,
                        resizable: false,
                        dialogClass: 'repairlayer-dialog',
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
                console.log('Loading wreck field data for Space Dock description...');
                console.log('Description structure:', $description.html());

                // Remove any existing wreck field section first
                $description.find('#wreckFieldSection').remove();

                $.get('{{ route('facilities.wreckfieldstatus') }}', {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    console.log('Wreck field response:', response);
                    if (response.debug_info) {
                        console.log('Server checking coordinates:', response.debug_info.planet_coordinates);
                        console.log('Server player ID:', response.debug_info.player_id);
                    }
                    if (response.success) {
                        updateWreckFieldDisplayInDescription($description, response.wreckField);
                    } else {
                        console.log('Error response:', response.message);
                        // Don't show alert for normal "no wreck field" case
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('AJAX error:', textStatus, errorThrown);
                });
            }

            function updateWreckFieldDisplayInDescription($description, wreckFieldData) {
                console.log('Updating wreck field display in description with data:', wreckFieldData);

                // Clear existing timers
                clearWreckFieldTimers();

                if (!wreckFieldData) {
                    // No wreck field available - don't add anything to description
                    console.log('No wreck field found - not adding section to description');
                    return;
                }

                // Remove any existing wreck field elements
                $description.find('.complex_action').remove();

                // Calculate totals for display
                const totalShips = wreckFieldData.ship_data.reduce((sum, ship) => sum + ship.quantity, 0);
                const repairedShips = wreckFieldData.ship_data.reduce((sum, ship) => {
                    return sum + Math.floor((ship.quantity * (ship.repair_progress || 0)) / 100);
                }, 0);

                // Create the exact HTML structure from OGame
                var $complexAction = $('<div class="complex_action ' + (wreckFieldData.is_repairing ? 'nowreckfield_repairorder' : 'wreckfield_norepairorder') + '"></div>');

                var $innerDescription = $('<div id="description"></div>');

                if (wreckFieldData.is_repairing) {
                    // When repairing: create the exact OGame structure
                    var $wreckFieldSpan = $('<span class="wreck_field" style="font-size: 7px !important;">There is no wreckage at this position.</span>');
                    var $separator = $('<hr>');
                    var $repairOrder = $('<span class="repair_order"></span>');

                    // Create ship details for tooltip
                  var shipDetails = '';
                  for (const ship of wreckFieldData.ship_data) {
                      if (ship.quantity > 0) {
                          const repairedCount = Math.floor((ship.quantity * (ship.repair_progress || 0)) / 100);
                          // Format machine name to readable name (replace underscores with spaces and capitalize)
                          let shipName = ship.machine_name.replace(/_/g, ' ');
                          shipName = shipName.replace(/\b\w/g, l => l.toUpperCase());
                          shipDetails += `${shipName}: ${repairedCount} / ${ship.quantity}<br>`;
                      }
                  }

                  var $shipsSpan = $(`
                        <span class="ships" style="font-size: 7px;">
                            Repaired Ships:
                            <a href="#" class="value tooltip overlay" title="" data-overlay-title="Space Dock" data-overlay-class="repairlayer" data-overlay-width="656px" style="font-size: 8px; font-weight: bold;">${repairedShips} / ${totalShips}</a>
                        </span>
                    `);

                  // Set the tooltip content with ship details
                  $shipsSpan.find('a').attr('title', shipDetails);

                    $repairOrder.append($shipsSpan);

                    var $wreckfieldBtns = $('<div id="wreckfield-btns"></div>');

                    // Only enable collect button if there are actually ships to collect
                    const collectEnabled = repairedShips > 0;
                    const collectButtonStyle = collectEnabled ? '' : 'opacity: 0.5; cursor: not-allowed;';
                    const collectButtonOnclick = collectEnabled ? 'onclick="collectRepairedShips()"' : 'disabled=""';

                    var $collectBtn = $(`
                        <button class="recomission" ${collectButtonOnclick}>
                            <span class="btn btn_dark tooltip middlemark" title="" style="${collectButtonStyle}">Collect</span>
                        </button>
                    `);
                    var $detailsBtn = $('<a class="btn btn_dark undermark fright" href="javascript:void(0);" onclick="showWreckFieldDetails(); return false;">Details</a>');

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
                    // When not repairing: create structure for active wreck field
                    // (You can fill this in later based on what the non-repairing OGame structure looks like)
                    var $wreckFieldSpan = $('<span class="wreck_field">Wreckage field is active</span>');
                    $innerDescription.append($wreckFieldSpan);
                }

                $complexAction.append($innerDescription);

                // Insert the complex_action BEFORE the txt_box (this is the key fix!)
                var $txtBox = $description.find('.txt_box');
                $txtBox.before($complexAction);

                // Start countdown timers
                if (wreckFieldData.is_repairing && wreckFieldData.remaining_repair_time > 0) {
                    // Add repair timer if needed
                }
            }

            function clearWreckFieldTimers() {
                if (wreckFieldUpdateInterval) {
                    clearInterval(wreckFieldUpdateInterval);
                    wreckFieldUpdateInterval = null;
                }

                // Clear our custom countdown timers
                var $countdowns = $('#wreckFieldCountdown, #repairCountdown');
                $countdowns.each(function() {
                    var interval = $(this).data('countdownInterval');
                    if (interval) {
                        clearInterval(interval);
                        $(this).removeData('countdownInterval');
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
    </div>
    {{-- openTech querystring parameter handling --}}
    @include ('ingame.shared.technology.open-tech', ['open_tech_id' => $open_tech_id])
@endsection
