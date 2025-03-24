<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologyinformation', 'objectId' => $object->id])

    <div class="content technologyinformation sprite_before sprite_large {{ $object->class_name }}">
        <div class="information">
            <p>{!! nl2br($object->description_long) !!}</p>
            {!! $production_table !!}
            {!! $storage_table !!}
            {!! $rapidfire_table !!}
            {!! $properties_table !!}
            {!! $plasma_table !!}
            {!! $astrophysics_table !!}
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
