@extends('ingame.layouts.main')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div id="eventboxContent" style="display: none">
        <img height="16" width="16" src="/img/icons/3f9884806436537bdec305aa26fc60.gif">
    </div>

    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Alliance</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    @if($isInAlliance)
                        {{-- User is in an alliance --}}
                        <div id="tabs">
                            <ul class="tabsbelow" id="tab-ally">
                                <li class="aktiv" data-tab="alliance-info">
                                    <a href="javascript:void(0);"><span>Alliance Info</span></a>
                                </li>
                                <li data-tab="members">
                                    <a href="javascript:void(0);"><span>Members ({{ $members->count() }})</span></a>
                                </li>
                                @if($allianceService && $allianceService->hasPermission(Auth::id(), 'can_see_applications'))
                                    <li data-tab="applications">
                                        <a href="javascript:void(0);"><span>Applications ({{ $pendingApplications->count() }})</span></a>
                                    </li>
                                @endif
                                @if($allianceService && $allianceService->hasPermission(Auth::id(), 'can_edit_alliance'))
                                    <li>
                                        <a href="{{ route('alliance.manage') }}"><span>Manage</span></a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <div class="clearfloat"></div>
                        <div class="alliance_wrapper">
                            <div class="allianceContent">
                                {{-- Alliance Info Tab --}}
                                <div id="alliance-info" class="tab-content contentz" style="display: block;">
                                    <p style="font-size: 14px; font-weight: bold; margin-bottom: 10px;">[{{ $alliance->tag }}] {{ $alliance->name }}</p>

                                    @if($alliance->logo)
                                        <div style="margin: 10px 0;">
                                            <img src="{{ $alliance->logo }}" alt="{{ $alliance->name }} Logo" style="max-width: 200px;">
                                        </div>
                                    @endif

                                    @if($alliance->description)
                                        <div style="margin: 15px 0;">
                                            <p style="font-weight: bold; margin-bottom: 5px;">Description</p>
                                            <div id="allyText">{{ $alliance->description }}</div>
                                        </div>
                                    @endif

                                    @if($alliance->external_url)
                                        <p><strong>Homepage:</strong> <a href="{{ $alliance->external_url }}" target="_blank" rel="noopener">{{ $alliance->external_url }}</a></p>
                                    @endif

                                    @if($alliance->internal_text && $membership)
                                        <div style="margin: 15px 0;">
                                            <p style="font-weight: bold; margin-bottom: 5px;">Internal Area</p>
                                            <div id="allyText">{{ $alliance->internal_text }}</div>
                                        </div>
                                    @endif

                                    <div style="margin: 15px 0;">
                                        <p><strong>Founded by:</strong> {{ $alliance->founder->username }}</p>
                                        <p><strong>Members:</strong> {{ $members->count() }}</p>
                                        <p><strong>Your Rank:</strong> {{ $userRank->name ?? 'No rank assigned' }}</p>
                                    </div>

                                    <div style="margin-top: 20px; text-align: center;">
                                        <form method="POST" action="{{ route('alliance.leave') }}" style="display: inline; margin-right: 10px;">
                                            @csrf
                                            <button type="submit" class="btn_blue" onclick="return confirm('Are you sure you want to leave this alliance?')">Leave Alliance</button>
                                        </form>

                                        @if($alliance->founder_id === Auth::id())
                                            <form method="POST" action="{{ route('alliance.disband') }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn_blue" onclick="return confirm('Are you sure you want to disband this alliance? This action cannot be undone!')">Disband Alliance</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                {{-- Members Tab --}}
                                <div id="members" class="tab-content contentz" style="display: none;">
                                    @if($members->isNotEmpty())
                                        <div id="section12" style="margin: 20px 0;">
                                            <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Rank</th>
                                                    <th>Joined</th>
                                                    @if($allianceService && $allianceService->hasPermission(Auth::id(), 'can_kick'))
                                                        <th>Actions</th>
                                                    @endif
                                                </tr>
                                                @foreach($members as $index => $member)
                                                    <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                        <td>{{ $member->user->username }}</td>
                                                        <td>{{ $member->rank->name ?? 'No rank' }}</td>
                                                        <td>{{ $member->joined_at ? $member->joined_at->format('Y-m-d') : 'N/A' }}</td>
                                                        @if($allianceService && $allianceService->hasPermission(Auth::id(), 'can_kick'))
                                                            <td>
                                                                @if($member->user_id !== $alliance->founder_id && $member->user_id !== Auth::id())
                                                                    <form method="POST" action="{{ route('alliance.member.kick', $member->id) }}" style="display: inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn_blue" onclick="return confirm('Are you sure you want to kick this member?')" style="padding: 2px 8px;">Kick</button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    @else
                                        <p>No members found.</p>
                                    @endif
                                </div>

                                {{-- Applications Tab --}}
                                @if($allianceService && $allianceService->hasPermission(Auth::id(), 'can_see_applications'))
                                    <div id="applications" class="tab-content contentz" style="display: none;">
                                        @if($pendingApplications->isNotEmpty())
                                            <div id="section22" style="margin: 20px 0;">
                                                <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                                    <tr>
                                                        <th>Username</th>
                                                        <th>Application Text</th>
                                                        <th>Date</th>
                                                        @if($allianceService->hasPermission(Auth::id(), 'can_accept_applications'))
                                                            <th>Actions</th>
                                                        @endif
                                                    </tr>
                                                    @foreach($pendingApplications as $index => $application)
                                                        <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                            <td>{{ $application->user->username }}</td>
                                                            <td>{{ $application->application_text ?? 'No message' }}</td>
                                                            <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                                                            @if($allianceService->hasPermission(Auth::id(), 'can_accept_applications'))
                                                                <td>
                                                                    <form method="POST" action="{{ route('alliance.application.accept', $application->id) }}" style="display: inline; margin-right: 5px;">
                                                                        @csrf
                                                                        <button type="submit" class="btn_blue" style="padding: 2px 8px;">Accept</button>
                                                                    </form>
                                                                    <form method="POST" action="{{ route('alliance.application.reject', $application->id) }}" style="display: inline;">
                                                                        @csrf
                                                                        <button type="submit" class="btn_blue" style="padding: 2px 8px;">Reject</button>
                                                                    </form>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        @else
                                            <p>No pending applications.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        {{-- User is not in an alliance --}}
                        <div id="tabs">
                            <ul class="tabsbelow" id="tab-ally">
                                <li class="aktiv">
                                    <a href="{{ route('alliance.create') }}"><span>Create Alliance</span></a>
                                </li>
                                <li>
                                    <a href="{{ route('alliance.search') }}"><span>Search Alliance</span></a>
                                </li>
                            </ul>
                        </div>

                        <div class="clearfloat"></div>
                        <div class="alliance_wrapper">
                            <div class="allianceContent">
                                <div class="contentz">
                                    <p>You are not currently a member of any alliance.</p>
                                    <p>You can either <a href="{{ route('alliance.create') }}">create your own alliance</a> or <a href="{{ route('alliance.search') }}">search for an existing alliance</a> to join.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('#tab-ally li[data-tab]');
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
                const tabToActivate = document.querySelector(`#tab-ally li[data-tab="${hash}"]`);
                if (tabToActivate) {
                    tabToActivate.click();
                }
            }
        });
    </script>
@endsection
