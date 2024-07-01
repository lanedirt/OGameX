
<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    <nav data-current-action="applications">
        <ul>
            <li>
                <a class="overlay" data-action="technologytree" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 1, 'object_id' => $object->id]) }}">
                    @lang('Techtree')
                </a>
            </li>
            <li>
                <a class="overlay" data-action="applications" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 4, 'object_id' => $object->id]) }}">
                    @lang('Applications')
                </a>
            </li>
            <li>
                <a class="overlay" data-action="technologyinformation" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 2, 'object_id' => $object->id]) }}">
                    @lang('Techinfo')
                </a>
            </li>
            <li>
                <a class="overlay" data-action="technologies" data-overlay-same="true" href="{{ route('techtree.ajax', ['tab' => 3, 'object_id' => $object->id]) }}">
                    @lang('Technology')
                </a>
            </li>
        </ul>
    </nav>

    <div class="content applications">
        <p class="hint">{{$object->title}} is a requirement for:</p>
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
    <script>
    </script>
</div>

<script type="text/javascript">
    $(
        function(){
            initOverlayName();
        }
    );
</script>
