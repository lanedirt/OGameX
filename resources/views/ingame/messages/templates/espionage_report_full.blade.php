<div class="detail_msg_head">
    <div class="msg_status"></div>
    <span class="msg_title new blue_txt">{!! $subject !!}</span>
    <span class="msg_date fright">21.05.2024 19:00:28</span>
    <br/>
    <span class="msg_sender_label">@lang('From'): </span>
    <span class="msg_sender">{{ $from }}</span>

    <!-- only if comments are allowed (Only shared reports and broadcasts have comments): -->

    <div class="msg_actions clearfix">
        <div class="icon_nf_link fleft">
                    <span class="icon_nf icon_apikey tooltipCustom tooltip-width:400 fleft"
                          title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-256-265b38b75e565e12526a560bf4f5c83bfce4c5c5' readonly onclick='select()' style='width:360px'></input>"></span>
        </div>
        <a href="#TODOpage=shareReportOverlay&messageId=1645218"
           data-overlay-title="share message" title='share message'
           class="icon_nf_link fleft overlay tooltip js_hideTipOnMobile"
        >
                    <span class="icon_nf icon_share tooltip js_hideTipOnMobile"
                          title='share message'></span>
        </a>

        <a href="#TODOpage=ingame&component=fleetdispatch&galaxy=1&system=4&position=10&type=1&mission=1" class="icon_nf_link fleft">
                    <span class="icon_nf icon_attack tooltip js_hideTipOnMobile" title='Attack'>
                                                </span>
        </a>
        <a href="#" onClick="sendShipsWithPopup(6,1,4,10,1,0); return false;" class="icon_nf_link fleft">
                    <span class="icon_nf icon_espionage tooltip js_hideTipOnMobile"
                          title='Espionage'>
                                                </span>
        </a>
        <a href="javascript:void(0);" class="icon_nf_link fright">
                    <span class="icon_nf icon_refuse js_actionKillDetail tooltip js_hideTipOnMobile"
                          title='delete'></span>
        </a>
    </div>
</div>
<div class="detail_msg_ctn">

    <div class="detail_txt">
        <span>@lang('Player')&nbsp;&nbsp;<span class="status_abbr_active">{!! $playername !!}</span></span>
    </div>
    <div class="detail_txt">
        <span>@lang('Class'):<span class="status_abbr_inactive">&nbsp;@lang('Unknown')</span></span>
    </div>
    <div class="detail_txt">
        <span>@lang('Alliance Class'):&nbsp;<span class="alliance_class small none">@lang('No alliance class selected')</span></span>
    </div>
    <div class="detail_txt">
        @lang('Chance of counter-espionage'): 0%
        <div class="">
            @lang('Your espionage does not show abnormalities in the atmosphere of the planet. There appears to have been no activity on the planet within the last hour.')
        </div>
    </div>
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('Resources')</span>
    </div>
    <ul class="detail_list clearfix" data-type="resources">
        <li class="resource_list_el tooltipCustom" title="{{ $resources->metal->getFormattedFull() }}">
            <div class="resourceIcon metal"></div>
            <span class="res_value">{{ $resources->metal->getFormattedLong() }}</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="{{ $resources->crystal->getFormattedFull() }}">
            <div class="resourceIcon crystal"></div>
            <span class="res_value">{{ $resources->crystal->getFormattedLong() }}</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="{{ $resources->deuterium->getFormattedFull() }}">
            <div class="resourceIcon deuterium"></div>
            <span class="res_value">{{ $resources->deuterium->getFormattedLong() }}</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="{{ $resources->energy->getFormattedFull() }}">
            <div class="resourceIcon energy"></div>
            <span class="res_value">{{ $resources->energy->getFormattedLong() }}</span>
        </li>
    </ul>
    <!--
    <ul class="detail_list clearfix" data-type="resources">
        <li class="resource_list_el tooltipCustom" title="190,005">
            <div class="resourceIcon food"></div>
            <span class="res_value">190,005</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="27,893,982">
            <div class="resourceIcon population"></div>
            <span class="res_value">27.893Mn</span>
        </li>
    </ul>
    -->
    @if ($debris->any())
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('debris field')</span>
    </div>
    <ul class="detail_list clearfix" data-type="resources">
        <li class="resource_list_el tooltipCustom" title="{{ $debris->metal->getFormattedFull() }}">
            <div class="resourceIcon metal"></div>
            <span class="res_value">{{ $debris->metal->getFormattedLong() }}</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="{{ $debris->crystal->getFormattedFull() }}">
            <div class="resourceIcon crystal"></div>
            <span class="res_value">{{ $debris->crystal->getFormattedLong() }}</span>
        </li>
        <li class="resource_list_el tooltipCustom" title="{{ $debris->deuterium->getFormattedFull() }}">
            <div class="resourceIcon deuterium"></div>
            <span class="res_value">{{ $debris->deuterium->getFormattedLong() }}</span>
        </li>
    </ul>
    @endif
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('Fleets')</span>
    </div>
    <ul class="detail_list clearfix" data-type="ships">
        @php /** @var OGame\ViewModels\UnitViewModel $unit */ @endphp
        @forelse ($ships as $unit)
            <li class="detail_list_el">
                <div class="shipImage float_left">
                    <img class="tech{{ $unit->object->id }}" width="28" height="28" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                </div>
                <span class="detail_list_txt">{{ $unit->object->title }}</span>
                <span class="fright" style="margin-right: 10px;">{{ $unit->amount }}</span>
            </li>
        @empty
            @lang('We were unable to retrieve any reliable information of this type from the scan.')
        @endforelse
    </ul>

    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('Defense')</span>
    </div>
    <ul class="detail_list clearfix" data-type="defense">
        @php /** @var OGame\ViewModels\UnitViewModel $unit */ @endphp
        @forelse ($defense as $unit)
            <li class="detail_list_el">
                <div class="defense_image float_left">
                    <img class="defense{{ $unit->object->id }}" width="28" height="28" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                </div>
                <span class="detail_list_txt">{{ $unit->object->title }}</span>
                <span class="fright" style="margin-right: 10px;">{{ $unit->amount }}</span>
            </li>
        @empty
            @lang('We were unable to retrieve any reliable information of this type from the scan.')
        @endforelse
    </ul>

    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('Building')</span>
    </div>

    <ul class="detail_list clearfix" data-type="buildings">
        @php /** @var OGame\ViewModels\UnitViewModel $unit */ @endphp
        @forelse ($buildings as $unit)
            <li class="detail_list_el">
                <div class="building_image float_left">
                    <img class="building{{ $unit->object->id }}" width="28" height="28" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                </div>
                <span class="detail_list_txt">{{ $unit->object->title }}</span>
                <span class="fright" style="margin-right: 10px;">{{ $unit->amount }}</span>
            </li>
        @empty
            @lang('We were unable to retrieve any reliable information of this type from the scan.')
        @endforelse
    </ul>

    <!--
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">Lifeform Buildings</span>
    </div>
    <ul class="detail_list clearfix" data-type="buildings">
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11101"></div>
            </div>
            <span class="detail_list_txt">Residential Sector</span>
            <span class="fright" style="margin-right: 10px;">42</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11102"></div>
            </div>
            <span class="detail_list_txt">Biosphere Farm</span>
            <span class="fright" style="margin-right: 10px;">43</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11103"></div>
            </div>
            <span class="detail_list_txt">Research Centre</span>
            <span class="fright" style="margin-right: 10px;">5</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11104"></div>
            </div>
            <span class="detail_list_txt">Academy of Sciences</span>
            <span class="fright" style="margin-right: 10px;">3</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11106"></div>
            </div>
            <span class="detail_list_txt">High Energy Smelting</span>
            <span class="fright" style="margin-right: 10px;">5</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11107"></div>
            </div>
            <span class="detail_list_txt">Food Silo</span>
            <span class="fright" style="margin-right: 10px;">6</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11108"></div>
            </div>
            <span class="detail_list_txt">Fusion-Powered Production</span>
            <span class="fright" style="margin-right: 10px;">3</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11109"></div>
            </div>
            <span class="detail_list_txt">Skyscraper
</span>
            <span class="fright" style="margin-right: 10px;">2</span>
        </li>
        <li class="detail_list_el">
            <div class="building_image float_left">
                <div class="lifeformsprite sprite_tiny queuePic lifeformqueuetiny lifeformTech11110"></div>
            </div>
            <span class="detail_list_txt">Biotech Lab</span>
            <span class="fright" style="margin-right: 10px;">3</span>
        </li>
    </ul>
    -->

    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">@lang('Research')</span>
    </div>

    <ul class="detail_list clearfix" data-type="research">
        @forelse ($research as $unit)
            <li class="detail_list_el">
                <div class="research_image float_left">
                    <img class="research{{ $unit->object->id }}" width="28" height="28" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif">
                </div>
                <span class="detail_list_txt">{{ $unit->object->title }}</span>
                <span class="fright" style="margin-right: 10px;">{{ $unit->amount }}</span>
            </li>
        @empty
            @lang('We were unable to retrieve any reliable information of this type from the scan.')
        @endforelse
    </ul>

    <!--
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">Lifeform Research</span>
    </div>
    <div class="lfBonusReportWrapper">
        <div class="lfsettingsContentWrapper">
            <bonus-items-holder>
                <lifeform-level-bonuses>
                    <bonus-item-heading>
                        <div></div>
                        <div aria-label="Lifeform experience level bonus">Lifeform experience level bonus</div>
                        <div class="info tooltip js_hideTipOnMobile" title="Increasing the experience level of your lifeforms gives you a 0.1% bonus per level to all lifeform technologies on planets where these lifeforms are active. The bonus is only available while these lifeforms are active on the planet. All lifeform technologies on the planet benefit from this bonus, not just those of the active lifeform. You can increase your lifeform experience level by sending people on discovery missions using the Humans’ Intergalactic Envoys tech. Once you’ve discovered all lifeforms, you’ll receive a random amount of experience each time you would have discovered a new lifeform. The maximum experience level for this bonus is level 100 (10% bonus).">
                            <span class="icon icon_info"></span>
                        </div>
                    </bonus-item-heading>
                    <bonus-item-content display-block>
                        <lifeform-items>
                            <lifeform-item>
                                <lifeform-avatar>
                                    <div class="currentlevel" aria-label="Level">Level <strong>9</strong></div>
                                    <lifeform-avatar-xp-holder>
                                        <div class="lifeform-item-icon lifeform1"></div>
                                        <div class="xpHolder">
                                            <div class="xpbar tooltip js_hideTipOnMobile" title="Level 9: 5395/9000 XP
" aria-label="Level 9: 5395/9000 XP
">
                                                <svg
                                                        class="progress-ring"
                                                        id="progress1"
                                                        height="88"
                                                        width="88"
                                                >
                                                    <circle
                                                            class="progress-ring__back"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#333"
                                                            stroke-dasharray="238.76104167282"
                                                            stroke-dashoffset="0"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                    <circle
                                                            class="progress-ring__circle"
                                                            id="progressBar1"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#99cc00"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                </svg>
                                                <script type="text/javascript">
                                                    circle = document.querySelector('#progressBar1')
                                                    circumference = 238.76104167282
                                                    circle.style.strokeDasharray = `${circumference} ${circumference}`;
                                                    offset = circumference - 59.944444444444 / 100 * circumference
                                                    circle.style.strokeDashoffset = `${offset}`;
                                                </script>
                                            </div>
                                        </div></lifeform-avatar-xp-holder>        <div class="bonusValue">Bonus: 0.9%</div>
                                </lifeform-avatar>            </lifeform-item>
                            <lifeform-item>
                                <lifeform-avatar>
                                    <div class="currentlevel" aria-label="Level">Level <strong>8</strong></div>
                                    <lifeform-avatar-xp-holder>
                                        <div class="lifeform-item-icon lifeform3"></div>
                                        <div class="xpHolder">
                                            <div class="xpbar tooltip js_hideTipOnMobile" title="Level 8: 5767/8100 XP
" aria-label="Level 8: 5767/8100 XP
">
                                                <svg
                                                        class="progress-ring"
                                                        id="progress3"
                                                        height="88"
                                                        width="88"
                                                >
                                                    <circle
                                                            class="progress-ring__back"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#333"
                                                            stroke-dasharray="238.76104167282"
                                                            stroke-dashoffset="0"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                    <circle
                                                            class="progress-ring__circle"
                                                            id="progressBar3"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#99cc00"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                </svg>
                                                <script type="text/javascript">
                                                    circle = document.querySelector('#progressBar3')
                                                    circumference = 238.76104167282
                                                    circle.style.strokeDasharray = `${circumference} ${circumference}`;
                                                    offset = circumference - 71.197530864198 / 100 * circumference
                                                    circle.style.strokeDashoffset = `${offset}`;
                                                </script>
                                            </div>
                                        </div></lifeform-avatar-xp-holder>        <div class="bonusValue">Bonus: 0.8%</div>
                                </lifeform-avatar>            </lifeform-item>
                            <lifeform-item>
                                <lifeform-avatar>
                                    <div class="currentlevel" aria-label="Level">Level <strong>6</strong></div>
                                    <lifeform-avatar-xp-holder>
                                        <div class="lifeform-item-icon lifeform4"></div>
                                        <div class="xpHolder">
                                            <div class="xpbar tooltip js_hideTipOnMobile" title="Level 6: 1976/6300 XP
" aria-label="Level 6: 1976/6300 XP
">
                                                <svg
                                                        class="progress-ring"
                                                        id="progress4"
                                                        height="88"
                                                        width="88"
                                                >
                                                    <circle
                                                            class="progress-ring__back"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#333"
                                                            stroke-dasharray="238.76104167282"
                                                            stroke-dashoffset="0"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                    <circle
                                                            class="progress-ring__circle"
                                                            id="progressBar4"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#99cc00"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                </svg>
                                                <script type="text/javascript">
                                                    circle = document.querySelector('#progressBar4')
                                                    circumference = 238.76104167282
                                                    circle.style.strokeDasharray = `${circumference} ${circumference}`;
                                                    offset = circumference - 31.365079365079 / 100 * circumference
                                                    circle.style.strokeDashoffset = `${offset}`;
                                                </script>
                                            </div>
                                        </div></lifeform-avatar-xp-holder>        <div class="bonusValue">Bonus: 0.6%</div>
                                </lifeform-avatar>            </lifeform-item>
                            <lifeform-item>
                                <lifeform-avatar>
                                    <div class="currentlevel" aria-label="Level">Level <strong>7</strong></div>
                                    <lifeform-avatar-xp-holder>
                                        <div class="lifeform-item-icon lifeform2"></div>
                                        <div class="xpHolder">
                                            <div class="xpbar tooltip js_hideTipOnMobile" title="Level 7: 3084/7200 XP
" aria-label="Level 7: 3084/7200 XP
">
                                                <svg
                                                        class="progress-ring"
                                                        id="progress2"
                                                        height="88"
                                                        width="88"
                                                >
                                                    <circle
                                                            class="progress-ring__back"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#333"
                                                            stroke-dasharray="238.76104167282"
                                                            stroke-dashoffset="0"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                    <circle
                                                            class="progress-ring__circle"
                                                            id="progressBar2"
                                                            stroke-width="5"
                                                            fill="transparent"
                                                            stroke="#99cc00"
                                                            r="38"
                                                            cx="44"
                                                            cy="44"
                                                    />
                                                </svg>
                                                <script type="text/javascript">
                                                    circle = document.querySelector('#progressBar2')
                                                    circumference = 238.76104167282
                                                    circle.style.strokeDasharray = `${circumference} ${circumference}`;
                                                    offset = circumference - 42.833333333333 / 100 * circumference
                                                    circle.style.strokeDashoffset = `${offset}`;
                                                </script>
                                            </div>
                                        </div></lifeform-avatar-xp-holder>        <div class="bonusValue">Bonus: 0.7%</div>
                                </lifeform-avatar>            </lifeform-item>
                        </lifeform-items>
                    </bonus-item-content>        </lifeform-level-bonuses>
            </bonus-items-holder>
        </div>
        <div class="lfsettingsContentWrapper">

            <bonus-item-heading data-toggable="globalLifeformBonuses">
                <arrow-icon></arrow-icon>
                <div aria-label="Global Lifeform Tech Bonus">Global Lifeform Tech Bonus</div>
                <div class="info tooltip js_hideTipOnMobile" rel="legendTT">
                </div>
            </bonus-item-heading>
            <bonus-item-content data-toggable-target="globalLifeformBonuses" w100p>
                <bonus-item-content-holder>
                    <div text-left>
                        Lifeform tech bonuses are global, which means the bonuses of all your planets are added together and apply to all of your planets and moons. Here you can find an overview of all your globally applicable lifeform tech bonuses. Remember that the bonuses from lifeform buildings only apply locally, and are thus not listed here.

                        Lifeform tech bonuses are influenced by a number of factors. One of these is the lifeform experience level, and whether the lifeform has any buildings which increases the lifeform tech bonus (e.g. Humans and Mechas). The global lifeform tech bonus is also only active while your population is large enough. This means that anyone who attacks your planet and kills enough of your population can reduce your global lifeform tech bonus until such time as your population numbers have recovered.
                    </div>
                </bonus-item-content-holder>
            </bonus-item-content>
        </div>
        <div class="lfsettingsContentWrapper">
            <bonus-items-holder>
                <lifeform-technology-bonuses>
                    <technology-bonus-category>
                        <bonus-item-heading class="toggable" data-toggable="categoryResources">
                            <arrow-icon></arrow-icon>
                            <div aria-label="Resource bonuses">Resource bonuses</div>
                            <div class="info" rel="legendTT" title="">
                            </div>
                        </bonus-item-heading>
                        <bonus-item-content class="toggableTarget" data-toggable-target="categoryResources"  >
                            <bonus-item-content-holder>
                                <bonus-items mb4>
                                    <bonus-item w68>
                                        <div p4 text-blue>Total</div>
                                    </bonus-item>
                                </bonus-items>
                                <inner-bonus-item-heading data-toggable="subcategoryResources0">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Metal">Metal</div>
                                    <div class="subCategoryBonus">0.18%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResources0">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.18%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>2</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>3</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech11202 sq20 bordered class=tooltip title="High-Performance Extractors" aria-label="High-Performance Extractors"></technology-icon>                        <div ml4 aria-label="High-Performance Extractors">High-Performance Extractors</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.18%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading data-toggable="subcategoryResources1">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Crystal">Crystal</div>
                                    <div class="subCategoryBonus">0.18%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResources1">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.18%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>2</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>3</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech11202 sq20 bordered class=tooltip title="High-Performance Extractors" aria-label="High-Performance Extractors"></technology-icon>                        <div ml4 aria-label="High-Performance Extractors">High-Performance Extractors</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.18%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading data-toggable="subcategoryResources2">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Deuterium">Deuterium</div>
                                    <div class="subCategoryBonus">0.5%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResources2">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.42%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>2</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>3</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech11202 sq20 bordered class=tooltip title="High-Performance Extractors" aria-label="High-Performance Extractors"></technology-icon>                        <div ml4 aria-label="High-Performance Extractors">High-Performance Extractors</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.18%</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>3</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>3</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech12203 sq20 bordered class=tooltip title="High Energy Pump Systems" aria-label="High Energy Pump Systems"></technology-icon>                        <div ml4 aria-label="High Energy Pump Systems">High Energy Pump Systems</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.24%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading data-toggable="subcategoryResources3">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Energy">Energy</div>
                                    <div class="subCategoryBonus">1.26%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResources3">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.5%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>6</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>2</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech12206 sq20 bordered class=tooltip title="Geothermal Power Plants" aria-label="Geothermal Power Plants"></technology-icon>                        <div ml4 aria-label="Geothermal Power Plants">Geothermal Power Plants</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.5%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading data-toggable="subcategoryResourcesExpedition">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Enhanced Sensor Technology
">Enhanced Sensor Technology
                                    </div>
                                    <div class="subCategoryBonus">0.8%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResourcesExpedition">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.8%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>5</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>4</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech14205 sq20 bordered class=tooltip title="Enhanced Sensor Technology
" aria-label="Enhanced Sensor Technology
"></technology-icon>                        <div ml4 aria-label="Enhanced Sensor Technology
">Enhanced Sensor Technology
                                                            </div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.8%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading data-toggable="subcategoryResourcesExpeditionShipsFound">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Telekinetic Tractor Beam
">Telekinetic Tractor Beam
                                    </div>
                                    <div class="subCategoryBonus">0.8%</div>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryResourcesExpeditionShipsFound">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <arrow-icon></arrow-icon>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <div fontsize-11 class="subCategoryBonus">0.8%</div>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 text-blue aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 text-blue aria-label="Technology">Technology</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 text-blue aria-label="Total">Total</space-object-technology-item>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>4</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w50 text-center p4 light-background flex align-items-center justify-content-center>4</space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w420 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech14204 sq20 bordered class=tooltip title="Telekinetic Tractor Beam
" aria-label="Telekinetic Tractor Beam
"></technology-icon>                        <div ml4 aria-label="Telekinetic Tractor Beam
">Telekinetic Tractor Beam
                                                            </div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item fontsize-11 w100 text-center p4 light-background flex align-items-center justify-content-center>0.8%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>            </bonus-item-content-holder>
                        </bonus-item-content>                </technology-bonus-category>
                </lifeform-technology-bonuses>
            </bonus-items-holder>
        </div>
        <div class="lfsettingsContentWrapper">
            <bonus-items-holder>
                <lifeform-technology-bonuses>
                    <technology-bonus-category>
                        <bonus-item-heading class="toggable" data-toggable="categoryShips">
                            <arrow-icon></arrow-icon>
                            <div aria-label="Ship bonuses">Ship bonuses</div>
                            <div class="info" rel="legendTT" title="">
                            </div>
                        </bonus-item-heading>
                        <bonus-item-content class="toggableTarget" data-toggable-target="categoryShips"  >
                            <bonus-item-content-holder>
                                <bonus-items mb4 mr4>
                                    <bonus-item w68>
                                        <armor-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Structural Integrity" aria-label="Structural Integrity"></armor-icon>
                                    </bonus-item>
                                    <bonus-item w68>
                                        <shield-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Shield Strength" aria-label="Shield Strength"></shield-icon>
                                    </bonus-item>
                                    <bonus-item w68>
                                        <weapon-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Attack Strength" aria-label="Attack Strength"></weapon-icon>
                                    </bonus-item>
                                    <bonus-item w68>
                                        <speed-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Speed" aria-label="Speed"></speed-icon>
                                    </bonus-item>
                                    <bonus-item w68>
                                        <cargo-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Cargo Capacity" aria-label="Cargo Capacity"></cargo-icon>
                                    </bonus-item>
                                    <bonus-item w68 flex-direction-row justify-content-evenly>
                                        <fuel-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Fuel consumption %" aria-label="Fuel consumption %"></fuel-icon>
                                        <div class="info tooltip js_hideTipOnMobile" title="Fuel can only be reduced by a maximum of 90% of the base value. Even with server settings, your ships’ fuel consumption cannot be reduced below 10%.">
                                            <span class="icon icon_info"></span>
                                        </div>
                                    </bonus-item>
                                </bonus-items>                            <inner-bonus-item-heading h30 data-toggable="subcategoryShips202">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular transporterSmall sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Small Cargo">Small Cargo</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips202">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips203">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular transporterLarge sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Large Cargo">Large Cargo</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips203">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips204">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular fighterLight sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Light Fighter">Light Fighter</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips204">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips205">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular fighterHeavy sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Heavy Fighter">Heavy Fighter</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips205">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips206">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular cruiser sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Cruiser">Cruiser</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips206">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips207">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular battleship sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Battleship">Battleship</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips207">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips208">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular colonyShip sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Colony Ship">Colony Ship</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips208">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips209">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular recycler sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Recycler">Recycler</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips209">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips210">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular espionageProbe sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Espionage Probe">Espionage Probe</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips210">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips211">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular bomber sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Bomber">Bomber</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips211">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips212">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular solarSatellite sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Solar Satellite">Solar Satellite</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips212">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips213">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular destroyer sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Destroyer">Destroyer</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips213">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips214">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular deathstar sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Deathstar">Deathstar</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips214">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips215">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular interceptor sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Battlecruiser">Battlecruiser</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips215">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips217">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular resbuggy sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Crawler">Crawler</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips217">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips218">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular reaper sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Reaper">Reaper</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips218">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>                    <inner-bonus-item-heading h30 data-toggable="subcategoryShips219">
                                    <arrow-icon></arrow-icon>

                                    <technology-icon regular explorer sq20 bordered></technology-icon>    <div class="subCategoryTitle" aria-label="Pathfinder">Pathfinder</div>
                                    <bonus-items>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>0.8%</bonus-item>
                                        <bonus-item w68>-</bonus-item>
                                        <bonus-item w68>-0.13% <br/><span fontsize-9 text-blue mt4>Max. -30%</span> </bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryShips219">
                                    <bonus-item-content-holder>
                                    </bonus-item-content-holder>
                                </bonus-item-content>            </bonus-item-content-holder>
                        </bonus-item-content>                </technology-bonus-category>
                </lifeform-technology-bonuses>
            </bonus-items-holder>
        </div>
        <div class="lfsettingsContentWrapper">
            <bonus-items-holder>
                <lifeform-technology-bonuses>
                    <technology-bonus-category>
                        <bonus-item-heading class="toggable" data-toggable="categoryMisc">
                            <arrow-icon></arrow-icon>
                            <div aria-label="Misc.">Misc.</div>
                            <div class="info" rel="legendTT" title="">
                            </div>
                        </bonus-item-heading>
                        <bonus-item-content class="toggableTarget" data-toggable-target="categoryMisc"  >
                            <bonus-item-content-holder>
                                <bonus-items mb4 mr4>
                                    <bonus-item w68>
                                        <speed-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Speed" aria-label="Speed"></speed-icon>
                                    </bonus-item>
                                </bonus-items>
                                <inner-bonus-item-heading h30 data-toggable="subcategoryMiscDiscoverer">
                                    <arrow-icon></arrow-icon>
                                    <div class="subCategoryTitle" aria-label="Exploration Flight Duration Bonus">Exploration Flight Duration Bonus</div>
                                    <bonus-items>
                                        <bonus-item w68>6.04%</bonus-item>
                                    </bonus-items>
                                </inner-bonus-item-heading>
                                <bonus-item-content data-toggable-target="subcategoryMiscDiscoverer">
                                    <bonus-item-content-holder>
                                        <bonus-category>
                                            <inner-bonus-item-heading data-toggable="33620205">
                                                <div class="openDetails">
                                                    <a href="javascript:void(0);" class="openCloseDetails">
                                                        <img src="https://gf2.geo.gfsrv.net/cdn10/de1e5f629d9e47d283488eee0c0ede.gif" height="16" width="16" />
                                                    </a>
                                                </div>
                                                <div fontsize-11 class="subCategoryTitle">SMs Bodyguard [1:4:10]</div>
                                                <bonus-items>
                                                    <bonus-item w68 fontsize-11 text-center>4.03%</bonus-item>
                                                </bonus-items>
                                            </inner-bonus-item-heading>
                                            <bonus-item-content data-toggable-target="33620205">
                                                <bonus-item-content-holder blue-bordered-container>
                                                    <space-object-technology>
                                                        <space-object-technology-item w50 text-center p4 text-blue fontsize-11 aria-label="Slot">Slot</space-object-technology-item>
                                                        <space-object-technology-item w50 text-center p4 text-blue fontsize-11 aria-label="Level">Level</space-object-technology-item>
                                                        <space-object-technology-item w450 p4 text-blue fontsize-11 aria-label="Technology">Technology</space-object-technology-item>
                                                        <bonus-items>
                                                            <bonus-item w68 p4>
                                                                <speed-icon icon-blue sq16 class="tooltip js_hideTipOnMobile" title="Speed" aria-label="Speed"></speed-icon>
                                                            </bonus-item>
                                                        </bonus-items>
                                                    </space-object-technology>
                                                    <space-object-technology>
                                                        <space-object-technology-item w50 text-center p4 light-background flex justify-content-center align-items-center fontsize-11>1</space-object-technology-item>
                                                        <space-object-technology-item w50 text-center p4 light-background flex justify-content-center align-items-center fontsize-11>4</space-object-technology-item>
                                                        <space-object-technology-item w450 p4 light-background flex align-items-center>

                                                            <technology-icon lifeform lifeformTech11201 sq20 bordered class=tooltip title="Intergalactic Envoys" aria-label="Intergalactic Envoys"></technology-icon>                        <div ml4 fontsize-11 aria-label="Intergalactic Envoys">Intergalactic Envoys</div>
                                                        </space-object-technology-item>
                                                        <space-object-technology-item light-background p4 w68 flex justify-content-center align-items-center fontsize-11>4.03%</space-object-technology-item>
                                                    </space-object-technology>
                                                </bonus-item-content-holder>
                                            </bonus-item-content>
                                        </bonus-category>
                                    </bonus-item-content-holder>
                                </bonus-item-content>
                            </bonus-item-content-holder>
                        </bonus-item-content>                </technology-bonus-category>
                </lifeform-technology-bonuses>
            </bonus-items-holder>
        </div>
    </div>-->

    <!--
    TODO: implement espionage report comments form
    <br class="clearfloat">
    <div class="section_title">
        <div class="c-left"></div>
        <div class="c-right"></div>
        <span class="title_txt">Comments</span>
    </div>

    <ul class="tab_inner ctn_with_new_msg clearfix">
        <li class="msg">
            <form id="newCommentForm" class="clearfix" action="index.php?page=messages" method="POST">
                <input type="hidden" name="action" value="108">
                <input type="hidden" name="messageId" value="1645218">
                <link rel="stylesheet" href="/cdn/css/select2.css" type="text/css">
                <div>
                    <button class="btn_blue js_send_comment fright ally_send_button" onclick="return false;">Send</button>
                    <div class="editor_wrap">
                        <textarea name="text" class="new_msg_textarea"></textarea>
                    </div>
                </div>
                <script language="javascript">
                    initBBCodeEditor(locaKeys, itemNames, false, '.new_msg_textarea', 2000, true);
                </script>
            </form>
        </li>
    </ul>
    <script language="javascript">
        ogame.messages.initCommentForm();
    </script>
    -->
</div>
