@php /** @var OGame\ViewModels\Queue\UnitQueueViewModel $build_active */ @endphp
@if (!empty($build_active))
    <script>
        $(document).ready(function () {
            @if (!empty($build_active))
            // Countdown for inline building element (pusher)
            new shipCountdown(getElementByIdWithCache('shipAllCountdown'), getElementByIdWithCache('shipCountdown'), getElementByIdWithCache('shipSumCount'), {{ $build_active->time_countdown }}, {{ $build_active->time_countdown_object_next }}, {{ $build_queue_countdown }}, {{ $build_active->object_amount_remaining }}, "{{ route('defense.index') }}");
            @endif
        });
    </script>

    <div class="content-box-s">
        <div class="header"><h3>Current production:</h3></div>
        <div class="content">
            <table cellspacing="0" cellpadding="0" class="construction active">
                <tbody>
                <tr class="data">
                    <th colspan="2">{{ $build_active->object->title }}</th>
                </tr>
                <tr class="data">
                    <td title="Production of {{ $build_active->object_amount_remaining }} {{ $build_active->object->title }} in progress" class="building tooltip" rowspan="2" valign="top">
                        <a href="{{ route('shipyard.index', ['openTech' => $build_active->object->id]) }}" onclick="$('.detail_button[ref={{ $build_active->object->id }}]').click(); return false;">
                            <img class="queuePic" width="40" height="40" alt="{{ $build_active->object->title }}" src="{{ asset('img/objects/units/' . $build_active->object->assets->imgSmall) }}"></a>
                        <div class="shipSumCount" id="shipSumCount">{{ $build_active->object_amount_remaining }}</div>
                    </td>
                    <td class="desc timeProdShip">
                        Building duration <span class="shipCountdown" id="shipCountdown">{{ $build_active->time_countdown }}</span>
                    </td>
                </tr>
                <tr class="data">
                    <td class="desc timeProdAll">
                        Total time: <br><span class="shipAllCountdown" id="shipAllCountdown">{{ $build_queue_countdown }}</span>
                    </td>
                </tr>
                <tr class="data">
                    <td colspan="2">
                        <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile ships " title="Reduces construction time by 50% of the total construction time (9s)." href="javascript:void(0);" rel="{{ route('shop.index', ['buyAndActivate' => '75accaa0d1bc22b78d83b89cd437bdccd6a58887']) }}">
                            <div class="build-faster-img" alt="Halve time"></div>
                            <span class="build-txt">Halve time</span>
                            <span class="dm_cost ">
                                Costs: 750 DM                            </span>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="queue">
                <tbody><tr>

                </tr>
                </tbody></table>


        </div>
        <div class="footer"></div>
    </div>
@endif