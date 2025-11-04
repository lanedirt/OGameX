<div id="content">
    <div>
        <script type="text/javascript">
            var currentCategory = 2;
            var currentType = {{ $highscoreCurrentType }};
            var searchPosition = {{ $highscoreCurrentAllianceRank }};
            var site = {{ $highscoreCurrentPage }};
            var searchSite = {{ $highscoreCurrentPage }};
            var resultsPerPage = 100;
            var searchRelId = {{ $highscoreCurrentAllianceRank }};
        </script>

        <div class="pagebar">
            @if ($highscoreCurrentPage > 1)
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscoreAllianceAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < ceil($highscoreAllianceAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => ceil($highscoreAllianceAmount / 100), 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">»</a>
            @endif
        </div>
        <select class="changeSite fright">
            @if($playerAlliance)
                <option value="{{ $highscoreCurrentAlliancePage }}">Own position</option>
            @endif
            @for ($i = 1; $i <= ceil($highscoreAllianceAmount / 100); $i++)
                <option {{ $i == $highscoreCurrentPage ? 'selected="selected"' : '' }} value="{{ $i }}"> {{ ((($i-1) * 100) + 1)  }} - {{ min($i * 100, $highscoreAllianceAmount) }}</option>
            @endfor
        </select>
        <div class="fleft" id="highscoreHeadline">
            @if($highscoreCurrentType == 0)
                Points
            @elseif($highscoreCurrentType == 1)
                Economy
            @elseif($highscoreCurrentType == 2)
                Research
            @elseif($highscoreCurrentType == 3)
                Military
            @endif
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
                    Members
                </td>
                <td align="center" class="score tooltip js_hideTipOnMobile" title="Average points">
                    Points
                </td>
            </tr>
            </thead>
            <tbody>
            @foreach ($highscoreAlliances as $alliance)
                <tr class="{{ $playerAlliance && $alliance['id'] == $playerAlliance->id ? 'myrank' : '' }} {{$alliance['rank'] % 2 == 0  ? 'alt' : ''}}" id="position{{ $alliance['id'] }}">
                    <td class="position">
                        {{ $alliance['rank'] }}
                    </td>

                    <td class="movement">
                        <img src="/img/icons/ea5bf2cc93e52e22e3c1b80c7f7563.gif" alt="stay">
                    </td>

                    <td class="name">
                        <div class="ally-name">
                            {{ $alliance['name'] }}
                        </div>
                        <div class="ally-tag">
                            <a href="{{ route('alliance.show', $alliance['id']) }}">
                                [{{ $alliance['tag'] }}]
                            </a>
                        </div>
                    </td>

                    <td class="member_count" align="center">
                        {{ $alliance['member_count'] }}
                    </td>

                    <td class="score">
                        {{ $alliance['points_formatted'] }}
                        <div class="small">ø{{ number_format($alliance['average_points']) }}
                        </div>
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
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => 1, 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">«</a>&nbsp;
            @endif
            @for ($i = 1; $i <= ceil($highscoreAllianceAmount / 100); $i++)
                @if ($highscoreCurrentPage == $i)
                    <span class=" activePager">{{ $i }}</span>
                @else
                    <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => $i, 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">
                        {{ $i }}
                    </a>
                @endif
                &nbsp;
            @endfor
            @if ($highscoreCurrentPage < ceil($highscoreAllianceAmount / 100))
                <a href="javascript:void(0);" class="" onclick="ajaxCall('{{ route('highscore.ajax', ['page' => ceil($highscoreAllianceAmount / 100), 'type' => $highscoreCurrentType, 'category' => 2]) }}', '#stat_list_content'); return false;">»</a>
            @endif
        </div>
    </div>
</div>
