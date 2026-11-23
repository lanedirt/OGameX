@php
    /** @var \OGame\ViewModels\FleetEventRowViewModel $fleet */
    $intel = $fleet->fleet_intel_level;
@endphp
@if ($intel->showsCompositionTooltip())
&lt;div class=&quot;htmlTooltip&quot;&gt;
    &lt;h1&gt;@lang('Fleet details'):&lt;/h1&gt;
    &lt;div class=&quot;splitLine&quot;&gt;&lt;/div&gt;
    &lt;table cellpadding=&quot;0&quot; cellspacing=&quot;0&quot; class=&quot;fleetinfo&quot;&gt;
    @if ($intel->showsShipTypes())
            &lt;tr&gt;
                &lt;th colspan=&quot;3&quot;&gt;@lang('Ships'):&lt;/th&gt;
            &lt;/tr&gt;
            @foreach ($fleet->fleet_units->units as $fleet_unit)
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;{{ $fleet_unit->unitObject->title }}:&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $intel->showsShipAmounts() ? $fleet_unit->amount : '?' }}&lt;/td&gt;
                &lt;/tr&gt;
            @endforeach
    @elseif ($intel->showsTotalCount())
            &lt;tr&gt;
                &lt;th colspan=&quot;3&quot;&gt;@lang('Ships'):&lt;/th&gt;
            &lt;/tr&gt;
            &lt;tr&gt;
                &lt;td colspan=&quot;2&quot;&gt;@lang('Ships'):&lt;/td&gt;
                &lt;td class=&quot;value&quot;&gt;{{ $fleet->fleet_unit_count }}&lt;/td&gt;
            &lt;/tr&gt;
    @endif
    @if ($fleet->show_shipment)
                &lt;tr&gt;
                    &lt;th colspan=&quot;3&quot;&gt;&nbsp;&lt;/th&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;th colspan=&quot;3&quot;&gt;@lang('Shipment'):&lt;/th&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Metal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet->resources->metal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Crystal'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet->resources->crystal->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
                &lt;tr&gt;
                    &lt;td colspan=&quot;2&quot;&gt;@lang('Deuterium'):&lt;/td&gt;
                    &lt;td class=&quot;value&quot;&gt;{{ $fleet->resources->deuterium->getFormattedLong() }}&lt;/td&gt;
                &lt;/tr&gt;
    @endif
            &lt;/table&gt;
    &lt;/div&gt;
@endif
