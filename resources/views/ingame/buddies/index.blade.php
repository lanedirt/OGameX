@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
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
                    <h3 class="ui-accordion-header ui-corner-top ui-state-default ui-accordion-header-active ui-state-active ui-accordion-icons" role="tab" id="ui-id-1" aria-controls="ui-id-2" aria-selected="true" aria-expanded="true" tabindex="0"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-s"></span>Buddy requests ({{ $received_requests->count() + $sent_requests->count() }})</h3>
                    <div class="js_tabs ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active ui-tabs ui-corner-all ui-widget" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false" style="">
                        <ul class="tabsbelow ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header" role="tablist">
                            <li role="tab" tabindex="0" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active" aria-controls="tabs-reqReveived" aria-labelledby="ui-id-3" aria-selected="true" aria-expanded="true">
                                <a href="#tabs-reqReveived" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-3">
                                    <span id="newRequestCount">{{ $received_requests->count() }}</span> requests received (<span id="unreadCount">{{ $unread_requests_count }}</span> new)
                                </a>
                            </li>
                            <li role="tab" tabindex="-1" class="ui-tabs-tab ui-corner-top ui-state-default ui-tab" aria-controls="tabs-reqSent" aria-labelledby="ui-id-4" aria-selected="false" aria-expanded="false">
                                <a href="#tabs-reqSent" role="presentation" tabindex="-1" class="ui-tabs-anchor" id="ui-id-4">
                                    <span id="ownRequestCount">{{ $sent_requests->count() }}</span> requests sent
                                </a>
                            </li>
                        </ul>
                        <div id="tabs-reqReveived" class="tab_ctn js_scrollbar" aria-labelledby="ui-id-3" role="tabpanel">
                                    <ul class="clearfix">
                                        @if($received_requests->isEmpty())
                                            <li class="no_req">You currently have no buddy requests.</li>
                                        @else
                                            @foreach($received_requests as $request)
                                                <li class="msg @if(!$request->viewed) msg_new @endif" data-msg-id="{{ $request->id }}">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title @if(!$request->viewed) new blue_txt @endif">@if(!$request->viewed)New @endif buddy request</span>
                                                        <span class="msg_date fright">
                                                            {{ $request->created_at->format('d.m.Y H:i:s') }}
                                                        </span><br>
                                                        <span class="msg_sender_label">From:</span>
                                                        <span class="msg_sender">{{ $request->sender->username }}</span>
                                                    </div>
                                                    @if($request->message)
                                                        <span class="msg_content">{{ $request->message }}<br></span>
                                                    @endif
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>
                                                            <gradient-button sq30="">
                                                                <button class="custom_btn tooltip acceptRequest" data-buddyid="{{ $request->id }}" data-tooltip-title="Accept buddy request">
                                                                    <span class="icon_nf icon_accept"></span>
                                                                </button>
                                                            </gradient-button>
                                                            <gradient-button sq30="">
                                                                <button class="custom_btn tooltip rejectRequest" data-buddyid="{{ $request->id }}" data-tooltip-title="Reject buddy request">
                                                                    <span class="icon_nf icon_refuse"></span>
                                                                </button>
                                                            </gradient-button>
                                                        </message-footer-actions>
                                                    </message-footer>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                        </div>
                        <div id="tabs-reqSent" class="tab_ctn js_scrollbar" aria-labelledby="ui-id-4" role="tabpanel" style="display: none;">
                                    <ul>
                                        @if($sent_requests->isEmpty())
                                            <li class="no_req">You have not sent any buddy requests.</li>
                                        @else
                                            @foreach($sent_requests as $request)
                                                <li class="msg msg_new" data-msg-id="{{ $request->id }}">
                                                    <div class="msg_status"></div>
                                                    <div class="msg_head">
                                                        <span class="msg_title new blue_txt">New buddy request</span>
                                                        <span class="msg_date fright">
                                                            {{ $request->created_at->format('d.m.Y H:i:s') }}
                                                        </span><br>
                                                        <span class="msg_sender_label">
                                                            To:
                                                        </span>
                                                        <span class="msg_sender">
                                                            {{ $request->receiver->username }}
                                                        </span>
                                                    </div>

                                                    <span class="msg_content">You have received a new buddy request from {{ $request->sender->username }}.<br>{{ $request->message }}</span>
                                                    <message-footer class="msg_actions">
                                                        <message-footer-actions>
                                                            <gradient-button sq30=""><button class="custom_btn tooltip cancelBuddyRequest cancelRequest" data-buddyid="{{ $request->id }}" data-tooltip-title="Withdraw buddy request"><img src="/img/icons/basic/refuse.png" style="width: 16px; height: 16px;"></button></gradient-button>
                                                        </message-footer-actions>
                                                    </message-footer>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                        </div>
                    </div>
                </div>

                <div class="buddylistContent">
                    @php
                        $onlineCount = 0;
                        $totalCount = $buddies->count();
                        foreach ($buddies as $buddyRequest) {
                            $buddy = $buddyRequest->sender_user_id === auth()->id()
                                ? $buddyRequest->receiver
                                : $buddyRequest->sender;
                            if ($buddy->isOnline()) {
                                $onlineCount++;
                            }
                        }
                    @endphp

                    <span class="fleft online_count">({{ $onlineCount }} / {{ $totalCount }} online)</span>
                    <input class="fright buddySearch" type="text" placeholder="Search...">
                    <br class="clearfloat">

                    @if($buddies->isEmpty())
                        <p class="box_highlight textCenter no_buddies">No buddies found</p>
                    @else
                        <table cellpadding="0" cellspacing="0" class="content_table" id="buddylist">
                            <colgroup>
                                <col span="1" style="width: 37px;">
                                <col span="1" style="width: 200px;">
                                <col span="1" style="width: 110px;">
                                <col span="1" style="width: 65px;">
                                <col span="1" style="width: 65px;">
                                <col span="1" style="width: 65px;">
                                <col span="1" style="width: 65px;">
                            </colgroup>
                            <thead>
                            <tr class="ct_head_row">
                                <th class="no ct_th first">ID</th>
                                <th class="ct_th ct_sortable_title">Name</th>
                                <th class="ct_th ct_sortable_title">Points</th>
                                <th class="ct_th ct_sortable_title">Rank</th>
                                <th class="ct_th ct_sortable_title">Alliance</th>
                                <th class="ct_th ct_sortable_title">Coords</th>
                                <th class="ct_th textCenter">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="zebra">
                                @include('ingame.buddies.partials.buddy-list', ['buddies' => $buddies])
                            </tbody>
                        </table>
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

                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('buddies.post') }}",
                                    data: {
                                        action: buddyAction,
                                        id: actionId,
                                        ajax: 1,
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function (data) {
                                        // Replace tbody content with the returned partial view
                                        $('#buddylist tbody').html(data);
                                        ogame.buddies.initBuddyList();
                                        fadeBox('Buddy deleted successfully!', false);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Delete buddy error:', xhr.responseJSON);
                                        var errorMsg = xhr.responseJSON && xhr.responseJSON.message
                                            ? xhr.responseJSON.message
                                            : 'Failed to delete buddy';
                                        fadeBox(errorMsg, true);
                                    }
                                });
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

                            params._token = "{{ csrf_token() }}";
                            $.post(
                                "{{ route('buddies.post') }}",
                                params,
                                function (data) {
                                    // Replace tbody content with the returned partial view
                                    $('#buddylist tbody').html(data);
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

                        $.post("{{ route('buddies.post') }}",
                            {
                                action: buddyAction,
                                id: actionId,
                                _token: "{{ csrf_token() }}"
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
                        var $button = $(this);
                        var $messageFooter = $button.closest('message-footer');

                        $.post("{{ route('buddies.post') }}",
                            {
                                action: buddyAction,
                                id: actionId,
                                _token: "{{ csrf_token() }}"
                            },
                            function (data) {
                                // Remove the action buttons and show accepted status
                                $messageFooter.find('message-footer-actions').html(
                                    '<span class="success" style="color: #6f9;padding: 5px;">✓ Buddy request accepted</span>'
                                );

                                fadeBox('Buddy request accepted!', false);

                                // If on buddies page, update the count
                                if (window.location.href.indexOf('component=buddies') > -1) {
                                    var buddyCount = parseInt($("#newRequestCount").text()) - 1;
                                    if (buddyCount >= 0) {
                                        $("#newRequestCount").html(buddyCount);
                                    }
                                }
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
                        var $button = $(this);
                        var $messageFooter = $button.closest('message-footer');

                        $.post("{{ route('buddies.post') }}",
                            {
                                action: buddyAction,
                                id: actionId,
                                _token: "{{ csrf_token() }}"
                            },
                            function (data) {
                                // Remove the action buttons and show rejected status
                                $messageFooter.find('message-footer-actions').html(
                                    '<span class="rejected" style="color: #f66;padding: 5px;">✗ Buddy request rejected</span>'
                                );

                                fadeBox('Buddy request rejected', false);

                                // If on buddies page, update the count
                                if (window.location.href.indexOf('component=buddies') > -1) {
                                    var buddyCount = parseInt($("#newRequestCount").text()) - 1;
                                    if (buddyCount >= 0) {
                                        $("#newRequestCount").html(buddyCount);
                                    }
                                }
                            });
                    }
                </script>

                <script type="text/javascript">
                    ogame.buddies.initBuddyList();
                    updateBuddyCount({{ $unread_requests_count }});

                    $('.buddySearch').keyup(function(e)
                    {
                        ajaxBuddySearch(e);
                    });

                    // Bind event handlers
                    $(document).on('click', '.cancelRequest', cancelRequest);
                    $(document).on('click', '.acceptRequest', acceptRequest);
                    $(document).on('click', '.rejectRequest', rejectRequest);
                </script>



                <div class="footer"></div>
            </div>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>Ignored Players</h2>
            </div>
            <div class="content">
                @if($ignored_players->isEmpty())
                    <p class="box_highlight textCenter">No ignored players</p>
                @else
                    <table cellpadding="0" cellspacing="0" class="content_table ignorelist" id="ignorelist">
                        <colgroup>
                            <col span="1" style="width: 37px;">
                            <col span="1" style="width: 150px;">
                            <col span="1" style="width: 65px;">
                        </colgroup>
                        <thead>
                        <tr class="ct_head_row">
                            <th class="no ct_th first">ID</th>
                            <th class="ct_th ct_sortable_title">Name</th>
                            <th class="ct_th textCenter">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="zebra">
                            @foreach($ignored_players as $ignored)
                                <tr>
                                    <td>{{ $ignored->ignoredUser->id }}</td>
                                    <td>{{ $ignored->ignoredUser->username }}</td>
                                    <td class="textCenter">
                                        <a href="#" class="txt_link" onclick="confirmUnignore('Do you really want to unignore {{ $ignored->ignoredUser->username }}?', '{{ route('buddies.unignore') }}?ignored_user_id={{ $ignored->ignoredUser->id }}'); return false;">
                                            <span class="icon icon_check"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
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
