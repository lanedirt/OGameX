<div id="content">
    <div>
        <script type="text/javascript">
            var currentCategory = 2;
            var currentType = {{ $highscoreCurrentType }};
            var searchPosition = 113970;
            var site = {{ $highscoreCurrentPage }};
            var searchSite = {{ $highscoreCurrentPage }};
            var resultsPerPage = 100;
            var searchRelId = 113970;
        </script>

        <div class="pagebar">
            @if ($highscoreCurrentPage > 1)
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}#ategory=1&amp;type=0&amp;searchRelId=113970&amp;site=1', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType, 'category' => 2]) }}#highscoreContent&amp;category=2&amp;type=0&amp;searchRelId=113970&amp;site={{ $i }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => floor($highscorePlayerAmount / 100) + 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}#highscoreContent&amp;category=2&amp;type=0&amp;searchRelId=113970&amp;site=x', '#stat_list_content'); return false;">»</a>
            @endif
        </div>
        <select class="changeSite fright">
            <option value="{{ $highscoreCurrentPlayerPage }}">Own position</option>
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                <option {{ $i == $highscoreCurrentPage ? 'selected="selected"' : '' }} value="{{ $i }}"> {{ ((($i-1) * 100) + 1)  }} - {{ $i * 100 }}</option>
            @endfor
        </select>
        <div class="fleft" id="highscoreHeadline">
            Points
        </div>

        <table id="ranks" class="allyHighscore">
            <thead>
            <tr>
                <td class="position">
                    Position
                </td>
                <td class="movement"></td>
                <td class="name">
                    Alliance
                </td>


                <td class="member_count" align="center">
                    Member
                </td>
                <td align="center" class="score tooltip js_hideTipOnMobile" title="Average points">
                    Points
                </td>
            </tr>
            </thead>
            <tbody>
            <tr class="
                        " id="position500665">
                <td class="position">
                    1
                </td>

                <td class="movement">
                            <span class="overmark">
                    <img src="/img/icons/7e6b4e65bec62ac2f10ea24ba76c51.gif" alt="down">
                    <span class="stats_counter">(1)</span>
                </span>
                </td>

                <td class="name">
                    <div class="ally-name">
                    <span title="Traders" class="alliance_class small trader">
                        BDOffline
                    </span>
                    </div>
                    <div class="ally-tag">
                        <a href="allianceInfo.php?allianceId=500665" target="_ally">
                            [BDO]
                        </a>
                    </div>
                </td>



                <td class="member_count" align="center">
                    11
                </td>

                <td class="score">
                    1,068,614,555
                    <div class="small">ø97,146,778
                    </div></td>
            </tr>
            <tr class="
                         alt
                        " id="position500044">
                <td class="position">
                    2
                </td>

                <td class="movement">
                            <span class="overmark">
                    <img src="/img/icons/7e6b4e65bec62ac2f10ea24ba76c51.gif" alt="down">
                    <span class="stats_counter">(2)</span>
                </span>
                </td>

                <td class="name">
                    <div class="ally-name">
                    <span title="Traders" class="alliance_class small trader">
                        No Waste Of Time_IT
                    </span>
                    </div>
                    <div class="ally-tag">
                        <a href="allianceInfo.php?allianceId=500044" target="_ally">
                            [NWOT_IT]
                        </a>
                    </div>
                </td>



                <td class="member_count" align="center">
                    19
                </td>

                <td class="score">
                    954,016,496
                    <div class="small">ø50,211,395
                    </div></td>
            </tr>

            @foreach ($highscorePlayers as $highscorePlayer)
                <!--<tr class="{{ $highscorePlayer['id'] == $player->getId()  ? 'myrank' : '' }} {{$highscorePlayer['rank'] % 2 == 0  ? 'alt' : ''}}" id="position{{ $highscorePlayer['id'] }}">
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
{{ route('galaxy.index', ['galaxy' => $highscorePlayer['planet_coords']->galaxy, 'system' => $highscorePlayer['planet_coords']->system, 'position' => $highscorePlayer['planet_coords']->planet])  }}" class="dark_highlight_tablet">

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
                </tr>-->
            @endforeach
            </tbody>
        </table>

        <script type="text/javascript">
            $(document).ready(function(){
                initHighscoreContent();
                initHighscore();
            });
        </script>
        <div class="pagebar">
            <a href="javascript:void(0);" class="scrollToTop">Back to top</a>
            &nbsp;
            @if ($highscoreCurrentPage > 1)
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}#ategory=1&amp;type=0&amp;searchRelId=113970&amp;site=1', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType, 'category' => 2]) }}#highscoreContent&amp;category=2&amp;type=0&amp;searchRelId=113970&amp;site={{ $i }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => floor($highscorePlayerAmount / 100) + 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}#highscoreContent&amp;category=2&amp;type=0&amp;searchRelId=113970&amp;site=x', '#stat_list_content'); return false;">»</a>
            @endif
        </div>
    </div>
</div>
