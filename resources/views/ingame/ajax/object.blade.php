@php /** @var OGame\Models\Resources $price */ @endphp

<div id="technologydetails" data-technology-id="3">
    <div class="sprite sprite_large building {{ $object->class_name }}">
        @if ($has_requirements)
            <button class="technology_tree  tooltip js_hideTipOnMobile overlay ipiHintable"
                    aria-label="@lang('Open techtree')"
                    data-target="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}"
                    data-ipi-hint="ipiTechnologyTreefusionPlant"
                    data-tooltip-title="Open techtree">
                Techtree
            </button>
        @else
            <button class="technology_tree no_prerequisites tooltip js_hideTipOnMobile overlay ipiHintable"
                    aria-label="@lang('Open techtree')" title="@lang('No requirements available')"
                    data-target="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}"
                    data-ipi-hint="ipiTechnologyTreedeuteriumSynthesizer"> @lang('Techtree')
            </button>
        @endif

        @if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Research)
            @if (!empty($build_active_current) && $build_active_current->object->id == $object->id)
                <a role="button" href="javascript:void(0);" class="tooltip abort_link js_hideTipOnMobile" title="" onclick="cancelbuilding({{ $object->id }},{{ $build_active_current->id }},'Cancel expansion of {{ $object->title }} to level {{ $build_active_current->level_target }}?'); return false;"></a>
            @endif
        @endif
    </div>

    <div class="content">
        <button class="close">✖</button>
        <h3>{!! $title !!}</h3>

        <div class="information">
            @if ($object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
                <span class="amount" data-value="{{ $next_level }}">
                     @lang('Number'): {!! $current_level !!}
                </span>
            @else
                <span class="level" data-value="{{ $next_level }}">
                    @lang('Level') {!! $current_level !!}
                </span>
            @endif
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
                        <strong>@lang('Production'):</strong>
                        <span class="value tooltip" data-value="{{ $production_next->energy->get() }}" title="">{{ $production_next->energy->getFormattedLong() }}
                            <span class="bonus" data-value="{{ ($energy_difference * -1) }}">
                                (+{{ \OGame\Facades\AppUtil::formatNumberLong($energy_difference * -1) }})
                            </span>
                        </span>

                    </li>
                @endif
            </ul>

            <div class="costs">
                @if ($object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
                    <p>@lang('Costs per piece'):</p>
                @else
                    <p>@lang('Required to improve to level') {!! $next_level !!}:</p>
                @endif

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
                        @if ($planet->energyProduction()->get() < $price->energy->get())
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

            @if ($max_build_amount && ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense))
                <div class="build_amount">
                    <label for="build_amount">Number:</label>
                    <input type="text" name="build_amount" id="build_amount" min="0" max="{{ $max_build_amount }}" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, {{ $max_build_amount }});event.stopPropagation();">
                    <button class="maximum">[max. {{ $max_build_amount }}]</button>
                </div>
            @elseif ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station)
                <!-- TODO: implement downgrade feature -->
                <!--<button class="downgrade" data-technology="3" data-name="{{ $title }}">
                    <div class="demolish_img tooltipRel ipiHintable" rel="demolition_costs_tooltip_oneTimeelement"
                         data-ipi-hint="ipiTechnologyTearDowndeuteriumSynthesizer"></div>
                    <span class="label">tear down</span>
                </button>-->
            @endif

            <div class="build-it_wrap">
                <div class="ipiHintable" data-ipi-hint="ipiTechnologyUpgradedeuteriumSynthesizer">
                    <button class="upgrade"
                            @php
                                $disabled_shipyard_upgrading = ($object->type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object->type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense) && $shipyard_upgrading;
                                $ships_being_built = $object->machine_name == 'shipyard' && $ship_or_defense_in_progress;
                            @endphp

                            @if (!$enough_resources || !$requirements_met || !$valid_planet_type || $build_queue_max || !$max_build_amount || $research_lab_upgrading || ($object->machine_name === 'research_lab' && $research_in_progress || $disabled_shipyard_upgrading || $ships_being_built))
                                disabled
                            @else
                            @endif
                            data-technology="{{ $object->id }}">
                            @php
                                $tooltip = $disabled_shipyard_upgrading ? __('Shipyard is being upgraded') :
                                   ($ships_being_built ? __('The Shipyard is still busy') : false);
                            @endphp
                        <span class="tooltip" title="{{ is_string($tooltip) ? $tooltip : '' }}">
                            @if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
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
                    data-target="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object->id]) }}"
                    data-overlay-title="{{ $title }}"> ?
            </button>
            <span class="text">
                {!! $description !!}
            </span>
        </div>

    </div>

</div>
<script type="text/javascript">
    if (document.getElementById("build_amount") !== null) {
        document.getElementById("build_amount").focus();
    }
    var showLifeformBonusCapReached = false
    if (typeof IPI !== 'undefined') {
        IPI.refreshHighlights()
    }
</script>

@if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station))
    {{-- Last building slot warning for buildings --}}
    @include ('ingame.shared.buildings.last-building-slot-warning', ['planet' => $planet])
@else
    {{-- Define default last building slot warning variables for other objects --}}
    <script type="text/javascript">
        var lastBuildingSlot = {
            "showWarning": false,
            "slotWarning": ""
        };
    </script>
@endif
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