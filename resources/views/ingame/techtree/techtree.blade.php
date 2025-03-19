@php
    use OGame\GameObjects\Models\Abstracts\GameObject;
    use OGame\GameObjects\Models\Techtree\TechtreeRequirement;
    use OGame\Services\ObjectService;
    /** @var GameObject $object */
    /** @var array<TechtreeRequirement> $requirement_graph */
@endphp

<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologytree', 'objectId' => $object->id])

    <div class="content technologytree">
        @if ($object->hasRequirements())
        <div class="graph columns_{{ $amount_of_columns }}" data-id="67d6cebc93399">
            @foreach ($requirement_graph_by_depth as $depth => $depth_items)
                <div class="techWrapper depth{{ $depth }} clearfix">
                    @for ($column = 0; $column < $amount_of_columns; $column++)
                        @if (isset($depth_items[$column]))
                            @include('ingame.techtree.partials.techtree_node', ['object' => $depth_items[$column]->gameObject, 'required_level' => $depth_items[$column]->levelRequired, 'current_level' => $depth_items[$column]->levelCurrent])
                        @else
                            <div class="techtreeNode empty">&nbsp;</div>
                        @endif
                    @endfor
                </div>
            @endforeach
        @else
            <p class="hint">
                No requirements available
            </p>
        @endif
    </div>
    <script>
        var endpoints = [
            {{-- Create list of all requirements with level required --}}
            @foreach ($requirement_graph_unique as $requirement)
                "t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}",
            @endforeach
        ];
        var connections = [
            @foreach ($requirement_graph as $requirement)
                {{-- Create connections from child to parent --}}
                @foreach ($requirement->gameObject->requirements as $requirement_dependency)
                    @php
                        $object_dependency = ObjectService::getObjectByMachineName($requirement_dependency->object_machine_name);
                    @endphp
                    @if ($requirement_dependency->level >= $requirement->levelRequired)
                        {"source":"t{{ $object_dependency->id }}l{{ $requirement_dependency->level }}","target":"t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}","label":"{{ $requirement_dependency->level }}","paintStyle":"hasRequirements"},
                    @else
                        {"source":"t{{ $object_dependency->id }}l{{ $requirement_dependency->level }}","target":"t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}","label":"{{ $requirement_dependency->level }}/{{ $requirement->levelRequired }}","paintStyle":"hasNotRequirements"},
                    @endif
                @endforeach
            @endforeach
        ];
        (function($){
          initTechtree("67d6cebc93399")
        })(jQuery);
    </script>
</div>

<script type="text/javascript">
    $(
        function(){
            initOverlayName();
        }
    );
</script>