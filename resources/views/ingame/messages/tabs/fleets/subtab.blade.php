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
                <!--
                <message-footer class="msg_actions">
                    <message-footer-actions>

                        <gradient-button sq30="">
                            <button class="custom_btn icon_not_favorited tooltip msgFavouriteBtn"
                                    title="mark as favourite"
                                    data-message-id="81049"><img
                                        src="/img/icons/not_favorited.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>

                        <gradient-button sq30="">
                            <button class="custom_btn icon_apikey tooltip msgApiKeyBtn"
                                    title="This data can be entered into a compatible combat simulator:<br/><input value='sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50' readonly onclick='select()' style='width:360px'></input>"
                                    data-message-id="81049"><img
                                        src="/img/icons/apikey.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgCombatSimBtn"
                                    title="Open in Combat Simulator"
                                    onclick="window.open('#combatsim&amp;reportHash=sr-en-255-f6796891d781ce5b9c10a401795b3e5acf4bcc50');"
                                    data-message-id="81049"><img
                                        src="/img/icons/speed.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn overlay tooltip msgShareBtn"
                                    title="share message" data-message-id="81049"
                                    data-overlay-title="share message"
                                    data-target="#shareReportOverlay&amp;messageId=81049">
                                <img src="/img/icons/share.png"
                                     style="width:20px;height:20px;"></button>
                        </gradient-button>
                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgAttackBtn"
                                    title="Attack"
                                    onclick="window.location.href='#fleetdispatch&amp;galaxy=2&amp;system=8&amp;position=12&amp;type=1&amp;mission=1';"
                                    data-message-id="81049">
                                <div class="msgAttackIconContainer"><img
                                            src="/img/icons/attack.png"
                                            style="width:20px;height:20px;"></div>
                            </button>
                        </gradient-button>


                        <gradient-button sq30="">
                            <button class="custom_btn tooltip msgEspionageBtn"
                                    title="Espionage"
                                    onclick="sendShipsWithPopup(6,2,8,12,1,2); return false;"
                                    data-message-id="81049"><img
                                        src="/img/icons/espionage.png"
                                        style="width:20px;height:20px;"></button>
                        </gradient-button>
                    </message-footer-actions>
                    <message-footer-details>
                        <a class="fright txt_link msg_action_link overlay"
                           href="#messages&amp;messageId=81049&amp;tabid=20&amp;ajax=1"
                           data-overlay-title="More details">
                            More details
                        </a>
                    </message-footer-details>
                </message-footer>-->
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
