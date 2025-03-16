@php
    use OGame\GameObjects\Models\Abstracts\GameObject;
    /** @var GameObject $object */
@endphp

<div id="technologytree" data-title="@lang('Technology') - {{ $object->title }}">
    @include('ingame.techtree.partials.nav', ['currentAction' => 'technologytree', 'objectId' => $object->id])

    <div class="content technologytree">
        @if ($object->hasRequirements())
            <div class="graph columns_1" data-id="67d6cebc93399">
                <div class="techWrapper depth0 clearfix">
                    {{-- The depth0 represents the current object which is always displayed on the first row --}}
                    <div class="techtreeNode">
                        @include('ingame.techtree.partials.techtree_node', ['object' => $object, 'required_level' => 1])
                    </div>
                </div>
                <div class="techWrapper depth1 clearfix">
                  <div class="techtreeNode">
                    <div class="techImage js_hideTipOnMobile tooltipHTML tech14 techt14l2 built" title="Robotics Factory Level (2)|Robotic factories provide construction robots to aid in the construction of buildings. Each level increases the speed of the upgrade of buildings.">
                        <a href="https://s256-en.ogame.gameforge.com/game/index.php?page=ajax&amp;component=technologytree&amp;technologyId=14&amp;ajax=1"
                        class="sprite sprite_small small overlay roboticsFactory hasRequirements"
                        data-overlay-same="true"
                        data-tech-id="14"
                        data-tech-name="Robotics Factory"
                        data-tech-type="Type Buildings">
                        </a>
                    </div>
                </div>
                </div>
            </div>
        @else
            <p class="hint">
                No requirements available
            </p>
        @endif
    </div>
    <script>
        var endpoints = ["t21l1","t14l2"];
        var connections = [{"source":"t14l2","target":"t21l1","label":"2","paintStyle":"hasRequirements"}];
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