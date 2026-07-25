@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="inhalt" class="officers">
        <div id="planet">
            <div id="header_text">
                <h2>{{ __('t_ingame.premium.recruit_officers') }}</h2>
            </div>

            {{-- detailWrapper è necessario per GFSlider: currHeight = detailWrapper.offsetHeight --}}
            <div id="detailWrapper" style="height:300px; top:0; left:0;">
                <div id="detail" class="detail_screen small">
                    <div id="techDetailLoading"></div>
                </div>
            </div>

        </div>	<div class="c-left"></div>
        <div class="c-right"></div>
        <div id="buttonz">
            <div class="header">
                <h2>{{ __('t_ingame.premium.your_officers') }}</h2>
            </div>
            <div class="content">
                <p class="stimulus">
                    {{ __('t_ingame.premium.intro_text') }}</p>

                <ul id="building">
                    <li class="on button" id="button1">
                        <div class="premium1">
                            <div class="officers100  darkMatter">
                                <a tabindex="1" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_dark_matter') }}" class="detail_button tooltip js_hideTipOnMobile slideIn" ref="1" data-type="1">
                        <span class="ecke">
                            <span class="level">
                                {{ number_format($darkMatter, 0, ',', '.') }}
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>			</li>
                    @php
                        $officerList = [
                            ['ref' => 2,  'key' => 'commander',   'css' => 'commander',   'active' => $officer->isCommanderActive(),   'title' => __('t_ingame.premium.info_commander')],
                            ['ref' => 3,  'key' => 'admiral',     'css' => 'admiral',     'active' => $officer->isAdmiralActive(),     'title' => __('t_ingame.premium.info_admiral')],
                            ['ref' => 4,  'key' => 'engineer',    'css' => 'engineer',    'active' => $officer->isEngineerActive(),    'title' => __('t_ingame.premium.info_engineer')],
                            ['ref' => 5,  'key' => 'geologist',   'css' => 'geologist',   'active' => $officer->isGeologistActive(),   'title' => __('t_ingame.premium.info_geologist')],
                            ['ref' => 6,  'key' => 'technocrat',  'css' => 'technocrat',  'active' => $officer->isTechnocratActive(),  'title' => __('t_ingame.premium.info_technocrat')],
                            ['ref' => 12, 'key' => 'all_officers','css' => 'allOfficers', 'active' => $officer->isAllOfficersActive(), 'title' => __('t_ingame.premium.info_commanding_staff')],
                        ];
                    @endphp
                    @foreach($officerList as $off)
                    <li class="button {{ $off['active'] ? 'on' : '' }}" id="button{{ $off['ref'] }}">
                        <div class="premium">
                            <div class="officers100  {{ $off['css'] }}">
                                <a tabindex="{{ $off['ref'] }}" href="javascript:void(0);" title="{{ $off['title'] }}" ref="{{ $off['ref'] }}" data-type="{{ $off['ref'] }}" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                @if($off['active'])
                                    <img src="/img/icons/b1c7ef5b1164eba44e55b7f6d25d35.gif" width="12" height="11">
                                @else
                                    <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                                @endif
                            </span>
                        </span>
                                </a>
                            </div>
                        @if($off['ref'] === 12)
                            <div class="remaining tooltip " title="">
                                <span class="remDate">{{ __('t_ingame.premium.remaining_officers', ['current' => $officer->getActiveOfficerCount(), 'max' => 5]) }}</span>
                            </div>
                        @endif
                        </div>
                    </li>
                    @endforeach

                    <li class="allOfficers {{ $officer->getActiveOfficerCount() >= 5 ? 'on' : 'off' }}">
                        <span title="{{ __('t_ingame.premium.benefit_fleet_slots_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_fleet_slots') }}</span><span title="{{ __('t_ingame.premium.benefit_energy_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_energy') }}</span><span title="{{ __('t_ingame.premium.benefit_mines_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_mines') }}</span><span title="{{ __('t_ingame.premium.benefit_espionage_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_espionage') }}</span>            </li>
                </ul>
                <br class="clearfloat">
                <div class="footer"></div>
            </div>
        </div>
    </div>

    <script>
        // URL usato da loadDetails() nel main layout per le chiamate AJAX degli ufficiali
        var detailUrl = "{{ route('premium.ajax') }}";

        $(document).ready(function () {
            // Inizializza gfSlider con il wrapper del detail panel.
            // currHeight = offsetHeight di #detailWrapper (300px dal style inline).
            // Questo permette al handler .slideIn nel JS globale di funzionare
            // anche su pagine diverse da overview.
            gfSlider = new GFSlider(getElementByIdWithCache('detailWrapper'));

            // Se la pagina è stata aperta con ?openDetail=N (es. dal click sull'icona ufficiale
            // nella barra header), simula il click sul bottone corrispondente per aprire
            // automaticamente il pannello dettagli dell'ufficiale.
            var openDetail = {{ (int) request()->query('openDetail', 0) }};
            if (openDetail) {
                var $btn = $("a.detail_button[ref='" + openDetail + "']");
                if ($btn.length) {
                    $btn.trigger('click');
                }
            }
        });
    </script>

@endsection
