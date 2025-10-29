@extends('ingame.layouts.main')

@section('content')

    <div id="buddiescomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Buddies</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="buddies">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div id="tabs">
                        <ul class="tabsbelow" id="tab-buddies">
                            <li class="aktiv" data-tab="buddies-list">
                                <a href="javascript:void(0);"><span>My Buddies ({{ count($buddies) }})</span></a>
                            </li>
                            <li data-tab="requests-received">
                                <a href="javascript:void(0);"><span>Requests Received ({{ count($receivedRequests) }})</span></a>
                            </li>
                            <li data-tab="requests-sent">
                                <a href="javascript:void(0);"><span>Requests Sent ({{ count($sentRequests) }})</span></a>
                            </li>
                            <li data-tab="add-buddy">
                                <a href="javascript:void(0);"><span>Add Buddy</span></a>
                            </li>
                        </ul>
                    </div>

                    <div class="clearfloat"></div>
                    <div class="alliance_wrapper">
                        <div class="allianceContent">

                            {{-- Success/Error Messages --}}
                            @if (session('success'))
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #6f9;">{{ session('success') }}</td>
                                    </tr>
                                </table>
                            @endif

                            @if (session('error'))
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #f66;">{{ session('error') }}</td>
                                    </tr>
                                </table>
                            @endif

                            @if ($errors->any())
                                <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="color: #f66;">
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- My Buddies Tab --}}
                            <div id="buddies-list" class="tab-content contentz" style="display: block;">
                                @if(count($buddies) > 0)
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Since</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        @foreach($buddies as $index => $buddy)
                                            <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                <td>{{ $buddy->buddyUser->id }}</td>
                                                <td>{{ $buddy->buddyUser->username }}</td>
                                                <td>{{ $buddy->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($buddy->buddyUser->last_login_at && $buddy->buddyUser->last_login_at->gt(now()->subMinutes(15)))
                                                        <span style="color: #6f9;">Online</span>
                                                    @else
                                                        <span style="color: #999;">Offline</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <form method="POST" action="{{ route('buddies.removeBuddy', $buddy->buddy_id) }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn_blue" onclick="return confirm('Are you sure you want to remove this buddy?');" style="padding: 2px 8px;">Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>My Buddies</th>
                                        </tr>
                                        <tr>
                                            <td>You don't have any buddies yet.</td>
                                        </tr>
                                        <tr class="alt">
                                            <td>You can add buddies by sending them a request from the "Add Buddy" tab or from the galaxy view.</td>
                                        </tr>
                                    </table>
                                @endif
                            </div>

                            {{-- Requests Received Tab --}}
                            <div id="requests-received" class="tab-content contentz" style="display: none;">
                                @if(count($receivedRequests) > 0)
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>From</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                        @foreach($receivedRequests as $index => $request)
                                            <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                <td>{{ $request->sender->username }}</td>
                                                <td>{{ $request->message ?? 'No message' }}</td>
                                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('buddies.acceptRequest', $request->id) }}" style="display:inline; margin-right: 5px;">
                                                        @csrf
                                                        <button type="submit" class="btn_blue" style="padding: 2px 8px;">Accept</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('buddies.rejectRequest', $request->id) }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn_blue" style="padding: 2px 8px;">Reject</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>Buddy Requests Received</th>
                                        </tr>
                                        <tr>
                                            <td>You have no pending buddy requests.</td>
                                        </tr>
                                    </table>
                                @endif
                            </div>

                            {{-- Requests Sent Tab --}}
                            <div id="requests-sent" class="tab-content contentz" style="display: none;">
                                @if(count($sentRequests) > 0)
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>To</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                        @foreach($sentRequests as $index => $request)
                                            <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                <td>{{ $request->receiver->username }}</td>
                                                <td>{{ $request->message ?? 'No message' }}</td>
                                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <form method="POST" action="{{ route('buddies.cancelRequest', $request->id) }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn_blue" style="padding: 2px 8px;">Cancel</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th>Buddy Requests Sent</th>
                                        </tr>
                                        <tr>
                                            <td>You have not sent any buddy requests.</td>
                                        </tr>
                                    </table>
                                @endif
                            </div>

                            {{-- Add Buddy Tab --}}
                            <div id="add-buddy" class="tab-content contentz" style="display: none;">
                                <form method="POST" action="{{ route('buddies.sendRequest') }}">
                                    @csrf
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th colspan="2">Send Buddy Request</th>
                                        </tr>
                                        <tr>
                                            <td style="width: 200px;">Player ID:</td>
                                            <td>
                                                <input class="text w200" type="number" name="receiver_id" id="receiver_id" required
                                                       value="{{ request()->get('add', '') }}">
                                            </td>
                                        </tr>
                                        <tr class="alt">
                                            <td>Message (optional):</td>
                                            <td>
                                                <textarea name="message" id="message" rows="5" cols="50" maxlength="500"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center; padding: 15px;">
                                                <button type="submit" class="btn_blue">Send Buddy Request</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#tab-buddies li[data-tab]');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all tabs
                    tabs.forEach(t => t.classList.remove('aktiv'));
                    // Add active class to clicked tab
                    this.classList.add('aktiv');

                    // Hide all tab contents
                    tabContents.forEach(content => content.style.display = 'none');
                    // Show target tab content
                    const targetContent = document.getElementById(targetTab);
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }

                    // Update URL hash without scrolling
                    if (history.pushState) {
                        history.pushState(null, null, '#' + targetTab);
                    } else {
                        location.hash = '#' + targetTab;
                    }
                });
            });

            // Handle initial hash on page load
            if (window.location.hash) {
                const hash = window.location.hash.substring(1);
                const tabToActivate = document.querySelector(`#tab-buddies li[data-tab="${hash}"]`);
                if (tabToActivate) {
                    tabToActivate.click();
                }
            }

            // Auto-switch to add buddy tab if coming from galaxy view with player ID
            @if(request()->has('add'))
            setTimeout(function() {
                const addBuddyTab = document.querySelector('#tab-buddies li[data-tab="add-buddy"]');
                if (addBuddyTab) {
                    addBuddyTab.click();
                }
            }, 100);
            @endif
        });
    </script>
@endsection
