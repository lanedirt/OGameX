<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'applications', 'objectId' => $object->id])

    <div class="content applications">
        <p class="hint">{{$object->title}} @lang('is a requirement for'):</p>
        <ul class="applications">
            @php /** @var OGame\GameObjects\Models\Techtree\TechtreeRequiredBy $required */ @endphp
            @foreach ($required_by as $required)
                <li class="tooltipHTML" title="{{ $required->gameObject->title }}|{{ $required->gameObject->description }}" aria-label="{{ $required->gameObject->title }}" data-prerequisites-met="{{ $required->requirementsMet ? 'true' : 'false' }}">
                    <a href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $required->gameObject->id]) }}" class="sprite sprite_small {{ $required->gameObject->class_name }} overlay" data-overlay-same="true">
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script type="text/javascript">
    $(
        function(){
            initOverlayName();
        }
    );
</script>
