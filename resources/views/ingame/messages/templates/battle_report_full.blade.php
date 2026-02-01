@php /** @var array<OGame\GameObjects\Models\ShipObject> $military_objects */ @endphp
@php /** @var array<OGame\GameObjects\Models\ShipObject> $civil_objects */ @endphp
@php /** @var array<OGame\GameObjects\Models\DefenseObject> $defense_objects */ @endphp
@php /** @var array<OGame\GameObjects\Models\Units\UnitCollection> $attacker_units_start */ @endphp
@php /** @var array<OGame\GameObjects\Models\Units\UnitCollection> $defender_units_start */ @endphp
@php /** @var array<OGame\GameMissions\BattleEngine\Models\BattleResultRound> $rounds */ @endphp

<div id="messagedetails">
<div class="detail_msg_head">
    <span class="msg_title new blue_txt"><span class="middlemark">@lang('Combat Report') {{ $defender_planet_name }} <figure
                    class="planetIcon planet tooltip js_hideTipOnMobile" data-tooltip-title="Planet"></figure> <a
                    href="{{ $defender_planet_link }}"
                    class="txt_link">[{{ $defender_planet_coords }}]</a></span></span>
    <span class="msg_date fright">{{ $report_datetime }}</span>
    <br>
    <span class="msg_sender_label">From:</span>
    <span class="msg_sender">Fleet Command</span>
    <div class="footerHolder">
        <message-footer class="msg_actions">
            <message-footer-actions>

                <gradient-button sq28="">
                    <button class="custom_btn tooltip msgFavouriteBtn" onclick="ogame.messages.flagArchived(this)"
                            data-message-id="8196210" data-tooltip-title="mark as favourite"><img
                                src="/img/icons/basic/not_favorited.png" style="width:20px;height:20px;"></button>
                </gradient-button>

                <gradient-button sq28="">
                    <button class="custom_btn icon_apikey tooltip msgApiKeyBtn" data-tooltip-interactive="true"
                            data-tooltip-append-to-id="messagedetails" data-message-id="8196210"
                            data-tooltip-title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-5b67379a1bc25465e7e7692d01afc560e85eda30' readonly onclick='ogame.messages.copyToClipboard(this)' style='width:360px'></input>"
                            aria-expanded="false"><img src="/img/icons/basic/apikey.png"
                                                       style="width:20px;height:20px;"></button>
                </gradient-button>
                <gradient-button sq28="">
                    <button class="custom_btn overlay tooltip msgShareBtn" data-message-id="8196210"
                            data-overlay-title="share message"
                            data-target="https://s255-en.ogame.gameforge.com/game/index.php?page=componentOnly&amp;component=messagedetails&amp;action=loadShareReport&amp;ajax=1&amp;messageId=8196210"
                            data-tooltip-title="share message"><img src="/img/icons/basic/share.png"
                                                                    style="width:20px;height:20px;"></button>
                </gradient-button>
                <gradient-button sq28="">
                    <button class="custom_btn tooltip msgAttackBtn"
                            onclick="window.location.href='https://s255-en.ogame.gameforge.com/game/index.php?page=ingame&amp;component=fleetdispatch&amp;galaxy=2&amp;system=488&amp;position=1&amp;type=3&amp;mission=1';"
                            data-message-id="8196210" data-tooltip-title="Attack">
                        <div class="msgAttackIconContainer"><img src="/img/icons/basic/attack.png"
                                                                 style="width:20px;height:20px;"></div>
                    </button>
                </gradient-button>


                <gradient-button sq28="">
                    <button class="custom_btn tooltip msgEspionageBtn"
                            onclick="sendShipsWithPopup(6,2,3,11,1,0); return false;" data-message-id="8196210"
                            data-tooltip-title="Espionage"><img src="/img/icons/basic/espionage.png"
                                                                style="width:20px;height:20px;"></button>
                </gradient-button>
            </message-footer-actions>
            <message-footer-delete>
                <gradient-button sq28="">
                    <button class="custom_btn tooltip msgDeleteBtn" onclick="ogame.messages.flagDeleted(this)"
                            data-message-id="8196210" data-tooltip-title="delete"><img
                                src="/img/icons/basic/refuse.png" style="width:20px;height:20px;"></button>
                </gradient-button>
            </message-footer-delete>
        </message-footer>
        <script type="text/javascript">
            initOverlays();
        </script>

    </div>
</div>

<div class="rawMessageData" data-raw-messagetype="25" data-raw-timestamp="1723394617"
     data-raw-date="2024-08-11T16:43:37.000Z" data-raw-hashcode="cr-en-255-5b67379a1bc25465e7e7692d01afc560e85eda30"
     data-raw-coords="2:3:11"
     data-raw-defenderspaceobject="{&quot;id&quot;:33643427,&quot;type&quot;:&quot;planet&quot;,&quot;name&quot;:&quot;Destroyed Planet&quot;,&quot;coordinates&quot;:{&quot;galaxy&quot;:2,&quot;system&quot;:3,&quot;position&quot;:11},&quot;owner&quot;:{&quot;type&quot;:&quot;player&quot;,&quot;id&quot;:102489,&quot;name&quot;:&quot;Lieutenant Cupid&quot;,&quot;alliance&quot;:null,&quot;rankId&quot;:&quot;rank_starlord3&quot;,&quot;status&quot;:[],&quot;classId&quot;:1}}"
     data-raw-fleets="[{&quot;side&quot;:&quot;defender&quot;,&quot;fleetId&quot;:0,&quot;player&quot;:{&quot;type&quot;:&quot;player&quot;,&quot;id&quot;:102489,&quot;name&quot;:&quot;Lieutenant Cupid&quot;,&quot;alliance&quot;:null,&quot;rankId&quot;:&quot;rank_starlord3&quot;,&quot;status&quot;:[],&quot;classId&quot;:1},&quot;spaceObject&quot;:{&quot;id&quot;:33643427,&quot;name&quot;:&quot;Destroyed Planet&quot;,&quot;type&quot;:&quot;planet&quot;,&quot;coordinates&quot;:{&quot;galaxy&quot;:2,&quot;system&quot;:3,&quot;position&quot;:11}},&quot;combatResearchPercentage&quot;:[{&quot;id&quot;:109,&quot;percentage&quot;:130},{&quot;id&quot;:110,&quot;percentage&quot;:120},{&quot;id&quot;:111,&quot;percentage&quot;:140}],&quot;combatTechnologies&quot;:[{&quot;technologyId&quot;:401,&quot;amount&quot;:1},{&quot;technologyId&quot;:402,&quot;amount&quot;:1},{&quot;technologyId&quot;:404,&quot;amount&quot;:1}]},{&quot;side&quot;:&quot;attacker&quot;,&quot;fleetId&quot;:4492924,&quot;player&quot;:{&quot;type&quot;:&quot;player&quot;,&quot;id&quot;:115473,&quot;name&quot;:&quot;Commodore Taurus&quot;,&quot;alliance&quot;:{&quot;id&quot;:501061,&quot;name&quot;:&quot;PTL&quot;,&quot;tag&quot;:&quot;PTL&quot;,&quot;classId&quot;:0},&quot;rankId&quot;:&quot;rank_starlord3&quot;,&quot;status&quot;:[&quot;honorable-target&quot;],&quot;classId&quot;:1},&quot;spaceObject&quot;:{&quot;id&quot;:33699068,&quot;name&quot;:&quot;Moon&quot;,&quot;type&quot;:&quot;moon&quot;,&quot;coordinates&quot;:{&quot;galaxy&quot;:2,&quot;system&quot;:488,&quot;position&quot;:1}},&quot;combatResearchPercentage&quot;:[{&quot;id&quot;:109,&quot;percentage&quot;:180},{&quot;id&quot;:110,&quot;percentage&quot;:180},{&quot;id&quot;:111,&quot;percentage&quot;:190}],&quot;combatTechnologies&quot;:[{&quot;technologyId&quot;:204,&quot;amount&quot;:1},{&quot;technologyId&quot;:205,&quot;amount&quot;:1},{&quot;technologyId&quot;:207,&quot;amount&quot;:1},{&quot;technologyId&quot;:215,&quot;amount&quot;:1},{&quot;technologyId&quot;:211,&quot;amount&quot;:1},{&quot;technologyId&quot;:203,&quot;amount&quot;:1}]}]"
     data-raw-combatrounds="[{&quot;statistics&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;strength&quot;:16740,&quot;hits&quot;:66,&quot;absorbedDamage&quot;:3772},{&quot;side&quot;:&quot;attacker&quot;,&quot;strength&quot;:590656,&quot;hits&quot;:361,&quot;absorbedDamage&quot;:4448}],&quot;fleets&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;fleetId&quot;:0,&quot;technologies&quot;:[{&quot;technologyId&quot;:401,&quot;destroyed&quot;:15,&quot;destroyedTotal&quot;:15,&quot;remaining&quot;:0},{&quot;technologyId&quot;:402,&quot;destroyed&quot;:45,&quot;destroyedTotal&quot;:45,&quot;remaining&quot;:5},{&quot;technologyId&quot;:404,&quot;destroyed&quot;:1,&quot;destroyedTotal&quot;:1,&quot;remaining&quot;:0}]},{&quot;side&quot;:&quot;attacker&quot;,&quot;fleetId&quot;:4492924,&quot;technologies&quot;:[{&quot;technologyId&quot;:204,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:26},{&quot;technologyId&quot;:205,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:29},{&quot;technologyId&quot;:207,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:9},{&quot;technologyId&quot;:215,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:4},{&quot;technologyId&quot;:211,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:6},{&quot;technologyId&quot;:203,&quot;destroyed&quot;:1,&quot;destroyedTotal&quot;:1,&quot;remaining&quot;:99}]}]},{&quot;statistics&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;strength&quot;:1145,&quot;hits&quot;:5,&quot;absorbedDamage&quot;:275},{&quot;side&quot;:&quot;attacker&quot;,&quot;strength&quot;:67042,&quot;hits&quot;:173,&quot;absorbedDamage&quot;:308}],&quot;fleets&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;fleetId&quot;:0,&quot;technologies&quot;:[{&quot;technologyId&quot;:401,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:15,&quot;remaining&quot;:0},{&quot;technologyId&quot;:402,&quot;destroyed&quot;:5,&quot;destroyedTotal&quot;:50,&quot;remaining&quot;:0},{&quot;technologyId&quot;:404,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:1,&quot;remaining&quot;:0}]},{&quot;side&quot;:&quot;attacker&quot;,&quot;fleetId&quot;:4492924,&quot;technologies&quot;:[{&quot;technologyId&quot;:204,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:26},{&quot;technologyId&quot;:205,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:29},{&quot;technologyId&quot;:207,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:9},{&quot;technologyId&quot;:215,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:4},{&quot;technologyId&quot;:211,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:0,&quot;remaining&quot;:6},{&quot;technologyId&quot;:203,&quot;destroyed&quot;:0,&quot;destroyedTotal&quot;:1,&quot;remaining&quot;:99}]}]}]"
     data-raw-result="{&quot;winner&quot;:&quot;attacker&quot;,&quot;loot&quot;:{&quot;percentage&quot;:50,&quot;resources&quot;:[{&quot;resource&quot;:&quot;metal&quot;,&quot;amount&quot;:1468279},{&quot;resource&quot;:&quot;crystal&quot;,&quot;amount&quot;:500451},{&quot;resource&quot;:&quot;deuterium&quot;,&quot;amount&quot;:65266},{&quot;resource&quot;:&quot;food&quot;,&quot;amount&quot;:0}]},&quot;debris&quot;:{&quot;resources&quot;:[{&quot;resource&quot;:&quot;metal&quot;,&quot;total&quot;:3000,&quot;recycled&quot;:0,&quot;remaining&quot;:3000},{&quot;resource&quot;:&quot;crystal&quot;,&quot;total&quot;:3000,&quot;recycled&quot;:0,&quot;remaining&quot;:3000}],&quot;requiredShips&quot;:[{&quot;technologyId&quot;:209,&quot;amount&quot;:1}],&quot;shipsUsedForRecycling&quot;:[],&quot;totalRequiredShips&quot;:[{&quot;technologyId&quot;:209,&quot;amount&quot;:1}]},&quot;totalValueOfUnitsLost&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;value&quot;:167000},{&quot;side&quot;:&quot;attacker&quot;,&quot;value&quot;:12000}],&quot;deathStarDestroyed&quot;:false,&quot;repairedTechnologies&quot;:[{&quot;technologyId&quot;:401,&quot;amount&quot;:11},{&quot;technologyId&quot;:402,&quot;amount&quot;:37},{&quot;technologyId&quot;:404,&quot;amount&quot;:1}],&quot;moonCreation&quot;:null,&quot;honor&quot;:[{&quot;side&quot;:&quot;defender&quot;,&quot;points&quot;:0},{&quot;side&quot;:&quot;attacker&quot;,&quot;points&quot;:-11}],&quot;tacticalRetreat&quot;:{&quot;by&quot;:&quot;none&quot;,&quot;supremacy&quot;:2114000}}"></div>

<div class="messageDetails">
    <div class="detailReport" data-combatreportid="8196210">
        <p class="detail_txt fleft">
            @lang('Tactical retreat'):<span class="middlemark"> 1:100</span>
        </p>
        <p class="fleft">
            <span class="icon_info tooltip" style="margin: 5px" data-tooltip-title="Please note that Deathstars, Espionage Probes, Solar Satellites and any fleet on a ACS Defence mission cannot flee. Tactical retreats are also deactivated in honourable battles. A retreat may also have been manually deactivated or prevented by a lack of deuterium. Bandits and players with more than 500,000 points never retreat."> The defending fleet did not flee.</span>
        </p>
        <!-- Rundenpagenator -->
        <ul class="combat_round_list">
            <li>Rounds: </li>
            <li class="round_id" data-round="0">
                <span class="list_placeholder"></span>
                <a href="#">Start</a>
                <span class="list_placeholder"></span>
            </li>
@foreach ($rounds as $round)
            <li class="round_id" data-round="{{ $loop->iteration }}">
                <span class="list_placeholder"></span>
                <a href="#">{{ $loop->iteration }}</a>
                <span class="list_placeholder"></span>
            </li>
@endforeach
        </ul>
        <br class="clearfloat">
        <!-- Loot -->
        <div class="resourcedisplay loot">
            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt">@lang('Total loot'):</span>
            </div>
            <ul class="detail_list clearfix">
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall metal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $loot_resources->metal->getFormattedLong() }}">{{ $loot_resources->metal->getFormatted() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall crystal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $loot_resources->crystal->getFormattedLong() }}">{{ $loot_resources->crystal->getFormatted() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall deuterium"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $loot_resources->deuterium->getFormattedLong() }}">{{ $loot_resources->deuterium->getFormatted() }}</span>
                </li>
                <!-- TODO: Implement food -->
                <!--
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall food"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="0">0</span>
                </li>
                -->
            </ul>
        </div>

        <!-- Trümmerfeld -->
        <div class="resourcedisplay tf">
            <!-- Trümmerfeld gesamt -->
            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt">@lang('Debris (new)'):</span>
                <span class="title_txt tooltipCustom" data-tooltip-title="{{ $debris_sum_formatted }}">{{ $debris_sum_formatted }}</span>
                <span class="title_txt">=&gt; {{ $debris_recyclers_needed }} @lang('Recycler')</span>
            </div>
            <ul class="detail_list clearfix">
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall metal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $debris_resources->metal->getFormattedLong() }}">{{ $debris_resources->metal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall crystal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $debris_resources->crystal->getFormattedLong() }}">{{ $debris_resources->crystal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall deuterium"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $debris_resources->deuterium->getFormattedLong() }}">{{ $debris_resources->deuterium->getFormattedLong() }}</span>
                </li>
            </ul>
@if ($collected_debris_resources->sum() > 0)
            <!-- Trümmerfeld von reaper abgebaut -->
            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt">@lang('Mined after combat'):</span>
                <span class="title_txt tooltipCustom" data-tooltip-title="{{ number_format($collected_debris_resources->sum(), 0, ',', '.') }}">{{ number_format($collected_debris_resources->sum(), 0, ',', '.') }}</span>
                <span class="title_txt">=&gt; {{ $total_reapers_used }} @lang('Reaper')</span>
            </div>
            <ul class="detail_list clearfix">
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall metal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $attacker_collected_debris_resources->metal->getFormattedLong() }}">{{ $attacker_collected_debris_resources->metal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall crystal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $attacker_collected_debris_resources->crystal->getFormattedLong() }}">{{ $attacker_collected_debris_resources->crystal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall deuterium"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $attacker_collected_debris_resources->deuterium->getFormattedLong() }}">{{ $attacker_collected_debris_resources->deuterium->getFormattedLong() }}</span>
                </li>
            </ul>
            <ul class="detail_list clearfix">
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall metal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $defender_collected_debris_resources->metal->getFormattedLong() }}">{{ $defender_collected_debris_resources->metal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall crystal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $defender_collected_debris_resources->crystal->getFormattedLong() }}">{{ $defender_collected_debris_resources->crystal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall deuterium"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $defender_collected_debris_resources->deuterium->getFormattedLong() }}">{{ $defender_collected_debris_resources->deuterium->getFormattedLong() }}</span>
                </li>
            </ul>
            <!-- Trümmerfeld übrig -->
            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt">@lang('Debris fields (left)'):</span>
                <span class="title_txt tooltipCustom" data-tooltip-title="{{ number_format($remaining_debris_resources->sum(), 0, ',', '.') }}">{{ number_format($remaining_debris_resources->sum(), 0, ',', '.') }}</span>
                <span class="title_txt">=&gt; {{ $remaining_debris_recyclers_needed }} @lang('Recycler')</span>
            </div>
            <ul class="detail_list clearfix">
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall metal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $remaining_debris_resources->metal->getFormattedLong() }}">{{ $remaining_debris_resources->metal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall crystal"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $remaining_debris_resources->crystal->getFormattedLong() }}">{{ $remaining_debris_resources->crystal->getFormattedLong() }}</span>
                </li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall deuterium"></div>
                    <span class="res_value tooltipCustom" data-tooltip-title="{{ $remaining_debris_resources->deuterium->getFormattedLong() }}">{{ $remaining_debris_resources->deuterium->getFormattedLong() }}</span>
                </li>
            </ul>
@endif
        </div>

        <br class="clearfloat">
        <div class="fightdetails">
           @lang(' Honour points'):<br> (<span class="overmark">@lang('Dishonourable fight'): -0</span>)
            @lang('vs'). (<span class="undermark">@lang('Honourable fight'): +0</span>)
        </div>
@if ($moon_created)
        <div class="og_video">
            <video src="{{ asset('video/messages/moon-created.mp4') }}" width="655" height="380" autoplay preload="auto" controls>
                <source src="{{ asset('video/messages/moon-created.mp4') }}" type="video/mp4">
            </video>
        </div>
@endif
@if ($hamill_manoeuvre_triggered)
        <div class="fightdetails" style="margin-top: 10px; padding: 10px; background-color: rgba(255, 215, 0, 0.1); border: 2px solid #FFD700; border-radius: 5px;">
            <span class="overmark" style="font-size: 14px; font-weight: bold;">
                ⚡ HAMILL MANOEUVRE SUCCESSFUL! ⚡
            </span>
            <br>
            <span style="color: #FFD700;">
                @lang('A Light Fighter destroyed one Deathstar before the battle began!')
            </span>
        </div>
@endif
        <!-- Attacker -->
        <!-- possible classes: winner, draw, defeated -->
        <div class="combat_participant attacker winner">
            <div class="common_info">

                <span id="attacker_select_combatreport" data-member-name="{{ $attacker_name }}">
                                              <span>{{ $attacker_name }} from {{ $attacker_planet_type }} {{ $attacker_planet_name }} [{{ $attacker_planet_coords }}]</span>
                                     </span>
                <span class="participant_label {{ $attacker_class }}">@lang('Attacker'):</span>
            </div>
            <br class="clearfloat">

            <ul class="common_info fleft">
                <li class="attackerCharacterClass">@lang('Class'): {{ $attacker_character_class ?? '' }}</li>
            </ul>
            <br class="clearfloat">

            <ul class="common_info fleft">
                <li class="attackerWeapon">Weapons: {{ $attacker_weapons }}%</li>
                <li class="attackerShield">Shields: {{ $attacker_shields }}%</li>
                <li class="attackerCover">Armour: {{ $attacker_armor }}%</li>
            </ul>
            <br class="clearfloat">

            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt textCenter">
            <span class="h_battleships">@lang('Combat ships')</span>
        </span>
            </div>

            <ul class="ship_list_28 military_ships fleft">
                @foreach ($military_objects as $object)
                    <li class="{{ $loop->even ? 'odd' : '' }}">
                        <div class="buildingimg military{{ $object->id }} on">
                            <span class="detail_shipname">{{ $object->title }}</span>
                            <span class="detail_shipsleft ecke">{{ $attacker_units_start->getAmountByMachineName($object->machine_name) }}</span>
                            <span class="detail_shipslost lost_ships">0</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <br class="clearfloat">
            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt textCenter">
                    <span class="h_civilships">@lang('Civil ships')</span>
                </span>
            </div>

            <ul class="ship_list_28 military_ships fleft">
                @foreach ($civil_objects as $object)
                    <li class="{{ $loop->even ? 'odd' : '' }}">
                        <div class="buildingimg civil{{ $object->id }} on">
                            <span class="detail_shipname">{{ $object->title }}</span>
                            <span class="detail_shipsleft ecke">{{ $attacker_units_start->getAmountByMachineName($object->machine_name) }}</span>
                            <span class="detail_shipslost lost_ships">0</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <br class="clearfloat">
        </div>
        <!-- END Attacker -->


        <!-- START Defender -->
        <div class="combat_participant defender defeated">
            <div class="common_info">
                            <span id="defender_select_combatreport" data-member-name="{{ $defender_name }}">
                                            <span class="tooltip js_hideTipOnMobile" data-tooltip-title="{{ $defender_name }} from {{ $defender_planet_name }} [{{ $defender_planet_coords }}]">{{ $defender_name }}</span>
                                    </span>
                <span class="participant_label {{ $defender_class }}">@lang('Defender'):</span>
            </div>
            <br class="clearfloat">

            <ul class="common_info fleft">
                <li class="defenderCharacterClass">@lang('Class'): {{ $defender_character_class ?? '' }}</li>
            </ul>

            <br class="clearfloat">

            <ul class="common_info fleft">
                <li class="defenderWeapon">@lang('Weapons'): {{ $defender_weapons }}%</li>
                <li class="defenderShield">@lang('Shields'): {{ $defender_shields }}%</li>
                <li class="defenderCover">@lang('Armour'): {{ $defender_armor }}%</li>
                <li class="resource_list_el_small">
                    <div class="resourceIconSmall population"></div>
                    <span class="res_value tooltipCustom overmark" data-tooltip-title="0">0</span>
                </li>
            </ul>
            <br class="clearfloat">

            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt textCenter">
                <span class="h_battleships">@lang('Combat ships')</span>
            </span>
            </div>

            <ul class="ship_list_28 military_ships fleft">
                @foreach ($military_objects as $object)
                    <li class="{{ $loop->even ? 'odd' : '' }}">
                        <div class="buildingimg military{{ $object->id }} on">
                            <span class="detail_shipname">{{ $object->title }}</span>
                            <span class="detail_shipsleft ecke">{{ $defender_units_start->getAmountByMachineName($object->machine_name) }}</span>
                            <span class="detail_shipslost lost_ships">0</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            <br class="clearfloat">

            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt textCenter">
                <span class="h_civilships">@lang('Civil ships')</span>
            </span>
            </div>

            <ul class="ship_list_28 military_ships fleft">
                @foreach ($civil_objects as $object)
                    <li class="{{ $loop->even ? 'odd' : '' }}">
                        <div class="buildingimg civil{{ $object->id }} on">
                            <span class="detail_shipname">{{ $object->title }}</span>
                            <span class="detail_shipsleft ecke">{{ $defender_units_start->getAmountByMachineName($object->machine_name) }}</span>
                            <span class="detail_shipslost lost_ships">0</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <br class="clearfloat">

            <div class="section_title">
                <div class="c-left"></div>
                <div class="c-right"></div>
                <span class="title_txt textCenter">
                <span class="h_civilships">@lang('Defences:')</span>
            </span>
            </div>

            <ul class="ship_list_28 military_ships fleft">
                @foreach ($defense_objects as $object)
                    <li class="{{ $loop->even ? 'odd' : '' }}">
                        <div class="defenseimg defense{{ $object->id }} on">
                            <span class="detail_shipname">{{ $object->title }}</span>
                            <span class="detail_shipsleft ecke">{{ $defender_units_start->getAmountByMachineName($object->machine_name) }}</span>
                            <span class="detail_shipslost lost_ships">0</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <!-- END Defender -->
        <br class="clearfloat">

        <br class="clearfloat">
@if ($repaired_defenses_count > 0)
        <div class="section_title">
            <div class="c-left"></div>
            <div class="c-right"></div>
            <span class="title_txt">@lang('Repaired defences'): {{ $repaired_defenses_count }}</span>
        </div>
        <ul class="detail_list clearfix repairedDefensesContainer">
            @foreach ($repaired_defenses->units as $unit)
            <li class="detail_list_el">
                <div class="repaired_defense_icon float_left">
                    <img width="28" height="28" alt="{{ $unit->unitObject->title }}" src="{{ asset('img/objects/units/' . $unit->unitObject->assets->imgMicro) }}">
                </div>
                <span class="detail_list_txt">{{ $unit->unitObject->title }}</span>
                <span class="fright" style="margin-right: 10px">{{ $unit->amount }}</span>
            </li>
            @endforeach
        </ul>
@endif

        <!-- WF information -->
        <!-- attacker WF information -->
        <p class="detail_txt">
            The <span class="{{ $attacker_class }}">Attacker</span> fires a total of <span class="statistic_attacker hits">173</span> shots at the <span class="{{ $defender_class }}">Defender</span> with a total strength of <span class="statistic_attacker strength">67.042</span>.
            The <span class="{{ $defender_class }}">defender</span>`s shields absorb <span class="statistic_defender absorbed">275</span> points of damage.
        </p>
        <p class="detail_txt">
            The <span class="{{ $defender_class }}">Defender</span> fires a total of <span class="statistic_defender hits">5</span> shots at the <span class="{{ $attacker_class }}">Attacker</span> with a total strength of <span class="statistic_defender strength">1.145</span>.
            The <span class="{{ $attacker_class }}">attacker</span>`s shields absorb <span class="statistic_attacker absorbed">308</span> points of damage.
        </p>
        <!-- WF information END -->

    </div>

    <script type="application/javascript">
        var combatData = {
            "combatId": 8196210,
            // Start combat rounds ----------------------------------------------------------------------
            "combatRounds": [
                // Start round (static)
                {
                    "statistics":null,
                    "attackerLosses":null,
                    "defenderLosses":null,
                    "attackerShips":{
                        "4492924": {
@foreach ($attacker_units_start->units as $unit)
                            "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                        }
                    },
                    "defender":{
                    },
                    "defenderShips":[
                        {
@foreach ($defender_units_start->units as $unit)
                            "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                        }
                    ]
                },
                // Actual rounds starting from round 1.
@foreach ($rounds as $round)
                {
                    "attackerLosses": {
                        "4492924": [
@foreach ($round->attackerLosses->units as $unit)
                        {
                            "{{ $unit->unitObject->id }}": "{{ $unit->amount }}"
                        },
@endforeach
                        ]
                    },
                    "attackerLossesInThisRound": {
                        "4492924": [
@foreach ($round->attackerLossesInRound->units as $unit)
                        {
                            "{{ $unit->unitObject->id }}": "{{ $unit->amount }}"
                        },
@endforeach
                        ]
                    },
                    "defenderLosses": [
@foreach ($round->defenderLosses->units as $unit)
                        {
                            "{{ $unit->unitObject->id }}": "{{ $unit->amount }}"
                        },
@endforeach
                    ],
                    "defenderLossesInThisRound": [
@foreach ($round->defenderLossesInRound->units as $unit)
                        {
                            "{{ $unit->unitObject->id }}": "{{ $unit->amount }}"
                        },
@endforeach
                    ],
                    "statistic": {
                        "hitsAttacker": "{{ $round->hitsAttacker }}",
                        "hitsDefender": "{{ $round->hitsDefender }}",
                        "absorbedDamageAttacker": "{{ $round->absorbedDamageAttacker }}",
                        "absorbedDamageDefender": "{{ $round->absorbedDamageDefender }}",
                        "fullStrengthAttacker": "{{ $round->fullStrengthAttacker }}",
                        "fullStrengthDefender": "{{ $round->fullStrengthDefender }}"
                    },
                    "attackerShips": {
                        "4492924": {
@foreach ($round->attackerShips->units as $unit)
                         "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                        }
                    },
                    "defenderShips": [{
@foreach ($round->defenderShips->units as $unit)
                    "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                    }],
                },
@endforeach
            ],
            // End combat rounds --------------------------------------------------------------------
            "lifeformEnabled": true,
            "isExpedition": false,
            "attackerJSON": {
                "member": {
                    "4492924": {
                        "ownerName": "{{ $attacker_name }}",
                        "ownerCharacterClassId": 1,
                        "ownerCharacterClassName": "{{ $attacker_character_class ?? '' }}",
                        "ownerID": 115473,
                        "ownerCoordinates": "2:488:1",
                        "ownerPlanetType": 3,
                        "ownerHomePlanet": "Moon",
                        "planetId": 33699068,
                        "fleetID": 4492924,
                        "ownerAlliance": "PTL",
                        "ownerAllianceClassId": 0,
                        "ownerAllianceTag": "PTL",
                        "armorPercentage": {{ $attacker_armor }},
                        "weaponPercentage": {{ $attacker_weapons }},
                        "shieldPercentage": {{ $attacker_shields }},
                        "shipDetails": {
@foreach ($attacker_units_start->units as $unit)
                            "{{ $unit->unitObject->id }}": {"armor": 1160, "weapon": 140, "shield": 28, "count": {{ $unit->amount }}},
@endforeach
                        }
                    }
                },
                "combatRounds": [
                    // Start round (static)
                    {
                        "lossesInThisRound": null,
                        "statistic": {"hits": 0, "absorbedDamage": 0, "fullStrength": 0},
                        "losses": null,
                        "ships":
                        {
                            "4492924": {
@foreach ($attacker_units_start->units as $unit)
                                "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                            }
                        }
                    },
                    // Actual rounds starting from round 1.
@foreach ($rounds as $round)
                    {
                        "lossesInThisRound": {
                            "4492924": {
@foreach ($round->attackerLossesInRound->units as $unit)
                                "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                            }
                        },
                        "statistic": {
                            "hits": "{{ $round->hitsAttacker }}",
                            "absorbedDamage": "{{ $round->absorbedDamageAttacker }}",
                            "fullStrength": "{{ $round->fullStrengthAttacker }}"
                        },
                        "losses": {
                            "4492924": {
@foreach ($round->attackerLosses->units as $unit)
                                "{{ $unit->unitObject->id }}": "{{ $unit->amount }}",
@endforeach
                            }
                        },
                        "ships": {
                            "4492924": {
@foreach ($round->attackerShips->units as $unit)
                                "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                            }
                        }
                    },
@endforeach
                ]
            },
            "defenderJSON": {
                "member": [{
                    "ownerName": "{{ $defender_name }}",
                    "ownerCharacterClassId": 1,
                    "ownerCharacterClassName": "{{ $defender_character_class ?? '' }}",
                    "ownerID": 102489,
                    "ownerCoordinates": "2:3:11",
                    "ownerPlanetType": 1,
                    "ownerHomePlanet": "Destroyed Planet",
                    "planetId": 33643427,
                    "fleetID": 0,
                    "armorPercentage": {{ $defender_armor }},
                    "weaponPercentage": {{ $defender_weapons }},
                    "shieldPercentage": {{ $defender_shields }},
                    "shipDetails": {
@foreach ($defender_units_start->units as $unit)
                        "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                    }
                }],
                "combatRounds": [
                    // Start round (static)
                    {
                        "lossesInThisRound": null,
                        "statistic": {"hits": 0, "absorbedDamage": 0, "fullStrength": 0},
                        "losses": null,
                        "ships": {
@foreach ($defender_units_start->units as $unit)
                            "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                        }
                    },
                    // Actual rounds starting from round 1.
@foreach ($rounds as $round)
                    {
                        "lossesInThisRound": [{
@foreach ($round->defenderLossesInRound->units as $unit)
                                "{{ $unit->unitObject->id }}": "{{ $unit->amount }}",
@endforeach
                        }],
                        "statistic": {
                            "hits": "{{ $round->hitsDefender }}",
                            "absorbedDamage": "{{ $round->absorbedDamageDefender }}",
                            "fullStrength": "{{ $round->fullStrengthDefender }}"
                        },
                        "losses": [{
@foreach ($round->defenderLosses->units as $unit)
                                "{{ $unit->unitObject->id }}": "{{ $unit->amount }}",
@endforeach
                        }],
                        "ships": [{
@foreach ($round->defenderShips->units as $unit)
                            "{{ $unit->unitObject->id }}": {{ $unit->amount }},
@endforeach
                        }]
                    },
@endforeach
                ]
            }
        };

        var attackerJson = combatData.attackerJSON;
        var defenderJson = combatData.defenderJSON;

        ogame.messages.initCombatReportDetails();
        ogame.messages.combatreport.setCombatLoca(
            'Weapons:',
            'Shields:',
            'Armour:',
            'Class:'
        );

        ogame.messages.combatreport.initCombatText(combatData);
        ogame.messages.combatreport.loadDataBySelectedCombatMember(attackerJson, 'attacker');
        ogame.messages.combatreport.loadDataBySelectedCombatMember(defenderJson, 'defender');

        //------------------------------Attacker - Data-------------------------------
        $('.attacker .participant_select').on('change.combatreport', function () {
            var coords = $('.attacker .participant_select option:selected').data('coords') || 0;
            var type = $('.attacker .participant_select option:selected').data('planettype') || 1;
            $('.combat_round_list .round_id').find('a').removeClass("active");
            $('.combat_round_list .round_id').last().find('a').addClass("active");
            ogame.messages.combatreport.loadDataBySelectedCombatMember(attackerJson, 'attacker', coords, type);
        });

        //------------------------------Defender - Data-------------------------------
        $('.defender .participant_select').on('change.combatreport', function () {
            var coords = $('.defender .participant_select option:selected').data('coords') || 0;
            var type = $('.defender .participant_select option:selected').data('planettype') || 1;
            $('.combat_round_list .round_id').find('a').removeClass("active");
            $('.combat_round_list .round_id').last().find('a').addClass("active");
            ogame.messages.combatreport.loadDataBySelectedCombatMember(defenderJson, 'defender', coords, type);
        });

        //------------------------------Data by Round---------------------------------
        $('.combat_round_list .round_id').on('click.combatreport', function () {
            $('.combat_round_list .round_id').find('a').removeClass("active");
            $(this).find('a').addClass("active");
            ogame.messages.combatreport.loadDataBySelectedRound(attackerJson, defenderJson, $(this).data('round'));
            event.preventDefault();
        });

        initTooltips();
    </script>
</div>

<div class="commentBlock">
    <div>
        <div class="editor_wrap">
            <div><div id="markItUpMessageContent-8196210" class="markItUp"><div class="markItUpContainer"><div class="markItUpHeader"><ul class="miu_basic"><li class="markItUpButton markItUpButton1 bold"><a href="" accesskey="B" data-tooltip-title="Bold [Ctrl+B]">Bold</a></li><li class="markItUpButton markItUpButton2 italic"><a href="" accesskey="I" data-tooltip-title="Italic [Ctrl+I]">Italic</a></li><li class="markItUpButton markItUpButton3 fontColor"><a href="" data-tooltip-title="Font colour">Font colour</a></li><li class="markItUpButton markItUpButton4 fontSize markItUpDropMenu"><a href="" data-tooltip-title="Font size">Font size</a><ul class=""><li class="markItUpButton markItUpButton4-1 fontSize6"><a href="" title="">6</a></li><li class="markItUpButton markItUpButton4-2 fontSize8"><a href="" title="">8</a></li><li class="markItUpButton markItUpButton4-3 fontSize10"><a href="" title="">10</a></li><li class="markItUpButton markItUpButton4-4 fontSize12"><a href="" title="">12</a></li><li class="markItUpButton markItUpButton4-5 fontSize14"><a href="" title="">14</a></li><li class="markItUpButton markItUpButton4-6 fontSize16"><a href="" title="">16</a></li><li class="markItUpButton markItUpButton4-7 fontSize18"><a href="" title="">18</a></li><li class="markItUpButton markItUpButton4-8 fontSize20"><a href="" title="">20</a></li><li class="markItUpButton markItUpButton4-9 fontSize22"><a href="" title="">22</a></li><li class="markItUpButton markItUpButton4-10 fontSize24"><a href="" title="">24</a></li><li class="markItUpButton markItUpButton4-11 fontSize26"><a href="" title="">26</a></li><li class="markItUpButton markItUpButton4-12 fontSize28"><a href="" title="">28</a></li><li class="markItUpButton markItUpButton4-13 fontSize30"><a href="" title="">30</a></li></ul><span class="dropdown_arr"></span></li><li class="markItUpButton markItUpButton5 list"><a href="" data-tooltip-title="List">List</a></li><li class="markItUpButton markItUpButton6 coordinates"><a href="" data-tooltip-title="Coordinates">Coordinates</a></li><li class="txt_link fright li_miu_advanced"><span class="toggle_miu_advanced show_miu_advanced awesome-button" role="button">More Options</span></li></ul><ul class="miu_advanced" style="display: none;"><li class="markItUpButton markItUpButton1 underline"><a href="" accesskey="U" data-tooltip-title="Underline [Ctrl+U]">Underline</a></li><li class="markItUpButton markItUpButton2 strikeThrough"><a href="" accesskey="S" data-tooltip-title="Strikethrough [Ctrl+S]">Strikethrough</a></li><li class="markItUpButton markItUpButton3 sub"><a href="" data-tooltip-title="Subscript">Subscript</a></li><li class="markItUpButton markItUpButton4 sup"><a href="" data-tooltip-title="Superscript">Superscript</a></li><li class="markItUpSeparator">-</li><li class="markItUpButton markItUpButton5 item markItUpDropMenu"><a href="" data-tooltip-title="Item">Item</a><ul class=""><li class="markItUpButton markItUpButton5-1 "><a href="" title="">Researchers</a></li><li class="markItUpButton markItUpButton5-2 "><a href="" title="">Traders</a></li><li class="markItUpButton markItUpButton5-3 "><a href="" title="">Warriors</a></li><li class="markItUpButton markItUpButton5-4 "><a href="" title="">Bronze Crystal Booster</a></li><li class="markItUpButton markItUpButton5-5 "><a href="" title="">Bronze Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-6 "><a href="" title="">Bronze Metal Booster</a></li><li class="markItUpButton markItUpButton5-7 "><a href="" title="">Discoverer</a></li><li class="markItUpButton markItUpButton5-8 "><a href="" title="">Collector</a></li><li class="markItUpButton markItUpButton5-9 "><a href="" title="">General</a></li><li class="markItUpButton markItUpButton5-10 "><a href="" title="">Bronze Crystal Booster</a></li><li class="markItUpButton markItUpButton5-11 "><a href="" title="">Bronze Crystal Booster</a></li><li class="markItUpButton markItUpButton5-12 "><a href="" title="">Bronze Crystal Booster</a></li><li class="markItUpButton markItUpButton5-13 "><a href="" title="">Silver Crystal Booster</a></li><li class="markItUpButton markItUpButton5-14 "><a href="" title="">Silver Crystal Booster</a></li><li class="markItUpButton markItUpButton5-15 "><a href="" title="">Silver Crystal Booster</a></li><li class="markItUpButton markItUpButton5-16 "><a href="" title="">Gold Crystal Booster</a></li><li class="markItUpButton markItUpButton5-17 "><a href="" title="">Gold Crystal Booster</a></li><li class="markItUpButton markItUpButton5-18 "><a href="" title="">Gold Crystal Booster</a></li><li class="markItUpButton markItUpButton5-19 "><a href="" title="">Platinum Crystal Booster</a></li><li class="markItUpButton markItUpButton5-20 "><a href="" title="">Platinum Crystal Booster</a></li><li class="markItUpButton markItUpButton5-21 "><a href="" title="">Platinum Crystal Booster</a></li><li class="markItUpButton markItUpButton5-22 "><a href="" title="">DETROID Bronze</a></li><li class="markItUpButton markItUpButton5-23 "><a href="" title="">DETROID Gold</a></li><li class="markItUpButton markItUpButton5-24 "><a href="" title="">DETROID Platinum</a></li><li class="markItUpButton markItUpButton5-25 "><a href="" title="">DETROID Silver</a></li><li class="markItUpButton markItUpButton5-26 "><a href="" title="">Bronze Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-27 "><a href="" title="">Bronze Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-28 "><a href="" title="">Bronze Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-29 "><a href="" title="">Silver Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-30 "><a href="" title="">Silver Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-31 "><a href="" title="">Silver Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-32 "><a href="" title="">Gold Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-33 "><a href="" title="">Gold Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-34 "><a href="" title="">Gold Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-35 "><a href="" title="">Platinum Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-36 "><a href="" title="">Platinum Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-37 "><a href="" title="">Platinum Deuterium Booster</a></li><li class="markItUpButton markItUpButton5-38 "><a href="" title="">Energy Booster Bronze</a></li><li class="markItUpButton markItUpButton5-39 "><a href="" title="">Energy Booster Bronze</a></li><li class="markItUpButton markItUpButton5-40 "><a href="" title="">Energy Booster Bronze</a></li><li class="markItUpButton markItUpButton5-41 "><a href="" title="">Energy Booster Silver</a></li><li class="markItUpButton markItUpButton5-42 "><a href="" title="">Energy Booster Silver</a></li><li class="markItUpButton markItUpButton5-43 "><a href="" title="">Energy Booster Silver</a></li><li class="markItUpButton markItUpButton5-44 "><a href="" title="">Energy Booster Gold</a></li><li class="markItUpButton markItUpButton5-45 "><a href="" title="">Energy Booster Gold</a></li><li class="markItUpButton markItUpButton5-46 "><a href="" title="">Energy Booster Gold</a></li><li class="markItUpButton markItUpButton5-47 "><a href="" title="">Energy Booster Platinum</a></li><li class="markItUpButton markItUpButton5-48 "><a href="" title="">Energy Booster Platinum</a></li><li class="markItUpButton markItUpButton5-49 "><a href="" title="">Energy Booster Platinum</a></li><li class="markItUpButton markItUpButton5-50 "><a href="" title="">Bronze Expedition Slots</a></li><li class="markItUpButton markItUpButton5-51 "><a href="" title="">Bronze Expedition Slots</a></li><li class="markItUpButton markItUpButton5-52 "><a href="" title="">Bronze Expedition Slots</a></li><li class="markItUpButton markItUpButton5-53 "><a href="" title="">Silver Expedition Slots</a></li><li class="markItUpButton markItUpButton5-54 "><a href="" title="">Silver Expedition Slots</a></li><li class="markItUpButton markItUpButton5-55 "><a href="" title="">Silver Expedition Slots</a></li><li class="markItUpButton markItUpButton5-56 "><a href="" title="">Gold Expedition Slots</a></li><li class="markItUpButton markItUpButton5-57 "><a href="" title="">Gold Expedition Slots</a></li><li class="markItUpButton markItUpButton5-58 "><a href="" title="">Gold Expedition Slots</a></li><li class="markItUpButton markItUpButton5-59 "><a href="" title="">Bronze Fleet Slots</a></li><li class="markItUpButton markItUpButton5-60 "><a href="" title="">Bronze Fleet Slots</a></li><li class="markItUpButton markItUpButton5-61 "><a href="" title="">Bronze Fleet Slots</a></li><li class="markItUpButton markItUpButton5-62 "><a href="" title="">Silver Fleet Slots</a></li><li class="markItUpButton markItUpButton5-63 "><a href="" title="">Silver Fleet Slots</a></li><li class="markItUpButton markItUpButton5-64 "><a href="" title="">Silver Fleet Slots</a></li><li class="markItUpButton markItUpButton5-65 "><a href="" title="">Gold Fleet Slots</a></li><li class="markItUpButton markItUpButton5-66 "><a href="" title="">Gold Fleet Slots</a></li><li class="markItUpButton markItUpButton5-67 "><a href="" title="">Gold Fleet Slots</a></li><li class="markItUpButton markItUpButton5-68 "><a href="" title="">KRAKEN Bronze</a></li><li class="markItUpButton markItUpButton5-69 "><a href="" title="">KRAKEN Gold</a></li><li class="markItUpButton markItUpButton5-70 "><a href="" title="">KRAKEN Platinum (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-71 "><a href="" title="">KRAKEN Bronze (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-72 "><a href="" title="">KRAKEN Gold (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-73 "><a href="" title="">KRAKEN Silver (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-74 "><a href="" title="">KRAKEN Platinum</a></li><li class="markItUpButton markItUpButton5-75 "><a href="" title="">KRAKEN Silver</a></li><li class="markItUpButton markItUpButton5-76 "><a href="" title="">Bronze Metal Booster</a></li><li class="markItUpButton markItUpButton5-77 "><a href="" title="">Bronze Metal Booster</a></li><li class="markItUpButton markItUpButton5-78 "><a href="" title="">Bronze Metal Booster</a></li><li class="markItUpButton markItUpButton5-79 "><a href="" title="">Silver Metal Booster</a></li><li class="markItUpButton markItUpButton5-80 "><a href="" title="">Silver Metal Booster</a></li><li class="markItUpButton markItUpButton5-81 "><a href="" title="">Silver Metal Booster</a></li><li class="markItUpButton markItUpButton5-82 "><a href="" title="">Gold Metal Booster</a></li><li class="markItUpButton markItUpButton5-83 "><a href="" title="">Gold Metal Booster</a></li><li class="markItUpButton markItUpButton5-84 "><a href="" title="">Gold Metal Booster</a></li><li class="markItUpButton markItUpButton5-85 "><a href="" title="">Platinum Metal Booster</a></li><li class="markItUpButton markItUpButton5-86 "><a href="" title="">Platinum Metal Booster</a></li><li class="markItUpButton markItUpButton5-87 "><a href="" title="">Platinum Metal Booster</a></li><li class="markItUpButton markItUpButton5-88 "><a href="" title="">Bronze Moon Fields</a></li><li class="markItUpButton markItUpButton5-89 "><a href="" title="">Gold Moon Fields</a></li><li class="markItUpButton markItUpButton5-90 "><a href="" title="">Platinum Moon Fields</a></li><li class="markItUpButton markItUpButton5-91 "><a href="" title="">Silver Moon Fields</a></li><li class="markItUpButton markItUpButton5-92 "><a href="" title="">Bronze M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-93 "><a href="" title="">Bronze M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-94 "><a href="" title="">Gold M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-95 "><a href="" title="">Gold M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-96 "><a href="" title="">Silver M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-97 "><a href="" title="">Silver M.O.O.N.S.</a></li><li class="markItUpButton markItUpButton5-98 "><a href="" title="">NEWTRON Bronze</a></li><li class="markItUpButton markItUpButton5-99 "><a href="" title="">NEWTRON Gold</a></li><li class="markItUpButton markItUpButton5-100 "><a href="" title="">NEWTRON Bronze (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-101 "><a href="" title="">NEWTRON Gold (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-102 "><a href="" title="">NEWTRON Platinum (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-103 "><a href="" title="">NEWTRON Silver (Lifeforms)</a></li><li class="markItUpButton markItUpButton5-104 "><a href="" title="">NEWTRON Platinum</a></li><li class="markItUpButton markItUpButton5-105 "><a href="" title="">NEWTRON Silver</a></li><li class="markItUpButton markItUpButton5-106 "><a href="" title="">Bronze Planet Fields</a></li><li class="markItUpButton markItUpButton5-107 "><a href="" title="">Gold Planet Fields</a></li><li class="markItUpButton markItUpButton5-108 "><a href="" title="">Platinum Planet Fields</a></li><li class="markItUpButton markItUpButton5-109 "><a href="" title="">Silver Planet Fields</a></li><li class="markItUpButton markItUpButton5-110 "><a href="" title="">Complete Resource Package</a></li><li class="markItUpButton markItUpButton5-111 "><a href="" title="">Crystal Package</a></li><li class="markItUpButton markItUpButton5-112 "><a href="" title="">Deuterium Package</a></li><li class="markItUpButton markItUpButton5-113 "><a href="" title="">Metal Package</a></li></ul><span class="dropdown_arr"></span></li><li class="markItUpButton markItUpButton6 player"><a href="" data-tooltip-title="Player">Player</a></li><li class="markItUpSeparator">-</li><li class="markItUpButton markItUpButton7 leftAlign"><a href="" data-tooltip-title="Left align">Left align</a></li><li class="markItUpButton markItUpButton8 centerAlign"><a href="" data-tooltip-title="Centre align">Centre align</a></li><li class="markItUpButton markItUpButton9 rightAlign"><a href="" data-tooltip-title="Right align">Right align</a></li><li class="markItUpButton markItUpButton10 justifyAlign"><a href="" data-tooltip-title="Justify">Justify</a></li><li class="markItUpSeparator">-</li><li class="markItUpButton markItUpButton11 code"><a href="" data-tooltip-title="Code">Code</a></li><li class="markItUpSeparator">-</li><li class="markItUpButton markItUpButton12 email"><a href="" accesskey="E" data-tooltip-title="Email [Ctrl+E]">Email</a></li><li class="markItUpButton markItUpButton13 preview" style="display: none;"><a href="" data-tooltip-title="Preview">Preview</a></li></ul></div><textarea name="text" class="new_msg_textarea markItUpEditor" id="messageContent-8196210"></textarea><div class="miuFooter"><div><span class="cnt_chars">2000</span> Characters remaining</div><gradient-button class="sendComment" w70="" h28=""><button class="custom_btn preview_link" onclick="return false" data-target="" aria-label="Preview">Preview</button></gradient-button></div><div class="miu_preview_container" style="display: none;">

                            <script type="text/javascript">
                                initBBCodes();
                            </script></div><input type="hidden" class="colorpicker"><div class="markItUpFooter"></div></div></div></div>
        </div>
    </div>
    <script type="application/javascript">
        initBBCodeEditor(locaKeys, itemNames, false, '.new_msg_textarea', 2000, true);
    </script>
</div>

<div class="commentsHolder">
</div>
</div>