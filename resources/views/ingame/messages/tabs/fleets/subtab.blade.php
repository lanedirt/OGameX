<div id="fleetsgenericpage">
    <!-- TODO: implement trash -->
    <!--<ul class="tab_inner ctn_with_trash clearfix"> -->
    <ul class="tab_inner clearfix">
        <ul class="pagination">
            <li class="paginator" data-tab="20" data-page="1">|&lt;&lt;</li>
            <li class="paginator" data-tab="20" data-page="1">&lt;</li>
            <li class="curPage" data-tab="20">1/1</li>
            <li class="paginator" data-tab="20" data-page="1">&gt;</li>
            <li class="paginator" data-tab="20" data-page="1">&gt;&gt;|</li>
        </ul>
        <input type="hidden" name="token"
               value="d99f68937305e0b2c3ff3f059259fcec">
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
            <li class="msg @if ($message->isNew()) msg_new @endif" data-msg-id="{{ $message->id }}">
                <div class="msg_status"></div>
                <div class="msg_head">
                    <span class="msg_title blue_txt">{!! $message->getSubject() !!}</span>
                    <span class="fright">
                        <a href="javascript: void(0);" class="fright">
                            <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile tpd-hideOnClickOutside" title=""></span>
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
            <li class="paginator" data-tab="20" data-page="1">|&lt;&lt;</li>
            <li class="paginator" data-tab="20" data-page="1">&lt;</li>
            <li class="curPage" data-tab="20">1/1</li>
            <li class="paginator" data-tab="20" data-page="1">&gt;</li>
            <li class="paginator" data-tab="20" data-page="1">&gt;&gt;|</li>
        </ul>
    </ul>
    @include('ingame.messages.tabs.subtab-init-js')
</div>
