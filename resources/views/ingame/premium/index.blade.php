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
                <h2>Recruit Officers</h2>
            </div>

            <div id="detail" class="detail_screen small">
                <div id="techDetailLoading"></div>
            </div>

        </div>	<div class="c-left"></div>
        <div class="c-right"></div>
        <div id="buttonz">
            <div class="header">
                <h2>Your officers</h2>
            </div>
            <div class="content">
                <p class="stimulus">
                    With your officers you can lead your empire to a size beyond your wildest dreams - all you need is some Dark Matter and your workers and advisers will work even harder!        </p>

                <ul id="building">
                    <li class="on button" id="button1">
                        <div class="premium1">
                            <div class="officers100  darkMatter">
                                <a tabindex="1" href="javascript:void(0);" title="More information about: Dark Matter" class="detail_button tooltip js_hideTipOnMobile slideIn" ref="1">
                        <span class="ecke">
                            <span class="level">
                                0	                            </span>
                        </span>
                                </a>
                            </div>
                        </div>			</li>
                    <li class="button" id="button2">
                        <div class="premium">
                            <div class="officers100  commander">
                                <a tabindex="2" href="javascript:void(0);" title="More information about: Commander" ref="2" class="detail_button tooltip js_hideTipOnMobile slideIn">
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
                                <a tabindex="3" href="javascript:void(0);" title="More information about: Admiral" ref="3" class="detail_button tooltip js_hideTipOnMobile slideIn">
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
                                <a tabindex="4" href="javascript:void(0);" title="More information about: Engineer" ref="4" class="detail_button tooltip js_hideTipOnMobile slideIn">
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
                                <a tabindex="5" href="javascript:void(0);" title="More information about: Geologist" ref="5" class="detail_button tooltip js_hideTipOnMobile slideIn">
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
                                <a tabindex="6" href="javascript:void(0);" title="More information about: Technocrat" ref="6" class="detail_button tooltip js_hideTipOnMobile slideIn">
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
                                <a tabindex="12" href="javascript:void(0);" title="More information about: Commanding Staff" ref="12" class="detail_button tooltip js_hideTipOnMobile slideIn">
                        <span class="ecke">
                            <span class="level">
                                <img src="/img/icons/aa2ad16d1e00956f7dc8af8be3ca52.gif" width="12" height="11">
                            </span>
                        </span>
                                </a>
                            </div>
                            <div class="remaining tooltip " title="">
                                <span class="remDate">0 of 5</span>
                            </div>
                        </div>
                    </li>

                    <li class="allOfficers off">
                        <span title="You can dispatch more fleets at the same time." class="tooltipCustom tooltipTop">Max. fleet slots +1</span><span title="Your power stations and solar satellites produce 2% more energy." class="tooltipCustom tooltipTop">+2% energy production</span><span title="Your mines produce 2% more." class="tooltipCustom tooltipTop">+2% mine production</span><span title="1 levels will be added to your espionage research." class="tooltipCustom tooltipTop">+1 espionage levels</span>            </li>
                </ul>
                <br class="clearfloat">
                <div class="footer"></div>
            </div>
        </div>
    </div>

@endsection
