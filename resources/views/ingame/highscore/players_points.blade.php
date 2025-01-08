<div id="content">
    <div>
        <script type="text/javascript">
            var currentCategory = 1;
            var currentType = {{ $highscoreCurrentType }};
            var searchPosition = {{ $player->getId() }};
            var site = {{ $highscoreCurrentPage }};
            var searchSite = {{ $highscoreCurrentPage }};
            var resultsPerPage = 100;
            var searchRelId = {{ $player->getId() }};
        </script>

        <div class="pagebar">
            @if ($highscoreCurrentPage > 1)
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => floor($highscorePlayerAmount / 100) + 1, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">»</a>
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
                <tr class="{{ $highscorePlayer['id'] == $player->getId()  ? 'myrank' : ($highscorePlayer['rank'] % 2 == 0  ? 'alt' : '') }}" id="position{{ $highscorePlayer['id'] }}">
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
{{ route('galaxy.index', ['galaxy' => $highscorePlayer['planet_coords']->galaxy, 'system' => $highscorePlayer['planet_coords']->system, 'position' => $highscorePlayer['planet_coords']->position])  }}" class="dark_highlight_tablet">

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
                initHighscore();
            });
        </script>
        <div class="pagebar">
            <a href="javascript:void(0);" class="scrollToTop">Back to top</a>
            &nbsp;
            @if ($highscoreCurrentPage > 1)
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < floor($highscorePlayerAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => floor($highscorePlayerAmount / 100) + 1, 'type' => $highscoreCurrentType]) }}', '#stat_list_content'); return false;">»</a>
            @endif
        </div>
    </div>
</div>
