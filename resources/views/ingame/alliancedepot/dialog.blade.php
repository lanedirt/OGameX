@php
    // Calculate capacity: 10,000 deuterium per level per hour
    $capacity = $alliance_depot_level * 10000;
@endphp

<div id="supplydepotlayer">
    <div id="inner">
        <div class="fleft sprite building large building34"></div>
        <div class="content">
            <p>@lang('The alliance depot supplies fuel to friendly fleets in orbit helping with defence. For each upgrade level of the alliance depot, a special demand of deuterium per hour can be sent to an orbiting fleet.')</p>
            <span class="capacity">@lang('Capacity'): {{ number_format($capacity, 0, ',', '.') }} / {{ number_format($capacity, 0, ',', '.') }}</span>
            @if (count($holding_fleets) === 0)
                <div class="textBeefy">@lang('There are no holding fleets!')</div>
            @else
                {{-- TODO: Show holding fleets when they exist --}}
                <div class="textBeefy">{{ count($holding_fleets) }} @lang('fleet(s) holding')</div>
            @endif
        </div>
    </div>
    <br class="clearfloat">
</div>

<script type="text/javascript">
    var supplyTimes = [];

    (function($) {
        // Initialize Alliance Depot if function exists
        if (typeof initAllianceDepot === 'function') {
            initAllianceDepot();
        }
    })($);
</script>
