@php /** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event_row */ @endphp

{{-- Union summary row --}}
<tr class="allianceAttack eventFleet unionunion{{ $fleet_event_row->union_id }} detailsClosed" id="eventRow-union{{ $fleet_event_row->union_id }}"
    data-mission-type="{{ $fleet_event_row->mission_type }}"
    data-return-flight="false"
    data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
>
    <td class="countDown">
        <span id="counter-eventlist-union{{ $fleet_event_row->union_id }}" class="{{ $fleet_event_row->friendly_status }} textBeefy">
            @lang('load...')
        </span>
    </td>
    <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} @lang('Clock')</td>
    <td class="missionFleet">
        <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltip"
             alt="" data-tooltip-title="@lang('Attack')"/>
    </td>

    <td class="originFleet">{{ $fleet_event_row->union_fleet_count }} / {{ $fleet_event_row->union_max_fleets }}</td>
    <td class="coordsOrigin textBeefy">{{ $fleet_event_row->union_player_count }} / {{ $fleet_event_row->union_max_players }}</td>

    <td class="detailsFleet">
        <span>{{ $fleet_event_row->fleet_unit_count }}</span>
    </td>
    <td class="icon_movement">
        <span class="tooltip tooltipRight tooltipClose"
              title="&lt;div class=&quot;htmlTooltip&quot;&gt;
    &lt;h1&gt;@lang('Fleet details'):&lt;/h1&gt;
    &lt;div class=&quot;splitLine&quot;&gt;&lt;/div&gt;
            &lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;&gt;
            @foreach ($fleet_event_row->union_player_breakdown as $playerInfo)
                &lt;tr&gt;
                    &lt;th colspan=&quot;3&quot;&gt;{{ $playerInfo['player_name'] }}&lt;/th&gt;
                &lt;/tr&gt;
                @foreach ($playerInfo['origins'] as $origin)
                &lt;tr&gt;
                    &lt;td&gt;{{ $origin['planet_name'] }}&lt;/td&gt;
                    &lt;td&gt;{{ $origin['coords'] }}&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td&gt;@lang('Fleets')&lt;/td&gt;
                    &lt;td&gt;{{ $origin['fleet_count'] }}&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td&gt;@lang('Ships')&lt;/td&gt;
                    &lt;td&gt;{{ $origin['ship_count'] }}&lt;/td&gt;
                &lt;/tr&gt;
                @endforeach
            @endforeach
            &lt;/table&gt;
    &lt;/div&gt;
">
            &nbsp;
        </span>
    </td>

    <td class="destFleet">
        @switch ($fleet_event_row->destination_planet_type)
            @case (OGame\Models\Enums\PlanetType::Planet)
                <figure class="planetIcon planet tooltip js_hideTipOnMobile"
                        data-tooltip-title="Planet"></figure>{{ $fleet_event_row->destination_planet_name }}
                @break
            @case (OGame\Models\Enums\PlanetType::Moon)
                <figure class="planetIcon moon tooltip js_hideTipOnMobile"
                        data-tooltip-title="Moon"></figure>{{ $fleet_event_row->destination_planet_name }}
                @break
            @default
                {{ $fleet_event_row->destination_planet_name }}
        @endswitch
    </td>
    <td class="destCoords">
        <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->destination_planet_coords->galaxy, 'system' => $fleet_event_row->destination_planet_coords->system]) }}"
           target="_top">
            [{{ $fleet_event_row->destination_planet_coords->asString() }}]
        </a>
    </td>
    <td colspan="2">
        <a class="toggleDetails icon_link" href="javascript:void(0);" rel="union{{ $fleet_event_row->union_id }}"></a>
    </td>
</tr>

{{-- Individual fleet rows within this union (collapsed by default) --}}
@foreach ($fleet_event_row->union_member_fleets as $member_fleet)
    <tr class="partnerInfo eventFleet union{{ $fleet_event_row->union_id }}" id="eventRow-{{ $member_fleet->id }}" style="display: none;"
        data-mission-type="{{ $fleet_event_row->mission_type }}"
        data-return-flight="false"
        data-arrival-time="{{ $member_fleet->mission_time_arrival }}"
    >
        <td class="countDown" style="color: #8C9EAA;">
            ---
        </td>
        <td class="descFleet">
            @if ($member_fleet->user_id !== null && $member_fleet->user_id !== auth()->id())
                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $member_fleet->user_id }}" title="{{ $member_fleet->player_name }}"><span class="icon icon_chat"></span></a>
            @endif
            {{ __('t_ingame.fleet.own_fleet') }}
        </td>
        <td class="missionFleet">
            <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltipHTML"
                 title="{{ __('t_ingame.fleet.own_fleet') }} | {{ $member_fleet->mission_label }}" alt=""/>
        </td>

        <td class="originFleet">
            @switch ($member_fleet->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $member_fleet->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $member_fleet->origin_planet_name }}
                    @break
                @default
                    {{ $member_fleet->origin_planet_name }}
            @endswitch
        </td>
        <td class="coordsOrigin">
            <a href="{{ route('galaxy.index', ['galaxy' => $member_fleet->origin_planet_coords->galaxy, 'system' => $member_fleet->origin_planet_coords->system]) }}"
               target="_top">
                [{{ $member_fleet->origin_planet_coords->asString() }}]
            </a>
        </td>

        <td class="detailsFleet">
            <span>{{ $member_fleet->fleet_unit_count }}</span>
        </td>
        <td class="icon_movement">
            <span class="tooltip tooltipRight tooltipClose"
                  title="&lt;div class=&quot;htmlTooltip&quot;&gt;
    &lt;h1&gt;@lang('Fleet details'):&lt;/h1&gt;
    &lt;div class=&quot;splitLine&quot;&gt;&lt;/div&gt;
            &lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;&gt;
            &lt;tr&gt;
                &lt;th colspan=&quot;3&quot;&gt;@lang('Ships'):&lt;/th&gt;
            &lt;/tr&gt;
            @foreach ($member_fleet->fleet_units->units as $fleet_unit)
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;{{ $fleet_unit->unitObject->title }}:&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_unit->amount }}&lt;/td&gt;
                &lt;/tr&gt;
            @endforeach

                &lt;tr&gt;
                    &lt;th colspan=&quot;3&quot;&gt;&nbsp;&lt;/th&gt;
                &lt;/tr&gt;

                &lt;tr&gt;
                    &lt;th colspan=&quot;3&quot;&gt;@lang('Shipment'):&lt;/th&gt;
                &lt;/tr&gt;

                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Metal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $member_fleet->resources->metal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;

                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Crystal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $member_fleet->resources->crystal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Deuterium'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $member_fleet->resources->deuterium->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
            &lt;/table&gt;
    &lt;/div&gt;
">
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            @switch ($member_fleet->destination_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet tooltip js_hideTipOnMobile"
                            data-tooltip-title="Planet"></figure>{{ $member_fleet->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon tooltip js_hideTipOnMobile"
                            data-tooltip-title="Moon"></figure>{{ $member_fleet->destination_planet_name }}
                    @break
                @default
                    {{ $member_fleet->destination_planet_name }}
            @endswitch
        </td>
        <td class="destCoords">
            <a href="{{ route('galaxy.index', ['galaxy' => $member_fleet->destination_planet_coords->galaxy, 'system' => $member_fleet->destination_planet_coords->system]) }}"
               target="_top">
                [{{ $member_fleet->destination_planet_coords->asString() }}]
            </a>
        </td>
        <td class="sendMail">
            @if ($member_fleet->is_recallable)
                <span class="reversal reversal_time" ref="{{ $member_fleet->id }}">
                    <a class="icon_link tooltipHTML recallFleet" data-fleet-id="{{ $member_fleet->real_mission_id ?? $member_fleet->id }}"
                       title="@lang('Recall'):| {{ date('d.m.Y', $member_fleet->active_recall_time) }}&lt;br&gt;{{ date('H:i:s', $member_fleet->active_recall_time) }}">
                        <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
                    </a>
                </span>
            @endif
        </td>
        <td class="sendProbe">
            <a class="tooltip js_hideTipOnMobile icon_link" href="javascript:void(0);" onclick="sendShips(6, {{ $member_fleet->destination_planet_coords->galaxy }}, {{ $member_fleet->destination_planet_coords->system }}, {{ $member_fleet->destination_planet_coords->position }}, {{ $member_fleet->destination_planet_type->value }}, {{ $espionage_probe_count }});return false;" title="@lang('Espionage')">
                <span class="icon icon_eye"></span>
            </a>
        </td>
        <td class="sendMail">
            @if ($member_fleet->destination_player_id)
                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $member_fleet->destination_player_id }}" title="{{ $member_fleet->destination_player_name }}"><span class="icon icon_chat"></span></a>
            @endif
        </td>
    </tr>
@endforeach

<script type="text/javascript">
    (function ($) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Union summary row countdown
        new eventboxCountdown(
            $("#counter-eventlist-union{{ $fleet_event_row->union_id }}"),
                {{ $fleet_event_row->mission_time_arrival }} - {{ time() }},
            $("#eventListWrap"),
            "{{ route('fleet.eventlist.checkevents') }}",
            [{{ collect($fleet_event_row->union_member_fleets)->pluck('id')->implode(',') }}]
        );
    })(jQuery);
</script>
