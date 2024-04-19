@php /** @var OGame\Models\Resources $price */ @endphp


<div id="technologydetails" data-technology-id="3">

    <div class="sprite sprite_large building {{ $object->class_name }}">
        <button class="technology_tree no_prerequisites tooltip js_hideTipOnMobile overlay ipiHintable"
                aria-label="open techtree" title="No requirements available"
                data-target="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $id]) }}"
                data-ipi-hint="ipiTechnologyTreedeuteriumSynthesizer"> techtree
        </button>
    </div>

    <div class="content">
        <button class="close">✖</button>
        <h3>{!! $title !!}</h3>

        <div class="information">
            <span class="level"
                  data-value="{{ $next_level }}">
                Level {!! $current_level !!}
            </span>
            <ul class="narrow">

                <li class="build_duration"><strong>@lang('Production duration:')</strong>
                    <time class="value tooltip" datetime="{{ $production_datetime }}" title="">{!! $production_time !!}
                        <!--
                        For event discounts
                        <span class="bonus" data-value="0">
                            -0%)
                        </span>
                        -->
                    </time>
                </li>

                @if ($energy_difference > 0)
                    <li class="additional_energy_consumption"><strong>@lang('Energy needed:')</strong>
                        <span class="value tooltip"
                              data-value="{{ $energy_difference }}"
                              title="">{{ $energy_difference }}
                        </span>
                    </li>
                @elseif ($energy_difference < 0)
                    <li class="energy_production">
                        <strong>Production:</strong>
                        <span class="value tooltip" data-value="{{ $production_next->energy->get() }}" title="">{{ $production_next->energy->get() }}
                            <span class="bonus" data-value="{{ ($energy_difference * -1) }}">
                                (+{{ ($energy_difference * -1) }})
                            </span>
                        </span>

                    </li>
                @endif



            </ul>

            <div class="costs">

                <p>@lang('Required to improve to level') {!! $next_level !!}:</p>

                <ul class="ipiHintable" data-ipi-hint="">
                    @if (!empty($price->metal->get()))
                        <li class="resource metal icon sufficient tooltip js_hideTipOnMobile
                        @if ($planet->metal()->get() < $price->metal->get())
                        insufficient
                        @else
                        sufficient
                        @endif" data-value="{{ $price->metal->get() }}"
                            aria-label="{!! $price->metal->getFormattedLong() !!}  @lang('Metal')" title="{!! $price->metal->getFormattedLong() !!}  @lang('Metal')">
                            {!! $price->metal->getFormatted() !!}
                        </li>
                    @endif
                    @if (!empty($price->crystal->get()))
                            <li class="resource crystal icon sufficient tooltip js_hideTipOnMobile
                        @if ($planet->crystal()->get() < $price->crystal->get())
                        insufficient
                        @else
                        sufficient
                        @endif" data-value="{{ $price->crystal->get() }}"
                                aria-label="{!! $price->crystal->getFormattedLong() !!}  @lang('Crystal')" title="{!! $price->crystal->getFormattedLong() !!}  @lang('Crystal')">
                                {!! $price->crystal->getFormatted() !!}
                            </li>
                    @endif
                    @if (!empty($price->deuterium->get()))
                            <li class="resource deuterium icon sufficient tooltip js_hideTipOnMobile
                        @if ($planet->deuterium()->get() < $price->deuterium->get())
                        insufficient
                        @else
                        sufficient
                        @endif" data-value="{{ $price->deuterium->get() }}"
                                aria-label="{!! $price->deuterium->getFormattedLong() !!}  @lang('Deuterium')" title="{!! $price->deuterium->getFormattedLong() !!}  @lang('Deuterium')">
                                {!! $price->deuterium->getFormatted() !!}
                            </li>
                    @endif
                    @if (!empty($price->energy->get()))
                            <li class="resource energy icon sufficient tooltip js_hideTipOnMobile
                        @if ($planet->energy()->get() < $price->energy->get())
                        insufficient
                        @else
                        sufficient
                        @endif" data-value="{{ $price->energy->get() }}"
                                aria-label="{!! $price->energy->getFormattedLong() !!}  @lang('Energy')" title="{!! $price->energy->getFormattedLong() !!}  @lang('Energy')">
                                {!! $price->energy->getFormatted() !!}
                            </li>
                    @endif
                </ul>

            </div>

            <div id="demolition_costs_tooltip" class="htmlTooltip">
                <h1>Deconstruction costs</h1>

                <div class="splitLine"></div>

                <table class="demolition_costs">

                    <tr class="demolition_costs_bonus">
                        <th>Ion technology bonus:</th>
                        <td data-value="24">-24%</td>
                    </tr>
                    <tr class="metal">
                        <th>Metal:</th>
                        <td class="sufficient" data-value="33279">33,279</td>
                    </tr>
                    <tr class="crystal">
                        <th>Crystal:</th>
                        <td class="sufficient" data-value="11092">11,092</td>
                    </tr>
                    <tr class="demolition_duration">
                        <th>Duration:</th>
                        <td>
                            <time datetime="PT6M22S"></time>6m 22s
                        </td>
                    </tr>
                </table>
            </div>

            <div id="demolition_costs_tooltip_oneTimeelement" class="htmlTooltip" style="display: none">
                <h1>Deconstruction costs</h1>
                <div class="splitLine"></div>
                <table class="demolition_costs">
                    <tr class="demolition_costs_bonus">
                        <th>Ion technology bonus:</th>
                        <td data-value="24">-24%</td>
                    </tr>
                    <tr class="metal">
                        <th>Metal:</th>
                        <td class="sufficient" data-value="33279">33,279</td>
                    </tr>
                    <tr class="crystal">
                        <th>Crystal:</th>
                        <td class="sufficient" data-value="11092">11,092</td>
                    </tr>
                    <tr class="demolition_duration">
                        <th>Duration:</th>
                        <td>
                            <time datetime="PT6M22S"></time>6m 22s
                        </td>
                    </tr>
                </table>

            </div>

            <button class="downgrade" data-technology="3" data-name="{{ $title }}">

                <div class="demolish_img tooltipRel ipiHintable" rel="demolition_costs_tooltip_oneTimeelement"
                     data-ipi-hint="ipiTechnologyTearDowndeuteriumSynthesizer"></div>
                <span class="label">tear down</span>
            </button>

            <div class="build-it_wrap">
                <div class="ipiHintable" data-ipi-hint="ipiTechnologyUpgradedeuteriumSynthesizer">
                    <button class="upgrade"
                            @if (!$enough_resources || !$requirements_met || $build_queue_max)
                                disabled
                            @else
                            @endif
                            data-technology="{{ $object->id }}">
                        <span class="tooltip" title="">
                            @if ($object_type == 'ship' || $object_type == 'defense')
                                Build
                            @elseif (!empty($build_active->id))
                                In queue
                            @else
                                Improve
                            @endif
                        </span>
                    </button>
                </div>
                <!--
                <a class="build-it_premium" href="javascript:void(0);" data-title="" data-url="#TODO_page=premium&amp;openDetail=2" data-question="You need a Commander to be able to use the building queue. Would you like to learn more about the advantages of a Commander?">
                    <span class="tooltip tpd-hideOnClickOutside" title="">Hire Commander</span>
                </a>
                -->
            </div>

        </div>

    </div>

    <div class="description">
        @if ($storage)
            <div class="capacity">
                <span class="label">@lang('Storage capacity:')</span>
                <meter min="0" max="{{ $max_storage }}" low="{{ (int)($max_storage * 0.9) }}" high="{{ $max_storage }}" optimum="0" value="{{ $current_storage }}"></meter>
                <span class="description">
                        <span class="good">{{ number_format($current_storage, 0, ',', '.') }}</span> / {{ number_format($max_storage, 0, ',', '.') }}
                    </span>
            </div>

            <div class="fill_capacity_info">
                <div class="arrow_description"></div>
                <div class="action">
                    <div class="description">@lang('Gain resources to immediately refill your storage')</div>
                    <a class="offers btn btn_confirm fright" href="{{ route('merchant.index') }}#animation=false&page=traderResources">@lang('View offers')</a>
                </div>
            </div>
        @endif
        <div class="txt_box">
            <button class="details tooltip js_hideTipOnMobile overlay" aria-label="@lang('More details')" title="@lang('More details')"
                    data-target="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}"
                    data-overlay-title="{{ $title }}"> ?
            </button>
            <span class="text">
                {!! $description !!}
            </span>
        </div>

    </div>

</div>
<script type="text/javascript">    if (document.getElementById("build_amount") !== null) {
        document.getElementById("build_amount").focus();
    }
    var showLifeformBonusCapReached = false
    var lastBuildingSlot = {
        "showWarning": false,
        "slotWarning": "This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item (e.g. <a href='#TODO_link'>Gold Planet Fields<\\/a>) to obtain more slots. Are you sure you want to build this building?"
    }
    if (typeof IPI !== 'undefined') {
        IPI.refreshHighlights()
    }</script>


<!--
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
    </div>
</div>

<div id="{{ $object_type }}_{!! $id !!}_large" class="pic">

    <a href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $id]) }}"
       class="techtree_link js_hideTipOnMobile tooltip overlay"
       data-overlay-title="Techtree - {!! $title !!}"
       title="No requirements available">
        <div class="techtree_img_disabled"></div>
        <span class="label">Techtree</span>
    </a>

    @if ($build_active_current)
        <a role="button" href="javascript:void(0);" class="tooltip abort_link js_hideTipOnMobile"
           title="Cancel expansion of {{ $title }} to level {{ $next_level }}?"
           onclick="cancelProduction({{ $build_active_current->object->id }},{{ $build_active_current->id }},&quot;Cancel expansion of {{ $title }} to level {{ $next_level }}?&quot;); return false;"></a>
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
                        <a href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}"
                           class="value overlay tooltip tpd-hideOnClickOutside" title="Requirements are not met"
                           data-overlay-title="&nbsp;">
                            Unknown
                        </a>
                    </span>
                @endif
            </li>
        </ul>
        <div class="enter">
            <p class="amount">Number:</p>
            <div class="clearfix maxlink_wrap">
                <input id="number" type="text" class="amount_input" pattern="[0-9,.]*" size="5" name="amount" value="1"
                       onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, 9999);event.stopPropagation();">
                <div class="maxlink_arrow"></div>
                <a id="maxlink" class="tooltip js_hideTipOnMobile" title="Produce maximum number"
                   href="javascript:void(0);" onclick="document.forms['form'].amount.value = {{ $max_build_amount }};">
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
                    <a href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $id]) }}"
                       class="value overlay tooltip tpd-hideOnClickOutside" title="Requirements are not met"
                       data-overlay-title="&nbsp;">
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
                        {!! $price->metal->getFormatted() !!}                    </div>
                </li>
            @endif
            @if (!empty($price->crystal->get()))
                <li class="crystal tooltip" title="{!! $price->crystal->getFormatted() !!} Crystal">
                    <div class="resourceIcon crystal"></div>
                    <div class="cost @if ($planet->crystal()->get() < $price->crystal->get())
                        overmark
                        @endif">
                        {!!$price->crystal->getFormatted() !!}                    </div>
                </li>
            @endif
            @if (!empty($price->deuterium->get()))
                <li class="deuterium tooltip" title="{!! $price->deuterium->get() !!} Deuterium">
                    <div class="resourceIcon deuterium"></div>
                    <div class="cost @if ($planet->deuterium()->get() < $price->deuterium->get())
                        overmark
                        @endif">
                        {!! $price->deuterium->getFormatted() !!}                    </div>
                </li>
            @endif
            @if (!empty($price->energy->get()))
                <li class="energy tooltip" title="{!! $price->energy->getFormatted() !!} Energy">
                    <div class="resourceIcon energy"></div>
                    <div class="cost @if ($planet->energy()->get() < $price->energy->get())
                            overmark
                            @endif">
                        {!! $price->energy->getFormatted() !!}                    </div>
                </li>
            @endif
        </ul>
    </div>

    <div class="build-it_wrap">
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
                @elseif (!empty($build_active->id))
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
                <div class="filllevel_bar filllevel_undermark"
                     style="width: {{ number_format(($current_storage / $max_storage) / 100, 2, '.', '') }}%;"></div>
                <div class="premium_bar"></div>
            </div>

            <div id="remainingresources">
                <span class="undermark">{{ number_format($current_storage, 0, ',', '.') }}</span> /
                <span id="maxresources">{{ number_format($max_storage, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="fill_capacity_info">
            <div class="arrow_description"></div>

            <a class="btn btn_confirm fright" href="{{ route('merchant.index') }}#animation=false&page=traderResources"
               data-overlay-title="Get more resources">View offers</a>
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
-->
<script>
    $(document).ready(function () {
        $("#number").focus();
        initOverlays();
    });

    $(".build-it_disabled:not(.isWorking)")
        .click(function () {
            errorBoxDecision('Error', 'You need a Commander to be able to use the building queue. Would you like to learn more about the advantages of a Commander?', 'yes', 'No', function () {
                window.location.href = '{{ route('premium.index', ['openDetail' => 2]) }}'
            });
        });

    var loca = loca || {};
    loca = $.extend({},
        loca,
        {
            'allError': 'Error',
            'infoBuildlist': 'You need a Commander to be able to use the building queue. Would you like to learn more about the advantages of a Commander?',
            'allYes': 'yes',
            'allNo': 'No',
            'allOk': 'Ok',
            'noRocketsiloCapacity': 'Not enough capacity. Upgrade missile silo.',
            'allDetailNow': 'now'
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
        '2000': 'With a price of 0 DM the profit margin is too low for the merchant!',
        '100': 'The merchant can only deliver resources to an amount totalling 10.000.000 to you',
        '10': 'Not enough storage capacity. - Would you like to expand your storage?',
        '20': 'Not enough storage capacity. - Would you like to expand your storage?',
        '30': 'Not enough storage capacity. - Would you like to expand your storage?',
        '1000': 'Not enough Dark Matter available! Do you want to buy some now?'
    };


    var isBuildlistNeeded = 0;

    //var showCommanderHint = (!buttonState && !hasCommander && isBuildlistNeeded && couldBeBuild && (isShip || isRocket));

    var showNoPremiumError = 0;

    var pageToReload = "{{ route('resources.index') }}";

    var isBusy = 0;

</script>
