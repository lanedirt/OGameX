@php /** @var OGame\Models\Resources $price */ @endphp

<div id="technologydetails" data-technology-id="3">
    <div class="sprite sprite_large building {{ $object->class_name }}">
        @if ($has_requirements)
            <button class="technology_tree  tooltip js_hideTipOnMobile overlay ipiHintable"
                    aria-label="{{ __('t_ingame.ajax_object.open_techtree') }}"
                    data-target="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}"
                    data-ipi-hint="ipiTechnologyTreefusionPlant"
                    data-tooltip-title="{{ __('t_ingame.ajax_object.open_techtree') }}">
                {{ __('t_ingame.ajax_object.techtree') }}
            </button>
        @else
            <button class="technology_tree no_prerequisites tooltip js_hideTipOnMobile overlay ipiHintable"
                    aria-label="{{ __('t_ingame.ajax_object.open_techtree') }}" title="{{ __('t_ingame.ajax_object.no_requirements') }}"
                    data-target="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}"
                    data-ipi-hint="ipiTechnologyTreedeuteriumSynthesizer"> {{ __('t_ingame.ajax_object.techtree') }}
            </button>
        @endif

        @if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Research)
            @if (!empty($build_active_current) && $build_active_current->object->id == $object->id)
                <a role="button" href="javascript:void(0);" class="tooltip abort_link js_hideTipOnMobile" title="" onclick="cancelbuilding({{ $object->id }},{{ $build_active_current->id }},'{{ __('t_ingame.ajax_object.cancel_expansion_confirm', ['name' => $object->title, 'level' => $build_active_current->level_target]) }}'); return false;"></a>
            @endif
        @endif
    </div>

    <div class="content">
        <button class="close">✖</button>
        <h3>{!! $title !!}</h3>

        <div class="information">
            @if ($object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
                <span class="amount" data-value="{{ $next_level }}">
                     {{ __('t_ingame.ajax_object.number') }}: {!! $current_level !!}
                </span>
            @else
                <span class="level" data-value="{{ $next_level }}">
                    {{ __('t_ingame.ajax_object.level') }} {!! $current_level !!}
                </span>
            @endif
            <ul class="narrow">

                <li class="build_duration"><strong>{{ __('t_ingame.ajax_object.production_duration') }}</strong>
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
                    <li class="additional_energy_consumption"><strong>{{ __('t_ingame.ajax_object.energy_needed') }}</strong>
                        <span class="value tooltip"
                              data-value="{{ $energy_difference }}"
                              title="">{{ $energy_difference }}
                        </span>
                    </li>
                @elseif ($energy_difference < 0)
                    <li class="energy_production">
                        <strong>{{ __('t_ingame.ajax_object.production') }}:</strong>
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
                    <p>{{ __('t_ingame.ajax_object.costs_per_piece') }}:</p>
                @else
                    <p>{{ __('t_ingame.ajax_object.required_to_improve') }} {!! $next_level !!}:</p>
                @endif

                <ul class="ipiHintable" data-ipi-hint="">
                    @if (!empty($price->metal->get()))
                        <li class="resource metal icon sufficient tooltip js_hideTipOnMobile
                        @if ($planet->metal()->get() < $price->metal->get())
                        insufficient
                        @else
                        sufficient
                        @endif" data-value="{{ $price->metal->get() }}"
                            aria-label="{!! $price->metal->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.metal') }}" title="{!! $price->metal->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.metal') }}">
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
                                aria-label="{!! $price->crystal->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.crystal') }}" title="{!! $price->crystal->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.crystal') }}">
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
                                aria-label="{!! $price->deuterium->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.deuterium') }}" title="{!! $price->deuterium->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.deuterium') }}">
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
                                aria-label="{!! $price->energy->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.energy') }}" title="{!! $price->energy->getFormattedLong() !!}  {{ __('t_ingame.ajax_object.energy') }}">
                                {!! $price->energy->getFormatted() !!}
                            </li>
                    @endif
                </ul>

            </div>

            @if ($can_downgrade && $downgrade_price !== null)
            <div id="demolition_costs_tooltip" class="htmlTooltip">
                <h1>{{ __('t_ingame.ajax_object.deconstruction_costs') }}</h1>

                <div class="splitLine"></div>

                <table class="demolition_costs">
                    @if ($ion_technology_level > 0)
                    <tr class="demolition_costs_bonus">
                        <th>{{ __('t_ingame.ajax_object.ion_technology_bonus') }}</th>
                        <td data-value="{{ $ion_technology_bonus }}">-{{ $ion_technology_bonus }}%</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->metal->get() > 0)
                    <tr class="metal">
                        <th>{{ __('t_ingame.ajax_object.metal') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->metal->get() }}">{{ $downgrade_price->metal->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->crystal->get() > 0)
                    <tr class="crystal">
                        <th>{{ __('t_ingame.ajax_object.crystal') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->crystal->get() }}">{{ $downgrade_price->crystal->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->deuterium->get() > 0)
                    <tr class="deuterium">
                        <th>{{ __('t_ingame.ajax_object.deuterium') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->deuterium->get() }}">{{ $downgrade_price->deuterium->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_duration_formatted)
                    <tr class="demolition_duration">
                        <th>{{ __('t_ingame.ajax_object.duration') }}</th>
                        <td>
                            <time datetime="{{ $downgrade_duration_formatted }}"></time>{{ $downgrade_duration_formatted }}
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            <div id="demolition_costs_tooltip_oneTimeelement" class="htmlTooltip" style="display: none">
                <h1>{{ __('t_ingame.ajax_object.deconstruction_costs') }}</h1>
                <div class="splitLine"></div>
                <table class="demolition_costs">
                    @if ($ion_technology_level > 0)
                    <tr class="demolition_costs_bonus">
                        <th>{{ __('t_ingame.ajax_object.ion_technology_bonus') }}</th>
                        <td data-value="{{ $ion_technology_bonus }}">-{{ $ion_technology_bonus }}%</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->metal->get() > 0)
                    <tr class="metal">
                        <th>{{ __('t_ingame.ajax_object.metal') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->metal->get() }}">{{ $downgrade_price->metal->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->crystal->get() > 0)
                    <tr class="crystal">
                        <th>{{ __('t_ingame.ajax_object.crystal') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->crystal->get() }}">{{ $downgrade_price->crystal->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_price->deuterium->get() > 0)
                    <tr class="deuterium">
                        <th>{{ __('t_ingame.ajax_object.deuterium') }}:</th>
                        <td class="sufficient" data-value="{{ $downgrade_price->deuterium->get() }}">{{ $downgrade_price->deuterium->getFormatted() }}</td>
                    </tr>
                    @endif
                    @if ($downgrade_duration_formatted)
                    <tr class="demolition_duration">
                        <th>{{ __('t_ingame.ajax_object.duration') }}</th>
                        <td>
                            <time datetime="{{ $downgrade_duration_formatted }}"></time>{{ $downgrade_duration_formatted }}
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            @if ($max_build_amount && ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense))
                <div class="build_amount">
                    <label for="build_amount">{{ __('t_ingame.ajax_object.number_label') }}</label>
                    <input type="text" name="build_amount" id="build_amount" min="0" max="{{ $max_build_amount }}" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, {{ $max_build_amount }});event.stopPropagation();">
                    <button class="maximum">{{ __('t_ingame.ajax_object.max_btn', ['amount' => $max_build_amount]) }}</button>
                </div>
            @elseif ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station)
                @if ($can_downgrade && $current_level > 0)
                    <button class="downgrade" data-technology="{{ $object->id }}" data-name="{{ $title }}"
                            @if ($is_in_vacation_mode)
                                disabled
                            @endif>
                        <div class="demolish_img tooltipRel ipiHintable" rel="demolition_costs_tooltip_oneTimeelement"
                             data-ipi-hint="ipiTechnologyTearDown{{ $object->class_name }}"></div>
                        <span class="label tooltip" title="{{ $is_in_vacation_mode ? __('t_ingame.ajax_object.vacation_mode') : '' }}">{{ __('t_ingame.ajax_object.tear_down_btn') }}</span>
                    </button>
                @endif
            @endif

            <div class="build-it_wrap">
                <div class="ipiHintable" data-ipi-hint="ipiTechnologyUpgradedeuteriumSynthesizer">
                    <button class="upgrade"
                            @php
                                $disabled_shipyard_upgrading = ($object->type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object->type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense) && $shipyard_upgrading;
                                $ships_being_built = ( $object->machine_name == 'shipyard' ||  $object->machine_name == 'nano_factory') && $ship_or_defense_in_progress;
                            @endphp

                            @if (!$enough_resources || !$requirements_met || !$character_class_met || !$valid_planet_type || $build_queue_max || !$max_build_amount || $research_lab_upgrading || ($object->machine_name === 'research_lab' && $research_in_progress || $disabled_shipyard_upgrading || $ships_being_built) || $is_in_vacation_mode || (($object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Station) && $fields_exceeded))
                                disabled
                            @else
                            @endif
                            data-technology="{{ $object->id }}">
                            @php
                                $tooltip = false;
                                if ($is_in_vacation_mode) {
                                    $tooltip = __('t_ingame.ajax_object.vacation_mode');
                                } elseif (!$character_class_met) {
                                    $tooltip = __('t_ingame.ajax_object.wrong_character_class');
                                } elseif ($disabled_shipyard_upgrading) {
                                    $tooltip = __('t_ingame.ajax_object.shipyard_upgrading');
                                } elseif ($ships_being_built) {
                                    $tooltip = __('t_ingame.ajax_object.shipyard_busy');
                                }
                            @endphp
                        <span class="tooltip" title="{{ is_string($tooltip) ? $tooltip : (($object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type === \OGame\GameObjects\Models\Enums\GameObjectType::Station) && $fields_exceeded ? __('t_ingame.ajax_object.not_enough_fields') : '') }}">
                            @if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
                                {{ __('t_ingame.ajax_object.build') }}
                            @elseif (!empty($build_active->id))
                                {{ __('t_ingame.ajax_object.in_queue') }}
                            @else
                                {{ __('t_ingame.ajax_object.improve') }}
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
                <span class="label">{{ __('t_ingame.ajax_object.storage_capacity') }}</span>
                <meter min="0" max="{{ $max_storage }}" low="{{ (int)($max_storage * 0.9) }}" high="{{ $max_storage }}" optimum="0" value="{{ $current_storage }}"></meter>
                <span class="description">
                        <span class="good">{{ number_format($current_storage, 0, ',', '.') }}</span> / {{ number_format($max_storage, 0, ',', '.') }}
                    </span>
            </div>

            <div class="fill_capacity_info">
                <div class="arrow_description"></div>
                <div class="action">
                    <div class="description">{{ __('t_ingame.ajax_object.gain_resources') }}</div>
                    <a class="offers btn btn_confirm fright" href="{{ route('merchant.index') }}#animation=false&page=traderResources">{{ __('t_ingame.ajax_object.view_offers') }}</a>
                </div>
            </div>
        @endif

        @if ($is_missile_silo && $current_level > 0)
            <div class="capacity">
                <span class="label">{{ __('t_ingame.ajax_object.storage_capacity') }}</span>
                <meter min="0" max="{{ $max_missiles }}" low="{{ (int)($max_missiles * 0.9) }}" high="{{ $max_missiles - 0.1 }}" optimum="0" value="{{ $current_missiles }}"></meter>
                <span class="description">
                    <span class="@if($current_missiles >= $max_missiles * 0.9) criticial @else good @endif">{{ $current_missiles }}</span> / {{ $max_missiles }}
                </span>
            </div>

            @if ($current_missiles > 0)
                <div class="fill_capacity_info">
                    <div class="arrow_description"></div>
                    <div class="action">
                        <div class="description">{{ __('t_ingame.ajax_object.destroy_rockets_desc') }}</div>
                        <a class="rockets btn btn_confirm fright overlay"
                           href="{{ route('facilities.destroy-rockets-overlay') }}"
                           data-overlay-class="rocketlayer"
                           data-overlay-title="{{ __('t_ingame.ajax_object.destroy_rockets_btn') }}"
                           data-overlay-width="684px">{{ __('t_ingame.ajax_object.destroy_rockets_btn') }}</a>
                    </div>
                </div>
            @endif
        @endif

        <div class="txt_box">
            <button class="details tooltip js_hideTipOnMobile overlay" aria-label="{{ __('t_ingame.ajax_object.more_details') }}" title="{{ __('t_ingame.ajax_object.more_details') }}"
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
            errorBoxDecision(@json(__('t_ingame.ajax_object.error')), @json(__('t_ingame.ajax_object.commander_queue_info')), @json(__('t_ingame.shared.yes')), @json(__('t_ingame.shared.no')), function () {
                window.location.href = '{{ route('premium.index', ['openDetail' => 2]) }}'
            });
        });

    var loca = loca || {};
    loca = $.extend({},
        loca,
        {
            'allError': @json(__('t_ingame.ajax_object.error')),
            'infoBuildlist': @json(__('t_ingame.ajax_object.commander_queue_info')),
            'allYes': @json(__('t_ingame.shared.yes')),
            'allNo': @json(__('t_ingame.shared.no')),
            'allOk': 'Ok',
            'noRocketsiloCapacity': @json(__('t_ingame.ajax_object.no_rocket_silo_capacity')),
            'allDetailNow': @json(__('t_ingame.ajax_object.detail_now'))
        }
    );

    var buttonClass = "build-it";
    var overlayTitle = @json(__('t_ingame.ajax_object.start_with_dm'));
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
        '2000': @json(__('t_ingame.ajax_object.err_dm_price_too_low')),
        '100': @json(__('t_ingame.ajax_object.err_resource_limit')),
        '10': @json(__('t_ingame.ajax_object.err_storage_capacity')),
        '20': @json(__('t_ingame.ajax_object.err_storage_capacity')),
        '30': @json(__('t_ingame.ajax_object.err_storage_capacity')),
        '1000': @json(__('t_ingame.ajax_object.err_no_dark_matter'))
    };

    var isBuildlistNeeded = 0;
    //var showCommanderHint = (!buttonState && !hasCommander && isBuildlistNeeded && couldBeBuild && (isShip || isRocket));
    var showNoPremiumError = 0;
    var pageToReload = "{{ route('resources.index') }}";
    var isBusy = 0;

</script>