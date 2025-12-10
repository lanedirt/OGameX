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
                            <a href="javascript:void(0)" class="sendMail js_openChat tooltip" data-playerid="{{ $highscorePlayer['id'] }}" title="{{ __('Write message') }}"><span class="icon icon_chat"></span></a>
                            @if($highscorePlayer['id'] != $player->getId() && !($highscorePlayer['is_admin'] ?? false))
                                <a class="tooltip js_hideTipOnMobile icon sendBuddyRequest" title="{{ __('Buddy request') }}" data-playerid="{{ $highscorePlayer['id'] }}" data-playername="{{ $highscorePlayer['name'] }}" href="javascript:void(0);">
                                    <span class="icon icon_user"></span>
                                </a>
                            @endif
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

@include('ingame.shared.buddy.bbcode-parser')

        <script type="text/javascript">
            // Initialize buddy dialog after it loads
            window.initBuddyDialog = function() {
                var locaKeys = {"bold":"Bold","italic":"Italic","underline":"Underline","stroke":"Strikethrough","sub":"Subscript","sup":"Superscript","fontColor":"Font colour","fontSize":"Font size","backgroundColor":"Background colour","backgroundImage":"Background image","tooltip":"Tool-tip","alignLeft":"Left align","alignCenter":"Centre align","alignRight":"Right align","alignJustify":"Justify","block":"Break","code":"Code","spoiler":"Spoiler","moreopts":"","list":"List","hr":"Horizontal line","picture":"Image","link":"Link","email":"Email","player":"Player","item":"Item","coordinates":"Coordinates","preview":"Preview","textPlaceHolder":"Text...","playerPlaceHolder":"Player ID or name","itemPlaceHolder":"Item ID","coordinatePlaceHolder":"Galaxy:system:position","charsLeft":"Characters remaining","colorPicker":{"ok":"Ok","cancel":"Cancel","rgbR":"R","rgbG":"G","rgbB":"B"},"backgroundImagePicker":{"ok":"Ok","repeatX":"Repeat horizontally","repeatY":"Repeat vertically"}};

                // Block BBCode preview AJAX calls temporarily to prevent 405 errors
                var blockPreviewCalls = true;
                $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
                    // Block POST requests to /overview which are preview-related
                    if (blockPreviewCalls && options.url && options.type === 'POST' && options.url.indexOf('/overview') > -1) {
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
                                fadeBox('{{ __("Buddy request sent successfully!") }}', false);
                                form.closest('.ui-dialog-content').dialog('close');
                                setTimeout(function() {
                                    form.closest('.overlayDiv').remove();
                                    form.closest('.ui-dialog').remove();
                                }, 100);
                            } else {
                                fadeBox(response.message || '{{ __("Failed to send buddy request.") }}', true);
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = '{{ __("Failed to send buddy request.") }}';
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
                    title: '{{ __("Buddy request to") }} ' + playerName,
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
                        if (confirm('{{ __("Are you sure you want to ignore") }} ' + playerName + '?')) {
                            $.ajax({
                                url: '{{ route('buddies.ignore') }}',
                                type: 'POST',
                                data: {
                                    ignored_user_id: playerId,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        fadeBox('{{ __("Player ignored successfully!") }}', false);
                                    } else {
                                        fadeBox(response.message || '{{ __("Failed to ignore player.") }}', true);
                                    }
                                },
                                error: function(xhr) {
                                    var errorMessage = '{{ __("Failed to ignore player.") }}';
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
