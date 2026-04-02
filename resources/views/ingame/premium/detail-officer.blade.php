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

    <div id="wrapper" style="position:relative;">
        <div id="features">

            <p style="width:255px; height:auto; min-height:120px; float:left;">{{ __('t_ingame.premium.officer_' . $officerKey . '_description') }}</p>

            <div style="position:absolute; right:0; top:0; display:flex; flex-direction:column; gap:6px;">
                @foreach($costs as $days => $cost)
                    @if($darkMatter >= $cost)
                        <a class="build-it officer"
                           href="{{ route('premium.purchase', ['type' => $typeId, 'days' => $days]) }}"
                           style="float:none; display:block;">
                            <span>
                                {{ $days }} {{ __('t_ingame.premium.days') }}<br>
                                <b>{{ number_format($cost, 0, ',', '.') }} {{ __('t_ingame.premium.dark_matter_label') }}</b>
                            </span>
                        </a>
                    @else
                        <a class="build-it_disabled officer"
                           href="javascript:void(0);"
                           style="float:none; display:block;"
                           title="{{ __('t_ingame.premium.insufficient_dark_matter') }}">
                            <span>
                                {{ $days }} {{ __('t_ingame.premium.days') }}<br>
                                <b>{{ number_format($cost, 0, ',', '.') }} {{ __('t_ingame.premium.dark_matter_label') }}</b>
                            </span>
                        </a>
                    @endif
                @endforeach
            </div>

            <br class="clearfloat">
        </div>
    </div>
</div>

<br clear="all">

<div id="description">
    <div class="benefits">{{ __('t_ingame.premium.advantages') }}</div>
    <div class="benefitlist">
        @foreach($benefitKeys as $key)
            <span>{{ __('t_ingame.premium.' . $key) }}</span>
        @endforeach
    </div>
    <a href="javascript:void(0);"
       class="tooltipRight help"
       data-tooltip-width="450"
       data-tooltip-title="{{ __('t_ingame.premium.officer_' . $officerKey . '_tooltip') }}"></a>
</div>

<script>
(function () {
    $('#features a.build-it.officer').on('click', buyOfficerWithDM);

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
