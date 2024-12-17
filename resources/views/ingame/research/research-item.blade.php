@props(['building', 'build_queue_max'])
@php /** @var OGame\ViewModels\ResearchViewModel $building */ @endphp
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
        title="{{ $building->object->title }}<br/>@lang('Under construction')"
    @elseif (!$building->requirements_met)
        data-status="off"
        title="{{ $building->object->title }}<br/>@lang('Requirements are not met!')"
    @elseif ($building->research_lab_upgrading)
        data-status="disabled"
        title="{{ $building->object->title }}<br/>@lang('Research Lab is being expanded.')"
    @elseif (!$building->enough_resources)
        data-status="disabled"
        title="{{ $building->object->title }}<br/>@lang('Not enough resources!')"
    @elseif ($build_queue_max)
        data-status="disabled"
        title="{{ $building->object->title }}<br/>@lang('Queue is full')"
    @else
        data-status="on"
        title="{{ $building->object->title }}"
    @endif
>

                        <span class="icon sprite sprite_small small {{ $building->object->class_name }}">
                            <!--
                            TODO: for events
                            <span class="acceleration"
                                  data-value="25">
                                    -25% ⌛
                                </span>-->
                            @if ($building->currently_building)
                                <span class="targetlevel" data-value="{{ $building->current_level + 1 }}" data-bonus="0">{{ $building->current_level + 1 }}</span>
                                <div class="cooldownBackground"></div>
                                <time-counter><time class="countdown researchCountdown" id="countdownbuildingDetails" data-segments="2">...</time></time-counter>
                            @endif
                            <span class="level" data-value="{{ $building->current_level }}" data-bonus="0">
                            <span class="stockAmount">{{ $building->current_level }}</span>
                            <span class="bonus"></span>
                            </span>
                        </span>