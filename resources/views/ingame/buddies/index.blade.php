@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="background: #4CAF50; color: white; padding: 10px; margin: 10px; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="background: #f44336; color: white; padding: 10px; margin: 10px; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="background: #f44336; color: white; padding: 10px; margin: 10px; border-radius: 4px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="buddiescomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>Buddies</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>My buddies</h2>
            </div>
            <div class="content">
                <div id="buddyRequests" class="js_accordion ui-accordion ui-widget ui-helper-reset" role="tablist">
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab" id="ui-id-1" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Buddy requests ({{ count($receivedRequests) + count($sentRequests) }})</h3>
                    <div class="js_tabs ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active ui-tabs ui-corner-all ui-widget" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="">
                        <ul class="tabsbelow ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                            <li role="tab" tabindex="0" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" aria-controls="tabs-reqReveived" aria-labelledby="ui-id-3" aria-selected="true" aria-expanded="true">
                                <a href="#tabs-reqReveived" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-3">
                                    <span id="newRequestCount">{{ count($receivedRequests) }}</span> requests received (<span id="newRequestCountNew">{{ $newRequestCount }}</span> new)
                                </a>
                            </li>
                            <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="tabs-reqSent" aria-labelledby="ui-id-4" aria-selected="false" aria-expanded="false">
                                <a href="#tabs-reqSent" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-4">
                                    <span id="ownRequestCount">{{ count($sentRequests) }}</span> requests sent
                                </a>
                            </li>
                        </ul>
                        <div id="tabs-reqReveived" class="tab_ctn js_scrollbar ui-tabs-panel ui-corner-bottom ui-widget-content mCustomScrollbar _mCS_1" aria-labelledby="ui-id-3" role="tabpanel" aria-hidden="false"><div id="mCSB_1" class="mCustomScrollBox mCS-ogame mCSB_vertical mCSB_inside" style="max-height: none;" tabindex="0"><div id="mCSB_1_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                    <ul class="clearfix">
                                        @forelse($receivedRequests as $request)
                                        <li class="msg msg_new" data-msg-id="{{ $request->id }}">
                                            <div class="msg_status"></div>
                                            <div class="msg_head">
                                                <span class="msg_title new blue_txt">New buddy request</span>
                                                <span class="msg_date fright">
                                                    {{ $request->created_at->format('d.m.Y H:i:s') }}
                                                </span><br>
                                                <span class="msg_sender_label">From:</span>
                                                <span class="msg_sender">{{ $request->sender->username }}:</span>
                                            </div>
                                            @if($request->message)
                                            <span class="msg_content">{{ $request->message }}<br></span>
                                            @endif
                                            <div class="msg_actions clearfix">
                                                <form method="POST" action="{{ route('buddies.acceptRequest', $request->id) }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="fleft msg_action_link txt_link" style="background:none;border:none;padding:0;cursor:pointer;">
                                                        <span class="dark_highlight_tablet">Accept buddy request</span>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('buddies.rejectRequest', $request->id) }}" style="display:inline;margin-left:10px;">
                                                    @csrf
                                                    <button type="submit" class="fleft msg_action_link txt_link" style="background:none;border:none;padding:0;cursor:pointer;">
                                                        <span class="dark_highlight_tablet">Reject buddy request</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="no_req">You currently have no buddy requests.</li>
                                        @endforelse
                                    </ul>
                                </div><div id="mCSB_1_scrollbar_vertical" class="mCSB_scrollTools mCSB_1_scrollbar mCS-ogame mCSB_scrollTools_vertical" style="display: block;"><div class="mCSB_draggerContainer"><div id="mCSB_1_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 30px; display: block; height: 31px; max-height: 21px;"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div><div class="mCSB_draggerRail"></div></div></div></div></div></div>
                        <div id="tabs-reqSent" class="tab_ctn js_scrollbar ui-tabs-panel ui-corner-bottom ui-widget-content mCustomScrollbar _mCS_2 mCS_no_scrollbar" aria-labelledby="ui-id-4" role="tabpanel" aria-hidden="true" style="display: none;"><div id="mCSB_2" class="mCustomScrollBox mCS-ogame mCSB_vertical mCSB_inside" style="max-height: 87px;" tabindex="0"><div id="mCSB_2_container" class="mCSB_container mCS_y_hidden mCS_no_scrollbar_y" style="position:relative; top:0; left:0;" dir="ltr">
                                    <ul>
                                        @forelse($sentRequests as $request)
                                        <li class="msg msg_new" data-msg-id="{{ $request->id }}">
                                            <div class="msg_status"></div>
                                            <div class="msg_head">
                                                <span class="msg_title new blue_txt">Buddy request sent</span>
                                                <span class="msg_date fright">
                                                    {{ $request->created_at->format('d.m.Y H:i:s') }}
                                                </span><br>
                                                <span class="msg_sender_label">To:</span>
                                                <span class="msg_sender">{{ $request->receiver->username }}:</span>
                                            </div>
                                            @if($request->message)
                                            <span class="msg_content">{{ $request->message }}<br></span>
                                            @endif
                                            <div class="msg_actions clearfix">
                                                <form method="POST" action="{{ route('buddies.cancelRequest', $request->id) }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="fleft msg_action_link txt_link" style="background:none;border:none;padding:0;cursor:pointer;">
                                                        <span class="dark_highlight_tablet">Withdraw buddy request</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="no_req">You have not sent any buddy requests.</li>
                                        @endforelse
                                    </ul>
                                </div><div id="mCSB_2_scrollbar_vertical" class="mCSB_scrollTools mCSB_2_scrollbar mCS-ogame mCSB_scrollTools_vertical" style="display: none;"><div class="mCSB_draggerContainer"><div id="mCSB_2_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 30px; top: 0px;"><div class="mCSB_dragger_bar" style="line-height: 30px;"></div><div class="mCSB_draggerRail"></div></div></div></div></div></div>
                    </div>
                </div>

                <div id="addBuddySection" style="margin: 20px 0;">
                    <h3 style="margin-bottom: 10px;">Add New Buddy</h3>
                    <form method="POST" action="{{ route('buddies.sendRequest') }}" style="background: #1a1a2e; padding: 15px; border-radius: 4px;">
                        @csrf
                        <div style="margin-bottom: 10px;">
                            <label for="receiver_id" style="display: block; margin-bottom: 5px;">Player ID:</label>
                            <input type="number" name="receiver_id" id="receiver_id" required
                                   value="{{ request()->get('add', '') }}"
                                   style="width: 200px; padding: 5px; background: #16213e; color: white; border: 1px solid #0f3460; border-radius: 3px;">
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label for="message" style="display: block; margin-bottom: 5px;">Message (optional):</label>
                            <textarea name="message" id="message" rows="3" maxlength="500"
                                      style="width: 100%; max-width: 500px; padding: 5px; background: #16213e; color: white; border: 1px solid #0f3460; border-radius: 3px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"
                                style="background: #4CAF50; color: white; padding: 8px 20px; border: none; border-radius: 4px; cursor: pointer;">
                            Send Buddy Request
                        </button>
                    </form>
                </div>

                <div class="buddylistContent">
                    @if(count($buddies) > 0)
                    <table cellpadding="0" cellspacing="0" class="content_table" id="buddylist">
                        <colgroup>
                            <col span="1" style="width: 37px;">
                            <col span="1" style="width: 150px;">
                            <col span="1" style="width: 110px;">
                            <col span="1" style="width: 65px;">
                            <col span="1" style="width: 65px;">
                        </colgroup>
                        <thead>
                        <tr class="ct_head_row">
                            <th class="no ct_th first">ID</th>
                            <th class="ct_th ct_sortable_title">Name</th>
                            <th class="ct_th ct_sortable_title">Since</th>
                            <th class="ct_th ct_sortable_title">Status</th>
                            <th class="ct_th textCenter">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="zebra">
                        @foreach($buddies as $buddy)
                        <tr>
                            <td class="no">{{ $buddy->buddyUser->id }}</td>
                            <td>{{ $buddy->buddyUser->username }}</td>
                            <td>{{ $buddy->created_at->format('d.m.Y') }}</td>
                            <td>
                                @if($buddy->buddyUser->last_login_at && $buddy->buddyUser->last_login_at->gt(now()->subMinutes(15)))
                                    <span style="color: green;">Online</span>
                                @else
                                    <span style="color: gray;">Offline</span>
                                @endif
                            </td>
                            <td class="textCenter">
                                <form method="POST" action="{{ route('buddies.removeBuddy', $buddy->buddy_id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn_delete" onclick="return confirm('Are you sure you want to remove this buddy?');" title="Delete buddy" style="background:none;border:none;cursor:pointer;">
                                        <span class="icon icon_against"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="box_highlight textCenter no_buddies">No buddies found</p>
                    @endif
                    <span></span>
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

                                $.post("#",
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
                                "#",
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

                        $.post("#",
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

                        $.post("#",
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

                        $.post("#",
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

                <script type="text/javascript">
                    ogame.buddies.initBuddyList();
                    updateBuddyCount(0);

                    $('.buddySearch').keyup(function(e)
                    {
                        ajaxBuddySearch(e);
                    });

                    // Scroll to add buddy form if coming from galaxy view with player ID
                    @if(request()->has('add'))
                    setTimeout(function() {
                        document.getElementById('addBuddySection').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                    @endif
                </script>



                <div class="footer"></div>
            </div>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>Ignored Players</h2>
            </div>
            <div class="content">
                <table cellpadding="0" cellspacing="0" class="content_table ignorelist" id="buddylist">
                    <colgroup>
                        <col span="1" style="width: 37px;">
                        <col span="1" style="width: 150px;">
                        <col span="1" style="width: 110px;">
                        <col span="1" style="width: 65px;">
                        <col span="1" style="width: 65px;">
                    </colgroup>
                    <thead>
                    <tr class="ct_head_row">
                        <th class="no ct_th first">ID</th>
                        <th class="ct_th ct_sortable_title">Name</th>
                        <th class="ct_th ct_sortable_title">Rank</th>
                        <th class="ct_th ct_sortable_title">Alliance</th>
                        <th class="ct_th textCenter">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="zebra">
                    </tbody>
                </table>
                <div class="footer"></div>
            </div>
        </div>

        <script language="javascript">
            $(function() {
                requestsReady();
            });
            ogame.buddies.initBuddies();
            /* @TODO-merge old line from twig
            ogame.messagecounter.resetCounterByType(ogame.messagecounter.type_buddy);
             */
            ogame.messagecounter.resetCounterByType(ogame.messagecounter.type_buddy, '#+# unread conversation(s)');

            initBBCodes();
            initOverlays();

            function confirmUnignore(question,link)
            {
                errorBoxDecision(
                    "Cancel ignore",
                    ""+question+"",
                    "yes",
                    "No",
                    function() {
                        window.location.replace(link);
                    }
                );
            }
        </script>
    </div>

@endsection
