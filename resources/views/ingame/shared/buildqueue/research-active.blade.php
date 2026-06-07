@php /** @var OGame\ViewModels\Queue\ResearchQueueViewModel $build_active */ @endphp
@if (!empty($build_active))
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
        <tr>
            <th colspan="2">{!! $build_active->object->title !!}</th>
        </tr>
        <tr class="data">
            <td class="first" rowspan="3">
                <div>
                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile tpd-hideOnClickOutside" style="display: block;"
                       onclick="cancelbuilding({{ $build_active->object->id }}, {{ $build_active->id }},
   '{{ __('Research: do you really want to cancel :object_title level :level_target on planet :planet_name [:planet_coordinates]?', [
        'object_title' => $build_active->object->title,
        'level_target' => $build_active->level_target,
        'planet_name' => $build_active->planet->getPlanetName(),
        'planet_coordinates' => $build_active->planet->getPlanetCoordinates()->asString()
    ]) }}'); return false;" title="{{ __('Research: do you really want to cancel :object_title level :level_target on planet :planet_name [:planet_coordinates]?', [
        'object_title' => $build_active->object->title,
        'level_target' => $build_active->level_target,
        'planet_name' => $build_active->planet->getPlanetName(),
        'planet_coordinates' => $build_active->planet->getPlanetCoordinates()->asString()
    ]) }}">
                        <img class="queuePic" width="40" height="40" src="{!! asset('img/objects/research/' . $build_active->object->assets->imgSmall) !!}" alt="{{ $build_active->object->title }}">
                    </a>
                    <a href="javascript:void(0);" class="tooltip js_hideTipOnMobile abortNow"
                       onclick="cancelbuilding({{ $build_active->object->id }}, {{ $build_active->id }},
   '{{ __('Research: do you really want to cancel :object_title level :level_target on planet :planet_name [:planet_coordinates]?', [
        'object_title' => $build_active->object->title,
        'level_target' => $build_active->level_target,
        'planet_name' => $build_active->planet->getPlanetName(),
        'planet_coordinates' => $build_active->planet->getPlanetCoordinates()->asString()
    ]) }}'); return false;"
                       title="{{ __('Research: do you really want to cancel :object_title level :level_target on planet :planet_name [:planet_coordinates]?', [
        'object_title' => $build_active->object->title,
        'level_target' => $build_active->level_target,
        'planet_name' => $build_active->planet->getPlanetName(),
        'planet_coordinates' => $build_active->planet->getPlanetCoordinates()->asString()
    ]) }}">
                        <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="15" width="15">
                    </a>

                </div>
            </td>
            <td class="desc ausbau">{{ __('t_ingame.buildqueue.improve_to') }}
                <span class="level">{{ __('t_ingame.shared.level') }} {!! $build_active->level_target !!}</span>
            </td>
        </tr>
        <tr class="data">
            <td class="desc">{{ __('t_ingame.shared.duration') }}:</td>
        </tr>
        <tr class="data">
            <td class="desc timer">
                <time class="countdown researchCountdown" data-segments="2">{{ \OGame\Facades\AppUtil::formatTimeDuration($build_active->time_countdown) }}</time>
            </td>
        </tr>
        <tr class="data">
            <td colspan="2">
                @php
                    $halvingService = app(\OGame\Services\HalvingService::class);
                    $halvingCost = $halvingService->calculateHalvingCost($build_active->time_total, 'research');
                @endphp
                <a class="build-faster dark_highlight tooltipLeft js_hideTipOnMobile research "
                   title="{{ __('t_ingame.buildqueue.halve_tooltip_research') }}"
                   href="javascript:void(0);"
                   rel="{{ route('research.halveresearch') }}?queue_item_id={{ $build_active->id }}">
                    <div class="build-faster-img" alt="{{ __('t_ingame.buildqueue.halve_time') }}"></div>
                    <span class="build-txt">{{ __('t_ingame.buildqueue.halve_time') }}</span>
                    <span class="dm_cost">{{ __('t_ingame.buildqueue.halve_cost', ['amount' => number_format($halvingCost)]) }}</span>
                </a>
            </td>
        </tr>
        </tbody>
    </table>
    <script type="text/javascript">
        var cancelBuildListEntryUrl = '{{ route('research.cancelbuildrequest') }}';
        var questionresearch = '{!! __('t_ingame.buildqueue.question_halve_research', ['dm_cost' => '<span style="font-weight: bold;">' . number_format($halvingCost) . ' ' . __('t_ingame.shared.dark_matter') . '</span>']) !!}';
        var priceresearch = {{ $halvingCost }};
        var referrerPage = $.deparam.querystring().page;

        new CountdownTimer('researchCountdown', {{ $build_active->time_countdown }}, '{{ url()->current() }}', null, true, 3)

        function cancelbuilding(id, listId, question) {
            errorBoxDecision('{{ __('t_ingame.shared.caution') }}', "" + question + "", '{{ __('t_ingame.shared.yes') }}', '{{ __('t_ingame.shared.no') }}', function () {
                buildListActionCancel(id, listId)
            });
        }
    </script>
{{-- No research is being done. --}}
@else
    <table cellspacing="0" cellpadding="0" class="construction active">
        <tbody>
            <tr>
                <td colspan="2" class="idle">
                    <a class="tooltip js_hideTipOnMobile
                                   " title="{{ __('t_ingame.buildqueue.no_research_idle_tooltip') }}" href="{{ url()->current() }}">
                        {{ __('t_ingame.buildqueue.no_research_idle') }}</a>
                </td>
            </tr>
        </tbody>
    </table>
@endif
