<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologies', 'objectId' => $object->id])

    <div class="techtree" data-id="5752c611e29741257f3cf67e060afb27" data-title="Applications - {{ $object->title }}">
        <div class="advice">{{ $object->title }} is a prerequisite of:</div>
        There are no such technologies.
    </div>

</div>

<script type="text/javascript">
    $(
        function(){
            initOverlayName();
        }
    );
</script>
