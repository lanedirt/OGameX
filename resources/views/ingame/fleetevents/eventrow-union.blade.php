@php /** @var \OGame\ViewModels\FleetEventRowViewModel $fleet_event_row */ @endphp

{{-- Union summary row --}}
<tr class="allianceAttack eventFleet unionunion{{ $fleet_event_row->union_id }} detailsClosed" id="eventRow-union{{ $fleet_event_row->union_id }}"
    data-mission-type="1"
    data-return-flight="false"
    data-arrival-time="{{ $fleet_event_row->mission_time_arrival }}"
>
    <td class="countDown">
        <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="{{ $fleet_event_row->friendly_status }} textBeefy">
            load...
        </span>
    </td>
    <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
    <td class="missionFleet">
        <img src="/img/fleet/2.gif" class="tooltipHTML"
             title="Own fleet | {{ $fleet_event_row->mission_label }}" alt=""/>
    </td>

    <td class="originFleet">
        @lang('Fleets'): {{ $fleet_event_row->union_fleet_count }}/{{ $fleet_event_row->union_max_fleets }}
    </td>
    <td class="coordsOrigin">
        @lang('Players'): {{ $fleet_event_row->union_player_count }}/{{ $fleet_event_row->union_max_players }}
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
            &lt;tr&gt;
                &lt;td colspan=&quot;2&quot;&gt;@lang('Total'):&lt;/td&gt;
                &lt;td class=&quot;value&quot;&gt;{{ $fleet_event_row->fleet_unit_count }}&lt;/td&gt;
            &lt;/tr&gt;
            &lt;/table&gt;
    &lt;/div&gt;
">
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
    <td class="sendMail">
    </td>
    <td class="sendProbe toggleDetails">
        <a href="javascript:void(0);" class="toggleUnionDetails" data-union-id="{{ $fleet_event_row->union_id }}">
            <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="16" width="16">
        </a>
    </td>
    <td class="sendMail">
    </td>
</tr>

{{-- Individual fleet rows within this union (collapsed by default) --}}
@foreach ($fleet_event_row->union_member_fleets as $member_fleet)
    <tr class="partnerInfo eventFleet union{{ $fleet_event_row->union_id }}" id="eventRow-{{ $member_fleet->id }}" style="display: none;"
        data-mission-type="1"
        data-return-flight="false"
        data-arrival-time="{{ $member_fleet->mission_time_arrival }}"
    >
        <td class="countDown">
            <span id="counter-eventlist-{{ $member_fleet->id }}" class="{{ $member_fleet->friendly_status }} textBeefy">
                load...
            </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $member_fleet->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            <img src="/img/fleet/2.gif" class="tooltipHTML"
                 title="Own fleet | {{ $member_fleet->mission_label }}" alt=""/>
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
                    <figure class="planetIcon planet js_hideTipOnMobile"
                            title="Planet"></figure>{{ $member_fleet->destination_planet_name }}
                    @break
                @case (OGame\Models\Enums\PlanetType::Moon)
                    <figure class="planetIcon moon js_hideTipOnMobile"
                            title="Moon"></figure>{{ $member_fleet->destination_planet_name }}
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
                       title="Recall:| {{ \Carbon\Carbon::parse($member_fleet->active_recall_time)->format('d.m.Y') }}<br>
                                            {{ \Carbon\Carbon::parse($member_fleet->active_recall_time)->format('H:i:s') }}">
                        <img src="/img/icons/89624964d4b06356842188dba05b1b.gif" height="16" width="16"/>
                    </a>
                </span>
            @endif
        </td>
        <td class="sendProbe">
        </td>
        <td class="sendMail">
        </td>
    </tr>

    <script type="text/javascript">
        (function ($) {
            var wrappedCountdown = function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                new eventboxCountdown(
                    $("#counter-eventlist-{{ $member_fleet->id }}"),
                        {{ $member_fleet->mission_time_arrival }} - {{ time() }},
                    $("#eventListWrap"),
                    "{{ route('fleet.eventlist.checkevents') }}",
                    [{{ $member_fleet->id }}]
                );
            };
            wrappedCountdown();
        })(jQuery);
    </script>
@endforeach

<script type="text/javascript">
    (function ($) {
        var wrappedCountdown = function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            new eventboxCountdown(
                $("#counter-eventlist-{{ $fleet_event_row->id }}"),
                    {{ $fleet_event_row->mission_time_arrival }} - {{ time() }},
                $("#eventListWrap"),
                "{{ route('fleet.eventlist.checkevents') }}",
                [{{ $fleet_event_row->id }}]
            );
        };
        wrappedCountdown();

        // Union expand/collapse toggle handler
        $(".toggleUnionDetails[data-union-id='{{ $fleet_event_row->union_id }}']").off('click').on('click', function (e) {
            e.preventDefault();
            var unionId = $(this).attr("data-union-id");
            var summaryRow = $(this).closest(".allianceAttack");
            var memberRows = $(".partnerInfo.union" + unionId);

            if (summaryRow.hasClass("detailsClosed")) {
                summaryRow.removeClass("detailsClosed").addClass("detailsOpened");
                memberRows.show();
            } else {
                summaryRow.removeClass("detailsOpened").addClass("detailsClosed");
                memberRows.hide();
            }
            return false;
        });
    })(jQuery);
</script>
