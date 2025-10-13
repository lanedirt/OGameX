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
                        <td><time datetime="PT6M22S"></time>6m 22s</td>
                    </tr>
                </table>
            </div>

            @if ($max_build_amount && ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Ship || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense))
                <div class="build_amount">
                    <label for="build_amount">Number:</label>
                    <input type="text" name="build_amount" id="build_amount" min="0" max="{{ $max_build_amount }}" onfocus="clearInput(this);" onkeyup="checkIntInput(this, 1, {{ $max_build_amount }});event.stopPropagation();">
                    <button class="maximum">[max. {{ $max_build_amount }}]</button>
                </div>
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
            </div>
        </div>
    </div>

    <div class="description">
        <div class="txt_box">
            <button class="details tooltip js_hideTipOnMobile overlay" aria-label="@lang('More details')" title="@lang('More details')"
                    data-target="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object->id]) }}"
                    data-overlay-title="{{ $title }}"> ?
            </button>
            
            <span class="text">
                {!! $description !!}
            </span>
    </div>
            @php
                $desc = is_string($description ?? null) ? $description : '';

                if ($desc !== '' && str_contains($desc, ':energy')) {
                    $perUnit = null;

                    // 1) Preferred: reuse already computed delta (green (+X))
                    if (isset($energy_difference) && is_numeric($energy_difference) && $energy_difference < 0) {
                        $perUnit = (int) abs($energy_difference);
                    }
                    // 2) Fallback: safe energy_formula evaluation
                    elseif (
                        isset($object?->production?->energy_formula) &&
                        is_callable($object->production->energy_formula) &&
                        isset($object->production->planetService)
                    ) {
                        $perUnit = (int) call_user_func($object->production->energy_formula, $object->production, 1);
                    }
                    // 3) Final fallback
                    if ($perUnit === null) {
                        $perUnit = 25;
                    }

                    $formatted = \OGame\Facades\AppUtil::formatNumberLong($perUnit);
                    $desc = strtr($desc, [':energy' => $formatted]);
                }
            @endphp

            <span class="text">
                {!! $desc !!}
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

@if ($object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Building || $object_type == \OGame\GameObjects\Models\Enums\GameObjectType::Station)
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
