@php /** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event_row */ @endphp

@if ($fleet_event_row->is_return_trip)
    <tr class="eventFleet" id="eventRow-{{ $fleet_event_row->id }}"
        data-mission-type="{{ $fleet_event_row->mission_type }}"
        data-return-flight="true"
        data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
    >
        <td class="countDown">
            <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="friendly textBeefy">
                load...
            </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltipHTML"
                 title="Own fleet | {{ $fleet_event_row->mission_label }} (R)" alt=""/>
        </td>

        <td class="originFleet">
            @switch ($fleet_event_row->destination_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DebrisField)
                    <figure class="planetIcon tf js_hideTipOnMobile" title="Debris Field"></figure>debris field
                    @break
                @case (OGame\Models\Enums\PlanetType::DeepSpace)
                    <span class="deep-space-text">{{ __('Deep space') }}</span>
                    @break
            @endswitch
        </td>
        <td class="coordsOrigin">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->destination_planet_coords->galaxy, 'system' => $fleet_event_row->destination_planet_coords->system]) }}"
               target="_top">
                [{{ $fleet_event_row->destination_planet_coords->asString() }}]
            </a>
        </td>

        <td class="detailsFleet">
            <span>@if ($fleet_event_row->fleet_intel_level->showsTotalCount()){{ $fleet_event_row->fleet_unit_count }}@endif</span>
        </td>
        <td class="icon_movement_reserve">
            <span @if ($fleet_event_row->fleet_intel_level->showsCompositionTooltip()) class="tooltip tooltipRight tooltipClose"
                  title="@include('ingame.fleetevents.partials.fleet-details-tooltip', ['fleet' => $fleet_event_row])"@endif>
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            @switch ($fleet_event_row->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DebrisField)
                    <figure class="planetIcon tf js_hideTipOnMobile" title="Debris Field"></figure>debris field
                    @break
                @case (OGame\Models\Enums\PlanetType::DeepSpace)
                    <span class="deep-space-text">{{ __('Deep space') }}</span>
                    @break
            @endswitch
        </td>
        <td class="destCoords">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->origin_planet_coords->galaxy, 'system' => $fleet_event_row->origin_planet_coords->system]) }}"
               target="_top">
                [{{ $fleet_event_row->origin_planet_coords->asString() }}]
            </a>
        </td>

        <td class="sendMail">
        </td>
        <td class="sendProbe">
        </td>
        <td class="sendMail">
            @if ($fleet_event_row->destination_player_id !== null && $fleet_event_row->destination_player_id !== auth()->id())
                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $fleet_event_row->destination_player_id }}" title="{{ $fleet_event_row->destination_player_name }}"><span class="icon icon_chat"></span></a>
            @endif
        </td>
    </tr>
@else
    <tr class="eventFleet" id="eventRow-{{ $fleet_event_row->id }}"
        data-mission-type="{{ $fleet_event_row->mission_type }}"
        data-return-flight="false"
        data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
    >
        <td class="countDown">
        <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="friendly textBeefy">
                    load...
        </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltipHTML"
                 title="Own fleet | {{ $fleet_event_row->mission_label }}" alt=""/>
        </td>

        <td class="originFleet">
            @switch ($fleet_event_row->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DebrisField)
                    <figure class="planetIcon tf js_hideTipOnMobile" title="Debris Field"></figure>debris field
                    @break
                @case (OGame\Models\Enums\PlanetType::DeepSpace)
                    <span class="deep-space-text">{{ __('Deep space') }}</span>
                    @break
            @endswitch
        </td>
        <td class="coordsOrigin">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->origin_planet_coords->galaxy, 'system' => $fleet_event_row->origin_planet_coords->system]) }}"
               target="_top">
                [{{ $fleet_event_row->origin_planet_coords->asString() }}]
            </a>
        </td>

        <td class="detailsFleet">
            <span>@if ($fleet_event_row->fleet_intel_level->showsTotalCount()){{ $fleet_event_row->fleet_unit_count }}@endif</span>
        </td>
        <td class="icon_movement">
            <span @if ($fleet_event_row->fleet_intel_level->showsCompositionTooltip()) class="tooltip tooltipRight tooltipClose"
                  title="@include('ingame.fleetevents.partials.fleet-details-tooltip', ['fleet' => $fleet_event_row])"@endif>
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            @switch ($fleet_event_row->destination_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DebrisField)
                    <figure class="planetIcon tf js_hideTipOnMobile" title="Debris Field"></figure>debris field
                    @break
                @case (OGame\Models\Enums\PlanetType::DeepSpace)
                    <span class="deep-space-text">{{ __('Deep space') }}</span>
                    @break
            @endswitch
        </td>
        <td class="destCoords">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->destination_planet_coords->galaxy, 'system' => $fleet_event_row->destination_planet_coords->system]) }}"
               target="_top">
                [{{ $fleet_event_row->destination_planet_coords->asString() }}]
            </a>
        </td>
        <td class="sendMail">
            @if ($fleet_event_row->is_recallable)
                <span class="reversal reversal_time" ref="{{ $fleet_event_row->id }}">
                    <a class="icon_link tooltipHTML recallFleet" data-fleet-id="{{ $fleet_event_row->real_mission_id }}"
                       title="Recall:| {{ \Carbon\Carbon::parse($fleet_event_row->active_recall_time)->format('d.m.Y') }}<br>
                                            {{ \Carbon\Carbon::parse($fleet_event_row->active_recall_time)->format('H:i:s') }}">
                        <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
                    </a>
                </span>
            @endif
        </td>
        <td class="sendProbe">
        </td>
        <td class="sendMail">
            @if ($fleet_event_row->destination_player_id !== null && $fleet_event_row->destination_player_id !== auth()->id())
                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $fleet_event_row->destination_player_id }}" title="{{ $fleet_event_row->destination_player_name }}"><span class="icon icon_chat"></span></a>
            @endif
        </td>
    </tr>
@endif

<script type="text/javascript">
    (function ($) {
        // Initialize countdown timer for this fleet mission row
        // When the countdown reaches zero, it calls the checkEvents endpoint to determine
        // which mission rows should be removed from the display (e.g., arrival missions entering hold time)

        // Set up AJAX to include CSRF token for Laravel authentication
        var wrappedCountdown = function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize the countdown timer
            // Parameters: countdown element, time remaining (seconds), parent container,
            //             checkEvents URL, mission IDs to check when timer expires
            new eventboxCountdown(
                $("#counter-eventlist-{{ $fleet_event_row->id }}"),
                    {{ $fleet_event_row->mission_time_arrival }} - {{ time() }},
                $("#eventListWrap"),
                "{{ route('fleet.eventlist.checkevents') }}",
                [{{ $fleet_event_row->id }}]
            );
        };
        wrappedCountdown();
    })(jQuery);
</script>
