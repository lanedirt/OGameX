@php /** @var OGame\ViewModels\Queue\BuildingQueueViewModel $build_active */ @endphp
@if (!empty($build_active))
    <script type="text/javascript">
        @if (!empty($build_active))
        $(document).ready(function () {
            // Countdown for inline building element (pusher)
            var elem = getElementByIdWithCache("b_resources{{ $build_active->object->id }}");
            if(elem) {
                new bauCountdown(elem, {{ $build_active->time_countdown }}, {{ $build_active->time_total }}, "{{ url()->current() }}");
            }
        });
        @endif
    </script>
    <tr>
        <th colspan="2">{!! $build_active->object->title !!}</th>
    </tr>
    <tr class="data">
        <td class="first" rowspan="3">
            <div>
                <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile" style="display: block;" onclick="cancelProduction({!! $build_active->object->id !!},{!! $build_active->id !!},&quot;Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?&quot;); return false;" title="">
                    <img class="queuePic" width="40" height="40" src="{!! asset('img/objects/buildings/' . $build_active->object->assets->imgSmall) !!}" alt="{!! $build_active->object->title !!}">
                </a>
                <a href="javascript:void(0);" class="tooltip abortNow js_hideTipOnMobile" onclick="cancelProduction({!! $build_active->object->id !!},{!! $build_active->id !!},&quot;Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?&quot;); return false;" title="Cancel expansion of {!! $build_active->object->title !!} to level {!! $build_active->level_target !!}?">
                    <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15" width="15">
                </a>
            </div>
        </td>
        <td class="desc ausbau">Improve to <span class="level">Level {!! $build_active->level_target !!}</span>
        </td>
    </tr>
    <tr class="data">
        <td class="desc">Duration:</td>
    </tr>
    <tr class="data">
        <td class="desc timer">
            <span id="Countdown">Loading...</span>
            <!-- JAVASCRIPT -->
            <script type="text/javascript">
                var timerHandler=new TimerHandler();
                new baulisteCountdown(getElementByIdWithCache("Countdown"), {!! $build_active->time_countdown !!}, "{!! url()->current() !!}");
            </script>
        </td>
    </tr>
    <tr class="data">
        <td colspan="2">
            <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building disabled" title="Reduces construction time by 50% of the total construction time (15s)." href="javascript:void(0);" rel="{{ route('shop.index', ['buyAndActivate' => 'cb4fd53e61feced0d52cfc4c1ce383bad9c05f67'])  }}">
                <div class="build-faster-img" alt="Halve time"></div>
                <span class="build-txt">Halve time</span>
                <span class="dm_cost overmark">
                                Costs: 750 DM                            </span>
                <span class="order_dm">Purchase Dark Matter</span>
            </a>
        </td>
    </tr>
{{-- No buildings are being built. --}}
@else
    <tr>
        <td colspan="2" class="idle">
            <a class="tooltip js_hideTipOnMobile
                           " title="At the moment there is no building being built on this planet. Click here to go to the build page." href="{{ url()->current() }}">
                No buildings in construction.                            </a>
        </td>
    </tr>
@endif