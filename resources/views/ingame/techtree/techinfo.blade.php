
<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    <nav data-current-action="technologyinformation">
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

    <div class="content technologyinformation sprite_before sprite_large {{ $object->class_name }}">
        <div class="information">
            <p>{!! nl2br($object->description_long) !!}</p>
            {!! $production_table !!}
            {!! $storage_table !!}
            {!! $rapidfire_table !!}
            {!! $properties_table !!}
            {!! $plasma_table !!}
        </div>
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
