<div id="communicationTab">
    <div class="tab_ctn">
        <div class="js_subtabs_communication ui-tabs ui-corner-all ui-widget ui-widget-content">
            <ul class="subtabs ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header"
                role="tablist">
                <li id="subtabs-nfCommunication10"
                    class="list_item first ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active"
                    data-subtabname="Messages" data-tabid="10" role="tab" tabindex="0" aria-controls="ui-id-32"
                    aria-labelledby="ui-id-31" aria-selected="true" aria-expanded="true">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'communication', 'subtab' => 'messages']) }}"
                       class="txt_link ui-tabs-anchor"
                       role="presentation" tabindex="-1" id="ui-id-31">
                        Messages

                    </a>
                </li>
                <li id="subtabs-nfCommunication14" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Information" data-tabid="14" role="tab" tabindex="-1" aria-controls="ui-id-34"
                    aria-labelledby="ui-id-33" aria-selected="false" aria-expanded="false">
                    <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'communication', 'subtab' => 'information']) }}"
                       class="txt_link ui-tabs-anchor"
                       role="presentation" tabindex="-1" id="ui-id-33">
                        Information
                        <span></span>
                    </a>
                </li>
                <li id="subtabs-nfCommunication12" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Shared Combat Reports" data-tabid="12" role="tab" tabindex="-1"
                    aria-controls="ui-id-36" aria-labelledby="ui-id-35" aria-selected="false" aria-expanded="false">
                    <a href="#" class="txt_link ui-tabs-anchor"
                       role="presentation" tabindex="-1" id="ui-id-35">
                        Shared Combat Reports
                        <span></span>
                    </a>
                </li>
                <li id="subtabs-nfCommunication11" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Shared Espionage Reports" data-tabid="11" role="tab" tabindex="-1"
                    aria-controls="ui-id-38" aria-labelledby="ui-id-37" aria-selected="false" aria-expanded="false">
                    <a href="#" class="txt_link ui-tabs-anchor"
                       role="presentation" tabindex="-1" id="ui-id-37">
                        Shared Espionage Reports
                        <span></span>
                    </a>
                </li>
                <li id="subtabs-nfCommunication13"
                    class="list_item last ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                    data-subtabname="Expeditions" data-tabid="13" role="tab" tabindex="-1" aria-controls="ui-id-40"
                    aria-labelledby="ui-id-39" aria-selected="false" aria-expanded="false">
                    <a href="#" class="txt_link ui-tabs-anchor"
                       role="presentation" tabindex="-1" id="ui-id-39">
                        Expeditions
                        <span></span>
                    </a>
                </li>
            </ul>
            <div id="ui-id-32" aria-live="polite" aria-labelledby="ui-id-31" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
                <div id="communicationmessagespage">
                    <script>
                        $("#newAllianceMsg").submit(function () {
                            var $thisObj = $(this);

                            if ($.trim($thisObj.find("textarea[name=\"text\"]").val()).length == 0) {
                                errorBoxNotify(LocalizationStrings.error, chatLoca["TEXT_EMPTY"], LocalizationStrings.ok);
                                return false;
                            }
                            let newMessage = $("#newAllianceMsg");
                            let rankIds = $('#select2').val()
                            let broadcastText = newMessage.find('.new_msg_textarea').val()
                            let token = newMessage.find('input[name=token]').val()
                            let params = {rankIds: rankIds, broadcastText: broadcastText, token: token}

                            $.post(
                                $thisObj.attr("action"),
                                params,
                                function (data) {
                                    try {
                                        var decodedData = $.parseJSON(data);
                                        if (typeof decodedData.message != "undefined") {
                                            errorBoxNotify(
                                                LocalizationStrings.error,
                                                decodedData.message,
                                                LocalizationStrings.ok,
                                                function () {
                                                    $.ajax({
                                                        url: '?page=messages',
                                                        type: 'GET',
                                                        dataType: 'html',
                                                        data: {
                                                            tab: 10,
                                                            ajax: 1
                                                        },
                                                        success: function (data) {
                                                            $('#communicationmessagespage').parent().html(data);
                                                        },
                                                        error: function (jqXHR, textStatus, errorThrown) {
                                                        }
                                                    });
                                                }
                                            );
                                        }
                                    } catch (e) {
                                        $("#eins").html(data);
                                    }
                                }
                            );

                            return false;
                        });
                    </script>
                    <div class="js_accordion">
                        <h3>New alliance message</h3>
                        <!-- the form for creating new messages (broadcasts in this case) is in a separate tpl because is is also needed for sharing reports -->
                        <form action="#TODO_page=ingame&amp;component=alliance&amp;tab=broadcast&amp;action=sendBroadcast&amp;asJson=1"
                              method="post" id="newAllianceMsg">
                            <input type="hidden" name="token" value="b5e7750a20009bf4c875f592bcc7a432">

                            <link rel="stylesheet" href="/cdn/css/select2.css" type="text/css">
                            <div>
                                To:
                                <script language="javascript">
                                    jQuery("#select2").select2({
                                        tags: true,
                                        id: function (object) {
                                            return object.value;
                                        }
                                    });
                                </script>

                                <div style="margin-bottom: -10px;">
                                    <select name="empfaenger[]" multiple="" id="select2" style="width: 310px;"
                                            tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                        <option value="-1" selected="">
                                            all players
                                        </option>
                                        <option value="3147">only rank: Founder</option>
                                        <option value="3148">only rank: Newcomer</option>
                                    </select><span class="select2 select2-container select2-container--default"
                                                   dir="ltr" style="width: 310px;"><span class="selection"><span
                                                    class="select2-selection select2-selection--multiple"
                                                    role="combobox" aria-autocomplete="list" aria-haspopup="true"
                                                    aria-expanded="false" tabindex="0"><ul
                                                        class="select2-selection__rendered"><li
                                                            class="select2-selection__choice" title="
                all players
            "><span class="select2-selection__choice__remove" role="presentation">×</span>
                all players
            </li><li class="select2-search select2-search--inline"><input class="select2-search__field" type="search"
                                                                          tabindex="-1" autocomplete="off"
                                                                          autocorrect="off" autocapitalize="off"
                                                                          spellcheck="false" role="textbox"
                                                                          placeholder=""
                                                                          style="width: 0.75em;"></li></ul></span></span><span
                                                class="dropdown-wrapper" aria-hidden="true"></span></span>
                                </div>
                                <br>
                                <button class="btn_blue js_send_msg fright ally_send_button">send</button>
                                <div class="editor_wrap">
                                    <div>
                                        <div class="markItUp">
                                            <div class="markItUpContainer">
                                                <div class="markItUpHeader">
                                                    <ul class="miu_basic">
                                                        <li class="markItUpButton markItUpButton1 bold"><a href=""
                                                                                                           accesskey="B"
                                                                                                           title="Bold [Ctrl+B]">Bold</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton2 italic"><a href=""
                                                                                                             accesskey="I"
                                                                                                             title="Italic [Ctrl+I]">Italic</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton3 fontColor"><a href=""
                                                                                                                title="Font colour">Font
                                                                colour</a></li>
                                                        <li class="markItUpButton markItUpButton4 fontSize markItUpDropMenu">
                                                            <a href="" title="Font size">Font size</a>
                                                            <ul class="">
                                                                <li class="markItUpButton markItUpButton4-1 fontSize6">
                                                                    <a href="" title="">6</a></li>
                                                                <li class="markItUpButton markItUpButton4-2 fontSize8">
                                                                    <a href="" title="">8</a></li>
                                                                <li class="markItUpButton markItUpButton4-3 fontSize10">
                                                                    <a href="" title="">10</a></li>
                                                                <li class="markItUpButton markItUpButton4-4 fontSize12">
                                                                    <a href="" title="">12</a></li>
                                                                <li class="markItUpButton markItUpButton4-5 fontSize14">
                                                                    <a href="" title="">14</a></li>
                                                                <li class="markItUpButton markItUpButton4-6 fontSize16">
                                                                    <a href="" title="">16</a></li>
                                                                <li class="markItUpButton markItUpButton4-7 fontSize18">
                                                                    <a href="" title="">18</a></li>
                                                                <li class="markItUpButton markItUpButton4-8 fontSize20">
                                                                    <a href="" title="">20</a></li>
                                                                <li class="markItUpButton markItUpButton4-9 fontSize22">
                                                                    <a href="" title="">22</a></li>
                                                                <li class="markItUpButton markItUpButton4-10 fontSize24">
                                                                    <a href="" title="">24</a></li>
                                                                <li class="markItUpButton markItUpButton4-11 fontSize26">
                                                                    <a href="" title="">26</a></li>
                                                                <li class="markItUpButton markItUpButton4-12 fontSize28">
                                                                    <a href="" title="">28</a></li>
                                                                <li class="markItUpButton markItUpButton4-13 fontSize30">
                                                                    <a href="" title="">30</a></li>
                                                            </ul>
                                                            <span class="dropdown_arr"></span></li>
                                                        <li class="markItUpButton markItUpButton5 list"><a href=""
                                                                                                           title="List">List</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton6 coordinates"><a
                                                                    href="" title="Coordinates">Coordinates</a></li>
                                                        <li class="txt_link fright li_miu_advanced"><span
                                                                    class="toggle_miu_advanced show_miu_advanced awesome-button"
                                                                    role="button"></span></li>
                                                    </ul>
                                                    <ul class="miu_advanced" style="display: none;">
                                                        <li class="markItUpButton markItUpButton1 underline"><a href=""
                                                                                                                accesskey="U"
                                                                                                                title="Underline [Ctrl+U]">Underline</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton2 strikeThrough"><a
                                                                    href="" accesskey="S"
                                                                    title="Strikethrough [Ctrl+S]">Strikethrough</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton3 sub"><a href=""
                                                                                                          title="Subscript">Subscript</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton4 sup"><a href=""
                                                                                                          title="Superscript">Superscript</a>
                                                        </li>
                                                        <li class="markItUpSeparator">-</li>
                                                        <li class="markItUpButton markItUpButton5 item markItUpDropMenu">
                                                            <a href="" title="Item">Item</a>
                                                            <ul class="">
                                                                <li class="markItUpButton markItUpButton5-1 "><a href=""
                                                                                                                 title="">Researchers</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-2 "><a href=""
                                                                                                                 title="">Traders</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-3 "><a href=""
                                                                                                                 title="">Warriors</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-4 "><a href=""
                                                                                                                 title="">Bronze
                                                                        Crystal Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-5 "><a href=""
                                                                                                                 title="">Bronze
                                                                        Deuterium Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-6 "><a href=""
                                                                                                                 title="">Bronze
                                                                        Metal Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-7 "><a href=""
                                                                                                                 title="">Discoverer</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-8 "><a href=""
                                                                                                                 title="">Collector</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-9 "><a href=""
                                                                                                                 title="">General</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-10 "><a
                                                                            href="" title="">Bronze Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-11 "><a
                                                                            href="" title="">Bronze Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-12 "><a
                                                                            href="" title="">Bronze Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-13 "><a
                                                                            href="" title="">Silver Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-14 "><a
                                                                            href="" title="">Silver Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-15 "><a
                                                                            href="" title="">Silver Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-16 "><a
                                                                            href="" title="">Gold Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-17 "><a
                                                                            href="" title="">Gold Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-18 "><a
                                                                            href="" title="">Gold Crystal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-19 "><a
                                                                            href="" title="">Platinum Crystal
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-20 "><a
                                                                            href="" title="">Platinum Crystal
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-21 "><a
                                                                            href="" title="">Platinum Crystal
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-22 "><a
                                                                            href="" title="">DETROID Bronze</a></li>
                                                                <li class="markItUpButton markItUpButton5-23 "><a
                                                                            href="" title="">DETROID Gold</a></li>
                                                                <li class="markItUpButton markItUpButton5-24 "><a
                                                                            href="" title="">DETROID Platinum</a></li>
                                                                <li class="markItUpButton markItUpButton5-25 "><a
                                                                            href="" title="">DETROID Silver</a></li>
                                                                <li class="markItUpButton markItUpButton5-26 "><a
                                                                            href="" title="">Bronze Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-27 "><a
                                                                            href="" title="">Bronze Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-28 "><a
                                                                            href="" title="">Bronze Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-29 "><a
                                                                            href="" title="">Silver Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-30 "><a
                                                                            href="" title="">Silver Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-31 "><a
                                                                            href="" title="">Silver Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-32 "><a
                                                                            href="" title="">Gold Deuterium Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-33 "><a
                                                                            href="" title="">Gold Deuterium Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-34 "><a
                                                                            href="" title="">Gold Deuterium Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-35 "><a
                                                                            href="" title="">Platinum Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-36 "><a
                                                                            href="" title="">Platinum Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-37 "><a
                                                                            href="" title="">Platinum Deuterium
                                                                        Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-38 "><a
                                                                            href="" title="">Energy Booster Bronze</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-39 "><a
                                                                            href="" title="">Energy Booster Bronze</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-40 "><a
                                                                            href="" title="">Energy Booster Bronze</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-41 "><a
                                                                            href="" title="">Energy Booster Silver</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-42 "><a
                                                                            href="" title="">Energy Booster Silver</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-43 "><a
                                                                            href="" title="">Energy Booster Silver</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-44 "><a
                                                                            href="" title="">Energy Booster Gold</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-45 "><a
                                                                            href="" title="">Energy Booster Gold</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-46 "><a
                                                                            href="" title="">Energy Booster Gold</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-47 "><a
                                                                            href="" title="">Energy Booster Platinum</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-48 "><a
                                                                            href="" title="">Energy Booster Platinum</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-49 "><a
                                                                            href="" title="">Energy Booster Platinum</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-50 "><a
                                                                            href="" title="">Bronze Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-51 "><a
                                                                            href="" title="">Bronze Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-52 "><a
                                                                            href="" title="">Bronze Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-53 "><a
                                                                            href="" title="">Silver Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-54 "><a
                                                                            href="" title="">Silver Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-55 "><a
                                                                            href="" title="">Silver Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-56 "><a
                                                                            href="" title="">Gold Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-57 "><a
                                                                            href="" title="">Gold Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-58 "><a
                                                                            href="" title="">Gold Expedition Slots</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-59 "><a
                                                                            href="" title="">Bronze Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-60 "><a
                                                                            href="" title="">Bronze Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-61 "><a
                                                                            href="" title="">Bronze Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-62 "><a
                                                                            href="" title="">Silver Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-63 "><a
                                                                            href="" title="">Silver Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-64 "><a
                                                                            href="" title="">Silver Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-65 "><a
                                                                            href="" title="">Gold Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-66 "><a
                                                                            href="" title="">Gold Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-67 "><a
                                                                            href="" title="">Gold Fleet Slots</a></li>
                                                                <li class="markItUpButton markItUpButton5-68 "><a
                                                                            href="" title="">KRAKEN Bronze</a></li>
                                                                <li class="markItUpButton markItUpButton5-69 "><a
                                                                            href="" title="">KRAKEN Gold</a></li>
                                                                <li class="markItUpButton markItUpButton5-70 "><a
                                                                            href="" title="">KRAKEN Platinum
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-71 "><a
                                                                            href="" title="">KRAKEN Bronze
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-72 "><a
                                                                            href="" title="">KRAKEN Gold (Lifeforms)</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-73 "><a
                                                                            href="" title="">KRAKEN Silver
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-74 "><a
                                                                            href="" title="">KRAKEN Platinum</a></li>
                                                                <li class="markItUpButton markItUpButton5-75 "><a
                                                                            href="" title="">KRAKEN Silver</a></li>
                                                                <li class="markItUpButton markItUpButton5-76 "><a
                                                                            href="" title="">Bronze Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-77 "><a
                                                                            href="" title="">Bronze Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-78 "><a
                                                                            href="" title="">Bronze Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-79 "><a
                                                                            href="" title="">Silver Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-80 "><a
                                                                            href="" title="">Silver Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-81 "><a
                                                                            href="" title="">Silver Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-82 "><a
                                                                            href="" title="">Gold Metal Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-83 "><a
                                                                            href="" title="">Gold Metal Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-84 "><a
                                                                            href="" title="">Gold Metal Booster</a></li>
                                                                <li class="markItUpButton markItUpButton5-85 "><a
                                                                            href="" title="">Platinum Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-86 "><a
                                                                            href="" title="">Platinum Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-87 "><a
                                                                            href="" title="">Platinum Metal Booster</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-88 "><a
                                                                            href="" title="">Bronze Moon Fields</a></li>
                                                                <li class="markItUpButton markItUpButton5-89 "><a
                                                                            href="" title="">Gold Moon Fields</a></li>
                                                                <li class="markItUpButton markItUpButton5-90 "><a
                                                                            href="" title="">Platinum Moon Fields</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-91 "><a
                                                                            href="" title="">Silver Moon Fields</a></li>
                                                                <li class="markItUpButton markItUpButton5-92 "><a
                                                                            href="" title="">Bronze M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-93 "><a
                                                                            href="" title="">Bronze M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-94 "><a
                                                                            href="" title="">Gold M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-95 "><a
                                                                            href="" title="">Gold M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-96 "><a
                                                                            href="" title="">Silver M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-97 "><a
                                                                            href="" title="">Silver M.O.O.N.S.</a></li>
                                                                <li class="markItUpButton markItUpButton5-98 "><a
                                                                            href="" title="">NEWTRON Bronze</a></li>
                                                                <li class="markItUpButton markItUpButton5-99 "><a
                                                                            href="" title="">NEWTRON Gold</a></li>
                                                                <li class="markItUpButton markItUpButton5-100 "><a
                                                                            href="" title="">NEWTRON Bronze
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-101 "><a
                                                                            href="" title="">NEWTRON Gold
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-102 "><a
                                                                            href="" title="">NEWTRON Platinum
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-103 "><a
                                                                            href="" title="">NEWTRON Silver
                                                                        (Lifeforms)</a></li>
                                                                <li class="markItUpButton markItUpButton5-104 "><a
                                                                            href="" title="">NEWTRON Platinum</a></li>
                                                                <li class="markItUpButton markItUpButton5-105 "><a
                                                                            href="" title="">NEWTRON Silver</a></li>
                                                                <li class="markItUpButton markItUpButton5-106 "><a
                                                                            href="" title="">Bronze Planet Fields</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-107 "><a
                                                                            href="" title="">Gold Planet Fields</a></li>
                                                                <li class="markItUpButton markItUpButton5-108 "><a
                                                                            href="" title="">Platinum Planet Fields</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-109 "><a
                                                                            href="" title="">Silver Planet Fields</a>
                                                                </li>
                                                                <li class="markItUpButton markItUpButton5-110 "><a
                                                                            href="" title="">Complete Resource
                                                                        Package</a></li>
                                                                <li class="markItUpButton markItUpButton5-111 "><a
                                                                            href="" title="">Crystal Package</a></li>
                                                                <li class="markItUpButton markItUpButton5-112 "><a
                                                                            href="" title="">Deuterium Package</a></li>
                                                                <li class="markItUpButton markItUpButton5-113 "><a
                                                                            href="" title="">Metal Package</a></li>
                                                            </ul>
                                                            <span class="dropdown_arr"></span></li>
                                                        <li class="markItUpButton markItUpButton6 player"><a href=""
                                                                                                             title="Player">Player</a>
                                                        </li>
                                                        <li class="markItUpSeparator">-</li>
                                                        <li class="markItUpButton markItUpButton7 leftAlign"><a href=""
                                                                                                                title="Left align">Left
                                                                align</a></li>
                                                        <li class="markItUpButton markItUpButton8 centerAlign"><a
                                                                    href="" title="Centre align">Centre align</a></li>
                                                        <li class="markItUpButton markItUpButton9 rightAlign"><a href=""
                                                                                                                 title="Right align">Right
                                                                align</a></li>
                                                        <li class="markItUpButton markItUpButton10 justifyAlign"><a
                                                                    href="" title="Justify">Justify</a></li>
                                                        <li class="markItUpSeparator">-</li>
                                                        <li class="markItUpButton markItUpButton11 code"><a href=""
                                                                                                            title="Code">Code</a>
                                                        </li>
                                                        <li class="markItUpSeparator">-</li>
                                                        <li class="markItUpButton markItUpButton12 email"><a href=""
                                                                                                             accesskey="E"
                                                                                                             title="Email [Ctrl+E]">Email</a>
                                                        </li>
                                                        <li class="markItUpButton markItUpButton13 preview"
                                                            style="display: none;"><a href=""
                                                                                      title="Preview">Preview</a></li>
                                                    </ul>
                                                </div>
                                                <textarea name="text"
                                                          class="new_msg_textarea markItUpEditor"></textarea>
                                                <div class="miu_footer clearfix"><a role="button"
                                                                                    class="fright txt_link btn_blue preview_link">Preview</a><span
                                                            class="fleft"><span class="cnt_chars">2000</span> Characters remaining</span>
                                                </div>
                                                <div class="miu_preview_container" style="display: none;">

                                                    <script type="text/javascript">
                                                        initBBCodes();
                                                    </script>
                                                </div>
                                                <input type="hidden" class="colorpicker">
                                                <div class="markItUpFooter"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script language="javascript">
                                initBBCodeEditor(locaKeys, itemNames, false, '.new_msg_textarea', 2000, true);
                            </script>
                        </form>
                    </div>

                    <ul class="tab_inner ctn_with_new_msg clearfix">
                        <ul class="pagination">
                            <li class="paginator" data-tab="10" data-page="1">|&lt;&lt;</li>
                            <li class="paginator" data-tab="10" data-page="1">&lt;</li>
                            <li class="curPage" data-tab="10">1/1</li>
                            <li class="paginator" data-tab="10" data-page="1">&gt;</li>
                            <li class="paginator" data-tab="10" data-page="1">&gt;&gt;|</li>
                        </ul>
                        <input type="hidden" name="token" value="b5e7750a20009bf4c875f592bcc7a432">
                        <li class="msg " data-msg-id="4543048">
                            <div class="msg_status"></div>
                            <div class="msg_head">
                                <span class="msg_title blue_txt">Broadcast by <span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_?page=ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_page=highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                                <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">08.04.2024 09:34:48</span>
    </span>
                                <br>
                                <span class="msg_sender_label">From:</span>
                                <span class="msg_sender"><span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_page=ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                            </div>
                            <span class="msg_content">
        Hey
    </span>
                            <message-footer class="msg_actions">
                                <message-footer-actions>


                                </message-footer-actions>
                                <message-footer-details>
                                </message-footer-details>
                            </message-footer>
                            <script type="text/javascript">
                                initOverlays();
                            </script>

                        </li>
                        <li class="msg " data-msg-id="4542257">
                            <div class="msg_status"></div>
                            <div class="msg_head">
                                <span class="msg_title blue_txt">Broadcast by <span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_game/index.php?page=highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                                <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">08.04.2024 09:15:04</span>
    </span>
                                <br>
                                <span class="msg_sender_label">From:</span>
                                <span class="msg_sender"><span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_index.php?page=ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_?page=highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                            </div>
                            <span class="msg_content">
        hi there
    </span>
                            <message-footer class="msg_actions">
                                <message-footer-actions>


                                </message-footer-actions>
                                <message-footer-details>
                                </message-footer-details>
                            </message-footer>
                            <script type="text/javascript">
                                initOverlays();
                            </script>

                        </li>
                        <li class="msg " data-msg-id="4269146">
                            <div class="msg_status"></div>
                            <div class="msg_head">
                                <span class="msg_title blue_txt">Broadcast by <span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_game/index.php?page=ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_page=highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                                <span class="fright">
                            <a href="javascript: void(0);" class="fright">
                <span class="icon_nf icon_refuse js_actionKill tooltip js_hideTipOnMobile" title="delete"></span>
            </a>

        <span class="msg_date fright">03.04.2024 07:50:11</span>
    </span>
                                <br>
                                <span class="msg_sender_label">From:</span>
                                <span class="msg_sender"><span
                                            title="Player: President Hati2|<table class=&quot;playerTooltip&quot;><tr><th>Homeworld</th><td><a target=&quot;_parent&quot; href=&quot;#TODOpage=ingame&amp;component=galaxy&amp;galaxy=7&amp;system=158&quot;>[7:158:10]</a></td></tr><tr><th>Highscore ranking</th><td><a target=&quot;_parent&quot; href=&quot;#TODO_page=highscore&amp;searchRelId=113970&amp;category=1&quot;>1147</a></td></tr></table>"
                                            class="tooltipHTML tooltipCustom player advice">President Hati2</span></span>
                            </div>
                            <span class="msg_content">
        heello players
    </span>
                            <message-footer class="msg_actions">
                                <message-footer-actions>


                                </message-footer-actions>
                                <message-footer-details>
                                </message-footer-details>
                            </message-footer>
                            <script type="text/javascript">
                                initOverlays();
                            </script>

                        </li>
                        <ul class="pagination">
                            <li class="paginator" data-tab="10" data-page="1">|&lt;&lt;</li>
                            <li class="paginator" data-tab="10" data-page="1">&lt;</li>
                            <li class="curPage" data-tab="10">1/1</li>
                            <li class="paginator" data-tab="10" data-page="1">&gt;</li>
                            <li class="paginator" data-tab="10" data-page="1">&gt;&gt;|</li>
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
            <div id="ui-id-34" aria-live="polite" aria-labelledby="ui-id-33" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
            <div id="ui-id-36" aria-live="polite" aria-labelledby="ui-id-35" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
            <div id="ui-id-38" aria-live="polite" aria-labelledby="ui-id-37" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
            <div id="ui-id-40" aria-live="polite" aria-labelledby="ui-id-39" role="tabpanel"
                 class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                 style="display: none;"></div>
        </div>
    </div>
</div>
