<div id="fleetsTab">
    <div class="tab_ctn">
        <div class="js_subtabs_fleets ui-tabs ui-corner-all ui-widget ui-widget-content">
            <ul class="subtabs ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header"
                role="tablist">
                <li id="subtabs-nfFleet20"
                    class="list_item first ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active"
                    data-subtabname="Espionage" data-tabid="20" role="tab" tabindex="0"
                    aria-controls="ui-id-14" aria-labelledby="ui-id-13" aria-selected="true"
                    aria-expanded="true">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets', 'subtab' => 'espionage']) }}"
                       class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1"
                       id="ui-id-13">
                        Espionage
                        @if ($unread_messages_count['espionage'] > 0)
                            <span>({{ $unread_messages_count['espionage'] }})</span>
                        @endif
                    </a>
                </li>
                <li id="subtabs-nfFleet21"
                    class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Combat Reports" data-tabid="21" role="tab" tabindex="-1"
                    aria-controls="ui-id-16" aria-labelledby="ui-id-15" aria-selected="false"
                    aria-expanded="false">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets', 'subtab' => 'combat_reports' ]) }}"
                       class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1"
                       id="ui-id-15">
                        Combat Reports
                        @if ($unread_messages_count['combat_reports'] > 0)
                            <span>({{ $unread_messages_count['combat_reports'] }})</span>
                        @endif
                    </a>
                </li>
                <li id="subtabs-nfFleet22"
                    class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Expeditions" data-tabid="22" role="tab" tabindex="-1"
                    aria-controls="ui-id-18" aria-labelledby="ui-id-17" aria-selected="false"
                    aria-expanded="false">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets', 'subtab' => 'expeditions' ]) }}"
                       class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1"
                       id="ui-id-17">
                        Expeditions
                        @if ($unread_messages_count['expeditions'] > 0)
                            <span>({{ $unread_messages_count['expeditions'] }})</span>
                        @endif
                    </a>
                </li>
                <li id="subtabs-nfFleet23"
                    class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Unions/Transport" data-tabid="23" role="tab" tabindex="-1"
                    aria-controls="ui-id-20" aria-labelledby="ui-id-19" aria-selected="false"
                    aria-expanded="false">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets', 'subtab' => 'transport' ]) }}"
                       class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1"
                       id="ui-id-19">
                        Unions/Transport
                        @if ($unread_messages_count['transport'] > 0)
                            <span>({{ $unread_messages_count['transport'] }})</span>
                        @endif
                    </a>
                </li>
                <li id="subtabs-nfFleet24"
                    class="list_item last ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Other" data-tabid="24" role="tab" tabindex="-1"
                    aria-controls="ui-id-22" aria-labelledby="ui-id-21" aria-selected="false"
                    aria-expanded="false">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets', 'subtab' => 'other' ]) }}"
                       class="txt_link ui-tabs-anchor" role="presentation" tabindex="-1"
                       id="ui-id-21">
                        Other
                        @if ($unread_messages_count['other'] > 0)
                            <span>({{ $unread_messages_count['other'] }})</span>
                        @endif
                        <span></span>
                    </a>
                </li>
                <!-- TODO: Implement trash tab -->
                <!--
            <li id="subtabs-nfFleetTrash"
                class="trash_tab ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                data-subtabname="Trash" data-tabid="25" role="tab" tabindex="-1"
                aria-controls="ui-id-24" aria-labelledby="ui-id-23" aria-selected="false"
                aria-expanded="false">
                    <div class="trash_box">
                    <h3 class="trash_title in_trash" style="display: none;">Trash</h3>
                    <span class="not_in_trash"><span class="trash_action js_actionKillAll">Delete</span></span><span
                            class="in_trash" style="display: none;"><span
                                class="trash_action js_actionReviveAll">restore</span> or <span
                                class="trash_action js_actionDeleteAll">delete permanently</span></span>
                    all in tab <span class="js_active_tab">Espionage</span>.
                </div>
                <a href="index.php?page=messages&amp;tab=25&amp;ajax=1"
                   class="btn_blue btn_trash ui-tabs-anchor" role="presentation"
                   tabindex="-1" id="ui-id-23">
                    Trash
                    <span></span>
                </a>
                </li>-->
            </ul>
            <div id="ui-id-14" aria-live="polite" aria-labelledby="ui-id-13" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false"
                 style="">
            </div>
            <div id="ui-id-16" aria-live="polite" aria-labelledby="ui-id-15" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;">
            </div>
            <div id="ui-id-18" aria-live="polite" aria-labelledby="ui-id-17" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;">
            </div>
            <div id="ui-id-20" aria-live="polite" aria-labelledby="ui-id-19" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
            <div id="ui-id-22" aria-live="polite" aria-labelledby="ui-id-21" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
            <div id="ui-id-24" aria-live="polite" aria-labelledby="ui-id-23" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
        </div>
    </div>
</div>
