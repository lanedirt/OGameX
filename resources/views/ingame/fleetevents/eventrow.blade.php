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
            <img src="https://gf1.geo.gfsrv.net/cdn38/2af2939219d8227a11a50ff4df7b51.gif" class="tooltipHTML"  title="Own fleet | Transport (R)" alt=""/>
        </td>

        <td class="originFleet">
            <figure class="planetIcon planet"></figure>
            {{ $fleet_event_row->destination_planet_name }}
        </td>
        <td class="coordsOrigin">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->destination_planet_coords->galaxy, 'system' => $fleet_event_row->destination_planet_coords->system]) }}" target="_top">
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
            @php /** @var \OGame\GameObjects\Models\UnitCollection $fleet_unit */ @endphp
            @foreach ($fleet_event_row->fleet_units->units as $fleet_unit)
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;{{ $fleet_unit->unitObject->title }}:&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_unit->amount }}&lt;/td&gt;
                &lt;/tr&gt;
            @endforeach
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
                                    &lt;/table&gt;
    &lt;/div&gt;
">
                &nbsp;
            </span>
        </td>

        <!--
           &lt;tr&gt;
                        &lt;td colspan=&quot;2&quot;&gt;@lang('Food'):&lt;/td&gt;
                        &lt;td class=&quot;value&quot;&gt;0&lt;/td&gt;
                    &lt;/tr&gt;
        -->

        <td class="destFleet">
            <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>
            {{ $fleet_event_row->origin_planet_name }}
        </td>
        <td class="destCoords">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->origin_planet_coords->galaxy, 'system' => $fleet_event_row->origin_planet_coords->system]) }}" target="_top">
                [{{ $fleet_event_row->origin_planet_coords->asString() }}]
            </a>
        </td>

        <td class="sendMail">
        </td>    <td class="sendProbe">
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
        <span id="counter-eventlist-{{ $fleet_event_row->id }}" class="friendly textBeefy">
                    load...
        </span>
        </td>
        <td class="arrivalTime">{{ date('H:i:s', $fleet_event_row->mission_time_arrival) }} Clock</td>
        <td class="missionFleet">
            <img src="https://gf1.geo.gfsrv.net/cdn38/2af2939219d8227a11a50ff4df7b51.gif" class="tooltipHTML"  title="Own fleet | Transport" alt=""/>
        </td>

        <td class="originFleet">
            <figure class="planetIcon planet"></figure>
            {{ $fleet_event_row->origin_planet_name }}
        </td>
        <td class="coordsOrigin">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->origin_planet_coords->galaxy, 'system' => $fleet_event_row->origin_planet_coords->system]) }}" target="_top">
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
            @php /** @var \OGame\GameObjects\Models\UnitCollection $fleet_unit */ @endphp
            @foreach ($fleet_event_row->fleet_units->units as $fleet_unit)
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;{{ $fleet_unit->unitObject->title }}:&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet_unit->amount }}&lt;/td&gt;
                &lt;/tr&gt;
            @endforeach
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
                                    &lt;/table&gt;
    &lt;/div&gt;
">
                &nbsp;
            </span>
        </td>

        <td class="destFleet">
            <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>
            {{ $fleet_event_row->destination_planet_name }}
        </td>
        <td class="destCoords">
            <a href="{{ route('galaxy.index', ['galaxy' => $fleet_event_row->destination_planet_coords->galaxy, 'system' => $fleet_event_row->destination_planet_coords->system]) }}" target="_top">
                [{{ $fleet_event_row->destination_planet_coords->asString() }}]
            </a>
        </td>

        <td class="sendMail">
            <span class="reversal reversal_time" ref="1795549">
            <a class="icon_link tooltipHTML recallFleet" data-fleet-id="1795549" title="Recall:| 22.04.2024<br>15:28:45">
                <img src="https://gf2.geo.gfsrv.net/cdna2/89624964d4b06356842188dba05b1b.gif" height="16" width="16" />
            </a>
        </span>
        </td>    <td class="sendProbe">
        </td>
        <td class="sendMail">
        </td>
    </tr>
@endif

<script type="text/javascript">
    (function($) {
        new eventboxCountdown(
            $("#counter-eventlist-{{ $fleet_event_row->id }}"),
            {{ $fleet_event_row->mission_time_arrival }} - {{ time() }},
            $("#eventListWrap"),
            "https:\/\/s255-en.ogame.gameforge.com\/game\/index.php?page=componentOnly&component=eventList&action=checkEvents&ajax=1&asJson=1",
            [0,1]
        );
    })(jQuery);
</script>