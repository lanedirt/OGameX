@php
/** @var array $fleet_movements */
/** @var int $server_time */
/** @var int $scanner_player_id */
@endphp

<div id="phalanxEventContent">
    @if (empty($fleet_movements))
        <div style="padding: 20px; text-align: center;">No fleet movements detected at this location.</div>
    @else
        @foreach ($fleet_movements as $movement)
            {{-- Hidden tooltip content for this fleet movement --}}
            <div id="fleet-tooltip-{{ $movement['mission_id'] }}" style="display: none;">
                <div class="htmlTooltip">
                    <h1>Fleet details:</h1>
                    <div class="splitLine"></div>
                    <table cellpadding="0" cellspacing="0" class="fleetinfo">
                        <tr><th colspan="2">Ships:</th></tr>
                        @foreach ($movement['ships'] as $ship_type => $count)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $ship_type)) }}:</td>
                                <td class="value">{{ number_format($count, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="{{ $loop->even ? 'eventFleet even' : 'eventFleet odd' }}" id="eventRow-{{ $movement['mission_id'] }}" data-mission-type="{{ $movement['mission_type'] }}" data-return-flight="{{ $movement['is_return_trip'] ? 'true' : 'false' }}" data-arrival-time="{{ $movement['display_time'] }}">
                <ul>
                    <li class="countDown">
                        <span class="friendly textBeefy" id="counter-phalanx-{{ $movement['mission_id'] }}">Loading...</span>
                    </li>
                    <li class="arrivalTime">{{ date('H:i:s', $movement['display_time']) }} Clock</li>
                    <li class="descFleet">{{ $movement['fleet_direction'] }}</li>
                    <li class="missionFleet">
                        <img src="/img/fleet/{{ $movement['mission_type'] }}.gif">
                        <span>{{ $movement['mission_type_name'] }}{{ $movement['is_return_trip'] ? ' (R)' : '' }}</span>
                    </li>
                    <li class="originFleet">
                        <figure class="planetIcon planet"></figure>
                        @if (!empty($movement['origin']['planet_name']))
                            <span class="planetName">{{ $movement['origin']['planet_name'] }}</span>
                        @endif
                    </li>
                    <li class="coordsOrigin">
                        <a class="dark_highlight_tablet" href="{{ route('galaxy.index', ['galaxy' => $movement['origin']['galaxy'], 'system' => $movement['origin']['system']]) }}" target="_top">
                            [{{ $movement['origin']['galaxy'] }}:{{ $movement['origin']['system'] }}:{{ $movement['origin']['position'] }}]
                        </a>
                    </li>
                    <li class="detailsFleet">
                        <span class="dark_highlight_tablet tooltipClose tooltipRight tooltipRel" rel="fleet-tooltip-{{ $movement['mission_id'] }}">
                            {{ $movement['ship_count'] }}
                            <img src="/img/icons/{{ $movement['fleet_icon'] }}" alt="">
                        </span>
                    </li>
                    <li class="destFleet">
                        <figure class="planetIcon planet"></figure>
                        @if (!empty($movement['destination']['planet_name']))
                            <span class="planetName">{{ $movement['destination']['planet_name'] }}</span>
                        @endif
                    </li>
                    <li class="destCoords">
                        <a class="dark_highlight_tablet" href="{{ route('galaxy.index', ['galaxy' => $movement['destination']['galaxy'], 'system' => $movement['destination']['system']]) }}" target="_top">
                            [{{ $movement['destination']['galaxy'] }}:{{ $movement['destination']['system'] }}:{{ $movement['destination']['position'] }}]
                        </a>
                    </li>
                    <li class="descSpeed">Speed</li>
                    <li class="baseSpeed">{{ $movement['fleet_speed'] }}</li>
                    @if (!empty($movement['fleet_owner_id']))
                        <li class="sendMail">
                            <a href="javascript:void(0)"
                               class="sendMail js_openChat tooltip"
                               data-playerid="{{ $movement['fleet_owner_id'] }}"
                               title="{{ __('t_ingame.highscore.write_message') }}">
                                <span class="icon icon_chat"></span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endforeach
    @endif
</div>

<script type="text/javascript">
    (function($) {
        @foreach ($fleet_movements as $movement)
            new eventboxCountdown(
                $("#counter-phalanx-{{ $movement['mission_id'] }}")[0],
                {{ $movement['display_time'] }} - {{ $server_time }},
                $('#phalanxWrap'),
                null,
                [{{ $movement['mission_id'] }}]
            );
        @endforeach

        // Initialize tooltips
        if (typeof initTooltips === 'function') {
            initTooltips();
        }
    })(jQuery);
</script>
