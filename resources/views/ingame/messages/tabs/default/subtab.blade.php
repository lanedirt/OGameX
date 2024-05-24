<div id="defaultmessagespage">
    <div class="tab_ctn">
        <ul class="tab_inner clearfix">
            <ul class="pagination">
                <li class="paginator" data-tab="3" data-page="1">|&lt;&lt;</li>
                <li class="paginator" data-tab="3" data-page="1">&lt;</li>
                <li class="curPage" data-tab="3">1/1</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;</li>
                <li class="paginator" data-tab="3" data-page="1">&gt;&gt;|</li>
            </ul>
            <input type="hidden" name="token" value="b5e7750a20009bf4c875f592bcc7a432">
            @php
                /** @var OGame\GameMessages\Abstracts\GameMessage[] $messages */
            @endphp
            @if (count($messages) === 0)
                <li class="no_msg">
                    There are currently no messages available in this tab
                </li>
                <br>
            @endif
            @foreach ($messages as $message)
                <li class="msg @if ($message->isUnread()) msg_new @endif" data-msg-id="{{ $message->getId() }}">
                    <div class="msg_status"></div>
                    <div class="msg_head">
                        <span class="msg_title blue_txt">{{ $message->getSubject() }}</span>
                        <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
                            </a>
                            <span class="msg_date fright">{{ $message->getDateFormatted() }}</span>
                        </span>
                        <br>
                        <span class="msg_sender_label">From:</span>
                        <span class="msg_sender">{{ $message->getFrom() }}</span>
                    </div>
                    <span class="msg_content">
                        {!! $message->getBody() !!}
                    </span>
                    <message-footer class="msg_actions">
                        <message-footer-actions>
                            {!! $message->getFooterActions() !!}
                        </message-footer-actions>
                        <message-footer-details>
                            {!! $message->getFooterDetails() !!}
                        </message-footer-details>
                    </message-footer>
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
        @include('ingame.messages.tabs.subtab-init-js')
    </div>
</div>