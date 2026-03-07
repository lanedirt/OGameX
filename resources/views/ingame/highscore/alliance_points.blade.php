<div id="content">
    <div>
        <script type="text/javascript">
            var currentCategory = 2;
            var currentType = {{ $highscoreCurrentType }};
            var site = {{ $highscoreCurrentPage }};
            var searchSite = {{ $highscoreCurrentPage }};
            var resultsPerPage = 100;
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
            @if($currentUserAllianceId)
                <option value="{{ $highscoreCurrentAlliancePage }}">{{ __('t_ingame.highscore.own_position') }}</option>
            @endif
            @for ($i = 1; $i <= ceil($highscoreAllianceAmount / 100); $i++)
                <option {{ $i == $highscoreCurrentPage ? 'selected="selected"' : '' }} value="{{ $i }}"> {{ ((($i-1) * 100) + 1)  }} - {{ $i * 100 }}</option>
            @endfor
        </select>
        <div class="fleft" id="highscoreHeadline">
            @if($highscoreCurrentType == 0)
                {{ __('t_ingame.highscore.points') }}
            @elseif($highscoreCurrentType == 1)
                {{ __('t_ingame.highscore.economy') }}
            @elseif($highscoreCurrentType == 2)
                {{ __('t_ingame.highscore.research') }}
            @elseif($highscoreCurrentType == 3)
                {{ __('t_ingame.highscore.military') }}
            @endif
        </div>

        <table id="ranks" class="allyHighscore">
            <thead>
            <tr>
                <td class="position">
                    {{ __('t_ingame.highscore.position') }}
                </td>
                <td class="movement"></td>
                <td class="name">
                    {{ __('t_ingame.highscore.alliance') }}
                </td>
                <td class="member_count" align="center">
                    {{ __('t_ingame.highscore.member') }}
                </td>
                <td align="center" class="score tooltip js_hideTipOnMobile" title="{{ __('t_ingame.highscore.average_points') }}">
                    {{ __('t_ingame.highscore.points') }}
                </td>
            </tr>
            </thead>
            <tbody>
            @forelse ($highscoreAlliances as $highscoreAlliance)
                <tr class="{{ $highscoreAlliance['id'] == $currentUserAllianceId ? 'myrank' : '' }} {{ $highscoreAlliance['rank'] % 2 == 0 ? 'alt' : '' }}" id="position{{ $highscoreAlliance['id'] }}">
                    <td class="position">
                        {{ $highscoreAlliance['rank'] }}
                    </td>

                    <td class="movement">
                        <img src="/img/icons/ea5bf2cc93e52e22e3c1b80c7f7563.gif" alt="stay">
                    </td>

                    <td class="name">
                        <div class="ally-name">
                            <span>{{ $highscoreAlliance['name'] }}</span>
                        </div>
                        <div class="ally-tag">
                            <a href="{{ route('alliance.info', ['alliance_id' => $highscoreAlliance['id']]) }}" target="_blank" class="txt_link">[{{ $highscoreAlliance['tag'] }}]</a>
                        </div>
                    </td>

                    <td class="member_count" align="center">
                        {{ $highscoreAlliance['member_count'] }}
                    </td>

                    <td class="score">
                        {{ $highscoreAlliance['points_formatted'] }}
                        <div class="small">ø{{ $highscoreAlliance['average_points_formatted'] }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">{{ __('t_ingame.highscore.no_alliances_found') }}</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <script type="text/javascript">
            $(document).ready(function(){
                initHighscoreContent();
                initHighscore();
            });
        </script>
        <div class="pagebar">
            <a href="javascript:void(0);" class="scrollToTop">{{ __('t_ingame.layout.back_to_top') }}</a>
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
