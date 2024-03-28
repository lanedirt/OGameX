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
                                    @if ($highscoreCurrentPage > 1)
                                        <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', 'stat_list_content'); return false;">«</a>&nbsp;
                                    @endif
                                    @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                                        @if ($highscoreCurrentPlayerPage == $i)
                                            <span class=" activePager">{{ $i }}</span>
                                        @else
                                            <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site={{ $i }}', 'stat_list_content'); return false;">
                                                {{ $i }}
                                            </a>
                                        @endif
                                        &nbsp;
                                    @endfor
                                    @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                                        <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site={{ floor($highscorePlayerAmount / 100) + 1 }}', 'stat_list_content'); return false;">»</a>
                                    @endif
                                </div>
                                <select class="changeSite fright">
                                    <option value="{{ $highscoreCurrentPlayerPage }}">Own position</option>
                                    @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                                        <option value="{{ $i }}"> {{ ((($i-1) * 100) + 1)  }} - {{ $i * 100 }}</option>
                                    @endfor
                                </select>
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
                                    &nbsp;
                                    @if ($highscoreCurrentPage > 1)
                                        <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site=1', 'stat_list_content'); return false;">«</a>&nbsp;
                                    @endif
                                    @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                                        @if ($highscoreCurrentPlayerPage == $i)
                                            <span class=" activePager">{{ $i }}</span>
                                        @else
                                            <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site={{ $i }}', 'stat_list_content'); return false;">
                                                {{ $i }}
                                            </a>
                                        @endif
                                        &nbsp;
                                    @endfor
                                    @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                                        <a href="javascript:void(0);" class="" onclick="ajaxCall('#highscoreContent&amp;category=1&amp;type=0&amp;searchRelId=113970&amp;site={{ floor($highscorePlayerAmount / 100) + 1 }}', 'stat_list_content'); return false;">»</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div><!--leftcol -->
                </form>
            </div>
            <div class="footer"></div>
        </div>
    </div>

@endsection
