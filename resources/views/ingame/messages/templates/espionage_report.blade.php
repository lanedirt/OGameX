<br>
<br>
<div class="compacting">
    <span class="ctn ctn4">@lang('Player'):</span>
    <!-- TODO: implement dynamic status_abbr_active based on active status of player
    (active last 7 days, last 28 days etc, see galaxy legend for more info). -->
    <span class="status_abbr_active">&nbsp;&nbsp;{!! $playername !!}</span>
    <!-- TODO: implement player activity detection -->
    &nbsp;<span class="ctn ctn4 fright">@lang('Activity'): &gt;60 @lang('minutes ago').</span>
</div>

<div class="compacting">
    <!-- TODO: implement player class -->
    <span class="ctn ctn4">@lang('Class'):</span>
    &nbsp;
    @lang('Unknown')
</div>

<div class="compacting">
    <!-- TODO: implement alliance class -->
    <span class="ctn ctn4">@lang('Alliance Class'):</span>
    &nbsp;
    <span class="alliance_class small none">@lang('No alliance class selected')</span>
</div>

<br>

<div class="compacting">
    <span class="ctn ctn4">
        <span class="resspan">@lang('Metal'): {{ $resources->metal->getFormattedLong() }}</span>
        <span class="resspan">@lang('Crystal'): {{ $resources->crystal->getFormattedLong() }}</span>
        <span class="resspan">@lang('Deuterium'): {{ $resources->deuterium->getFormattedLong() }}</span>
        <!--
        <br>
        <span class="resspan">Food: 190,005</span>
        <span class="resspan">Population: 27.893Mn</span>
        -->
    </span>
    <!-- TODO: implement this element -->
    <span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 352,927<br/><a href=&quot;#TODO_page=ingame&amp;component=fleetdispatch&amp;galaxy=1&amp;system=4&amp;position=10&amp;type=1&amp;mission=1&amp;am202=51&quot;>S.Cargo: 51</a><br/><a href=&quot;#TODO_page=ingame&amp;component=fleetdispatch&amp;galaxy=1&amp;system=4&amp;position=10&amp;type=1&amp;mission=1&amp;am203=11&quot;>L.Cargo: 11</a><br/>">@lang('Resources'): {{ \OGame\Facades\AppUtil::formatNumber($resources->sum()) }}</span>
</div>

<div class="compacting">
    <!-- TODO: implement loot percentage -->
    <span class="ctn ctn4">@lang('Loot'): 75%</span>
    <!-- TODO: implement counter-espionage -->
    <span class="fright">@lang('Chance of counter-espionage'): 0%</span>
</div>

<div class="compacting">
    <!-- TODO: implement fleet to resource calculation -->
    <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">@lang('Fleets'): 0</span>
    <!-- TODO: implement defense to resource calculation -->
    <span class="ctn ctn4 fright tooltipRight" title="693,000">@lang('Defense'): 0</span>
</div>

<br>