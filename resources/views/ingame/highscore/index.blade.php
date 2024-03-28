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
                <h2>Player highscore</h2>
            </div>
            <div class="content">
                <form id="send" name="send" action="#highscore">
                    <div id="scrollToTop" style="left: 760.336px;"><a href="javascript:void(0);" title="Back to top" class="scrollToTop tooltip js_hideTipOnMobile"></a></div>
                    <div id="row">

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
                                    &nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', 'stat_list_content'); return false;">«</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=18', 'stat_list_content'); return false;">18</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=19', 'stat_list_content'); return false;">19</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=20', 'stat_list_content'); return false;">20</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=21', 'stat_list_content'); return false;">21</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=22', 'stat_list_content'); return false;">22</a>&nbsp; <span class=" activePager">23</span>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=24', 'stat_list_content'); return false;">24</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=25', 'stat_list_content'); return false;">25</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=26', 'stat_list_content'); return false;">26</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=27', 'stat_list_content'); return false;">27</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=28', 'stat_list_content'); return false;">28</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=55', 'stat_list_content'); return false;">»</a>
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
                                    @foreach ($highscorePlayers as $highscorePlayer)
                                        <tr class="{{ $highscorePlayer['id'] == $player->getId()  ? 'myrank' : '' }} {{$highscorePlayer['rank'] % 2 == 0  ? 'alt' : ''}}" id="position{{ $highscorePlayer['id'] }}">
                                            <td class="position">
                                                {{ $highscorePlayer['rank'] }}
                                            </td>

                                            <td class="movement">
                                                @if (1 > 5)
                                                    <span class="undermark"><img src="/img/icons/1c7545144452ec3e38c9fba216c4f9.gif" alt="up">
                                                        <span class="stats_counter">(11)</span>
                                                    </span>
                                                @elseif (1 == 1)
                                                    <img src="/img/icons/ea5bf2cc93e52e22e3c1b80c7f7563.gif" alt="stay">
                                                @else
                                                    <span class="overmark">
                                                        <img src="/img/icons/7e6b4e65bec62ac2f10ea24ba76c51.gif" alt="down">
                                                        <span class="stats_counter">(1)</span>
                                                    </span>
                                                @endif

                                            </td>
                                            <td class="name">

                                                <a href="
                    {{ route('galaxy.index', ['galaxy' => $highscorePlayer['planet_coords']['galaxy'], 'system' => $highscorePlayer['planet_coords']['system'], 'position' => $highscorePlayer['planet_coords']['planet']])  }}" class="dark_highlight_tablet">

                <span class="
                                         playername">
                                            {{ $highscorePlayer['name'] }}
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
                                                {{ $highscorePlayer['points_formatted'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <script type="text/javascript">
                                    $(document).ready(function(){
                                        initHighscoreContent();
                                    });
                                </script>
                                <div class="pagebar">
                                    <a href="javascript:void(0);" class="scrollToTop">Back to top</a>
                                    &nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', 'stat_list_content'); return false;">«</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=18', 'stat_list_content'); return false;">18</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=19', 'stat_list_content'); return false;">19</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=20', 'stat_list_content'); return false;">20</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=21', 'stat_list_content'); return false;">21</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=22', 'stat_list_content'); return false;">22</a>&nbsp; <span class=" activePager">23</span>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=24', 'stat_list_content'); return false;">24</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=25', 'stat_list_content'); return false;">25</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=26', 'stat_list_content'); return false;">26</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=27', 'stat_list_content'); return false;">27</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=28', 'stat_list_content'); return false;">28</a>&nbsp; <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=55', 'stat_list_content'); return false;">»</a>
                                </div>
                            </div>
                        </div>            </div><!--leftcol -->

                </form>
            </div>        <div class="footer"></div>
        </div>
    </div>

@endsection
