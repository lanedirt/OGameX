@if(isset($ignored_players) && $ignored_players->isNotEmpty())
    @foreach($ignored_players as $index => $ignoredPlayer)
        @php
            $ignoredUser = $ignoredPlayer->ignoredUser;

            // Get player service for the ignored user
            $ignoredPlayerService = app(\OGame\Services\PlayerService::class, ['player_id' => $ignoredUser->id]);

            // Get player statistics
            $highscoreService = app(\OGame\Services\HighscoreService::class);
            $playerRank = $highscoreService->getHighscorePlayerRank($ignoredPlayerService);

            // Row class for zebra striping
            $rowClass = $index % 2 === 0 ? 'odd' : 'even';
        @endphp
        <tr class="{{ $rowClass }}">
            <td class="no ct_td">{{ $index + 1 }}.</td>
            <td class="ct_td">{{ $ignoredUser->username }}</td>
            <td class="ct_td">
                <a class="txt_link" href="{{ route('highscore.index', ['searchRelId' => $ignoredUser->id, 'category' => 1]) }}">
                    {{ number_format($playerRank) }}
                </a>
            </td>
            <td class="ct_td">
                {{-- TODO: Implement alliance system. Display alliance tag here when available. --}}
                <span class="ally-tag dark_highlight_tablet">
                    <span class="txt_link">-</span>
                </span>
            </td>
            <td class="ct_td textCenter">
                <form action="{{ route('buddies.unignore') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="ignored_user_id" value="{{ $ignoredUser->id }}">
                    <button type="submit" class="tooltip js_hideTipOnMobile icon_link" data-tooltip-title="Remove from ignore list" style="background: none; border: none; cursor: pointer; padding: 0;">
                        <span class="icon icon_trash"></span>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="textCenter">
            <p class="box_highlight textCenter">No ignored players</p>
        </td>
    </tr>
@endif
