<div id="defaultmessagespage">
    <div class="tab_ctn">
        <ul class="tab_inner ctn_with_new_msg clearfix">
            <ul class="pagination">
                <li class="paginator" data-tab="3" data-page="1">|&lt;&lt;</li>
                <li class="paginator" data-tab="3" data-page="1">&lt;</li>
                <li class="curPage" data-tab="3">1/1</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;&gt;|</li>
            </ul>
            <input type="hidden" name="token" value="b5e7750a20009bf4c875f592bcc7a432">
            @php
                /** @var OGame\ViewModels\MessageViewModel[] $messages */
            @endphp
            @if (count($messages) === 0)
                <li class="no_msg">
                    There are currently no messages available in this tab
                </li>
                <br>
            @endif
            @foreach ($messages as $message)
                <li class="msg @if ($message->isNew()) msg_new @endif" data-msg-id="{{ $message->getId() }}">
                    <div class="msg_status"></div>
                    <div class="msg_head">
                        <span class="msg_title blue_txt">{{ $message->getSubject() }}</span>
                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
                            </a>
                            <span class="msg_date fright">{{ $message->getDate() }}</span>
                        </span>
                        <br>
                        <span class="msg_sender_label">From:</span>
                        <span class="msg_sender">{{ $message->getFrom() }}</span>
                    </div>
                    <span class="msg_content">
                        {!! $message->getBody() !!}
                    </span>
                    <!--<message-footer class="msg_actions">
                        <message-footer-actions>


                        </message-footer-actions>
                        <message-footer-details>
                        </message-footer-details>
                    </message-footer>-->
                    <script type="text/javascript">
                        initOverlays();
                    </script>

                </li>
            @endforeach
            <ul class="pagination">
                <li class="paginator" data-tab="3" data-page="1">|&lt;&lt;</li>
                <li class="paginator" data-tab="3" data-page="1">&lt;</li>
                <li class="curPage" data-tab="3">1/1</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;&gt;|</li>
            </ul>
        </ul>
        <script type="text/javascript">
            var activeTabid = $('.ui-tabs-active a').attr('id'); //erster tab als default
            var hasSubtabs = $('div[aria-labelledby="' + activeTabid + '"] .tab_ctn div ul.subtabs').length;
            var activeSubtabid = '';

            $('.ui-tabs-active a').each(function () {
                activeSubtabid = $(this).attr('id');
            });

            var msgids = [];
            var index = 0;

            if (hasSubtabs > 0) {
                $('div[aria-labelledby="' + activeSubtabid + '"] .msg_new').each(function () {
                    msgids[index] = $(this).data('msg-id');
                    index++;
                });
            } else {
                $('div[aria-labelledby="' + activeTabid + '"] .msg_new').each(function () {
                    msgids[index] = $(this).data('msg-id');
                    index++;
                });
            }

            msgids = JSON.stringify(msgids);

            var msgcountUrl = "#TODO_index.php?page=ajaxMessageCount";
            var playerid = parseInt(113970);
            var action = 111;

            $.ajax({
                url: msgcountUrl,
                type: 'POST',
                data: {
                    player: playerid,
                    action: action,
                    newMessageIds: msgids,
                    ajax: 1
                },
                success: function (data) {
                    var message_menu_count = $('.comm_menu.messages span.new_msg_count');
                    var message_tab_count = $('.ui-tabs-active .new_msg_count');

                    if (message_menu_count.length > 0 && message_tab_count.length > 0) {
                        var menuCount = parseInt(message_menu_count[0].innerHTML);
                        var tabCount = parseInt(message_tab_count[0].innerHTML);
                        var newCount = menuCount - tabCount;

                        if (newCount > 0) {
                            message_menu_count.val(newCount);
                        } else {
                            message_menu_count.remove();
                        }
                    }

                    $('.ui-tabs-active .new_msg_count').remove();

                    if (hasSubtabs > 0) {
                        $('.ui-tabs-active a span:not(.icon_caption)').remove();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        </script>
    </div>
</div>