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
            <div class="graph columns_1" data-id="67d6cebc93399">
                @php
                    $depth = 0;
                    $nextDepthFound = true;
                @endphp
                @while ($nextDepthFound)
                    @php
                        // Keep track if we have found a new depth with this iteration.
                        $nextDepthFound = false;
                    @endphp
                    {{-- Print all objects per depth together, so loop through all objects and print expected depth until there is no next depth found --}}
                    @foreach ($requirement_graph as $requirement)
                        @if ($requirement->depth === $depth)
                            <div class="techWrapper depth{{ $requirement->depth }} clearfix">
                                @include('ingame.techtree.partials.techtree_node', ['object' => $requirement->gameObject, 'required_level' => $requirement->levelRequired])
                            </div>
                        @elseif ($requirement->depth > $depth)
                            @php
                                // If we have found a new depth, we need to mark it so loop will continue for next interation.
                                $nextDepthFound = true;
                            @endphp
                        @endif
                    @endforeach
                    @php
                        $depth++;
                    @endphp
                @endwhile
            </div>
        @else
            <p class="hint">
                No requirements available
            </p>
        @endif
    </div>
    <script>
        var endpoints = [
            {{-- Create list of all requirements with level required --}}
            @foreach ($requirement_graph as $requirement)
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
                    {"source":"t{{ $object_dependency->id }}l{{ $requirement_dependency->level }}","target":"t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}","label":"{{ $requirement_dependency->level }}","paintStyle":"hasRequirements"},
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