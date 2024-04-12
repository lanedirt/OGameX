@php /** @var OGame\Models\Resources $price */ @endphp


<input type="hidden" name="modus" value="1">
<input type="hidden" name="type" value="{!! $id !!}">
<input type="hidden" name="planet_id" value="{!! $planet_id !!}">



<div id="demolish1" style="display: none;">
    <div class="htmlTooltip">
        <h1>Deconstruction costs:</h1>
        <div class="splitLine"></div>
        <table cellpadding="0" cellspacing="0" class="demolishinfo">
            <tr class="costReduction">
                <td class="res">Ion technology bonus:</td>
                <td class="value undermark">-0%</td>
            </tr>
            <tr>
                <td class="res">Duration:</td>
                <td class="value">2s</td>
            </tr>
        </table>
    </div>    </div>

<div id="{{ $object_type }}_{!! $id !!}_large" class="pic">

    <a href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $id]) }}" class="techtree_link js_hideTipOnMobile tooltip overlay"
       data-overlay-title="Techtree - {!! $title !!}"
       title="No requirements available">
        <div class="techtree_img_disabled"></div>
        <span class="label">Techtree</span>
    </a>

    @if ($build_active_current)
    <a role="button" href="javascript:void(0);" class="tooltip abort_link js_hideTipOnMobile" title="Cancel expansion of {{ $title }} to level {{ $next_level }}?" onclick="cancelProduction({{ $build_active_current['object']['id'] }},{{ $build_active_current['id'] }},&quot;Cancel expansion of {{ $title }} to level {{ $next_level }}?&quot;); return false;"></a>
    @endif
</div>

<div id="content">

    <h2>{!! $title !!}</h2>
    <a id="close" class='close_details'
       href="javascript:void(0);"
       onclick="gfSlider.hide(getElementByIdWithCache('detail')); return false;">
    </a>
    @if ($object_type == 'ship' || $object_type == 'defense')
        <span class="number-info">
            Number: {{ $current_amount }}
        </span>
    @else
        <span class="level">
            Level {!! $current_level !!}
        <span class="undermark"></span>
	</span>
    @endif
    <br class="clearfloat"/>

    @if ($object_type == 'ship' || $object_type == 'defense')
        <ul class="production_info narrow">
            <li>
                Production duration
                <span class="time" id="buildDuration">
                    {!! $production_time !!}
                    <span class="undermark"></span>
                </span>
            </li>
            <li>
                Construction possible:
                @if ($requirements_met)
                    <span class="time" id="possibleInTime">
                        {!! $production_time !!}
                    </span>
                @else
                    <span class="time" id="possibleInTime">
                        <a href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}" class="value overlay tooltip tpd-hideOnClickOutside" title="Requirements are not met" data-overlay-title="&nbsp;">
                            Unknown
                        </a>
                    </span>
                @endif
            </li>
        </ul>
        <div class="enter">
            <p class="amount">Number:</p>
            <div class="clearfix maxlink_wrap">
                <input id="number" type="text" class="amount_input" pattern="[0-9,.]*" size="5" name="amount" value="1" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, 9999);event.stopPropagation();">
                <div class="maxlink_arrow"></div>
                <a id="maxlink" class="tooltip js_hideTipOnMobile" title="Produce maximum number" href="javascript:void(0);" onclick="document.forms['form'].amount.value = {{ $max_build_amount }};">
                    [max. {{ $max_build_amount }}]
                </a>
            </div>
        </div>
    @else
    <ul class="production_info ">
        <li>
            Production duration
            <span class="time" id="buildDuration">
                {!! $production_time !!}
                <span class="undermark"></span>
            </span>
        </li>
        <li>
            Construction possible:
            @if ($requirements_met)
                <span class="time" id="possibleInTime">
                    {!! $production_time !!}
                </span>
            @else
                <span class="time" id="possibleInTime">
                    <a href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}" class="value overlay tooltip tpd-hideOnClickOutside" title="Requirements are not met" data-overlay-title="&nbsp;">
                        Unknown
                    </a>
                </span>
            @endif
        </li>
        @if ($energy_difference > 0)
            <li>
                Energy needed:
                <span class="time">{{ $energy_difference }}</span>
            </li>
        @elseif ($energy_difference < 0)
            <li>
                Production:
                <span class="time">{{ $production_next->energy->get() }} <span class="undermark"> (+{{ ($energy_difference * -1) }})</span>
            </li>
        @endif
    </ul>
    @endif

    <div class="costs_wrap">

        @if ($object_type == 'ship' || $object_type == 'defense')
            <p class="costs_info">Costs per piece:</p>
        @else
            <p class="costs_info">Required to improve to level {!! $next_level !!}:</p>
        @endif

        <ul id="costs">
            @if (!empty($price->metal->get()))
            <li class="metal tooltip" title="{!! $price->metal->getFormatted() !!} Metal">
                <div class="resourceIcon metal"></div>
                <div class="cost @if ($planet->metal()->get() < $price->metal->get())
                        overmark
                        @endif">
                    {!! $price->metal->getFormatted() !!}	                </div>
            </li>
            @endif
            @if (!empty($price->crystal->get()))
            <li class="crystal tooltip" title="{!! $price->crystal->getFormatted() !!} Crystal">
                <div class="resourceIcon crystal"></div>
                <div class="cost @if ($planet->crystal()->get() < $price->crystal->get())
                        overmark
                        @endif">
                    {!!$price->crystal->getFormatted() !!}	                </div>
            </li>
            @endif
            @if (!empty($price->deuterium->get()))
            <li class="deuterium tooltip" title="{!! $price->deuterium->get() !!} Deuterium">
                <div class="resourceIcon deuterium"></div>
                <div class="cost @if ($planet->deuterium()->get() < $price->deuterium->get())
                        overmark
                        @endif">
                    {!! $price->deuterium->getFormatted() !!}	                </div>
            </li>
            @endif
            @if (!empty($price->energy->get()))
                <li class="energy tooltip" title="{!! $price->energy->getFormatted() !!} Energy">
                    <div class="resourceIcon energy"></div>
                    <div class="cost @if ($planet->energy()->get() < $price->energy->get())
                            overmark
                            @endif">
                        {!! $price->energy->getFormatted() !!}	                </div>
                </li>
            @endif
        </ul>
    </div>

    <div class="build-it_wrap">
        <!-- all ok -->
        <div class="premium_info_placeholder"></div>
        <a class="@if (!$enough_resources || !$requirements_met || $build_queue_max)
                build-it_disabled isWorking
@else
build-it
                @endif"
           href="javascript:void(0);">
            <span>
            @if ($object_type == 'ship' || $object_type == 'defense')
            Build
            @elseif (!empty($build_active['id']))
            In queue
            @else
            Improve
            @endif</span>
        </a>

    </div>

</div>
<br clear="all"/>

<div id="description">
    @if ($storage)
    <div class="capacity_display">
        <p>Storage capacity</p>

        <div class="bar_container" data-current-amount="{{ $current_storage }}" data-capacity="{{ $max_storage }}">
            <div class="filllevel_bar filllevel_undermark" style="width: {{ number_format(($current_storage / $max_storage) / 100, 2, '.', '') }}%;"></div>
            <div class="premium_bar"></div>
        </div>

        <div id="remainingresources">
            <span class="undermark">{{ number_format($current_storage, 0, ',', '.') }}</span> /
            <span id="maxresources">{{ number_format($max_storage, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="fill_capacity_info">
        <div class="arrow_description"></div>

        <a class="btn btn_confirm fright" href="{{ route('merchant.index') }}#animation=false&page=traderResources" data-overlay-title="Get more resources">View offers</a>
        <div class="info_txt">Gain resources to immediately refill your storage</div>

    </div>

    <br class="clearfloat">
    @endif

    <div class="txt_box  ">
        <a class="tooltip js_hideTipOnMobile help overlay"
           href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}"
           title="More details">
        </a>
        <p class="description_txt" style="width: 90%">{!! $description !!}</p>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#number").focus();
        initOverlays();
    });

    $(".build-it_disabled:not(.isWorking)")
            .click(function() {
                errorBoxDecision('Error','You need a Commander to be able to use the building queue. Would you like to learn more about the advantages of a Commander?','yes','No', function() { window.location.href = '{{ route('premium.index', ['openDetail' => 2]) }}' });
            });

    var loca = loca || {};
    loca = $.extend({},
            loca,
            {
                'allError'				:	'Error',
                'infoBuildlist'			:	'You need a Commander to be able to use the building queue. Would you like to learn more about the advantages of a Commander?',
                'allYes'				:	'yes',
                'allNo'					:	'No',
                'allOk'					:	'Ok',
                'noRocketsiloCapacity'	:	'Not enough capacity. Upgrade missile silo.',
                'allDetailNow'			:	'now'
            }
    );

    var buttonClass = "build-it";

    var overlayTitle = 'Start with DM';

    var showSlotWarning = 1;

    var buttonState = 1;

    var techID = 1;

    var isRocketAndStorageNotFree = 0;

    var couldBeBuild = 1;

    var isShip = 0;

    var isRocket = 0;

    var hasCommander = 0;

    var buildableAt = null;

    var error = 2000;

    var premiumerror = 0;

    var showErrorOnPremiumbutton = 0;

    var errorlist = {
        '2000' : 'With a price of 0 DM the profit margin is too low for the merchant!',
        '100' : 'The merchant can only deliver resources to an amount totalling 10.000.000 to you',
        '10' : 'Not enough storage capacity. - Would you like to expand your storage?',
        '20' : 'Not enough storage capacity. - Would you like to expand your storage?',
        '30' : 'Not enough storage capacity. - Would you like to expand your storage?',
        '1000' : 'Not enough Dark Matter available! Do you want to buy some now?'
    };


    var isBuildlistNeeded = 0;

    //var showCommanderHint = (!buttonState && !hasCommander && isBuildlistNeeded && couldBeBuild && (isShip || isRocket));

    var showNoPremiumError = 0;

    var pageToReload = "{{ route('resources.index') }}";

    var isBusy = 0;

    initTechDetailsAjax();
</script>
