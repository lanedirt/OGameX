@php /** @var OGame\Services\PlanetService $planet */ @endphp

<script type="text/javascript">
    var lastBuildingSlot = {
        "showWarning": @if (($planet->getBuildingCount() + 1) >= $planet->getPlanetFieldMax()) true @else false @endif,
@if ($planet->isMoon())
        "slotWarning": "This building will use the last available building slot. Expand your Moon Base or buy a moon field item (e.g. <a href=\"#TODO_?page&#61;shop#page&#61;shop&amp;category&#61;c18170d3125b9941ef3a86bd28dded7bf2066a6a&amp;item&#61;04e58444d6d0beb57b3e998edc34c60f8318825a\" class=\"tooltipHTML itemLink\">Gold Moon Fields<\/a>) to obtain more slots. Do you really want to build this building?"
@else
        "slotWarning": "This building will use the last available building slot. Expand your Terraformer or buy a Planet Field item (e.g. <a href=\"#TODO_?page&#61;shop#page&#61;shop&amp;category&#61;c18170d3125b9941ef3a86bd28dded7bf2066a6a&amp;item&#61;04e58444d6d0beb57b3e998edc34c60f8318825a\" class=\"tooltipHTML itemLink\">Gold Planet Fields<\/a>) to obtain more slots. Are you sure you want to build this building?"
@endif
    };
</script>