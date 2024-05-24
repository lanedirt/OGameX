@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="content">
        <div id="buttonz">
            <div class="header">
                <h2>News feed</h2>
            </div>
            <div class="content">
                <div class="js_tabs tabs_wrap ui-tabs ui-corner-all ui-widget ui-widget-content">
                    <ul class="tabs_btn ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header"
                        role="tablist">
                        <li id="tabs-nfFleets"
                            class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active"
                            data-tabid="2" role="tab" tabindex="0" aria-controls="ui-id-2" aria-labelledby="ui-id-1"
                            aria-selected="true" aria-expanded="true">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets']) }}"
                               class="tabs_btn_img tb_fleets ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'fleets']) }}" role="presentation" tabindex="-1"
                               id="ui-id-1">
                                @if ($unread_messages_count['fleets'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['fleets'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Fleets</span>
                            </a>
                        </li>
                        <li id="tabs-nfCommunication"
                            class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab" data-tabid="1"
                            role="tab" tabindex="-1" aria-controls="ui-id-4" aria-labelledby="ui-id-3"
                            aria-selected="false" aria-expanded="false">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'communication']) }}"
                               class="tabs_btn_img tb_communication ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'communication']) }}" role="presentation" tabindex="-1"
                               id="ui-id-3">
                                @if ($unread_messages_count['communication'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['communication'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Communication</span>
                            </a>
                        </li>
                        <li id="tabs-nfEconomy" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                            data-tabid="3" role="tab" tabindex="-1" aria-controls="ui-id-6" aria-labelledby="ui-id-5"
                            aria-selected="false" aria-expanded="false">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'economy']) }}"
                               class="tabs_btn_img tb_economy ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'economy']) }}" role="presentation" tabindex="-1"
                               id="ui-id-5">
                                @if ($unread_messages_count['economy'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['economy'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Economy</span>
                            </a>
                        </li>
                        <li id="tabs-nfUniverse" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                            data-tabid="5" role="tab" tabindex="-1" aria-controls="ui-id-8" aria-labelledby="ui-id-7"
                            aria-selected="false" aria-expanded="false">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'universe']) }}"
                               class="tabs_btn_img tb_universe ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'universe']) }}" role="presentation" tabindex="-1"
                               id="ui-id-7">
                                @if ($unread_messages_count['universe'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['universe'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Universe</span>
                            </a>
                        </li>
                        <li id="tabs-nfSystem" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                            data-tabid="4" role="tab" tabindex="-1" aria-controls="ui-id-10" aria-labelledby="ui-id-9"
                            aria-selected="false" aria-expanded="false">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'system']) }}"
                               class="tabs_btn_img tb_system ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'system']) }}" role="presentation" tabindex="-1"
                               id="ui-id-9">
                                @if ($unread_messages_count['system'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['system'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">OGame</span>
                            </a>
                        </li>
                        <li id="tabs-nfFavorites" class="list_item ui-tabs-tab ui-corner-top ui-state-default ui-tab"
                            data-tabid="6" role="tab" tabindex="-1" aria-controls="ui-id-12" aria-labelledby="ui-id-11"
                            aria-selected="false" aria-expanded="false">
                            <a href="{{ route('messages.ajax.gettabcontents', ['tab' => 'favorites']) }}"
                               class="tabs_btn_img tb_favorites premiumHighligt ui-tabs-anchor"
                               rel="{{ route('messages.ajax.gettabcontents', ['tab' => 'favorites']) }}" role="presentation" tabindex="-1"
                               id="ui-id-11">
                                @if ($unread_messages_count['favorites'] > 0)
                                    <span class="new_msg_count">{{ $unread_messages_count['favorites'] }}</span>
                                @endif
                                <img src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="54" width="54">
                                <div class="marker"></div>
                                <span class="icon_caption">Favourites</span>
                            </a>
                        </li>
                    </ul>
                    <div id="ui-id-2" aria-live="polite" aria-labelledby="ui-id-1" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="false">
                    </div>
                    <div id="ui-id-4" aria-live="polite" aria-labelledby="ui-id-3" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                         style="display: none;"></div>
                    <div id="ui-id-6" aria-live="polite" aria-labelledby="ui-id-5" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                         style="display: none;"></div>
                    <div id="ui-id-8" aria-live="polite" aria-labelledby="ui-id-7" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                         style="display: none;"></div>
                    <div id="ui-id-10" aria-live="polite" aria-labelledby="ui-id-9" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                         style="display: none;"></div>
                    <div id="ui-id-12" aria-live="polite" aria-labelledby="ui-id-11" role="tabpanel"
                         class="ui-tabs-panel ui-corner-bottom ui-widget-content" aria-hidden="true"
                         style="display: none;"></div>
                    <div class="ajax_load_shadow clearfix" style="display: none;">
                        <img class="ajax_load_img64px" src="/img/icons/4161a64a933a5345d00cb9fdaa25c7.gif" alt="load..."
                             height="64" width="64">
                    </div>
                </div>
                <div class="footer">
                </div>
            </div>
        </div>


        <script type="text/javascript">
            function deleteBuddy() {
                var $thisObj = $(this);
                errorBoxDecision(
                    'Delete buddy',
                    $thisObj.attr("ref"),
                    'yes',
                    'No',
                    function () {
                        var buddyAction = 10;
                        var actionId = $thisObj.attr("id");

                        $("#buddies .ajaxContent").html("<p class=\"ajaxLoad\">load...</p>");

                        $.post("#ingame&component=buddies&ajax=1",
                            {
                                action: buddyAction,
                                id: actionId,
                                ajax: 1
                            },
                            function (data) {
                                var ajaxContent = $(data).find('.content .buddylistContent');

                                $('.buddylistContent').html($(ajaxContent).html());

                                ogame.buddies.initBuddyList();
                            }
                        );
                    }
                );
            }

            function ajaxBuddySearch(e) {

                var searchTextField = $('.buddySearch');
                var searchValue = searchTextField.val();

                if (searchValue.length >= 2 || e.keyCode === 8 || e.keyCode === 46) {
                    var params = {};
                    if (searchValue.length > 0) {
                        params =
                            {
                                "search": searchValue,
                                "action": 15,
                                "ajax": "1"
                            };
                    } else {
                        params =
                            {
                                "action": 9,
                                "ajax": "1"
                            };
                    }

                    $.post(
                        "#ingame&component=buddies&ajax=1",
                        params,
                        function (data) {
                            var ajaxTableContent = $(data).find('#buddylist');
                            $('#buddylist').html($(ajaxTableContent).html());
                            ogame.buddies.initBuddyList();
                        }
                    );

                } else {
                    if (e.keyCode === 13) {
                        fadeBox("Too few characters! Please put in at least 2 characters.", true);
                    }
                }
            }

            function cancelRequest() {
                var buddyAction = 3;
                var actionId = $(this).data('buddyid');
                var buddyCount = parseInt($("#ownRequestCount").text()) - 1;

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {
                        $("#ownRequestCount").html(buddyCount);

                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }

            function acceptRequest() {
                var buddyAction = 5;
                var actionId = $(this).data('buddyid');

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {
                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }

            function reportRequest() {
                var buddy = $(this).data('buddyid');
                var id = $(this).data('requestid');
                var icon = $(this).parent();

                function sendReport() {
                    $.ajax({
                        type: 'POST',
                        url: '?page=reportSpam_ajax',
                        dataType: 'json',
                        data: {
                            buddyId: buddy,
                            requestId: id
                        },
                        success: function (data) {
                            icon.hide();

                            fadeBox(data.message, !data.result);
                        },
                        error: function () {
                        }
                    });
                }

                errorBoxDecision(
                    "Caution",
                    "Report this message to a game operator?",
                    "yes",
                    "No",
                    sendReport
                );

                return true;
            }

            function rejectRequest() {
                var buddyAction = 4;
                var actionId = $(this).data('buddyid');
                var buddyCount = parseInt($("#newRequestCount").text()) - 1;

                $.post("#ingame&component=buddies&ajax=1",
                    {
                        action: buddyAction,
                        id: actionId
                    },
                    function (data) {

                        if (buddyCount >= 0) {
                            $("#newRequestCount").html(buddyCount);
                        }
                        var currentlocation = window.location.href;
                        window.location = currentlocation.substring(0, currentlocation.indexOf('?')) + '?page=ingame&component=buddies';
                    });
            }
        </script>

        <script language="javascript">
            // passing variables to javascript
            var localizedBBCode = {
                "bold": "Bold",
                "italic": "Italic",
                "underline": "Underline",
                "stroke": "Strikethrough",
                "sub": "Subscript",
                "sup": "Superscript",
                "fontColor": "Font colour",
                "fontSize": "Font size",
                "backgroundColor": "Background colour",
                "backgroundImage": "Background image",
                "tooltip": "Tool-tip",
                "alignLeft": "Left align",
                "alignCenter": "Centre align",
                "alignRight": "Right align",
                "alignJustify": "Justify",
                "block": "Break",
                "code": "Code",
                "spoiler": "Spoiler",
                "moreopts": "More Options",
                "list": "List",
                "hr": "Horizontal line",
                "picture": "Image",
                "link": "Link",
                "email": "Email",
                "player": "Player",
                "item": "Item",
                "coordinates": "Coordinates",
                "preview": "Preview",
                "textPlaceHolder": "Text...",
                "playerPlaceHolder": "Player ID or name",
                "itemPlaceHolder": "Item ID",
                "coordinatePlaceHolder": "Galaxy:system:position",
                "charsLeft": "Characters remaining",
                "colorPicker": {"ok": "Ok", "cancel": "Cancel", "rgbR": "R", "rgbG": "G", "rgbB": "B"},
                "backgroundImagePicker": {"ok": "Ok", "repeatX": "Repeat horizontally", "repeatY": "Repeat vertically"}
            };
            var itemNames = {
                "1aa36213cb676fd5baad5edc2bee4fbe117a778b": "Researchers",
                "6c9fe5e35bdad0d4e3382eb6a5aeac6bc8263752": "Traders",
                "9b48e257cbef6c5df0f03a47cead7f9abda3d437": "Warriors",
                "090a969b05d1b5dc458a6b1080da7ba08b84ec7f": "Bronze Crystal Booster",
                "e254352ac599de4dd1f20f0719df0a070c623ca8": "Bronze Deuterium Booster",
                "b956c46faa8e4e5d8775701c69dbfbf53309b279": "Bronze Metal Booster",
                "2dd05cc4c0e185fce2e712112dc44932027aee98": "Discoverer",
                "9374c79a24b84c4331f0d26526ef6c2d33319a6e": "Collector",
                "77eff880829027daf23b755e14820a60c4c6fd93": "General",
                "3c9f85221807b8d593fa5276cdf7af9913c4a35d": "Bronze Crystal Booster",
                "060902a23da9dd917f1a754fe85734a91ec8d785": "Bronze Crystal Booster",
                "bb7579f7a21152a4a256f001d5162765e2f2c5b9": "Bronze Crystal Booster",
                "422db99aac4ec594d483d8ef7faadc5d40d6f7d3": "Silver Crystal Booster",
                "5b69663e3ba09a1fe77cf72c5094e246cfe954d6": "Silver Crystal Booster",
                "04d8afd5936976e32ce894b765ea8bd168aa07ef": "Silver Crystal Booster",
                "118d34e685b5d1472267696d1010a393a59aed03": "Gold Crystal Booster",
                "36fb611e71d42014f5ebd0aa5a52bc0c81a0c1cb": "Gold Crystal Booster",
                "d45f00e8b909f5293a83df4f369737ea7d69c684": "Gold Crystal Booster",
                "35d96e441c21ef112a84c618934d9d0f026998fd": "Platinum Crystal Booster",
                "6bf45fcba8a6a68158273d04a924452eca75cf39": "Platinum Crystal Booster",
                "7c2edf40c5cd54ad11c6439398b83020c0a7a6be": "Platinum Crystal Booster",
                "d3d541ecc23e4daa0c698e44c32f04afd2037d84": "DETROID Bronze",
                "0968999df2fe956aa4a07aea74921f860af7d97f": "DETROID Gold",
                "3347bcd4ee59f1d3fa03c4d18a25bca2da81de82": "DETROID Platinum",
                "27cbcd52f16693023cb966e5026d8a1efbbfc0f9": "DETROID Silver",
                "d9fa5f359e80ff4f4c97545d07c66dbadab1d1be": "Bronze Deuterium Booster",
                "d50005c05fd5b95125364af43c78dfaba64d7f83": "Bronze Deuterium Booster",
                "63d11915e9af76ee41938cc099dbf8d54ad59a17": "Bronze Deuterium Booster",
                "e4b78acddfa6fd0234bcb814b676271898b0dbb3": "Silver Deuterium Booster",
                "26416a3cdb94613844b1d3ca78b9057fd6ae9b15": "Silver Deuterium Booster",
                "6f0952a919fd2ab9c009e9ccd83c1745f98f758f": "Silver Deuterium Booster",
                "5560a1580a0330e8aadf05cb5bfe6bc3200406e2": "Gold Deuterium Booster",
                "300493ddc756869578cb2888a3a1bc0c3c66765f": "Gold Deuterium Booster",
                "dc5896bed3311434224d511fa7ced6fdbe41b4e8": "Gold Deuterium Booster",
                "4b51d903560edd102467b110586000bd64fdb954": "Platinum Deuterium Booster",
                "620f779dbffa1011aded69b091239727910a3d03": "Platinum Deuterium Booster",
                "831c3ea8d868eb3601536f4d5e768842988a1ba9": "Platinum Deuterium Booster",
                "3f6f381dc9b92822406731a942c028adf8dc978f": "Energy Booster Bronze",
                "7eeeb36a455c428eb6923a50d2f03544b6dd05d6": "Energy Booster Bronze",
                "6837c08228d2b023fb955ca2dc589a0a4bed3ba8": "Energy Booster Bronze",
                "c2bad58fcec374d709099d11d0549e59ea7e233e": "Energy Booster Silver",
                "bedd248aaf288c27e9351cfacfa6be03f1dbb898": "Energy Booster Silver",
                "e05aa5b9e3df5be3857b43da8403eafbf5ad3b96": "Energy Booster Silver",
                "55b52cbfb148ec80cd4e5b0580f7bed01149d643": "Energy Booster Gold",
                "4fa9a2273ee446284d5177fd9d60a22de01e932b": "Energy Booster Gold",
                "5ad783dcfce3655ef97b36197425718a0dad6b66": "Energy Booster Gold",
                "77c36199102e074dca46f5f26ef57ce824d044dd": "Energy Booster Platinum",
                "dfe86378f8c3d7f3ee0790ea64603bc44e83ca47": "Energy Booster Platinum",
                "c39aa972a971e94b1d9b4d7a8f734b3d8be12534": "Energy Booster Platinum",
                "8c1f6c6849d1a5e4d9de6ae9bb1b861f6f7b5d4d": "Bronze Expedition Slots",
                "e54ecc0416d6e96b4165f24238b03a1b32c1df47": "Bronze Expedition Slots",
                "a5784c685c0e1e6111d9c18aeaf80af2e0777ab4": "Bronze Expedition Slots",
                "31a504be1195149a3bef05b9cc6e3af185d24ef2": "Silver Expedition Slots",
                "b2bc9789df7c1ef5e058f72d61380b696dde54e8": "Silver Expedition Slots",
                "4f6f941bbf2a8527b0424b3ad11014502d8f4fb8": "Silver Expedition Slots",
                "fd7d35e73d0e09e83e30812b738ef966ea9ef790": "Gold Expedition Slots",
                "9336b9f29d36e3f69b0619c9523d8bec5e09ab8e": "Gold Expedition Slots",
                "540410439514ac09363c5c47cf47117a8b8ae79a": "Gold Expedition Slots",
                "94a28491b6fd85003f1cb151e88dde106f1d7596": "Bronze Fleet Slots",
                "0684c6a5a42acbb3cd134913d421fc28dae6b90d": "Bronze Fleet Slots",
                "bb47add58876240199a18ddacc2db07789be1934": "Bronze Fleet Slots",
                "c4e598a85805a7eb3ca70f9265cbd366fc4d2b0e": "Silver Fleet Slots",
                "f8fd610825fb4a442e27e4e9add74f050e040e27": "Silver Fleet Slots",
                "a693c5ce3f5676efaaf0781d94234bea4f599d2e": "Silver Fleet Slots",
                "1808bf7639b81ac3ac87bcb7eb3bbba0a1874d0a": "Gold Fleet Slots",
                "5a8000c372cd079292a92d35d4ddba3c0f348d3b": "Gold Fleet Slots",
                "1f7024c4f6493f0c589e1b00c76e6ced258c00e5": "Gold Fleet Slots",
                "40f6c78e11be01ad3389b7dccd6ab8efa9347f3c": "KRAKEN Bronze",
                "929d5e15709cc51a4500de4499e19763c879f7f7": "KRAKEN Gold",
                "c19f0e09d862d93d7956beb3185d9ee929b5ef74": "KRAKEN Platinum (Lifeforms)",
                "00b42f7113d81f98df865bbfa2280fe3a4465e89": "KRAKEN Bronze (Lifeforms)",
                "0ad06bba14dfd0b576f1daef729a60753e2263c7": "KRAKEN Gold (Lifeforms)",
                "5f194777c5b69d5c2a3c68e9e04a4cae9c28bcf2": "KRAKEN Silver (Lifeforms)",
                "f36042d76e6b8b33d931e1d4ae99f35265cd82d1": "KRAKEN Platinum",
                "4a58d4978bbe24e3efb3b0248e21b3b4b1bfbd8a": "KRAKEN Silver",
                "de922af379061263a56d7204d1c395cefcfb7d75": "Bronze Metal Booster",
                "8a469c50ed10b78eaf872ea766ca66495da31a17": "Bronze Metal Booster",
                "9ce31395cbd1e60d29e0770b9e20c6eb6053a344": "Bronze Metal Booster",
                "ba85cc2b8a5d986bbfba6954e2164ef71af95d4a": "Silver Metal Booster",
                "742743b3b0ae1f0b8a1e01921042810b58f12f39": "Silver Metal Booster",
                "6f44dcd2bd84875527abba69158b4e976c308bbc": "Silver Metal Booster",
                "05294270032e5dc968672425ab5611998c409166": "Gold Metal Booster",
                "6fecb993169fe918d9c63cd37a2e541cc067664e": "Gold Metal Booster",
                "21c1a65ca6aecf54ffafb94c01d0c60d821b325d": "Gold Metal Booster",
                "a83cfdc15b8dba27c82962d57e50d8101d263cfb": "Platinum Metal Booster",
                "c690f492cffe5f9f2952337e8eed307a8a62d6cf": "Platinum Metal Booster",
                "ca7f903a65467b70411e513b0920d66c417aa3a2": "Platinum Metal Booster",
                "be67e009a5894f19bbf3b0c9d9b072d49040a2cc": "Bronze Moon Fields",
                "05ee9654bd11a261f1ff0e5d0e49121b5e7e4401": "Gold Moon Fields",
                "8a426241572b2fea57844acd99bc326fe40e35cf": "Platinum Moon Fields",
                "c21ff33ba8f0a7eadb6b7d1135763366f0c4b8bf": "Silver Moon Fields",
                "485a6d5624d9de836d3eb52b181b13423f795770": "Bronze M.O.O.N.S.",
                "d94731aa4a989f741ca18dd7d16589e970f0486f": "Bronze M.O.O.N.S.",
                "45d6660308689c65d97f3c27327b0b31f880ae75": "Gold M.O.O.N.S.",
                "faab6a750c53d440cd5a1638dbd853ef4ecb1fec": "Gold M.O.O.N.S.",
                "fd895a5c9fd978b9c5c7b65158099773ba0eccef": "Silver M.O.O.N.S.",
                "8ecde49bed4d3da1c3266ab736cb0c1a3dc209aa": "Silver M.O.O.N.S.",
                "da4a2a1bb9afd410be07bc9736d87f1c8059e66d": "NEWTRON Bronze",
                "8a4f9e8309e1078f7f5ced47d558d30ae15b4a1b": "NEWTRON Gold",
                "ba3e6693f112986b7964c835bcac6ae201900e2f": "NEWTRON Bronze (Lifeforms)",
                "7fe4cdb098685f8af827ca460a56e00ef46f5f05": "NEWTRON Gold (Lifeforms)",
                "9cde936fabc5037617f8261955e7d3f2262eec69": "NEWTRON Platinum (Lifeforms)",
                "9879a36c42797a868416b13f07e033f664cabd70": "NEWTRON Silver (Lifeforms)",
                "a1ba242ede5286b530cdf991796b3d1cae9e4f23": "NEWTRON Platinum",
                "d26f4dab76fdc5296e3ebec11a1e1d2558c713ea": "NEWTRON Silver",
                "16768164989dffd819a373613b5e1a52e226a5b0": "Bronze Planet Fields",
                "04e58444d6d0beb57b3e998edc34c60f8318825a": "Gold Planet Fields",
                "f3d9b82e10f2e969209c1a5ad7d22181c703bb36": "Platinum Planet Fields",
                "0e41524dc46225dca21c9119f2fb735fd7ea5cb3": "Silver Planet Fields",
                "c1d0232604872f899ea15a9772baf76880f55c5f": "Complete Resource Package",
                "bb2f6843226ef598f0b567b92c51b283de90aa48": "Crystal Package",
                "cb72ed207dd871832a850ee29f1c1f83aa3f4f36": "Deuterium Package",
                "859d82d316b83848f7365d21949b3e1e63c7841f": "Metal Package"
            };
            var loca = {
                "LOCA_SETTINGS_NEWSFEED": "News feed",
                "LOCA_ALL_AJAXLOAD": "load...",
                "LOCA_GALAXY_ERROR_OCCURED": "An error has occurred",
                "LOCA_MSG_ADD_FAV": "mark as favourite",
                "LOCA_MSG_DELETE_FAV": "remove from favourites"
            };

            (function ($) {
                ogame.messages.initMessages('d99f68937305e0b2c3ff3f059259fcec');
                requestsReady();
            })(jQuery);
        </script>
    </div>
@endsection
