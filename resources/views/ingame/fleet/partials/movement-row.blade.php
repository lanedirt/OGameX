@php
/** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event */
// Determine fleet icon class based on mission state
if ($fleet_event->is_return_trip) {
    $fleetIconClass = 'fleet_icon_reverse';
} elseif ($fleet_event->is_at_destination) {
    $fleetIconClass = 'fleet_icon_forward_end';
} else {
    $fleetIconClass = 'fleet_icon_forward';
}
@endphp

<div id="fleet{{ $fleet_event->id }}" class="fleetDetails detailsOpened"
     data-mission-type="{{ $fleet_event->mission_type }}"
     data-return-flight="{{ $fleet_event->is_return_trip ? '1' : '' }}"
     data-arrival-time="{{ $fleet_event->mission_time_arrival }}">

    <span class="timer tooltip" title="{{ date('d.m.Y H:i:s', $fleet_event->timer_time) }}" id="timer_{{ $fleet_event->id }}">{{ $fleet_event->remaining_time > 0 ? __('t_ingame.fleet.load_dots') : '-' }}</span>
    <span class="absTime">{{ date('H:i:s', $fleet_event->timer_time) }} {{ __('t_ingame.fleet.clock') }}</span>
    <span class="mission {{ $fleet_event->friendly_status }} textBeefy">{{ $fleet_event->mission_label }}{{ $fleet_event->is_return_trip ? ' (R)' : '' }}</span>
    <span class="allianceName">{{ $fleet_event->alliance_name }}</span>

    <span class="originData">
        <span class="originCoords tooltip" title="">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event->origin_planet_coords->galaxy, 'system' => $fleet_event->origin_planet_coords->system]) }}">
                [{{ $fleet_event->origin_planet_coords->asString() }}]
            </a>
        </span>
        <span class="originPlanet">
            @switch ($fleet_event->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet"></figure>{{ $fleet_event->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon"></figure>{{ $fleet_event->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DebrisField)
                    <figure class="planetIcon tf"></figure>{{ __('t_ingame.fleet.debris_field') }}
                    @break
                @case (OGame\Models\Enums\PlanetType::DeepSpace)
                    {{ __('t_ingame.fleet.deep_space') }}
                    @break
            @endswitch
        </span>
    </span>

    <span class="marker01"></span>
    <span class="marker02"></span>

    <span class="fleetDetailButton">
        <a href="#bl{{ $fleet_event->id }}" rel="bl{{ $fleet_event->id }}" title="{{ __('t_ingame.fleet.fleet_details') }}" class="tooltipRel tooltipClose {{ $fleetIconClass }}"></a>
    </span>

    @if ($fleet_event->is_recallable && !$fleet_event->is_return_trip && !$fleet_event->is_at_destination)
        <span class="reversal reversal_time" ref="{{ $fleet_event->id }}">
            <a class="icon_link tooltipHTML recallFleet" data-fleet-id="{{ $fleet_event->id }}"
               title="{{ __('t_ingame.fleet.recall') }}:| {{ date('d.m.Y', $fleet_event->active_recall_time) }}<br>{{ date('H:i:s', $fleet_event->active_recall_time) }}">
                <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
            </a>
        </span>
    @endif

    <span class="starStreak">
        <div style="position: relative;">
            <div class="origin fixed">
                @switch ($fleet_event->origin_planet_type)
                    @case (OGame\Models\Enums\PlanetType::Planet)
                        <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->origin_planet_biome_type . '_' . $fleet_event->origin_planet_image_type . '.png') }}" title="{{ __('t_ingame.fleet.start_time') }}:| {{ date('d.m.Y', $fleet_event->time_departure) }}<br>{{ date('H:i:s', $fleet_event->time_departure) }}" alt="">
                        @break
                    @case (OGame\Models\Enums\PlanetType::Moon)
                        <img class="tooltipHTML" height="30" width="30" src="/img/moons/big/{{ $fleet_event->origin_planet_image_type }}.gif" title="{{ __('t_ingame.fleet.start_time') }}:| {{ date('d.m.Y', $fleet_event->time_departure) }}<br>{{ date('H:i:s', $fleet_event->time_departure) }}" alt="">
                        @break
                    @case (OGame\Models\Enums\PlanetType::DebrisField)
                        <img class="tooltipHTML" height="30" width="30" src="/img/icons/3ca961edd69ea535317329e75b0e13.gif" title="{{ __('t_ingame.fleet.start_time') }}:| {{ date('d.m.Y', $fleet_event->time_departure) }}<br>{{ date('H:i:s', $fleet_event->time_departure) }}" alt="">
                        @break
                    @default
                        <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->origin_planet_biome_type . '_' . $fleet_event->origin_planet_image_type . '.png') }}" title="{{ __('t_ingame.fleet.start_time') }}:| {{ date('d.m.Y', $fleet_event->time_departure) }}<br>{{ date('H:i:s', $fleet_event->time_departure) }}" alt="">
                @endswitch
            </div>

            <div class="route fixed">
                <a href="#bl{{ $fleet_event->id }}" rel="bl{{ $fleet_event->id }}" title="{{ __('t_ingame.fleet.fleet_details') }}" class="tooltipRel tooltipClose basic2 {{ $fleetIconClass }}" id="route_{{ $fleet_event->id }}" style="margin-left: {{ $fleet_event->is_at_destination ? 274 : 0 }}px;"></a>

                <div style="display:none;" id="bl{{ $fleet_event->id }}">
                    <div class="htmlTooltip">
                        <h1>{{ __('t_ingame.fleet.fleet_details') }}:</h1>
                        <div class="splitLine"></div>
                        <table cellpadding="0" cellspacing="0" class="fleetinfo">
                            <tr>
                                <th colspan="2">{{ __('t_ingame.fleet.ships') }}:</th>
                            </tr>
                            @foreach ($fleet_event->fleet_units->units as $fleet_unit)
                                <tr>
                                    <td>{{ $fleet_unit->unitObject->title }}:</td>
                                    <td class="value">{{ number_format($fleet_unit->amount) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <th colspan="2">{{ __('t_ingame.fleet.shipment') }}:</th>
                            </tr>
                            <tr>
                                <td>{{ __('t_ingame.fleet.metal') }}:</td>
                                <td class="value">{{ $fleet_event->resources->metal->getFormattedLong() }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('t_ingame.fleet.crystal') }}:</td>
                                <td class="value">{{ $fleet_event->resources->crystal->getFormattedLong() }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('t_ingame.fleet.deuterium') }}:</td>
                                <td class="value">{{ $fleet_event->resources->deuterium->getFormattedLong() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="destination fixed">
                @if ($fleet_event->is_at_destination)
                    {{-- Fleet is at destination (e.g., expedition), show animated gif --}}
                    <img class="tooltipHTML" height="30" width="30" src="/img/icons/449345bf70822be196b2bff6fc4763.gif" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                @else
                    @switch ($fleet_event->destination_planet_type)
                        @case (OGame\Models\Enums\PlanetType::Planet)
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->destination_planet_biome_type . '_' . $fleet_event->destination_planet_image_type . '.png') }}" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                            @break
                        @case (OGame\Models\Enums\PlanetType::Moon)
                            <img class="tooltipHTML" height="30" width="30" src="/img/moons/big/{{ $fleet_event->destination_planet_image_type }}.gif" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                            @break
                        @case (OGame\Models\Enums\PlanetType::DebrisField)
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/3ca961edd69ea535317329e75b0e13.gif" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                            @break
                        @case (OGame\Models\Enums\PlanetType::DeepSpace)
                            <img class="tooltipHTML" height="30" width="30" src="/img/icons/449345bf70822be196b2bff6fc4763.gif" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                            @break
                        @default
                            <img class="tooltipHTML" height="30" width="30" src="{{ asset('img/planets/medium/' . $fleet_event->destination_planet_biome_type . '_' . $fleet_event->destination_planet_image_type . '.png') }}" title="{{ __('t_ingame.fleet.time_of_arrival') }}:| {{ date('d.m.Y', $fleet_event->mission_time_arrival) }}<br>{{ date('H:i:s', $fleet_event->mission_time_arrival) }}" alt="">
                    @endswitch
                @endif
            </div>
        </div>
    </span>

    <span class="destinationData">
        <span class="destinationPlanet">
            <span>
                @switch ($fleet_event->destination_planet_type)
                    @case (OGame\Models\Enums\PlanetType::Planet)
                        <figure class="planetIcon planet"></figure>{{ $fleet_event->destination_planet_name ?: __('t_ingame.fleet.deep_space') }}
                        @break
                    @case (OGame\Models\Enums\PlanetType::Moon)
                        <figure class="planetIcon moon"></figure>{{ $fleet_event->destination_planet_name }}
                        @break
                    @case (OGame\Models\Enums\PlanetType::DebrisField)
                        <figure class="planetIcon tf"></figure>{{ __('t_ingame.fleet.debris_field') }}
                        @break
                    @case (OGame\Models\Enums\PlanetType::DeepSpace)
                        @break
                @endswitch
            </span>
        </span>
        <span class="destinationCoords tooltip" title="{{ $fleet_event->destination_planet_name ?: __('t_ingame.fleet.deep_space') }}">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event->destination_planet_coords->galaxy, 'system' => $fleet_event->destination_planet_coords->system]) }}">
                [{{ $fleet_event->destination_planet_coords->asString() }}]
            </a>
        </span>
    </span>

    @if ($fleet_event->has_return_trip && $fleet_event->return_time_arrival)
        <span class="nextTimer tooltip" title="{{ date('d.m.Y H:i:s', $fleet_event->return_time_arrival) }}" id="timerNext_{{ $fleet_event->id }}">{{ __('t_ingame.fleet.load_dots') }}</span>
        <span class="nextabsTime">{{ date('H:i:s', $fleet_event->return_time_arrival) }} {{ __('t_ingame.fleet.clock') }}</span>
        <span class="nextMission friendly textBeefy">{{ __('t_ingame.fleet.return_trip') }}</span>
    @endif

    @if ($fleet_event->can_create_federation)
        <span class="fedAttack" id="federation_{{ $fleet_event->id }}">
            @if ($fleet_event->union_id)
                <a href="{{ route('fleet.federation.overlay', ['fleet' => $fleet_event->id, 'union' => $fleet_event->union_id]) }}" class="openOverlay tooltip icon" data-title="@lang('Fleet union')" data-overlay-class="federation-layer" data-fleet-id="{{ $fleet_event->id }}" title="@lang('fleet union')">
                    <img src="/img/icons/2ff25995f98351834db4b5aa048c68.gif" height="16" width="16">
                </a>
            @else
                <a href="{{ route('fleet.federation.overlay', ['fleet' => $fleet_event->id]) }}" class="openOverlay tooltip icon" data-title="@lang('Fleet union')" data-overlay-class="federation-layer" data-fleet-id="{{ $fleet_event->id }}" title="@lang('fleet union')">
                    <img src="/img/icons/2ff25995f98351834db4b5aa048c68.gif" height="16" width="16">
                </a>
            @endif
        </span>
    @endif

    <span class="openDetails">
        <a href="javascript:void(0);" class="openCloseDetails" data-mission-id="{{ $fleet_event->id }}" data-end-time="{{ $fleet_event->mission_time_arrival }}">
            <img src="/img/icons/577565fadab7780b0997a76d0dca9b.gif" height="16" width="16">
        </a>
    </span>
</div>
