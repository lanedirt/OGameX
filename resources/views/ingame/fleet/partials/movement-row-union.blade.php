@php
/** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event */
@endphp

<div id="eventRow-union{{ $fleet_event->union_id }}" class="fleetDetails allianceAttack unionunion{{ $fleet_event->union_id }} detailsClosed"
     data-mission-type="1"
     data-return-flight=""
     data-arrival-time="{{ $fleet_event->mission_time_arrival }}">

    <span class="timer tooltip" title="{{ date('d.m.Y H:i:s', $fleet_event->timer_time) }}" id="timer_{{ $fleet_event->id }}">{{ $fleet_event->remaining_time > 0 ? __('load...') : '-' }}</span>
    <span class="absTime">{{ date('H:i:s', $fleet_event->timer_time) }} @lang('Clock')</span>
    <span class="mission {{ $fleet_event->friendly_status }} textBeefy">{{ $fleet_event->mission_label }}</span>
    <span class="allianceName"></span>

    <span class="originData">
        <span class="originFleet">
            @lang('Fleets'): {{ $fleet_event->union_fleet_count }}/{{ $fleet_event->union_max_fleets }}
        </span>
        <span class="coordsOrigin">
            @lang('Players'): {{ $fleet_event->union_player_count }}/{{ $fleet_event->union_max_players }}
        </span>
    </span>

    <span class="marker01"></span>
    <span class="marker02"></span>

    <span class="fleetDetailButton">
        <a href="#blunion{{ $fleet_event->union_id }}" rel="blunion{{ $fleet_event->union_id }}" title="@lang('Fleet details')" class="tooltipRel tooltipClose fleet_icon_forward"></a>
    </span>

    <span class="starStreak">
        <div style="position: relative;">
            <div class="origin fixed">
                <img class="tooltipHTML" height="30" width="30" src="/img/icons/2ff25995f98351834db4b5aa048c68.gif" title="@lang('ACS Attack')" alt="">
            </div>

            <div class="route fixed">
                <a href="#blunion{{ $fleet_event->union_id }}" rel="blunion{{ $fleet_event->union_id }}" title="@lang('Fleet details')" class="tooltipRel tooltipClose basic2 fleet_icon_forward" id="route_{{ $fleet_event->id }}" style="margin-left: 0px;"></a>

                <div style="display:none;" id="blunion{{ $fleet_event->union_id }}">
                    <div class="htmlTooltip">
                        <h1>@lang('Fleet details'):</h1>
                        <div class="splitLine"></div>
                        <table cellpadding="0" cellspacing="0" class="fleetinfo">
                            <tr>
                                <th colspan="2">@lang('Ships'):</th>
                            </tr>
                            <tr>
                                <td>@lang('Total'):</td>
                                <td class="value">{{ number_format($fleet_event->fleet_unit_count) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="destination fixed">
                @switch ($fleet_event->destination_planet_type)
                    @case (OGame\Models\Enums\PlanetType::Planet)
                        <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->destination_planet_biome_type . '_' . $fleet_event->destination_planet_image_type . '.png') }}" title="@lang('Time of arrival'):| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                        @break
                    @case (OGame\Models\Enums\PlanetType::Moon)
                        <img class="tooltipHTML" height="30" width="30" src="/img/moons/big/{{ $fleet_event->destination_planet_image_type }}.gif" title="@lang('Time of arrival'):| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                        @break
                    @default
                        <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->destination_planet_biome_type . '_' . $fleet_event->destination_planet_image_type . '.png') }}" title="@lang('Time of arrival'):| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                @endswitch
            </div>
        </div>
    </span>

    <span class="destinationData">
        <span class="destinationPlanet">
            <span>
                @switch ($fleet_event->destination_planet_type)
                    @case (OGame\Models\Enums\PlanetType::Planet)
                        <figure class="planetIcon planet"></figure>{{ $fleet_event->destination_planet_name }}
                        @break
                    @case (OGame\Models\Enums\PlanetType::Moon)
                        <figure class="planetIcon moon"></figure>{{ $fleet_event->destination_planet_name }}
                        @break
                    @default
                        {{ $fleet_event->destination_planet_name }}
                @endswitch
            </span>
        </span>
        <span class="destinationCoords tooltip" title="{{ $fleet_event->destination_planet_name }}">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event->destination_planet_coords->galaxy, 'system' => $fleet_event->destination_planet_coords->system]) }}">
                [{{ $fleet_event->destination_planet_coords->asString() }}]
            </a>
        </span>
    </span>

    <span class="openDetails toggleDetails">
        <a href="javascript:void(0);" class="toggleUnionDetails" data-union-id="{{ $fleet_event->union_id }}">
            <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
        </a>
    </span>
</div>

{{-- Individual fleet rows within this union (collapsed by default) --}}
@foreach ($fleet_event->union_member_fleets as $member_fleet)
    @php
    if ($member_fleet->is_return_trip) {
        $memberFleetIconClass = 'fleet_icon_reverse';
    } else {
        $memberFleetIconClass = 'fleet_icon_forward';
    }
    @endphp
    <div id="fleet{{ $member_fleet->id }}" class="fleetDetails partnerInfo eventFleet union{{ $fleet_event->union_id }}" style="display: none;"
         data-mission-type="1"
         data-return-flight="{{ $member_fleet->is_return_trip ? '1' : '' }}"
         data-arrival-time="{{ $member_fleet->mission_time_arrival }}">

        <span class="timer tooltip" title="{{ date('d.m.Y H:i:s', $member_fleet->timer_time) }}" id="timer_{{ $member_fleet->id }}">{{ $member_fleet->remaining_time > 0 ? __('load...') : '-' }}</span>
        <span class="absTime">{{ date('H:i:s', $member_fleet->timer_time) }} @lang('Clock')</span>
        <span class="mission {{ $member_fleet->friendly_status }} textBeefy">{{ $member_fleet->mission_label }}</span>
        <span class="allianceName"></span>

        <span class="originData">
            <span class="originCoords tooltip" title="">
                <a href="{{ route('galaxy.index', ['galaxy' => $member_fleet->origin_planet_coords->galaxy, 'system' => $member_fleet->origin_planet_coords->system]) }}">
                    [{{ $member_fleet->origin_planet_coords->asString() }}]
                </a>
            </span>
            <span class="originPlanet">
                @switch ($member_fleet->origin_planet_type)
                    @case (OGame\Models\Enums\PlanetType::Planet)
                        <figure class="planetIcon planet"></figure>{{ $member_fleet->origin_planet_name }}
                        @break
                    @case (OGame\Models\Enums\PlanetType::Moon)
                        <figure class="planetIcon moon"></figure>{{ $member_fleet->origin_planet_name }}
                        @break
                    @default
                        {{ $member_fleet->origin_planet_name }}
                @endswitch
            </span>
        </span>

        <span class="marker01"></span>
        <span class="marker02"></span>

        <span class="fleetDetailButton">
            <a href="#bl{{ $member_fleet->id }}" rel="bl{{ $member_fleet->id }}" title="@lang('Fleet details')" class="tooltipRel tooltipClose {{ $memberFleetIconClass }}"></a>
        </span>

        @if ($member_fleet->is_recallable && !$member_fleet->is_return_trip)
            <span class="reversal reversal_time" ref="{{ $member_fleet->id }}">
                <a class="icon_link tooltipHTML recallFleet" data-fleet-id="{{ $member_fleet->id }}"
                   title="@lang('Recall'):| {{ date('d.m.Y', $member_fleet->active_recall_time) }}<br>{{ date('H:i:s', $member_fleet->active_recall_time) }}">
                    <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
                </a>
            </span>
        @endif

        <span class="starStreak">
            <div style="position: relative;">
                <div class="origin fixed">
                    @switch ($member_fleet->origin_planet_type)
                        @case (OGame\Models\Enums\PlanetType::Planet)
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $member_fleet->origin_planet_biome_type . '_' . $member_fleet->origin_planet_image_type . '.png') }}" title="@lang('Start time'):| {{ date('d.m.Y', $member_fleet->time_departure) }}<br>{{ date('H:i:s', $member_fleet->time_departure) }}" alt="">
                            @break
                        @case (OGame\Models\Enums\PlanetType::Moon)
                            <img class="tooltipHTML" height="30" width="30" src="/img/moons/big/{{ $member_fleet->origin_planet_image_type }}.gif" title="@lang('Start time'):| {{ date('d.m.Y', $member_fleet->time_departure) }}<br>{{ date('H:i:s', $member_fleet->time_departure) }}" alt="">
                            @break
                        @default
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $member_fleet->origin_planet_biome_type . '_' . $member_fleet->origin_planet_image_type . '.png') }}" title="@lang('Start time'):| {{ date('d.m.Y', $member_fleet->time_departure) }}<br>{{ date('H:i:s', $member_fleet->time_departure) }}" alt="">
                    @endswitch
                </div>

                <div class="route fixed">
                    <a href="#bl{{ $member_fleet->id }}" rel="bl{{ $member_fleet->id }}" title="@lang('Fleet details')" class="tooltipRel tooltipClose basic2 {{ $memberFleetIconClass }}" style="margin-left: 0px;"></a>

                    <div style="display:none;" id="bl{{ $member_fleet->id }}">
                        <div class="htmlTooltip">
                            <h1>@lang('Fleet details'):</h1>
                            <div class="splitLine"></div>
                            <table cellpadding="0" cellspacing="0" class="fleetinfo">
                                <tr>
                                    <th colspan="2">@lang('Ships'):</th>
                                </tr>
                                @foreach ($member_fleet->fleet_units->units as $fleet_unit)
                                    <tr>
                                        <td>{{ $fleet_unit->unitObject->title }}:</td>
                                        <td class="value">{{ number_format($fleet_unit->amount) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <th colspan="2">@lang('Shipment'):</th>
                                </tr>
                                <tr>
                                    <td>@lang('Metal'):</td>
                                    <td class="value">{{ $member_fleet->resources->metal->getFormattedLong() }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Crystal'):</td>
                                    <td class="value">{{ $member_fleet->resources->crystal->getFormattedLong() }}</td>
                                </tr>
                                <tr>
                                    <td>@lang('Deuterium'):</td>
                                    <td class="value">{{ $member_fleet->resources->deuterium->getFormattedLong() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="destination fixed">
                    @switch ($member_fleet->destination_planet_type)
                        @case (OGame\Models\Enums\PlanetType::Planet)
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $member_fleet->destination_planet_biome_type . '_' . $member_fleet->destination_planet_image_type . '.png') }}" title="@lang('Time of arrival'):| {{ date('d.m.Y', $member_fleet->mission_time_arrival) }}<br>{{ date('H:i:s', $member_fleet->mission_time_arrival) }}" alt="">
                            @break
                        @case (OGame\Models\Enums\PlanetType::Moon)
                            <img class="tooltipHTML" height="30" width="30" src="/img/moons/big/{{ $member_fleet->destination_planet_image_type }}.gif" title="@lang('Time of arrival'):| {{ date('d.m.Y', $member_fleet->mission_time_arrival) }}<br>{{ date('H:i:s', $member_fleet->mission_time_arrival) }}" alt="">
                            @break
                        @default
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $member_fleet->destination_planet_biome_type . '_' . $member_fleet->destination_planet_image_type . '.png') }}" title="@lang('Time of arrival'):| {{ date('d.m.Y', $member_fleet->mission_time_arrival) }}<br>{{ date('H:i:s', $member_fleet->mission_time_arrival) }}" alt="">
                    @endswitch
                </div>
            </div>
        </span>

        <span class="destinationData">
            <span class="destinationPlanet">
                <span>
                    @switch ($member_fleet->destination_planet_type)
                        @case (OGame\Models\Enums\PlanetType::Planet)
                            <figure class="planetIcon planet"></figure>{{ $member_fleet->destination_planet_name }}
                            @break
                        @case (OGame\Models\Enums\PlanetType::Moon)
                            <figure class="planetIcon moon"></figure>{{ $member_fleet->destination_planet_name }}
                            @break
                        @default
                            {{ $member_fleet->destination_planet_name }}
                    @endswitch
                </span>
            </span>
            <span class="destinationCoords tooltip" title="{{ $member_fleet->destination_planet_name }}">
                <a href="{{ route('galaxy.index', ['galaxy' => $member_fleet->destination_planet_coords->galaxy, 'system' => $member_fleet->destination_planet_coords->system]) }}">
                    [{{ $member_fleet->destination_planet_coords->asString() }}]
                </a>
            </span>
        </span>

        <span class="openDetails">
            <a href="javascript:void(0);" class="openCloseDetails" data-mission-id="{{ $member_fleet->id }}" data-end-time="{{ $member_fleet->mission_time_arrival }}">
                <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
            </a>
        </span>
    </div>
@endforeach
