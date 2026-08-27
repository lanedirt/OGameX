<br>
<br>
<div class="compacting">
    <span class="ctn ctn4">{{ __('t_ingame.messages.spy_player') }}:</span>
    <!-- TODO: implement dynamic status_abbr_active based on active status of player
    (active last 7 days, last 28 days etc, see galaxy legend for more info). -->
    <span class="status_abbr_active">&nbsp;&nbsp;{!! $playername !!}</span>
    <!-- TODO: implement player activity detection -->
    &nbsp;<span class="ctn ctn4 fright">{{ __('t_ingame.messages.spy_activity') }}: &gt;60 {{ __('t_ingame.messages.spy_minutes_ago') }}.</span>
</div>

<div class="compacting">
    <!-- TODO: implement player class -->
    <span class="ctn ctn4">{{ __('t_ingame.messages.spy_class') }}:</span>
    &nbsp;
    {{ __('t_ingame.messages.spy_unknown') }}
</div>

<div class="compacting">
    <!-- TODO: implement alliance class -->
    <span class="ctn ctn4">{{ __('t_ingame.messages.spy_alliance_class') }}:</span>
    &nbsp;
    <span class="alliance_class small none">{{ __('t_ingame.messages.spy_no_alliance_class') }}</span>
</div>

<br>

<div class="compacting">
    <span class="ctn ctn4">
        <span class="resspan">{{ __('t_ingame.fleet.metal') }}: {{ $resources->metal->getFormattedLong() }}</span>
        <span class="resspan">{{ __('t_ingame.fleet.crystal') }}: {{ $resources->crystal->getFormattedLong() }}</span>
        <span class="resspan">{{ __('t_ingame.fleet.deuterium') }}: {{ $resources->deuterium->getFormattedLong() }}</span>
        <!--
        <br>
        <span class="resspan">Food: 190,005</span>
        <span class="resspan">Population: 27.893Mn</span>
        -->
    </span>
    <!-- TODO: implement this element -->
    <span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 352,927<br/><a href=&quot;#TODO_page=ingame&amp;component=fleetdispatch&amp;galaxy=1&amp;system=4&amp;position=10&amp;type=1&amp;mission=1&amp;am202=51&quot;>S.Cargo: 51</a><br/><a href=&quot;#TODO_page=ingame&amp;component=fleetdispatch&amp;galaxy=1&amp;system=4&amp;position=10&amp;type=1&amp;mission=1&amp;am203=11&quot;>L.Cargo: 11</a><br/>">{{ __('t_ingame.messages.spy_resources') }}: {{ \OGame\Facades\AppUtil::formatNumber($resources->sum()) }}</span>
</div>

<div class="compacting">
    <!-- TODO: implement loot percentage -->
    <span class="ctn ctn4">{{ __('t_ingame.messages.spy_loot') }}: 75%</span>
    <span class="fright">{{ __('t_ingame.messages.spy_counter_esp') }}: {{ $counter_espionage_chance }}%</span>
</div>

<div class="compacting">
    <!-- TODO: implement fleet to resource calculation -->
    <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">{{ __('t_ingame.messages.spy_fleets') }}: 0</span>
    <!-- TODO: implement defense to resource calculation -->
    <span class="ctn ctn4 fright tooltipRight" title="693,000">{{ __('t_ingame.messages.spy_defense') }}: 0</span>
</div>

<br>
