@php /** @var OGame\ViewModels\Queue\UnitQueueViewModel $build_active */ @endphp
@if (!empty($build_active))
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
        <tr>
            <th colspan="2">{{ $build_active->object->title }}</th>
        </tr>
        <tr class="data">
            <td class="first" rowspan="5">
                <div>
                    <a href="{{ route('shipyard.index', ['openTech' => $build_active->object->id]) }}">
                        <img class="queuePic" width="40" height="40"
                             src="{!! asset('img/objects/units/' . $build_active->object->assets->imgSmall) !!}"
                             alt="{{ $build_active->object->title }}">
                    </a>
                </div>
                <div class="shipSumCount shipyardCountdownUnit">{{ $build_active->object_amount_remaining }}</div>
            </td>
        </tr>
        <tr class="data">
            <td class="desc">{{ __('t_ingame.buildqueue.building_duration') }}</td>
        </tr>
        <tr class="data">
            <td class="desc timer">
                <time class="countdown shipyardCountdownUnit"
                      data-segments="2">{{ \OGame\Facades\AppUtil::formatTimeDuration($build_active->time_countdown_object_next) }}</time>
            </td>
        </tr>
        <tr class="data">
            <td class="desc">{{ __('t_ingame.buildqueue.total_time') }}:</td>
        </tr>
        <tr class="data">
            <td class="desc timer">
                <time class="countdown unitCountdown"
                      data-segments="2">{{ \OGame\Facades\AppUtil::formatTimeDuration($build_queue_countdown) }}</time>
            </td>
        </tr>
        <tr class="data">
            <td colspan="2">
                @php
                    $halvingService = app(\OGame\Services\HalvingService::class);
                    $halvingCost = $halvingService->calculateHalvingCost($build_active->time_countdown, 'unit');
                    $wouldComplete = $build_active->dm_halved;
                @endphp
                @if ($wouldComplete)
                    <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ship tpd-hideOnClickOutside"
                       title="{{ __('t_ingame.buildqueue.complete_tooltip') }}"
                       href="javascript:void(0);"
                       rel="{{ route('shipyard.completeunit') }}?queue_item_id={{ $build_active->id }}">
                        <div class="build-finish-img" alt="{{ __('t_ingame.buildqueue.complete') }}"></div>
                        <span class="build-txt">{{ __('t_ingame.buildqueue.complete') }}</span>
                        <span class="dm_cost">{{ __('t_ingame.buildqueue.halve_cost', ['amount' => number_format($halvingCost)]) }}</span>
                    </a>
                @else
                    <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ship tpd-hideOnClickOutside"
                       title="{{ __('t_ingame.buildqueue.halve_tooltip_building') }}"
                       href="javascript:void(0);"
                       rel="{{ route('shipyard.halveunit') }}?queue_item_id={{ $build_active->id }}">
                        <div class="build-faster-img" alt="{{ __('t_ingame.buildqueue.halve_time') }}"></div>
                        <span class="build-txt">{{ __('t_ingame.buildqueue.halve_time') }}</span>
                        <span class="dm_cost">{{ __('t_ingame.buildqueue.halve_cost', ['amount' => number_format($halvingCost)]) }}</span>
                    </a>
                @endif
            </td>
        </tr>
        </tbody>
    </table>

    <script type="text/javascript">
        @if ($wouldComplete)
        var questionship = '{!! __('t_ingame.buildqueue.question_complete_unit', ['dm_cost' => '<span style="font-weight: bold;">' . number_format($halvingCost) . ' ' . __('t_ingame.shared.dark_matter') . '</span>']) !!}';
        @else
        var questionship = '{!! __('t_ingame.buildqueue.question_halve_unit', ['time_reduction' => \OGame\Facades\AppUtil::formatTimeDuration(intdiv($build_active->time_total, 2)), 'dm_cost' => '<span style="font-weight: bold;">' . number_format($halvingCost) . ' ' . __('t_ingame.shared.dark_matter') . '</span>']) !!}';
        @endif
        var priceship = {{ $halvingCost }};
        var referrerPage = $.deparam.querystring().page;
        new CountdownTimer('unitCountdown', {{ $build_active->time_countdown }}, '{{ url()->current() }}', null, true, 3)
        new CountdownTimerUnit('shipyardCountdownUnit', {{ $build_active->time_countdown_object_next }}, {{ $build_active->object_amount_remaining }}, {{ $build_active->object->id }}, {{ $build_active->time_countdown_per_object }}, null, 3)
    </script>
@else
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
        <tr>
            <td colspan="2" class="idle">
                <a class="tooltip js_hideTipOnMobile " title="{{ __('t_ingame.shipyard_page.no_units_idle_tooltip') }}" href="{{ route('shipyard.index') }}">
                    {{ __('t_ingame.shipyard_page.no_units_idle') }}
                    <br>
                    {{ __('t_ingame.shipyard_page.to_shipyard') }}
                </a>
            </td>
        </tr>
        </tbody>
    </table>
@endif
