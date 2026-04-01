<div class="officers200 darkMatter"></div>

<div id="content">
    <h2>{{ __('t_ingame.premium.dark_matter_title') }}</h2>

    <a id="close" class="close_details" href="javascript:void(0);"></a>

    <span class="level dmLevel">
        @if($darkMatter <= 0)
            <span class="overmark">{{ __('t_ingame.premium.no_dark_matter') }}</span>
        @else
            <span class="undermark">{{ number_format($darkMatter, 0, ',', '.') }} {{ __('t_ingame.premium.dark_matter_label') }}</span>
        @endif
    </span>

    <br class="clearfloat">

    <div id="wrapper">
        <div id="features">
            <div class="build-it_wrap">
                <a class="build-it overlay" href="{{ route('payment.overlay') }}">
                    <span>{{ __('t_ingame.premium.buy_dark_matter') }}</span>
                </a>
            </div>

            <p>{!! __('t_ingame.premium.dark_matter_description') !!}</p>

            <br class="clearfloat">
        </div>
    </div>
</div>

<br clear="all">

<div id="description">
    <div class="benefits">{{ __('t_ingame.premium.advantages') }}</div>
    <div class="benefitlist">{{ __('t_ingame.premium.dark_matter_benefits') }}</div>
</div>

<script>
$('#features .officer.build-it').on('click', buyOfficerWithDM)
function buyOfficerWithDM(event) {
    event.preventDefault()
    $('#features .officer.build-it').unbind('click')
    let buyOfficerLink = event.currentTarget.getAttribute('href')
    $('#features .officer.build-it').attr('href', 'javascript:void(0)').removeClass('build-it').addClass('build-it_disabled')
    window.location.href = buyOfficerLink
}
</script>
