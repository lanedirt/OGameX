@php /** @var OGame\ViewModels\UnitQueueListViewModel $build_queue */ @endphp
@if ($build_queue->count() > 0)
    <div id="pqueue">
        <div class="header"><h3><span>Production queue</span></h3></div>
        <div class="body">
            <ul class="item">
                @php /** @var OGame\ViewModels\UnitQueueViewModel $item */ @endphp
                @foreach ($build_queue->queue as $item)
                    <li class="tooltip" title="{{ $item->object_amount }} {{ $item->object->title }}<br>Building duration {{ $item->time_total }}s">
                        <a class="slideIn" ref="{{ $item->object->id }}" href="javascript:void(0);">
                            <img width="40" height="40" src="{{ asset('img/objects/units/' . $item->object->assets->imgSmall) }}">
                        </a>
                        <span class="number">{{ $item->object_amount }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="clearfloat"></div>
        </div>
        <div class="footer"></div>
    </div>
@endif