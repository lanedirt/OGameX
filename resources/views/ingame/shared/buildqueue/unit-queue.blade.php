@php /** @var array<\OGame\ViewModels\Queue\UnitQueueViewModel> $build_queue */ @endphp
@if (count($build_queue) > 0)
    <table class="queue">
        <tbody>
        <tr>
            @foreach ($build_queue as $item)
                <td>
                    <a href="javascript:void(0);" class="queue_link tooltip js_hideTipOnMobile dark_highlight_tablet"
                       onclick="cancelbuilding({!! $item->object->id !!},{!! $item->id !!},&quot;Cancel production of {!! $item->object_amount !!}x {!! $item->object->title !!}?&quot;); return false;"
                       title="">
                        <img class="queuePic"
                             src="{!! asset('img/objects/units/' . $item->object->assets->imgSmall) !!}" height="28"
                             width="28" alt="{!! $item->object->title !!}">
                        <span>{!! $item->object_amount !!}</span>
                    </a>
                </td>
            @endforeach
        </tr>
        </tbody>
    </table>
@endif