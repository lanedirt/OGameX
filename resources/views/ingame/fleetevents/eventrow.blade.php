@php /** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event_row */ @endphp

@if ($fleet_event_row->is_return_trip)
    <tr class="eventFleet" id="eventRow-{{ $fleet_event_row->id }}"
        data-mission-type="{{ $fleet_event_row->mission_type }}"
        data-return-flight="true"
        data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
    >
        <td class="countDown">
            <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="{{ $fleet_event_row->mission_status }} textBeefy">
                load...
            </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            @php
                $fleetLabel = $fleet_event_row->mission_status === 'own' ? 'Own fleet' :
                              ($fleet_event_row->mission_status === 'friendly' ? 'Friendly fleet' :
                              ($fleet_event_row->mission_status === 'hostile' ? 'Hostile fleet' : ucfirst($fleet_event_row->mission_status) . ' fleet'));
            @endphp
            <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltipHTML"
                 title="{{ $fleetLabel }} | {{ $fleet_event_row->mission_label }} (R)" alt=""/>
        </td>

        <td class="originFleet">
            @switch ($fleet_event_row->destination_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile" title="Planet"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile" title="Moon"></figure>{{ $fleet_event_row->destination_planet_name }}
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
            <span>{{ $fleet_event_row->fleet_unit_count }}</span>
        </td>
        <td class="icon_movement_reserve">
            <span class="tooltip tooltipRight tooltipClose"
                  title="&lt;div class=&quot;htmlTooltip&quot;&gt;
    &lt;h1&gt;@lang('Fleet details'):&lt;/h1&gt;
    &lt;div class=&quot;splitLine&quot;&gt;&lt;/div&gt;
            &lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;&gt;
            &lt;tr&gt;
                &lt;th colspan=&quot;3&quot;&gt;@lang('Ships'):&lt;/th&gt;
            &lt;/tr&gt;
            @php /** @var \OGame\GameObjects\Models\Units\UnitCollection $fleet_unit */ @endphp
            @foreach ($fleet_event_row->fleet_units->units as $fleet_unit)
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
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->metal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;

                                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Crystal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->crystal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Deuterium'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->deuterium->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                @if($fleet_event_row->acs_group_id)
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot;&gt;&nbsp;&lt;/th&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot; style=&quot;color: #6f9fc8;&quot;&gt;@lang('ACS Attack Group'):&lt;/th&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;td colspan=&quot;2&quot;&gt;@lang('Group Name'):&lt;/td&gt;
                        &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->acs_group_name }}&lt;/td&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;td colspan=&quot;2&quot;&gt;@lang('Fleets in Group'):&lt;/td&gt;
                        &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->acs_fleet_count }}&lt;/td&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot;&gt;@lang('Participants'):&lt;/th&gt;
                    &lt;/tr&gt;
                    @foreach($fleet_event_row->acs_participants as $participant)
                        &lt;tr&gt;
                            &lt;td colspan=&quot;2&quot; style=&quot;font-size: 10px;&quot;&gt;{{ $participant['planet_name'] }} [{{ $participant['coordinates'] }}]:&lt;/td&gt;
                            &lt;td class=&quot;value&quot;&gt;{{ $participant['unit_count'] }} @lang('ships')&lt;/td&gt;
                        &lt;/tr&gt;
                    @endforeach
                @endif
            &lt;/table&gt;
    &lt;/div&gt;
">
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            @switch ($fleet_event_row->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile" title="Planet"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile" title="Moon"></figure>{{ $fleet_event_row->origin_planet_name }}
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
        </td>
    </tr>
@else
    <tr class="eventFleet" id="eventRow-{{ $fleet_event_row->id }}"
        data-mission-type="{{ $fleet_event_row->mission_type }}"
        data-return-flight="false"
        data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
    >
        <td class="countDown">
        <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="{{ $fleet_event_row->mission_status }} textBeefy">
                    load...
        </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            @php
                $fleetLabel = $fleet_event_row->mission_status === 'own' ? 'Own fleet' :
                              ($fleet_event_row->mission_status === 'friendly' ? 'Friendly fleet' :
                              ($fleet_event_row->mission_status === 'hostile' ? 'Hostile fleet' : ucfirst($fleet_event_row->mission_status) . ' fleet'));
            @endphp
            <img src="/img/fleet/{{ $fleet_event_row->mission_type }}.gif" class="tooltipHTML"
                 title="{{ $fleetLabel }} | {{ $fleet_event_row->mission_label }}" alt=""/>
        </td>

        <td class="originFleet">
            @switch ($fleet_event_row->origin_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile" title="Planet"></figure>{{ $fleet_event_row->origin_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile" title="Moon"></figure>{{ $fleet_event_row->origin_planet_name }}
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
            <span>{{ $fleet_event_row->fleet_unit_count }}</span>
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
            @php /** @var \OGame\GameObjects\Models\Units\UnitCollection $fleet_unit */ @endphp
            @foreach ($fleet_event_row->fleet_units->units as $fleet_unit)
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
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->metal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;

                                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Crystal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->crystal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Deuterium'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->resources->deuterium->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                @if($fleet_event_row->acs_group_id)
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot;&gt;&nbsp;&lt;/th&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot; style=&quot;color: #6f9fc8;&quot;&gt;@lang('ACS Attack Group'):&lt;/th&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;td colspan=&quot;2&quot;&gt;@lang('Group Name'):&lt;/td&gt;
                        &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->acs_group_name }}&lt;/td&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;td colspan=&quot;2&quot;&gt;@lang('Fleets in Group'):&lt;/td&gt;
                        &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->acs_fleet_count }}&lt;/td&gt;
                    &lt;/tr&gt;
                    &lt;tr&gt;
                        &lt;th colspan=&quot;3&quot;&gt;@lang('Participants'):&lt;/th&gt;
                    &lt;/tr&gt;
                    @foreach($fleet_event_row->acs_participants as $participant)
                        &lt;tr&gt;
                            &lt;td colspan=&quot;2&quot; style=&quot;font-size: 10px;&quot;&gt;{{ $participant['planet_name'] }} [{{ $participant['coordinates'] }}]:&lt;/td&gt;
                            &lt;td class=&quot;value&quot;&gt;{{ $participant['unit_count'] }} @lang('ships')&lt;/td&gt;
                        &lt;/tr&gt;
                    @endforeach
                @endif
            &lt;/table&gt;
    &lt;/div&gt;
">
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            @switch ($fleet_event_row->destination_planet_type)
                @case (OGame\Models\Enums\PlanetType::Planet)
                    <figure class="planetIcon planet js_hideTipOnMobile" title="Planet"></figure>{{ $fleet_event_row->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile" title="Moon"></figure>{{ $fleet_event_row->destination_planet_name }}
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
                    <a class="icon_link tooltipHTML recallFleet" data-fleet-id="{{ $fleet_event_row->id }}"
                       title="Recall:| 22.04.2024<br>15:28:45">
                        <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
                    </a>
                </span>
            @endif
        </td>
        <td class="sendProbe">
            {{-- Convert regular attack to ACS attack - only show if mission is attack (type 1) and not in an ACS group yet --}}
            @if ($fleet_event_row->mission_type === 1 && empty($fleet_event_row->acs_group_id))
                <a href="javascript:void(0);"
                   class="convertToACS"
                   data-fleet-id="{{ $fleet_event_row->id }}"
                   style="color: #6f9fc8; text-decoration: none; font-size: 11px;"
                   title="Convert this attack to an ACS group">
                    [ACS]
                </a>
            @endif

            {{-- Invite button - only show if mission is ACS attack (type 2) or converted attack with ACS group, and player is group creator --}}
            @if ($fleet_event_row->acs_group_id && $fleet_event_row->is_acs_group_creator)
                <a href="javascript:void(0);"
                   class="inviteToACS"
                   data-acs-group-id="{{ $fleet_event_row->acs_group_id }}"
                   style="color: #6f9fc8; text-decoration: none; font-size: 11px; margin-left: 5px;"
                   title="Invite players to this ACS group">
                    [Invite]
                </a>
            @endif
        </td>
        <td class="sendMail">
        </td>
    </tr>
@endif

@php
    $time_diff = $fleet_event_row->mission_time_arrival - time();
@endphp
<script type="text/javascript">
    (function ($) {
        new eventboxCountdown(
            $("#counter-eventlist-{{ $fleet_event_row->id }}"),
            {{ $time_diff }},
            $("#eventListWrap"),
            "#TODO_page=componentOnly&component=eventList&action=checkEvents&ajax=1&asJson=1",
            [0, 1]
        );
    })(jQuery);
</script>

