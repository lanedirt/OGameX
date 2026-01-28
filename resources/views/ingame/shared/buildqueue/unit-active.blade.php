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
            <td class="desc">@lang('Building duration')</td>
        </tr>
        <tr class="data">
            <td class="desc timer">
                <time class="countdown shipyardCountdownUnit"
                      data-segments="2">{{ \OGame\Facades\AppUtil::formatTimeDuration($build_active->time_countdown_object_next) }}</time>
            </td>
        </tr>
        <tr class="data">
            <td class="desc">@lang('Total time'):</td>
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
                @endphp
                <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ship tpd-hideOnClickOutside"
                   title="Reduces construction time by 50% of the total construction time."
                   href="javascript:void(0);"
                   rel="{{ route('shipyard.halveunit') }}?queue_item_id={{ $build_active->id }}">
                    <div class="build-faster-img" alt="Halve time"></div>
                    <span class="build-txt">Halve time</span>
                    <span class="dm_cost">Costs: {{ number_format($halvingCost) }} DM</span>
                </a>
            </td>
        </tr>
        </tbody>
    </table>

    <script type="text/javascript">
        var questionship = 'Do you want to reduce the construction time of the current shipyard production by 50% of the total construction time for <span style="font-weight: bold;">{{ number_format($halvingCost) }} Dark Matter</span>?';
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
                <a class="tooltip js_hideTipOnMobile " title="@lang('At the moment there are no ships or defense built on this planet. Click here to get to the shipyard.')" href="{{ route('shipyard.index') }}">
                    @lang('No ships/defense in construction.')
                    <br>
                    @lang('(To shipyard)')
                </a>
            </td>
        </tr>
        </tbody>
    </table>
@endif