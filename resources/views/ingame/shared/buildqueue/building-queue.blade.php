@php /** @var \OGame\ViewModels\Queue\BuildingQueueListViewModel $build_queue */ @endphp
@if (count($build_queue) > 0)
    <table class="queue">
        <tbody>
        <tr>
            @foreach ($build_queue as $item)
                <td>
                    <a href="javascript:void(0);" class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet"
                       onclick="cancelbuilding({!! $item->object->id !!},{!! $item->id !!},&quot;Cancel expansion of {!! $item->object->title !!} to level {!! $item->level_target !!}?&quot;); return false;"
                       title="">
                        <img class="queuePic"
                             src="{!! asset('img/objects/buildings/' . $item->object->assets->imgMicro) !!}" height="28"
                             width="28" alt="{!! $item->object->title !!}">
                        <span>{!! $item->level_target !!}</span>
                    </a>
                </td>
            @endforeach
        </tr>
        </tbody>
    </table>
@endif