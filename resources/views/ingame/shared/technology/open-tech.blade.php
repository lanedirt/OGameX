@php /** @var int $open_tech_id */ @endphp
@if ($open_tech_id > 0)
<script type="text/javascript">
        $('.technology.hasDetails:not(.showsDetails)[data-technology="{{ $open_tech_id }}"] .icon').click();
</script>
@endif
