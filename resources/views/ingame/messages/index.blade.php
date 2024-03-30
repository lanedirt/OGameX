@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="content"><div id="buttonz">
            <div class="header">
                <h2>News feed</h2>
            </div>
            <div class="content">
                <div class="js_tabs tabs_wrap ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <ul class="tabs_btn ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                        <li id="tabs-nfFleets" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" data-tabid="2" role="tab" tabindex="0" aria-controls="ui-id-2" aria-labelledby="ui-id-1" aria-selected="true" aria-expanded="true">
                            <a href="index.php?page=messages&amp;tab=2&amp;ajax=1" class="tabs_btn_img tb_fleets ui-tabs-anchor" rel="index.php?page=messages&amp;tab=2&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-1">

                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Fleets</span>
                            </a>
                        </li>
                        <li id="tabs-nfCommunication" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="1" role="tab" tabindex="-1" aria-controls="ui-id-4" aria-labelledby="ui-id-3" aria-selected="false" aria-expanded="false">
                            <a href="index.php?page=messages&amp;tab=1&amp;ajax=1" class="tabs_btn_img tb_communication ui-tabs-anchor" rel="index.php?page=messages&amp;tab=1&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-3">
                                <span class="new_msg_count" style="display: none;">0</span>
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Communication</span>
                            </a>
                        </li>
                        <li id="tabs-nfEconomy" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="3" role="tab" tabindex="-1" aria-controls="ui-id-6" aria-labelledby="ui-id-5" aria-selected="false" aria-expanded="false">
                            <a href="index.php?page=messages&amp;tab=3&amp;ajax=1" class="tabs_btn_img tb_economy ui-tabs-anchor" rel="index.php?page=messages&amp;tab=3&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-5">
                                <span class="new_msg_count" style="display: none;">0</span>
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Economy</span>
                            </a>
                        </li>
                        <li id="tabs-nfUniverse" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="5" role="tab" tabindex="-1" aria-controls="ui-id-8" aria-labelledby="ui-id-7" aria-selected="false" aria-expanded="false">
                            <a href="index.php?page=messages&amp;tab=5&amp;ajax=1" class="tabs_btn_img tb_universe ui-tabs-anchor" rel="index.php?page=messages&amp;tab=5&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-7">
                                <span class="new_msg_count" style="display: none;">0</span>
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Universe</span>
                            </a>
                        </li>
                        <li id="tabs-nfSystem" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="4" role="tab" tabindex="-1" aria-controls="ui-id-10" aria-labelledby="ui-id-9" aria-selected="false" aria-expanded="false">
                            <a href="index.php?page=messages&amp;tab=4&amp;ajax=1" class="tabs_btn_img tb_system ui-tabs-anchor" rel="index.php?page=messages&amp;tab=4&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-9">
                                <span class="new_msg_count" style="display: none;">0</span>
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">OGame</span>
                            </a>
                        </li>
                        <li id="tabs-nfFavorites" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="6" role="tab" tabindex="-1" aria-controls="ui-id-12" aria-labelledby="ui-id-11" aria-selected="false" aria-expanded="false">
                            <a href="index.php?page=messages&amp;tab=6&amp;ajax=1" class="tabs_btn_img tb_favorites premiumHighligt ui-tabs-anchor" rel="index.php?page=messages&amp;tab=6&amp;ajax=1" role="presentation" tabindex="-1" id="ui-id-11">
                                <span class="new_msg_count" style="display: none;">0</span>
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Favourites</span>
                            </a>
                        </li>
                    </ul><div id="ui-id-2" aria-live="polite" aria-labelledby="ui-id-1" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false"><div id="fleetsTab"><div class="tab_ctn">
                                <div class="js_subtabs_fleets ui-tabs ui-corner-all ui-widget ui-widget-content">
                                    <ul class="subtabs ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                                        <li id="subtabs-nfFleet20" class="list_item first ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" data-subtabname="Espionage" data-tabid="20" role="tab" tabindex="0" aria-controls="ui-id-14" aria-labelledby="ui-id-13" aria-selected="true" aria-expanded="true">
                                            <a href="index.php?page=messages&amp;tab=20&amp;ajax=1" class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-13">
                                                Espionage

                                            </a>
                                        </li>
                                        <li id="subtabs-nfFleet21" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-subtabname="Combat Reports" data-tabid="21" role="tab" tabindex="-1" aria-controls="ui-id-16" aria-labelledby="ui-id-15" aria-selected="false" aria-expanded="false">
                                            <a href="index.php?page=messages&amp;tab=21&amp;ajax=1" class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-15">
                                                Combat Reports

                                            </a>
                                        </li>
                                        <li id="subtabs-nfFleet22" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-subtabname="Expeditions" data-tabid="22" role="tab" tabindex="-1" aria-controls="ui-id-18" aria-labelledby="ui-id-17" aria-selected="false" aria-expanded="false">
                                            <a href="index.php?page=messages&amp;tab=22&amp;ajax=1" class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-17">
                                                Expeditions

                                            </a>
                                        </li>
                                        <li id="subtabs-nfFleet23" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-subtabname="Unions/Transport" data-tabid="23" role="tab" tabindex="-1" aria-controls="ui-id-20" aria-labelledby="ui-id-19" aria-selected="false" aria-expanded="false">
                                            <a href="index.php?page=messages&amp;tab=23&amp;ajax=1" class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-19">
                                                Unions/Transport
                                                <span></span>
                                            </a>
                                        </li>
                                        <li id="subtabs-nfFleet24" class="list_item last ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-subtabname="Other" data-tabid="24" role="tab" tabindex="-1" aria-controls="ui-id-22" aria-labelledby="ui-id-21" aria-selected="false" aria-expanded="false">
                                            <a href="index.php?page=messages&amp;tab=24&amp;ajax=1" class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-21">
                                                Other
                                                <span></span>
                                            </a>
                                        </li>
                                        <li id="subtabs-nfFleetTrash" class="trash_tab ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-subtabname="Trash" data-tabid="25" role="tab" tabindex="-1" aria-controls="ui-id-24" aria-labelledby="ui-id-23" aria-selected="false" aria-expanded="false">
                                            <div class="trash_box">
                                                <h3 class="trash_title in_trash" style="display: none;">Trash</h3>
                                                <span class="not_in_trash"><span class="trash_action js_actionKillAll">Delete</span></span><span class="in_trash" style="display: none;"><span class="trash_action js_actionReviveAll">restore</span> or <span class="trash_action js_actionDeleteAll">delete permanently</span></span> all in tab <span class="js_active_tab">Espionage</span>.
                                            </div>
                                            <a href="index.php?page=messages&amp;tab=25&amp;ajax=1" class="btn_blue btn_trash ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-23">
                                                Trash
                                                <span></span>
                                            </a>
                                        </li>
                                    </ul><div id="ui-id-14" aria-live="polite" aria-labelledby="ui-id-13" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false" style=""><div id="fleetsgenericpage"><ul class="tab_inner ctn_with_trash clearfix">
                                                <ul class="pagination"><li class="paginator" data-tab="20" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="20" data-page="1">&lt;</li><li class="curPage" data-tab="20">1/1</li><li class="paginator" data-tab="20" data-page="1">&gt;</li><li class="paginator" data-tab="20" data-page="1">&gt;&gt;|</li></ul>
                                                <input type="hidden" name="token" value="d99f68937305e0b2c3ff3f059259fcec">
                                                <li class="msg " data-msg-id="81049">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=8&amp;position=12" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Ply [2:8:12]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 14:50:38</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_honorableTarget">&nbsp;&nbsp;Kapton Tape</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright">Activity: &gt;60 minutes ago.</span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;Collector</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 38,429</span><span class="resspan">Crystal: 38,953</span><span class="resspan">Deuterium: 13,056</span><br><span class="resspan">Food: 161</span><span class="resspan">Population: 1,388</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 67,828<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1&amp;am202=11&quot;>S.Cargo: 11</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1&amp;am203=3&quot;>L.Cargo: 3</a><br/>">Resources: 90,599</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 0%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">Fleets: 0</span>  <span class="ctn ctn4 fright tooltipRight" title="0">Defense: 0</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="81049"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50' readonly onclick='select()' style='width:360px'></input>" data-message-id="81049"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50');" data-message-id="81049"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="81049" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=81049"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="81049"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;" data-message-id="81049"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=81049&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="81002">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=7&amp;position=12" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>FR [2:7:12]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 14:49:22</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_honorableTarget">&nbsp;&nbsp;FrameIT</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright"><span style="color:red">Activity: &lt;15 minutes ago.</span></span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;Collector</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 68,493</span><span class="resspan">Crystal: 34,230</span><span class="resspan">Deuterium: 1,286</span><br><span class="resspan">Food: 10</span><span class="resspan">Population: 110</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 78,006<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1&amp;am202=13&quot;>S.Cargo: 13</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1&amp;am203=3&quot;>L.Cargo: 3</a><br/>">Resources: 104,019</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 0%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">Fleets: 0</span>  <span class="ctn ctn4 fright tooltipRight" title="0">Defense: 0</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="81002"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-029d2c32b71dd3527ca16df57ad50d374e8b19cc' readonly onclick='select()' style='width:360px'></input>" data-message-id="81002"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-029d2c32b71dd3527ca16df57ad50d374e8b19cc');" data-message-id="81002"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="81002" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=81002"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="81002"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,7,12,1,2); return false;" data-message-id="81002"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=81002&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="80152">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage action on <a href="#galaxy&amp;galaxy=2&amp;system=3&amp;position=6" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>BirbTown [2:3:6]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 14:23:44</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Space Monitoring</span>
                                                    </div>
                                                    <span class="msg_content">
        <span class="espionageDefText">A foreign fleet from planet <a href="#galaxy&amp;galaxy=2&amp;system=1&amp;position=6" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Zara [2:1:6]</a> (<span title="Player: Et58|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#galaxy&amp;galaxy=2&amp;system=1&quot;>[2:1:6]</a></td></tr><tr><th>Actions</th><td><a href=&quot;javascript:void(0)&quot;
                     class=&quot;sendMail js_openChat tooltip&quot;
                     data-playerId=&quot;102480&quot;
                     title=&quot;Write message&quot;><span class=&quot;icon icon_chat&quot;></span></a><a title=&quot;Buddy request&quot; href=&quot;#buddies&amp;action=7&amp;id=102480&amp;ajax=1&quot; class=&quot;overlay tooltip&quot;><span class=&quot;icon icon_user&quot;></span></a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#highscore&amp;searchRelId=102480&amp;category=1&quot;>527</a></td></tr></table>" class="tooltipHTML tooltipCustom player advice">Et58</span>) was sighted near your planet <a href="#galaxy&amp;galaxy=2&amp;system=3&amp;position=6" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>BirbTown [2:3:6]</a>.<br>
Chance of counter-espionage: 0%</span>
    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="80152"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="80152" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=80152"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=6&amp;type=1&amp;mission=1';" data-message-id="80152"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,1,6,1,2); return false;" data-message-id="80152"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="74538">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=7&amp;position=12" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>FR [2:7:12]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 11:17:19</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_honorableTarget">&nbsp;&nbsp;FrameIT</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright">Activity: &gt;60 minutes ago.</span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;Collector</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 68,493</span><span class="resspan">Crystal: 38,371</span><span class="resspan">Deuterium: 5,698</span><br><span class="resspan">Food: 10</span><span class="resspan">Population: 110</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 84,421<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1&amp;am202=14&quot;>S.Cargo: 14</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1&amp;am203=3&quot;>L.Cargo: 3</a><br/>">Resources: 112,572</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 0%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">Fleets: 0</span>  <span class="ctn ctn4 fright tooltipRight" title="0">Defense: 0</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="74538"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-fcdad7eea72e24833aa74fe041f3653edf9e9119' readonly onclick='select()' style='width:360px'></input>" data-message-id="74538"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-fcdad7eea72e24833aa74fe041f3653edf9e9119');" data-message-id="74538"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="74538" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=74538"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="74538"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,7,12,1,2); return false;" data-message-id="74538"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=74538&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="73350">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=6&amp;position=8" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Earth [2:6:8]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 10:37:45</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_outlaw">&nbsp;&nbsp;PreziEvil</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright"><span style="color:red">Activity: &lt;15 minutes ago.</span></span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;Collector</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 55,606</span><span class="resspan">Crystal: 237</span><span class="resspan">Deuterium: 8,888</span><br><span class="resspan">Food: 10</span><span class="resspan">Population: 110</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 48,548<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=6&amp;position=8&amp;type=1&amp;mission=1&amp;am202=8&quot;>S.Cargo: 8</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=6&amp;position=8&amp;type=1&amp;mission=1&amp;am203=2&quot;>L.Cargo: 2</a><br/>">Resources: 64,741</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 1%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 24,000">Fleets: 24,000</span>  <span class="ctn ctn4 fright tooltipRight" title="0">Defense: 0</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="73350"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-66a7720ee4b3e2c7abd51623d5c2aeeae55600e7' readonly onclick='select()' style='width:360px'></input>" data-message-id="73350"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-66a7720ee4b3e2c7abd51623d5c2aeeae55600e7');" data-message-id="73350"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="73350" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=73350"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=6&amp;position=8&amp;type=1&amp;mission=1';" data-message-id="73350"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,6,8,1,2); return false;" data-message-id="73350"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=73350&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="73281">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=1&amp;position=10" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Homeworld [2:1:10]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 10:35:26</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_honorableTarget">&nbsp;&nbsp;Commander Callisto</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright"><span style="color:red">Activity: &lt;15 minutes ago.</span></span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;No class selected</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 0</span><span class="resspan">Crystal: 0</span><span class="resspan">Deuterium: 989</span><br><span class="resspan">Food: 1,301</span><span class="resspan">Population: 48,109</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 741<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1&amp;am202=1&quot;>S.Cargo: 1</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1&amp;am203=1&quot;>L.Cargo: 1</a><br/>">Resources: 2,290</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 0%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">Fleets: 0</span>  <span class="ctn ctn4 fright tooltipRight" title="34,000">Defense: 34,000</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="73281"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-672d9ace8280423493be6e84df3999ad0fe7dc41' readonly onclick='select()' style='width:360px'></input>" data-message-id="73281"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-672d9ace8280423493be6e84df3999ad0fe7dc41');" data-message-id="73281"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="73281" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=73281"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1';" data-message-id="73281"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,1,10,1,2); return false;" data-message-id="73281"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=73281&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="70963">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Espionage report from <a href="#galaxy&amp;galaxy=2&amp;system=1&amp;position=10" class="txt_link"><figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure>Homeworld [2:1:10]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 09:01:49</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <br>
<br>
<div class="compacting"><span class="ctn ctn4">Player:</span><span class="status_abbr_honorableTarget">&nbsp;&nbsp;Commander Callisto</span><span class="status_abbr_honorableTarget">(<span class="status_abbr_honorableTarget"><span class="status_abbr_honorableTarget tooltipHTML" title="Honourable target|In battle against this target you can receive honour points and plunder 50% more loot.">hp</span></span>)</span>&nbsp;<span class="ctn ctn4 fright">Activity: &gt;60 minutes ago.</span></div><div class="compacting"><span class="ctn ctn4">Class:</span>&nbsp;No class selected</div><div class="compacting"><span class="ctn ctn4">Alliance Class:</span>&nbsp;<span class="alliance_class small none">No alliance class selected</span></div><br>
<div class="compacting"><span class="ctn ctn4"><span class="resspan">Metal: 36,505</span><span class="resspan">Crystal: 18,035</span><span class="resspan">Deuterium: 7,758</span><br><span class="resspan">Food: 1,301</span><span class="resspan">Population: 46,622</span></span><span class="ctn ctn4 fright tooltipRight tooltipClose" title="Loot: 46,723<br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1&amp;am202=8&quot;>S.Cargo: 8</a><br/><a href=&quot;#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1&amp;am203=2&quot;>L.Cargo: 2</a><br/>">Resources: 63,599</span></div><div class="compacting"><span class="ctn ctn4">Loot: 75%</span><span class="fright">Chance of counter-espionage: 0%</span></div><div class="compacting"> <span class="ctn ctn4 tooltipLeft" title="Fleets: 0">Fleets: 0</span>  <span class="ctn ctn4 fright tooltipRight" title="0">Defense: 0</span></div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="70963"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-0b3a7babcd1b0a86808e46966a9ecbf6ecf89525' readonly onclick='select()' style='width:360px'></input>" data-message-id="70963"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>                <gradient-button sq30=""><button class="custom_btn tooltip msgCombatSimBtn" title="Open in Combat Simulator" onclick="window.open('#combatsim&amp;reportHash=sr-en-255-0b3a7babcd1b0a86808e46966a9ecbf6ecf89525');" data-message-id="70963"><img src="/img/icons/speed.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="70963" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=70963"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1';" data-message-id="70963"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,1,10,1,2); return false;" data-message-id="70963"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=70963&amp;tabid=20&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <ul class="pagination"><li class="paginator" data-tab="20" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="20" data-page="1">&lt;</li><li class="curPage" data-tab="20">1/1</li><li class="paginator" data-tab="20" data-page="1">&gt;</li><li class="paginator" data-tab="20" data-page="1">&gt;&gt;|</li></ul>
                                            </ul>
                                            <script type="text/javascript">
                                                var activeTabid = $('.ui-tabs-active a').attr('id'); //erster tab als default
                                                var hasSubtabs = $('div[aria-labelledby="' + activeTabid + '"] .tab_ctn div ul.subtabs').length;
                                                var activeSubtabid = '';

                                                $('.ui-tabs-active a').each(function(){
                                                    activeSubtabid = $(this).attr('id');
                                                });

                                                var msgids = [];
                                                var index = 0;

                                                if (hasSubtabs > 0) {
                                                    $('div[aria-labelledby="' + activeSubtabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                } else {
                                                    $('div[aria-labelledby="' + activeTabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                }

                                                msgids = JSON.stringify(msgids);

                                                // TODO: re-enable when working on the messages feature.
                                                if (1===3) {
                                                    var msgcountUrl = "#ajaxMessageCount";
                                                    var playerid = parseInt(102489);
                                                    var action = 111;

                                                    $.ajax({
                                                        url: msgcountUrl,
                                                        type: 'POST',
                                                        data: {
                                                            player: playerid,
                                                            action: action,
                                                            newMessageIds: msgids,
                                                            ajax: 1
                                                        },
                                                        success: function (data) {
                                                            var message_menu_count = $('.comm_menu.messages span.new_msg_count');
                                                            var message_tab_count = $('.ui-tabs-active .new_msg_count');

                                                            if (message_menu_count.length > 0 && message_tab_count.length > 0) {
                                                                var menuCount = parseInt(message_menu_count[0].innerHTML);
                                                                var tabCount = parseInt(message_tab_count[0].innerHTML);
                                                                var newCount = menuCount - tabCount;

                                                                if (newCount > 0) {
                                                                    message_menu_count.val(newCount);
                                                                } else {
                                                                    message_menu_count.remove();
                                                                }
                                                            }

                                                            $('.ui-tabs-active .new_msg_count').remove();

                                                            if (hasSubtabs > 0) {
                                                                $('.ui-tabs-active a span:not(.icon_caption)').remove();
                                                            }
                                                        },
                                                        error: function (jqXHR, textStatus, errorThrown) {
                                                        }
                                                    });
                                                }
                                            </script></div></div><div id="ui-id-16" aria-live="polite" aria-labelledby="ui-id-15" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"><div id="fleetsgenericpage"><ul class="tab_inner ctn_with_trash clearfix">
                                                <ul class="pagination"><li class="paginator" data-tab="21" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="21" data-page="1">&lt;</li><li class="curPage" data-tab="21">1/1</li><li class="paginator" data-tab="21" data-page="1">&gt;</li><li class="paginator" data-tab="21" data-page="1">&gt;&gt;|</li></ul>
                                                <input type="hidden" name="token" value="d99f68937305e0b2c3ff3f059259fcec">
                                                <li class="msg " data-msg-id="87194">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt"><span class="overmark">Combat Report Ply <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure> <a href="#galaxy&amp;galaxy=2&amp;system=8&amp;position=12" class="txt_link">[2:8:12]</a></span></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 17:41:03</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <div class="combatLeftSide"><br>
    <span class="msg_ctn msg_ctn2 overmark tooltipLeft" title="16,000">Attacker: (Lieutenant Cupid): 16,000</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="Resources<br/>Metal: 0<br/>Crystal: 0<br/>Deuterium: 0<br/>Food: 0">Resources: 0, Loot: 100%</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="8,000">Debris field (newly created): 8,000</span><br>
</div><div class="combatRightSide"><br>
    <span class="msg_ctn msg_ctn2 undermark tooltipRight" title="0">Defender: (Kapton Tape): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipRight" title="0">Actually repaired: 0</span><br>
            <span class="msg_ctn msg_ct3 ">Moon Chance: 0 %</span><br>
    </div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="87194"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-806813cc1b8da4c600836a297e10ae7c6d4dcdf1' readonly onclick='select()' style='width:360px'></input>" data-message-id="87194"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="87194" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=87194"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="87194"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;" data-message-id="87194"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=87194&amp;tabid=21&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="84479">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt"><span class="undermark">Combat Report Ply <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure> <a href="#galaxy&amp;galaxy=2&amp;system=8&amp;position=12" class="txt_link">[2:8:12]</a></span></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 16:29:22</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <div class="combatLeftSide"><br>
    <span class="msg_ctn msg_ctn2 undermark tooltipLeft" title="0">Attacker: (Lieutenant Cupid): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="Resources<br/>Metal: 9,967<br/>Crystal: 9,968<br/>Deuterium: 4,945<br/>Food: 120">Resources: 25,000, Loot: 75%</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="0">Debris field (newly created): 0</span><br>
</div><div class="combatRightSide"><br>
    <span class="msg_ctn msg_ctn2 overmark tooltipRight" title="0">Defender: (Kapton Tape): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipRight" title="0">Actually repaired: 0</span><br>
            <span class="msg_ctn msg_ct3 ">Moon Chance: 0 %</span><br>
    </div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="84479"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-d64f51ee62b8f2714d0c44a8203669cdcf7f5eab' readonly onclick='select()' style='width:360px'></input>" data-message-id="84479"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="84479" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=84479"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="84479"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;" data-message-id="84479"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=84479&amp;tabid=21&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="82065">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt"><span class="undermark">Combat Report Ply <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure> <a href="#galaxy&amp;galaxy=2&amp;system=8&amp;position=12" class="txt_link">[2:8:12]</a></span></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 15:23:41</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <div class="combatLeftSide"><br>
    <span class="msg_ctn msg_ctn2 undermark tooltipLeft" title="0">Attacker: (Lieutenant Cupid): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="Resources<br/>Metal: 8,293<br/>Crystal: 8,293<br/>Deuterium: 8,294<br/>Food: 120">Resources: 25,000, Loot: 75%</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="0">Debris field (newly created): 0</span><br>
</div><div class="combatRightSide"><br>
    <span class="msg_ctn msg_ctn2 overmark tooltipRight" title="0">Defender: (Kapton Tape): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipRight" title="0">Actually repaired: 0</span><br>
            <span class="msg_ctn msg_ct3 ">Moon Chance: 0 %</span><br>
    </div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="82065"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-1d47fab8ab3464d60ec068249f12466cbdfcde46' readonly onclick='select()' style='width:360px'></input>" data-message-id="82065"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="82065" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=82065"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="82065"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;" data-message-id="82065"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=82065&amp;tabid=21&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="75463">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt"><span class="undermark">Combat Report FR <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure> <a href="#galaxy&amp;galaxy=2&amp;system=7&amp;position=12" class="txt_link">[2:7:12]</a></span></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 11:50:22</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <div class="combatLeftSide"><br>
    <span class="msg_ctn msg_ctn2 undermark tooltipLeft" title="0">Attacker: (Lieutenant Cupid): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="Resources<br/>Metal: 10,287<br/>Crystal: 10,287<br/>Deuterium: 4,419<br/>Food: 7">Resources: 25,000, Loot: 75%</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="0">Debris field (newly created): 0</span><br>
</div><div class="combatRightSide"><br>
    <span class="msg_ctn msg_ctn2 overmark tooltipRight" title="0">Defender: (FrameIT): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipRight" title="0">Actually repaired: 0</span><br>
            <span class="msg_ctn msg_ct3 ">Moon Chance: 0 %</span><br>
    </div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="75463"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-211c4aa5d5e1e574aff6df78a25094bfadea5ef4' readonly onclick='select()' style='width:360px'></input>" data-message-id="75463"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="75463" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=75463"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=7&amp;position=12&amp;type=1&amp;mission=1';" data-message-id="75463"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,7,12,1,2); return false;" data-message-id="75463"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=75463&amp;tabid=21&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="73008">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt">Contact with the attacking fleet has been lost.<br><a href="#galaxy&amp;galaxy=2&amp;system=1&amp;position=10" class="txt_link">[2:1:10]</a></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 10:24:23</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        (That means it was destroyed in the first round.)
    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>





                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <li class="msg " data-msg-id="71770">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title blue_txt"><span class="undermark">Combat Report Homeworld <figure class="planetIcon planet tooltip js_hideTipOnMobile" title="Planet"></figure> <a href="#galaxy&amp;galaxy=2&amp;system=1&amp;position=10" class="txt_link">[2:1:10]</a></span></span>
                                                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">18.03.2024 09:33:37</span>
    </span>
                                                        <br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">Fleet Command</span>
                                                    </div>
                                                    <span class="msg_content">
        <div class="combatLeftSide"><br>
    <span class="msg_ctn msg_ctn2 undermark tooltipLeft" title="0">Attacker: (Lieutenant Cupid): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="Resources<br/>Metal: 5,925<br/>Crystal: 5,925<br/>Deuterium: 5,925<br/>Food: 975">Resources: 18,750, Loot: 75%</span><br>
    <span class="msg_ctn msg_ctn3 tooltipLeft" title="0">Debris field (newly created): 0</span><br>
</div><div class="combatRightSide"><br>
    <span class="msg_ctn msg_ctn2 overmark tooltipRight" title="0">Defender: (Commander Callisto): 0</span><br>
    <span class="msg_ctn msg_ctn3 tooltipRight" title="0">Actually repaired: 0</span><br>
            <span class="msg_ctn msg_ct3 ">Moon Chance: 0 %</span><br>
    </div><br>

    </span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>

                                                            <gradient-button sq30=""><button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn" title="mark as favourite" data-message-id="71770"><img src="/img/icons/not_favorited.png" style="width:20px;height:20px;"></button></gradient-button>

                                                            <gradient-button sq30=""><button class="custom_btn icon_apikey tooltip msgApiKeyBtn" title="This data can be entered into a compatible combat simulator:<br/><input value='cr-en-255-c209ba1d51eb5ebf8c14d7aeb669cbdc77418f79' readonly onclick='select()' style='width:360px'></input>" data-message-id="71770"><img src="/img/icons/apikey.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn overlay tooltip msgShareBtn" title="share message" data-message-id="71770" data-overlay-title="share message" data-target="#shareReportOverlay&amp;messageId=71770"><img src="/img/icons/share.png" style="width:20px;height:20px;"></button></gradient-button>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgAttackBtn" title="Attack" onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=1&amp;position=10&amp;type=1&amp;mission=1';" data-message-id="71770"><div class="msgAttackIconContainer"><img src="/img/icons/attack.png" style="width:20px;height:20px;"></div></button></gradient-button>




                                                            <gradient-button sq30=""><button class="custom_btn tooltip msgEspionageBtn" title="Espionage" onclick="sendShipsWithPopup(6,2,1,10,1,2); return false;" data-message-id="71770"><img src="/img/icons/espionage.png" style="width:20px;height:20px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                        <message-footer-details>
                                                            <a class="fright txt_link msg_action_link overlay" href="#messages&amp;messageId=71770&amp;tabid=21&amp;ajax=1" data-overlay-title="More details">
                                                                More details
                                                            </a>
                                                        </message-footer-details>
                                                    </message-footer>
                                                    <script type="text/javascript">
                                                        initOverlays();
                                                    </script>

                                                </li>
                                                <ul class="pagination"><li class="paginator" data-tab="21" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="21" data-page="1">&lt;</li><li class="curPage" data-tab="21">1/1</li><li class="paginator" data-tab="21" data-page="1">&gt;</li><li class="paginator" data-tab="21" data-page="1">&gt;&gt;|</li></ul>
                                            </ul>
                                            <script type="text/javascript">
                                                var activeTabid = $('.ui-tabs-active a').attr('id'); //erster tab als default
                                                var hasSubtabs = $('div[aria-labelledby="' + activeTabid + '"] .tab_ctn div ul.subtabs').length;
                                                var activeSubtabid = '';

                                                $('.ui-tabs-active a').each(function(){
                                                    activeSubtabid = $(this).attr('id');
                                                });

                                                var msgids = [];
                                                var index = 0;

                                                if (hasSubtabs > 0) {
                                                    $('div[aria-labelledby="' + activeSubtabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                } else {
                                                    $('div[aria-labelledby="' + activeTabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                }

                                                msgids = JSON.stringify(msgids);

                                                // TODO: re-enable when working on the messages feature.
                                                if (1===3) {
                                                    var msgcountUrl = "#ajaxMessageCount";
                                                    var playerid = parseInt(102489);
                                                    var action = 111;

                                                    $.ajax({
                                                        url: msgcountUrl,
                                                        type: 'POST',
                                                        data: {
                                                            player: playerid,
                                                            action: action,
                                                            newMessageIds: msgids,
                                                            ajax: 1
                                                        },
                                                        success: function (data) {
                                                            var message_menu_count = $('.comm_menu.messages span.new_msg_count');
                                                            var message_tab_count = $('.ui-tabs-active .new_msg_count');

                                                            if (message_menu_count.length > 0 && message_tab_count.length > 0) {
                                                                var menuCount = parseInt(message_menu_count[0].innerHTML);
                                                                var tabCount = parseInt(message_tab_count[0].innerHTML);
                                                                var newCount = menuCount - tabCount;

                                                                if (newCount > 0) {
                                                                    message_menu_count.val(newCount);
                                                                } else {
                                                                    message_menu_count.remove();
                                                                }
                                                            }

                                                            $('.ui-tabs-active .new_msg_count').remove();

                                                            if (hasSubtabs > 0) {
                                                                $('.ui-tabs-active a span:not(.icon_caption)').remove();
                                                            }
                                                        },
                                                        error: function (jqXHR, textStatus, errorThrown) {
                                                        }
                                                    });
                                                }
                                            </script></div></div><div id="ui-id-18" aria-live="polite" aria-labelledby="ui-id-17" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"><div id="fleetsgenericpage"><ul class="tab_inner ctn_with_trash clearfix">
                                                <ul class="pagination"><li class="paginator" data-tab="22" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="22" data-page="1">&lt;</li><li class="curPage" data-tab="22">1/1</li><li class="paginator" data-tab="22" data-page="1">&gt;</li><li class="paginator" data-tab="22" data-page="1">&gt;&gt;|</li></ul>
                                                <input type="hidden" name="token" value="d99f68937305e0b2c3ff3f059259fcec">
                                                <li class="no_msg">
                                                    There are currently no messages available in this tab
                                                </li>
                                                <br>
                                                <ul class="pagination"><li class="paginator" data-tab="22" data-page="1">|&lt;&lt;</li><li class="paginator" data-tab="22" data-page="1">&lt;</li><li class="curPage" data-tab="22">1/1</li><li class="paginator" data-tab="22" data-page="1">&gt;</li><li class="paginator" data-tab="22" data-page="1">&gt;&gt;|</li></ul>
                                            </ul>
                                            <script type="text/javascript">
                                                var activeTabid = $('.ui-tabs-active a').attr('id'); //erster tab als default
                                                var hasSubtabs = $('div[aria-labelledby="' + activeTabid + '"] .tab_ctn div ul.subtabs').length;
                                                var activeSubtabid = '';

                                                $('.ui-tabs-active a').each(function(){
                                                    activeSubtabid = $(this).attr('id');
                                                });

                                                var msgids = [];
                                                var index = 0;

                                                if (hasSubtabs > 0) {
                                                    $('div[aria-labelledby="' + activeSubtabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                } else {
                                                    $('div[aria-labelledby="' + activeTabid + '"] .msg_new').each(function() {
                                                        msgids[index] = $(this).data('msg-id');
                                                        index++;
                                                    });
                                                }

                                                msgids = JSON.stringify(msgids);

                                                // TODO: re-enable when working on the messages feature.
                                                if (1===3) {
                                                    var msgcountUrl = "#ajaxMessageCount";
                                                    var playerid = parseInt(102489);
                                                    var action = 111;

                                                    $.ajax({
                                                        url: msgcountUrl,
                                                        type: 'POST',
                                                        data: {
                                                            player: playerid,
                                                            action: action,
                                                            newMessageIds: msgids,
                                                            ajax: 1
                                                        },
                                                        success: function (data) {
                                                            var message_menu_count = $('.comm_menu.messages span.new_msg_count');
                                                            var message_tab_count = $('.ui-tabs-active .new_msg_count');

                                                            if (message_menu_count.length > 0 && message_tab_count.length > 0) {
                                                                var menuCount = parseInt(message_menu_count[0].innerHTML);
                                                                var tabCount = parseInt(message_tab_count[0].innerHTML);
                                                                var newCount = menuCount - tabCount;

                                                                if (newCount > 0) {
                                                                    message_menu_count.val(newCount);
                                                                } else {
                                                                    message_menu_count.remove();
                                                                }
                                                            }

                                                            $('.ui-tabs-active .new_msg_count').remove();

                                                            if (hasSubtabs > 0) {
                                                                $('.ui-tabs-active a span:not(.icon_caption)').remove();
                                                            }
                                                        },
                                                        error: function (jqXHR, textStatus, errorThrown) {
                                                        }
                                                    });
                                                }
                                            </script></div></div><div id="ui-id-20" aria-live="polite" aria-labelledby="ui-id-19" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-22" aria-live="polite" aria-labelledby="ui-id-21" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-24" aria-live="polite" aria-labelledby="ui-id-23" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div>
                                </div>
                            </div>
                        </div></div><div id="ui-id-4" aria-live="polite" aria-labelledby="ui-id-3" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-6" aria-live="polite" aria-labelledby="ui-id-5" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-8" aria-live="polite" aria-labelledby="ui-id-7" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-10" aria-live="polite" aria-labelledby="ui-id-9" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div><div id="ui-id-12" aria-live="polite" aria-labelledby="ui-id-11" role="tabpanel" class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true" style="display: none;"></div>
                    <div class="ajax_load_shadow clearfix" style="display: none;">
                        <img class="ajax_load_img64px" src="/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif" alt="load..." height="64" width="64">
                    </div>
                </div>         <div class="footer">
                </div>
            </div> </div>


        <script type="text/javascript">
            function deleteBuddy() {
                var $thisObj = $(this);
                errorBoxDecision(
                    'Delete buddy',
                    $thisObj.attr("ref"),
                    'yes',
                    'No',
                    function () {
                        var buddyAction = 10;
                        var actionId = $thisObj.attr("id");

                        $("#buddies .ajaxContent").html("<p class=\"ajaxLoad\">load...</p>");

                        $.post("#ingame&component=buddies&ajax=1",
                            {
                                action: buddyAction,
                                id: actionId,
                                ajax: 1
                            },
                            function (data) {
                                var ajaxContent = $(data).find('.content .buddylistContent');

                                $('.buddylistContent').html($(ajaxContent).html());

                                ogame.buddies.initBuddyList();
                            }
                        );
                    }
                );
            }

            function ajaxBuddySearch(e) {

                var searchTextField = $('.buddySearch');
                var searchValue = searchTextField.val();

                if (searchValue.length >= 2 || e.keyCode === 8 || e.keyCode === 46) {
                    var params = {};
                    if (searchValue.length > 0) {
                        params =
                            {
                                "search": searchValue,
                                "action": 15,
                                "ajax": "1"
                            };
                    } else {
                        params =
                            {
                                "action": 9,
                                "ajax": "1"
                            };
                    }

                    $.post(
                        "#ingame&component=buddies&ajax=1",
                        params,
                        function (data) {
                            var ajaxTableContent = $(data).find('#buddylist');
                            $('#buddylist').html($(ajaxTableContent).html());
                            ogame.buddies.initBuddyList();
                        }
                    );

                } else {
                    if (e.keyCode === 13) {
                        fadeBox("Too few characters! Please put in at least 2 characters.", true);
                    }
                }
            }

            function cancelRequest() {
                var buddyAction = 3;
                var actionId = $(this).data('buddyid');
                var buddyCount = parseInt($("#ownRequestCount").text()) - 1;

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {
                        $("#ownRequestCount").html(buddyCount);

                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }

            function acceptRequest() {
                var buddyAction = 5;
                var actionId = $(this).data('buddyid');

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {
                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }

            function reportRequest() {
                var buddy = $(this).data('buddyid');
                var id = $(this).data('requestid');
                var icon = $(this).parent();

                function sendReport() {
                    $.ajax({
                        type: 'POST',
                        url: '?page=reportSpam_ajax',
                        dataType: 'json',
                        data: {
                            buddyId: buddy,
                            requestId: id
                        },
                        success: function (data) {
                            icon.hide();

                            fadeBox(data.message, !data.result);
                        },
                        error: function () {
                        }
                    });
                }

                errorBoxDecision(
                    "Caution",
                    "Report this message to a game operator?",
                    "yes",
                    "No",
                    sendReport
                );

                return true;
            }

            function rejectRequest() {
                var buddyAction = 4;
                var actionId = $(this).data('buddyid');
                var buddyCount = parseInt($("#newRequestCount").text()) - 1;

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {

                        if (buddyCount >= 0) {
                            $("#newRequestCount").html(buddyCount);
                        }
                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }
        </script>

        <script language="javascript">
            // passing variables to javascript
            var localizedBBCode = {"bold":"Bold","italic":"Italic","underline":"Underline","stroke":"Strikethrough","sub":"Subscript","sup":"Superscript","fontColor":"Font colour","fontSize":"Font size","backgroundColor":"Background colour","backgroundImage":"Background image","tooltip":"Tool-tip","alignLeft":"Left align","alignCenter":"Centre align","alignRight":"Right align","alignJustify":"Justify","block":"Break","code":"Code","spoiler":"Spoiler","moreopts":"More Options","list":"List","hr":"Horizontal line","picture":"Image","link":"Link","email":"Email","player":"Player","item":"Item","coordinates":"Coordinates","preview":"Preview","textPlaceHolder":"Text...","playerPlaceHolder":"Player ID or name","itemPlaceHolder":"Item ID","coordinatePlaceHolder":"Galaxy:system:position","charsLeft":"Characters remaining","colorPicker":{"ok":"Ok","cancel":"Cancel","rgbR":"R","rgbG":"G","rgbB":"B"},"backgroundImagePicker":{"ok":"Ok","repeatX":"Repeat horizontally","repeatY":"Repeat vertically"}};
            var itemNames = {"1aa36213cb676fd5baad5edc2bee4fbe117a778b":"Researchers","6c9fe5e35bdad0d4e3382eb6a5aeac6bc8263752":"Traders","9b48e257cbef6c5df0f03a47cead7f9abda3d437":"Warriors","090a969b05d1b5dc458a6b1080da7ba08b84ec7f":"Bronze Crystal Booster","e254352ac599de4dd1f20f0719df0a070c623ca8":"Bronze Deuterium Booster","b956c46faa8e4e5d8775701c69dbfbf53309b279":"Bronze Metal Booster","2dd05cc4c0e185fce2e712112dc44932027aee98":"Discoverer","9374c79a24b84c4331f0d26526ef6c2d33319a6e":"Collector","77eff880829027daf23b755e14820a60c4c6fd93":"General","3c9f85221807b8d593fa5276cdf7af9913c4a35d":"Bronze Crystal Booster","060902a23da9dd917f1a754fe85734a91ec8d785":"Bronze Crystal Booster","bb7579f7a21152a4a256f001d5162765e2f2c5b9":"Bronze Crystal Booster","422db99aac4ec594d483d8ef7faadc5d40d6f7d3":"Silver Crystal Booster","5b69663e3ba09a1fe77cf72c5094e246cfe954d6":"Silver Crystal Booster","04d8afd5936976e32ce894b765ea8bd168aa07ef":"Silver Crystal Booster","118d34e685b5d1472267696d1010a393a59aed03":"Gold Crystal Booster","36fb611e71d42014f5ebd0aa5a52bc0c81a0c1cb":"Gold Crystal Booster","d45f00e8b909f5293a83df4f369737ea7d69c684":"Gold Crystal Booster","35d96e441c21ef112a84c618934d9d0f026998fd":"Platinum Crystal Booster","6bf45fcba8a6a68158273d04a924452eca75cf39":"Platinum Crystal Booster","7c2edf40c5cd54ad11c6439398b83020c0a7a6be":"Platinum Crystal Booster","d3d541ecc23e4daa0c698e44c32f04afd2037d84":"DETROID Bronze","0968999df2fe956aa4a07aea74921f860af7d97f":"DETROID Gold","3347bcd4ee59f1d3fa03c4d18a25bca2da81de82":"DETROID Platinum","27cbcd52f16693023cb966e5026d8a1efbbfc0f9":"DETROID Silver","d9fa5f359e80ff4f4c97545d07c66dbadab1d1be":"Bronze Deuterium Booster","d50005c05fd5b95125364af43c78dfaba64d7f83":"Bronze Deuterium Booster","63d11915e9af76ee41938cc099dbf8d54ad59a17":"Bronze Deuterium Booster","e4b78acddfa6fd0234bcb814b676271898b0dbb3":"Silver Deuterium Booster","26416a3cdb94613844b1d3ca78b9057fd6ae9b15":"Silver Deuterium Booster","6f0952a919fd2ab9c009e9ccd83c1745f98f758f":"Silver Deuterium Booster","5560a1580a0330e8aadf05cb5bfe6bc3200406e2":"Gold Deuterium Booster","300493ddc756869578cb2888a3a1bc0c3c66765f":"Gold Deuterium Booster","dc5896bed3311434224d511fa7ced6fdbe41b4e8":"Gold Deuterium Booster","4b51d903560edd102467b110586000bd64fdb954":"Platinum Deuterium Booster","620f779dbffa1011aded69b091239727910a3d03":"Platinum Deuterium Booster","831c3ea8d868eb3601536f4d5e768842988a1ba9":"Platinum Deuterium Booster","3f6f381dc9b92822406731a942c028adf8dc978f":"Energy Booster Bronze","7eeeb36a455c428eb6923a50d2f03544b6dd05d6":"Energy Booster Bronze","6837c08228d2b023fb955ca2dc589a0a4bed3ba8":"Energy Booster Bronze","c2bad58fcec374d709099d11d0549e59ea7e233e":"Energy Booster Silver","bedd248aaf288c27e9351cfacfa6be03f1dbb898":"Energy Booster Silver","e05aa5b9e3df5be3857b43da8403eafbf5ad3b96":"Energy Booster Silver","55b52cbfb148ec80cd4e5b0580f7bed01149d643":"Energy Booster Gold","4fa9a2273ee446284d5177fd9d60a22de01e932b":"Energy Booster Gold","5ad783dcfce3655ef97b36197425718a0dad6b66":"Energy Booster Gold","77c36199102e074dca46f5f26ef57ce824d044dd":"Energy Booster Platinum","dfe86378f8c3d7f3ee0790ea64603bc44e83ca47":"Energy Booster Platinum","c39aa972a971e94b1d9b4d7a8f734b3d8be12534":"Energy Booster Platinum","8c1f6c6849d1a5e4d9de6ae9bb1b861f6f7b5d4d":"Bronze Expedition Slots","e54ecc0416d6e96b4165f24238b03a1b32c1df47":"Bronze Expedition Slots","a5784c685c0e1e6111d9c18aeaf80af2e0777ab4":"Bronze Expedition Slots","31a504be1195149a3bef05b9cc6e3af185d24ef2":"Silver Expedition Slots","b2bc9789df7c1ef5e058f72d61380b696dde54e8":"Silver Expedition Slots","4f6f941bbf2a8527b0424b3ad11014502d8f4fb8":"Silver Expedition Slots","fd7d35e73d0e09e83e30812b738ef966ea9ef790":"Gold Expedition Slots","9336b9f29d36e3f69b0619c9523d8bec5e09ab8e":"Gold Expedition Slots","540410439514ac09363c5c47cf47117a8b8ae79a":"Gold Expedition Slots","94a28491b6fd85003f1cb151e88dde106f1d7596":"Bronze Fleet Slots","0684c6a5a42acbb3cd134913d421fc28dae6b90d":"Bronze Fleet Slots","bb47add58876240199a18ddacc2db07789be1934":"Bronze Fleet Slots","c4e598a85805a7eb3ca70f9265cbd366fc4d2b0e":"Silver Fleet Slots","f8fd610825fb4a442e27e4e9add74f050e040e27":"Silver Fleet Slots","a693c5ce3f5676efaaf0781d94234bea4f599d2e":"Silver Fleet Slots","1808bf7639b81ac3ac87bcb7eb3bbba0a1874d0a":"Gold Fleet Slots","5a8000c372cd079292a92d35d4ddba3c0f348d3b":"Gold Fleet Slots","1f7024c4f6493f0c589e1b00c76e6ced258c00e5":"Gold Fleet Slots","40f6c78e11be01ad3389b7dccd6ab8efa9347f3c":"KRAKEN Bronze","929d5e15709cc51a4500de4499e19763c879f7f7":"KRAKEN Gold","c19f0e09d862d93d7956beb3185d9ee929b5ef74":"KRAKEN Platinum (Lifeforms)","00b42f7113d81f98df865bbfa2280fe3a4465e89":"KRAKEN Bronze (Lifeforms)","0ad06bba14dfd0b576f1daef729a60753e2263c7":"KRAKEN Gold (Lifeforms)","5f194777c5b69d5c2a3c68e9e04a4cae9c28bcf2":"KRAKEN Silver (Lifeforms)","f36042d76e6b8b33d931e1d4ae99f35265cd82d1":"KRAKEN Platinum","4a58d4978bbe24e3efb3b0248e21b3b4b1bfbd8a":"KRAKEN Silver","de922af379061263a56d7204d1c395cefcfb7d75":"Bronze Metal Booster","8a469c50ed10b78eaf872ea766ca66495da31a17":"Bronze Metal Booster","9ce31395cbd1e60d29e0770b9e20c6eb6053a344":"Bronze Metal Booster","ba85cc2b8a5d986bbfba6954e2164ef71af95d4a":"Silver Metal Booster","742743b3b0ae1f0b8a1e01921042810b58f12f39":"Silver Metal Booster","6f44dcd2bd84875527abba69158b4e976c308bbc":"Silver Metal Booster","05294270032e5dc968672425ab5611998c409166":"Gold Metal Booster","6fecb993169fe918d9c63cd37a2e541cc067664e":"Gold Metal Booster","21c1a65ca6aecf54ffafb94c01d0c60d821b325d":"Gold Metal Booster","a83cfdc15b8dba27c82962d57e50d8101d263cfb":"Platinum Metal Booster","c690f492cffe5f9f2952337e8eed307a8a62d6cf":"Platinum Metal Booster","ca7f903a65467b70411e513b0920d66c417aa3a2":"Platinum Metal Booster","be67e009a5894f19bbf3b0c9d9b072d49040a2cc":"Bronze Moon Fields","05ee9654bd11a261f1ff0e5d0e49121b5e7e4401":"Gold Moon Fields","8a426241572b2fea57844acd99bc326fe40e35cf":"Platinum Moon Fields","c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf":"Silver Moon Fields","485a6d5624d9de836d3eb52b181b13423f795770":"Bronze M.O.O.N.S.","d94731aa4a989f741ca18dd7d16589e970f0486f":"Bronze M.O.O.N.S.","45d6660308689c65d97f3c27327b0b31f880ae75":"Gold M.O.O.N.S.","faab6a750c53d440cd5a1638dbd853ef4ecb1fec":"Gold M.O.O.N.S.","fd895a5c9fd978b9c5c7b65158099773ba0eccef":"Silver M.O.O.N.S.","8ecde49bed4d3da1c3266ab736cb0c1a3dc209aa":"Silver M.O.O.N.S.","da4a2a1bb9afd410be07bc9736d87f1c8059e66d":"NEWTRON Bronze","8a4f9e8309e1078f7f5ced47d558d30ae15b4a1b":"NEWTRON Gold","ba3e6693f112986b7964c835bcac6ae201900e2f":"NEWTRON Bronze (Lifeforms)","7fe4cdb098685f8af827ca460a56e00ef46f5f05":"NEWTRON Gold (Lifeforms)","9cde936fabc5037617f8261955e7d3f2262eec69":"NEWTRON Platinum (Lifeforms)","9879a36c42797a868416b13f07e033f664cabd70":"NEWTRON Silver (Lifeforms)","a1ba242ede5286b530cdf991796b3d1cae9e4f23":"NEWTRON Platinum","d26f4dab76fdc5296e3ebec11a1e1d2558c713ea":"NEWTRON Silver","16768164989dffd819a373613b5e1a52e226a5b0":"Bronze Planet Fields","04e58444d6d0beb57b3e998edc34c60f8318825a":"Gold Planet Fields","f3d9b82e10f2e969209c1a5ad7d22181c703bb36":"Platinum Planet Fields","0e41524dc46225dca21c9119f2fb735fd7ea5cb3":"Silver Planet Fields","c1d0232604872f899ea15a9772baf76880f55c5f":"Complete Resource Package","bb2f6843226ef598f0b567b92c51b283de90aa48":"Crystal Package","cb72ed207dd871832a850ee29f1c1f83aa3f4f36":"Deuterium Package","859d82d316b83848f7365d21949b3e1e63c7841f":"Metal Package"};
            var loca = {"LOCA_SETTINGS_NEWSFEED":"News feed","LOCA_ALL_AJAXLOAD":"load...","LOCA_GALAXY_ERROR_OCCURED":"An error has occurred","LOCA_MSG_ADD_FAV":"mark as favourite","LOCA_MSG_DELETE_FAV":"remove from favourites"};

            (function($) {
                ogame.messages.initMessages('d99f68937305e0b2c3ff3f059259fcec');
                requestsReady();
            })(jQuery);
        </script>
    </div>
@endsection
