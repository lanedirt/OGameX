@php /** @var array<\OGame\ViewModels\Queue\UnitQueueViewModel> $build_queue */ @endphp
@if (count($build_queue) > 0)
    <table class="queue">
        <tbody>
        <tr>
            @foreach ($build_queue as $item)
                <td>
                        <img class="queuePic"
                             src="{!! asset('img/objects/units/' . $item->object->assets->imgSmall) !!}" height="28"
                             width="28" alt="{!! $item->object->title !!}">
                    <br />
                    {!! $item->object_amount !!}
                </td>
            @endforeach
        </tr>
        </tbody>
    </table>
@endif