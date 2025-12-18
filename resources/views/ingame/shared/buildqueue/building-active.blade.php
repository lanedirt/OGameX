@php /** @var OGame\ViewModels\Queue\BuildingQueueViewModel $build_active */ @endphp
@if (!empty($build_active))
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
        <tr>
            <th colspan="2">{!! $build_active->object->title !!}</th>
        </tr>
        <tr class="data">
            <td class="first" rowspan="3">
                <div>
                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile tpd-hideOnClickOutside"
                       style="display: block;"
                       onclick="cancelbuilding({{ $build_active->object->id }}, {{ $build_active->id }}, &quot;Cancel production of {!! $build_active->object->title !!} level {!! $build_active->level_target !!}?&quot;); return false;"
                       title="">
                        <img class="queuePic" width="40" height="40"
                             src="{!! asset('img/objects/buildings/' . $build_active->object->assets->imgSmall) !!}"
                             alt="{{ $build_active->object->title }}">
                    </a>
                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile abortNow"
                       onclick="cancelbuilding({{ $build_active->object->id }}, {{ $build_active->id }}, &quot;Cancel production of {!! $build_active->object->title !!} level {!! $build_active->level_target !!}?&quot;); return false;"
                       title="Cancel production of {!! $build_active->object->title !!} level {!! $build_active->level_target !!}?">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15" width="15">
                    </a>
                </div>
            </td>
            <td class="desc ausbau">
                @if ($build_active->is_downgrade ?? false)
                    @lang('Downgrade to')
                @else
                    @lang('Improve to')
                @endif
                <span class="level">@lang('Level') {!! $build_active->level_target !!}</span>
            </td>
        </tr>
        <tr class="data">
            <td class="desc">@lang('Duration'):</td>
        </tr>
        <tr class="data">
            <td class="desc timer">
                <time class="countdown buildingCountdown"
                      data-segments="2">{{ \OGame\Facades\AppUtil::formatTimeDuration($build_active->time_countdown) }}</time>
            </td>
        </tr>
        <tr class="data">
            <td colspan="2">
                @php
                    $halvingService = app(\OGame\Services\HalvingService::class);
                    $halvingCost = $halvingService->calculateHalvingCost($build_active->time_total, 'building');
                @endphp
                <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile building "
                   title="Reduces construction time by 50% of the total construction time."
                   href="javascript:void(0);"
                   rel="{{ route('facilities.halvebuilding') }}?queue_item_id={{ $build_active->id }}">
                    <div class="build-faster-img" alt="Halve time"></div>
                    <span class="build-txt">Halve time</span>
                    <span class="dm_cost">Costs: {{ number_format($halvingCost) }} DM</span>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        var cancelBuildListEntryUrl = '{{ route('resources.cancelbuildrequest') }}';
        var questionbuilding = 'Do you want to reduce the construction time of the current construction project by 50% of the total construction time for <span style="font-weight: bold;">{{ number_format($halvingCost) }} Dark Matter</span>?';
        var pricebuilding = {{ $halvingCost }};
        var referrerPage = $.deparam.querystring().page;

        new CountdownTimer('buildingCountdown', {{ $build_active->time_countdown }}, '{{ url()->current() }}', null, true, 3)

        function cancelbuilding(id, listId, question) {
            errorBoxDecision('Caution', "" + question + "", 'yes', 'No', function () {
                buildListActionCancel(id, listId)
            });
        }
    </script>
    {{-- No buildings are being built. --}}
@else
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
        <tr>
            <td colspan="2" class="idle">
                <a class="tooltip js_hideTipOnMobile
                                   "
                   title="@lang('At the moment there is no building being built on this planet. Click here to go to the build page.')"
                   href="{{ url()->current() }}">
                    @lang('No buildings in construction.')</a>
            </td>
        </tr>
        </tbody>
    </table>
@endif