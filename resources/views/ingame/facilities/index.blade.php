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
                // For now, just show an alert - this could be expanded to show a modal with ship details
                alert('Wreck field details feature coming soon!');
                return false;
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
                    var $collectBtn = $(`
                        <button class="recomission" onclick="collectRepairedShips()">
                            <span class="btn btn_dark tooltip middlemark" title="">Collect</span>
                        </button>
                    `);
                    var $detailsBtn = $('<a class="btn btn_dark undermark fright overlay" href="#" onclick="showWreckFieldDetails()" data-overlay-title="Space Dock" data-overlay-class="repairlayer" data-overlay-width="656px">Details</a>');

                    $wreckfieldBtns.append($collectBtn);
                    $wreckfieldBtns.append($detailsBtn);

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
