@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    
    <div id="inhalt">
        <div id="highscoreContent">
            <div class="header">
                <h2>
                    Player highscore            </h2>
            </div>
            <div class="content">
                <form id="send" name="send" action="#highscore">
                    <div id="scrollToTop" style="left: 760.336px;"><a href="javascript:void(0);" title="Back to top" class="scrollToTop tooltip js_hideTipOnMobile"></a></div>
                    <div id="row" class="lifeform-enabled">

                        <div class="buttons leftCol" id="categoryButtons">
                            <a id="player" class="active navButton" href="javascript:void(0);" rel="1" onclick="">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Player</span>
                            </a>
                            <a id="alliance" class="navButton" href="javascript:void(0);" rel="2" onclick="">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Alliance</span>
                            </a>
                        </div>

                        <div class="buttons rightCol" id="typeButtons">

                            <a id="points" class="stat_filter active navButton fleft" href="javascript:void(0);" rel="0">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Points</span>
                            </a>

                            <a id="economy" class="stat_filter navButton fleft" href="javascript:void(0);" rel="1">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Economy</span>
                            </a>

                            <a id="research" class="stat_filter navButton fleft" href="javascript:void(0);" rel="2">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Research</span>
                            </a>

                            <a id="fleet" class="stat_filter navButton fleft" href="javascript:void(0);" rel="3">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Military</span>
                            </a>

                            <a id="lifeform" class="stat_filter navButton fleft" href="javascript:void(0);" rel="8">
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <span class="marker"></span>
                                <span class="textlabel">Lifeform</span>
                            </a>


                            <div id="subnav_fleet" class="fleft subnav">
                                <a href="javascript:void(0);" rel="5" class="subnavButton subnavButton_built tooltip js_hideTipOnMobile" title="Military points built">
                                    <span class="small-marker"></span>
                                </a>


                                <a href="javascript:void(0);" rel="6" class="subnavButton subnavButton_destroyed tooltip js_hideTipOnMobile" title="Military points destroyed">
                                    <span class="small-marker"></span>
                                </a>

                                <a href="javascript:void(0);" rel="4" class="subnavButton subnavButton_lost tooltip js_hideTipOnMobile" title="Military points lost">
                                    <span class="small-marker"></span>
                                </a>

                                <a href="javascript:void(0);" rel="7" class="subnavButton subnavButton_honor tooltip js_hideTipOnMobile" title="Honour points">
                                    <span class="small-marker"></span>
                                </a>
                            </div>

                            <div id="subnav_lifeform" class="fleft subnav">
                                <a href="javascript:void(0);" rel="9" class="subnavButton subnavButton_lfbuilt tooltip js_hideTipOnMobile" title="Lifeform Economy
">
                                    <span class="small-marker"></span>
                                </a>

                                <a href="javascript:void(0);" rel="10" class="subnavButton subnavButton_lfresearch tooltip js_hideTipOnMobile" title="Lifeform Technology
">
                                    <span class="small-marker"></span>
                                </a>

                                <a href="javascript:void(0);" rel="11" class="subnavButton subnavButton_lfdiscover tooltip js_hideTipOnMobile" title="Lifeform Discoveries
">
                                    <span class="small-marker"></span>
                                </a>
                            </div>

                        </div>

                        <br class="clearfloat">
                    </div>

                    <div class="" id="stat_list_content">

                        <div id="content"><div>
                                <script type="text/javascript">
                                    var currentCategory = 1;
                                    var currentType = 0;
                                    var searchPosition = 113970;
                                    var userWantsFocus = false;
                                    var site = 23;
                                    var searchSite = 23;
                                    var resultsPerPage = 100;
                                    var searchRelId = 113970;
                                </script>

                                <div class="pagebar">
                                    &nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', '#stat_list_content'); return false;">«</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=18', '#stat_list_content'); return false;">18</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=19', '#stat_list_content'); return false;">19</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=20', '#stat_list_content'); return false;">20</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=21', '#stat_list_content'); return false;">21</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=22', '#stat_list_content'); return false;">22</a>&nbsp; <span class=" activePager">23</span>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=24', '#stat_list_content'); return false;">24</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=25', '#stat_list_content'); return false;">25</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=26', '#stat_list_content'); return false;">26</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=27', '#stat_list_content'); return false;">27</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=28', '#stat_list_content'); return false;">28</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=55', '#stat_list_content'); return false;">»</a>
                                </div>
                                <select class="changeSite fright dropdownInitialized" style="display: none;">
                                    <option value="23">Own position</option>
                                    <option value="1"> 1 - 100</option>
                                    <option value="2"> 101 - 200</option>
                                    <option value="3"> 201 - 300</option>
                                    <option value="4"> 301 - 400</option>
                                    <option value="5"> 401 - 500</option>
                                    <option value="6"> 501 - 600</option>
                                    <option value="7"> 601 - 700</option>
                                    <option value="8"> 701 - 800</option>
                                    <option value="9"> 801 - 900</option>
                                    <option value="10"> 901 - 1000</option>
                                    <option value="11"> 1001 - 1100</option>
                                    <option value="12"> 1101 - 1200</option>
                                    <option value="13"> 1201 - 1300</option>
                                    <option value="14"> 1301 - 1400</option>
                                    <option value="15"> 1401 - 1500</option>
                                    <option value="16"> 1501 - 1600</option>
                                    <option value="17"> 1601 - 1700</option>
                                    <option value="18"> 1701 - 1800</option>
                                    <option value="19"> 1801 - 1900</option>
                                    <option value="20"> 1901 - 2000</option>
                                    <option value="21"> 2001 - 2100</option>
                                    <option value="22"> 2101 - 2200</option>
                                    <option value="23" selected="selected"> 2201 - 2300</option>
                                    <option value="24"> 2301 - 2400</option>
                                    <option value="25"> 2401 - 2500</option>
                                    <option value="26"> 2501 - 2600</option>
                                    <option value="27"> 2601 - 2700</option>
                                    <option value="28"> 2701 - 2800</option>
                                    <option value="29"> 2801 - 2900</option>
                                    <option value="30"> 2901 - 3000</option>
                                    <option value="31"> 3001 - 3100</option>
                                    <option value="32"> 3101 - 3200</option>
                                    <option value="33"> 3201 - 3300</option>
                                    <option value="34"> 3301 - 3400</option>
                                    <option value="35"> 3401 - 3500</option>
                                    <option value="36"> 3501 - 3600</option>
                                    <option value="37"> 3601 - 3700</option>
                                    <option value="38"> 3701 - 3800</option>
                                    <option value="39"> 3801 - 3900</option>
                                    <option value="40"> 3901 - 4000</option>
                                    <option value="41"> 4001 - 4100</option>
                                    <option value="42"> 4101 - 4200</option>
                                    <option value="43"> 4201 - 4300</option>
                                    <option value="44"> 4301 - 4400</option>
                                    <option value="45"> 4401 - 4500</option>
                                    <option value="46"> 4501 - 4600</option>
                                    <option value="47"> 4601 - 4700</option>
                                    <option value="48"> 4701 - 4800</option>
                                    <option value="49"> 4801 - 4900</option>
                                    <option value="50"> 4901 - 5000</option>
                                    <option value="51"> 5001 - 5100</option>
                                    <option value="52"> 5101 - 5200</option>
                                    <option value="53"> 5201 - 5300</option>
                                    <option value="54"> 5301 - 5400</option>
                                    <option value="55"> 5401 - 5441</option>

                                </select><span class="dropdown currentlySelected changeSite fright" rel="dropdown143" style="width: 150px;"><a class="undefined" data-value="23" rel="dropdown143" href="javascript:void(0);"> 2201 - 2300</a></span>
                                <div class="fleft" id="highscoreHeadline">
                                    Points
                                </div>
                                <table id="ranks" class="userHighscore">
                                    <thead>
                                    <tr>
                                        <td class="position">
                                            Position
                                        </td>
                                        <td class="movement"></td>
                                        <td class="name">
                                            Player’s Name
                                            (Honour points)
                                        </td>
                                        <td class="sendmsg" align="center">
                                            Action
                                        </td>
                                        <td class="score" align="center">
                                            Points
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr class="
            " id="position114167">

                                        <td class="position">
                                            2204
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=204&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Governor Telorix
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114167" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114167&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            645
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position107555">

                                        <td class="position">
                                            2205
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=136&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            shitoldman
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="107555" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=107555&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            645
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position100819">

                                        <td class="position">
                                            2206
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500500" target="_ally">
                                [SGC]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=114&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Empire Day
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">3</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="100819" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=100819&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            642
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position115149">

                                        <td class="position">
                                            2207
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(297)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=445&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Pesmerga
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="115149" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=115149&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            658
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position105131">

                                        <td class="position">
                                            2208
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500373" target="_ally">
                                [LCA]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=48&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            gaby65433
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="105131" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=105131&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            632
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position115088">

                                        <td class="position">
                                            2209
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(211)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=430&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Technocrat Majoris
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="115088" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=115088&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            632
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position110165">

                                        <td class="position">
                                            2210
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(9)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501081" target="_ally">
                                [SYN]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=258&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Sovereign Galaxarion
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="110165" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=110165&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            627
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position111672">

                                        <td class="position">
                                            2211
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501133" target="_ally">
                                [MCR]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=109&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Commodore Atlas
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="111672" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=111672&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            622
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position102729">

                                        <td class="position">
                                            2212
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500707" target="_ally">
                                [RAC]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=8&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            John the Third
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="102729" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=102729&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            621
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position102924">

                                        <td class="position">
                                            2213
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=50&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Quadrat
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="102924" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=102924&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            617
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108378">

                                        <td class="position">
                                            2214
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=340&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Czar Tethys
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108378" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108378&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            615
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112761">

                                        <td class="position">
                                            2215
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=370&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Chancellor Nomad
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112761" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112761&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            613
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113532">

                                        <td class="position">
                                            2216
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=52&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            PeLegeleu
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">1</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113532" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113532&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            598
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position114136">

                                        <td class="position">
                                            2217
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(53)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501407" target="_ally">
                                [9876]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=197&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Imperator Lostesache
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114136" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114136&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            596
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position104388">

                                        <td class="position">
                                            2218
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=384&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Valkiria Darling
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104388" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104388&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            596
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position100667">

                                        <td class="position">
                                            2219
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=84&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            NoToSiup
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="100667" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=100667&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            594
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position101923">

                                        <td class="position">
                                            2220
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500181" target="_ally">
                                [IIIII]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=338&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Geologist Virgo
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="101923" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=101923&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            588
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112968">

                                        <td class="position">
                                            2221
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(74)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=420&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Consul Oberon
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112968" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112968&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            581
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position114084">

                                        <td class="position">
                                            2222
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501369" target="_ally">
                                [gag]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=185&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Emperor Aurius
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114084" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114084&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            572
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position104979">

                                        <td class="position">
                                            2223
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500671" target="_ally">
                                [SAS]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=14&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            GhostPyro
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104979" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104979&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            567
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position110333">

                                        <td class="position">
                                            2224
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=299&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Trynikwa
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="110333" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=110333&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            558
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112536">

                                        <td class="position">
                                            2225
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=317&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Wtopor
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112536" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112536&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            550
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108208">

                                        <td class="position">
                                            2226
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500938" target="_ally">
                                [KIT]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=300&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            KITstupid
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">12</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108208" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108208&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            550
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position109156">

                                        <td class="position">
                                            2227
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500975" target="_ally">
                                [IOP]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=19&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Tenerife
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109156" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109156&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            547
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109555">

                                        <td class="position">
                                            2228
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501014" target="_ally">
                                [c9LOL]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=113&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            mattlakerskb24
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109555" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109555&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            547
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position109154">

                                        <td class="position">
                                            2229
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500978" target="_ally">
                                [Tryhm]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=18&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Vice Tryhm
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109154" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109154&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            546
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109725">

                                        <td class="position">
                                            2230
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501023" target="_ally">
                                [Axel]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=153&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Axelrod
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109725" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109725&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            541
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position104890">

                                        <td class="position">
                                            2231
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=492&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Rasta Barbie
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104890" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104890&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            541
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108012">

                                        <td class="position">
                                            2232
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=250&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            General Darculvich
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108012" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108012&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            540
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112247">

                                        <td class="position">
                                            2233
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501193" target="_ally">
                                [GOT]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=247&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            RaStiQ
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112247" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112247&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            535
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position115150">

                                        <td class="position">
                                            2234
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(295)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501412" target="_ally">
                                [ITA]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=445&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            PolpettaJ
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="115150" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=115150&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            529
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106210">

                                        <td class="position">
                                            2235
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500683" target="_ally">
                                [TECH]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=312&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Anam23
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106210" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106210&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            517
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position107504">

                                        <td class="position">
                                            2236
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=124&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Krynul
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="107504" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=107504&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            515
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position108644">

                                        <td class="position">
                                            2237
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500967" target="_ally">
                                [D-B]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=404&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lieutenant Halo
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108644" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108644&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            515
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113230">

                                        <td class="position">
                                            2238
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=481&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            lastwish
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113230" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113230&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            513
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position113878">

                                        <td class="position">
                                            2239
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=134&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            synator
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113878" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113878&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            512
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position114199">

                                        <td class="position">
                                            2240
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(79)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=212&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Commander Orcus
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114199" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114199&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            512
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106391">

                                        <td class="position">
                                            2241
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(10)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=356&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lord Meteor
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106391" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106391&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            506
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position114241">

                                        <td class="position">
                                            2242
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=223&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Captain Stingray
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114241" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114241&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            500
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106140">

                                        <td class="position">
                                            2243
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=294&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Governor Nexor
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106140" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106140&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            495
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position106646">

                                        <td class="position">
                                            2244
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=414&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lord Perkeo
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">1</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106646" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106646&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            484
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106287">

                                        <td class="position">
                                            2245
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=331&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Sage
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106287" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106287&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            477
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113623">

                                        <td class="position">
                                            2246
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=73&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Renegade Vaylax
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113623" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113623&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            477
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112901">

                                        <td class="position">
                                            2247
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=404&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Chancellor Exilon
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112901" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112901&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            475
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position101728">

                                        <td class="position">
                                            2248
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=297&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Purple
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="101728" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=101728&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            469
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106480">

                                        <td class="position">
                                            2249
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500718" target="_ally">
                                [FrozenEH]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=376&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Master Voiceinhead
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106480" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106480&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            466
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113872">

                                        <td class="position">
                                            2250
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=132&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Captain Proteus
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113872" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113872&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            465
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112097">

                                        <td class="position">
                                            2251
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(12)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501326" target="_ally">
                                [Era]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=210&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            EraGonX15
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112097" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112097&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            464
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position100352">

                                        <td class="position">
                                            2252
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(26)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501174" target="_ally">
                                [CBD]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=21&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Mogul Aries
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="100352" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=100352&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            458
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position105240">

                                        <td class="position">
                                            2253
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500635" target="_ally">
                                [loot]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=71&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Chef
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="105240" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=105240&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            456
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108313">

                                        <td class="position">
                                            2254
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=325&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Director Buzz
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108313" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108313&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            455
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position107008">

                                        <td class="position">
                                            2255
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500842" target="_ally">
                                [Best]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=1&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Salamalacum
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="107008" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=107008&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            453
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113611">

                                        <td class="position">
                                            2256
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=70&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Aetherr
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113611" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113611&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            450
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position102995">

                                        <td class="position">
                                            2257
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=65&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lord Crux
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="102995" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=102995&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            450
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113238">

                                        <td class="position">
                                            2258
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(11)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=482&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Commander Zephron
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">38</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113238" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113238&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            449
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position102427">

                                        <td class="position">
                                            2259
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500278" target="_ally">
                                [Total]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=441&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            TrolledTGBot
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="102427" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=102427&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            439
                                        </td>
                                    </tr>

                                    <tr class="
                            myrank
            " id="position113970">

                                        <td class="position">
                                            2260
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(265)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="#alliance" style="color:#9c0;">
                                [DST]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=158&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            President Hati
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            435
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position105077">

                                        <td class="position">
                                            2261
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=36&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Vice Solar
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">3</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="105077" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=105077&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            430
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position110560">

                                        <td class="position">
                                            2262
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=352&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Salsero lelinho
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="110560" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=110560&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            423
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position105533">

                                        <td class="position">
                                            2263
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500601" target="_ally">
                                [WOW]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=142&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Procurator Magnetar
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="105533" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=105533&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            422
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position112790">

                                        <td class="position">
                                            2264
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=378&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Renegade
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112790" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112790&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            419
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position105417">

                                        <td class="position">
                                            2265
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500545" target="_ally">
                                [G F]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=113&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Suppenkasper
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="105417" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=105417&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            415
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113944">

                                        <td class="position">
                                            2266
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501327" target="_ally">
                                [XIID]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=151&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            MoSinA
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113944" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113944&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            414
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position107016">

                                        <td class="position">
                                            2267
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(13)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=3&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Kittens
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="107016" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=107016&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            413
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109908">

                                        <td class="position">
                                            2268
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501047" target="_ally">
                                [ChungJu]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=194&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Sovereign Vyron
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109908" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109908&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            412
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position102562">

                                        <td class="position">
                                            2269
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=469&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            LonestarX
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="102562" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=102562&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            406
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position114679">

                                        <td class="position">
                                            2270
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500696" target="_ally">
                                [BadN]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=330&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Shiro
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114679" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114679&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            399
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position108179">

                                        <td class="position">
                                            2271
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=293&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Senator Castor
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108179" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108179&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            396
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position100764">

                                        <td class="position">
                                            2272
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=103&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Consul Kraz
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="100764" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=100764&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            391
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position104777">

                                        <td class="position">
                                            2273
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=469&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Topisku
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104777" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104777&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            389
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position115031">

                                        <td class="position">
                                            2274
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(153)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=415&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Kr0gan Scorpius
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="115031" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=115031&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            382
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position114870">

                                        <td class="position">
                                            2275
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=376&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            miszka29
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114870" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114870&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            382
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position110989">

                                        <td class="position">
                                            2276
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501097" target="_ally">
                                [SOHO]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=451&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Commodore Astrid
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="110989" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=110989&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            375
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position109091">

                                        <td class="position">
                                            2277
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=3&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Technocrat Herschel
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109091" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109091&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            367
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109696">

                                        <td class="position">
                                            2278
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501028" target="_ally">
                                [Green]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=146&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Steve Merkle
                                    </span>

                                                <span class="honorScore">
                        (<span class="overmark tooltip js_hideTipOnMobile" title="Honour points">-6</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109696" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109696&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            363
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106072">

                                        <td class="position">
                                            2279
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=276&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Dezyder
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">1</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106072" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106072&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            361
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position104983">

                                        <td class="position">
                                            2280
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=15&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Galaxy 13
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104983" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104983&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            358
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position114986">

                                        <td class="position">
                                            2281
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=404&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Thaddeus Bonk
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114986" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114986&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            358
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108719">

                                        <td class="position">
                                            2282
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500937" target="_ally">
                                [Tiel]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=421&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lt92JL
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108719" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108719&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            356
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position108978">

                                        <td class="position">
                                            2283
                                        </td>

                                        <td class="movement">
                                            <img src="/img/icons/ea5bf2cc93e52e22e3c1b80c7f7563.gif" alt="stay">
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=478&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Wizziwoppi
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108978" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108978&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            354
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position113159">

                                        <td class="position">
                                            2284
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501259" target="_ally">
                                [ArcX]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=465&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            President Drogon
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="113159" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=113159&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            350
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position108432">

                                        <td class="position">
                                            2285
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500940" target="_ally">
                                [fox]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=353&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Vice Space
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108432" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108432&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            348
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109174">

                                        <td class="position">
                                            2286
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500979" target="_ally">
                                [Cairon]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=23&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Baron Ellis
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">52</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109174" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109174&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            348
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position108303">

                                        <td class="position">
                                            2287
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=322&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Gerstetter
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">33</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108303" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108303&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            344
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position109831">

                                        <td class="position">
                                            2288
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=5&amp;system=177&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Captain Gemini
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="109831" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=109831&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            339
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position111898">

                                        <td class="position">
                                            2289
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=165&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            -_-
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="111898" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=111898&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            339
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108754">

                                        <td class="position">
                                            2290
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500954" target="_ally">
                                [ATG]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=428&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Consul Omega
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108754" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108754&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            339
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position103937">

                                        <td class="position">
                                            2291
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=281&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Chancellor Alpha
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="103937" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=103937&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            338
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position106015">

                                        <td class="position">
                                            2292
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=263&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Karma
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106015" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106015&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            336
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position111239">

                                        <td class="position">
                                            2293
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=6&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Proconsul Echo
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">13</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="111239" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=111239&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            335
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position108357">

                                        <td class="position">
                                            2294
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500924" target="_ally">
                                [T C]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=4&amp;system=336&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            SachaOFF
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="108357" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=108357&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            335
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position101080">

                                        <td class="position">
                                            2295
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=1&amp;system=166&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Fierra
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="101080" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=101080&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            334
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position104207">

                                        <td class="position">
                                            2296
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(16)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500446" target="_ally">
                                [TCC]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=343&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Lord Von Darkmoore
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">3</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104207" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104207&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            332
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112904">

                                        <td class="position">
                                            2297
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(107)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=405&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            Moo
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112904" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112904&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            332
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position111723">

                                        <td class="position">
                                            2298
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=122&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                        status_abbr_honorableTarget
                                         playername">
                                            astertoth
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="111723" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=111723&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            327
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position104214">

                                        <td class="position">
                                            2299
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(15)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=500429" target="_ally">
                                [xmp]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=2&amp;system=344&amp;position=8" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Captain blackbird
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="104214" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=104214&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            326
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position114997">

                                        <td class="position">
                                            2300
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(50)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=7&amp;system=407&amp;position=12" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Nitroman52
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="114997" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=114997&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            325
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position106106">

                                        <td class="position">
                                            2301
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=3&amp;system=285&amp;position=10" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Ceteris Paribus
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="106106" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=106106&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            324
                                        </td>
                                    </tr>

                                    <tr class="
            " id="position112716">

                                        <td class="position">
                                            2302
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=358&amp;position=4" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            RoXio
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112716" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112716&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            324
                                        </td>
                                    </tr>

                                    <tr class="
                            alt
            " id="position112327">

                                        <td class="position">
                                            2303
                                        </td>

                                        <td class="movement">
                            <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                    <span class="stats_counter">(14)</span>
                </span>
                                        </td>
                                        <td class="name">

                                        <span class="ally-tag">
                                                    <a href="allianceInfo.php?allianceId=501204" target="_ally">
                                [Crap]</a>
                                                </span>
                                            <a href="
                    #galaxy&amp;galaxy=6&amp;system=267&amp;position=6" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            Kyng
                                    </span>

                                                <span class="honorScore">
                        (<span class="undermark tooltip js_hideTipOnMobile" title="Honour points">0</span>)
                    </span>
                                            </a>
                                        </td>

                                        <td class="sendmsg">
                                            <div class="sendmsg_content">
                                                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="112327" title="Write message"><span class="icon icon_chat"></span></a>
                                                <a class="tooltip overlay js_hideTipOnMobile icon" title="Buddy request" data-overlay-title="Buddy request" href="#buddies&amp;action=7&amp;id=112327&amp;ajax=1">
                                                    <span class="icon icon_user"></span>
                                                </a>

                                            </div>
                                        </td>

                                        <td class="score
                                             ">
                                            323
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        initHighscoreContent();
                                    });
                                </script>
                                <div class="pagebar">
                                    <a href="javascript:void(0);" class="scrollToTop">Back to top</a>
                                    &nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', '#stat_list_content'); return false;">«</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=18', '#stat_list_content'); return false;">18</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=19', '#stat_list_content'); return false;">19</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=20', '#stat_list_content'); return false;">20</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=21', '#stat_list_content'); return false;">21</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=22', '#stat_list_content'); return false;">22</a>&nbsp; <span class=" activePager">23</span>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=24', '#stat_list_content'); return false;">24</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=25', '#stat_list_content'); return false;">25</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=26', '#stat_list_content'); return false;">26</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=27', '#stat_list_content'); return false;">27</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=28', '#stat_list_content'); return false;">28</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=55', '#stat_list_content'); return false;">»</a>
                                </div>
                            </div>
                        </div>            </div><!--leftcol -->

                </form>
            </div>        <div class="footer"></div>
        </div>
    </div>

@endsection
