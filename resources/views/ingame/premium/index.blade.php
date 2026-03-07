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

            <div id="detail" class="detail_screen small">
                <div id="techDetailLoading"></div>
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
                                <a tabindex="1" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_dark_matter') }}" class="detail_button tooltip js_hideTipOnMobile slideIn" ref="1">
                        <span class="ecke">
                            <span class="level">
                                {{ number_format($darkMatter, 0, ',', '.') }}
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>			</li>
                    <li class="button" id="button2">
                        <div class="premium">
                            <div class="officers100  commander">
                                <a tabindex="2" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_commander') }}" ref="2" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="button" id="button3">
                        <div class="premium">
                            <div class="officers100  admiral">
                                <a tabindex="3" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_admiral') }}" ref="3" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="button" id="button4">
                        <div class="premium">
                            <div class="officers100  engineer">
                                <a tabindex="4" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_engineer') }}" ref="4" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="button" id="button5">
                        <div class="premium">
                            <div class="officers100  geologist">
                                <a tabindex="5" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_geologist') }}" ref="5" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="button" id="button6">
                        <div class="premium">
                            <div class="officers100  technocrat">
                                <a tabindex="6" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_technocrat') }}" ref="6" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="button" id="button12">
                        <div class="premium">
                            <div class="officers100  allOfficers">
                                <a tabindex="12" href="javascript:void(0);" title="{{ __('t_ingame.premium.info_commanding_staff') }}" ref="12" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                            <div class="remaining tooltip " title="">
                                <span class="remDate">{{ __('t_ingame.premium.remaining_officers', ['current' => 0, 'max' => 5]) }}</span>
                            </div>
                        </div>
                    </li>

                    <li class="allOfficers off">
                        <span title="{{ __('t_ingame.premium.benefit_fleet_slots_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_fleet_slots') }}</span><span title="{{ __('t_ingame.premium.benefit_energy_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_energy') }}</span><span title="{{ __('t_ingame.premium.benefit_mines_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_mines') }}</span><span title="{{ __('t_ingame.premium.benefit_espionage_title') }}" class="tooltipCustom tooltipTop">{{ __('t_ingame.premium.benefit_espionage') }}</span>            </li>
                </ul>
                <br class="clearfloat">
                <div class="footer"></div>
            </div>
        </div>
    </div>

@endsection
