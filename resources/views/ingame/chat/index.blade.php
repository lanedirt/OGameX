@extends('ingame.layouts.main')

@section('content')

    <div id="planet" class="shortHeader">
        <h2>Chat</h2>
    </div>

    @if($chatPartner)
        {{-- Chat thread view: shown when a player is selected --}}
        <div id="chatContent" class="contentbox fleft js_chatHistory" data-chatplayerid="{{ $chatPartner->id }}">
            <div class="header">
                <span class="c-left"></span>
                <span class="c-right"></span>
                <a href="{{ route('chat.index') }}" class="chat_back">
                    <span class="icon icon_reply"></span>
                </a>
                <span class="status">
                    <span class="tooltip icon icon_user @if(!$isBuddy) grayscale @endif" data-tooltip-title="Buddy"></span>
                    <span class="tooltip icon allianceMember @if(!$isAllianceMember) grayscale @endif" data-tooltip-title="Your alliance"></span>
                    <span class="tooltip playerstatus {{ ($isBuddy || $isAllianceMember) ? ($chatPartner->isOnline() ? 'online' : 'offline') : 'disallowed' }}" data-tooltip-title="{{ ($isBuddy || $isAllianceMember) ? ($chatPartner->isOnline() ? 'online' : 'offline') : 'Status not visible' }}"></span>
                </span>
                <span id="chatpartner" class="tooltipHTML js_hideTipOnMobile" title="Highscore ranking: {{ $chatPartnerRank }}|Alliance: {{ $chatPartnerAlliance ? e($chatPartnerAlliance->alliance_name) : '-' }}">
                    @if($chatPartnerAlliance)<a href="{{ route('alliance.index') }}" id="otherPlayerAllianceTag">{{ $chatPartnerAlliance->alliance_tag }}</a> @endif<a href="{{ route('chat.index', ['playerId' => $chatPartner->id]) }}" id="otherPlayerName">{{ $chatPartner->username }}</a>
                    @if(isset($chatPartnerPlanet) && $chatPartnerPlanet)
                        <a href="{{ route('galaxy.index', ['galaxy' => $chatPartnerPlanet->galaxy, 'system' => $chatPartnerPlanet->system]) }}" class="txt_link">
                            <img src="{{ asset($chatPartnerPlanetImage) }}" width="16" height="16" alt="Planet">
                            <span>[{{ $chatPartnerPlanet->galaxy }}:{{ $chatPartnerPlanet->system }}:{{ $chatPartnerPlanet->planet }}]</span>
                        </a>
                    @endif
                </span>
            </div>
            <div class="content clearfix">
                <div class="largeChatContainer chat_bar_list">
                    <ul class="chat clearfix largeChat" data-playerid="{{ $chatPartner->id }}">
                        @foreach($chatMessages as $message)
                            <li class="chat_msg @if($message->sender_id === (int) auth()->id()) odd @endif" data-chat-id="{{ $message->id }}">
                                <div class="msg_head">
                                    <span class="msg_title blue_txt">
                                        {{ $message->sender->username }}
                                    </span>
                                    <span class="msg_date fright">{{ $message->created_at->format('d.m.Y H:i:s') }}</span>
                                </div>
                                <span class="msg_content">{!! nl2br(e($message->message)) !!}</span>
                                <div class="speechbubble_arrow"></div>
                            </li>
                        @endforeach
                        @if($chatMessages->isEmpty())
                            <li class="chat_msg">
                                <span class="msg_content">No messages yet. Start the conversation!</span>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="replyText" data-msgid="0"></div>
                <div class="editor_wrap">
                    <div><textarea name="text" class="new_msg_textarea"></textarea></div>
                    <a href="#" class="btn_blue fright send_new_msg">Submit</a>
                </div>
            </div>
            <div class="footer">
                <div class="c-right"></div>
                <div class="c-left"></div>
            </div>
        </div>

        <script type="text/javascript">
        $(document).ready(function() {
            initBBCodeEditor(locaKeys, itemNames, false, '.new_msg_textarea', 2000, true);
            initBBCodes();

            var chatPlayerId = {{ $chatPartner->id }};
            var currentUserId = {{ auth()->id() }};
            var currentUserName = '{{ e(auth()->user()->username) }}';
            var $chatList = $('ul.chat.largeChat');

            function formatDate(timestamp) {
                var d = new Date(timestamp * 1000);
                var pad = function(n) { return n < 10 ? '0' + n : n; };
                return pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }

            function appendMessage(id, senderName, text, date, isOwn) {
                var $msg = $('<li class="chat_msg' + (isOwn ? ' odd' : '') + '" data-chat-id="' + id + '">' +
                    '<div class="msg_head">' +
                        '<span class="msg_title blue_txt">' + $('<span>').text(senderName).html() + '</span>' +
                        '<span class="msg_date fright">' + formatDate(date) + '</span>' +
                    '</div>' +
                    '<span class="msg_content">' + text.replace(/\n/g, '<br>') + '</span>' +
                    '<div class="speechbubble_arrow"></div>' +
                '</li>');

                // Remove "No messages yet" placeholder
                $chatList.find('.chat_msg:not([data-chat-id])').remove();
                $chatList.append($msg);

                // Scroll to bottom
                var $container = $chatList.closest('.largeChatContainer');
                $container.scrollTop($container[0].scrollHeight);
            }

            // Scroll to bottom on load (only if content overflows)
            var $container = $chatList.closest('.largeChatContainer');
            if ($container[0].scrollHeight > $container[0].clientHeight) {
                $container.scrollTop($container[0].scrollHeight);
            }

            // Send message
            $('.send_new_msg').on('click', function(e) {
                e.preventDefault();
                var text = $('.new_msg_textarea').val();
                if (text.trim() === '') return;

                var $btn = $(this);
                $btn.addClass('disabled');

                $.ajax({
                    url: '{{ route("chat.send") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mode: 1,
                        playerId: chatPlayerId,
                        text: text
                    },
                    success: function(response) {
                        if (response.status === 'OK') {
                            appendMessage(response.id, currentUserName, response.text, response.date, true);
                            $('.new_msg_textarea').val('');
                            // Update character count
                            $('.cnt_chars').html(2000);
                        }
                        $btn.removeClass('disabled');
                    },
                    error: function() {
                        $btn.removeClass('disabled');
                    }
                });
            });

            // Submit on Enter (without Shift)
            $('.new_msg_textarea').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $('.send_new_msg').click();
                }
            });

            // Poll for new messages every 5 seconds
            function getLatestChatId() {
                var ids = $chatList.find('li[data-chat-id]').map(function() {
                    return parseInt($(this).data('chat-id'), 10);
                }).get();
                return ids.length ? Math.max.apply(null, ids) : 0;
            }

            setInterval(function() {
                $.ajax({
                    url: '{{ route("chat.history") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mode: 2,
                        playerId: chatPlayerId,
                        updateUnread: 1
                    },
                    success: function(response) {
                        if (response.chatItemsByDateAsc && response.chatItems) {
                            var latestId = getLatestChatId();
                            $.each(response.chatItemsByDateAsc, function(i, chatId) {
                                var id = parseInt(chatId, 10);
                                if (id > latestId) {
                                    var item = response.chatItems[chatId];
                                    var isOwn = item.altClass === 'odd';
                                    appendMessage(id, item.playerName, item.chatContent, item.date, isOwn);
                                }
                            });
                        }
                    }
                });
            }, 5000);
        });
        </script>
    @elseif(isset($chatAllianceId) && $chatAllianceId)
        {{-- Alliance chat thread view --}}
        <div id="chatContent" class="contentbox fleft js_chatHistory" data-associationid="{{ $chatAllianceId }}">
            <div class="header">
                <span class="c-left"></span>
                <span class="c-right"></span>
                <a href="{{ route('chat.index') }}" class="chat_back">
                    <span class="icon icon_reply"></span>
                </a>
                <span class="status">
                    <span class="tooltip icon allianceMember" data-tooltip-title="Your alliance"></span>
                    <span class="tooltip playerstatus online" data-tooltip-title="online"></span>
                </span>
                <span id="chatpartner">
                    <span id="otherPlayerName" style="color: orange">{{ $alliance->alliance_tag }} - Alliance Chat</span>
                </span>
            </div>
            <div class="content clearfix">
                <div class="largeChatContainer chat_bar_list">
                    <ul class="chat clearfix largeChat" data-associationid="{{ $chatAllianceId }}">
                        @foreach($chatAllianceMessages as $message)
                            <li class="chat_msg @if($message->sender_id === (int) auth()->id()) odd @endif" data-chat-id="{{ $message->id }}">
                                <div class="msg_head">
                                    <span class="msg_title blue_txt">
                                        {{ $message->sender->username }}
                                    </span>
                                    <span class="msg_date fright">{{ $message->created_at->format('d.m.Y H:i:s') }}</span>
                                </div>
                                <span class="msg_content">{!! nl2br(e($message->message)) !!}</span>
                                <div class="speechbubble_arrow"></div>
                            </li>
                        @endforeach
                        @if($chatAllianceMessages->isEmpty())
                            <li class="chat_msg">
                                <span class="msg_content">No messages yet. Start the conversation!</span>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="replyText" data-msgid="0"></div>
                <div class="editor_wrap">
                    <div><textarea name="text" class="new_msg_textarea"></textarea></div>
                    <a href="#" class="btn_blue fright send_new_msg">Submit</a>
                </div>
            </div>
            <div class="footer">
                <div class="c-right"></div>
                <div class="c-left"></div>
            </div>
        </div>

        <script type="text/javascript">
        $(document).ready(function() {
            initBBCodeEditor(locaKeys, itemNames, false, '.new_msg_textarea', 2000, true);
            initBBCodes();

            var chatAllianceId = {{ $chatAllianceId }};
            var currentUserId = {{ auth()->id() }};
            var currentUserName = '{{ e(auth()->user()->username) }}';
            var $chatList = $('ul.chat.largeChat');

            function formatDate(timestamp) {
                var d = new Date(timestamp * 1000);
                var pad = function(n) { return n < 10 ? '0' + n : n; };
                return pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }

            function appendMessage(id, senderName, text, date, isOwn) {
                var $msg = $('<li class="chat_msg' + (isOwn ? ' odd' : '') + '" data-chat-id="' + id + '">' +
                    '<div class="msg_head">' +
                        '<span class="msg_title blue_txt">' + $('<span>').text(senderName).html() + '</span>' +
                        '<span class="msg_date fright">' + formatDate(date) + '</span>' +
                    '</div>' +
                    '<span class="msg_content">' + text.replace(/\n/g, '<br>') + '</span>' +
                    '<div class="speechbubble_arrow"></div>' +
                '</li>');

                $chatList.find('.chat_msg:not([data-chat-id])').remove();
                $chatList.append($msg);

                var $container = $chatList.closest('.largeChatContainer');
                $container.scrollTop($container[0].scrollHeight);
            }

            // Scroll to bottom on load (only if content overflows)
            var $container = $chatList.closest('.largeChatContainer');
            if ($container[0].scrollHeight > $container[0].clientHeight) {
                $container.scrollTop($container[0].scrollHeight);
            }

            // Send message
            $('.send_new_msg').on('click', function(e) {
                e.preventDefault();
                var text = $('.new_msg_textarea').val();
                if (text.trim() === '') return;

                var $btn = $(this);
                $btn.addClass('disabled');

                $.ajax({
                    url: '{{ route("chat.send") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mode: 3,
                        associationId: chatAllianceId,
                        text: text
                    },
                    success: function(response) {
                        if (response.status === 'OK') {
                            appendMessage(response.id, currentUserName, response.text, response.date, true);
                            $('.new_msg_textarea').val('');
                            $('.cnt_chars').html(2000);
                        }
                        $btn.removeClass('disabled');
                    },
                    error: function() {
                        $btn.removeClass('disabled');
                    }
                });
            });

            // Submit on Enter (without Shift)
            $('.new_msg_textarea').on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    $('.send_new_msg').click();
                }
            });

            // Poll for new messages every 5 seconds
            function getLatestChatId() {
                var ids = $chatList.find('li[data-chat-id]').map(function() {
                    return parseInt($(this).data('chat-id'), 10);
                }).get();
                return ids.length ? Math.max.apply(null, ids) : 0;
            }

            setInterval(function() {
                $.ajax({
                    url: '{{ route("chat.history") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mode: 4,
                        associationId: chatAllianceId
                    },
                    success: function(response) {
                        if (response.chatItemsByDateAsc && response.chatItems) {
                            var latestId = getLatestChatId();
                            $.each(response.chatItemsByDateAsc, function(i, chatId) {
                                var id = parseInt(chatId, 10);
                                if (id > latestId) {
                                    var item = response.chatItems[chatId];
                                    var isOwn = item.altClass === 'odd';
                                    appendMessage(id, item.playerName, item.chatContent, item.date, isOwn);
                                }
                            });
                        }
                    }
                });
            }, 5000);
        });
        </script>
    @else
        {{-- Chat list view: shown by default --}}
        <div id="chatList" class="contentbox fleft">
            <h2 class="header">
                <span class="c-right"></span>
                <span class="c-left"></span>
                List of your chats
            </h2>
            <div class="content clearfix">
                <div id="chatMsgListContainer">
                    <ul id="chatMsgList">
                        @if($alliance)
                            <li class="msg last" data-associationid="{{ $alliance->id }}">
                                <a href="{{ route('chat.index', ['allianceId' => $alliance->id]) }}" style="text-decoration: none; color: inherit; display: block;">
                                    <div class="msg_status"></div>
                                    <div class="msg_head">
                                        <span class="status">
                                            <span class="tooltip icon allianceMember" data-tooltip-title="Your alliance"></span>
                                            <span title="" class="tooltip playerstatus blank"></span>
                                            <span class="icon" style="background: none;"></span>
                                        </span>
                                        <span class="msg_title blue_txt">
                                            {{ $alliance->alliance_tag }}
                                            <span style="color: orange">Alliance Chat</span>
                                        </span>
                                        <span class="msg_date fright">@if($latestAllianceMessage){{ $latestAllianceMessage->created_at->format('d.m.Y H:i:s') }}@endif</span><br>
                                    </div>
                                    <span class="msg_content">@if($latestAllianceMessage)<strong>{{ $latestAllianceMessage->sender->username }}:</strong> {{ \Illuminate\Support\Str::limit($latestAllianceMessage->message, 100) }}@else Alliance group chat @endif
                                        <span class="msg_content_fadeout"></span>
                                    </span>
                                    <span class="detail_arrow fright">
                                        <span class="new_msg_count noMessage" data-associationid="{{ $alliance->id }}" data-new-messages="0">
                                            0
                                        </span>
                                    </span>
                                </a>
                            </li>
                            <hr>
                        @endif
                        @foreach($conversations as $index => $conversation)
                            <li class="msg @if($loop->last) last @endif" data-playerid="{{ $conversation['partner_id'] }}">
                                <a href="{{ route('chat.index', ['playerId' => $conversation['partner_id']]) }}" style="text-decoration: none; color: inherit; display: block;">
                                    <div class="msg_status"></div>
                                    <div class="msg_head">
                                        <span class="status">
                                            <span class="tooltip icon icon_user grayscale" data-tooltip-title=""></span>
                                            <span class="tooltip playerstatus disallowed" data-tooltip-title="Status not visible"></span>
                                        </span>
                                        <span class="msg_title blue_txt">
                                            {{ $conversation['partner_name'] }}
                                        </span>
                                        <span class="msg_date fright">{{ $conversation['last_message_date']->format('d.m.Y H:i:s') }}</span><br>
                                    </div>
                                    <span class="msg_content">{{ \Illuminate\Support\Str::limit($conversation['last_message'], 120) }}
                                        <span class="msg_content_fadeout"></span>
                                    </span>
                                    <span class="detail_arrow fright">
                                        <span class="new_msg_count @if($conversation['unread_count'] === 0) noMessage @endif" data-playerid="{{ $conversation['partner_id'] }}" data-new-messages="{{ $conversation['unread_count'] }}">
                                            {{ $conversation['unread_count'] }}
                                        </span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                        @if(empty($conversations) && !$alliance)
                            <li class="msg last">
                                <div class="msg_head">
                                    <span class="msg_title">No conversations yet.</span>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="footer">
                <div class="c-right"></div>
                <div class="c-left"></div>
            </div>
        </div>
    @endif

    <div id="sideBar">
        <div class="js_playerlist pl_container contentbox fleft">
            <h2 class="header">
                <span class="c-right"></span>
                <span class="c-left"></span>
                Player list
            </h2>
            <div class="content">
                <div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" style="overflow: hidden;" role="tablist">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab" tabindex="0">
                        <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Buddies
                    </h3>
                    <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" role="tabpanel" style="padding: 0px; overflow: hidden;">
                        <div class="playerlist_top_box"></div>
                        <div class="scrollContainer">
                            <ul class="playerlist">
                                @if($buddyUsers->isEmpty())
                                    <li class="no_buddies">No buddies</li>
                                @else
                                    @foreach($buddyUsers as $buddy)
                                        <li class="playerlist_item @if($loop->iteration % 2 === 0) odd @endif @if($chatPartner && $chatPartner->id === $buddy->id) active @endif" data-playerid="{{ $buddy->id }}" data-filterchatactive="off" data-filteronline="off">
                                            <a href="{{ route('chat.index', ['playerId' => $buddy->id]) }}" style="text-decoration: none; color: inherit; display: block;">
                                                <p class="playername">
                                                    <span class="playerstatus tooltip {{ $buddy->isOnline() ? 'online' : 'offline' }}" data-tooltip-title="{{ $buddy->isOnline() ? 'online' : 'offline' }}">
                                                    </span>
                                                    {{ $buddy->username }}
                                                </p>
                                                <span class="new_msg_count noMessage" data-playerid="{{ $buddy->id }}" data-new-messages="0">
                                                    0
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                @if($alliance)
                    <div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" style="overflow: hidden;" role="tablist">
                        <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab" tabindex="0">
                            <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Alliance
                        </h3>
                        <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" role="tabpanel" style="padding: 0px; overflow: hidden;">
                            <div class="playerlist_top_box">
                                <a href="{{ route('chat.index', ['allianceId' => $alliance->id]) }}" class="playerlist openAssociationChat" data-associationid="{{ $alliance->id }}" style="text-decoration: none; color: inherit; display: block;">
                                    <span title="" class="playerstatus tooltip blank"></span>
                                    <span style="color: orange">Alliance Chat</span>
                                    <span class="new_msg_count noMessage" data-new-messages="0" data-associationid="{{ $alliance->id }}">
                                        0
                                    </span>
                                </a>
                            </div>
                            <div class="scrollContainer">
                                <ul class="playerlist">
                                    @foreach($allianceMembers as $member)
                                        <li class="playerlist_item @if($loop->iteration % 2 === 0) odd @endif @if($chatPartner && $chatPartner->id === $member->user_id) active @endif" data-playerid="{{ $member->user_id }}" data-filterchatactive="off" data-filteronline="off">
                                            <a href="{{ route('chat.index', ['playerId' => $member->user_id]) }}" style="text-decoration: none; color: inherit; display: block;">
                                                <p class="playername">
                                                    <span class="playerstatus tooltip {{ $member->user->isOnline() ? 'online' : 'offline' }}" data-tooltip-title="{{ $member->user->isOnline() ? 'online' : 'offline' }}">
                                                    </span>
                                                    {{ $member->user->username }}
                                                </p>
                                                <span class="new_msg_count noMessage" data-playerid="{{ $member->user_id }}" data-new-messages="0">
                                                    0
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="playerlist_box js_accordion ui-accordion ui-widget ui-helper-reset" style="overflow: hidden;" role="tablist">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab" tabindex="0">
                        <span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Strangers
                    </h3>
                    <div class="ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" role="tabpanel" style="padding: 0px; overflow: hidden;">
                        <div class="playerlist_top_box"></div>
                        <div class="scrollContainer">
                            <ul class="playerlist">
                                @if($strangers->isEmpty())
                                    <li class="no_buddies">No strangers</li>
                                @else
                                    @foreach($strangers as $stranger)
                                        <li class="playerlist_item @if($loop->iteration % 2 === 0) odd @endif @if($chatPartner && $chatPartner->id === $stranger->id) active @endif" data-playerid="{{ $stranger->id }}" data-filterchatactive="on" data-filteronline="off">
                                            <a href="{{ route('chat.index', ['playerId' => $stranger->id]) }}" style="text-decoration: none; color: inherit; display: block;">
                                                <p class="playername">
                                                    <span class="playerstatus tooltip disallowed" data-tooltip-title="Status not visible">
                                                    </span>
                                                    {{ $stranger->username }}
                                                </p>
                                                <span class="new_msg_count noMessage" data-playerid="{{ $stranger->id }}" data-new-messages="0">
                                                    0
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="c-right"></div>
                <div class="c-left"></div>
            </div>
        </div>
    </div>

@endsection
