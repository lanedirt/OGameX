@php /** @var OGame\ViewModels\UnitViewModel $object */ @endphp
@if ($object->currently_building)
    <div class="construction">
        <div class="pusher" id="b_resources{{ $object->object->id }}" style="height:100px;">
        </div>
        <a class="slideIn timeLink" href="javascript:void(0);" ref="{{ $object->object->id }}">
            <span class="time" id="test" name="zeit"></span>
        </a>

        <a class="detail_button slideIn"
           id="details{{ $object->object->id }}"
           ref="{{ $object->object->id }}"
           href="javascript:void(0);">
            <span class="eckeoben">
                <span style="font-size:11px;" class="undermark"> {{ $object->amount + $object->currently_building_amount }}</span>
            </span>
            <span class="ecke">
                <span class="level">{{ $object->amount }}</span>
            </span>
        </a>
    </div>
@endif