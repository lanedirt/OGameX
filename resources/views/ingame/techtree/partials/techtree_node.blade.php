@php
    use OGame\GameObjects\Models\Enums\GameObjectType;
    /** @var OGame\GameObjects\Models\Abstracts\GameObject $object */
@endphp

{{-- Techtree node component --}}
<div class="techtreeNode">
    <div class="techImage js_hideTipOnMobile tooltipHTML tech{{ $object->id }} techt{{ $object->id }}l{{ $required_level }} {{ $current_level >= $required_level ? 'built' : 'notBuilt' }}" title="{{ $object->title }} @lang('Level') ({{ $required_level }})|{{ $object->description }}">
        <a href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}"
           class="sprite sprite_small small overlay {{ $object->class_name }}"
           data-overlay-same="true"
           data-tech-id="{{ $object->id }}"
           data-tech-name="{{ $object->title }}"
           data-tech-type="{{ $object->type === GameObjectType::Research ? 'Type Research' : 'Type Buildings' }}">
           </a>
    </div>
</div>