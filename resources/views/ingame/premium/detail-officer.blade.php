@php
    $cssClass = $officerKey === 'all_officers' ? 'allOfficers' : $officerKey;
@endphp

<div class="officers200 {{ $cssClass }}"></div>

<div id="content">
    <h2>{{ __('t_ingame.premium.officer_' . $officerKey . '_title') }}</h2>

    <a id="close" class="close_details" href="javascript:void(0);"></a>

    <span class="level">
        @if($isActive && $expiresAt)
            <span class="overmark">
                {{ __('t_ingame.premium.active_for_days', ['days' => $expiresAt->diffInDays(now())]) }}
            </span>
        @else
            <span class="undermark">{{ __('t_ingame.premium.not_active') }}</span>
        @endif
    </span>

    <br class="clearfloat">

    <div id="wrapper">
        <div id="features">
            <p>{{ __('t_ingame.premium.officer_' . $officerKey . '_description') }}</p>

            <div class="build-it_wrap">
                @foreach($costs as $days => $cost)
                    <a class="build-it officer"
                       href="{{ route('premium.purchase', ['type' => $typeId, 'days' => $days]) }}">
                        <span>
                            {{ $days }} {{ __('t_ingame.premium.days') }}<br>
                            <b>{{ number_format($cost, 0, ',', '.') }} {{ __('t_ingame.premium.dark_matter_label') }}</b>
                        </span>
                    </a>
                @endforeach
            </div>

            <br class="clearfloat">
        </div>
    </div>
</div>

<br clear="all">

<div id="description">
    <div class="benefits">{{ __('t_ingame.premium.advantages') }}</div>
    <div class="benefitlist">{{ __('t_ingame.premium.officer_' . $officerKey . '_benefits') }}</div>
</div>

<script>
(function () {
    $('#features .officer.build-it').on('click', buyOfficerWithDM);

    function buyOfficerWithDM(event) {
        event.preventDefault();
        $('#features .officer.build-it').off('click');
        var buyOfficerLink = event.currentTarget.getAttribute('href');
        $('#features .officer.build-it')
            .attr('href', 'javascript:void(0)')
            .removeClass('build-it')
            .addClass('build-it_disabled');
        window.location.href = buyOfficerLink;
    }
}());
</script>
