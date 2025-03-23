@php
    use OGame\GameObjects\Models\Abstracts\GameObject;
    use OGame\GameObjects\Models\Techtree\TechtreeRequirement;
    /** @var GameObject $object */
    /** @var array<TechtreeRequirement> $techtree */
    /** @var array<TechtreeRequirement> $techtree_unique */
    /** @var array<int, array<int, TechtreeRequirement>> $techtree_by_depth */
@endphp

<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologytree', 'objectId' => $object->id])
    <div class="content technologytree">
        @if ($object->hasRequirements())
        <div class="graph columns_{{ $amount_of_columns }}" data-id="67d6cebc93399">
            @foreach ($techtree_by_depth as $depth => $depth_items)
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
        </div>
        @else
            <p class="hint">
                @lang('No requirements available')
            </p>
        @endif
    </div>
    <script>
        var endpoints = [
            {{-- Create list of all requirements with level required --}}
            @foreach ($techtree_unique as $requirement)
                "t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}",
            @endforeach
        ];
        var connections = [
            @foreach ($techtree as $requirement)
                @if ($requirement->parent !== null && $requirement->levelCurrent >= $requirement->levelRequired)
                    {"source":"t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}","target":"t{{ $requirement->parent->gameObject->id }}l{{ $requirement->parent->levelRequired }}","label":"{{ $requirement->levelRequired }}","paintStyle":"hasRequirements"},
                @elseif ($requirement->parent !== null)
                    {"source":"t{{ $requirement->gameObject->id }}l{{ $requirement->levelRequired }}","target":"t{{ $requirement->parent->gameObject->id }}l{{ $requirement->parent->levelRequired }}","label":"{{ $requirement->levelCurrent }}/{{ $requirement->levelRequired }}","paintStyle":"hasNotRequirements"},
                @endif
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
