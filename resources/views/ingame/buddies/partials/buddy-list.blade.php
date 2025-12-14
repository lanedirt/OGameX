@if(isset($buddies) && $buddies->isNotEmpty())
    @foreach($buddies as $index => $buddyRequest)
        @php
            // Determine which user is the buddy (not the current user)
            $buddy = $buddyRequest->sender_user_id === auth()->id()
                ? $buddyRequest->receiver
                : $buddyRequest->sender;

            // Determine online/offline status
            $isOnline = $buddy->isOnline();
            $statusClass = $isOnline ? 'online' : 'offline';
            $statusTitle = $isOnline ? 'On' : 'Off';

            // Get player service for the buddy
            $buddyPlayer = app(\OGame\Services\PlayerService::class, ['player_id' => $buddy->id]);

            // Get player statistics
            $highscoreService = app(\OGame\Services\HighscoreService::class);
            $playerRank = $highscoreService->getHighscorePlayerRank($buddyPlayer);
            $playerScore = $highscoreService->getPlayerScore($buddyPlayer);

            // Get homeworld coordinates
            $homeworldPlanet = $buddyPlayer->planets->first();
            if ($homeworldPlanet) {
                $coords = $homeworldPlanet->getPlanetCoordinates();
            } else {
                $coords = null;
            }

            // Row class for zebra striping
            $rowClass = $index % 2 === 0 ? 'odd' : 'even';
        @endphp
        <tr class="{{ $rowClass }}">
            <td class="no ct_td">{{ $index + 1 }}.</td>
            <td class="ct_td">
                <span class="tooltip fleft playerstatus {{ $statusClass }}" data-tooltip-title="{{ $statusTitle }}"></span>
                <span class="fleft buddylist_playername">
                    {{ $buddy->username }}
                </span>
            </td>
            <td class="ct_td">{{ number_format($playerScore) }}</td>
            <td class="ct_td">
                <a class="txt_link" href="{{ route('highscore.index', ['searchRelId' => $buddy->id, 'category' => 1]) }}">
                    {{ number_format($playerRank) }}
                </a>
            </td>
            <td class="ct_td">
                {{-- TODO: Implement alliance system. Display alliance tag here when available. --}}
                <span class="ally-tag dark_highlight_tablet">
                    <span class="txt_link">-</span>
                </span>
            </td>
            <td class="ct_td">
                @if($coords)
                    <span class="dark_highlight_tablet">
                        <a class="txt_link" href="{{ route('galaxy.index', ['galaxy' => $coords->galaxy, 'system' => $coords->system]) }}">
                            [{{ $coords->asString() }}]
                        </a>
                    </span>
                @else
                    <span class="dark_highlight_tablet">N/A</span>
                @endif
            </td>
            <td class="ct_td textCenter">
                <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $buddy->id }}" data-tooltip-title="{{ __('t_buddies.ui.send_message') }}">
                    <span class="icon icon_chat"></span>
                </a>
                <span class="tooltip js_hideTipOnMobile deleteBuddy icon_link" id="{{ $buddy->id }}" ref="{{ __('t_buddies.action.confirm_delete_buddy') }} {{ $buddy->username }}?" onclick="deleteBuddy.call(this); return false;" data-tooltip-title="{{ __('t_buddies.action.delete_buddy') }}">
                    <span class="icon icon_trash"></span>
                </span>
            </td>
        </tr>
    @endforeach
@elseif(isset($search_results) && $search_results->isNotEmpty())
    @foreach($search_results as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->username }}</td>
            <td>
                <a href="#" class="txt_link" onclick="sendBuddyRequest({{ $user->id }}); return false;">
                    <span>{{ __('t_buddies.action.add_as_buddy') }}</span>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7" class="textCenter">
            <p class="box_highlight textCenter no_buddies">No buddies found</p>
        </td>
    </tr>
@endif
