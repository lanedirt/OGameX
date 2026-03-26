@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <script>fadeBox('{{ session('status') }}', false);</script>
    @endif

    @if (session('error'))
        <script>fadeBox('{{ session('error') }}', true);</script>
    @endif

    <div id="resourcesettingscomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>@lang('Server Administration')</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>@lang('Server Administration')</h2>
            </div>
            <div class="content">
                <div class="buddylistContent" style="margin-bottom: 60px;">

                    {{-- ===== MASQUERADE AS USER ===== --}}
                    <p class="box_highlight textCenter no_buddies">@lang('Masquerade as User')</p>
                    <form action="{{ route('admin.developershortcuts.impersonate') }}" method="post" style="margin-bottom: 20px;">
                        {{ csrf_field() }}
                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="masquerade_username">@lang('Username:')</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="masquerade_username"
                                           name="username"
                                           class="textInput w150 textCenter textBeefy"
                                           placeholder="@lang('Enter username')">
                                </div>
                            </div>
                            <div class="fieldwrapper" style="text-align: center; margin-top: 10px;">
                                <input type="submit" class="btn_blue" value="@lang('Masquerade')">
                            </div>
                        </div>
                    </form>

                    {{-- ===== MULTI-ACCOUNT DETECTION ===== --}}
                    <p class="box_highlight textCenter no_buddies">@lang('Multi-Account Detection (Shared IP)')</p>

                    @if ($suspiciousGroups->isEmpty())
                        <div class="group bborder" style="display: block;">
                            <p style="text-align: center; padding: 10px;">No suspicious accounts detected.</p>
                        </div>
                    @else
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #333; border-radius: 3px; margin-bottom: 10px;">
                        @foreach ($suspiciousGroups as $group)
                            <div style="padding: 8px 10px; border-bottom: 1px solid #333;">
                                <div style="padding: 4px 0; margin-bottom: 4px;">
                                    <strong>{{ $group['type'] }}:</strong>
                                    <code style="background: #1a1a2e; padding: 2px 6px; border-radius: 3px; margin-left: 6px;">{{ $group['ip'] }}</code>
                                </div>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                            <th style="padding: 4px 6px; text-align: left;">ID</th>
                                            <th style="padding: 4px 6px; text-align: left;">Username</th>
                                            <th style="padding: 4px 6px; text-align: left;">Email</th>
                                            <th style="padding: 4px 6px; text-align: left;">Registered</th>
                                            <th style="padding: 4px 6px; text-align: left;">Last Active</th>
                                            <th style="padding: 4px 6px; text-align: left;">Status</th>
                                            <th style="padding: 4px 6px; text-align: left;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($group['users'] as $user)
                                            <tr style="border-top: 1px solid #222;">
                                                <td style="padding: 4px 6px;">{{ $user->id }}</td>
                                                <td style="padding: 4px 6px;">
                                                    {{ $user->username }}
                                                    @if ($user->hasRole('admin'))
                                                        <span style="color: #f48406; font-size: 10px;">[ADMIN]</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">{{ $user->email }}</td>
                                                <td style="padding: 4px 6px;">{{ $user->created_at?->format('Y-m-d') }}</td>
                                                <td style="padding: 4px 6px;">{{ $user->time ? \Illuminate\Support\Carbon::createFromTimestamp((int)$user->time)->format('Y-m-d H:i') : '-' }}</td>
                                                <td style="padding: 4px 6px;">
                                                    @if ($user->isBanned())
                                                        <span style="color: #e74c3c;">Banned</span>
                                                    @else
                                                        <span style="color: #2ecc71;">Active</span>
                                                    @endif
                                                </td>
                                                <td style="padding: 4px 6px;">
                                                    @if (!$user->hasRole('admin') && !$user->isBanned())
                                                        <form action="{{ route('admin.server-administration.ban') }}" method="post" style="display:inline;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="username" value="{{ $user->username }}">
                                                            <input type="hidden" name="reason" value="Multi-account violation">
                                                            <input type="hidden" name="duration" value="permanent">
                                                            <input type="submit" class="btn_blue" value="Quick Ban" style="font-size: 10px; padding: 2px 6px;">
                                                        </form>
                                                    @elseif (!$user->hasRole('admin') && $user->isBanned())
                                                        <form action="{{ route('admin.server-administration.unban') }}" method="post" style="display:inline;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                            <input type="submit" class="btn_blue" value="Unban" style="font-size: 10px; padding: 2px 6px;">
                                                        </form>
                                                    @else
                                                        <span style="color: #666; font-size: 10px;">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                        </div>
                    @endif

                    {{-- ===== BAN A PLAYER ===== --}}
                    <p class="box_highlight textCenter no_buddies" style="margin-top: 20px;">@lang('Ban a Player')</p>
                    <form action="{{ route('admin.server-administration.ban') }}" method="post">
                        {{ csrf_field() }}
                        <div class="group bborder" style="display: block;">
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_username">Username:</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="ban_username"
                                           name="username"
                                           class="textInput w150 textCenter textBeefy"
                                           placeholder="Enter username"
                                           required>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_reason">Reason:</label>
                                <div class="thefield">
                                    <input type="text"
                                           id="ban_reason"
                                           name="reason"
                                           class="textInput w300 textBeefy"
                                           placeholder="Reason for ban"
                                           required>
                                </div>
                            </div>
                            <div class="fieldwrapper">
                                <label class="styled textBeefy" for="ban_duration">Duration:</label>
                                <div class="thefield">
                                    <select id="ban_duration" name="duration" class="textInput textBeefy">
                                        <option value="86400">1 Day</option>
                                        <option value="259200">3 Days</option>
                                        <option value="604800">7 Days</option>
                                        <option value="2592000">30 Days</option>
                                        <option value="permanent">Permanent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="fieldwrapper" style="text-align: center; margin-top: 10px;">
                                <input type="submit" class="btn_blue" value="Ban Player">
                            </div>
                        </div>
                    </form>

                    {{-- ===== CURRENTLY BANNED USERS ===== --}}
                    <p class="box_highlight textCenter no_buddies" style="margin-top: 20px;">@lang('Currently Banned Players')</p>

                    @if ($bannedUsers->isEmpty())
                        <div class="group bborder" style="display: block;">
                            <p style="text-align: center; padding: 10px;">No players are currently banned.</p>
                        </div>
                    @else
                        <div class="group bborder" style="display: block;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #0d0d1a; color: #aaa; font-size: 11px;">
                                        <th style="padding: 5px 8px; text-align: left;">Username</th>
                                        <th style="padding: 5px 8px; text-align: left;">Reason</th>
                                        <th style="padding: 5px 8px; text-align: left;">Banned Until</th>
                                        <th style="padding: 5px 8px; text-align: left;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bannedUsers as $user)
                                        <tr style="border-top: 1px solid #222;">
                                            <td style="padding: 5px 8px;">{{ $user->username }}</td>
                                            <td style="padding: 5px 8px;">{{ $user->ban_reason }}</td>
                                            <td style="padding: 5px 8px;">
                                                {{ $user->banned_until ? $user->banned_until->format('Y-m-d H:i') . ' UTC' : 'Permanent' }}
                                            </td>
                                            <td style="padding: 5px 8px;">
                                                <form action="{{ route('admin.server-administration.unban') }}" method="post" style="display:inline;">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <input type="submit" class="btn_blue" value="Unban" style="font-size: 10px; padding: 2px 8px;">
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

@endsection
