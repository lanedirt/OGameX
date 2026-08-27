@props(['building'])
@php /** @var OGame\ViewModels\UnitViewModel $building */ @endphp
@php
    $wrongClassKey = match (strtolower($building->object->machine_name)) {
        'reaper' => 'wrong_class_general',
        'crawler' => 'wrong_class_collector',
        'pathfinder' => 'wrong_class_discoverer',
        default => 'wrong_class',
    };
@endphp

<li class="technology {{ $building->object->class_name }} hasDetails tooltip hideTooltipOnMouseenter js_hideTipOnMobile ipiHintable tpd-hideOnClickOutside"
    data-technology="{{ $building->object->id }}"
    data-is-spaceprovider=""
    aria-label="{{ $building->object->title }}"
    data-ipi-hint="ipiTechnology{{ $building->object->class_name }}"
    @if ($building->currently_building)
        data-status="active"
    data-is-spaceprovider=""
    data-progress="26"
    data-start="1713521207"
    data-end="1713604880"
    data-total="61608"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.under_construction') }}"
    @elseif ($is_in_vacation_mode ?? false)
        data-status="disabled"
    title="{{ $building->object->title }} ({{ $building->amount }})<br/>{{ __('t_ingame.buildings.vacation_mode_error') }}"
    @elseif (!$building->requirements_met)
        data-status="off"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.requirements_not_met') }}"
    @elseif (!$building->character_class_met)
        data-status="disabled"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.' . $wrongClassKey) }}"
    @elseif (!$building->enough_resources)
        data-status="disabled"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.not_enough_resources') }}"
    @elseif (!$building->max_build_amount)
        data-status="disabled"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.max_amount_reached') }}"
    @elseif ($shipyard_upgrading ?? false)
        data-status="disabled"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.shipyard_upgrading') }}"
    @elseif ($nanite_upgrading ?? false)
        data-status="disabled"
    title="{{ $building->object->title }}<br/>{{ __('t_ingame.buildings.nanite_upgrading') }}"
    @else
        data-status="on"
    title="{{ $building->object->title }}"
    @endif
>
    <span class="icon sprite @if ($building->object->type == \OGame\GameObjects\Models\Enums\GameObjectType::Defense)
        sprite_medium medium
        @else
        sprite_small small
        @endif{{ $building->object->class_name }}"
>
        @if ($building->currently_building)
            <span class="targetamount" data-value="{{ $building->amount + $building->currently_building_amount }}" data-bonus="0">
                {{ $building->amount + $building->currently_building_amount }}
            </span>
            <div class="cooldownBackground"></div>
            <time-counter><time class="countdown unitCountdown" id="countdownbuildingDetails" data-segments="2">...</time></time-counter>
        @endif
            <span class="amount" data-value="{{ $building->amount }}" data-bonus="0">
            <span class="stockAmount">{{ $building->getFormattedFull() }}</span>
            <span class="bonus"></span>
        </span>
    </span>
</li>
