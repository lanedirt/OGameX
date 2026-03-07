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
            @if ($currentPlayerIsAdmin ?? false)
                @if ($highscoreAdminVisible ?? false)
                    <option value="{{ $highscoreCurrentPlayerPage }}">{{ __('t_ingame.highscore.own_position') }}</option>
                @else
                    <option value="1">{{ __('t_ingame.highscore.own_position_hidden') }}</option>
                @endif
            @else
                <option value="{{ $highscoreCurrentPlayerPage }}">{{ __('t_ingame.highscore.own_position') }}</option>
            @endif
            @for ($i = 1; $i <= ceil($highscorePlayerAmount / 100); $i++)
                <option {{ $i == $highscoreCurrentPage ? 'selected="selected"' : '' }} value="{{ $i }}"> {{ ((($i-1) * 100) + 1)  }} - {{ $i * 100 }}</option>
            @endfor
        </select>
        <div class="fleft" id="highscoreHeadline">
            {{ __('t_ingame.highscore.points') }}
        </div>
        <table id="ranks" class="userHighscore">
            <thead>
            <tr>
                <td class="position">
                    {{ __('t_ingame.highscore.position') }}
                </td>
                <td class="movement"></td>
                <td class="name">
                    {{ __('t_ingame.highscore.player_name_honour') }}
                </td>
                <td class="sendmsg" align="center">
                    {{ __('t_ingame.highscore.action') }}
                </td>
                <td class="score" align="center">
                    {{ __('t_ingame.highscore.points') }}
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
                        <div class="highscoreNameFieldWrapper" style="height: unset;">
                            <div class="highscoreNameAndTitleHolder" style="width: calc(100% - 0px); flex-direction: row;">
                                <div class="highscoreNameHolder">
                                    @if(!empty($highscorePlayer['alliance_tag']))
                                        <span class="ally-tag">
                                            <a href="{{ route('alliance.info', ['alliance_id' => $highscorePlayer['alliance_id']]) }}" target="_ally">
                                                [{{ $highscorePlayer['alliance_tag'] }}]
                                            </a>
                                        </span>
                                    @endif

                                    <a href="{{ route('galaxy.index', ['galaxy' => $highscorePlayer['planet_coords']->galaxy, 'system' => $highscorePlayer['planet_coords']->system, 'position' => $highscorePlayer['planet_coords']->position]) }}" class="dark_highlight_tablet">
                                        <span class="playername{{ ($highscorePlayer['is_admin'] ?? false) ? ' status_abbr_admin' : '' }}">
                                            {{ $highscorePlayer['name'] }}
                                        </span>
                                    </a>
                                </div>
                                <div class="honorScore">
                                    (<span class="undermark tooltip js_hideTipOnMobile" title="{{ __('t_ingame.highscore.honour_points') }}">0</span>)
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="sendmsg">
                        <div class="sendmsg_content">
                            <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $highscorePlayer['id'] }}" title="{{ __('t_ingame.highscore.write_message') }}"><span class="icon icon_chat"></span></a>
                            @if($highscorePlayer['id'] != $player->getId() && !($highscorePlayer['is_admin'] ?? false))
                                <a class="tooltip js_hideTipOnMobile icon sendBuddyRequest" title="{{ __('t_ingame.highscore.buddy_request') }}" data-playerid="{{ $highscorePlayer['id'] }}" data-playername="{{ $highscorePlayer['name'] }}" href="javascript:void(0);">
                                    <span class="icon icon_user"></span>
                                </a>
                            @endif
                        </div>
                    </td>

                    <td class="score">
                        @if($highscoreCurrentType == 3 && isset($highscorePlayer['total_ships']))
                            <span class="tooltip" title="{{ __('t_ingame.highscore.total_ships') }}: {{ number_format($highscorePlayer['total_ships']) }}">
                                {{ $highscorePlayer['points_formatted'] }}
                            </span>
                        @else
                            {{ $highscorePlayer['points_formatted'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

@include('ingame.shared.buddy.bbcode-parser')

        <script type="text/javascript">
            // Initialize buddy dialog after it loads
            window.initBuddyDialog = function() {
                var locaKeys = {!! json_encode([
                    'bold'               => __('t_ingame.messages.bbcode_bold'),
                    'italic'             => __('t_ingame.messages.bbcode_italic'),
                    'underline'          => __('t_ingame.messages.bbcode_underline'),
                    'stroke'             => __('t_ingame.messages.bbcode_stroke'),
                    'sub'                => __('t_ingame.messages.bbcode_sub'),
                    'sup'                => __('t_ingame.messages.bbcode_sup'),
                    'fontColor'          => __('t_ingame.messages.bbcode_font_color'),
                    'fontSize'           => __('t_ingame.messages.bbcode_font_size'),
                    'backgroundColor'    => __('t_ingame.messages.bbcode_bg_color'),
                    'backgroundImage'    => __('t_ingame.messages.bbcode_bg_image'),
                    'tooltip'            => __('t_ingame.messages.bbcode_tooltip'),
                    'alignLeft'          => __('t_ingame.messages.bbcode_align_left'),
                    'alignCenter'        => __('t_ingame.messages.bbcode_align_center'),
                    'alignRight'         => __('t_ingame.messages.bbcode_align_right'),
                    'alignJustify'       => __('t_ingame.messages.bbcode_align_justify'),
                    'block'              => __('t_ingame.messages.bbcode_block'),
                    'code'               => __('t_ingame.messages.bbcode_code'),
                    'spoiler'            => __('t_ingame.messages.bbcode_spoiler'),
                    'moreopts'           => __('t_ingame.messages.bbcode_moreopts'),
                    'list'               => __('t_ingame.messages.bbcode_list'),
                    'hr'                 => __('t_ingame.messages.bbcode_hr'),
                    'picture'            => __('t_ingame.messages.bbcode_picture'),
                    'link'               => __('t_ingame.messages.bbcode_link'),
                    'email'              => __('t_ingame.messages.bbcode_email'),
                    'player'             => __('t_ingame.messages.bbcode_player'),
                    'item'               => __('t_ingame.messages.bbcode_item'),
                    'coordinates'        => __('t_ingame.messages.bbcode_coordinates'),
                    'preview'            => __('t_ingame.messages.bbcode_preview'),
                    'textPlaceHolder'    => __('t_ingame.messages.bbcode_text_ph'),
                    'playerPlaceHolder'  => __('t_ingame.messages.bbcode_player_ph'),
                    'itemPlaceHolder'    => __('t_ingame.messages.bbcode_item_ph'),
                    'coordinatePlaceHolder' => __('t_ingame.messages.bbcode_coord_ph'),
                    'charsLeft'          => __('t_ingame.messages.bbcode_chars_left'),
                    'colorPicker'        => ['ok' => __('t_ingame.messages.bbcode_ok'), 'cancel' => __('t_ingame.messages.bbcode_cancel'), 'rgbR' => 'R', 'rgbG' => 'G', 'rgbB' => 'B'],
                    'backgroundImagePicker' => ['ok' => __('t_ingame.messages.bbcode_ok'), 'repeatX' => __('t_ingame.messages.bbcode_repeat_x'), 'repeatY' => __('t_ingame.messages.bbcode_repeat_y')],
                ]) !!};

                // Block BBCode preview AJAX calls temporarily to prevent 405 errors
                var blockPreviewCalls = true;
                $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                    // Block POST requests to preview URLs (empty, /overview, or invalid URLs)
                    if (blockPreviewCalls && options.type === 'POST' &&
                        (!options.url || options.url === '' || options.url.indexOf('/overview') > -1 ||
                         options.url.indexOf('&imgAllowed=') === 0)) {
                        jqXHR.abort();
                        return false;
                    }
                });

                initBuddyRequestForm();

                // TODO: The BBCode editor includes an "Item" dropdown for linking game items.
                // This feature is not yet implemented as the item system is not available.
                // When items are implemented, update the BBCode parser and preview to support [item]ItemID[/item] tags.
                initBBCodeEditor(locaKeys, {}, false, '.buddy_request_textarea', 5000, true);

                // Re-enable AJAX calls after initialization
                setTimeout(function() {
                    blockPreviewCalls = false;
                }, 500);

                setTimeout(function() {
                    var $textarea = $('.buddy_request_textarea');
                    var $container = $textarea.closest('.markItUpContainer');
                    var $preview = $container.find('.miu_preview_container');

                    $container.find('.preview_link').off('click').on('click', function(e) {
                        e.preventDefault();
                        if ($preview.is(':visible')) {
                            $preview.hide();
                            $(this).removeClass('active');
                        } else {
                            $preview.html(window.buddyBBCodeParser($textarea.val())).show();
                            $(this).addClass('active');
                        }
                    });
                }, 150);

                $('#buddyRequestForm').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    var form = $(this);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            if (response.success) {
                                fadeBox(@json(__('t_ingame.highscore.buddy_request_sent')), false);
                                form.closest('.ui-dialog-content').dialog('close');
                                setTimeout(function() {
                                    form.closest('.overlayDiv').remove();
                                    form.closest('.ui-dialog').remove();
                                }, 100);
                            } else {
                                fadeBox(response.message || @json(__('t_ingame.highscore.buddy_request_failed')), true);
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = @json(__('t_ingame.highscore.buddy_request_failed'));
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            fadeBox(errorMessage, true);
                        }
                    });
                });
            };

            // Global function for sending buddy requests (must be global for AJAX-loaded content)
            window.sendBuddyRequestDialog = function(playerId, playerName) {
                // Close any existing buddy request dialogs
                $('.buddyRequestDialog').each(function() {
                    try {
                        $(this).dialog('destroy');
                    } catch(e) {}
                    $(this).remove();
                });
                $('.ui-dialog:has(.buddyRequestDialog)').remove();

                // Create dialog container
                var $dialog = $('<div class="overlayDiv buddyRequestDialog"></div>').css('display', 'none');
                $('body').append($dialog);

                // Initialize the dialog first
                $dialog.dialog({
                    title: @json(__('t_ingame.highscore.buddy_request_to')) + ' ' + playerName,
                    width: 'auto',
                    height: 'auto',
                    modal: false,
                    closeText: '',
                    position: { my: "center", at: "center" },
                    close: function() {
                        $(this).dialog('destroy');
                        $(this).remove();
                    }
                });

                // Load content via AJAX
                var dialogUrl = '{{ route('buddies.requestdialog') }}?id=' + playerId + '&name=' + encodeURIComponent(playerName) + '&_=' + Date.now();

                $.get(dialogUrl).done(function(data) {
                    $dialog.empty().append(data);

                    // Initialize buddy dialog BBCode editor
                    if (typeof window.initBuddyDialog === 'function') {
                        window.initBuddyDialog();
                    }

                    // Reposition after content loads - check if dialog is still initialized
                    try {
                        if ($dialog.hasClass('ui-dialog-content')) {
                            $dialog.dialog('option', 'position', $dialog.dialog('option', 'position'));
                        }
                    } catch(e) {
                        // Silently ignore repositioning errors
                    }
                }).fail(function() {
                    try {
                        $dialog.dialog('close');
                    } catch(e) {}
                });
            };

            $(document).ready(function(){
                initHighscoreContent();
                initHighscore();

                // Handle buddy request button clicks
                $(document).on('click', '.sendBuddyRequest, .sendBuddyRequestLink', function(e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var playerId = $(this).data('playerid');
                    var playerName = $(this).data('playername');
                    if (playerId && playerName) {
                        window.sendBuddyRequestDialog(playerId, playerName);
                    }
                    return false;
                });

                // Handle ignore player button clicks
                $(document).on('click', '.ignorePlayerLink', function(e) {
                    e.preventDefault();
                    var playerId = $(this).data('playerid');
                    var playerName = $(this).data('playername');

                    if (playerId && playerName) {
                        // Confirm before ignoring
                        if (confirm(@json(__('t_ingame.highscore.are_you_sure_ignore')) + ' ' + playerName + '?')) {
                            $.ajax({
                                url: '{{ route('buddies.ignore') }}',
                                type: 'POST',
                                data: {
                                    ignored_user_id: playerId,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        fadeBox(@json(__('t_ingame.highscore.player_ignored')), false);
                                    } else {
                                        fadeBox(response.message || @json(__('t_ingame.highscore.player_ignored_failed')), true);
                                    }
                                },
                                error: function(xhr) {
                                    var errorMessage = @json(__('t_ingame.highscore.player_ignored_failed'));
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    fadeBox(errorMessage, true);
                                }
                            });
                        }
                    }
                    return false;
                });
            });
        </script>
        <div class="pagebar">
            <a href="javascript:void(0);" class="scrollToTop">{{ __('t_ingame.layout.back_to_top') }}</a>
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
